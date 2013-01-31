<?php 
	require_once("functions.php");
	getHead();
?>

		<?php 
			if(isset($_GET['lang'])){
				getFood($_GET['lang']);			 
			} else {
				getFood();
			}
		?>		

<?php

getFoot();


function getFood(){
	

	$time = strtotime('monday this week');  	
	$date = date('Y-m-d', $time);  	
	$datetime = $date.'T00:00:00Z';

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
  FILTER (?start > "'.$datetime.'"^^xsd:dateTime) .
} ORDER BY MONTH(?start) DAY(?start) LCASE(?mensaname) 
');
	
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
 				$other = false;
 				
				// create a new list for each day of the week:
 				$day = substr($menu->start->value, 0, 10);

 				if(!$header){
 					echo '<div class="container"><div class="row-fluid"><div class="span12"><h1>Mensaplan für die Woche vom '.date('j. F Y', strtotime($menu->start->value)).'</h1><hr /></div></div>
 					';
 					$header = true;
 				}

 				if($day !== $tag){
					if($weekday < count($weekdays)){ 					
 						echo '</tbody></table>';
 						// close the span6 div - but only if we have opened it before!
 						if ($tag !== 'none'){
 							echo '</div>';
 						}
 						// close the row-fluid div after every other span6 div
 						if($other){
 							echo '</div>';
 						}

 						$other = !$other;

 						echo '<div class="row-fluid"><div class="span6"';
 						if($other){ // move the right column a bit 
 							echo ' style="padding-right: 10px"';
 						}
 						echo '><h2 id="'.$day.'">'.$weekdays[$weekday++].'</h2>
 						<table class="table table-bordered table-striped">';
 						$tag = $day;
 					}else{
 						$weekday++;
 					}
 				}

	 			if($weekday <= count($weekdays)){	
					// break the list down by mensa:
					if($menu->mensa->value !== $mns){
						echo '<thead><tr><td><a href="orgdetails.php?org_uri='.$menu->mensa->value.'" data-ajax="false">'.$menu->mensaname->value.'</a></td></tr></thead><tbody>';
						$mns = $menu->mensa->value;
					}

					echo '<tr><td>'.$menu->name->value.' <span class="pull-right">'.$menu->minPrice->value.'€ | '.$menu->maxPrice->value.'€</span></td></tr>';
 				} 								
 			}  

 			echo '</tbody></table></div></div>';	
 		}
 	}

}

?>