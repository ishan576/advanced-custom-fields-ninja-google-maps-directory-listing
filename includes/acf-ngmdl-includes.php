<?php
/* Features */

// all import custom function 
// USAGE : [get_lat_lng({address[1]})]
// - {address[1]} is your full address field
if (!function_exists('get_lat_lng')){
	function get_lat_lng($address){
		$address = urlencode($address);
		$url = "http://maps.google.com/maps/api/geocode/json?address=".$address."&sensor=false";
		$response = file_get_contents($url);
		$response = json_decode($response, true);

		$f_address = $response['results'][0]['formatted_address'];
		$lat = $response['results'][0]['geometry']['location']['lat'];
		$long = $response['results'][0]['geometry']['location']['lng'];

		$add_array = array(
			'address' => '85 Lynden Rd, Brantford ON N3R 7J9',
			'lat' => sprintf('%s', $lat),
			'lng' => sprintf('%s', $long)
		);

		return serialize($add_array);
	}
}
?>