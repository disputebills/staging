=== Facebook Comments Importer ===
Contributors: ivan.m89
Donate link: 
Tags: comments importer, fb comments, facebook, comments, fb comments import, facebook comments, facebook comments import, discussion
Requires at least: 3.0
Tested up to: 4.4
Stable tag: 2.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Import Facebook comments into Wordpress database.

== Description ==

Facebook comments importer is used to import comments from Facebook pages, groups and user profiles directly in the Wordpress database. 
Apart from just importing the comments, our plugin will also pull and display profile pictures as avatars and every comment will have the 
real authors name to it. The comments are displayed in the same format as they appear on Facebook, which means that nested comments are 
fully supported and the plugin will also show replies to comments made by users. The main advantage of importing comments into the Wordpress 
database is the fact that they become visible to search engines and this procedure has a positive effect on your websites SEO. Besides that, 
your website will look much more active and also encourage other users to get involved in discussions.


We also have a **PRO** version with additional features:

* Import comments from regular posts, videos, images or statuses
* Import from pages, groups or profiles
* Manually connect post with comments
* Importing comments from very old posts, no limits
* Automate the import process (with WP cron)
* Turn the cron on/off or restart it
* Modification of the main URL (useful in cases of changing the domain)
* Faster and more efficient support

The free version offers just basic functionality, like the manual import of comments from a Facebook group or page if the post was published 
as a link to some of the articles on your website. 

The PRO version has **no limitations**, it is possible to import comments from almost everything, like from pictures, videos, links through to 
ordinary text statuses. The PRO version will in various ways automatically try to connect Facebook posts with some of the articles on your 
website. Furthermore, the PRO version is doing everything automatically, so there is no need for manual comment importing, the plugin will 
automatically start importing comments every 10 minutes and do everything on it’s own. Besides that, you will also have the possibility of 
importing comments from two different Facebook groups or pages + the import from your user wall.

For any issues with the plugin, please open a support ticket and we will fix it ASAP!

== Installation ==

1. Unzip `fb-comments-importer.zip` to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Create a Facebook APP at developers.facebook.com/apps/
4. Configure the plugin by going to the `FB Comments Importer` page

== Frequently asked questions ==

== Screenshots ==

1. FB Comments Importer FREE
2. FB Comments Importer PRO

== Changelog ==

= 2.3 2015-03-04 =
* Just a small readme update, plugin works even with wp 4.4.x
* Avatar image size fixed, there was problem with too small image size, it should be fine now

= 2.2 2015-02-13 =
* readme update
* fixed problem with checking comments number (api call updated)
* Switched to latest API version (2.5)

= 2.1 2015-09-28 =
* readme update
* small bug on unlimited fetcher fixed
* code cleanup
* tests implemented

= 2.0 2015-07-25 =
* Switch to the new facebook API version (2.4)
* Few minor bug fixes
* javascript code cleanup

= 1.9 2015-07-10 =

* NEW FEATURE: unlimited post fetcher, now you can fetch unlimited number of posts from facebook very quickly. For now this is experimental feature.
* Code cleanup
* few small bugs fixed

= 1.8.1 2015-06-22 =
* Fixed bug related with checking number of comments on button click. 

= 1.8.0 2015-06-13 =
* Fixed bug related with import images inside replies 

= 1.7.9 2015-06-08 =
* NEW FEATURE: option to disable images inside imported comments

= 1.7.8 2015-05-30 =
* There was a problem with importing comments with emoji emoticons, it is fixed in this version

= 1.7.7 2015-05-29 =
* Sorry for another update, but there was a problem with importing comments with emoticons inside message. It is fixed with this update 

= 1.7.6 2015-05-25 =
* Code cleanup
* Interface redesign

=1.7.5 2015-04-25 =
* Another bug related with importing images inside comments is fixed

= 1.7.4 2015-04-22 =
* Important update: In last version (1.7.3) was small bug related with images inside comments, it is fixed in this version.

= 1.7.3 2015-04-21 =
* Ability to import images inside comments
* Tested up to version updated

= 1.7.2 2015-04-15 =
* Plugin moved to fabook graph api 2.3
* Import process has been enhanced.

= 1.7.1 2015-03-27 =
* Documentation and readme file updated
* Fixed bug related with comments replies import 
* Code cleaned-up

= 1.7 2015-03-20 =
* NEW FEATURE: Import comments from facebook text statuses (with link to your site)
* Small bug related with access token fixed
* Ability to change base URL (useful if you move site to another domain)

= 1.6.3 2015-03-11 =
* Performance improvement
* Notices from error reporting fixed

= 1.6.2 2015-03-09 =
* Few interface changes
* From version 1.6.2 plugin is capable to fetch unlimited number of posts from facebook page

= 1.6.1 2015-02-12 =
* fixed bug related with short links auto follow

= 1.6 2015-02-10 =
* Performance improvement
* Ability to automatically follow redirects and connect short URLs (goo.gl, tinyurl.com, tiny.cc etc.)
* file_get_content removed from all functions, and replaced with curl

= 1.5.2 2015-02-06 =
* Fixed error handler for wrong facebook page ID
* Few notices from error reporting fixed

= 1.5.1 2015-01-30 =
* Some notices from error reporting fixed
* Small template changes
* Few small bug fixes in FB Comments class 

= 1.5 2015-01-26 =
* Light interface redesign
* get_avatar filter priority changed

= 1.4.1 2014-04-10 =
* Avatar bug fixed. Facebook API has changed so we had to do it.

= 1.4 2014-04-10 =
* NEW FEATURE: now you can change status for imported comments (pending or approved)
* Few bugs fixed (number of imported comments was wrong, limit in form and etc...)
* Admin interface improvements

= 1.3.3 2013-01-17 =
* Fixed another bug with duplicate comments being imported
* Better compatibility with Wordpress 3.8

= 1.3.2 2013-09-19 =
* Improved duplicate comments detection.
* Fixed bug with user avatars not being displayed.
* Removed the ugly array warning message when trying to import post without comments.
* Added a screenshot for free and pro versions

= 1.3.1 2013-08-31 =
* Fixed bug with importing duplicate comments in posts with the non-standard Facebook emoticons.
* Fixed some warning in_array errors.

= 1.3 2013-08-15 =
* Plugin now imports the Facebook comment replies as well as regular comments.
* Expanded the limit of posts available for import to 50. For more you need to purchase a PRO version.
* Added the CURL check, so the plugin reports if you don't have it enabled on your server.
* Added the "Show / Hide unavailable posts" button.
* Comments count is now retrieved about twice times faster.
* Fixed several bugs which usually led to a blank page or different PHP errors.

= 1.0.2 2013-03-10 =
* Plugin didnt work on some server configurations with short_tags set to off in php.ini

= 1.0.1 2013-03-02 =
* Manual import was giving some errors on some server configurations.

= 1.0 2013-03-01 =
* Initial release