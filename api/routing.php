<?php
include "functions.php";
sleep(5);
$angjs_data = file_get_contents('php://input');
$angjs_data_decoded = json_decode($angjs_data);
if(!empty($angjs_data_decoded)) {
	$type = $angjs_data_decoded->data;
	//print_r($type);
	echo file_get_contents_cached("http://open.mapquestapi.com/directions/v2/route?key=Fmjtd%7Cluu829682d%2C85%3Do5-9w1lg0&outFormat=json&routeType=".$type."&timeType=1&enhancedNarrative=false&shapeFormat=raw&generalize=0&locale=de_DE&unit=k&from=51.95969559,7.60106368&to=51.96275228,7.62267759&narrativeType=text");
} else {
	echo "[]";
}