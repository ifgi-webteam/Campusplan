<?php
include("functions.php");

$wetter = array();
$request = array();

// weather data by Klimatologie Institute
function getWetterKlimatologie() {
	try{
		// request weather from Wetterstation Klimatologie Münster
		// cache expiry 1 hour
		$request['temp'] = file_get_contents_cached('https://www.uni-muenster.de/Klima/data/0001tdhg_de.txt', 3600);
		$request['code'] = file_get_contents_cached('https://www.uni-muenster.de/Klima/data/0007cdhg_de.txt', 3600);
		$request['description'] = file_get_contents_cached('https://www.uni-muenster.de/Klima/data/0007cdhg_de_txt.txt', 3600);

		if(!in_array(false, $request)) {
			$wetter['temp'] = floatval($request['temp']);
			$wetter['code'] = intval($request['code']);
			$wetter['description'] = htmlentities($request['description']);
			return json_encode($wetter);
		}
		return false;
	} catch(Exception $e) {
		return false;
	}
}

// Forecast.io free API
function getWetterForecastIO() {
	try {
		$request = file_get_contents_cached('https://api.forecast.io/forecast/05f2b397daaa4a4c5428a7888ce043b7/51.96246,7.62558?units=si&exclude=alerts,flags&lang=de', 1800);
		return $request;
	}  catch(Exception $e) {
		return false;
	}
}

//echo getWetterKlimatologie();
echo getWetterForecastIO();