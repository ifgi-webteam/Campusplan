<?php 
	
	// returns a JSON object containing the route itself (to put on the map), plus the instructions
	// sample call: route.php?coords=47.25976,9.58423,47.2603,9.588&mode=car&lang=de

	include_once('keys.php');

	$url = 'http://routes.cloudmade.com/'.$cloudmadekey.'/api/0.3/'.$_GET["coords"].'/'.$_GET["mode"].'.js?lang='.$_GET["lang"];

	header('Content-type: application/json; charset=utf-8');
	echo file_get_contents($url);
	
?>