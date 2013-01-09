<?php 

// This file contains some generic functions used across most pages of the campus plan app. 

// returns the results of the query as a PHP object
function sparql_get($query){
 
   // init cURL-Handle
   $ch = curl_init();
   
   $url = 'http://data.uni-muenster.de/sparql?'.'query='.urlencode($query).'&format=json';
   	
   // set URL and headers
   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_HEADER, false); //  header output OFF
   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/sparql-results+json'));
   
   // return response, don't print/echo
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
   $response = curl_exec($ch);
   // done...
   curl_close($ch);
   $resobj = json_decode($response);  
   
   return $resobj;
}

function getHead($title = 'WWU CampusPlan'){
?>

<!DOCTYPE html> 
<html>
	<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<meta name="apple-mobile-web-app-capable" content="yes" />

	<title><?php echo $title ?></title> 

	<link rel="apple-touch-icon" href="img/start.png"/>
	
	<link rel="stylesheet" href="css/leaflet.css" />
	<!--[if lte IE 8]>
		<link rel="stylesheet" href="css/leaflet.ie.css" />
	<![endif]-->
	<link href="css/campusplan.css" rel="stylesheet" media="screen">
    <link href="css/responsive.css" rel="stylesheet" media="screen">

    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.cookie.js"></script>  
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.scrollto.min.js"></script>
    <script src="js/jquery.history.js"></script>
    <script src="js/ajaxify-html5.js"></script>
    <script src="js/leaflet.js"></script>  
	<script src="js/campusplan.js"></script>  
	
</head> 

<body> 

	<div id="content">

		<div class="navbar navbar-inverse navbar-fixed-top visible-phone"><!-- phone displays -->
			  <div class="navbar-inner">
			    <div class="container-fluid">          
			      <a class="brand" href="index.php"><img src="img/wwu-white-s.png" class="navbarlogo"> WWU CampusPlan</a>          
			    </div>
			  </div>
			</div><div class="navbar navbar-inverse navbar-fixed-top hidden-phone"><!-- tablets & desktop -->
			  			<a href="uni-a-z.php"><img src="img/front-az.png" class="menuimgs" /></a><a href="mensen.php"><img src="img/front-mensen.png" class="menuimgs" /></a><a href="ulb.php"><img src="img/front-ulb.png" class="menuimgs" /></a><a href="karte.php"><img src="img/front-karte.png" class="menuimgs" /></a><a href="favoriten.php"><img src="img/front-favoriten.png" class="menuimgs" /></a><a href="fachbereiche.php"><img src="img/front-fachbereiche.png" class="menuimgs" /></a><a href="hoersaele.php"><img src="img/front-hoersaele.png" class="menuimgs" /></a><a href="wohnheime.php"><img src="img/front-wohnheime.png" class="menuimgs" /></a><a href="info.php"><img src="img/front-info.png" class="menuimgs" /></a>  	
			</div>

<?php	
}



function getFoot(){
?>	
	</div>
	<div class="container hidden-phone"><a href="http://www.uni-muenster.de"><img src="img/wwu-full.png" class="logoimgs pull-right" /></a>
			<a href="http://ifgi.uni-muenster.de"><img src="img/ifgi.png" class="logoimgs pull-right" /></a></div>
</body>
</html>

<?php	
}
?>