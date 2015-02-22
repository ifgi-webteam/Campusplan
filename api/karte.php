<?php
include("functions.php");
$mapjson = json_decode(getMapGeometries(), true);

$points = array();
foreach($mapjson["results"]["bindings"] as $point) {
	if(isset($point["lat"]) && $point["lon"]) {
		$points[] = array("lat" => $point["lat"]["value"],
			"lng" => $point["lon"]["value"],
			"message" => $point["name"]["value"]."<br>".$point["streetaddress"]["value"]);
	}
}
echo json_encode($points, JSON_NUMERIC_CHECK);