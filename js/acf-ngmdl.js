var ajaxUrl = acf_ngmdl.ajaxUrl;
var infowindow;
var map;
var is_fullscreen = 0;
var show = 0;

/*
 *  render_map
 *
 *  @description: This function will render a Google Map onto the selected jQuery element
 *  @since: 1.0
 *	@created: 21/09/14
 */
function render_map($el) {

	// var
	var $markers = jQuery($el).find('.marker');

	// vars
	var map_zomming_fact = 15;
	var isDraggable = jQuery(document).width() > 480 ? true : false;

	var args = {
		zoom: map_zomming_fact,
		scrollwheel: false,
		draggable: isDraggable,
		center: new google.maps.LatLng(0, 0),
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};

	// create map
	map = new google.maps.Map($el[0], args);

	// add a markers reference
	map.markers = [];

	// add markers
	$markers.each(function() {
		add_marker(jQuery(this), map);
	});

	// cluster map
	cluster_map(map, markers);

	// center map
	center_map(map);
}

/*
 *  add_marker
 *
 *  @description: This function will add a marker to the selected Google Map
 *  @since: 1.0
 *	@created: 21/09/14
 */
function add_marker($marker, map) {

	// var
	var latlng = new google.maps.LatLng($marker.attr('data-lat'), $marker.attr('data-lng'));
	
	// create marker
	var marker = new google.maps.Marker({
		position: latlng,
		map: map,
		title: $marker.attr('data-address'),
	});

	// add to array
	map.markers.push(marker);

	markers = map.markers;

	// if marker contains HTML, add it to an infoWindow
	if ($marker.html()) {
		
		google.maps.event.addListener(marker, 'click', function() {
		
			if(infowindow) { infowindow.close(); }
			
			// create info window
			infowindow = new google.maps.InfoWindow({
				content: $marker.html()
			});

			// Center map when marker is clicked
			map.setCenter(marker.getPosition());
			// smoothZoom(map, map.getZoom(), map.getZoom());

			// show info window when marker is clicked
			infowindow.open(map, marker);
		});
	}
}

/*
 *  cluster_map
 *
 *  @description: This function will cluster markers to the selected Google Map
 *  @since: 1.0
 *	@created: 21/09/14
 */
function cluster_map(map, markers){
	var markerCluster = new MarkerClusterer(map, markers, {
		averageCenter: true
	});
}

/*
 *  center_map
 *
 *  @description: This function will center the map, showing all markers attached to this map
 *  @since	1.0
 *	@created: 21/09/14
 */
function center_map(map) {

	// vars
	var bounds = new google.maps.LatLngBounds();

	// loop through all markers and create bounds
	jQuery.each(map.markers, function(i, marker) {
		var latlng = new google.maps.LatLng(marker.position.lat(), marker.position.lng());
		bounds.extend(latlng);
	});

	// only 1 marker?
	if (map.markers.length == 1) {
		// set center of map
		map.setCenter(bounds.getCenter());
		map.setZoom(16);
	} else {
		// fit to bounds
		map.fitBounds(bounds);
	}
}

/*
 *  newgooglemap_initialize
 *
 *  @description: This function will get the locations category selected by users on Google Maps
 *  @since: 1.0
 *	@created: 21/09/14
 */
function newgooglemap_initialize(map, posttype) {
	var post_type = '';
	var categoryname = '';
	var classname;
	var checkbox_id = jQuery(map).attr('id');
	var totalcounts = 0;
	var map_canvas_style = jQuery('#map_canvas').attr('style');

	jQuery('#map_loading_div').show();
	if (posttype != '') {
		jQuery("input[name='posttype[]']").each(function() { // post type loop
			if (jQuery(this).attr('value') == posttype) {
				jQuery('#' + jQuery(this).attr('id')).prop('checked', true);
			}
		}); // finish post type loop
	}

	jQuery("input[name='posttype[]']").each(function() { // post type loop
		
		classname = jQuery(this).attr('id');
		categoryname = '';
		id_name = jQuery(this).attr('data-category');

		if (checkbox_id == classname) {
			if (!jQuery(this).attr('checked')) {
				jQuery('.' + classname).find(':checkbox').attr('checked', jQuery('#' + classname).is(":checked"));
			} else {
				jQuery('.' + classname).find(':checkbox').attr('checked', jQuery('#' + classname).is(":checked"));
			}
		}
		if (jQuery(this).attr('checked')) {
			post_type = jQuery(this).val();
			jQuery("div#" + id_name + " input[name='categoryname[]']").each(function() { // post type category loop
				if (jQuery(this).attr('checked')) {
					categoryname += jQuery(this).val() + ','; // finish the ajax
				}
			}); // finish post type category loop

			jQuery.ajax({
				url: ajaxUrl,
				type: 'POST',
				async: true,
				data: 'action=googlemap_initialize&posttype=' + post_type + '&category=' + categoryname + '&style=' + map_canvas_style,
				success: function(results) {
					jQuery('#map_loading_div').hide();
					
					jQuery('.acf-map').remove();
					jQuery('#maps_found').remove();
					
					jQuery('.iprelative').prepend(results);

					if (is_fullscreen){
						jQuery('.acf-map').addClass('map-fullscreen');
					}
					jQuery('.acf-map').each(function() {
						render_map(jQuery(this));
					});
					totalcounts = jQuery('#maps_found').val();
					display_marker_nofound(totalcounts);
				}
			});
		}
	}); // finish post type loop	
	if (categoryname == '') {
		jQuery('#map_loading_div').hide();
	}
}

