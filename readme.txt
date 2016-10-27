=== WP Facebook Open Graph protocol ===
Contributors: ryno267, andrewryno
Donate link: http://goo.gl/8lGv3
Tags: open graph, ogp, facebook open graph, google +1, +1, google plus one, plus one, linkedin share, facebook meta, open graph meta, facebook share, facebook like, linkedin
Requires at least: 3.0
Tested up to: 4.6.1
Stable tag: 2.0.13
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds proper Facebook Open Graph Meta tags and values to your site so when links are shared it looks awesome!

== Description ==
This plugin adds well executed and accurate Facebook Open Graph Meta tag information to your site. The idea is to keep minimal settings and options as to remain out of your way and in the background while still proving a powerful Open Graph plugin for your WordPress site. This plugin works on Facebook, Google Plus, and Linkedin.
Shortname: WPFBOGP

= Image Handling =
By popular demand we've added a setting so the Fallback image in settings can be the default sitewide. If you don't check that box, here's now she works.
The plugin will first look for a featured image. If there isn't one or your theme doesn't have those available, then it will pull the image(s) in the post/page content. If that isn't there either, then it will default to using the image you put into the plugin settings in the admin panel. If THAT isn't there then... well you fail and you won't have an image and we'll put a comment in your source to remind you to add one as Facebook requires one.
New in 2.0 - We'll pull ALL those images and feed them to Facebook and Google + so you can hit the arrows to select which one you want. It will use the fallback image first IF it's selected as default, then will do featured image next and then any content images.
Test with the <a href="http://developers.facebook.com/tools/debug">Facebook Debugger</a>.

= Title and Description =
New in 2.0+, the plugin will use the title and meta description from ANY SEO plugin or theme, including Genesis and Thesis. Worst case it'll fall back to pulling some of your content as a last-ditch backup. But if you're concerned with what your Open Graph tags look like, then you should probably be running some kind of SEO plugin anyways. AMIRITE?

= Testing Your Site =
Once you've enabled the plugin head over to Facebook's testing tool and paste in one of your post/page url's or your home page to see what info Facebook is pulling in. This tool is located here: <a href="http://developers.facebook.com/tools/debug">http://developers.facebook.com/tools/debug</a>

= Plugin Roadmap =
If you have feature requests or bugs? Use the support links on <a href="http://rynoweb.com/wordpress-plugins/?utm_source=wpfbogp_readme" title="rynoweb wordpress plugins">rYnoweb.com/WordPress-Plugins</a> and get in touch.

== Installation ==

1. Upload the `wp-facebook-ogp` folder to the `/wp-content/plugins/` directory
1. Activate the WP Facebook OGP plugin through the 'Plugins' menu in WordPress
1. You MUST add your Facebook ID OR an App ID to the Plugin Settings page for the plugin to produce OGP meta data. This is required by Facebook.

== Frequently Asked Questions ==

= Do I need to create a Facebook Application to use this plugin? =
No. Either your Facebook User ID or or an Application ID is a requirement. You're not required to register an app, instead just use your User ID (plugin admin settings page helps you find that). You can use both an App ID and User ID if you'd like. More details on how Facebook verifies admins is located here: https://developers.facebook.com/docs/insights/

= Why doesn't this plugin have a Like/Send button? =
Honestly it's not hard to add one once you have the proper meta content in the header. Look at <a href="https://developers.facebook.com/docs/reference/plugins/like/">how to add a Like button</a> using fb:like. There are a lot of 'like' button plugins but this one focuses on solid and accurate Open Graph meta data.

== Screenshots ==

1. The Open Graph admin options panel has all options laid out in one easy place. The rest is all behind the scenes.

== Upgrade Notice ==
= 2.0.13 =
Small patch for sites use .html in their url permalink structure

= 2.0.12 =
Quick Patch - Changes how trailing slashes are handled. Big update coming soon too!

= 2.0.11 =
Fixes an issue some had with thumbnail image paths & fixes php notice some were getting. Cheers.

= 2.0.9 =
uses large size thumbnail instead of medium. bigger is better!

= 2.0.8 =
fixes feed problems some people are having. this is an interm upgrade before the massive rewrite coming soon

= 2.0.7 =
fixes bug with certain plugins not showing description tags properly

= 2.0.6 =
preg_replace causing LOTs of issues. rolled back to fix admin issues. more planning needed. big update coming.

= 2.0.5 =
Fixes issues with dollar signs and special characters in titles and descriptions

= 2.0.3 =
This update should fix the "Parser Mismatched Metadata" warnings Facebook started throwing.

= 2.0.2 =
Fixes bug that caused a PHP warning to display and also fixes bugs with plugins not functioning properly when using content filters.

= 2.0.1 =
Bug fixes for HTTPS/SSL pages, SEO plugins, force fallback images and more.

= 2.0 =
BIG Update - now works with ALL SEO plugins for titles and descriptions, adds multiple images and other fixes!

= 1.6.1 =
Bug fix with 1.6 initial release. Titles broke for some running 'naked sites' w/ no seo plugins. It worked on all testing sites but obviously I need to test a little more. Standby

