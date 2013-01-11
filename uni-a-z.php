<?php 
	require_once("functions.php");
	getHead("Uni A-Z");
?>
 
	<div class="container">
		<div class="row">
			<?php 
			if(isset($_GET['search'])){
				getSearchList($_GET['search']);
			}else if(isset($_GET['letter'])){		
				getLetterList($_GET['letter']);
			}else{
				getAZList();
			} 
			?>
		</div>	

	</div><!--/container -->
			
<?php

getFoot();

function searchForm($value){
	echo '
		<h1>Uni A-Z</h1>
		<p class="lead">Durchsuche das Einrichtungsverzeichnis der WWU, oder klicke dich durch den alphabetischen Index.</p>
		<form action="uni-a-z.php">
			<div class="input-append">
	  			<input class="searchfield" name="search" placeholder="Suche..." id="appendedInputButton" type="text">
	  			<button class="btn btn-large" type="submit"><i class="icon-search"></i></button>
			</div>
		</form>
	';
	// echo '<form><input type="search" name="search" id="search-basic" value="'.$value.'" /><input type="submit" value="Einrichtung suchen" /></form>';
}

function getAZList(){

	searchForm('');

	$az = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
	foreach ($az as $letter) {
		echo '<a class="btn btn-large btn-letter" href="uni-a-z.php?letter='.$letter.'">'.$letter.'</a> ';
	}
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
		echo '<h2>Einrichtungen mit <em>'.$template.'</em></h2>';
		
		foreach ($orgs->results->bindings as $fb) {
				
			$name = $fb->name->value;
			$orga = $fb->orga->value;
 			
 			echo '<h4><a class="btn btn-org" href="orgdetails.php?org_uri='.$orga.'&org_title='.urlencode($name).'">'.$name.'</a></h4>';
 		}
 		 		
 	} else {

 		searchForm($searchterm);
	
		print "<h3>Keine Einrichtungen mit <em>".$template."</em> gefunden.</h3>";

 	}
 	
}

?>