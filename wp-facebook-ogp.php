<?php 
/*
Plugin Name: WP Facebook Open Graph protocol
Plugin URI: http://rynoweb.com
Description: Plugin to add proper Facebook OGP meta values to your header for single posts and pages and fallback for index and other pages
Version: 0.0.4
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

// version check
function wpfbogp_url( $path = '' ) {
	global $wp_version;
	if ( version_compare( $wp_version, '3.0', '<' ) ) { // using at least WordPress 3.0?
		$folder = dirname( plugin_basename( __FILE__ ) );
		if ( '.' != $folder )
			$path = path_join( ltrim( $folder, '/' ), $path );

		return plugins_url( $path );
	}
	return plugins_url( $path, __FILE__ );
}

/* for debugging
function dumpVars($item)
{
	echo "<pre>";
	print_r($item);
	echo "</pre>";
} */

// add OGP namespace per ogp.me
function wpfbogp_namespace( $output ) {
	return $output . ' xmlns:og="http://ogp.me/ns#"';
}
add_filter('language_attributes', 'wpfbogp_namespace');

// function to call first uploaded image in functions file. borrowed from i forgot :/ sorry.
function first_image() {
  global $post, $posts;
  $first_img = '';
  ob_start();
  ob_end_clean();
  $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
  $first_img = $matches [1] [0];
  if(empty($first_img)){ // return false if nothing there, makes it easier in my if statements
    return false;
  }
  return $first_img;
}

// build ogp meta
function wpfbogp_build_head() {
	global $post;
	$options = get_option('wpfbogp');
	if ( (!isset($options['admin_ids']) || empty($options['admin_ids'])) && (!isset($options['app_id']) || empty($options['app_id'])) ) {
		echo "\n\t<!-- Facebook Open Graph protocol plugin NEEDS an admin or app ID to work, please visit the plugin settings page! -->\n\n";
	} else {
		echo "\n\t<!-- WordPress Facebook Open Graph protocol plugin (WPFBOGP) -->\n";
		echo "\t<meta property='og:url' content='".get_permalink($post->ID)."' />\n";
		echo "\t<meta property='og:site_name' content='".esc_attr( get_bloginfo('name') )."' />\n";
		if(is_single()) {
			echo "\t<meta property='og:title' content='".get_the_title()."' />\n";
			echo "\t<meta property='og:description' content='".strip_tags(get_the_excerpt($post->ID))."' />\n";
			echo "\t<meta property='og:type' content='article' />\n";
		}else{
			echo "\t<meta property='og:title' content='".get_bloginfo('name')."' />\n";
			echo "\t<meta property='og:description' content='".get_bloginfo('description')."' />\n";
			echo "\t<meta property='og:type' content='website' />\n";
		}
		// image tricks. if there's a thumbnail use that, if not check for a content image, none of those? use the admin default url
		if ((function_exists('has_post_thumbnail')) && (has_post_thumbnail())) {
			$thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
			echo "\t<meta property='og:image' content='".esc_attr( $thumbnail_src[0] )."' />\n";
		}
		elseif (( first_image() !== false ) && (is_singular())) {
			echo "\t<meta property='og:image' content='".first_image()."' />\n";
		}
		else{
			echo "\t<meta property='og:image' content='".$options['fallback_img']."' />\n";
		}
		
		if ( isset($options['admin_ids']) && $options['admin_ids'] != '' ) {
			echo "\t<meta property='fb:admins' content='".esc_attr( $options['admin_ids'] )."' />\n";
		}
		if ( isset($options['app_id']) && $options['app_id'] != '' ) {
			echo "\t<meta property='fb:app_id' content='".esc_attr( $options['app_id'] )."' />\n";
		}
		
		echo "\t<!-- // end wpfbogp -->\n\n";
		} // end isset admin ids

} // end function


