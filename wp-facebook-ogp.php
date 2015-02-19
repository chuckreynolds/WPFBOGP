<?php
/*
Plugin Name:    WP Facebook Open Graph protocol
Plugin URI:     https://wordpress.org/plugins/wp-facebook-open-graph-protocol/
Description:    Adds proper Facebook Open Graph Meta tags and values to your site so when links are shared it looks awesome! Works on Google+ and Linkedin too!
Version: 		2.3.0-beta.2
Author: 		Chuck Reynolds
Author URI: 	https://chuckreynolds.us
License:		GPLv2 or later
License URI: 	http://www.gnu.org/licenses/gpl-2.0.html

Copyright 2014 Chuck Reynolds (email : chuck@rynoweb.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

define( 'WPFBOGP_VERSION', '2.3.0-beta.2' );
wpfbogp_admin_warnings();

/**
* Provide a fallback if server php doesn't have mb_substr enabled
*
* @link http://php.net/manual/en/mbstring.installation.php
*
* @return void
*/
if (!function_exists('mb_substr')) {
	function mb_substr($str , $start, $length = null, $encoding = 'UTF-8') {
		return is_null($length) ? substr($str , $start) : substr($str , $start, $length);
	}
}

/**
* Drop filter for when jetpack has their ogp stuff on
*
* @return void
*/
function wpfbogp_filter_jetpackogp () {
	add_filter( 'jetpack_enable_open_graph', '__return_false' );
}

/**
* Add OGP namespace per ogp.me schema
*
* @param string $output The output namespace string
*
* @return string with opg.me schema added
*/
function wpfbogp_namespace( $output ) {
	return $output.' prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#"';
}
add_filter( 'language_attributes','wpfbogp_namespace' );

/**
* Function to call first uploaded image in content
*
* @return array of images from the current post
*/
function wpfbogp_find_images() {
	global $post;

	if( !is_object($post) || get_class($post) !== 'WP_Post' ) {
		return array();
	}

	// Grab content and match first image
	$content = $post->post_content;
	$output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches );

	// Make sure there was an image that was found, otherwise return false
	if ( $output === FALSE ) {
		return false;
	}

	$wpfbogp_images = array();
	foreach ( $matches[1] as $match ) {
		// If the image path is relative, add the site url to the beginning
		if ( ! preg_match('/^https?:\/\//', $match ) ) {
			// Remove any starting slash with ltrim() and add one to the end of site_url()
			$match = site_url( '/' ) . ltrim( $match, '/' );
		}
		$wpfbogp_images[] = $match;
	}

	return $wpfbogp_images;
}

/**
* Start the output buffer for 'wpfbogp_callback'
*
* @return void
*/
function wpfbogp_start_ob() {
	// Start the buffer before any output
	if ( ! is_feed() ) {
		ob_start( 'wpfbogp_callback' );
	}
}

/**
* Create the og:title and og:description tag with content from blog <title> and meta description
*
* @return string of og meta tags
*/
function wpfbogp_callback( $content ) {
	// Grab the page title and meta description
	$title = preg_match( '/<title>(.*)<\/title>/', $content, $title_matches );
	$description = preg_match( '/<meta name="description" content="(.*)"/', $content, $description_matches );

	// Take page title and meta description and place it in the ogp meta tags
	if ( $title !== FALSE && count( $title_matches ) == 2 ) {
		$content = preg_replace( '/<meta property="og:title" content="(.*)">/', '<meta property="og:title" content="' . $title_matches[1] . '">', $content );
	}

	if ( $description !== FALSE && count( $description_matches ) == 2 ) {
		$content = preg_replace( '/<meta property="og:description" content="(.*)">/', '<meta property="og:description" content="' . $description_matches[1] . '">', $content );
	}

	return $content;
}

/**
* End the output buffer for 'wpfbogp_callback'
*
* @return void
*/
function wpfbogp_flush_ob() {
	if ( ! is_feed() ) {
		ob_end_flush();
	}
}

add_action( 'init', 'wpfbogp_start_ob', 0 );
add_action( 'wp_footer', 'wpfbogp_flush_ob', 10000 ); // Fire after other plugins (which default to priority 10)

