=== WP Facebook Open Graph protocol ===
Contributors: ryno267
Donate link: http://goo.gl/8lGv3
Tags: facebook, open graph, ogp, google +1, +1, google plus one, plus one, facebook meta, open graph meta, featured image, facebook share, facebook like 
Requires at least: 3.0
Tested up to: 3.3
Stable tag: trunk

Adds the proper Facebook meta tags and values to your site and/or blog so when your pages and posts are shared it looks awesome and provides Facebook with good data.
NEW: Plugin will work for the new +Snippets for Google +1 Button!

== Description ==

This plugin adds Facebook Meta information to your site and properly designates if it's an article or a website page. There will be more in regard to this in soonish releases. See Roadmap info below.
As of 08/24/2011 this plugin will also provide +Snippet data to the Google +1 Plus One Button.
Shortname: WPFBOGP

= Image Handling =
The plugin will first look for a featured image. If there isn't one or your theme doesn't have those available, then it will pull the first image in the post/page content. If that isn't there either, then it will default to using the image you put into the plugin settings in the admin panel. If THAT isn't there then... well you fail and you won't have an image. The plugin will still work fine but it won't look as pretty on your Facebook wall. People click more on wall posts with images and your site will have better reader conversion with an image in the content. Fact.

= Title and Description =
On your home/index it will display your site name (from wp settings), otherwise will display whatever the page or post title is. Description on single posts will first look for an excerpt and if that's not there it now will auto-generate one from the first 160 characters of the content. On main pages it will use the site description (from wp settings). I've updated it to do so due to popular request.

= Testing Your Site =
Once you've enabled the plugin head over to Facebook's testing tool and paste in one of your post/page url's or your home page to see what info Facebook is pulling in. This tool is located here: <a href="http://developers.facebook.com/tools/debug">http://developers.facebook.com/tools/debug</a>

= Plugin Roadmap =
This plugin is fully featured as is right now. Though I'm not going into the details here, but there is MUCH to be desired in current OGP plugins and I just need more time to implement them into this plugin. But they'll make it in. Stay tuned!

== Installation ==

1. Upload the `wp-facebook-ogp` folder to the `/wp-content/plugins/` directory
1. Activate the WP Facebook OGP plugin through the 'Plugins' menu in WordPress
1. You MUST add your Facebook ID to the Plugin Settings page for the plugin to produce OGP meta data

== Frequently Asked Questions ==

= Do I need to create a Facebook Application to use this plugin? =

Short answer is no. Either your Facebook user ID or or an Application ID is a requirement. You don't need to register an app, just use your User ID (plugin admin helps you find that). You can have both App ID and User ID if you'd like. More details on how Facebook verifies admins is located here: http://developers.facebook.com/docs/opengraph/#admin

= Why doesn't this plugin have a Like/Send button? =
Honestly it's not hard to add one once you have the proper meta content in the header. Look at <a href="http://developers.facebook.com/docs/opengraph/#plugins">how to add a Like button</a> using fb:like. I may consider incorporating a basic layout one if there's enough demand for it. Let me know -> <a href="http://twitter.com/chuckreynolds">@chuckreynolds</a>

== Screenshots ==

1. The FB OGP Admin options panel has all the control laid out in one easy place. The rest is all behind the scenes.

== Changelog ==

= 1.4 =
* fixed bug with line returns if they were in auto desc
* added strip_shortcodes on autogen desc
* updated image handling post id issue and changed medium size image to thumbnail for optimization and speed
* fixed og:url to work with every page type and archive type

= 1.3 =
* Added much requested auto-description generation from content. So now on single posts, it will still look for the post excerpt and if one doesn't exist it will auto-generate one from the first 160 characters of the content field.
* redacted some other updates to push this one feature out - more soonish.

= 1.2 =
* fixed if latest post in blog listing had a featured image, og:image would use that instead of default image from admin. Fixed that.

= 1.1 =
* fixed an issue with single quotes in excerpts ruining the meta description (props Chris Jensen)
* added link to support form for bugs and/or feature requests & minor copy updates in admin

= 1.0 =
* initial public release on wordpress repo
* lots of verbiage and help updates. more to come here as I think it's still too confusing.
* removed help menu function until I can get something more useful in there. next version.

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

== Upgrade Notice ==

= 1.3 =
Added much requested auto-description generation as fallback if single has no excerpt. Enjoy.

== Other Notes ==

