<?php
/*
Plugin Name: QuickBlox MapChat
Plugin URI: http://quickblox.com/applications/mapchat
Description: QuickBlox MapChat is simple plugin for Wordpress, that fast adds MapChat (chat integrated with map) to your website and powers it with great functions and features of communication.
Version: 0.6
Author: QuickBlox Team
Author URI: http://quickblox.com
License: License
*/

add_action('admin_menu', 'qb_create_menu');

add_action('init', 'register_qb_mapchat_widget');

add_shortcode( 'qbmapchat', 'qb_shortcode_handler' );

// add_action( 'activate_qbmapchat', 'init_defaults' );
register_activation_hook( __FILE__, 'init_defaults' );

function init_defaults() {
	$defaults['appId'] = '208';
	//$defaults['ownerId'] = '4434';
	$defaults['authKey'] = 'DLuug-5VNjNTGsV';
	$defaults['authSecret'] = 'F7mWg3f2X2WM-hk';
	
	if (get_option('qb_app_id') == '') {
		update_option('qb_app_id', $defaults['appId']);	
	}
	/*if (get_option('qb_owner_id') == '') {
		update_option('qb_owner_id', $defaults['ownerId']);	
	}*/
	if (get_option('qb_auth_key') == '') {
		update_option('qb_auth_key', $defaults['authKey']);	
	}
	if (get_option('qb_auth_secret') == '') {
		update_option('qb_auth_secret', $defaults['authSecret']);	
	}
	
	add_option('qb_activated', 1);
	update_option('qb_activated', 1);
}

function qb_create_menu() {
	add_menu_page('QB MapChat Settings', 'QB MapChat', 'administrator', __FILE__, 'qb_settings_form', plugins_url('favicon.ico', __FILE__));
	add_action( 'admin_init', 'register_mysettings' );
}

function register_mysettings() {
	register_setting('qb-settings-group', 'qb_app_id');
	//register_setting('qb-settings-group', 'qb_owner_id');
	register_setting('qb-settings-group', 'qb_auth_key');
	register_setting('qb-settings-group', 'qb_auth_secret');
	register_setting('qb-settings-group', 'qb_widget_title');
	register_setting('qb-settings-group', 'qb_widget_height');
	register_setting('qb-settings-group', 'qb_widget_width');
}

function getRealIpAddr() {
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip=$_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    	$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
    	$ip=$_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}	

function qb_settings_form() {
	$errorMessage = '<div id="message" class="error"><p><strong>There is no QuickBlox application with specified parameters. Check parameters (application id, auth key and auth secret), please.</strong></p></div>';
	$emptyFieldsMessage = '<div id="message" class="updated"><p><strong>Just fill fields below to get MapChat widget in your website pages.</strong></p></div>';

	$appId =  get_option('qb_app_id');
	//$ownerId =  get_option('qb_owner_id');
	$authKey =  get_option('qb_auth_key');
	$authSecret =  get_option('qb_auth_secret'); 
	
	$activated = get_option('qb_activated');
	
	if ($_GET['settings-updated'] == true || $activated) {
		//add_option('qb_activated', 1);
		update_option('qb_activated', 0);

		$website_domain = '1';
		if ($_SERVER['HTTP_HOST']) {
			$website_domain = $_SERVER['HTTP_HOST'];
		}
		
		$website_ip = getRealIpAddr();
		
		$params = "app_domain=$website_domain&app_id=$appId&auth_key=$authKey&auth_secret=$authSecret&param_response=1&ip=$website_ip";
		
		$resKey = POSTRequest('http://quickblox.com/applications/mapchat/code.php', $params, true);

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

	if (!$appId && /*!$ownerId &&*/ !$authKey && !$authSecret) {
		echo $emptyFieldsMessage;
	} else 
		if ($optKey == '0') {
			echo $errorMessage;
		}
	
?>    
    
    <div class="wrap">
	    <h2>QuickBlox MapChat Settings</h2>
		
		<p>To create your own cloud hosted MapChat instance:</p>
		<ol>
			<li>Register your QuickBlox account;</li>
			<li>Create an application;</li>
			<li>Copy&Paste <a href="http://i.imgur.com/PXfiX97.png">application parameters</a>.</li>
		</ol> 
		<p>Any difficulties &mdash; check out the <a href="http://quickblox.com/developers/5_Minute_Guide">5 minute guide</a> or submit your issue to our <a href="http://community.quickblox.com/quickblox/products/quickblox_wordpress_widget_mapchat">support community</a>.</p>		
		
	    <h3 class="title">Chat Settings</h3>
	    <p><strong>Attention</strong>: when you <em>first run</em> the plugin, the default settings are for the demo account. To setup your own MapChat, please register for your own QuickBlox account and put your application settings in the fields below.</p>
	    <form method="post" action="options.php">
		    <?php settings_fields( 'qb-settings-group' ); ?>
			<table class="form-table">
				<tr valign="top">
				<th scope="row"><label for="qb_app_id">Application id</label></th>
				<td><input name="qb_app_id" type="text" id="qb_app_id" class="regular-text code" placeholder="e.g. 123" value="<?php echo get_option('qb_app_id') ?>"></td>
				</tr>
				<!--<tr valign="top">
				<th scope="row"><label for="qb_owner_id">Account owner id</label></th>
				<td><input name="qb_owner_id" type="text" id="qb_owner_id" class="regular-text code" placeholder="e.g. 456" value="<?php //echo get_option('qb_owner_id') ?>">
				<span class="description"></span></td>
				</tr>-->
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
	
	return "<div id='qb-mapchat'$style><div id='qb-powered-by-quickblox' style='font:11px/18px Arial, Helvetica, sans-serif !important; height:18px !important; position:relative !important; margin-bottom:-18px !important; padding-right:5px !important; text-align:right !important; margin-left:130px !important;'>Powered by <a href='http://quickblox.com' style='color:#3B5998 !important; text-decoration:none !important;'>QuickBlox</a></div><iframe src='http://quickblox.com/applications/mapchat/app.php?key=$key' frameborder=0 height=100% width=100%></iframe></div>";
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