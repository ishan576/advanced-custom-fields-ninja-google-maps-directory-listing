<?php
/*
Plugin Name: Advanced Custom Fields: Ninja Google Maps Directory Listing
Plugin URI: http://customwpninjas.com
Description: The plugin creates custom post type where the users can add add a new map loaction and using a short code maps marker can be diplayed on front end.
Version: 1.0.0
Author: CustomWPNinjas
Author URI: http://customwpninjas.com
Contributor: Ishan Kukadia
Tested up to: 4.0
Text Domain: acf-ngmdl

License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

// Check if Advanced Custom Field is active on plugin activation
if ( ! in_array( 'advanced-custom-fields/acf.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	deactivate_plugins( 'advanced-custom-fields-ninja-google-maps-directory-listing/acf-ngmdl.php');
	add_action('admin_notices', function(){echo '<div id="message" class="error"><p>Advanced Custom Fields: Ninja Google Maps Directory Listing requires "Advanced Custom Field" Plugin. Please install and active Advanced Custom Fields. <br /><strong>Advanced Custom Fields: Ninja Google Maps Directory Listing is automatically deactivated.</strong></p></div>';});
	return;
}

// Constants
define('ACF_NGMDL_PLUGIN_URL', plugins_url() . '/advanced-custom-fields-ninja-google-maps-directory-listing');
define('ACF_NGMDL_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define('ACF_PLUGIN_URL', plugins_url() . '/advanced-custom-fields');

// Include
include_once(ACF_NGMDL_PLUGIN_PATH.'classes/acf-ngmdl-init.php');
include_once(ACF_NGMDL_PLUGIN_PATH.'classes/acf-ngmdl-admin.php');
include_once(ACF_NGMDL_PLUGIN_PATH.'classes/acf-ngmdl-shortcode.php');
include_once(ACF_NGMDL_PLUGIN_PATH.'includes/acf-ngmdl-includes.php');

?>