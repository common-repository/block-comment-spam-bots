=== Block Comment Spam Bots ===
Contributors: rhellewellgmailcom
Donate link: https://cellarweb.com/
Author URI: https://www.cellarweb.com
Plugin URI: https://www.cellarweb.com/wordpress-plugins/
Tags: comments, spam, bots, blocking, automated spam
Requires at least: 4.9
Tested up to: 6.5 
Version: 2.62
Requires PHP: 5.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

 A simple to use plugin that stops automated spam. Install and forget, and any automated spam targeting your native WordPress comments is immediately terminated, and never gets written to your WP database. Automated spam will never show up in your spam queue. Ever.

== Description ==

Professional spammers use programs to automate their spamming. The 'Block Comment Spam Bots' (BCSB) plugin efficiently blocks their process. No more comment spam!

As no legitimate user will use the professional spammer's automated process which relies on cURL and WGET commands, real users will never notice the BCSB plugin at work. There are no CAPTCHAS for your visitors to interact with. No silly questions. Just the comment form as designed in any theme.

On the admin side, there are no blacklists, special keys (like Askimet), overloaded spam queues, or overworked databases that store spam comments until you manually delete them.

Install the plugin and that's it. Invisible, to you and your visitors. The only change you will notice is in your admin area. The list of comments now has a green check next to them. That way you know that comment was made on your website by a real person and was not bypassed by hacking spammers connecting directly to your server.

All that remains is comments made by real people, and while real people can spam, it takes them time and effort. The amount of spam from real people is a lot more manageable than the tsunami from automated spammers, saving you time to concentrate on the important things in life, like your readers, and making connections.

We've tested it on multiple websites and it wipes out automated spam completely. If it doesn't on your site, please let us know.

** Geeky Stuff **
...in case you are interested in how it works...

tl;dr - **This provides a total and easy solution to comment spam from spam bots.**

Comments are processed by the WordPress wp-post-comments.php file. Automated spammers ('spam bots') can provide ('post') data directly to that page, bypassing any comment processing, by using CURL/WGET commands.

Bypassing the comment form by posting directly (via CURL or WGET commands), is quite easy. Just send the post ID number, and the bot's fake name and email, and the spammy content. Boom! Comment spam is on your site!

The result is comment spam - and that is not always caught by other comment spam checkers. Even if it is caught by programs such as Akismet, processing that spam takes some server resources, including writing to the database.

This plugin uses several techniques to 'sense' a spambot. There are hidden fields that are changed after a delay. There is a delay in displaying the submit button. And it blocks direct access to the WordPress post/processing functions.

The techniques, also used in our standalone "FormSpemmerTrap" (FST) program, and our other anti-spam plugins (like FormSpammerTrap for Comments), are very effective. They use a bit of JavaScript to block spambots - since automated processes via CURL/WGET/etc cannot process JS code.

It's simple: you install this plugin, activate it, and bot comments will stop. Immediately.

And it doesn't add any visual impediments to your comments. No reCaptcha things (which many see as a pain). No silly questions ('what is 2+8') on the form. Your comment form does not change. Regular users will not notice a difference. But you will. No more spam comments for you!

**This is the best solution to block comment spam.** We've tested it on a site that had 20-40 spam comments a day. With this plugin enabled, the spam comment stopped. Immediately. And there have been none since installing this plugin. ** Not one. Zero.**

The Admin, Comments list page is modified to show a column with a green checkmark icon if the comment was entered by a real person and not a bot. This is an assurance that the comment was not entered via an automated CURL/WGET to the wp-comments-post.php file. A comment that is on the list that does not show the checkmark was done by a bot. But you won't see those blocked comments with this plugin enabled. They never get into your database. You can hover over the checkmark icon to see the GUID value indicating a person entered the comment.

The plugins 'Settings' screen has no settings. You don't even need to look at the Settings screen. If you do, you'll see information about the plugin. And there is a CURL command you can use to test the effectiveness of blocking (or not blocking) direct access to the wp-comments-post.php file.

The plugin also adds the hidden GUID field to the comment form after a delay to help block bots that are using the comment form to submit. If the hidden field is not submitted then a bot tried to bypass the comment form. And a short delay happens before the comment submit button is displayed - another bot protection.


