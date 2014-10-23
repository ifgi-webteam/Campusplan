<?php
include("functions.php");
$mapjson = json_decode(getMapGeometries(), true);

$points = array();
foreach($mapjson["results"]["bindings"] as $point) {
	if(isset($point["lat"]) && $point["long"]) {
		$points[] = array("lat" => $point["lat"]["value"],
			"lng" => $point["long"]["value"],
			"message" => $point["name"]["value"]
			);
	}
}
echo json_encode($points, JSON_NUMERIC_CHECK);