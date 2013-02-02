<?php 
	require_once("functions.php");

	$fb_title = $_GET["fb_title"];
	$fb_desc  = $_GET["fb_desc"]; 
	$fb_uri   = $_GET["fb_uri"]; 

getHead();
?>

<div class="container">
		<div class="row">

		<?php 
			
			echo '<h1>'.$fb_title.'</h1>';
			echo '<p class="lead">'.$fb_desc.'</p>';
		
			if(isset($_GET['lang'])){
				getFBorgs($fb_uri, $fb_title, $fb_desc, $_GET['lang']);			 
			} else {
				getFBorgs($fb_uri, $fb_title, $fb_desc);
			}
		?>	

		</div>	

	</div><!--/container -->

<?php

getFoot();

// loads all suborganisations of the given department

function getFBorgs($fb_uri, $fb_title, $fb_desc, $lang = "de"){
	
	$fbs = sparql_get("

prefix foaf: <http://xmlns.com/foaf/0.1/> 
prefix aiiso: <http://purl.org/vocab/aiiso/schema#>

SELECT DISTINCT * WHERE {
  GRAPH <http://data.uni-muenster.de/context/uniaz/>{
    ?org aiiso:part_of <".$fb_uri."> ;
         foaf:name ?name .
    BIND(lcase(?name) as ?lname) .
    FILTER langMatches(lang(?name),'".$lang."') .
    FILTER regex(str(?org),'uniaz') .
  }
} ORDER BY ?lname

");
	
	if( !isset($fbs) ) {
		print "<li>Fehler beim Abruf der Unterorganisationen dieses Fachbereichs.</li>";
	}else{		

		// only start if there are any results:
		if($fbs->results->bindings){
			foreach ($fbs->results->bindings as $fb) {
 				
 				$name = $fb->name->value;
 				$url = $fb->org->value;
 				
 				echo '<h4><a class="btn btn-org internal" href="orgdetails.php?org_uri='.$url.'&org_title='.urlencode($name).'">'.$name.'</a></h4>';
 				
 			}
 		 			
 		}
 	}

}

?>