= 1.6 =
Because we're getting more accurate titles you may see a change in how they're pulled. If you're using an SEO plugin it should now pull that title

== Changelog ==

= 2.0.13 =

Release Date - 2015-07-06

* Fixed trailing slash issue for sites that use .html in their article url permalink structure. Should play nice now.

= 2.0.12 =

Release Date - 2015-06-19

* Changed how og:url is called and handle trailing slashes better all around
* Fixed issue when using the blog as a page had root url

= 2.0.11 =

Release Date - 2014-07-31

* Check for relative URLs in post thumbnails images - props -> github.com/jjeaton
* fixes problem w/ strings in image array
* flip image array. this will move your default image (if you have one) to the top of the array but Facebook pulls it the way they want anyways.

= 2.0.10 =
* Remove deprecated argument from `get_the_excerpt`. Fixes PHP Notice. props -> github.com/jjeaton

= 2.0.9 =
* bump og:image size to large like beta/dev version. bigger is better. but size doesn't matter? well it's large now
* tested to WP3.9

= 2.0.8 =
* update html namespace to prefix (ogp.me) from xmlns (facebook)
* dont run buffer if in a feed
* fixed media uploader link

= 2.0.7 =
* fixes bug with certain plugins not showing description tags properly
* added back self-close on meta tags to preserve xhtml compatability, html5 is forgiving. core does this.

= 2.0.6 =
* The changes we made to fix the dollar sign in title problem caused big problems in wp admin. rolling back for now while we plan and recode a lot of the plugin w/ norcross for a bigger update. Just don't use dollar signs for now in titles. sorry. standby.

= 2.0.5 =
* Short: Dollar signs in titles were screwing things up. Long Story: Fixed a bug where preg_replace() would think a dollar sign in the argument is a backreference, instead of just a dollar sign. Now using preg_quote() to escape all regex variables/symbols in a string. Makes things mo betta!

= 2.0.4 =
* Image size for OpenGraph now defaults to medium for thumbnail image
* updated screenshot. it was from v1 so was about time
* fixed grammar error - props Jeff K.

= 2.0.3 =
* Should fix the "Parser Mismatched Metadata" warnings Facebook started throwing. Made locale all lowercase.

= 2.0.2 =
* Fixes bug that would display a PHP warning with debug mode turned on.
* Removed code that would filter the_content() which caused problems with plugins that also used that same filter.

= 2.0.1 =
* Fixed bug where og:url was returning a https URL instead of http, causing Facebook Linter to fail. Now using the WordPress function is_ssl() instead of the $_SERVER global variable.
* Fixed bug with some SEO plugins where the page title and meta description were not being pulled correctly.
* Changed front pages to have the og:type 'website', not 'article'.
* Fixed bug where the 'force fallback' checkbox will check itself after initial installation.

= 2.0 =
* Now works with ALL SEO plugins AND Frameworks like Genesis and Thesis. It now grabs the actual outputted <title> tag and <meta> description after all SEO plugins/themes/etc have done their job.
* New Open Graph feature allow multiple images therefore we now add all those properly for selection (in Facebook, Google +, and even now Linkedin) if there are more than one in the post/page in addition to any featured image and the fallback image in settings (only if force fallback isn't selected).
* Removed og:page_id - Facebook depreciated that 1 April 2012.
* Adds filters to all outputted values.
* Images in Galleries work too now as we apply filters to the_content() before pulling images.
* Some house cleaning: old functions removed, code cleaned up, and better follows WordPress coding standards.
* Modified plugin settings help links - facebook keeps changing things :/
* Last but certainly not least... Improves performance == WIN

= 1.6.1 =
* Bug fix with 1.6 initial release. Titles broke for some running 'naked sites' w/ no seo plugins. It worked on all testing sites but obviously I need to test a little more. Standby

= 1.6 =
* update help info and links as Facebook has changed a lot about their docs including image should be 200px square now
* use wp_title() to get the title of the current page. SEO plugins filter wp_title so we will get the best title available. This is a baby step towards bigger and better things :)
* fix bug in scraping content images
* add settings option to allow fallback image to become site-wide default

= 1.5.2 =
* bug fix in urlpath again & check for https. Props goes to Seb Francis at burnit.co.uk for better fix.

= 1.5.1 =
* bug fix in image basepath thing I fixed in 1.5... false checking. make sure to update this or your images could not display on facebook

= 1.5 =
* simple cleanup and couple bug fixes
* fix image path auto pulled from content if there wasn't a basepath
* fix for custom post type og:description. (props Leia Scofield) did same for og:type
* remove hidden code for old contextual menus that never happened

= 1.4 =
* added og:locale as facebook has started requiring that for valid ogp

= 1.3.5 =
* fixed bug with line returns if they were in auto desc
* added strip_shortcodes on autogen desc
* updated image handling post id issue and changed medium size image to thumbnail for optimization and speed
* fixed og:url to work with every page type and archive type
* x.5 update because needed to push some of these bugs out but too busy to fix others. more updates coming soon.

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

= 0.0.2 =
* added meta field if no admin id set as to give instruction to liven up plugin
* more readme explanation/help

= 0.0.1 =
* Initial beta release

== Other Notes ==