/**
* Build ogp meta
*
* @return string of meta tag content
*/
function wpfbogp_build_head() {
	global $post;

	if( !is_object($post) || get_class($post) !== 'WP_Post' ) {
		return '';
	}

	$options = wpfbogp_get_option();
	// check to see if you've filled out one of the required fields and announce if not
	if ( ( ! isset( $options['wpfbogp_admin_ids'] ) || empty( $options['wpfbogp_admin_ids'] ) ) && ( ! isset( $options['wpfbogp_app_id'] ) || empty( $options['wpfbogp_app_id'] ) ) ) {
		echo "\n<!-- Facebook Open Graph protocol plugin requires a FB User ID or App ID to work, please visit the plugin settings page! -->\n";
	} else {
		echo "\n<!-- WordPress Facebook Open Graph protocol plugin (WPFBOGP v".WPFBOGP_VERSION.") http://rynoweb.com/wordpress-plugins/ -->\n";

		// do fb verification fields
		if ( isset( $options['wpfbogp_admin_ids'] ) && ! empty( $options['wpfbogp_admin_ids'] ) ) {
			echo '<meta property="fb:admins" content="' . esc_attr( apply_filters( 'wpfbogp_app_id', $options['wpfbogp_admin_ids'] ) ) . '" />' . "\n";
		}
		if ( isset( $options['wpfbogp_app_id'] ) && ! empty( $options['wpfbogp_app_id'] ) ) {
			echo '<meta property="fb:app_id" content="' . esc_attr( apply_filters( 'wpfbogp_app_id', $options['wpfbogp_app_id'] ) ) . '" />' . "\n";
		}

		// do url stuff based on rel_canonical in wp
		#if ( !is_singular() ) {
		if (is_home() || is_front_page() ) {
			#$wpfbogp_url = get_bloginfo( 'url' ) . '/';
			$wpfbogp_url = trailingslashit( home_url() );
		} else {
			$wpfbogp_url = 'http' . (is_ssl() ? 's' : '') . "://".$_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		}
		echo '<meta property="og:url" content="' . esc_url( apply_filters( 'wpfbogp_url', $wpfbogp_url ) ) . '" />' . "\n";

		// do title stuff
		if (is_home() || is_front_page() ) {
			$wpfbogp_title = get_bloginfo( 'name' );
		} else {
			$wpfbogp_title = get_the_title();
		}
		echo '<meta property="og:title" content="' . esc_attr( apply_filters( 'wpfbogp_title', $wpfbogp_title ) ) . '" />' . "\n";

		// do site title general
		echo '<meta property="og:site_name" content="' . get_bloginfo( 'name' ) . '" />' . "\n";

		// do descriptions
		if ( is_singular() ) {
			if ( has_excerpt( $post->ID ) ) {
				$wpfbogp_description = get_the_excerpt();
			} else {
				$wpfbogp_description = preg_replace('/\s+/', ' ', mb_substr( strip_tags( strip_shortcodes( $post->post_content ) ), 0, 160, 'UTF-8' ) );
			}
		} else {
			$wpfbogp_description = get_bloginfo( 'description' );
		}
		$wpfbogp_description_clean = sanitize_text_field( strip_shortcodes( $wpfbogp_description ) );
		echo '<meta property="og:description" content="' . apply_filters( 'wpfbogp_description', $wpfbogp_description_clean ) . '" />' . "\n";

		// do ogp type
		if ( is_single() ) {
			$wpfbogp_type = 'article';
		} else {
			$wpfbogp_type = 'website';
		}
		echo '<meta property="og:type" content="' . esc_attr( apply_filters( 'wpfbpogp_type', $wpfbogp_type ) ) . '" />' . "\n";

		// Find/output any images for use in the OGP tags
		$wpfbogp_images = array();

		// Only find images if it isn't the homepage and the fallback isn't being forced
		if ( ! is_home() && $options['wpfbogp_force_fallback'] != 1 ) {
			// Find featured thumbnail of the current post/page
			if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( $post->ID ) ) {
				$thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
				$link = $thumbnail_src[0];
 				if ( ! preg_match( '/^https?:\/\//', $link ) ) {
 					// Remove any starting slash with ltrim() and add one to the end of site_url()
					$link = site_url( '/' ) . ltrim( $link, '/' );
				}
				$wpfbogp_images[] = $link; // Add to images array
			}

			if ( wpfbogp_find_images() !== false && is_singular() ) { // Use our function to find post/page images
				$wpfbogp_images = array_merge( $wpfbogp_images, wpfbogp_find_images() ); // Returns an array already, so merge into existing
			}
		}

		// Add the fallback image to the images array (which is empty if it's being forced)
		if ( isset( $options['wpfbogp_fallback_img'] ) && $options['wpfbogp_fallback_img'] != '' ) {
			$wpfbogp_fallback_img = esc_attr( apply_filters( 'wpfbogp_fallback_img', $options['wpfbogp_fallback_img'] ) );

			if ( is_array( $wpfbogp_images ) )
			{
				$wpfbogp_images[] = $wpfbogp_fallback_img; // Add default img to image array
				$wpfbogp_images; // order now is: feat img, content imgs, default-unchecked
			}
			else {
				$wpfbogp_images = array( $wpfbogp_fallback_img ); // Replace image array with default image as index 0
			}
		}

		// Make sure there were images passed as an array and loop through/output each
		if ( ! empty( $wpfbogp_images ) && is_array( $wpfbogp_images ) ) {
			foreach ( $wpfbogp_images as $image ) {
				echo '<meta property="og:image" content="' . esc_url( apply_filters( 'wpfbogp_image', $image ) ) . '" />' . "\n";
			}
		} else {
			// No images were outputted because they have no default image (at the very least)
			echo "<!-- No featured or content images are found and no default image is set in the plugin settings! -->\n";
		}

		// do locale // make lower case cause facebook freaks out and shits parser mismatched metadata warning
		echo '<meta property="og:locale" content="' . strtolower( esc_attr( get_locale() ) ) . '" />' . "\n";
		echo "<!-- // end wpfbogp -->\n";
	}
}