== Installation ==

This section describes how to install the plugin and get it working.


1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Settings->Plugin Name screen to configure the plugin
1. (Make your instructions match the desired user flow for activating and installing your plugin. Include any steps that might be needed for explanatory purposes)

== Frequently Asked Questions ==

= Does it really work? =

Yep. We've tested it on a site that was getting 20-40 spam comments a day. We installed and activated the plugin, and the spam comments stopped immediately. And just like that battery rabbit, it's still going strong, blocking comment spam. The plugin will work the same on your site - no more comment spam!

= Does this modify the comment form? =

The comment form will look as it always did. No additional visible fields. No reCaptchas. No silly questions. Only the spambots will notice the difference - and we don't care about them.

= Are there any settings? =

Nope. Just an information screen about how it works, including an easy way to test blocking automated comment spam.

= What about customized comment forms? =

The plgin makes no changes to the visual or operational comment form. It just adds a hidden field with a unique value, then checks for that field on submit. It delays showing the Submit button. And it changes field names and values to block automated 'scrapers'. Plus it blocks direct posting to the comment processing code.

= What about Contact forms? =

This plugin doesn't affect Contact forms; it just works on comments.

But we have a solution for Contact forms - see our https://www.FormSpammerTrap.com (FST) site. It works on WordPress and other sites. Takes a small bit of customization for your WP theme, but full instructions and examples are included. And it is quite easy to customize your contact form. An example of a customized contact form is on the FST site. (We will implement the Contact form customization on your site for a small fee.)

And, like this plugin, it's entirely free.

= So a full solution for comment and contact spam is ...? =

This plugin which takes care of comment spam, plus the FormSpammerTrap code you can easily add to your site for Contact forms.

You're welcome!


== Screenshots ==

1. No screenshots; no settings screen needed.


== Changelog ==

** Version 2.62 (4 Apr 2022) **
- Improvements to hidden field area; reduces chance of warning-type error.

** Version 2.6/2.61 (16 Feb 2022) **
- The hidden field is only added on pages (posts) with the comment form. Previously that JS code was added to all pages, causing issues with forms on other pages having the JS code to process a hidden field that wasn't there.

** Version 2.5 (30 Jan 2022 - later that same day) **
- Removal of override of the 'p' font size on the front end. Snuck in there while I wasn't looking.

** Version 2.4 (30 Jan 2022) **
- Minor changes to the header of the Setting page (to add the CellarWeb.com logo).
- Minor changes to the text on the Settings page.
- CSS file is now cache-proof.
- Change to the image shown on the plugin page.
- CellarWeb.com logo now a jpg for faster loading.

** Version 2.3 (28 Jan 2022) **
- Changes to the plugin description and text on the Settings/Info screen, and to the text of this readme file. Changes inspired by suggestions from 'phillip-s' (Thanks again!).
- Enhanced the fake field to the comment form - which is changed after a delay.
- Code cleanup, removing unused functions.
- minor visual changes to the Settings page text; added shadows to the boxes.
- Changes to this readme file.


** Version 2.2 (23 Jan 2022)**
- Fixed bug where replying to a comment in the comment admin area would give you a 'go away spammer' message on submitting the reply. This required a more complex check if you were in the admin area; the GUID value is forced into the $_POST if in admin area.  (Thanks to phillip-s for the alert.)

(Geeky explanation) The enhancement uses the role of the current user to check their 'moderate comments' capability. Admin, editors, and author roles have that capability. Since those roles allow them access to the admin/comments screen (and editor/authors only see the comments for their level, as opposed to the admin role that can see everything), then those roles can see the 'reply' link for each comment, and we don't need to add any special fields to the comment drop-down form. We just add the GUID for that instance, because a valid user is replying to (which is creating a new comment) via the admin/comments screen.
- Fixed bug where the GUID value is displayed on added columns from other plugins, rather than the date from those other plugins. (Thanks to phillip-s for the alert.)
- Changed the heading for indicating that the comment was done by a human from "Bot Blocked?" to "Comment Verified". The old heading could indicate that the comment was from a bot, rather than from a human.
- Changed the display of the "Comment Verified" column to show a green check mark, rather than the GUID value. If you hover over the checkmark icon, you will see the GUID value in a tooltip. Done to show less clutter on comment list screen.
- Added constant for the checkmark icon for a slight performance increase. so we don't call the plugin_dir_url function for each checkmark icon display.
- Fixed minor warning-type error in the function that checks if the GUID value is in the POST on comment submit.
- Changed the BCSB_VERSION_NUMBER from a global variable to a constant (used on the plugin info pages).
- Added additional text to the settings screen reminding you to refresh that screen if you are trying the CURL command again. This changes the random value in the sample CURL command, so you don't get a 'duplicate comment' message.
- Minor text changes to the Settings screen.
- Minor code formatting.

