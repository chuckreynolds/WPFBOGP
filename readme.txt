=== WP Facebook Open Graph protocol ===
Contributors: ryno267
Donate link: http://rynoweb.com/wordpress-plugins/
Tags: open graph, facebook, ogp, facebook meta, open graph meta, featured image, 
Requires at least: 3.0
Tested up to: 3.2
Stable tag: trunk

Super betta way to do Facebook Open Graph stuff for your site and/or blog


== Description ==

This plugin adds Facebook Meta information on your site and properly designates if it's an article or a website for your pages.

IMAGE HANDLING:
Plugin will first look for a featured image. If that isn't there, then it will pull the first image in the content. If that isn't there either, then it will default to the image you put into the plugin settings in the admin panel. If that isn't then then... well you fail and you won't have an image.

TESTING:
Once you've enabled the plugin head over to Facebook's testing tool and input one of your blog url's or your main site url to see what info Facebook is finding: https://developers.facebook.com/tools/lint/

ROADMAP:
Secrets! Not getting crazy on features but there is MUCH to be desired in current OGP plugins, just need more time to implement ;) Stay tuned!

== Installation ==

1. Upload the `wp-facebook-ogp` folder to the `/wp-content/plugins/` directory
1. Activate the WP Facebook OGP plugin through the 'Plugins' menu in WordPress
1. You MUST add a Facebook ID to the Plugin Settings page for the plugin to produce OGP meta data


== Frequently Asked Questions ==

= Do I need to create a Facebook Application to use this plugin? =

Short answer is no. Your Facebook user ID or or an Application ID is a requirement. You don't need to register an app, just use your User ID (plugin admin helps you find that). You can have both App ID and User ID if you'd like. More details on how Facebook verifies admins is located here: http://developers.facebook.com/docs/opengraph/#admin


== Screenshots ==



== Changelog ==

= 0.0.10 =
* fixed the continue reading injection added to the end of excerpts in twentyten and twentyeleven themes/childs that look bad in meta desc
* fixed admin width issue for table, now displays correct
* verbiage updates - prep for full help menu setup

= 0.0.9 =
* added admin notifications if plugin is activated but doesn't have the required fields (app id or user id) saved

= 0.0.8 =
* if no default image set no longer display a blank og:image tag, now it will show a comment in source reminding you to add a default image in the plugin settings

= 0.0.7 =
* updated admin UI - now follows WP 3.2 style admin UI. Very Nice!

= 0.0.6 =
* lots of changes!
* titles, descriptions and urls all now working with home and all other page/posts
* og:type handled better and working on home and page/posts
* added optional page_id field if ppl want to associate insights with their page instead. but still need a user or app id req
* more admin help / reference information linked up. promotional tweet about this plugin added. big help/instruction overhaul coming soon.
* lots of little changes I forget and some clean up sweeping

= 0.0.1 =
* added meta field if no admin id set as to give instruction to liven up plugin
* more readme explanation/help

= 0.0.1 =
* Initial beta release


== Other Notes ==

