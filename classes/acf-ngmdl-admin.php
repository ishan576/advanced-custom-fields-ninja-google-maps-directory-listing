<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

/**
* acf_ngmdl_admin
*
*  @description: Admin Menu Class
*  @since: 1.0
*  @created: 21/09/14
*/

if (!class_exists('acf_ngmdl_admin')){

	class acf_ngmdl_admin {
		
		function __construct(){

			add_action( 'admin_menu', array($this, 'acf_ngmdl_submenu' ) );			
			add_action( 'admin_enqueue_scripts', array($this, 'acf_ngmdl_register_admin_scripts' ) );

		}

		/*
		*  acf_ngmdl_register_admin_scripts
		*
		*  @description: Register admin script and styles
		*  @since: 1.0
		*  @created: 21/09/14
		*/
		public function acf_ngmdl_register_admin_scripts(){
			// Registers Style
			wp_register_style('acf_ngmdl_admin', ACF_NGMDL_PLUGIN_URL .'/css/acf-ngmdl-admin.css', array(), '1');

			// Enqueue Style
			wp_enqueue_style('acf_ngmdl_admin');

		}

		/*
		*  acf_ngmdl_submenu
		*
		*  @description: Add submenu support to the Ninja Maps Menu
		*  @since: 1.0
		*  @created: 21/09/14
		*/
		public function acf_ngmdl_submenu(){
			add_submenu_page( 'edit.php?post_type=acf_maps', 'Get Started', 'Get Started', 'manage_options', 'acf-mm-get-started', array($this, 'acf_ngmdl_submenu_get_started') ) ; 
		}

		/*
		*  acf_ngmdl_submenu_get_started
		*
		*  @description: Add "Get Started" help submenu content
		*  @since: 1.0
		*  @created: 21/09/14
		*/
		public function acf_ngmdl_submenu_get_started(){
			?>
			<div class="wrap acf_ngmdl">

				<div class="banner-img">
					<div class="img">
						<a href="https://www.wphostingninjas.com" target="_blank"><img src="http://www.customwpninjas.com/wp-content/plugins/gzip-ninja-speed-compression/images/wphostingninjas-banner.jpg"></a>
					</div>
				</div>

				<h2>Get Started</h2>

				<div class="welcome-panel" id="welcome-panel">

					<div class="welcome-panel-content">
						<div class="welcome-panel-column-container">
							<div class="welcome-panel-column">
								<h4>How to use Advanced Custom Fields: Ninja Google Maps Directory Listing</h4>
								<p><label for='s_maps'>This short code allows you to see all the map points that have been placed</label> <input type='text' id='s_maps' value='[acf_maps]' onfocus='this.select();' readonly='readonly' style='width: 83px;'></p>
								<p><label for='s_maps'>Replace the x with your <strong>Category Slug</strong> for the map type you want to display</label> <input type='text' id='s_maps' value='[acf_maps cat="x"]' onfocus='this.select();' readonly='readonly' style='width: 133px;'></p>
								<p><label for='s_maps'>Replace the y with your maximum height requirement in <strong>pixels</strong></label> <input type='text' id='s_maps' value='[acf_maps height="y"]' onfocus='this.select();' readonly='readonly' style='width: 154px;'></p>
								<p><label for='s_maps'>Replace the z with your maximum width requirement in <strong>pixels</strong></label> <input type='text' id='s_maps' value='[acf_maps width="z"]' onfocus='this.select();' readonly='readonly' style='width: 149px;'></p>
								<p><label for='s_maps'>Show the category filter on the map <strong>(default)</strong></label> <input type='text' id='s_maps' value='[acf_maps filter="1"]' onfocus='this.select();' readonly='readonly' style='width: 144px;'></p>
								<p><label for='s_maps'>Do not show the category filter on the map</label> <input type='text' id='s_maps' value='[acf_maps filter="0"]' onfocus='this.select();' readonly='readonly' style='width: 144px;'></p>
							</div>
							<div class="welcome-panel-column">
								<h4>Multiple Use Example:</h4>
								<p><label for='s_maps'>Display all maps in category X with a height of Y and a width of Z while turning the filter off.</label> <input type='text' id='s_maps' value='[acf_maps cat="x" height="y" width="z" filter="0"]' onfocus='this.select();' readonly='readonly' style='width: 331px;'></p>
							</div>
						</div>
					</div>

				</div>

				<div class="banner-img">
					<div class="img">
						<form target="_top" method="post" action="https://www.paypal.com/cgi-bin/webscr">
							<input type="hidden" value="_s-xclick" name="cmd">
							<input type="hidden" value="E9STJGRYXRH24" name="hosted_button_id">
							<input border="0" type="image" alt="PayPal - The safer, easier way to pay online!" name="submit" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif">
							<img border="0" width="1" height="1" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" alt="">
						</form>
					</div>
				</div>
				<div class="banner-img">
					<div class="img">
						<a href="http://www.customwpninjas.com" target="_blank"><img src="http://www.customwpninjas.com/wp-content/plugins/gzip-ninja-speed-compression/images/customwpninjas-banner.jpg"></a>
					</div>
				</div>
				
			</div>
			<?php 
		}

	}

	// Init
	if (is_admin()){
		$acf_ngmdl_admin = new acf_ngmdl_admin();
	}

}

?>