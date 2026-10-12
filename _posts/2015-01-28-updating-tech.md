---
layout: post
title: "Updating tech"
date: 2015-01-28 09:13:00 -0500
categories: update
---
A few months ago PHP stopped support for PHP 5.3. I tend to lag significantly behind modern versions. PHP 5.3.0 was released in June 2009. I haven't really kept up with some of the other interesting bits of PHP tech to come out since then.

PHP-Calendar was originally written for PHP 3. The first release, back in 2002, was ported to PHP 4. I've tried to support as many versions of PHP as I could. Unfortunately, PHP designers like to break things required by older releases in their newer ones. The addition of new features in PHP has rarely required me to bump the minimum requirements.

With the creation of Symfony2 and Composer, the PHP landscape seems to have made a dramatic shift. Symfony2 requires PHP 5.3 or greater. PHP 5.3 is already past official support. Starting with the next version of PHP-Calendar I'll be dropping support for older versions of PHP in order to take advantage of the features offered by PHP 5.3 and Symfony2. Especially Symfony2. The addition of Symfony as a requirement is going to allow me to shed a lot of custom code that I have written.