add_action('wp_head', 'wpfbogp_build_head', 5);
add_action('admin_init', 'wpfbogp_init' );
add_action('admin_menu', 'wpfbogp_add_page' );

// register settings and sanitization callback
function wpfbogp_init(){
	register_setting( 'wpfbogp_options', 'wpfbogp', 'wpfbogp_validate' );
}

// add admin page to menu
function wpfbogp_add_page() {
	add_options_page('Facebook Open Graph protocol plugin', 'Facebook OGP', 'manage_options', 'wpfbogp', 'wpfbogp_buildpage');
}

// build admin page
function wpfbogp_buildpage() {
?>
    <div style="width:200px; right:0; float:right; position:fixed; margin:30px 10px 20px 0; background:#ebebeb; border:1px solid #e9e9e9; padding:5px; color:#888; font-size:12px;">
		<h3 style="margin: 0 0 10px 0; border-bottom:1px dashed #888;">About the Author:</h3>
		<p>You can <a href="http://www.twitter.com/chuckreynolds">follow Chuck on Twitter</a> and/or ask questions there and <a href="http://facebook.com/rynoweb">like rYnoweb on Facebook</a>.</p>
		<?php/*
		<p>-- put Tweet This button... "I'm using @chuckreynolds's WordPress Facebook Open Graph plugin - check it out! http://rynoweb.com/go/wpfbogp" --</p>
		<p>--- put facebook like button for the page/post on rynoweb in here ---</p>
		<p>---- maybe facebook credits donate button here? ----</p>
		*/ ?>
		<h3>More info</h3>
		<p><a href="http://developers.facebook.com/docs/opengraph/" target="_blank">Facebook Open Graph Docs</a><br />
			<a href="http://ogp.me" target="_blanK">The Open Graph Protocol</a></p>
	</div>
	<div class="wrap">
		<h2>Facebook Open Graph protocol plugin</h2>
		<form method="post" action="options.php">
			<?php settings_fields('wpfbogp_options'); ?>
			<?php $options = get_option('wpfbogp'); ?>

		<table class="form-table" style="width:75%;">
			<tr valign="top">
				<th scope="row">Facebook User Account ID:</th>
				<td><input type="text" name="wpfbogp[admin_ids]" value="<?php echo $options['admin_ids']; ?>" /><br />
				For personal sites use your Facebook User ID here. <em>(You can enter multiple by separating each with a comma)</em>, if you want to receive insights (analytics) about the Like Buttons. The meta values will not display in your site until you've completed this box.<br />
				You can find it by going to the URL like this: http://graph.facebook.com/yourusername</td>
			</tr>
			<tr valign="top">
				<th scope="row">Facebook Application ID:</th>
				<td><input type="text" name="wpfbogp[app_id]" value="<?php echo $options['app_id']; ?>" /><br />
				If you want Open Graph insights or to attach this to an app and not your personal admin id the build an app and use the application ID. Create an app and use the "App ID": https://www.facebook.com/developers/apps.php</td>
			</tr>
			<tr valign="top">
				<th scope="row">Default Image URL to use:</th>
				<td><input type="text" name="wpfbogp[fallback_img]" value="<?php echo $options['fallback_img']; ?>" /><br />
				Full URL including http:// to the default image to use if your posts/pages don't have a featured image or an image in the content. Facebook says: <em>An image URL which should represent your object within the graph. The image must be at least 50px by 50px and have a maximum aspect ratio of 3:1</em>. They will make it square if you don't.</td>
			</tr>
		</table>
		
		<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>
		</form>
	</div>
<?php	
}

// sanitize inputs. accepts an array, return a sanitized array.
function wpfbogp_validate($input) {
	$input['admin_ids'] = wp_filter_nohtml_kses($input['admin_ids']);
	$input['app_id'] = wp_filter_nohtml_kses($input['app_id']);
	$input['fallback_img'] = wp_filter_nohtml_kses($input['fallback_img']);
	return $input;
}
?>