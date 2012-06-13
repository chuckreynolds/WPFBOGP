<?php 
/*
Plugin Name: WP Facebook Open Graph protocol
Plugin URI: http://wordpress.org/extend/plugins/wp-facebook-open-graph-protocol/
Description: Adds proper Facebook Open Graph Meta tags and values to your site so when links are shared it looks awesome! Works on Google + and Linkedin too!
Version: 2.0.4
Author: Chuck Reynolds
Author URI: http://chuckreynolds.us
License: GPL2
*/
/*
	Copyright 2011 WordPress Facebook Open Graph protocol plugin (email: chuck@rynoweb.com)
	
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

define('WPFBOGP_VERSION', '2.0.4');
wpfbogp_admin_warnings();

// add OGP namespace per ogp.me schema
function wpfbogp_namespace($output) {
	return $output.' xmlns:og="http://ogp.me/ns#"';
}
add_filter('language_attributes','wpfbogp_namespace');

// function to call first uploaded image in content
function wpfbogp_find_images() {
	global $post, $posts;
	
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

function wpfbogp_start_ob() {
	// Start the buffer before any output
	ob_start( 'wpfbogp_callback' );
}

function wpfbogp_callback( $content ) {
	// Grab the page title and meta description
	$title = preg_match( '/<title>(.*)<\/title>/', $content, $title_matches );
	$decsription = preg_match( '/<meta name="description" content="(.*)"/', $content, $description_matches );
	
	// Take page title and meta description and place it in the ogp meta tags
	if ( $title !== FALSE && count( $title_matches ) == 2 ) {
		$content = preg_replace( '/<meta property="og:title" content="(.*)">/', '<meta property="og:title" content="' . $title_matches[1] . '">', $content );
	}
	
	if ( $description !== FALSE && count( $description_matches ) == 2 ) {
		$content = preg_replace( '/<meta property="og:description" content="(.*)">/', '<meta property="og:description" content="' . $description_matches[1] . '">', $content );
	}
	
	return $content;
}

function wpfbogp_flush_ob() {
	ob_end_flush();
}

add_action( 'init', 'wpfbogp_start_ob', 0 );
add_action( 'wp_footer', 'wpfbogp_flush_ob', 10000 ); // Fire after other plugins (which default to priority 10)

// build ogp meta
function wpfbogp_build_head() {
	global $post;
	$options = get_option('wpfbogp');
	// check to see if you've filled out one of the required fields and announce if not
	if ( ( ! isset( $options['wpfbogp_admin_ids'] ) || empty( $options['wpfbogp_admin_ids'] ) ) && ( ! isset( $options['wpfbogp_app_id'] ) || empty( $options['wpfbogp_app_id'] ) ) ) {
		echo "\n<!-- Facebook Open Graph protocol plugin NEEDS an admin or app ID to work, please visit the plugin settings page! -->\n";
	} else {
		echo "\n<!-- WordPress Facebook Open Graph protocol plugin (WPFBOGP v".WPFBOGP_VERSION.") http://rynoweb.com/wordpress-plugins/ -->\n";
		
		// do fb verification fields
		if ( isset( $options['wpfbogp_admin_ids'] ) && ! empty( $options['wpfbogp_admin_ids'] ) ) {
			echo '<meta property="fb:admins" content="' . esc_attr( apply_filters( 'wpfbogp_app_id', $options['wpfbogp_admin_ids'] ) ) . '">' . "\n";
		}
		if ( isset( $options['wpfbogp_app_id'] ) && ! empty( $options['wpfbogp_app_id'] ) ) {
			echo '<meta property="fb:app_id" content="' . esc_attr( apply_filters( 'wpfbogp_app_id', $options['wpfbogp_app_id'] ) ) . '">' . "\n";
		}
		
		// do url stuff
		if (is_home() || is_front_page() ) {
			$wpfbogp_url = get_bloginfo( 'url' );
		} else {
			$wpfbogp_url = 'http' . (is_ssl() ? 's' : '') . "://".$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}
		echo '<meta property="og:url" content="' . esc_url( apply_filters( 'wpfbogp_url', $wpfbogp_url ) ) . '">' . "\n";
		
		// do title stuff
		if (is_home() || is_front_page() ) {
			$wpfbogp_title = get_bloginfo( 'name' );
		} else {
			$wpfbogp_title = get_the_title();
		}
		echo '<meta property="og:title" content="' . esc_attr( apply_filters( 'wpfbogp_title', $wpfbogp_title ) ) . '">' . "\n";
		
		// do additional randoms
		echo '<meta property="og:site_name" content="' . get_bloginfo( 'name' ) . '">' . "\n";
		
		// do descriptions
		if ( is_singular() ) {
			if ( has_excerpt( $post->ID ) ) {
				$wpfbogp_description = strip_tags( get_the_excerpt( $post->ID ) );
			} else {
				$wpfbogp_description = str_replace( "\r\n", ' ' , substr( strip_tags( strip_shortcodes( $post->post_content ) ), 0, 160 ) );
			}
		} else {
			$wpfbogp_description = get_bloginfo( 'description' );
		}
		echo '<meta property="og:description" content="' . esc_attr( apply_filters( 'wpfbogp_description', $wpfbogp_description ) ) . '">' . "\n";
		
		// do ogp type
		if ( is_single() ) {
			$wpfbogp_type = 'article';
		} else {
			$wpfbogp_type = 'website';
		}
		echo '<meta property="og:type" content="' . esc_attr( apply_filters( 'wpfbpogp_type', $wpfbogp_type ) ) . '">' . "\n";
		
		// Find/output any images for use in the OGP tags
		$wpfbogp_images = array();
		
		// Only find images if it isn't the homepage and the fallback isn't being forced
		if ( ! is_home() && $options['wpfbogp_force_fallback'] != 1 ) {
			// Find featured thumbnail of the current post/page
			if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( $post->ID ) ) {
				$thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
				$wpfbogp_images[] = $thumbnail_src[0]; // Add to images array
			}
			
			if ( wpfbogp_find_images() !== false && is_singular() ) { // Use our function to find post/page images
				$wpfbogp_images = array_merge( $wpfbogp_images, wpfbogp_find_images() ); // Returns an array already, so merge into existing
			}
		}
		
		// Add the fallback image to the images array (which is empty if it's being forced)
		if ( isset( $options['wpfbogp_fallback_img'] ) && $options['wpfbogp_fallback_img'] != '') {
			$wpfbogp_images[] = $options['wpfbogp_fallback_img']; // Add to images array
		}
		
		// Make sure there were images passed as an array and loop through/output each
		if ( ! empty( $wpfbogp_images ) && is_array( $wpfbogp_images ) ) {
			foreach ( $wpfbogp_images as $image ) {
				echo '<meta property="og:image" content="' . esc_url( apply_filters( 'wpfbogp_image', $image ) ) . '">' . "\n";
			}
		} else {
			// No images were outputted because they have no default image (at the very least)
			echo "<!-- There is not an image here as you haven't set a default image in the plugin settings! -->\n"; 
		}
		
		// do locale // make lower case cause facebook freaks out and shits parser mismatched metadata warning
		echo '<meta property="og:locale" content="' . strtolower( esc_attr( get_locale() ) ) . '">' . "\n";
		echo "<!-- // end wpfbogp -->\n";
	}
}

add_action('wp_head','wpfbogp_build_head',50);
add_action('admin_init','wpfbogp_init');
add_action('admin_menu','wpfbogp_add_page');

// register settings and sanitization callback
function wpfbogp_init() {
	register_setting('wpfbogp_options','wpfbogp','wpfbogp_validate');
}

// add admin page to menu
function wpfbogp_add_page() {
	add_options_page('Facebook Open Graph protocol plugin','Facebook OGP','manage_options','wpfbogp','wpfbogp_buildpage');
}

// build admin page
function wpfbogp_buildpage() {
?>

<div class="wrap">
	<h2>Facebook Open Graph protocol plugin <em>v<?php echo WPFBOGP_VERSION; ?></em></h2>
	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<div id="side-info-column" class="inner-sidebar">
			<div class="meta-box-sortables">
				<div id="about" class="postbox">
					<h3 class="hndle" id="about-sidebar"><?php _e('About the Plugin:') ?></h3>
					<div class="inside">
						<p>Talk to <a href="http://twitter.com/chuckreynolds" target="_blank">@ChuckReynolds</a> on twitter or please fill out the <a href="http://rynoweb.com/wordpress-plugins/" target="_blank">plugin support form</a> for bugs or feature requests.</p>
						<p><?php _e('<strong>Enjoy the plugin?</strong>') ?><br />
						<a href="http://twitter.com/?status=I'm using @chuckreynolds's WordPress Facebook Open Graph plugin - check it out! http://rynoweb.com/wordpress-plugins/" target="_blank"><?php _e('Tweet about it') ?></a> <?php _e('and consider donating.') ?></p>
						<p><?php _e('<strong>Donate:</strong> A lot of hard work goes into building plugins - support your open source developers. Include your twitter username and I\'ll send you a shout out for your generosity. Thank you!') ?><br />
						<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
						<input type="hidden" name="cmd" value="_s-xclick">
						<input type="hidden" name="hosted_button_id" value="GWGGBTBJTJMPW">
						<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
						<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
						</form></p>
					</div>
				</div>
			</div>
			
			<div class="meta-box-sortables">
				<div id="about" class="postbox">
					<h3 class="hndle" id="about-sidebar"><?php _e('Relevant Information:') ?></h3>
					<div class="inside">
						<p><a href="http://ogp.me" target="_blank">The Open Graph Protocol</a><br />
						<a href="https://developers.facebook.com/docs/opengraph/" target="_blank">Facebook Open Graph Docs</a><br />
						<a href="https://developers.facebook.com/docs/insights/" target="_blank">Insights: Domain vs App vs Page</a><br />
						<a href="https://developers.facebook.com/docs/reference/plugins/like/" target="_blank">How To Add a Like Button</a></p>
					</div>
				</div>
			</div>
		</div> <!-- // #side-info-column .inner-sidebar -->


		<div id="post-body" class="has-sidebar">
			<div id="post-body-content" class="has-sidebar-content">
				<div id="normal-sortables" class="meta-box-sortables">
					<div id="about" class="postbox">
						<div class="inside">

		<form method="post" action="options.php">
			<?php settings_fields('wpfbogp_options'); ?>
			<?php $options = get_option('wpfbogp'); ?>

		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Facebook User Account ID:') ?></th>
				<td><input type="text" name="wpfbogp[wpfbogp_admin_ids]" value="<?php echo $options['wpfbogp_admin_ids']; ?>" class="regular-text" /><br />
					<?php _e('For personal sites use your Facebook User ID here. <em>(You can enter multiple by separating each with a comma)</em>, if you want to receive Insights about the Like Buttons. The meta values will not display in your site until you\'ve completed this box.<br />
					<strong>Find your ID</strong> by going to the URL like this: http://graph.facebook.com/yourusername') ?></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Facebook Application ID:') ?></th>
				<td><input type="text" name="wpfbogp[wpfbogp_app_id]" value="<?php echo $options['wpfbogp_app_id']; ?>" class="regular-text" /><br />
					<?php _e('For business and/or brand sites use Insights on an App ID as to not associate it with a particular person. You can use this with or without the User ID field. Create an app and use the "App ID": <a href="https://www.facebook.com/developers/apps.php" target="_blank">Create FB App</a>.') ?></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Default Image URL to use:') ?></th>
				<td><input type="text" name="wpfbogp[wpfbogp_fallback_img]" value="<?php echo $options['wpfbogp_fallback_img']; ?>" class="large-text" /><br />
					<?php _e('Full URL including http:// to the default image to use if your posts/pages don\'t have a featured image or an image in the content. <strong>The image is recommended to be 200px by 200px</strong>.<br />
					You can use the WordPress <a href="upload.php">media uploader</a> if you wish, just copy the location of the image and put it here.') ?></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Force Fallback Image as Default') ?></th>
				<td><input type="checkbox" name="wpfbogp[wpfbogp_force_fallback]" value="1" <?php if ($options['wpfbogp_force_fallback'] == 1) echo 'checked="checked"'; ?>) /> <?php _e('Use this if you want to use the Default Image for everything instead of looking for featured/content images.') ?></label></td>
			</tr>
		</table>
		
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</form>
		<br class="clear" />
					</div>
				</div>
			</div>
		</div>
	</div>
	</div>
</div>
<?php	
}

// sanitize inputs. accepts an array, return a sanitized array.
function wpfbogp_validate($input) {
	$input['wpfbogp_admin_ids'] = wp_filter_nohtml_kses($input['wpfbogp_admin_ids']);
	$input['wpfbogp_app_id'] = wp_filter_nohtml_kses($input['wpfbogp_app_id']);
	$input['wpfbogp_fallback_img'] = wp_filter_nohtml_kses($input['wpfbogp_fallback_img']);
	$input['wpfbogp_force_fallback'] = ($input['wpfbogp_force_fallback'] == 1)  ? 1 : 0;
	return $input;
}

// run admin notices on activation or if settings not set
function wpfbogp_admin_warnings() {
	global $wpfbogp_admins;
		$wpfbogp_data = get_option('wpfbogp');
	if ((empty($wpfbogp_data['wpfbogp_admin_ids']) || $wpfbogp_data['wpfbogp_admin_ids'] == '') && (empty($wpfbogp_data['wpfbogp_app_id']) || $wpfbogp_data['wpfbogp_app_id'] == '')) {
		function wpfbogp_warning() {
			echo "<div id='wpfbogp-warning' class='updated fade'><p><strong>".__('WP FB OGP is almost ready.')."</strong> ".sprintf(__('You must <a href="%1$s">enter your a Facebook User ID or App ID</a> for it to work.'), "options-general.php?page=wpfbogp")."</p></div>";
		}
	add_action('admin_notices', 'wpfbogp_warning');
	}
}

// twentyten and twentyeleven add crap to the excerpt so lets check for that and remove
add_action('after_setup_theme','wpfbogp_fix_excerpts_exist');
function wpfbogp_fix_excerpts_exist() {
	remove_filter('get_the_excerpt','twentyten_custom_excerpt_more');
	remove_filter('get_the_excerpt','twentyeleven_custom_excerpt_more');
}

// add settings link to plugins list
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

// lets offer an actual clean uninstall and rem db row on uninstall
if (function_exists('register_uninstall_hook')) {
    register_uninstall_hook(__FILE__, 'wpfbogp_uninstall_hook');
	function wpfbogp_uninstall_hook() {
		delete_option('wpfbogp');
	}
}