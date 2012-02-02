<?php
/*
Plugin Name: QuickBlox MapChat
Plugin URI: http://quickblox.com/apps/mapchat
Description: QuickBlox MapChat is simple plugin for Wordpress, that fast adds MapChat (chat integrated with map) to your website and powers it with great functions and features of communication.
Version: 0.1
Author: QuickBlox Team
Author URI: http://quickblox.com
License: License
*/

add_action('admin_menu', 'qb_create_menu');

function qb_create_menu() {
	add_menu_page('QB MapChat Settings', 'QB MapChat', 'administrator', __FILE__, 'qb_settings_form', plugins_url('favicon.ico', __FILE__));
	add_action( 'admin_init', 'register_mysettings' );
}

function register_mysettings() {
	register_setting( 'qb-settings-group', 'qb_app_id' );
	register_setting( 'qb-settings-group', 'qb_owner_id' );
	register_setting( 'qb-settings-group', 'qb_auth_key' );
	register_setting( 'qb-settings-group', 'qb_auth_secret' );
	register_setting( 'qb-settings-group', 'qb_widget_title' );
	register_setting( 'qb-settings-group', 'qb_widget_height' );
	register_setting( 'qb-settings-group', 'qb_widget_width' );
}

function qb_settings_form() {
	$errorMessage = '<div id="message" class="error"><p><strong>There is no QuickBlox application with specified parameters. Check parameters (application id, owner id, auth key and auth secret), please.</strong></p></div>';
	$emptyFieldsMessage = '<div id="message" class="updated"><p><strong>Just fill fields below to get MapChat widget in your website pages.</strong></p></div>';

	$appId =  get_option('qb_app_id');
	$ownerId =  get_option('qb_owner_id');
	$authKey =  get_option('qb_auth_key');
	$authSecret =  get_option('qb_auth_secret'); 
	
	if ($_GET['settings-updated'] == true) {
		
		$params = "app_domain=1&app_id=$appId&owner_id=$ownerId&auth_key=$authKey&auth_secret=$authSecret&param_response=1";
		
		$resKey = POSTRequest('http://quickblox.com/apps/mapchat/code.php', $params, true);

		if (strlen($resKey) == 0) {
			$resKey = '0';
		}
		
		add_option('qb_key', $resKey);
		update_option('qb_key', $resKey);
		
		$message = '<div id="message" class="updated"><p><strong>Updated.</strong></p></div>'; 
		
		if ($resKey != '0') {
			echo $message;
		}
		
	}
	
	$optKey = get_option('qb_key');

	if (!$appId && !$ownerId && !$authKey && !$authSecret) {
		echo $emptyFieldsMessage;
	} else 
		if ($optKey == '0') {
			echo $errorMessage;
		}
	
?>    
    
    <div class="wrap">
	    <h2>QuickBlox MapChat Settings</h2>
	    <p>You can simply get <a href="https://img.skitch.com/20120123-8iqh4qh3ftqjamu1mhfjhnrcx6.png">application parameters</a> after you register your QuickBlox account and add application.</p>
		<p>You should just register, log in, and get parameters of your appliction. More detailed information look at <a href="http://wiki.quickblox.com/5_Minute_Guide">5 minute QuickBlox guide</a> (first three points).</p>
	    <h3 class="title">Chat Settings</h3>
	    <form method="post" action="options.php">
		    <?php settings_fields( 'qb-settings-group' ); ?>
			<table class="form-table">
				<tr valign="top">
				<th scope="row"><label for="qb_app_id">Application id</label></th>
				<td><input name="qb_app_id" type="text" id="qb_app_id" class="regular-text code" placeholder="e.g. 123" value="<?php echo get_option('qb_app_id') ?>"></td>
				</tr>
				<tr valign="top">
				<th scope="row"><label for="qb_owner_id">Account owner id</label></th>
				<td><input name="qb_owner_id" type="text" id="qb_owner_id" class="regular-text code" placeholder="e.g. 456" value="<?php echo get_option('qb_owner_id') ?>">
				<span class="description"></span></td>
				</tr>
				<tr valign="top">
				<th scope="row"><label for="qb_auth_key">Authorization key</label></th>
				<td><input name="qb_auth_key" type="text" id="qb_auth_key" class="regular-text code" placeholder="e.g. WOuFKaCjtfksRnF" value="<?php echo get_option('qb_auth_key') ?>"></td>
				</tr>
				<tr valign="top">
				<th scope="row"><label for="qb_auth_secret">Authorization secret</label></th>
				<td><input name="qb_auth_secret" type="text" id="qb_auth_secret" class="regular-text code" placeholder="e.g. zvrThWhXwn5p5k0" value="<?php echo get_option('qb_auth_secret') ?>"></td>
				</tr>
			</table>
			<p>To get MapChat on your blog entry or post just add following code to the entry: <code>[qbmapchat]</code></p>
			<p>If you want to set width or height of MapChat layout, please use following code: <code>[qbmapchat width="500px" height="500px"]</code></p>
			<h3 class="title">Widget Settings</h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="qb_widget_title">Widget Title</label></th>
					<td><input name="qb_widget_title" type="text" id="qb_widget_title" class="regular-text" value="<?php echo get_option('qb_widget_title') ?>" placeholder="My MapChat"></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="qb_widget_height">Height</label></th>
					<td><input name="qb_widget_height" type="text" id="qb_widget_height" class="regular-text" value="<?php echo get_option('qb_widget_height') ?>" placeholder="e.g. 100% or 100px"></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="qb_widget_width">Width</label></th>
					<td><input name="qb_widget_width" type="text" id="qb_widget_width" class="regular-text" value="<?php echo get_option('qb_widget_width') ?>" placeholder="e.g. 100% or 100px"></td>
				</tr>
			</table>
			<p><input type="submit" name="update" id="submit" class="button-primary" value="Save Changes"></p>
	    </form>
    </div>

<?php 
} 