/**
* Getter for the wpfbogp options-array
*
* Use this getter whenever you need to get the plugin options.
* It checks for options on multi site level first.
* Should there be none, it checks the blog options.
*
* @return array of wordpress options "wpfbogp"
*/
function wpfbogp_get_option() {
	#if( is_multisite() == true ) {
	#	$options = get_site_option('wpfbogp', false, true);
	#}
	#if( $options === false ) {
	#	$options = get_option('wpfbogp');
	#}
	$options = get_site_option( 'wpfbogp', false, true );
	return $options;
}
// hhvm log was outputting this error. get_site_option will fallback to get_option if not multisite
// Notice: Undefined variable: options in /var/www/public/wp-content/plugins/WPFBOGP/wp-facebook-ogp.php on line 253
// Notice: Undefined variable: options in /var/www/public/wp-content/plugins/WPFBOGP/wp-facebook-ogp.php on line 256

/**
* Deletes all wpfbogp from wordpress options table
*
* It deletes the blog options as well as multisite options.
*
* @return void
*/
function wpfbogp_delete_option() {
	if( is_multisite() == true ) {
		delete_site_option( 'wpfbogp' );
	}
	delete_option( 'wpfbogp' );
}

add_action( 'wp_head', 'wpfbogp_build_head', 50 );
add_action( 'admin_init', 'wpfbogp_init' );
add_action( 'admin_menu', 'wpfbogp_add_page' );

/**
* Register settings and sanitization callback
*
* @return void
*/
function wpfbogp_init() {
	register_setting( 'wpfbogp_options', 'wpfbogp', 'wpfbogp_validate' );
}

/**
* Add admin page to menu
*
* You can hide the admin menu with the option 'wpfbogp_hide_page'
* You need to set this option with a custom plugin.
*
* @return void
*/
function wpfbogp_add_page() {
	$options = wpfbogp_get_option();
	if( $options && array_key_exists( 'wpfbogp_hide_page', $options ) && $options['wpfbogp_hide_page'] == true ) {
		return;
	} else {
		add_options_page( 'Facebook Open Graph protocol plugin', 'Facebook OGP', 'manage_options', 'wpfbogp', 'wpfbogp_buildpage' );
	}
}

