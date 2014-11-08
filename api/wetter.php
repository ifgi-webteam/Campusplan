<?php
include("functions.php");

$wetter = array();
$request = array();
function getWetterKlimatologie() {
	try{
		$request['temp'] = file_get_contents_cached('https://www.uni-muenster.de/Klima/data/0001tdhg_de.txt');
		$request['code'] = file_get_contents_cached('https://www.uni-muenster.de/Klima/data/0007cdhg_de.txt');

		if(!in_array(false, $request)) {
			$wetter['temp'] = floatval($request['temp']);
			$wetter['code'] = intval($request['code']);
			return json_encode($wetter);
		}
		return false;
	} catch(Exception $e) {
		return false;
	}
}

echo getWetterKlimatologie();