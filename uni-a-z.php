<?php 
	require_once("functions.php");
	checkCache();
	getHead("Uni A-Z");
?>
 
	<div class="container">
		
			<?php 
			if(isset($_GET['search'])){
				getSearchList($_GET['search']);
			}else if(isset($_GET['letter'])){		
				getLetterList($_GET['letter']);
			}else{
				getAZList();
			} 
			?>
		
	</div><!--/container -->
			
<?php

getFoot();

function searchForm($value){
	echo '
		<div class="row-fluid">
			<div class="span12">
				<h1>Uni A-Z</h1>
				<p class="lead">Das Einrichtungsverzeichnis der WWU.</p>
				<form action="uni-a-z.php">
					<div class="input-append">
		  				<input class="searchfield" name="search" placeholder="Suche..." id="appendedInputButton" type="text">
		  				<button class="btn btn-large" type="submit"><i class="icon-search"></i></button>
					</div>
				</form>		
			</div>
		</div>
	';
}

function getAZList(){

	searchForm('');
	echo '<p class="lead">... oder nach Anfangsbuchstabe:</p>';
	echo '<p class="az">';
	foreach (range('A', 'Z') as $letter) {
		echo '<a class="btn btn-large btn-letter internal" href="uni-a-z.php?letter='.$letter.'">'.$letter.'</a> ';
	}
	for($i=1; $i<=10; $i++) { echo '<a class="btn btn-large btn-letter internal invisible" href="uni-a-z.php?letter=" style="height:1px;">&nbsp;</a> ';}
	echo '</p>';
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
	if($orgs && $orgs->results->bindings){
		echo '<h2>Einrichtungen mit <em>'.htmlspecialchars($template).'</em></h2>

		<div class="btn-group btn-group-vertical">';
		
		foreach ($orgs->results->bindings as $fb) {
				
			$name = $fb->name->value;
			$orga = $fb->orga->value;
 			
 			echo '<a class="btn btn-large btn-stacked internal" href="orgdetails.php?org_uri='.urlencode($orga).'">'.htmlspecialchars($name).'</a>';
 		}

 		echo '</div>';
 		 		
 	} else {

 		print "<h3>Keine Einrichtungen mit <em>".htmlspecialchars($template)."</em> gefunden.</h3>";

 		getAZList();

 	}
 	
}

flushCache();

?>
