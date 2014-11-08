<?php
// Implement a cache mechanism for file_get_contents()
function file_get_contents_cached($url) {
	$urlMd5 = md5($url);
	$cachedFile = "cache/".$urlMd5;
	if(is_file($cachedFile)) {	
		$cached = file_get_contents($cachedFile, true);
		return $cached;
	} else {
		$opts = array(
			'http'=>array(
				'header' => "Accept: application/sparql-results+json\r\n",
				'timeout' => 10
				)
		);
		$context = stream_context_create($opts);
		$response = file_get_contents($url, false, $context);
		if($http_response_header[0] != "HTTP/1.1 200 OK") return false;

		file_put_contents($cachedFile, $response);
		return $response;
	}
}

// Query database and return JSON string
function sparql_get($query) {
	$url = 'http://data.uni-muenster.de/sparql?query='.urlencode($query).'&format=json';
	$response = file_get_contents_cached($url);
	if(json_decode($response)) { // check for validity
		return $response;
	}
	return false;
}

// Search database by starting letter
function searchByLetter($letter) {
	
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

// Search database by whole word
function searchByWord($searchterm) {

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

// single organization query
function getOrgDetails($identifier, $lang = "de") {
	$org = "http://data.uni-muenster.de/context/".$identifier;
	$orga = sparql_get("

prefix foaf: <http://xmlns.com/foaf/0.1/> 
prefix geo: <http://www.w3.org/2003/01/geo/wgs84_pos#> 
prefix vcard: <http://www.w3.org/2006/vcard/ns#>
prefix lodum: <http://vocab.lodum.de/helper/>
prefix ogc: <http://www.opengis.net/ont/OGC-GeoSPARQL/1.0/>
prefix xsd: <http://www.w3.org/2001/XMLSchema#> 

SELECT DISTINCT ?name ?homepage ?address ?street ?zip ?city ?buildingaddress ?lat ?long ?wkt ?start ?minPrice WHERE {
  <".$org."> foaf:name ?name.
  OPTIONAL { <".$org."> foaf:homepage ?homepage . }  
  OPTIONAL { <".$org."> vcard:adr ?address . 
  	FILTER ( datatype(?address) = xsd:string )
  }
  OPTIONAL { <".$org."> lodum:building ?building . 
     OPTIONAL { ?building geo:lat ?lat ; 
                              geo:long ?long . }
     OPTIONAL { ?building vcard:adr ?buildingAddress . 
     			?buildingAddress vcard:street-address ?street ;
     			    vcard:postal-code ?zip ;
     			    vcard:region ?city .     			
     }          
     OPTIONAL { ?building ogc:hasGeometry ?geometry .
                          ?geometry ogc:asWKT ?wkt . } 
  }  
  FILTER langMatches(lang(?name),'".$lang."') . 
}
	");

	return $orga;
}

// Mensaplan for whole week, all Mensas
function getMensaplan($identifier = "") {
	if(date('l') == "Saturday" || date('l') == "Sunday") {
		$timeStart = strtotime('monday next week');
	} else {
		$timeStart = strtotime('monday this week');
	}
	$timeEnd = $timeStart + 7*24*60*60;
	$dateStart = date('Y-m-d', $timeStart);
	$dateEnd = date('Y-m-d', $timeEnd);
	$datetimeStart = $dateStart.'T00:00:00Z';
	$datetimeEnd = $dateEnd.'T00:00:00Z';

	$specificMensa = "";
	if($identifier != "") $specificMensa = '<http://data.uni-muenster.de/context/'.$identifier.'> gr:offers ?menu.';

	$food = sparql_get('
prefix xsd: <http://www.w3.org/2001/XMLSchema#> 
prefix gr: <http://purl.org/goodrelations/v1#>
prefix foaf: <http://xmlns.com/foaf/0.1/> 

SELECT DISTINCT ?name ?start ?minPrice ?maxPrice ?mensa ?mensaname WHERE {
  '.$specificMensa.'
  ?menu a gr:Offering ;
        gr:availabilityStarts ?start ;
        gr:name ?name ;
        gr:hasPriceSpecification ?priceSpec .
  ?priceSpec gr:hasMinCurrencyValue ?minPrice ;
             gr:hasMaxCurrencyValue ?maxPrice .
  ?mensa gr:offers ?menu ;
         foaf:name ?mensaname .  
  FILTER (xsd:dateTime(?start) > "'.$datetimeStart.'"^^xsd:dateTime
  	&& xsd:dateTime(?start) < "'.$datetimeEnd.'"^^xsd:dateTime
  	) .
} ORDER BY MONTH(?start) DAY(?start) LCASE(?mensaname) 
');
	return $food;
}

function getMapGeometries() {
	$query = "
SELECT DISTINCT ?building ?name ?lat ?lon ?streetaddress ?postalcode ?region WHERE { 
	?building a <http://dbpedia.org/ontology/building> ; 
	<http://xmlns.com/foaf/0.1/name> ?name ; 
	<http://www.w3.org/2003/01/geo/wgs84_pos#lat> ?lat; 
	<http://www.w3.org/2003/01/geo/wgs84_pos#long> ?lon; 
	<http://www.w3.org/2006/vcard/ns#adr> ?adr . 
	Filter( !EXISTS { ?building <http://www.opengis.net/ont/OGC-GeoSPARQL/1.0/hasGeometry> ?geom. }) 
	?adr <http://www.w3.org/2006/vcard/ns#street-address> ?streetaddress; 
	<http://www.w3.org/2006/vcard/ns#postal-code> ?postalcode; 
	<http://www.w3.org/2006/vcard/ns#region> ?region.
} ORDER BY ?name
";
	$mapData = sparql_get($query);
	return $mapData;
}


function getFachbereiche() {
	$lang="de";
	$query = "
prefix foaf: <http://xmlns.com/foaf/0.1/> 
prefix lodum: <http://vocab.lodum.de/helper/>
prefix owl: <http://www.w3.org/2002/07/owl#>

SELECT DISTINCT * WHERE {
  ?fb a lodum:Department ;
     foaf:name ?name;
     lodum:departmentNo ?no.   
  FILTER langMatches(lang(?name),'".$lang."') . 
  FILTER regex(?name,' - ') . 
  FILTER regex(str(?fb), '/fb') .
} ORDER BY ?no
";
	$fbs = sparql_get($query);
	return $fbs;
}


function getHoersaele() {
	$lang="de";
	$query = "
prefix foaf: <http://xmlns.com/foaf/0.1/> 
prefix lodum: <http://vocab.lodum.de/helper/>
prefix owl: <http://www.w3.org/2002/07/owl#>
prefix vcard: <http://www.w3.org/2006/vcard/ns#>

SELECT DISTINCT * WHERE {

  ?hs a lodum:LectureHall ;
     foaf:name ?name ;
     lodum:building ?building ;
     lodum:floor ?floor .      
  
  ?building foaf:name ?buildingname;
            vcard:adr ?addr .

  ?addr vcard:street-address ?address .  

  FILTER langMatches(lang(?name),'de') .         

} ORDER BY ?name
";
	$fbs = sparql_get($query);
	return $fbs;
}


function getWohnheime() {
	$lang="de";
	$query = "
prefix foaf: <http://xmlns.com/foaf/0.1/> 
prefix lodum: <http://vocab.lodum.de/helper/>

SELECT DISTINCT * WHERE {
  ?fb a lodum:StudentHousing ;
     foaf:name ?name;
  FILTER langMatches(lang(?name),'".$lang."') .
} ORDER BY ?name
";
	$fbs = sparql_get($query);
	return $fbs;
}