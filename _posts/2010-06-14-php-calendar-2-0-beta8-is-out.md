---
layout: post
title: "PHP-Calendar 2.0 Beta8 is out"
date: 2010-06-14 16:34:00 -0400
categories: update
---
There's not really much to talk about here in the way of features. A couple of new translations, Danish and Chinese. Not too many bug fixes. No DB changes. The big changes here are all security related. I added a check to protect against [CSRF](http://en.wikipedia.org/wiki/Cross-site_request_forgery), fixed a few XSS and an SQL injection or two. Thanks to Teemu Vesala at Qentinel and the folks at Skipfish for helping find them. The update scripts from 1.x series have been removed as they were another attack vector.

There was a published vulnerability about the update scripts that I was never notified about. I'm pretty disappointed about how they handled that situation. [The security advisory.](http://seclists.org/fulldisclosure/2009/Dec/399) Despite the fact that the bulletin says I was notified and the rep from isecauditors.com said he notified me, I was not notified. If you're using an older version of PHP-Calendar, mainly version 1.1, please delete the update08.php and update10.php files.

VUPEN released an [advisory for the security issues fixed in 2.0-beta7](http://www.vupen.com/english/advisories/2010/1202). Anyone on an earlier beta should upgrade to 2.0-beta8.
