<?php
// query mensa data and aggregate by day

include('functions.php');
$daysGerman = array('Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag');

try {
	$angjs_data = file_get_contents('php://input');
	$angjs_data_decoded = json_decode($angjs_data);
	if(!empty($angjs_data_decoded)) {
		$searchterm = $angjs_data_decoded->data;
		$mensajson = getMensaplan2($searchterm);
	} else {
		$mensajson = getMensaplan2();
	}

	$mensaarr = json_decode($mensajson, true);
	$mensasorted = array();

	if($mensajson) {
		foreach($mensaarr as $food) {
			// reduce dates to date/month/year, stripping time
			$dateval = strtotime( $food['data']['date'] );

			$foodday = date('N', $dateval);
			if(!isset($mensasorted[ $foodday ])) {
				$mensasorted[ $foodday ] = array();
				$mensasorted[ $foodday ]["meta"]["date"] = date('Y-m-d', $dateval);
				$mensasorted[ $foodday ]["meta"]["dayOfWeek"] = date('w', $dateval);
				$mensasorted[ $foodday ]["meta"]["dayOfWeekNameGer"] = $daysGerman[date('w', $dateval)-1];
			}

			$foodarray = array('name' => $food['data']['name'],
				'minPrice' => $food['data']['minPrice'], 
				'maxPrice' => $food['data']['maxPrice']);
			$mensasorted[ $foodday ]["fooddata"][ $food['data']['mensa']['uid'] ]['food'][] = $foodarray;

			$mensasorted[ $foodday ]["fooddata"][ $food['data']['mensa']['uid'] ]['mensa']['name'] = $food['data']['mensa']['name'];
			$mensasorted[ $foodday ]["fooddata"][ $food['data']['mensa']['uid'] ]['mensa']['uri'] = $food['data']['mensa']['uid'];
		}

		echo json_encode($mensasorted);
	} else {
		echo json_encode(array());
	}

} catch(Exception $e) {
	echo json_encode(array());
}