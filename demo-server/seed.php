<?php
// Adds sample events, dated around today, to a freshly reset PHP-Calendar demo database. The dev
// database starts empty (the reset builds it with the migrations), so for dev this first adds the
// calendar, the admin and demo accounts, and the categories.
// Usage: php seed.php release|dev
// Reads the database and admin passwords from /root/php-calendar-demo.env.

$which = $argv[1] ?? '';
if (!in_array($which, ['release', 'dev'], true)) {
    fwrite(STDERR, "usage: {$argv[0]} release|dev\n");
    exit(2);
}

$secrets = parse_ini_file('/root/php-calendar-demo.env');
$db = new PDO(
    "mysql:host=127.0.0.1;dbname=phpc_$which;charset=utf8mb4",
    "phpc_$which",
    $secrets[strtoupper($which) . '_DB_PASS'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
);
$db->exec("SET time_zone = '+00:00'");

if ($which === 'dev') {
    $db->exec("INSERT INTO calendars (cid, title, slug, subject_max, events_max, timezone, anon_permission)
        VALUES (1, 'PHP-Calendar Demo', 'php-calendar-demo', 50, 8, 'America/New_York', 1)");
    $user = $db->prepare('INSERT INTO users (uid, username, hash, is_admin, password_is_editable, timezone, locale, is_disabled)
        VALUES (?, ?, ?, ?, ?, ?, ?, 0)');
    $user->execute([1, 'admin', password_hash($secrets['DEV_ADMIN_PASS'], PASSWORD_BCRYPT), 1, 1, 'America/New_York', 'en']);
    $user->execute([2, 'demo', password_hash('demo', PASSWORD_BCRYPT), 0, 0, 'America/New_York', null]);
    $db->exec('INSERT INTO user_permissions (cid, uid, `read`, `create`, `update`, `moderate`, `admin`) VALUES (1, 2, 1, 1, 1, 1, 1)');
    $db->exec("INSERT INTO categories (cid, name, color) VALUES
        (1, 'Meetings', '#2c6fbb'), (1, 'Social', '#f4c542'), (1, 'Travel', '#3a9a5b')");
}

$tz = new DateTimeZone('America/New_York');
$utc = new DateTimeZone('UTC');
$today = new DateTimeImmutable('today', $tz);
// Sunday of this week, so "Tuesday" etc. land in the current week
$sunday = $today->modify('-' . $today->format('w') . ' days');

// [subject, description, category, type, occurrences]
// type: timed occurrences are [day offset from this Sunday, start "H:i", end "H:i"];
//       full-day and TBA occurrences are [first day offset, last day offset]
$weekly = fn (int $weekday, string $start, string $end, array $weeks) =>
    array_map(fn ($w) => [$w * 7 + $weekday, $start, $end], $weeks);

$events = [
    ['Team meeting', 'Weekly status meeting. Bring your updates!', 'Meetings', 'timed',
        $weekly(2, '10:00', '11:00', [-2, -1, 0, 1, 2, 3, 4])],
    ['Yoga class', 'Beginners welcome. Mats provided.', 'Social', 'timed',
        $weekly(4, '18:00', '19:00', [0, 1, 2, 3])],
    ['Release planning', 'Decide what goes into the next release.', 'Meetings', 'timed',
        [[(int) $today->format('w'), '14:00', '15:00']]],
    ['Community potluck', "Bring a dish to share.\n\nThe grill will be going from 5.", 'Social', 'timed',
        [[6, '17:00', '20:00']]],
    ['Dentist', '', null, 'timed', [[8, '08:30', '09:15']]],
    ['Conference', 'Three days of talks. See the program on the conference website.', 'Travel', 'full',
        [[10, 12]]],
    ['Office closed', 'Holiday.', null, 'full', [[15, 15]]],
    ['Guest speaker', 'Time to be announced.', 'Meetings', 'tba', [[17, 17]]],
    ['Book club', 'This month: discuss chapters 1-6.', 'Social', 'timed', [[-4, '19:00', '20:30']]],
];

$categories = [];
if ($which === 'release') {
    foreach ($db->query('SELECT catid, name FROM phpc_categories WHERE cid = 1') as $row) {
        $categories[$row['name']] = (int) $row['catid'];
    }
} else {
    foreach ($db->query('SELECT catid, name FROM categories WHERE cid = 1') as $row) {
        $categories[$row['name']] = (int) $row['catid'];
    }
}

$now = (new DateTimeImmutable('now', $utc))->format('Y-m-d H:i:s');
$count = 0;
foreach ($events as [$subject, $description, $category, $type, $occurrences]) {
    $catid = $category === null ? null : ($categories[$category] ?? null);
    if ($which === 'release') {
        $db->prepare('INSERT INTO phpc_events (cid, owner, subject, description, catid, ctime) VALUES (1, 1, ?, ?, ?, ?)')
            ->execute([$subject, $description, $catid, $now]);
    } else {
        $db->prepare('INSERT INTO events (cid, owner_uid, catid, ctime, pubtime, subject, description, readonly) VALUES (1, 1, ?, ?, ?, ?, ?, 0)')
            ->execute([$catid, $now, $now, $subject, $description]);
    }
    $eid = (int) $db->lastInsertId();

    foreach ($occurrences as $occurrence) {
        if ($type === 'timed') {
            [$day, $start, $end] = $occurrence;
            $date = $sunday->modify("$day days")->format('Y-m-d');
            $s = (new DateTimeImmutable("$date $start", $tz))->setTimezone($utc);
            $e = (new DateTimeImmutable("$date $end", $tz))->setTimezone($utc);
            if ($which === 'release') {
                $db->prepare('INSERT INTO phpc_occurrences (eid, start_ts, end_ts, time_type) VALUES (?, FROM_UNIXTIME(?), FROM_UNIXTIME(?), 0)')
                    ->execute([$eid, $s->getTimestamp(), $e->getTimestamp()]);
            } else {
                $db->prepare('INSERT INTO occurrences (eid, start, end, time_type) VALUES (?, ?, ?, 0)')
                    ->execute([$eid, $s->format('Y-m-d H:i:s'), $e->format('Y-m-d H:i:s')]);
            }
        } else {
            [$first, $last] = $occurrence;
            $timeType = $type === 'full' ? 1 : 2;
            $s = $sunday->modify("$first days")->format('Y-m-d');
            $e = $sunday->modify("$last days")->format('Y-m-d');
            if ($which === 'release') {
                $db->prepare('INSERT INTO phpc_occurrences (eid, start_date, end_date, time_type) VALUES (?, ?, ?, ?)')
                    ->execute([$eid, $s, $e, $timeType]);
            } else {
                // Full-day and TBA dates are stored as midnight UTC
                $db->prepare('INSERT INTO occurrences (eid, start, end, time_type) VALUES (?, ?, ?, ?)')
                    ->execute([$eid, "$s 00:00:00", "$e 00:00:00", $timeType]);
            }
        }
    }
    $count++;
}
echo "$which: added $count sample events\n";
