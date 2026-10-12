---
layout: post
title: "Closing in on 2.0 final"
date: 2013-01-21 11:24:00 -0500
categories: update
---
I just put up 2.0-rc2. I'm really sorry with how buggy the last few releases have been. I'm really bad at testing and these releases were really unlucky. I think I've gotten everything sorted out now though. 2.0-rc2 should be the best release yet.

I haven't really described the changes since beta10. I've been putting more and more javascript into the calendar. It started with Jeff adding summaries when hovering over events in the month view. Then Michal Osowiecki wrote some code to add color pickers for the categories. Since beta10 I've added date pickers for the date input fields, time pickers for the time input fields, and timezones were made friendlier by splitting them into two dropdowns. There are now useful pages after forms are submitted. Users who aren't logged in can change their language and timezone settings and have them stored in cookies. Previously the date format was just kind of hacked on and associated with languages. It's now a calendar setting and respected in all of the places I found that display dates.

Support for PHP 5.2 ended at the end of 2010. I haven't used any features specific to PHP 5.3 yet but they improved timezone support in 5.3 and users on 5.2 will see some weird compatibility timezones. My webhost still supports 5.2 and they didn't upgrade me automatically. I use Dreamhost which is on version 5.3 now, FYI. I haven't tests on 5.4 or 5.5 yet. If you find any bugs specific to those versions, please let me know and I'll fix them.
