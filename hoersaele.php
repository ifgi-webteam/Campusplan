<?php 
	require_once("functions.php");
	getHead("Hörsäle");
?>

<body> 

<div data-role="page" class="type-interior">

	<?php getHeader("Hörsäle", "home"); ?>

	<div data-role="content">
		
		<div class="content-primary">

		<h2>Hörsäle</h2>
		<?php 
			getLectureHalls();			 			
		?>	

		</div><!--/content-primary -->		
		
		<?php getMenu("fachbereiche.php"); ?> 	

	</div><!-- /content -->		
</div><!-- /page -->

</body>
</html>
<?php

// loads all university lecture halls

function getLectureHalls(){
	
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
			echo '<ul data-role="listview" data-inset="true">
			';  
			
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
 				 	echo '<li><a href="orgdetails.php?org_uri='.$url.'&org_title='.$title.'"><h3>'.$title.'</h3><p><b>'.$building.'</b>, '.$address.' ('.$floor.')</p></a></li>
 				';}

 				$prevtitle = $title;
 			}
 		
 			echo '</ul>';
 		}
 	}

}

?>