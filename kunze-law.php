<?php
/*
 * Plugin Name: Kunze Law
 * Plugin URI: http://www.kunze-medien.de
 * Description: This Plugin distributes central hosted content files. 
 * Version: 1.9
 * Author: Kunze Medien
 * Author URI: http://www.kunze-medien.de
 * License: GPL2
 *
 * Copyright 2017  Kunze Medien AG
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * generate admin section in relation of the installation type. single installation table: wp_options / multisite: wp_sitemeta
 */
function kunzelaw_network_setting_menu(){

		if (is_multisite()) {

			// Multisite is enabled so add menu to Network Admin
			if (is_super_admin() ) {
				add_action('network_admin_menu', 'kunzelaw_add_network_setting_menu');
			}

		} else {

			if (current_user_can('administrator') && is_admin()) {
			// Multisite is NOT enabled so add menu to WordPress Admin
				add_action('admin_menu', 'kunzelaw_add_network_setting_menu');
			}
		}

	}


/**
 * create admin menu
 */
function kunzelaw_add_network_setting_menu(){
	add_menu_page('Network Options Page','Kunze Law','manage_options','kunzelaw-network-settings','kunzelaw_network_settings');
}


/**
 * option setting form
 */
function kunzelaw_network_settings() {

    ob_start();
	?>
	<div class="wrap" >
		<h2>Network Settings</h2>
		<form method="post">
			<?php

			//load option values
			$network_settings 	= get_site_option( 'kunzelaw_network_settings' );

			if (!empty($network_settings)){

				$count_network_settings = count($network_settings);

				// Set yes to email for older plugin versions
				If ($count_network_settings == 3){
					$network_settings['send_email_admin'] = 1;
					update_site_option( 'kunzelaw_network_settings', $network_settings);
					$network_settings 	= get_site_option( 'kunzelaw_network_settings' );
				}
			}

			if(empty($network_settings['remote_host'])){
				$network_settings['remote_host'] = '';
			}else{
				$cleanup = array("http://","https://");
				$network_settings['remote_host'] =  str_replace($cleanup,'',esc_url_raw($network_settings['remote_host'], array('http', 'https')));
			}

			if(empty($network_settings['protocol'])){
				$network_settings['protocol'] = 0;
			}

			if(empty($network_settings['send_email_admin'])){
				$network_settings['send_email_admin'] = 0;
			}

			if(empty($network_settings['send_email_from'])){
				// Default Email From
				$email_host = parse_url(get_site_url(), PHP_URL_HOST);
				$network_settings['send_email_from'] = 'wordpress@'.$email_host;
			}

			if(empty($network_settings['cache_time'])){
				$network_settings['cache_time'] = 86400;
			}

			$remote_host 		= $network_settings['remote_host'];
			$cache_time 		= $network_settings['cache_time'];
			$protocol 			= $network_settings['protocol'];
			$send_email_admin	= $network_settings['send_email_admin'];
			$send_email_from	= $network_settings['send_email_from'];

			//create nonce hidden field for security
			wp_nonce_field( 'save-network-settings', 'kunzelaw-network-plugin' );

			?>
			<table class="form-table">

				<tr valign="top"><th scope="row">Protocol / Remote Server:</th>
					<td>
						<select name="network_settings[protocol]">
							<option value="0"  <?php selected( $protocol, '0' ); ?> >http://</option>
							<option value="1"  <?php selected( $protocol, '1' ); ?> >https://</option>
						</select>
						<input style="width:300px;" type="text" name="network_settings[remote_host]" value="<?php echo $remote_host; ?>" />
					</td>
				</tr>

				<tr valign="top"><th scope="row">Cache Time: (seconds)</th>
					<td><input type="text" name="network_settings[cache_time]" value="<?php echo (int) $cache_time; ?>" /></td>
				</tr>

				<tr valign="top"><th scope="row">Get E-Mail Error Message:</th>
					<td>
						<input type="checkbox" name="network_settings[send_email_admin]" value="1" <?php checked($send_email_admin, 1); ?> />
						E-Mail will be send to WordPress Admin E-Mail Address. ( Setting -> General -> E-Mail Address )
					</td>
				</tr>

				<tr valign="top"><th scope="row">E-Mail Error "From" Address:</th>
					<td>
						<input style="width:300px;" type="text" name="network_settings[send_email_from]" value="<?php echo $send_email_from; ?>" />
					</td>
				</tr>

			</table>


			<p class="submit">
				<input type="submit" class="button-primary" name="network_settings_save" value="Save Settings" />
			</p>

			<p style="height:15px; margin-bottom: 30px;">
				<?php
				if ( isset( $_POST['network_settings'] ) && !empty($network_settings) )
					{ echo ' -- Data saved successfully --';} ?></p>

			<p>
				The Kunze Law Plugin embeds remote content (HTML snippets) into a post or page using a shortcode. Use the [kunze_xxx] shortcode to import remotely hosted content into your posts or page.<br/>
				During the first call, the content will be cached locally. Use the cache time to set up the period in seconds after the plugin will look for updated content.
				<br/><br/>
				1. Fill in the remote server. e.g. http://yourserver.com/content<br/>
				2. Set time in seconds after the plugin will look for updated content on the remote server. '86400' will used as a default value.<br/>
				3. Prepare HTML content snippets on the remote server. e.g. facebook.html, google.html<br/>
				4. Shortcode e.g. [kunze_facebook] or [kunze_google] to embed the code within WordPress. Always use [kunze_yourname] shortcode format.
				<br/><br/>
				<strong>Always use following format:</strong>
				<br/>
				Shortcode within WordPress: [kunze_yourname]<br/>
				HTML Snippet on remote Server: yourname.html
				<br/><br/>
				kunze = default prefix
				yourname = the name you choose for your HTML snippet. Use as many snippets as you like.
				<br/><br/>
				<strong>Example of a HTML snipped on the remote server: (yourname.html) </strong>
				<br/>
				<?php echo htmlentities('<div class="myClass"><strong>lorem ipsum</strong></div>');?>
				<br/><br/>
				<strong>Error Handling</strong>
				<br/>
				If no remote content can be served, an error message will be send to the WordPress administration email address. (Settings > General > E-Mail Address)
			</p>
		</form>



	</div>
	<?php
    ob_end_flush();
}

