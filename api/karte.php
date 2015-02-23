<?php
include("functions.php");
$mapjson = json_decode(getMapGeometriesPts(), true);

$points = array();
foreach($mapjson["results"]["bindings"] as $point) {
	if(isset($point["lat"]) && $point["lon"]) {
		$url_parts = explode('context/', $point["organization"]["value"]);
		$url = (isset($url_parts[1])) ? $url_parts[1] :  "";

		$building_uri = $point["building"]["value"];

		// combine orgas in the same building
		if(!isset($points[ $building_uri ]) || strpos($points[ $building_uri ]["message"], $url)) {
			$points[ $building_uri ] = array(
				"lat" => $point["lat"]["value"],
				"lng" => $point["lon"]["value"],
				"message" => (isset($point["address"])? $point["address"]["value"]."<br>" : "") . '&bullet;&nbsp;<b><a href="Organisation/'. $url .'">'.$point["name"]["value"].'</a></b>'
				);
		} else {
			$points[ $building_uri ] = array(
			  	"lat" => $point["lat"]["value"],
				"lng" => $point["lon"]["value"],
				"message" =>  $points[ $building_uri ]["message"]. '<br>&bullet;&nbsp;<b><a href="Organisation/'. $url .'">'.$point["name"]["value"].'</a></b>'
				);
		}

		
	}
}
echo json_encode( array_values($points), JSON_NUMERIC_CHECK);