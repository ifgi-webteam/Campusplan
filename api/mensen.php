<?php
include('functions.php');
$daysGerman = array('Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag');

$angjs_data = file_get_contents('php://input');
$angjs_data_decoded = json_decode($angjs_data);
if(!empty($angjs_data_decoded)) {
	$searchterm = $angjs_data_decoded->data;
	$mensajson = getMensaplan($searchterm);
} else {
	$mensajson = getMensaplan();
}

$mensaarr = json_decode($mensajson, true);
$mensasorted = array();
foreach($mensaarr['results']['bindings'] as $food) {
	// reduce dates to date/month/year, stripping time
	$dateval = strtotime( $food['start']['value'] );

	$foodday = date('N', $dateval);
	if(!isset($mensasorted[ $foodday ])) {
		$mensasorted[ $foodday ] = array();
		$mensasorted[ $foodday ]["meta"]["date"] = date('Y-m-d', $dateval);
		$mensasorted[ $foodday ]["meta"]["dayOfWeek"] = date('w', $dateval);
		$mensasorted[ $foodday ]["meta"]["dayOfWeekNameGer"] = $daysGerman[date('w', $dateval)-1];
	}

	$foodarray = array('name' => $food['name']['value'],
		'minPrice' => $food['minPrice']['value'], 
		'maxPrice' => $food['maxPrice']['value']);
	$mensasorted[ $foodday ]["fooddata"][ $food['mensa']['value'] ]['food'][] = $foodarray;

	$mensasorted[ $foodday ]["fooddata"][ $food['mensa']['value'] ]['mensa']['name'] = $food['mensaname']['value'];
	$mensasorted[ $foodday ]["fooddata"][ $food['mensa']['value'] ]['mensa']['uri'] = $food['mensa']['value'];
}

echo json_encode($mensasorted);
