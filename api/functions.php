<?php
// Implement a cache mechanism for file_get_contents()
// Set a default cache expiry time of 7 days (604800 seconds)
function file_get_contents_cached($url, $expiry=604800, $opts=array()) {
	try {
		$urlMd5 = md5($url);
		$cachedFile = "cache/".$urlMd5;
		$now = time();
		// check if a cached version exists AND if it's not older than $epiry
		if(is_file($cachedFile) && ($now-filemtime($cachedFile)) < $expiry) {
			$cached = file_get_contents($cachedFile, true);
			return $cached;
		} else {
			if(empty($opts)) {
				$opts = array(
					'http'=>array(
						'header' => "Accept: application/json\r\n",
						'timeout' => 10
						)
				);
			}
			$context = stream_context_create($opts);

			// mute file_get_contents because it doesn't behave within try/catch block when exception happens
			if($response = @file_get_contents($url, false, $context)) {
				if($http_response_header[0] != "HTTP/1.1 200 OK") return false;
				//print_r($http_response_header);

				file_put_contents($cachedFile, $response);
				return $response;
			}
			return false;
		}
	} catch(Exception $e) {

	}
}

// Query database and return JSON string
function sparql_get($query) {
	$url = 'http://giv-lodumdata.uni-muenster.de:8080/openrdf-sesame/repositories/lodumhbz?format=json&limit=10000&query='.rawurlencode($query).'';
	$opts = array(
			'http'=>array(
				'header' => "Accept: application/sparql-results+json\r\n",
				'timeout' => 10
				)
		);
	$response = file_get_contents_cached($url, 604800, $opts);
	if(json_decode($response)) { // check for validity
		return $response;
	}
	return false;
}

/*
	Query database for organizations by starting letter
*/
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

/*
	Query database for organizations by whole word
*/
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

