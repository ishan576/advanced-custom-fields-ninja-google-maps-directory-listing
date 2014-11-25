<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

/**
* acf_ngmdl_init
*
*  @description: acf mm Class
*  @since: 1.0
*  @created: 21/09/14
*/
if (!class_exists('acf_ngmdl_init')){
	class acf_ngmdl_init {
		/*
		*  Construct
		*
		*  @description: 
		*  @since: 1.0
		*  @created: 21/09/14
		*/
		
		function __construct() {

			// Actions
			add_action( 'init', array( $this, 'acf_ngmdl_install') );
			add_action( 'plugins_loaded', array( $this, 'acf_ngmdl_do_shortcode') );

			add_action( 'add_meta_boxes', array( $this, 'acf_ngmdl_featured_meta' ) );
			add_action( 'save_post', array( $this, 'acf_ngmdl_save_meta' ) );

		}

		/*
		*  acf_ngmdl_init
		*
		*  @description: Plugin init. Create Post Type and add new Field group to ACF
		*  @since: 1.0
		*  @created: 21/09/14
		*/
		public static function acf_ngmdl_install(){
			// Create acf-maps Post Type
			$labels = array(
				'name'                => _x( 'Maps', 'Post Type General Name', 'acf_ngmdl' ),
				'singular_name'       => _x( 'Map', 'Post Type Singular Name', 'acf_ngmdl' ),
				'menu_name'           => __( 'Ninja Maps', 'acf_ngmdl' ),
				'parent_item_colon'   => __( 'Parent Map:', 'acf_ngmdl' ),
				'all_items'           => __( 'All Maps', 'acf_ngmdl' ),
				'view_item'           => __( 'View Map', 'acf_ngmdl' ),
				'add_new_item'        => __( 'Add New Map', 'acf_ngmdl' ),
				'add_new'             => __( 'Add Map', 'acf_ngmdl' ),
				'edit_item'           => __( 'Edit Map', 'acf_ngmdl' ),
				'update_item'         => __( 'Update Map', 'acf_ngmdl' ),
				'search_items'        => __( 'Search Map', 'acf_ngmdl' ),
				'not_found'           => __( 'Not found', 'acf_ngmdl' ),
				'not_found_in_trash'  => __( 'Not found in Trash', 'acf_ngmdl' ),
			);
			$args = array(
				'label'               => __( 'acf_maps', 'acf_ngmdl' ),
				'description'         => __( 'Maps Marker', 'acf_ngmdl' ),
				'labels'              => $labels,
				'supports'            => array( 'title', 'thumbnail'),
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => true,
				'show_in_admin_bar'   => true,
				'menu_position'       => NULL,
				'menu_icon'           => 'dashicons-location-alt',
				'can_export'          => true,
				'has_archive'         => true,
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'rewrite'             => true,
				'query_var'           => 'acf_ngmdl',
				'capability_type'     => 'post',
			);
			register_post_type( 'acf_maps', $args );

			//Add Maps Category
			$labels = array(
				'name'                       => _x( 'Map Categories', 'Taxonomy General Name', 'acf_ngmdl' ),
				'singular_name'              => _x( 'Map Category', 'Taxonomy Singular Name', 'acf_ngmdl' ),
				'menu_name'                  => __( 'Map Category', 'acf_ngmdl' ),
				'all_items'                  => __( 'All Map Categories', 'acf_ngmdl' ),
				'parent_item'                => __( 'Parent Map Category', 'acf_ngmdl' ),
				'parent_item_colon'          => __( 'Parent Map Category:', 'acf_ngmdl' ),
				'new_item_name'              => __( 'New Map Category Name', 'acf_ngmdl' ),
				'add_new_item'               => __( 'Add New Map Category', 'acf_ngmdl' ),
				'edit_item'                  => __( 'Edit Map Category', 'acf_ngmdl' ),
				'update_item'                => __( 'Update Map Category', 'acf_ngmdl' ),
				'separate_items_with_commas' => __( 'Separate Map Categories with commas', 'acf_ngmdl' ),
				'search_items'               => __( 'Search Map Categories', 'acf_ngmdl' ),
				'add_or_remove_items'        => __( 'Add or remove Map Categories', 'acf_ngmdl' ),
				'choose_from_most_used'      => __( 'Choose from the most used Map Categories', 'acf_ngmdl' ),
				'not_found'                  => __( 'Not Found', 'acf_ngmdl' ),
			);
			$args = array(
				'labels'                     => $labels,
				'hierarchical'               => true,
				'public'                     => false,
				'show_ui'                    => true,
				'show_admin_column'          => true,
				'show_in_nav_menus'          => true,
				'show_tagcloud'              => true,
			);
			register_taxonomy( 'acf_maps_category', array( 'acf_maps' ), $args );

			// Add meta fields to the acf_ngmdl post
			if(function_exists("register_field_group")){
				register_field_group( array(
					'id' => 'acf_map',
					'title' => 'Map',
					'fields' => array (
						array (
							'key' => 'field_541dbac0e5458',
							'label' => 'Map Details',
							'name' => 'acf_ngmdl_map_details',
							'type' => 'google_map',
							'center_lat' => '36.95344',
							'center_lng' => '8.44435',
							'zoom' => 2,
							'height' => '',
						),
						array (
							'key' => 'field_5421bc7d2042c',
							'label' => 'Location Contact',
							'name' => 'acf_ngmdl_location_contact',
							'type' => 'text',
							'default_value' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'formatting' => 'html',
							'maxlength' => '',
						),
						array (
							'key' => 'field_5421bbfe2042b',
							'label' => 'Location URL',
							'name' => 'acf_ngmdl_location_url',
							'type' => 'text',
							'default_value' => '',
							'placeholder' => 'http://www.google.com',
							'prepend' => '',
							'append' => '',
							'formatting' => 'html',
							'maxlength' => '',
						),
						array (
							'key' => 'field_5421bc7d2142c',
							'label' => 'Location Other Details',
							'name' => 'acf_ngmdl_location_other',
							'type' => 'textarea',
							'default_value' => '',
							'placeholder' => '',
							'maxlength' => '',
							'rows' => '',
							'formatting' => 'br',
						),
					),
					'location' => array (
						array (
							array (
								'param' => 'post_type',
								'operator' => '==',
								'value' => 'acf_maps',
								'order_no' => 0,
								'group_no' => 0,
							),
						),
					),
					'options' => array (
						'position' => 'normal',
						'layout' => 'default',
						'hide_on_screen' => array (
						),
					),
					'menu_order' => 0,
				));
			}

		}

		/*
		*  acf_ngmdl_featured_meta
		*
		*  @description: hook to add a meta box
		*  @since: 1.0
		*  @created: 21/09/14
		*/
		public function acf_ngmdl_featured_meta() {
			//create a custom meta box
			add_meta_box( 'acf_ngmdl_meta', 'Featured Map', array($this, 'acf_ngmdl_featured'), 'acf_maps', 'side' );
		}

		/*
		*  acf_ngmdl_featured
		*
		*  @description: create a custom meta box
		*  @since: 1.0
		*  @created: 21/09/14
		*/
		public function acf_ngmdl_featured( $post ) {

			//retrieve the meta data values if they exist
			$acf_ngmdl_featured = get_post_meta( $post->ID, '_acf_ngmdl_featured', true );

			?>
			<p>Featured: 
			<select name="acf_ngmdl_featured">
				<option value="no" <?php selected( $acf_ngmdl_featured, 'no' ); ?>>No Way</option>
				<option value="yes" <?php selected( $acf_ngmdl_featured, 'yes' ); ?>>Yes, Sure</option>
			</select>
			</p>
			<?php
		}

		/*
		*  acf_ngmdl_save_meta
		*
		*  @description: save the meta box data
		*  @since: 1.0
		*  @created: 21/09/14
		*/
		public function acf_ngmdl_save_meta( $post_ID ) {
			global $post;
			if( isset($post) && $post->post_type == "acf_maps" ) {
				if ( isset( $_POST ) ) {
					update_post_meta( $post_ID, '_acf_ngmdl_featured', strip_tags( $_POST['acf_ngmdl_featured'] ) );
				}
			}
		}


		/*
		*  acf_ngmdl_uninstall
		*
		*  @description: Plugin Uninstall. Removes all the data associate to this plugin.
		*  @since: 1.0
		*  @created: 21/09/14
		*/
		public static function acf_ngmdl_uninstall(){
			if ( ! current_user_can( 'activate_plugins' ) )
				return;

			global $post;

			// Delete all the posts of the acf_maps post type
			$args = array(
				
				'post_type'   => 'acf_maps',
				'post_status' => array( 'publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash' ),
				
			);
			
			$the_query = new WP_Query( $args );

			if ( $the_query->have_posts() ) {
				while ( $the_query->have_posts() ) {
					$the_query->the_post();
					wp_delete_post( get_the_ID(), true );
				}
			}
			
			// Restore original Post Data
			wp_reset_postdata();
		}

		/*
		*  acf_ngmdl_do_shortcode
		*
		*  @description: Add Map with shortcode
		*  @since: 1.0
		*  @created: 21/09/14
		*/
		public function acf_ngmdl_do_shortcode(){
			if (!is_admin()){
				$acf_ngmdl_shortcode = new acf_ngmdl_shortcode();
			}
		}

	}

	// Init
	$acf_ngmdl_init = new acf_ngmdl_init();
}

// Uninstall hook
register_uninstall_hook( __FILE__ , array( 'acf_ngmdl_init', 'acf_ngmdl_uninstall' ) );

?>