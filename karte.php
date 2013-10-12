<?php 
	require_once("functions.php");
	checkCache();
	getHead();
?>	

	<script>
 
	 // wait until the page is loaded:
	 // $( document ).delegate("#mapPage", "pagecreate", function() {
  	 // wait until the page is loaded:
	 $(window).load(function(){

	 	$('#map').addClass('mapFullScreen');
	 	$('#map').show();

	 	var map = new L.Map('map', {
	 		zoomControl: false
	 	});
	 	
	 	var proxyUrl = 'tiles.php?z={z}&x={x}&y={y}';
	 	
	 	var osm = new L.TileLayer(proxyUrl);
	 	
	 	var center = new L.LatLng(51.9663, 7.6099); 
	 	map.setView(center, 14).addLayer(osm);

		// fixes the problem where some map tiles are not shown initally:
		map.invalidateSize();

		var geojsonLayer = new L.GeoJSON();
		 	
	 	geojsonLayer.on('featureparse', function (e) {
	 		alert('e.properties');
	 	    if (e.properties && e.properties.popupContent){
	 	        e.layer.bindPopup(e.properties.popupContent);
	 	    }
	 	});
 	
	 	var circleOptions = {
	 	    color: 'blue',
 	        fillColor: 'blue',
 	        fillOpacity: 0.5
 	    };

 <?php 

	include_once('geoPHP.inc');

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
	
	foreach($mapData->results->bindings as $building){
		
		if(isset($building->wkt->value)){ // handle orgs with WKTs for buildings
			
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
		
			
		} else if(isset($building->lat->value) && isset($building->long->value)){  // handle orgs that only have lat/lon
			echo "
			var circle = new L.Circle(new L.LatLng(".$building->lat->value.", ".$building->long->value."), 10, circleOptions); 
			";
			
			// circle.bindPopup(\"<b>".$building->address->value."</b>\");
			// map.addLayer(circle);
			// ";	
		}	
	}	
	
?>

	
	});	
		
	</script>
	
	</div><!-- /content -->		
</div><!-- /page -->

<?php getFoot(); 

flushCache();
?>

