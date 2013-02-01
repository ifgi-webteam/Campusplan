<?php 
	require_once("functions.php");
	dontCache();
	getHead();
?>

<body> 

<div class="container">
	<div class="row-fluid">
		<?php getFavorites(); ?>			
	</div>
</div>


<?php

getFoot();

function getFavorites(){
	
	echo '<h1>Favoriten</h1>';
	
	
	foreach ($_COOKIE as $url => $title) {
		if(strpos($url, 'http') === 0){
			$url = urldecode($url);
			$url = str_replace('orgdetails_php', 'orgdetails.php', $url);
			$url = str_replace('data_uni-muenster_de', 'data.uni-muenster.de', $url);
			echo '<h4><a class="btn btn-org" href="'.$url.'" style="white-space: normal !important">'.$title.'</a></h4>';		
		}
	} 					
	

	echo '<p class="lead">Du kannst Seiten zu den Favoriten hinzufügen, indem Du den &#9733; oben auf der jeweiligen Seite antippst.</p>';
		 	
}

?>