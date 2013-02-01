<?php 
	
	// returns a JSON object containing the route itself (to put on the map), plus the instructions
	// sample call: route.php?coords=47.25976,9.58423,47.2603,9.588&mode=car&lang=de

	include_once('keys.php');

	$url = 'http://routes.cloudmade.com/'.$cloudmadekey.'/api/0.3/'.$_GET["coords"].'/'.$_GET["mode"].'.js?lang='.$_GET["lang"];

	// we'll cache the requests, because users will quite likely switch back and forth between the modes
	// so we can server the second same request from our cache
	$file = 'routes/'.$_GET['coords'].'-'.$_GET['mode'].'-'.$_GET['lang'].'.json';
    $route = null;
    $tries = 0;
    if (!is_file($file) || filemtime($file) < time()-(86400*30)) {
		
		$route = file_get_contents($url);
 
		if ($route) {
			$fp = fopen($file, "w");
			fwrite($fp, $route);
			fclose($fp);
		}		
		
	} else {
		$route = file_get_contents($file);
	}

	header('Content-type: application/json; charset=utf-8');
	echo file_get_contents($url);
	
?>