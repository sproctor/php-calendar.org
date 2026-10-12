---
layout: post
title: "PHP-Calendar 2.0 Beta7"
date: 2010-05-19 12:04:00 -0400
categories: update
---
This is a somewhat unusual release. There were a couple of XSS issues either the early betas or maybe longer. One was an artifact from debugging where escaping HTML entities in subjects just never got re-enabled. I think that one was introduced when I added UTF-8 support. The other issue, which was around a lot long, was there was no checking of the lastaction parameter on login, so someone could craft a URL to be redirected to any location. Thanks to VUPEN for discovering these issues.

In other news, I've included a French translation, allowed month names to be translated, and fixed a few statements that caused PHP to print notices on some setups.
