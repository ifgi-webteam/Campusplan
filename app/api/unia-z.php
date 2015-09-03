<?php
include('functions.php');

// decode AngularJS data parameter
$angjs_data = file_get_contents('php://input');
$angjs_data_decoded = json_decode($angjs_data);
$searchterm = $angjs_data_decoded->data;

// perform a letter search if query is only one letter
if(preg_match('/^[a-zA-Z]$/', $searchterm)) {
	echo searchByLetter(strtoupper($searchterm));
} else {
	echo searchByWord($searchterm);
}
?>