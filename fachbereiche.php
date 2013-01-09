<?php 
	require_once("functions.php");
	getHead("Fachbereiche");
?>

<div data-role="page" class="type-interior" id="page">

	<div data-role="content">
		
		<div class="content-primary">

			<h2>Fachbereiche</h2>

		<?php 
			if(isset($_GET['lang'])){
				getFBs($_GET['lang']);			 
			} else {
				getFBs();
			}
		?>	

		</div><!--/content-primary -->		
		
	</div><!-- /content -->		
</div><!-- /page -->


<?php

getFoot();

// loads all university departments in $lang (currently supported: de, en (TODO!))

function getFBs($lang = "de"){
	
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
  FILTER regex(str(?fb),'uniaz') . 
} ORDER BY ?no

");
	
	if( !isset($fbs) ) {
		print "<li>Fehler beim Abruf der Fachbereichsdaten.</li>";
	}else{		

		// only start if there are any results:
		if($fbs->results->bindings){
			echo '<ul data-role="listview" data-inset="true">
			';  
			
			foreach ($fbs->results->bindings as $fb) {
 				
 				// TODO: English!
 				
 				$name  = $fb->name->value;
 				$title = substr($name, 0, 14);
 				$desc  = substr($name, 17);
 				$title = str_replace("Fachbereich 0", "Fachbereich ", $title);
 				$url   = $fb->fb->value;
 				 				
 				echo '<li><a href="fachbereich.php?fb_uri='.$url.'&fb_title='.$title.'&fb_desc='.$desc.'"><h3>'.$title.'</h3><p>'.$desc.'</p></a></li>
 				';
 			}
 		
 			echo '</ul>';
 		}
 	}

}

?>