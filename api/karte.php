<?php
// query all organizations which have lat/lon attributes
// aggregate them by lat/lon, so multiple organizations in the same
// building won't display as different points

include("functions.php");
$mapjson = json_decode(getMapGeometriesPts(), true);

$points = array();
foreach($mapjson["results"]["bindings"] as $point) {
	if(isset($point["lat"]) && $point["lon"]) {
		$url_parts = explode('context/', $point["organization"]["value"]);
		$url = (isset($url_parts[1])) ? $url_parts[1] :  "";

		//$building_uri = $point["building"]["value"];
		$building_uri = md5($point["lat"]["value"].$point["lon"]["value"]);

		// combine orgas in the same building
		if(!isset($points[ $building_uri ])) {
			$points[ $building_uri ] = array(
				"lat" => $point["lat"]["value"],
				"lng" => $point["lon"]["value"],
				"message" => (isset($point["buildingname"])? "<b>".$point["buildingname"]["value"]."</b><br>" : "") . (isset($point["address"])? $point["address"]["value"]."<br>" : "") . '<ul><li><a href="Organisation/'. $url .'">'.$point["name"]["value"].'</a></li>'
				);
		} elseif(strpos($points[ $building_uri ]["message"], $url) !== false) {
			// nothing
		} else {
			$points[ $building_uri ] = array(
			  	"lat" => $point["lat"]["value"],
				"lng" => $point["lon"]["value"],
				"message" =>  $points[ $building_uri ]["message"]. '<li><a href="Organisation/'. $url .'">'.$point["name"]["value"].'</a></li>'
				);
		}
		
	}
}

header('Content-Type: application/json');
echo json_encode( array_values($points), JSON_NUMERIC_CHECK);