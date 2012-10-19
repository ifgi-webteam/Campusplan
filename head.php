<?php 
	require_once("functions.php");
?>
<!DOCTYPE html> 
<html>
	<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<meta name="apple-mobile-web-app-capable" content="yes" />

	<title>WWU Campus Plan</title> 
	
	<?php 
	
	if(isset($leaftletCSS)){
		if($leaftletCSS){
			echo'<link rel="stylesheet" href="css/leaflet.css" />
			<!--[if lte IE 8]>
			    <link rel="stylesheet" href="css/leaflet.ie.css" />
			<![endif]-->';		
		}
	}
	
	?>

	<link rel="stylesheet"  href="css/themes/default/jquery.mobile.css" /> 
	<link rel="stylesheet" href="css/campusplan.css"/>
	
	<link rel="apple-touch-icon" href="images/front-az.png"/>

	<script src="js/jquery.js"></script>
	<script src="docs/_assets/js/jqm-docs.js"></script>
	<script src="js/"></script>
</head> 