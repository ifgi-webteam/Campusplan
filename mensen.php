<?php 
	require_once("functions.php");
	getHead();
?>

	<div class="container">
		<div class="row">
			
		<?php 
			if(isset($_GET['lang'])){
				getFood($_GET['lang']);			 
			} else {
				getFood();
			}
		?>	

		</div>	

	</div><!--/container -->


<?php

getFoot();


function getFood(){
	
	$food = sparql_get('
prefix xsd: <http://www.w3.org/2001/XMLSchema#> 
prefix gr: <http://purl.org/goodrelations/v1#>
prefix foaf: <http://xmlns.com/foaf/0.1/> 

SELECT DISTINCT ?name ?start ?minPrice ?maxPrice ?mensa ?mensaname WHERE {
    
  ?menu a gr:Offering ;
        gr:availabilityStarts ?start ;
        gr:name ?name ;
        gr:hasPriceSpecification ?priceSpec .
  ?priceSpec gr:hasMinCurrencyValue ?minPrice ;
             gr:hasMaxCurrencyValue ?maxPrice .
  ?mensa gr:offers ?menu ;
         foaf:name ?mensaname .  
} ORDER BY DESC(?start) ?mensa LIMIT 70
'); // The LIMIT might have to be increased if we add more Mensas!
	
	if( !isset($food) ) {
		echo '<div class="alert alert-error">Fehler beim Abruf der Mensadaten.</div>';
	}else{		

		// only start if there are any results:
		if($food->results->bindings){
			$header = false;
			
			$mns = 'none';
			$tag = 'none';
			$weekdays = array("Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag");
			$weekday = 0;

 			foreach ($food->results->bindings as $menu) {
 				
 				if($weekays <= count($weekdays)){
 					// create a new list for each day of the week:
 				$day = substr($menu->start->value, 0, 10);

 				if(!$header){
 					echo '<h1>Mensaplan für die Woche vom '.date('j. F Y', strtotime($menu->start->value)).'</h1>';
 					//echo '<h1>Mensa für die Woche vom '.$menu->start->value.'</h1>';
					$header = true;
 				}

 				if($day !== $tag){
 					
 					echo '<h2 id="'.$day.'">'.$weekdays[$weekday++].'</h2>
 					<ul>';
 					$tag = $day;
 				}
 				
				// break the list down by mensa:
				if($menu->mensa->value !== $mns){
					echo '<h3><a href="orgdetails.php?org_uri='.$menu->mensa->value.'" data-ajax="false">'.$menu->mensaname->value.'</a></h3>';
					$mns = $menu->mensa->value;
				}

				echo '<li>'.$menu->name->value.' <span class="ui-li-count ui-btn-up-c ui-btn-corner-all" style="margin-top: -16px">'.$menu->minPrice->value.'€<br/>'.$menu->maxPrice->value.'€</span></li>';
 				} 								
 			} 

 			echo '</ul>';		 		
 		}
 	}

}

?>