/*
 *  display_marker_nofound
 *
 *  @description: This function will get no locations found message on Google Maps
 *  @since: 1.0
 *	@created: 21/09/14
 */
function display_marker_nofound(totalcounts) {
    if (totalcounts <= 0) {
        jQuery('#map_marker_nofound').show();
    } else {
        jQuery('#map_marker_nofound').hide();
    }
}

/*
 *  map_category_toggle
 *
 *  @description: This function will add toggle effect for filter category
 *  @since: 1.0
 *	@created: 21/09/14
 */
function map_category_toggle(id, str) {
    var div1 = document.getElementById(id);
    var toggal = document.getElementById(str.id);
    var sw = jQuery(window).width();
    if (sw <= 480) {
        if (div1.style.display == 'none') {
            jQuery(div1).show();
            jQuery('#' + str.id).removeClass('toggleon');
            toggal.setAttribute('class', 'toggleoff toggle_listing');
        } else if (div1.style.display == 'block') {
            jQuery(div1).hide();
            jQuery('#' + str.id).removeClass('toggleoff');
            toggal.setAttribute('class', 'toggleon toggle_listing');
        } else {
            jQuery(div1).show();
            jQuery('#' + str.id).removeClass('toggleon');
            toggal.setAttribute('class', 'toggleoff toggle_listing');
        }
    } else {
        if (div1.style.display == 'none') {
            jQuery(div1).show();
            jQuery('#' + str.id).removeClass('toggleoff');
            toggal.setAttribute('class', 'toggleon toggle_listing');
        } else {
            jQuery(div1).hide();
            jQuery('#' + str.id).removeClass('toggleon');
            toggal.setAttribute('class', 'toggleoff toggle_listing');
        }
    }
}

/*
 *  toggle_listing
 *
 *  @description: This function will add toggle effect for filter listing
 *  @since: 1.0
 *	@created: 21/09/14
 */
function toggle_listing() {
	var e = document.getElementById("toggle_postID");
	if (e.style.display == "none") {
		e.style.display = "block";
	} else {
		e.style.display = "none";
	}
	if (jQuery('#toggle_listing').hasClass('toggleoff')){
		jQuery("#toggle_listing").removeClass("paf_row toggleoff").addClass("paf_row toggleon");
		jQuery('#ajaxform').removeClass('filter_toggle_off');
	} else {
		jQuery("#toggle_listing").removeClass("paf_row toggleon").addClass("paf_row toggleoff");
		jQuery('#ajaxform').addClass('filter_toggle_off');
	}
}

/*
 *  showFullscreen
 *
 *  @description: This function will allow users to have a fullscreen Google Map
 *  @since: 1.0
 *	@created: 21/09/14
 */
function showFullscreen() {
	// window.alert('DIV clicked');
	jQuery('#map_sidebar').toggleClass('map-fullscreen');
	jQuery('#map_canvas').toggleClass('map-fullscreen');
	jQuery('.map_category').toggleClass('map_category_fullscreen');
	jQuery('.map_post_type').toggleClass('map_category_fullscreen');
	jQuery('#toggle_listing').toggleClass('map_category_fullscreen');
	jQuery('#trigger').toggleClass('map_category_fullscreen');
	jQuery('body').toggleClass('body_fullscreen');
	jQuery('#loading_div').toggleClass('loading_div_fullscreen');
	jQuery('#triggermap').toggleClass('triggermap_fullscreen');
	jQuery('.TopLeft').toggleClass('TopLeft_fullscreen');
	jQuery('#ajaxform').toggleClass('filter_fullscreen');
	
	if (is_fullscreen == 1){
		is_fullscreen = 0;
	} else {
		is_fullscreen = 1;
	}

	window.setTimeout(function() {
		var center = map.getCenter();
		google.maps.event.trigger(map, 'resize');
		map.setCenter(center);
	}, 100);
}

/*
 *  filter_display
 *
 *  @description: This function will show hide filter option
 *  @since: 1.0
 *	@created: 21/09/14
 */
function filter_display(){
	if ( jQuery('.pe_advsearch_form .filter_fullscreen').length > 0 ){
		show = 1;
	}
	
	if (show){
		jQuery('#ajaxform').show();
	} else {
		jQuery('#ajaxform').hide();
	}
}

(function($) {
	/*
	 *  document ready
	 *
	 *  @description: This function will render each map when the document is ready (page has loaded)
	 *  @since: 1.0
	 *	@created: 21/09/14
	 */

	$(document).ready(function() {

		$('.acf-map').each(function() {

			render_map($(this));

			$form = $('.ajaxform').html();
			$('.ajaxform').remove();
			$($form).insertAfter($('.iprelative'));

			$map_width = $('.iprelative').width();
			$map_height = $('.iprelative').height();

			if ($map_width < 480 && $map_height < 240){
				filter_display();
			}

			var maxMap = $('#triggermap');
			$(maxMap).click(function(){
				showFullscreen();
			});

		});

	});
})(jQuery);