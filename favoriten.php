<?php 
	require_once("functions.php");
	getHead("Fachbereiche");
?>

<body> 

<div data-role="page" class="type-interior" id="page">

	<div data-role="content">
		
		<div class="content-primary">

		<?php getFavorites(); ?>	

		</div><!--/content-primary -->		
		
		<?php getMenu(); ?> 	

	</div><!-- /content -->		
</div><!-- /page -->


<?php

getFoot();

// loads all university departments in $lang (currently supported: de, en (TODO!))

function getFavorites(){
	
	echo '<h3>Favoriten</h3>
	';
	echo '<ul data-role="listview" data-inset="true">
			';  
	
	$zero = true;		
	foreach ($_COOKIE as $key => $value) {
		if(strpos($key, 'bookmark-') === 0){
			$zero = false;
			$key = substr($key, 9);
			$key = str_replace('_', ' ', $key);
			$value = substr($value, strrpos($value, 'orgdetails.php'));
			echo '<li><a href="'.$value.'&org_title='.$key.'" style="white-space: normal !important">'.$key.'</a></li>
';		
		}
	} 					
	if($zero){
		echo '<li>Du kannst Seiten zu den Favoriten hinzufügen, indem Du den &#9733; oben auf der Seite antippst.</li>';
	}	 				
 		
 	echo '</ul>
 	';
 	
}

?>