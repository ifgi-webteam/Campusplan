<?php 
	require_once("functions.php");
	getHead();
?>

	<div class="container">
		<div class="row-fluid">
			<h1>Wohnheime</h1>
		</div>

		<?php 
			if(isset($_GET['lang'])){
				getDorms($_GET['lang']);			 
			} else {
				getDorms();
			}
		?>			

	</div><!--/container -->


<?php

getFoot();
// loads all dorms in $lang (currently supported: de, en (TODO!))

function getDorms($lang = "de"){
	
	$fbs = sparql_get("

prefix foaf: <http://xmlns.com/foaf/0.1/> 
prefix lodum: <http://vocab.lodum.de/helper/>

SELECT DISTINCT * WHERE {
  ?fb a lodum:StudentHousing ;
     foaf:name ?name;
  FILTER langMatches(lang(?name),'".$lang."') .
} ORDER BY ?name
");
	
	if( !isset($fbs) ) {
		print '<p class="alert alert-error">Fehler beim Abruf der Wohnheimdaten.</p>';
	}else{		

		// only start if there are any results:
		if($fbs->results->bindings){
			
			echo '<div class="row-fluid">
			<div class="btn-group btn-group-vertical">';
			
			foreach ($fbs->results->bindings as $fb) {
 				
 				// TODO: English!
 				
 				$name  = $fb->name->value;
 				$name = str_replace("Wohnanlage ", "<span class='hidden-phone'>Wohnanlage </span>", $name);
 				$name = str_replace("Studentenwohnheim ", "<span class='hidden-phone'>Studentenwohnheim </span>", $name);
 				$name = str_replace("Internationales", "Int.", $name);
 				$url   = $fb->fb->value;
 				
 				echo '<a class="btn btn-large btn-stacked" href="orgdetails.php?org_uri='.$url.'">'.$name.'</a>';				 				
	
 			} 	

 			echo '</div></div>';	 		
 		}
 	}

}

?>