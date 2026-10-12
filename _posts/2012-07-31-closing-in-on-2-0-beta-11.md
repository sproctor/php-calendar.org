---
layout: post
title: "Closing in on 2.0 beta 11"
date: 2012-07-31 08:30:00 -0400
categories: update
---
I'm almost done with 2.0 beta 11. With every beta, I feel like the next release will be the final. Then every time I start working on the next one, I think of a bunch of features that I really need for the stable release. If anyone can checkout what I have and do some testing, that'd be awesome. http://code.google.com/p/php-calendar/source/checkout

What's new? The biggest improvement is persistent log-ins. No more getting logged out after half an hour of inactivity. This feature is new and it uses the DB, so I added another updater to the installer. Please test it on something other than your production calendar. I'll need to add something to clean up the stale log-ins before release.

Other than that, I cleaned up the date/time entries. I think they're a lot friendlier now. Anonymous users can store their settings in cookies. I added an interface to add and modify instances to event. Cleaned up form submissions to take the user to a useful page instead of just a page with a success/failure message.
