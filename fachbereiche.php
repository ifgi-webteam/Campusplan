<?php 
	require_once("functions.php");
	getHead();
?>

	<div class="container">
			
		<?php 
			if(isset($_GET['lang'])){
				getFBs($_GET['lang']);			 
			} else {
				getFBs();
			}
		?>	

		
	</div><!--/container -->


<?php

getFoot();

// loads all university departments in $lang (currently supported: de, en (TODO!))

function getFBs($lang = "de"){
	
	echo '<div class="row-fluid">
		<h1>Fachbereiche</h1>
		</div>
		<div class="row-fluid">
		<div class="btn-group btn-group-vertical">
		';

	$fbs = sparql_get("


prefix foaf: <http://xmlns.com/foaf/0.1/> 
prefix lodum: <http://vocab.lodum.de/helper/>
prefix owl: <http://www.w3.org/2002/07/owl#>

SELECT DISTINCT * WHERE {
  ?fb a lodum:Department ;
     foaf:name ?name;
     lodum:departmentNo ?no.   
  FILTER langMatches(lang(?name),'".$lang."') . 
  FILTER regex(?name,' - ') . 
} ORDER BY ?no

");
	
	if( !isset($fbs) ) {
		print "<li>Fehler beim Abruf der Fachbereichsdaten.</li>";
	}else{		

		// only start if there are any results:
		if($fbs->results->bindings){
			
			foreach ($fbs->results->bindings as $fb) {
 				
 				$name  = $fb->name->value;
 				$title = substr($name, 0, 14);
 				$desc  = substr($name, 17);
 				$title = str_replace("Fachbereich 0", "Fachbereich ", $title);
 				$url   = $fb->fb->value;
 				echo '<a class="btn btn-large btn-stacked" href="orgdetails.php?org_uri='.$url.'">'.$title.'<br /><small>'.$desc.'</small></a>';
 				//echo '<div class="span6"><h4><a class="btn btn-org" href="orgdetails.php?org_uri='.$url.'&org_title='.$title.'">'.$title.'<br /><span class="desc">'.$desc.'</span></a></h4></div>';
 				
 			}
 		 			
 		}
 	}

 	echo '</div>';

}

?>