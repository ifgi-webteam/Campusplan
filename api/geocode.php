<?php
$angjs_data = file_get_contents('php://input');
$angjs_data_decoded = json_decode($angjs_data);
if(!empty($angjs_data_decoded)) {
	$searchterm = $angjs_data_decoded->data;
	echo file_get_contents_cached("http://open.mapquestapi.com/geocoding/v1/address?key=Fmjtd%7Cluu829682d%2C85%3Do5-9w1lg0&inFormat=kvp&outFormat=json&location=".urlencode($searchterm)."&boundingBox=52.0349,7.3972,51.8892,7.8366&maxResults=1");
} else {
	echo "[]";
}