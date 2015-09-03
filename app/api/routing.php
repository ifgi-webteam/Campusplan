<?php
include "functions.php";
$angjs_data = file_get_contents('php://input');
$angjs_data_decoded = json_decode($angjs_data);
if(!empty($angjs_data_decoded)) {
	$type = $angjs_data_decoded->type;
	$toLat = $angjs_data_decoded->toLat;
	$toLng = $angjs_data_decoded->toLng;
	$fromLat = $angjs_data_decoded->fromLat;
	$fromLng = $angjs_data_decoded->fromLng;

	echo file_get_contents_cached(
		"http://open.mapquestapi.com/directions/v2/route?key=Fmjtd%7Cluu829682d%2C85%3Do5-9w1lg0".
		"&outFormat=json" .
		"&routeType=". $type .
		"&timeType=1" .
		"&enhancedNarrative=false" .
		"&shapeFormat=raw" .
		"&generalize=0" .
		"&locale=de_DE" .
		"&unit=k" .
		"&from=". $fromLat .",". $fromLng .
		"&to=". $toLat .",". $toLng .
		"&narrativeType=text"
		);
} else {
	echo "[]";
}