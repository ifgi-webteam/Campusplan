<?php 
	require_once("functions.php");
	getHead();
?>

<body> 

<div class="container">
	<div class="row">
		<?php getFavorites(); ?>			
	</div>
</div>


<?php

getFoot();

// loads all university departments in $lang (currently supported: de, en (TODO!))

function getFavorites(){
	
	echo '<h1>Favoriten</h1>';
	
	$zero = true;		
	foreach ($_COOKIE as $key => $value) {
		if(strpos($key, 'bookmark-') === 0){
			$zero = false;
			$key = substr($key, 9);
			$key = str_replace('_', ' ', $key);
			$value = substr($value, strrpos($value, 'orgdetails.php'));
			echo '<h4><a class="btn btn-org" href="'.$value.'&org_title='.$key.'" style="white-space: normal !important">'.$key.'</a></h4>';		
		}
	} 					
	if($zero){
		echo '<p class="lead">Du kannst Seiten zu den Favoriten hinzufügen, indem Du den &#9733; oben auf der Seite antippst.</p>';
	}	 				
 		 	
}

?>