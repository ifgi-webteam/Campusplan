<?php
// generate a list of all organizations in text form

include("functions.php");

$ids = array();
$letters = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
foreach($letters as $letter) {
	$results = searchByLetter(strtoupper($letter));
	$results_json = json_decode($results,true);
	foreach($results_json["results"]["bindings"] as $point) {
		$url_parts = explode('context/', $point["orga"]["value"]);
		$url = (isset($url_parts[1])) ? $url_parts[1] :  "";
		if(!in_array($url, $ids)) array_push($ids, $url);
	}
}

foreach ($ids as $val) {
	echo "http://app.uni-muenster.de/Organisation/".$val.'<br>';
}

