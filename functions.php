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


// returns the menu as image tiles, $yadda is ignored...
function getMenu(){
?>

<div class="content-secondary"><a href="uni-a-z.php"><img src="images/front-az.png" class="menuimg" /></a><a href="mensen.php"><img src="images/front-mensen.png" class="menuimg" /></a><a href="ulb.php"><img src="images/front-ulb.png" class="menuimg" /></a><a href="karte.php"><img src="images/front-karte.png" class="menuimg" /></a><a href="favoriten.php"><img src="images/front-favoriten.png" class="menuimg" /></a><a href="fachbereiche.php"><img src="images/front-fachbereiche.png" class="menuimg" /></a><a href="hoersaele.php"><img src="images/front-hoersaele.png" class="menuimg" /></a><a href="wohnheime.php"><img src="images/front-wohnheime.png" class="menuimg" /></a><a href="info.php"><img src="images/front-info.png" class="menuimg" /></a>
<br />

		<a href="http://www.uni-muenster.de"><img src="images/wwu-full.png" class="menu-logo" /></a>
		<a href="http://ifgi.uni-muenster.de"><img src="images/ifgi.png" class="menu-logo" /></a>

</div>
<?php
}

// returns the menu, with the active item highlighted:
function getOldMenu($activeItem){
	// make sure these arrays are all of the same lengths!
	// one of these should be in $activeItem
	$pages   = array( "uni-a-z.php",      
					 "fachbereiche.php", 
					 "mensen.php", 
					 "wohnheime.php", 
					 "karte.php",
					 "ulb.php",
					 "info.php" ); 
					 
	$titles = array( "Uni A-Z", 
					 "Fachbereiche", 
					 "Mensen", 
					 "Wohnheime", 
					 "Karte",
					 "ULB Katalog",
					 "Info" );

	// if the menu is shown in the menu popup page, don't 'class' it (otherwise it would be hidden):
	if($activeItem === "menu-dialog.php"){
		echo'<div>
				';		
	}else{
		echo'<div class="content-secondary">
				';		
	}
			
	echo'		<div data-theme="a" data-content-theme="b">
					<ul data-role="listview"  data-theme="b">
					';
	for ($i = 0; $i < count($pages); $i++) {
	    echo '<li';
	    if($activeItem === $pages[$i]){
	    	echo ' data-theme="g"'; // highlight the selected item
	    }
	    
	    
	    echo '><a href="'.$pages[$i].'"';
	//    if($pages[$i] === "karte.php" ){
	//    	echo ' data-ajax="false"'; // no ajax for the map, otherwise it doesn't show!
	//    }
	    echo '>'.$titles[$i].'</a></li>
	    ';
	}				
				
	echo'				</ul>
			</div>			
		</div>	';
}



// creates the header bar with title and an optional left button. options: home, back, none

function getHeader($title, $leftbutton = "home" ){

	echo '<div class="fixed-home"><a href="index.php" data-transition="fade"><img src="images/button-home.png" class="fixed-button"></a> <img src="images/favorite.png" class="fixed-button" id="star"></div>';

	// echo '<div data-role="header" data-theme="a">
	// 	<h1>'.$title.'</h1>
	// 	';
		
	// if($leftbutton === "back"){
	// 	echo'		<a href"" data-rel="back" data-icon="back" data-iconpos="notext" data-direction="reverse">Home</a>
	// 	';
	// }else if($leftbutton === "home"){
	// 	echo'		<a href="index.php" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
	// 	';
	// }else{ // none
	     
	// }
	
	// echo'	<a href="menu-dialog.php" id="menu-button" data-icon="grid" data-iconpos="notext" data-rel="dialog" data-transition="fade">Menü</a>
	// </div><!-- /header -->';
}


function getHead($title = 'WWU Campus Plan'){
?>

<!DOCTYPE html> 
<html>
	<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<meta name="apple-mobile-web-app-capable" content="yes" />

	<title><?php echo $title ?></title> 
	
	<link rel="stylesheet" href="css/leaflet.css" />
	<!--[if lte IE 8]>
		<link rel="stylesheet" href="css/leaflet.ie.css" />
	<![endif]-->

	<link rel="stylesheet" href="css/jquery.mobile-1.2.0.min.css"/>
	<link rel="stylesheet" href="css/campusplan.css"/>
	
	<link rel="apple-touch-icon" href="images/start.png"/>

	<script src='js/jquery-1.8.2.min.js'></script>
	<script src='js/jquery.mobile-1.2.0.min.js'></script>
	<script src="js/jquery.cookie.js"></script>
	<script src='js/leaflet.js'></script>
	<script>
	
	function hideAddressBar(){
  		if(document.documentElement.scrollHeight<window.outerHeight/window.devicePixelRatio){
    		document.documentElement.style.height=(window.outerHeight/window.devicePixelRatio)+'px';
  			setTimeout(window.scrollTo(1,1),0);
		}
		window.addEventListener("load",function(){hideAddressBar();});
		window.addEventListener("orientationchange",function(){hideAddressBar();});
	}

	// enable "starring" pages (adding to cookies)
	jQuery('[data-role="page"]').live('pageinit', function(){
    	$('img#star').unbind();
    	$('img#star').click(function(){
			var cookie = 'bookmark-'+$('title').html();
			var value = $(location).attr('href');

			// toggle cookie and star image
			if($.removeCookie(cookie)){
				// "unstar" page
				$("#star").attr("src","images/favorite.png");
				console.log('cookie entfernt');
			}else{
				$.cookie(cookie, value, { expires: 1000 });
				$("#star").attr("src","images/favorite-active.png");
				console.log('cookie hinzugefügt');
			}			
		});
	});
		
</script>
	
</head> 

<?php	
}



function getFoot(){
?>	
</body>
</html>

<?php	
}
?>