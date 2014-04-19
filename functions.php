<?php
function sparql_get($query){
	$url = 'http://data.uni-muenster.de/sparql?query='.urlencode($query).'&format=json';
	$opts = array(
		'http'=>array(
			'header' => "Accept: application/sparql-results+json\r\n",
			'timeout' => 10
		)
	);
	$context = stream_context_create($opts);
	$response = file_get_contents($url, false, $context);
	return $response;
}
