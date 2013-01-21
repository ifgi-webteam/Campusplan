<?php 
	require_once("functions.php");
	getHead();
?>

	<div class="container">
		<div class="row">
	
		<h1>Wohnheime</h1>

		<?php 
			if(isset($_GET['lang'])){
				getDorms($_GET['lang']);			 
			} else {
				getDorms();
			}
		?>	

		</div>	

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
		print "<li>Fehler beim Abruf der Wohnheimdaten.</li>";
	}else{		

		// only start if there are any results:
		if($fbs->results->bindings){
			
			foreach ($fbs->results->bindings as $fb) {
 				
 				// TODO: English!
 				
 				$name  = $fb->name->value;
 				$name = str_replace("Wohnanlage ", "", $name);
 				$name = str_replace("Studentenwohnheim ", "", $name);
 				$name = str_replace("Internationales", "Int.", $name);
 				$url   = $fb->fb->value;
 				 				
 				//echo '<li><a href="orgdetails.php?org_uri='.$url.'&org_title='.urlencode($name).'" data-ajax="false">'.$name.'</a></li>';
 				echo '<h4><a class="btn btn-org" href="orgdetails.php?org_uri='.$url.'&org_title='.urlencode($name).'">'.$name.'</a></h4>';
 			} 		 		
 		}
 	}

}

?>