/**
 * do some cleaning work and save the option settings
 */
function kunzelaw_save_network_settings() {

	//if network settings are being saved, process it
	if ( isset( $_POST['network_settings'] ) ) {

		// check nonce field
		if ( ! empty( $_POST ) && check_admin_referer( 'save-network-settings', 'kunzelaw-network-plugin' ) ) {

			//store option values in a variable
			$network_settings = array();

			//clean up the value
			$network_settings['protocol'] = absint($_POST['network_settings']['protocol']);
			$cleanup = array("http://","https://");
			$network_settings['remote_host'] = str_replace($cleanup,'',esc_url_raw($_POST['network_settings']['remote_host'], array('http', 'https')));
			$network_settings['cache_time'] = absint($_POST['network_settings']['cache_time']);

			if (empty($_POST['network_settings']['send_email_admin'])){
				$network_settings['send_email_admin'] = 0;
			} else{
				$network_settings['send_email_admin'] = absint($_POST['network_settings']['send_email_admin']);
			}

			$network_settings['send_email_from'] = $_POST['network_settings']['send_email_from'];

			//use array map function to sanitize option values
			$network_settings = array_map( 'sanitize_text_field', $network_settings );

			//save option values
			update_site_option( 'kunzelaw_network_settings', $network_settings );

		}

	}

}

date_default_timezone_set('Europe/Berlin');

/**
 * config initialization
 * @return array
 */
function kunzelaw_get_config() {

	$upload_dir = wp_upload_dir();

	$plugin_basedir_prepare = explode("uploads", $upload_dir['basedir'], -1);
	$plugin_basedir = $plugin_basedir_prepare[0].'plugins/';

	$plugin_absolute_path_prepare = explode("/", plugin_basename( __FILE__ ), 2);
	$plugin_absolute_path_extract = $plugin_absolute_path_prepare[0];

	$tmp_path = '/tmp/';

	$config = array(
		'cache_time' => '',
		'url_remote' => '',
		'dir_plugin_tmp' => $plugin_basedir.$plugin_absolute_path_extract.$tmp_path,
	    'url_plugin_tmp' => plugins_url( '' , __FILE__ ).$tmp_path,
		'email_admin' => get_bloginfo('admin_email')  //Admin Email Address
		);


	if ( is_multisite()) {
		$network_settings = get_site_option( 'kunzelaw_network_settings' );
	}else{
		$network_settings = get_option( 'kunzelaw_network_settings' );
	}

	$remote_host 		= $network_settings['remote_host'];
	$cache_time 		= $network_settings['cache_time'];
	$protocol 			= $network_settings['protocol'];
    $send_email_from 	= $network_settings['send_email_from'];
	$send_email_admin 	= $network_settings['send_email_admin'];

	switch ($protocol) {
		case '0':
			$protocol = 'http://';
			break;
		case '1':
			$protocol = 'https://';
			break;
		default:
			$protocol = 'http://';
	}

	$config['cache_time'] = $cache_time;
	$config['url_remote'] = rtrim($protocol.$remote_host,"/");
	$config['send_email_from'] = $send_email_from;
	$config['send_email_admin'] = $send_email_admin;

	return $config;

}

