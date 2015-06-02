<?php
include('functions.php');
// decode AngularJS data parameter
$angjs_data = file_get_contents('php://input');
$angjs_data_decoded = json_decode($angjs_data);
if(!empty($angjs_data_decoded)) {
	$searchterm = $angjs_data_decoded->data;
	echo listSubSorganizations($searchterm);
} else {
	echo "";
}