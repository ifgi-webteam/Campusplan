<?php 
	// load the additional CSS, we're gonna show a map:
	$leaftletCSS = true;	
	require_once("head.php");
?>

<body> 

<div data-role="page" class="type-interior" id="page">		

	<?php getHeader("Karte", "back"); ?>

	<div data-role="content">
		
		<div class="content-primary">
		
		<?php 
			if(!isset($_GET['lang'])){
				$lang = 'de';
			} else {
				$lang = $_GET['lang'];
			}
			
			$mapData = getMapData($lang);				
		?>	

		</div><!--/content-primary -->		
		
		<?php getMenu("karte.php"); ?> 	

	</div><!-- /content -->		
</div><!-- /page -->

<?php addMapCode($mapData); ?>

</body>
</html>
<?php

// generates the Leaflet JS Code
function addMapCode($mapData){

	// spit out the JS code the works for buildings with and without WKT 
	
	echo"
	<script src='js/leaflet.js'></script>
	<script>
	 
	 // wait until the page is loaded:
	 $( '#page' ).live( 'pageinit', function(event){
	 	
	 	// first remove and add the map div - jQuery mobile issue:
	 	$('#map').remove();
	 	$('#themap').append('<div id=\"map\"></div>');
	 	
	 	var map = new L.Map('map', {
	 		zoomControl: false
	 	});
	 	var mapquestUrl = 'http://{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png',
	 	subDomains = ['otile1','otile2','otile3','otile4'],
	 	mapquestAttrib = '';
	 	var mapquest = new L.TileLayer(mapquestUrl, {maxZoom: 18, attribution: mapquestAttrib, subdomains: subDomains});
	 	
	 	var center = new L.LatLng(51.9663, 7.6099); 
	 	map.setView(center, 14).addLayer(mapquest);
	 	var geojsonLayer = new L.GeoJSON();
	 	
	 	geojsonLayer.on('featureparse', function (e) {
	 	    if (e.properties && e.properties.popupContent){
	 	        e.layer.bindPopup(e.properties.popupContent);
	 	    }
	 	});
	 	
	 	var circleOptions = {
	 	        color: 'blue',
	 	        fillColor: 'blue',
	 	        fillOpacity: 0.5
	 	    };
	";
	
	
	foreach($mapData->results->bindings as $building){
		
		if(isset($building->wkt->value)){ // handle orgs with WKTs for buildings
			
			
			include_once('geoPHP.inc');
		
			//clean the WKT from the CDATA stuff to make geoPHP swallow it:
			$wkt = str_ireplace("<![CDATA[ <http://www.opengis.net/def/crs/OGC/1.3/CRS84> ", "", $building->wkt->value);
			$wkt = str_ireplace(" ]]>", "", $wkt);			
			
				
			$wkt_reader = new WKT();
			$geometry = $wkt_reader->read($wkt,TRUE);
			$centroid = $geometry->centroid();
			$x = $centroid->x();
			$y = $centroid->y();
			$json_writer = new GeoJSON();
			$json_geometry = $json_writer->write($geometry);
		
			
			// let's add some info details to the geojson:
			$json_geometry = '{"properties": {
					"name": "'.$building->name->value.'",
					"amenity": "building",
			        "popupContent": "<b>'.$building->address->value.'</b>"
			    },'.substr($json_geometry, 1); 
		echo "	
			
			var geoJSONfeature = ".$json_geometry.";
			geojsonLayer.addData(geoJSONfeature);
							
			map.addLayer(geojsonLayer);
			";
		
			
		}else if(isset($building->lat->value) && isset($building->long->value)){  //handle orgs that only have lat/lon
			echo "
			var circle = new L.Circle(new L.LatLng(".$building->lat->value.", ".$building->long->value."), 10, circleOptions);
			
			circle.bindPopup(\"<b>".$building->address->value."</b>\");
			map.addLayer(circle);
			";	
		}else{
			// nada
		}
		
	}
		echo"			
		// fixes the problem where some map tiles are not shown initally:
		L.Util.requestAnimFrame(map.invalidateSize,map,!1,map._container);
	});	
	</script>
		";


	
	
	// now make sure the page also shows the things we have inserted:
	echo"
	<script>
		$('#page').trigger('create');
	</script>
	";
}

// loads the details for this organization

function getMapData($lang = "de"){
	
	$query = "
	
	prefix foaf: <http://xmlns.com/foaf/0.1/> 
	prefix geo: <http://www.w3.org/2003/01/geo/wgs84_pos#> 
	prefix vcard: <http://www.w3.org/2006/vcard/ns#>
	prefix lodum: <http://vocab.lodum.de/helper/>
	prefix ogc: <http://www.opengis.net/ont/OGC-GeoSPARQL/1.0/>
	prefix dbp-ont: <http://dbpedia.org/ontology/> 
	
	SELECT ?building ?name ?address ?lat ?long ?wkt WHERE {
		  
	  ?building a dbp-ont:building ;
	            foaf:name ?name.
		  	  
	  OPTIONAL { ?building vcard:adr ?address . }
		  
	  OPTIONAL { ?building geo:lat ?lat ;
	                       geo:long ?long .
	             
	  OPTIONAL { ?building ogc:hasGeometry ?geometry .
	             ?geometry ogc:asWKT ?wkt . }           
	           }                                                                                                                       .
	}
		";
	$mapData = sparql_get($query);	
	
	if( !isset($mapData) ) {
		print "<li>Fehler beim Abruf der Informationen der Gebäudedaten:</li>";
		echo "<p><strong>Anfrage</strong>: ".$query."</p>";
	}else{		

		// only start if there are any results:
		if($mapData->results->bindings){
			
			echo '<ul data-role="listview" data-inset="true">
					<li id="themap"></li>
				</u>
					';			
				
//				echo '</li>
//					<li><h3>Wegbeschreibung per...</h3>
//			 			<div data-role="controlgroup" data-type="horizontal">
//			 				<a href="" data-role="button">Leeze</a>
//			 				<a href="" data-role="button">Zu Fuß</a>
//			 				<a href="http://efa.vrr.de/vrr/XSLT_TRIP_REQUEST2?language=de&itdLPxx_hideNavigationBar=1&itdLPxx_transpCompany=stwms&sessionID=0&requestID=0&language=de&useRealtime=1&place_origin=MS&type_origin=address&name_origin=Hubertistraße+12&place_destination=MS&type_destination=address&name_destination='.urlencode($thisOrg->address->value).'" data-role="button">Bus</a>
//			 				<a href="" data-role="button">Auto</a>
//			 			</div>
//			 		</li>
//			 		<li><a href="'.$thisOrg->homepage->value.'">Website</a></li>
//			'; 		
 			
 			return $mapData;
 		}
 	}

}

?>