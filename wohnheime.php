<?php 
	require_once("functions.php");
	getHead("Wohnheime");
?>

<body> 

<div data-role="page" class="type-interior">

	<?php getHeader("Wohnheime", "home"); ?>

	<div data-role="content">
		
		<div class="content-primary">

		<?php 
			if(isset($_GET['lang'])){
				getFBs($_GET['lang']);			 
			} else {
				getFBs();
			}
		?>	

		</div><!--/content-primary -->		
		
		<?php getMenu("wohnheime.php"); ?> 	

	</div><!-- /content -->		
</div><!-- /page -->

</body>
</html>
<?php

// loads all university departments in $lang (currently supported: de, en (TODO!))

function getFBs($lang = "de"){
	
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
			echo '<ul data-role="listview" data-inset="false">
			';  
			
			foreach ($fbs->results->bindings as $fb) {
 				
 				// TODO: English!
 				
 				$name  = $fb->name->value;
 				$name = str_replace("Wohnanlage ", "", $name);
 				$name = str_replace("Studentenwohnheim ", "", $name);
 				$name = str_replace("Internationales", "Int.", $name);
 				$url   = $fb->fb->value;
 				 				
 				echo '<li><a href="orgdetails.php?org_uri='.$url.'&org_title='.urlencode($name).'" data-ajax="false">'.$name.'</a></li>
 				';
 			}
 		
 			echo '</ul>';
 		}
 	}

}

?>