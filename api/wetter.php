<?php
/* 
file_get_contents is not caught in try-catch, write own error handler
http://stackoverflow.com/a/3406181/1781026 
*/
set_error_handler(
    create_function(
        '$severity, $message, $file, $line',
        'throw new ErrorException($message, $severity, $severity, $file, $line);'
    )
);

$wetter = array();
$request = array();
function getWetterKlimatologie() {
	try{
		$request['temp'] = file_get_contents('https://www.uni-muenster.de/Klima/data/0001tdhg_de.txt');
		if($http_response_header[0] != "HTTP/1.1 200 OK") $request['temp'] = false;
		$request['code'] = file_get_contents('https://www.uni-muenster.de/Klima/data/0007cdhg_de.txt');
		if($http_response_header[0] != "HTTP/1.1 200 OK") $request['code'] = false;

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
restore_error_handler();