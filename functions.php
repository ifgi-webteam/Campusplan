<?php 

// This file contains some generic functions used across most pages of the campus plan app. 

// function to keep client from caching the page; required e.g. for the favorites page, 
// which needs to be re-rendedered each time it is visited
function dontCache(){
	header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
	header('Pragma: no-cache'); // HTTP 1.0.
	header('Expires: 0'); // Proxies.
}

// for the generated pages that should be cached on the server side, 
// this function checks if a cached version is already there and less than 24 hourse old.
// if so, the cache is served; otherwise, the page is generated normally (and cached) 
function checkCache(){
	$cache_time = 24*60*60; // Time in seconds to keep a page cached - one day in this case 
	$cache_filename = 'cache/'.md5($_SERVER['REQUEST_URI']); // Location to lookup cached file 
	
   	//Check to see if this file has already been cached  
   	// If it has get and store the file creation time  
   	$cache_created = (file_exists($cache_filename)) ? filemtime($cache_filename) : 0;  
     
   	if ((time() - $cache_created) < $cache_time) {  
    	readfile($cache_filename); // The cached copy is still valid, read it into the output buffer  
    	die();  
   	}  

   	// else generate a 'fresh' version of the page that will be cached at the end: 
   	ob_start();
}

function flushCache(){
	$cache_filename = 'cache/'.md5($_SERVER['REQUEST_URI']); // Location to lookup cached file 
	// create new cached page if no cache is there yet or it is expired:
	file_put_contents($cache_filename, ob_get_contents());  
	ob_end_flush();  
}

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

function getHead($showmenu = true){	
?>

<!DOCTYPE html> 
<html>
	<head>
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no"> 
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
	<link rel="icon" href="favicon.png" type="image/png">

	<title>Campusplan</title> 

	<link rel="apple-touch-icon" href="img/start.png"/>

	<link rel="stylesheet" href="css/leaflet.css?v=<?= md5("css/leaflet.css") ?>" />
	<!--[if lte IE 8]>
		<link rel="stylesheet" href="css/leaflet.ie.css?v=<?= md5("css/leaflet.ie.css") ?>" />
	<![endif]-->
	<link href="css/campusplan.css?v=<?= md5("css/campusplan.css") ?>" rel="stylesheet" media="screen">
    <link href="css/campusplan-responsive.css?v=<?= md5("css/campusplan-responsive.css") ?>" rel="stylesheet" media="screen">

    <script src="js/jquery.min.js?v=<?= md5("js/jquery.min.js") ?>"></script>
    <script src="js/jquery.cookie.js?v=<?= md5("js/jquery.cookie.js"); ?>"></script>
    <script src="js/bootstrap.min.js?v=<?= md5("js/bootstrap.min.js"); ?>"></script>
    <script src="js/leaflet.js?v=<?= md5("js/leaflet.js"); ?>"></script>
	<script src="js/campusplan.js?v=<?= md5("js/campusplan.js"); ?>"></script>
	
</head> 

<body> 
	<div id="content"> 

		<div <?php if($showmenu){ ?>class="content"<?php } ?>>

		<?php if($showmenu){ ?>
			<div class="navbar navbar-inverse navbar-fixed-top visible-phone"><!-- phone displays -->
				<div class="navbar-inner">
				    <a class="brand internal" href="index.php"><img src="img/navigation_up.png" class="navbarlogo" style="margin-right:0px;"><img src="img/wwu-white-s.png" class="navbarlogo"> CampusPlan</a>
				    </div>
				</div>
			</div>
			<div class="navbar navbar-inverse navbar-fixed-bottom hidden-phone"><!-- tablets & desktop -->
				<a class="internal" href="uni-a-z.php"><img src="img/front-az.png" class="menuimgs" /></a>
				<a class="internal" href="mensen.php"><img src="img/front-mensen.png" class="menuimgs" /></a>
				<a class="internal" href="ulb.php"><img src="img/front-ulb.png" class="menuimgs" /></a>
				<a class="internal" href="karte.php"><img src="img/front-karte.png" class="menuimgs" /></a>
				<a class="internal" href="favoriten.php"><img src="img/front-favoriten.png" class="menuimgs" /></a>
				<a class="internal" href="fachbereiche.php"><img src="img/front-fachbereiche.png" class="menuimgs" /></a>
				<a class="internal" href="hoersaele.php"><img src="img/front-hoersaele.png" class="menuimgs" /></a>
				<a class="internal" href="wohnheime.php"><img src="img/front-wohnheime.png" class="menuimgs" /></a>
				<a class="internal" href="info.php"><img src="img/front-info.png" class="menuimgs" /></a>  	
			</div>
		<?php	} //end if showMenu ?>
	
	<div id="map"></div>

<?php } // end getHead



function getFoot($showlogos = false){
?>	
	
	<?php if($showlogos){ ?>
		<div class="container hidden-phone"><a href="http://www.uni-muenster.de"><img src="img/wwu-full.png" class="logoimgs pull-right" /></a>
			<a href="http://ifgi.uni-muenster.de"><img src="img/ifgi.png" class="logoimgs pull-right" /></a></div>
	<?php } ?>
		<div id="loading"><img src="img/loader-big.gif" width="64" height="64" alt="loading..." /></div>
	</div> <!-- class=content -->
	<?php include_once('piwik.php'); // contains the piwik tracking code - not on GitHub! ?>
</body>
</html>

<?php	
}



function endsWith($haystack, $needle) {
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

?>