/**
* Build the admin page
*
* @return returns (echo) the form html
*/
function wpfbogp_buildpage() {
?>

<div class="wrap">
	<h2>Facebook Open Graph protocol plugin <em>v<?php echo WPFBOGP_VERSION; ?></em></h2>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content" style="position: relative">
				<form method="post" action="options.php">
				<?php settings_fields( 'wpfbogp_options' ); ?>
				<?php $options = wpfbogp_get_option(); ?>

				<table class="form-table">
					<tr valign="top">
						<th scope="row"></th>
						<td><p class="description"><?php _e( 'Facebook requires you to use either a personal Facebook User ID <i>(most common)</i> or an Application ID. You can use both if you\'d like, but the plugin will not output tags for Facebook until one of these inputs has data. Then you\'re all set!' ); ?></p></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="wpfbogp[wpfbogp_admin_ids]"><?php _e( 'Facebook User Account ID' ); ?></label></th>
						<td><input type="text" name="wpfbogp[wpfbogp_admin_ids]" value="<?php echo $options['wpfbogp_admin_ids']; ?>" class="regular-text">
							<p class="description"><?php _e( 'Enter your personal Facebook User ID number here. For most sites you will use this field instead of an App ID below.<br>
								<strong>How to find your ID:</strong> Go to: <code>http://graph.facebook.com/<strong>YOURusername</strong></code> and copy the number next to "<code>id:</code>".<br>
								<small>- NOTE: You can enter multiple ID numbers by separating each with a comma</small><br>
								<small>- NOTE: This is your Personal ID number and cannot be a Page ID - Facebook will throw an error with a Page ID</small>' ); ?></p></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="wpfbogp[wpfbogp_app_id]"><?php _e( 'Facebook Application ID' ); ?></label></th>
						<td><input type="text" name="wpfbogp[wpfbogp_app_id]" value="<?php echo $options['wpfbogp_app_id']; ?>" class="regular-text">
							<p class="description"><?php _e( 'If you have a Facebook Application and would rather track insights on that instead of a personal ID, then use this field instead. Typically for business/brand sites.<br>
								<strong>How to find your Application ID</strong>: Go to: <code>https://developers.facebook.com/apps/</code> and copy the number next to "<code>App ID:</code>".<br>
								<small>- NOTE: You cannot use multiple App IDs here</small>' ); ?></p></td>
					</tr>
				</table>

				<table class="form-table">
					<tr valign="top">
						<th scope="row"><label for="wpfbogp[wpfbogp_fallback_img]"><?php _e( 'Fallback Image URL' ); ?></label></th>
						<td><input type="text" name="wpfbogp[wpfbogp_fallback_img]" value="<?php echo $options['wpfbogp_fallback_img']; ?>" class="large-text">
							<p class="description"><?php _e( 'Optional: Enter the full URL (including the http:// part) of the image you\'d like to use as a fallback when others aren\'t available.<br>
								<small>- NOTE: FB\'s minimum image size is 600px by 315px. (1200px by 630px is recommended)</small><br>
								<small>- NOTE: Choose or Upload an image via the <a href="upload.php">Media Library</a> and then copy the image URL and put it here. It doesn\'t have to be a local image file.</small>' ); ?></p></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'FORCE Image Fallback?' ); ?></th>
						<td>
							<fieldset>
								<label for="wpfbogp[wpfbogp_force_fallback]">
									<input type="checkbox" name="wpfbogp[wpfbogp_force_fallback]" value="1" <?php if ($options['wpfbogp_force_fallback'] == 1) echo 'checked="checked"'; ?>)>
									<?php _e( 'Check this to force the Fallback Image' ); ?>
								</label>
							</fieldset>
							<p class="description"><?php _e( 'Optional: Check this only if you want to use the Fallback Image for EVERYTHING instead of featured images or content images.'); ?></p>
						</td>
					</tr>
				</table>
				<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes' ); ?>">
				</form>
			</div> <!-- / #post-body-content -->

			<div id="postbox-container-1" class="postbox-container">
				<div id="side-sortables" class="meta-box-sortables ui-sortable">
					<div id="wpfbogp_about" class="postbox">
						<h3 class="hndle" id="about-sidebar"><?php _e( 'About the Plugin' ); ?></h3>
						<div class="inside">
							<p><?php printf( __( 'For any support issues or feature/function requests please use the <a href="%s" target="_blank">support forum on wordpress.org</a>.' ), 'https://wordpress.org/support/plugin/wp-facebook-open-graph-protocol' ); ?></p>
							<p><strong><?php _e( 'Enjoy the plugin?' ); ?></strong><br>
							<?php printf( __( '<a href="%s" target="_blank">Tweet about it</a> and consider donating.' ), 'https://twitter.com/?status=I\'m using the %23WordPress Facebook Open Graph plugin by @chuckreynolds - check it out! http://wordpress.org/plugins/wp-facebook-open-graph-protocol/' ); ?></p>
							<p><strong><?php _e( 'Donate:' ); ?></strong> <?php _e( 'A lot of hard work goes into building plugins - support your open source developers. Include your twitter username and I\'ll send you a shout out for your generosity. Thank you!' ); ?><br>
							<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
							<input type="hidden" name="cmd" value="_s-xclick">
							<input type="hidden" name="hosted_button_id" value="GWGGBTBJTJMPW">
							<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
							<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
							</form></p>
						</div>
					</div> <!-- / #wpfbogp_about -->

					<div id="wpfbogp_reference" class="postbox">
						<h3 class="hndle" id="about-sidebar"><?php _e( 'Relevant Information' ); ?></h3>
						<div class="inside">
							<p><?php printf( __( '<a href="%s" target="_blank">Facebook debugger</a> for checking errors and flushing Facebook\'s cache.' ), 'https://developers.facebook.com/tools/debug/' ); ?></p>
							<p>
								<a href="https://github.com/chuckreynolds/social-profile-image-sizes" target="_blank"><?php _e( 'Social Media Image Size Reference Guide' ); ?></a><br>
								<a href="http://ogp.me" target="_blank"><?php _e( 'Open Graph Protocol' ); ?></a><br>
								<a href="https://developers.facebook.com/docs/sharing/best-practices" target="_blank"><?php _e( 'Facebook Sharing Best Practices' ); ?></a><br>
								<a href="https://developers.facebook.com/docs/platforminsights/domains" target="_blank"><?php _e( 'Facebook Domain Insights' ); ?></a><br>
								<a href="https://developers.facebook.com/docs/plugins/like-button" target="_blank"><?php _e( 'How To Add a Like Button' ); ?></a>
							</p>
						</div>
					</div> <!-- / #wpfbogp_reference -->
				</div> <!-- / #side-sortables .meta-box-sortables ui-sortable -->
			</div> <!-- / #postbox-container-1 .postbox-container -->

		</div> <!-- / #post-body .metabox-holder columns-2 -->
		<br class="clear">

	</div> <!-- / #poststuff -->
</div> <!-- / .wrap -->
<?php
}

