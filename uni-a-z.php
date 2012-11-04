<?php 
	require_once("functions.php");
	getHead("Uni A-Z");
?>

<body> 

<div data-role="page" class="type-interior" id="page">

	<?php getHeader("Uni A-Z", "home"); ?>

	<div data-role="content">
		
		<div class="content-primary">
		
		<?php 
		if(isset($_GET['search'])){
			getSearchList($_GET['search']);
		}else if(isset($_GET['letter'])){		
			getLetterList($_GET['letter']);
		}else{
			getAZList();
		} 
		?>	

		</div><!--/content-primary -->		
		
		<?php getMenu("uni-a-z.php"); ?> 	

	</div><!-- /content -->
</div><!-- /page -->

<?php

getFoot();

function searchForm($value){
	echo '<form><input type="search" name="search" id="search-basic" value="'.$value.'" /><input type="submit" value="Einrichtung suchen" /></form>';
}

function getAZList(){

	searchForm('');

	$az = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
	echo '<ul data-role="listview" data-inset="true" data-theme="d" data-filter-theme="g" data-divider-theme="a">';
	foreach ($az as $letter) {
		echo '<li><a href="uni-a-z.php?letter='.$letter.'">'.$letter.'</a></li>';
	}
	echo '</ul>';
}


function getLetterList($letter){
	
	$orgs = sparql_get("

prefix foaf: <http://xmlns.com/foaf/0.1/> 
prefix aiiso: <http://purl.org/vocab/aiiso/schema#>
prefix lodum: <http://vocab.lodum.de/helper/>
		
SELECT DISTINCT ?orga ?name WHERE { 
		
	Graph <http://data.uni-muenster.de/context/uniaz/> {
          ?orga a ?type ; 
	            foaf:name ?name .
	  BIND(lcase(?name) as ?lname) .
	  FILTER langMatches(lang(?name),'DE') .
	  FILTER (STRSTARTS(?name, '".$letter."')) .
	  FILTER (STRLEN(?name) > 0) .
	  FILTER regex(str(?orga),'uniaz') . 
    }
           
} ORDER BY ?lname
");
	
	listOrgs($orgs, $letter);

}

function getSearchList($searchterm){

	$orgs = sparql_get("

prefix foaf: <http://xmlns.com/foaf/0.1/> 
prefix aiiso: <http://purl.org/vocab/aiiso/schema#>
prefix lodum: <http://vocab.lodum.de/helper/>
		
SELECT DISTINCT ?orga ?name WHERE { 
		
	Graph <http://data.uni-muenster.de/context/uniaz/> {
          ?orga a ?type ; 
	            foaf:name ?name .
	  
	  BIND(lcase(?name) as ?lname) .
	  FILTER langMatches(lang(?name),'DE') .
	  FILTER regex(?name, '".$searchterm."', 'i' ) .
	  FILTER (STRLEN(?name) > 0) .
	  FILTER regex(str(?orga),'uniaz') . 
    }
           
} ORDER BY ?lname
");
	
	listOrgs($orgs, $searchterm);

}

function listOrgs($orgs, $template){
	// only start if there are any results:
	if($orgs->results->bindings){
		echo '<h2 style="margin-top: 0">Einrichtungen: <em>'.$template.'</em></h2>';
		echo '<ul data-role="listview" data-inset="true" data-theme="d" data-filter="true" data-filter-placeholder="Ergebnisse durchsuchen"  data-filter-theme="g" data-divider-theme="a">
		';  
		// data-filter="true" data-filter-placeholder="Suche...">
		//';
		

			foreach ($orgs->results->bindings as $fb) {
				
				$name = $fb->name->value;
			$orga = $fb->orga->value;
 			
 			echo '<li><a href="orgdetails.php?org_uri='.$orga.'&org_title='.urlencode($name).'" style="white-space: normal !important">'.$name.'</a></li>';
 		}
 		
 		echo '</ul>';
 	} else {

 		searchForm($searchterm);
	
		print "<h3>Keine Einrichtungen mit <em>".$template."</em> gefunden.</h3>";

 	}
 	
}

?>