<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

/**
* acf_ngmdl_shortcode
*
*  @description: Shortcode Class
*  @since: 1.0
*  @created: 21/09/14
*/
if (!class_exists('acf_ngmdl_shortcode')){

	class acf_ngmdl_shortcode {

		var $height;
		var $width;	
		
		public function __construct(){
			// Hook Shortcode
			add_shortcode( 'acf_maps', array( $this, 'acf_ngmdl_do_shortcode' ) );

			// Hook wp_ajax
			add_action( 'wp_ajax_googlemap_initialize', array($this, 'acf_ngmdl_googlemap_initialize') );
			add_action( 'wp_ajax_nopriv_googlemap_initialize', array($this, 'acf_ngmdl_googlemap_initialize') );			
			
			// Hook javascripts
			add_action('wp_enqueue_scripts', array($this, 'acf_ngmdl_register_scripts'));
		}

		/*
		*  acf_ngmdl_register_scripts
		*
		*  @description: Scripts and Stylesheets
		*  @since: 1.0
		*  @created: 21/09/14
		*/
		public function acf_ngmdl_register_scripts() {
			// Registers Style
			wp_register_style('acf_ngmdl', ACF_NGMDL_PLUGIN_URL .'/css/acf-ngmdl.css', array(), '1');

			// Registers Scripts
			wp_register_script('google-api', 'http://maps.google.com/maps/api/js?sensor=false', array('jquery'), '1');
			wp_register_script('google-api-infoBubble', 'http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobubble/src/infobubble.js', array('jquery'), '1');
			wp_register_script('google-api-marker-cluster', 'http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js', array('jquery'), '1');
			wp_register_script('acf_ngmdl_js', ACF_NGMDL_PLUGIN_URL .'/js/acf-ngmdl.js', array('jquery'), '1');
			
			// Loaclization
			$translation_array = array( 'ajaxUrl' => admin_url('admin-ajax.php') );
			wp_localize_script( 'acf_ngmdl_js', 'acf_ngmdl', $translation_array );
			
			// Enqueue Style
			wp_enqueue_style('acf_ngmdl');
			
			// Enqueue Script
			wp_enqueue_script('google-api');
			wp_enqueue_script('google-api-marker-cluster');
			wp_enqueue_script('acf_ngmdl_js');
		}

		/*
		*  acf_ngmdl_do_shortcode
		*
		*  @description: Shortcode Start
		*  @since: 1.0
		*  @created: 21/09/14
		*/
		public function acf_ngmdl_do_shortcode($atts){

			// Attributes
			extract( shortcode_atts(
				array(
					'cat' 		=> 'all',
					'height'	=> '480px',
					'width'		=> '100%',
					'filter'	=> 1
				), $atts )
			);

			$args = array(
				//Type & Status Parameters
				'post_type'   => 'acf_maps',
				'post_status' => 'publish',
				
				//Pagination Parameters
				'posts_per_page'         => -1,
			);

			$terms = '';

			if ($cat != 'all'){
				$category = array_filter(explode(',', $cat));
				$terms = $this->acf_ngmdl_get_terms_id_list($category);

				$posttype = 'acf_maps_category';

				$args = array(
					//Type & Status Parameters
					'post_type'   => 'acf_maps',
					'post_status' => 'publish',
					
					//Pagination Parameters
					'posts_per_page'         => -1,

					//Taxonomy Parameters
					'tax_query' => array(
						'relation'  => 'AND',
							array(
								'taxonomy'         => $posttype,
								'field'            => 'id',
								'terms'            => $terms,
								'include_children' => true,
								'operator'         => 'IN'
							)
					),
				);
			}
			
			$the_query = new WP_Query( $args );

			$found = $the_query->found_posts;

			$markers = '';

			if ( $the_query->have_posts() ) {
				$i = 0;
				while ( $the_query->have_posts() ) {
					$the_query->the_post();
					$post_id = get_the_ID();

					$categories = get_the_terms($post_id, 'acf_maps_category');

					$cat_id_name = array();
					foreach ( $categories as $category ) {
						$inner = array();
						$inner['id'] = $category->term_id;
						$inner['name'] = $category->name;
						$cat_id_name[] = $inner;
					}

					$map_details = get_field( 'acf_ngmdl_map_details', $post_id);
					if ($map_details && !empty($map_details['address']) && !empty($map_details['lat']) && !empty($map_details['lng']) ){
						$markers[$i] = $map_details;
						$markers[$i]['id'] = $post_id;
						$markers[$i]['title'] = get_the_title();
						$markers[$i]['category'] = $cat_id_name;
						$markers[$i]['phone'] = get_field( 'acf_ngmdl_location_contact', $post_id); 
						$markers[$i]['url'] = get_field( 'acf_ngmdl_location_url', $post_id); 
						$markers[$i]['other'] = get_field( 'acf_ngmdl_location_other', $post_id); 
						$i++;
					}
				}
			}

			// Restore original Post Data
			wp_reset_postdata();
			
			$return = '';
			$marker_instances = '';
			// if ($markers){
				foreach ($markers as $marker) {
					$lng = $marker['lng'];
					$lat = $marker['lat'];
					$address = isset($marker['address']) ? $marker['address'] : '';
					$title = isset($marker['title']) ? $marker['title'] : '';
					$phone = isset($marker['phone']) ? $marker['phone'] : '';
					$url = isset($marker['url']) ? $marker['url'] : '';
					$other = isset($marker['other']) ? $marker['other'] : '';
					$mail_to = '';
					if (strpos($url,'@') !== false) {
						$mail_to = "mailto:";
					}

					if ($address != '') :
					$marker_instances .= "<div class='marker' style='display: none;' data-address='". $address ."' data-lat='". $lat ."' data-lng='". $lng ."'>
					<div class='map_infobubble map_popup' style='overflow: visible; cursor: default; clear: both; position: relative; width: 210px;'>
						<div class='google-map-info map-image'>
							<div class='map-inner-wrapper'>
								<div class='map-item-info'>
									<h6>
										<a class='ptitle' href='#'>
											<span>".$title."</span>
										</a>
									</h6>
									<p class='address'>".$address."</p>
									<p class='contact'>".$phone."</p>
									<p class='other'>".$other."</p>
									<p class='website'>
										<a href='".$mail_to.$url."'>".$url."</a>
									</p>
								</div>
							</div>
						</div>
					</div>
					<div style='position: relative;' class='map_infoarrow'>
						<div style='position: absolute; left: 50%; height: 0px; width: 0px; margin-left: 0px; border-width: 0px;'></div>
						<div style='position: absolute; left: 50%; height: 0px; width: 0px;'></div>
					</div>
				</div>";
					endif;
				}
			// }

			$return .= "<div id='map_sidebar' class='map_sidebar'><div class='acf_map_marker'><div class='acf_ngmdl' style='width: ".$width."; height: ".$height."'><div class='TopLeft'><span id='triggermap' class=''></span></div>
			<div class='iprelative'><div id='map_canvas' style='width: ".$width."; height:".$height."' class='acf-map'>".$marker_instances."</div>
			<input type='hidden' id='maps_found' value='".$found."'>
			<div id='map_marker_nofound' style='display: none;'><p>Your selected category do not have any records yet.</p></div>
			<div style='width: 100%; height: 480px; display: none;' id='map_loading_div'></div></div></div>";
			if ($filter){
				$return .= $this->acf_ngmdl_listing_tab($terms);
			}
			$return .= '</div></div>';

			// Display the content
			return $return;
			
		}

		/*
		*  acf_ngmdl_listing_tab
		*
		*  @description: Add filtering list
		*  @since: 1.0
		*  @created: 21/09/14
		*/
		public function acf_ngmdl_listing_tab($terms=''){

			$taxonomy = 'acf_maps_category';
			$args = array(
				'hide_empty' => true,
				'parent' => 0,
				'include' => $terms
			);
			$parents = get_terms($taxonomy, $args);

			$list = '';
			foreach ($parents as $parent => $value) {

				$list .= '<label style="margin-left:0px" for="in-listingcategory-'.$value->term_id.'"><input type="checkbox" onclick="newgooglemap_initialize(this,&quot;listing&quot;)" checked="checked" id="in-listingcategory-'.$value->term_id.'" value="'.$value->term_id.'" name="categoryname[]">'.$value->name.'</label>';

				$children = get_terms( $taxonomy, array('child_of' => $value->term_id) );
				$sub_list = '';
				foreach ($children as $child => $c_value) {
					$sub_list .= '<label style="margin-left:15px" for="in-listingcategory-'.$c_value->term_id.'"><input type="checkbox" onclick="newgooglemap_initialize(this,&quot;listing&quot;)" checked="checked" id="in-listingcategory-'.$c_value->term_id.'" value="'.$c_value->term_id.'" name="categoryname[]">'.$c_value->name.'</label>';
				}
				if (!empty($sub_list)){
					$list .= $sub_list;
				}
			}

			?>
			<div class="ajaxform">
				<form id="ajaxform" name="slider_search" class="pe_advsearch_form" action="javascript:void(0);"  onsubmit="return(new_googlemap_ajaxSearch());">

					<div class="paf_row map_post_type" id="toggle_postID" style="display:block; max-height:375px;">

						<div class="mw_cat_title">
							<label>
								<input type="checkbox" name="posttype[]" id="listingcustom_categories" class="listingcustom_categories" checked="checked" value="<?php echo $taxonomy; ?>" onclick="newgooglemap_initialize(this,&quot;&quot;);" data-category="listingcategories">
									Map Categories
								</label>
							<span onclick="map_category_toggle('listingcategories',this)" class="toggleon toggle_listing" id="listing_toggle"></span>
						</div>
						<div id="listingcategories" class="custom_categories listingcustom_categories" style="display: block;">
							<?php echo $list; ?>
						</div>

					</div>
					<div onclick="toggle_listing();" class="paf_row toggleon" id="toggle_listing"></div>
				</form>
			</div>
			<?php
		}

		/*
		*  acf_ngmdl_googlemap_initialize
		*
		*  @description: Google Maps filtering initialize
		*  @since: 1.0
		*  @created: 21/09/14
		*/
		public function acf_ngmdl_googlemap_initialize(){
			
			$category = array_filter(explode(',', $_POST['category']));
			$posttype = $_POST['posttype'];
			$map_canvas_style = $_POST['style'];

			$return = '';
			$marker_instances = '';
			$found = 0;

			if (!empty($category) && !empty($posttype)){

				$args = array(
					//Type & Status Parameters
					'post_type'   => 'acf_maps',
					'post_status' => 'publish',
					
					//Pagination Parameters
					'posts_per_page'         => -1,

					//Taxonomy Parameters
					'tax_query' => array(
						'relation'  => 'AND',
							array(
								'taxonomy'         => $posttype,
								'field'            => 'id',
								'terms'            => $category,
								'include_children' => true,
								'operator'         => 'IN'
							)
					),
				);
				
				$the_query = new WP_Query( $args );

				$found = $the_query->found_posts;

				$markers = '';

				if ( $the_query->have_posts() ) {
					$i = 0;
					while ( $the_query->have_posts() ) {
						$the_query->the_post();
						$post_id = get_the_ID();
						$categories = get_the_terms($post_id, 'acf_maps_category');

						$cat_id_name = array();
						foreach ( $categories as $category ) {
							$inner = array();
							$inner['id'] = $category->term_id;
							$inner['name'] = $category->name;
							$cat_id_name[] = $inner;
						}

						$map_details = get_field( 'acf_ngmdl_map_details', $post_id);
						if ($map_details && !empty($map_details['address']) && !empty($map_details['lat']) && !empty($map_details['lng']) ){
							$markers[$i] = $map_details;
							$markers[$i]['id'] = $post_id;
							$markers[$i]['title'] = get_the_title();
							$markers[$i]['category'] = $cat_id_name;
							$markers[$i]['phone'] = get_field( 'acf_ngmdl_location_contact', $post_id); 
							$markers[$i]['url'] = get_field( 'acf_ngmdl_location_url', $post_id); 
							$markers[$i]['other'] = get_field( 'acf_ngmdl_location_other', $post_id); 
							$i++;
						}
					}
				}

				// Restore original Post Data
				wp_reset_postdata();

				if ($markers != ''){
					foreach ($markers as $marker) {
						$lng = $marker['lng'];
						$lat = $marker['lat'];
						$address = isset($marker['address']) ? $marker['address'] : '';
						$title = isset($marker['title']) ? $marker['title'] : '';
						$phone = isset($marker['phone']) ? $marker['phone'] : '';
						$url = isset($marker['url']) ? $marker['url'] : '';
						$other = isset($marker['other']) ? $marker['other'] : '';
						$mail_to = '';
						if (strpos($url,'@') !== false) {
							$mail_to = "mailto:";
						}

						if ($address != '') :
						$marker_instances .= "<div class='marker' data-address='". $address ."' data-lat='". $lat ."' data-lng='". $lng ."'>
						<div class='map_infobubble map_popup' style='overflow: visible; cursor: default; clear: both; position: relative; width: 210px;'>
							<div class='google-map-info map-image'>
								<div class='map-inner-wrapper'>
									<div class='map-item-info'>
										<h6>
											<a class='ptitle' href='#'>
												<span>".$title."</span>
											</a>
										</h6>
										<p class='address'>".$address."</p>
										<p class='contact'>".$phone."</p>
										<p class='other'>".$other."</p>
										<p class='website'>
											<a href='".$mail_to.$url."'>".$url."</a>
										</p>
									</div>
								</div>
							</div>
						</div>
						<div style='position: relative;' class='map_infoarrow'>
							<div style='position: absolute; left: 50%; height: 0px; width: 0px; margin-left: 0px; border-width: 0px;'></div>
							<div style='position: absolute; left: 50%; height: 0px; width: 0px;'></div>
						</div>
					</div>";
						endif;
					}
				}
			}
			
			$return .= "<div id='map_canvas' style='".$map_canvas_style."' class='acf-map'>".$marker_instances."</div>
			<input type='hidden' id='maps_found' value='".$found."'>";

			echo $return;
			exit();
		}

		/*
		*  acf_ngmdl_get_terms_id_list
		*
		*  @description: Get the array list of ID by category or name
		*  @since: 1.0
		*  @created: 21/09/14
		*/
		public function acf_ngmdl_get_terms_id_list($terms){

			$taxonomy = 'acf_maps_category';

			$slugs = array();
			$array = array();

			foreach ($terms as $term) {
				$slugs[] = get_term_by( 'slug', $term, $taxonomy, 'ARRAY_A' );
				$slugs[] = get_term_by( 'name', $term, $taxonomy, 'ARRAY_A' );
			}

			foreach ($slugs as $slug) {
				$array[] = $slug['term_id'];
			}

			$array = array_unique($array);

			return $array;
		}
	}

	$acf_ngmdl_shortcode = new acf_ngmdl_shortcode();
}
?>