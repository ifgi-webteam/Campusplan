<?php 
	require_once("functions.php");
	getHead("Hörsäle");
?>

<div class="container">
		<div class="row">
		
		<?php 
			getLectureHalls();			 			
		?>			

	</div>
</div>

<?php

getFoot();

// loads all university lecture halls

function getLectureHalls(){
	
	echo '<h1>Hörsäle</h1>';
	
	$hoersaele = sparql_get("

prefix foaf: <http://xmlns.com/foaf/0.1/> 
prefix lodum: <http://vocab.lodum.de/helper/>
prefix owl: <http://www.w3.org/2002/07/owl#>
prefix vcard: <http://www.w3.org/2006/vcard/ns#>

SELECT DISTINCT * WHERE {

  ?hs a lodum:LectureHall ;
     foaf:name ?name ;
     lodum:building ?building ;
     lodum:floor ?floor .      
  
  ?building foaf:name ?buildingname;
            vcard:adr ?addr .

  ?addr vcard:street-address ?address .          

} ORDER BY ?name

");
	
	if( !isset($hoersaele) ) {
		print "<li>Fehler beim Abruf der Hörsaaldaten.</li>";
	}else{		

		// only start if there are any results:
		if($hoersaele->results->bindings){
			
			$prevtitle = '';

			foreach ($hoersaele->results->bindings as $hs) {
 				
 				$title    = $hs->name->value;
 				$url      = $hs->hs->value;
 				$building = $hs->buildingname->value;
 				$address  = $hs->address->value;
 				$floor    = $hs->floor->value;

 				if($floor == '1'){
 					$floor = 'Erdgeschoss';
 				}else{
 					$floor = intval($floor);
 					$floor--;
 					$floor = $floor.'. Obergeschoss';
 				} 

 				// skip duplicates
 				if($title != $prevtitle){
 				 	echo '<h4><a class="btn btn-large" href="orgdetails.php?org_uri='.$url.'&org_title='.$title.'">'.$title.'<br /><span class="desc">'.$building.', '.$address.' ('.$floor.')</span></a></h4>
 				';}

 				$prevtitle = $title;
 			}
 		
 			
 		}
 	}

}

?>