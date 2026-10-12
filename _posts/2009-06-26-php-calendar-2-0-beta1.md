---
layout: post
title: "PHP-Calendar 2.0 Beta1"
date: 2009-06-26 14:37:00 -0400
categories: update
---
I just released version 2.0 Beta1.

The new features include a vastly improved permissions system. Site admins can create users and give them permissions (read, write, admin, etc) on distinct calendars. Running multiple calendars may not be perfect yet, but I would like feedback from anyone who wants to try it. I decided to release before I finished the code to delete users/calendars; that will be available in the next release.

The DB format is not finalized and there is unlikely to be an updater for the next version. This version is strictly for feedback. If you plan on updating your calendar when the next version comes out and you want to keep your events, wait. If you want to help me get the next version out, try this calendar, but understand it's going to be a difficult or strictly manual process to update.

The updater doesn't work for this version. It's unlikely that I'm going to write an updater to version 2.0. The user schema shouldn't change, but the event schema is going to change to the previous development line by the final version. There's too much installation dependent code to make an easy updater. If someone wants to take on the task, I will certainly be a guide, but I think improving the installer or other code would be time better spent.