function hello_world($title) {
    echo $title.' -> '.get_option('qb_widget_title');
}

function qb_mapchat_widget($args) {
    extract($args);
    
    echo $before_widget; 
    echo $before_title;
    echo get_option('qb_widget_title');
    echo $after_title; 
    echo get_widget_code();
    echo $after_widget; 
}

function register_qb_mapchat_widget() {
    register_sidebar_widget('QB MapChat', 'qb_mapchat_widget');
}

add_action('init', 'register_qb_mapchat_widget');

add_shortcode( 'qbmapchat', 'qb_shortcode_handler' );

function get_mapchat_code($h, $w) {
	$key = get_option('qb_key');
	$style = '';
	if ($h && !$w) {
		$style = " style='height:$h;'";	
	}
	if ($w && !$h) {
		$style = " style='width:$w;'";	
	}
	if ($w && $h) {
		$style = " style='width:$w; height:$h;'";
	}
	
	return "<div id='qb-mapchat'$style><div id='qb-powered-by-quickblox' style='font:11px/18px Arial, Helvetica, sans-serif !important; height:18px !important; position:relative !important; margin-bottom:-18px !important; padding-right:5px !important; text-align:right !important; margin-left:130px !important;'>Powered by <a href='http://quickblox.com' style='color:#3B5998 !important; text-decoration:none !important;'>QuickBlox</a></div><iframe src='http://quickblox.com/apps/mapchat/app.php?key=$key' frameborder=0 height=100% width=100%></iframe></div>";
}

function get_widget_code() {
	$height = get_option('qb_widget_height');
	$width = get_option('qb_widget_width');
	return get_mapchat_code($height, $width);
}

function qb_shortcode_handler($atts, $content=null, $code="") {
	extract(shortcode_atts( array(
		'height' => '100%',
		'width' => '100%'
	), $atts));	
	
	return get_mapchat_code($height, $width);
}

function POSTRequest($url, $data, $return) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, strlen($data));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
	if (!$return) {
		header("HTTP/1.0 $status");
	} 
	
	curl_close ($ch);
	
	if ($return) {
		return $result;
	} else {
		echo $result;	
	}
}

?>