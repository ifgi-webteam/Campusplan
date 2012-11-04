<?php 
	require_once("functions.php");
	getHead("Mensen");
?>

<body> 

<div data-role="page" class="type-interior">

	<?php getHeader("Mensen", "home"); ?>

	<div data-role="content">
		
		<div class="content-primary">
		
		<?php 
			getFood();			 
		?>	

		</div><!--/content-primary -->		
		
		<?php getMenu(); ?> 	

	</div><!-- /content -->
</div><!-- /page -->

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
} ORDER BY ?start ?mensa 
');
	
	if( !isset($food) ) {
		print "<li>Fehler beim Abruf der Mensadaten.</li>";
	}else{		

		// only start if there are any results:
		if($food->results->bindings){
			echo '<h2>Angebote in den Mensen für die Woche vom '.date('d.m.Y', $time).'</h2>';
			
			
			$mns = 'none';
			$tag = 'none';
			$weekdays = array("Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag");
			$weekday = 0;

 			foreach ($food->results->bindings as $menu) {
 				// create a new list for each day of the week:
 				$day = substr($menu->start->value, 0, 10);
 				if($day !== $tag){
 					if($tag !== 'none'){
 						echo '</ul>';
 					}
 					echo '<h3 id="'.$day.'">'.$weekdays[$weekday++].'</h3>';
 					echo '<ul data-theme="g" data-role="listview" data-inset="true" data-theme="d" data-filter-theme="g" data-divider-theme="a">';
 					$tag = $day;
 				}
 				
				// break the list down by mensa:
				if($menu->mensa->value !== $mns){
					echo '<li><a href="orgdetails.php?org_uri='.$menu->mensa->value.'" data-ajax="false">'.$menu->mensaname->value.'</a></li>';
					$mns = $menu->mensa->value;
				}

				echo '<li>'.$menu->name->value.' <span class="ui-li-count ui-btn-up-c ui-btn-corner-all" style="margin-top: -16px">'.$menu->minPrice->value.'€<br/>'.$menu->maxPrice->value.'€</span></li>';
				
 			} 

 			echo '</ul>';		 		
 		}
 	}

}

?>