/**
 * get content of the related shortcodes through the local cached file
 * @param $path
 * @return bool|mixed
 */
function kunzelaw_get_cached_content($path){

	$data = @file_get_contents($path);

	if($data != ''){
		return $data;
	}else{
		return false;
	}
}



/**
 * get http status code - don't send error email in case of 403
 * @param $url
 * @return bool|mixed
 */
function kunzelaw_check_remote_status($url){

	$data = wp_remote_get( $url );
	$httpcode =  wp_remote_retrieve_response_code($data);

	if( is_array($data) ) {
		return $httpcode;
	}else{
		return false;
	}

}


/**
 * get content of the related shortcodes on the remote server
 * @param $url
 * @return bool|mixed
 */
function kunzelaw_get_remote_content($url){

	$data = wp_remote_get( $url );
	$httpcode =  wp_remote_retrieve_response_code($data);

	if( is_array($data) ) {
		if ($httpcode>=200 && $httpcode<300){
			return $data;
		}else{
			return false;
		}

	}
}


/**
 * get article content, search for used shortcodes and replace the shortcodes with the correct values on the remote server
 * @param $content
 * @return mixed
 */

function kunzelaw_page($content) {

	    # fire only if not within the admin area
		if(! is_admin() ) {

			# extract shortcode with close-by html tags format: <p>[xxx]</p>
			$matches_with_tag = array();

			$pattern_with_tag = '@(<(\w*+)[^>]*>|)\[kunze_(.*)\](</\2>|)@siU';

			$str_with_tag = $content;

			$find = preg_match_all($pattern_with_tag, $str_with_tag, $matches_with_tag, PREG_SET_ORDER);

			if ($find) {
				foreach ($matches_with_tag as $key => $match) {
					$closeTag = ($match[2] != '') ? strpos($match[4], $match[2]) : true;

					if (!$closeTag) {
						$matches_with_tag[$key][0] = str_replace($match[1], '', $match[0]);
					}
				}
			}

			foreach ($matches_with_tag as $value) {

				# naming of the related html file on the remote server e.g. agb.html
				$shortcode_extract = trim($value[3]);

				# get the correct value
				$file_extract = kunzelaw_get_remote_value($shortcode_extract);

				# if <p> is found - replace the shortcode and the close-by html tags, if <div> is found replace only the pure shortcode
				$prepared_shortcode = kunzelaw_get_prepare_shortcode($value);

				$content = str_replace($prepared_shortcode, $file_extract, $content);

			}

			return $content;
		}


}


# Analyse the close-by tags of the shortcode
/**
 * if <p> is found - replace the shortcode and the close-by html tags, if <div> is found replace only the pure shortcode
 * @param $shortcode_with_tag
 * @param $shortcode_clean
 * @return string
 */
function kunzelaw_get_prepare_shortcode($value){

	$str_found = false;
	$str_found_tag = "";
	$shortcode_with_tag = $value[1].'[kunze_'.$value[3].']'.$value[4];
	$shortcode_no_tag = '[kunze_'.$value[3].']';

	# check the certain tags
	$search = array('<p', '<div>');

	foreach ($search as $v) {
		if (strpos($value[1], $v) !== false) {
				$str_found = true;
			    $str_found_tag = $v;
		}

	}

	# What happens if a certain tag is found
	if ($str_found){
			switch ($str_found_tag) {
				case '<p':
					$shortcode_new = $shortcode_with_tag;
					break;
				case '<div>':
					$shortcode_new = $shortcode_no_tag;
					break;
				default:
					$shortcode_new = $shortcode_no_tag;
			}
		}else{
			$shortcode_new = $shortcode_no_tag;
	}

	return $shortcode_new;
}


/**
 * Activate Debug Error Messages
 * @param $etext
 */