/*
	Query single organization's details
*/
function getOrgDetails($identifier, $lang = "de") {
	$org = "http://data.uni-muenster.de/context/".$identifier;
	$orga = sparql_get("

prefix foaf: <http://xmlns.com/foaf/0.1/>
prefix wgs84: <http://www.w3.org/2003/01/geo/wgs84_pos#>
prefix vcard: <http://www.w3.org/2006/vcard/ns#>
prefix lodum: <http://vocab.lodum.de/helper/>
PREFIX geo:<http://www.opengis.net/ont/geosparql#>
prefix xsd: <http://www.w3.org/2001/XMLSchema#>

SELECT DISTINCT ?subject ?name ?homepage ?address ?street ?zip ?city ?buildingaddress ?lat ?long ?wkt WHERE {
  <".$org."> foaf:name ?name.
  OPTIONAL { <".$org."> foaf:homepage ?homepage . }
  OPTIONAL { <".$org."> vcard:adr ?address .
  	FILTER ( datatype(?address) = xsd:string )
  }
  OPTIONAL { <".$org."> lodum:building ?building .
     OPTIONAL { ?building wgs84:lat ?lat ;
                              wgs84:long ?long . }
     OPTIONAL { ?building vcard:adr ?buildingAddress .
     			?buildingAddress vcard:street-address ?street ;
     			    vcard:postal-code ?zip ;
     			    vcard:region ?city .
     }
     OPTIONAL { ?building geo:hasGeometry ?geometry .
                          ?geometry geo:asWKT ?wkt . }
  }
  BIND(<".$org."> as ?subject)
  FILTER langMatches(lang(?name),'".$lang."') .
}
	");

	return $orga;
}

function listSubSorganizations($identifier) {
	$org = "http://data.uni-muenster.de/context/".$identifier;
	$orgs = sparql_get("
prefix foaf: <http://xmlns.com/foaf/0.1/>
prefix aiiso: <http://purl.org/vocab/aiiso/schema#>
prefix lodum: <http://vocab.lodum.de/helper/>
SELECT DISTINCT ?orga ?name WHERE {
	Graph <http://data.uni-muenster.de/context/uniaz/> {
		?orga a ?type ;
		foaf:name ?name ;

		aiiso:part_of <".$org."> .
		BIND(lcase(?name) as ?lname) .
		FILTER langMatches(lang(?name),'DE') .
		FILTER (STRLEN(?name) > 0) .
		FILTER regex(str(?orga),'uniaz') .
	}
} 
	");
	return $orgs;
}

/*
	Query all Mensaplan for whole week, all Mensas
*/
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
/* Fetch mensa data from different source, implementation see https://github.com/chk1/mensaparser */
function getMensaplan2($identifier = "") {
	if($identifier!="") { $identifier = "/".$identifier; }
	return file_get_contents_cached('http://app.uni-muenster.de:9000/menu'.$identifier, 24*3600);
}


/*
	Query all buildings for display on map
	Includes point location data
*/
function getMapGeometriesPts() {
	$query = "
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
PREFIX aiiso: <http://purl.org/vocab/aiiso/schema#>
PREFIX lodum: <http://vocab.lodum.de/helper/>
PREFIX geo:<http://www.opengis.net/ont/geosparql#>
PREFIX vcard: <http://www.w3.org/2006/vcard/ns#>
PREFIX wgs84: <http://www.w3.org/2003/01/geo/wgs84_pos#>

SELECT DISTINCT ?name ?lat ?lon ?address ?organization ?buildingname ?building WHERE {
	?organization a foaf:Organization ;
	  foaf:name ?name ;
	  vcard:adr ?adr ;
	  lodum:building ?building .
	  
	?building 
	  wgs84:lat ?lat ;
	  wgs84:long ?lon ;
	  foaf:name ?buildingname .

	OPTIONAL { ?organization vcard:adr ?address .
 	  FILTER ( datatype(?address) = xsd:string )
  	}
} ";
	$mapData = sparql_get($query);
	return $mapData;
}

/*
	Query all buildings for display on map
	Includes polygon data
*/
function getMapGeometriesPoly() {
	$query = "
prefix foaf: <http://xmlns.com/foaf/0.1/>
prefix aiiso: <http://purl.org/vocab/aiiso/schema#>
prefix lodum: <http://vocab.lodum.de/helper/>
PREFIX geo:<http://www.opengis.net/ont/geosparql#>

SELECT DISTINCT ?building ?name ?wkt WHERE {
	?building a <http://dbpedia.org/ontology/building> ;
	<http://xmlns.com/foaf/0.1/name> ?name .
	?building geo:hasGeometry ?geom.
    	?geom geo:asWKT ?wkt 
} ORDER BY ?name
";
	$mapData = sparql_get($query);
	return $mapData;
}

/*
	Query all Fachbereiche
*/
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

/*
	Query all Hörsäle
*/
function getHoersaele() {
	$lang="de";
	$query = "
prefix foaf: <http://xmlns.com/foaf/0.1/>
prefix lodum: <http://vocab.lodum.de/helper/>
prefix owl: <http://www.w3.org/2002/07/owl#>
prefix vcard: <http://www.w3.org/2006/vcard/ns#>

SELECT DISTINCT ?hs (MAX(?name) AS ?name) (MAX(?building) AS ?building) (MAX(?floor) AS ?floor) (MAX(?buildingname) AS ?buildingname) (MAX(?addr) AS ?addr) (MAX(?address) AS ?address) WHERE {

  ?hs a lodum:LectureHall ;
     foaf:name ?name ;
     lodum:building ?building ;
     lodum:floor ?floor .

  ?building foaf:name ?buildingname ;
            vcard:adr ?addr .

  ?addr vcard:street-address ?address .

  FILTER langMatches(lang(?name),'de') .

}  GROUP BY ?hs ORDER BY ?name
";
	$fbs = sparql_get($query);
	return $fbs;
}

/*
	Query all Wohnheime
*/
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