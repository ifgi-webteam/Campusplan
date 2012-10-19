<?php 
	require_once("head.php");
	$fb_title = $_GET["fb_title"];
	$fb_desc  = $_GET["fb_desc"]; 
	$fb_uri   = $_GET["fb_uri"]; 
?>

<body> 

<div data-role="page" class="type-interior">		

	<?php getHeader($fb_title, "back"); ?>

	<div data-role="content">
		
		<div class="content-primary">

		<?php 
		
			echo '<h2>'.$fb_desc.'</h2>';
		
			if(isset($_GET['lang'])){
				getFBorgs($fb_uri, $fb_title, $fb_desc, $_GET['lang']);			 
			} else {
				getFBorgs($fb_uri, $fb_title, $fb_desc);
			}
		?>	

		</div><!--/content-primary -->		
		
		<?php getMenu("fachbereiche.php"); ?> 	

	</div><!-- /content -->		
</div><!-- /page -->

</body>
</html>
<?php

// loads all suborganisations of the given department

function getFBorgs($fb_uri, $fb_title, $fb_desc, $lang = "de"){
	
	$fbs = sparql_get("

prefix foaf: <http://xmlns.com/foaf/0.1/> 
prefix aiiso: <http://purl.org/vocab/aiiso/schema#>

SELECT DISTINCT * WHERE {
  GRAPH <http://data.uni-muenster.de/context/uniaz/>{
    ?org aiiso:part_of <".$fb_uri."> ;
         foaf:name ?name .
    FILTER langMatches(lang(?name),'".$lang."') .
    FILTER regex(str(?org),'uniaz') .
  }
} ORDER BY ?name

");
	
	if( !isset($fbs) ) {
		print "<li>Fehler beim Abruf der Unterorganisationen dieses Fachbereichs.</li>";
	}else{		

		// only start if there are any results:
		if($fbs->results->bindings){
			echo '<ul data-role="listview" data-inset="true">
			';  
			
			foreach ($fbs->results->bindings as $fb) {
 				
 				$name = $fb->name->value;
 				$url = $fb->org->value;
 				
 				// data-ajax="false" for all links to pages with maps - otherwise, the map doesn't load!
 				echo '<li><a href="orgdetails.php?org_uri='.$url.'&org_title='.urlencode($name).'" data-ajax="false">'.$name.'</a></li>
 				';
 			}
 		
 			echo '</ul>';
 		}
 	}

}

?>