function we($etext)
{
	$error = false;

	if ($error) {

		if (is_array($etext)) {
			echo '<pre>';
			print_r($etext);
			echo '</pre>';
		} else {
			echo $etext . '<br/>';
		}
	}
}
/**
 * send mail to administration email account, write error.html into /tmp
 * @param $error_message
 * @param $http_status
 */
function kunzelaw_send_error_mail($error_message, $http_status){

	$config = kunzelaw_get_config();

	// delete error.html if error e-mail sending is disabled
	if (file_exists($config['dir_plugin_tmp'].'error.html') && ($config['send_email_admin']==0) ) {
		unlink ($config['dir_plugin_tmp'].'error.html');
	}

	// e-mail will only be send once and if error e-mail sending is activated
	if (!file_exists($config['dir_plugin_tmp'].'error.html') && ($http_status!=403) && ($config['send_email_admin']==1)  ) {

		we("connection error - email will be send");

		# create error html
		$fp = fopen($config['dir_plugin_tmp'].'error.html', 'w');
		fwrite($fp, "remote server error");

		$to = $config['email_admin'];

		// email subject and body text
		$subject = 'Kunze Law Remote Server Error on: '.get_site_url();
		$message = $error_message.' http status:'.$http_status;
		$headers[] = 'From: <'.$config['send_email_from'].'>';


		// send test message using wp_mail function.
		$sent_message = wp_mail( $to, $subject, $message, $headers );

		//display message based on the result.
		if ( $sent_message ) {
			// The message was sent.
			we('The email message was sent successfully.');
		} else {
			// The message was not sent.
			we('The message was not sent!');
		}

	}

}


/**
 * get remote shortcode content, create local version of the used remote files.
 * @param $type
 * @return bool|mixed
 */

function kunzelaw_get_remote_value($type) {

	$config = kunzelaw_get_config();

	if (!file_exists($config['dir_plugin_tmp'])) {
		mkdir($config['dir_plugin_tmp'], 0755);
	}

	$fp = NULL;


	# if a cache file exists then use it
	if (file_exists($config['dir_plugin_tmp'].$type.'.html')) {

		we("get cache version");
		we("last update: " . date("F d Y H:i:s.", filemtime($config['dir_plugin_tmp'].$type.'.html')));

		# if the cache time is expired the pull a new version of the file from the remote server
		if (filemtime($config['dir_plugin_tmp'].$type.'.html')+$config['cache_time'] <= (time())) {

			we("cache time expired - pull new version from the remote server");

			if ($file = kunzelaw_get_remote_content($config['url_remote'].'/'.$type.'.html')) {

				#clean up error file if we had a previous error
				if (file_exists($config['dir_plugin_tmp'].'error.html')) {
					unlink ($config['dir_plugin_tmp'].'error.html');
				}

				unlink ($config['dir_plugin_tmp'].$type.'.html');
				$fp = file_put_contents($config['dir_plugin_tmp'] . $type . '.html', $file['body']);

			}else{
				# remote server does not work - no cache file exist - medium problem
				we("no connection - medium problem");

				$http_status = kunzelaw_check_remote_status($config['url_remote'].'/'.$type.'.html');
				kunzelaw_send_error_mail('Kunze Law error - check remote server connection and shortcode [kunze_'.$type.'] - cache files already exists', $http_status);
			}
		}
	} else {


		# if no cache file exists then create one
		if ($file = kunzelaw_get_remote_content($config['url_remote'].'/'.$type.'.html')) {

			#clean up error file if we had a previous error
			if (file_exists($config['dir_plugin_tmp'].'error.html')) {
				unlink ($config['dir_plugin_tmp'].'error.html');
			}

			we("no cache file exists - create new one");

			$fp = file_put_contents($config['dir_plugin_tmp'] . $type . '.html', $file['body']);

		}

	}


	# use always the cached file for content delivery
	if ($file = kunzelaw_get_cached_content($config['dir_plugin_tmp'].$type.'.html')) {
		return $file;
	} else {
		# remote server does not work and no cache file exist - big problem
		we("no connection - big problem");
		$http_status = kunzelaw_check_remote_status($config['url_remote'].'/'.$type.'.html');
		kunzelaw_send_error_mail('Kunze Law error - check remote server connection and shortcode [kunze_'.$type.'] - no cache file exists.', $http_status);
	}
}

# filter pages
add_filter('the_content','kunzelaw_page');
# filter widgets
add_filter('widget_text', 'kunzelaw_page');

add_action( 'admin_init', 'kunzelaw_save_network_settings' );
add_action( 'init', 'kunzelaw_network_setting_menu' );

?>