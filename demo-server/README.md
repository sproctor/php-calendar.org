# Demo server

Scripts and configuration for the server behind https://demo.php-calendar.org (latest 2.0 release)
and https://dev.php-calendar.org (the `main` branch). This folder isn't part of the website.

## Server

- Oracle Cloud Always Free VM (`VM.Standard.E2.1.Micro`, 1 GB RAM) in `us-ashburn-1`, Ubuntu 26.04,
  with a 2 GB swap file.
- nginx, PHP-FPM and MariaDB from Ubuntu's packages, plus `composer`, `git` and `gettext`. No Docker.
- Both hostnames are proxied by Cloudflare (orange cloud) with SSL/TLS set to Full (strict). nginx
  serves a Cloudflare Origin CA certificate from `/etc/ssl/cloudflare/` (not in this repository).
- Ports 80 and 443 are open only to Cloudflare's IP ranges (OCI security list), and the VM's iptables
  rules (`/etc/iptables/rules.v4`) allow 80 and 443.
- Database passwords and the admin passwords are in `/root/php-calendar-demo.env`, readable by root
  only.

## Layout

| Path on the server | Contents |
|---|---|
| `/srv/phpc/release` | Shallow clone at the highest `v2.0.x` tag, configured by its `install.php` (then deleted) |
| `/srv/phpc/dev` | Shallow clone of `main`; settings in `.env.local.php`, written before running `/setup` |
| `/var/lib/php-calendar-demo/phpc_{release,dev}.sql` | Baseline databases that the nightly reset restores |

## Files

| File | Installed as |
|---|---|
| `config/nginx-php-calendar.conf` | `/etc/nginx/sites-available/php-calendar` (linked from `sites-enabled`; the default site is removed). `/etc/nginx/conf.d/cloudflare-realip.conf` has a `set_real_ip_from` line for each range at https://www.cloudflare.com/ips/ and `real_ip_header CF-Connecting-IP;` |
| `config/mariadb-60-small.cnf` | `/etc/mysql/mariadb.conf.d/60-small.cnf`, to fit MariaDB in 1 GB |
| `update-php-calendar-demos` | `/usr/local/sbin/`: moves dev to the latest `main` (composer, migrations, cache) and release to the highest `v2.0.x` tag (skips tags that change the 2.0 database version) |
| `reset-php-calendar-demos` | `/usr/local/sbin/`: recreates both databases from the baselines, migrates dev, and adds sample events |
| `seed.php` | `/usr/local/lib/php-calendar-demo/seed.php`: the sample events, dated around today |
| `make-demo-baseline` | Run once (as root) after installing both demos: adds the `demo`/`demo` account and categories, then saves the baselines |
| `systemd/update-php-calendar-demos.{service,timer}` | `/etc/systemd/system/`: runs the reset and then the update every night at 08:00 UTC |

PHP-FPM's pool (`/etc/php/8.5/fpm/pool.d/www.conf`) uses `pm = ondemand`, `pm.max_children = 4`
and `catch_workers_output = yes`, so application errors reach `/var/log/php8.5-fpm.log`.

## Changing the demo data

The reset restores the baselines every night, so a change made through the website (including a new
admin password) only lasts until then. To keep one, make it and then save the baselines again:

```sh
for db in phpc_release phpc_dev; do
    sudo mariadb-dump --single-transaction --skip-dump-date "$db" | sudo tee "/var/lib/php-calendar-demo/$db.sql" > /dev/null
done
```

Delete the events first (or run `reset-php-calendar-demos`, make the change, and delete the events)
so the sample events aren't saved into the baseline and duplicated by the next reset.

## Logs

```sh
journalctl -u update-php-calendar-demos -n 30 --no-pager
```
