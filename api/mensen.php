<?php
include('functions.php');
$daysGerman = array('Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag');

$mensajson = getMensaplan();
$mensaarr = json_decode($mensajson, true);
//print_r($mensaarr);
//echo "<hr>";
$mensasorted = array();
foreach($mensaarr['results']['bindings'] as $food) {
	// reduce dates to date/month/year, stripping time
	$dateval = strtotime( $food['start']['value'] );
	
	$foodday = date('d.m.Y', $dateval);
	if(!isset($mensasorted[ date('d.m.Y', $dateval) ])) {
		$mensasorted[ $foodday ] = array();
		$mensasorted[ $foodday ]["meta"]["date"] = $foodday;
		$mensasorted[ $foodday ]["meta"]["dayOfWeek"] = date('w', $dateval);
		$mensasorted[ $foodday ]["meta"]["dayOfWeekNameGer"] = $daysGerman[date('w', $dateval)-1];
	}

	$foodarray = array('name' => $food['name']['value'],
		'minPrice' => $food['minPrice']['value'], 
		'maxPrice' => $food['maxPrice']['value']);
	$mensasorted[ $foodday ]["fooddata"][ $food['mensa']['value'] ]['food'][] = $foodarray;

	$mensasorted[ $foodday ]["fooddata"][ $food['mensa']['value'] ]['mensa']['name'] = $food['mensaname']['value'];
	$mensasorted[ $foodday ]["fooddata"][ $food['mensa']['value'] ]['mensa']['uri'] = basename($food['mensa']['value']);
}

echo json_encode($mensasorted);