**Version 2.1 (24 July 2020)**
- added a delay to showing the 'submit' button. It will display after a short delay. This will prevent an inadvertent 'spammer catch' of a person that creates a comment offline, then pastes the comment text into the comment box and then submits before the timeout. (The timeout is there to prevent a bot submission of the comment.)
Initially, the person will not see the submit button. After the short delay, the submit button will appear as normal.

**Version 2.0 (23 July 2020)**
- fixed bug where hidden field wasn't being inserted into the comment form if the user was **not** logged in. Bug didn't happen when user was logged in.
- set the extra hidden field to not be visible on the form.
- note that this plugin uses the  wp_generate_uuid4() function to create a (mostly) random value used in the hidden field after the delay. This value is not truly random; there is the possibility of duplicates. But we don't care if there are duplicates, just that it's a WP-verifiable UUID, and that it was changed after the delay. (The delay in changing that hidden fields, and verifying it is a WP-valid UUID, is one of the layers of spambot protection.)
- Changed heading/text of the hidden meta value shown on the Admin Comment Editing screen, and made the field read-only.
- Added single-click of the CURL command on the Settings page to get it into your clipboard.
- removed some unused/testing code.

**Version 1.5 (1 Jan 2020)**
- Changed the styling of the box that shows the CURL command for the site.
- Added an additional image showing a possible result from the CURL command.
- Minor CSS changes.
- Some minor changes to the information on the settings/information screen.

**Version 1.4 (29 Dec 2019)**
- Added more info to the FAQ area.
- Some more info on the Settings/Info screen.

**Version 1.3 (24 Dec 2019)**
- Added the storage and display of the hidden field on the Admin, Comments screen. That field can be edited, although not sure why you would want to.
- The addition of a column for the hidden field value will allow you to see if a spammy comment was entered manually. A blank value indicates that the comment was entered manually.
- Added a timed delay to change the value of the hidden field, to prevent automated entry of the actual comment form.
- Added additional information on the 'Info/Settings' screen, including the CURL command you can use to try to automated a comment.
- All function and variable names now have a prefix to ensure that there are no conflicts with other core/theme/plugin functions or values.
- Added CSS files, and images in the assets folder.
- Some minor changes to this readme file for additional information.

**Version 1.2 (23 Dec 2019)**
- Not released/testing version

**Version 1.1 (18 Dec 2019)**
- Initial Release (prior versions used in development only)

== Upgrade Notice ==

**Version 1.5 (1 Jan 2020)**
- Changed the box that shows the CURL command for the site.

**Version 1.4 (29 Dec 2019)**
- Added more info to the FAQ area.
- Some more info on the Settings/Info screen.

**Version 1.3 (24 Dec 2019)**
- Added the storage and display of the hidden field on the Admin, Comments screen. That field can be edited, although not sure why you would want to.
- The addition of a column for the hidden field value will allow you to see if a spammy comment was entered manually. A blank value indicates that the comment was entered manually.
- Added a timed delay to change the value of the hidden field, to prevent automated entry of the actual comment form.
- Added additional information on the 'Info/Settings' screen, including the CURL command you can use to try to automated a comment.
- All function and variable names now have a prefix to ensure that there are no conflicts with other core/theme/plugin functions or values.
- Added CSS files, and images in the assets folder.
- Some minor changes to this readme file for additional information.

**Version 1.2 (23 Dec 2019)**
- Not released/testing version

**Version 1.1 (18 Dec 2019)**
- Initial Release (prior versions used in development only)