/**
* Sanitize inputs
*
* @var $input array
* @return returns a sanitized array
*/
function wpfbogp_validate($input) {
	$input['wpfbogp_admin_ids'] = sanitize_text_field($input['wpfbogp_admin_ids']);
	$input['wpfbogp_app_id'] = sanitize_text_field($input['wpfbogp_app_id']);
	$input['wpfbogp_fallback_img'] = sanitize_text_field($input['wpfbogp_fallback_img']);
	$input['wpfbogp_force_fallback'] = ($input['wpfbogp_force_fallback'] == 1)  ? 1 : 0;
	return $input;
}

/**
* Run admin notices on activation or if settings not set
*
* @return void
*/
function wpfbogp_admin_warnings() {
	global $wpfbogp_admins;
		$wpfbogp_data = wpfbogp_get_option();
	if ((empty($wpfbogp_data['wpfbogp_admin_ids']) || $wpfbogp_data['wpfbogp_admin_ids'] == '') && (empty($wpfbogp_data['wpfbogp_app_id']) || $wpfbogp_data['wpfbogp_app_id'] == '')) {
		function wpfbogp_warning() {
			echo "<div id='wpfbogp-warning' class='updated fade'><p><strong>".__('WP Facebook OGP plugin is almost ready!')."</strong> ".sprintf(__('A <a href="%1$s">Facebook ID is needed</a> for it to start working.'), "options-general.php?page=wpfbogp")."</p></div>";
		}
	add_action('admin_notices', 'wpfbogp_warning');
	}
}

/**
* twentyten and twentyeleven add crap to the excerpt so lets check for that and remove
*
* @return void
*/
add_action('after_setup_theme','wpfbogp_fix_excerpts_exist');
function wpfbogp_fix_excerpts_exist() {
	remove_filter('get_the_excerpt','twentyten_custom_excerpt_more');
	remove_filter('get_the_excerpt','twentyeleven_custom_excerpt_more');
}

/**
* Add settings link to plugins list
*
* @return string with link to settings
*/
function wpfbogp_add_settings_link($links, $file) {
	static $this_plugin;
	if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);
	if ($file == $this_plugin){
		$settings_link = '<a href="options-general.php?page=wpfbogp">'.__("Settings","wpfbogp").'</a>';
		array_unshift($links, $settings_link);
	}
	return $links;
}
add_filter('plugin_action_links','wpfbogp_add_settings_link', 10, 2 );

/**
* Lets offer an actual clean uninstall and rem db row on uninstall
*
* @return void
*/
if (function_exists('register_uninstall_hook')) {
    register_uninstall_hook(__FILE__, 'wpfbogp_uninstall_hook');
	function wpfbogp_uninstall_hook() {
		wpfbogp_delete_option();
	}
}
