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





// returns the menu, with the active item highlighted:

function getMenu($activeItem){
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
	echo '<div data-role="header" data-theme="a">
		<h1>'.$title.'</h1>
		';
		
	if($leftbutton === "back"){
		echo'		<a href"" data-rel="back" data-icon="back" data-iconpos="notext" data-direction="reverse">Home</a>
		';
	}else if($leftbutton === "home"){
		echo'		<a href="index.php" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a>
		';
	}else{ // none
	     
	}
	
	echo'	<a href="menu-dialog.php" id="menu-button" data-icon="grid" data-iconpos="notext" data-rel="dialog" data-transition="fade">Menü</a>
	</div><!-- /header -->';
}
?>