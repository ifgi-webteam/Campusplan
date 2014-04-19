<?php
include('functions.php');

// search database by starting letter
function searchByLetter($letter){
	
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
	
	return $orgs;

}

// search database by whole word
function searchByWord($searchterm){

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
	
	return $orgs;

}

// decode AngularJS data parameter
$angjs_data = file_get_contents('php://input');
$angjs_data_decoded = json_decode($angjs_data);
$searchterm = $angjs_data_decoded->data;

// perform a letter search if query is only one letter
if(preg_match('/[a-zA-Z]/', $searchterm)) {
	echo searchByLetter($searchterm);
} else {
	echo searchByWord($searchterm);
}
?>