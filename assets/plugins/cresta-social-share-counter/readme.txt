=== Cresta Social Share Counter ===
Contributors: CrestaProject
Donate link: http://crestaproject.com/downloads/cresta-social-share-counter/
Tags: share, social, social share, social buttons, share button, share buttons, facebook, twitter, linkedin, pinterest, google plus, floating buttons, social count, social counter, sharing, social sharing, socialize, social icon, print, post, posts, page, plugin, facebook share, twitter share, google plus share, linkedin share, pinterest share
Requires at least: 3.9
Tested up to: 4.5
Stable tag: 2.5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Share your posts and pages quickly and easily with Cresta Social Share Count showing the share count.

== Description ==

With Cresta Social Share Counter you can share your posts and pages easily and show the social count.

<strong>You can select the following Social Network</strong>
<ul>
	<li>1. Facebook</li>
	<li>2. Twitter</li>
	<li>3. Google Plus</li>
	<li>4. Linkedin</li>
	<li>5. Pinterest</li>
</ul>

<strong>Some features</strong>
<ul>
	<li>Show Social Counter</li>
	<li>Choose up to 9 buttons styles</li>
	<li>Fade animation</li>
	<li>Show the floating social buttons</li>
	<li>Show the social buttons before/after the post or page content</li>
	<li>Use the shortcode <strong>[cresta-social-share]</strong> wherever you want to display the social buttons</li>
</ul>

<p>
<strong>Plugin Homepage & Demo</strong><br />
http://crestaproject.com/downloads/cresta-social-share-counter/
</p>

== Installation ==

1. Upload the folder 'cresta-social-share-counter' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to WordPress Main Menu -> CSSC FREE to set the options


== Frequently Asked Questions ==

= Can I choose which pages to display social buttons? =
Yes, in the options page you can choose which pages to display social buttons (pages, posts, media and any custom post type).

= The shortcode [cresta-social-share] doesn't work in a widget =
Most likely the theme you're using has not enabled the use of shortcodes within text-widget.<br/>
Try to add this snippet in your theme’s functions.php:
`
add_filter('widget_text', 'do_shortcode');
`

= Can I hide the floating social buttons and only show the buttons in content? =
Yes, the floating social buttons can be hidden (from version 1.1).

= The plugin does not appear in home page =
The plugin does not appear in home page if the home shows the latest posts. Indeed, if the home page shows a static page, the plugin works correctly. (the option "show on: pages" must be enabled).

= Why social buttons are not shown in the posts list? (tags page, categories page, home feeds, etc ...) =
Because the plugin is designed to work exclusively on individual pages (single post, single page, single custom post type)

== Screenshots ==

1. Cresta Social Share Counter Settings Page 1
2. Cresta Social Share Counter Settings Page 2
3. Floating social buttons with social count
4. Social buttons before/after posts/page content

== Changelog ==

= 2.5.6 =
* Fixed Linkedin Share Count

= 2.5.5 =
* Fixed share URL
* Fixed CSS code for floating buttons in mobile version
* Fixed share page title

= 2.5.4 =
* Added the possibility of re-use Twitter Share Count with newsharecounts.com public API (Unofficial Counter)

= 2.5.3 =
* Added a box to insert custom CSS code, if needed
* Updated compatibility with WordPress 4.5
* Minor bug fixes

= 2.5.2 =
* Improved plugin security
* Minor bug fixes

= 2.5.1 =
* Removed Twitter Share Count (no longer used by Twitter)
* Added the possibility to show the single shares count only if is more than 0
* Minor bug fixes

= 2.5.0 =
* Minor bug fixes

= 2.4.9 =
* Tweak: Changed text domain from "crestassc" to "cresta-social-share-counter"
* Minor bug fixes

= 2.4.8 =
* Updated Google Plus icon
* Updated Twitter Share Count (provisional)

= 2.4.7 =
* Updated Google Plus counter method
* Minor bug fixes

= 2.4.6 =
* Updated compatibility with WordPress 4.3
* Minor bug fixes

= 2.4.5 =
* Minor bug fixes

= 2.4.4 =
* Fixed small bug when you used the shortcode
* Updated compatibility with WordPress 4.2

= 2.4.3 =
* Fixed small bug that, in some cases, showed wrong title in Twitter box.
* Added possibility to hide/show the floating buttons via button

= 2.4.2 =
* Fixed an issue that did not display the shortcode
* Added Polish translation (Thanks to Piotr Deres)
* Minor bug fixes

= 2.4.1 =
* Minor bug fixes

= 2.4 =
* Added the possibility to set the social counter box with the same color of Social Network
* Fixed Google Plus share count
* Speed improvement
* Minor bug fixes

= 2.3 =
* Fixed a bug that did not allow you to choose where to display the social buttons
* Changed the name of the social icons font to avoid conflicts with other font
* Code cleanup
* Minor bug fixes

= 2.2.1 =
* Fixed an CSS error that sometimes did not display properly social icons

= 2.2 =
* Added the print button
* Ability to change the text of shares
* Added Metabox that allows you to hide the social icons on certain pages/posts/custom post type
* Minor Bug Fixes

= 2.1 =
* Now the plugin works with HTTPS protocol
* Added 1 new style
* Minor Bug Fixes

= 2.0.1 =
* Added the possibility to place the content buttons in the center of column
* Fixed share link
* Fixed CSS buttons height

= 2.0 =
* Added the possibility to adjust the z-index
* Added shadow and reflection on buttons
* Added Spanish localization (Thanks to Andrew Kurtis)
* Fixed javascript (NaN and number of shares)
* Fixed CSS and buttons reflection
* Minor Bug Fixes

= 1.9 =
* Now the plugin show the social buttons in home page if the home page is a static page (not blog index)
* Added 1 new style
* Minor Bug Fixes

= 1.8 =
* Added shadow on buttons
* Added Italian localization
* Minor bug fixes

= 1.7 =
* Fixed Share URL
* Now the Plugin is completely W3C Validated
* Add possibility to show / hide CrestaProject Credit

= 1.6 =
* Fixed Custom Post Type Bug

= 1.5 =
* Added 2 new styles
* Update icon
* Minor bug fixes

= 1.4 =
* Added a new field to enter the Twitter Username (optional)
* Added the possibility to choose the position of Social Buttons in content (left or right).
* Fixed Options Display
* Minor bug fixes

= 1.3 =
* Added the "Show Total Shares" option
* Now the "In content Buttons" have the same style of "Floating Buttons"
* Minor bug fixes

= 1.2 =
* Added custom post format filter
* Fixes share icons size
* Fixes page share title with special characters
* Minor bug fixes

= 1.1 =
* Added the option to disable floating buttons
* Minor bug fixes

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.0 =
This is the first version of the plugin