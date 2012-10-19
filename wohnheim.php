<?php 

	// load the additional CSS, we're gonna show a map:
	$leaftletCSS = true;
	
	require_once("head.php");
	
	$org_uri = $_GET["uri"]; 
?>

<body> 

<div data-role="page" class="type-interior" id="page">		

	<?php getHeader("Wohnheim", "back"); ?>

	<div data-role="content">
		
		<div class="content-primary">
		
		<?php 
			if(!isset($_GET['lang'])){
				$lang = 'de';
			} else {
				$lang = $_GET['lang'];
			}
			
			$orgDetails = getOrgDetails($org_uri, $lang);				
		?>	

		</div><!--/content-primary -->		
		
		<?php getMenu("wohnheime.php"); ?> 	

	</div><!-- /content -->		
</div><!-- /page -->

<?php addMapCode($orgDetails); ?>

</body>
</html>
<?php

// generates the Leaflet JS Code
function addMapCode($orgDetails){

	//only show the map if we have any geodata:
	
	if(isset($orgDetails->wkt->value) || (isset($orgDetails->lat->value) && isset($orgDetails->long->value))){
	
	// spit out the JS code the works for buildings with and without WKT 
	
	echo"
	<script src='js/leaflet0.3.1.js'></script>
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
	";
	
	
	if(isset($orgDetails->wkt->value)){ // handle orgs with WKTs for buildings
		
		
		include_once('geoPHP.inc');
	
		//clean the WKT from the CDATA stuff to make geoPHP swallow it:
		$wkt = str_ireplace("<![CDATA[ <http://www.opengis.net/def/crs/OGC/1.3/CRS84> ", "", $orgDetails->wkt->value);
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
				"name": "'.$orgDetails->buildingname->value.'",
				"amenity": "building",
		        "popupContent": "<b>'.$orgDetails->buildingname->value.'</b></br>'.$orgDetails->address->value.'"
		    },'.substr($json_geometry, 1); 
	echo "	
		var center = new L.LatLng(" .$y. ", ".$x."); 
		map.setView(center, 17).addLayer(mapquest);
		var geojsonLayer = new L.GeoJSON();
		
		geojsonLayer.on('featureparse', function (e) {
		    if (e.properties && e.properties.popupContent){
		        e.layer.bindPopup(e.properties.popupContent);
		    }
		});
		
		var geoJSONfeature = ".$json_geometry.";
		geojsonLayer.addGeoJSON(geoJSONfeature);
						
		map.addLayer(geojsonLayer);
		";
	
		
	}else{  //handle orgs that only have lat/lon
		echo "
		var center = new L.LatLng(".$orgDetails->lat->value.", ".$orgDetails->long->value.");
		map.setView(center, 17).addLayer(mapquest);
	
		var marker = new L.Marker(center);
		map.addLayer(marker);	
		marker.bindPopup(\"<b>".$orgDetails->buildingname->value."</b></br>".$orgDetails->address->value."\");
		";	
	}
		echo"			
		// fixes the problem where some map tiles are not shown initally:
		L.Util.requestAnimFrame(map.invalidateSize,map,!1,map._container);
	});	
	</script>
		";


	}else{ // no map
		
	echo"	
	<script>
		$('#map').remove();
	</script>
	";	
	}
	
	// now make sure the page also shows the things we have inserted:
	echo"
	<script>
		$('#page').trigger('create');
	</script>
	";
}

// loads the details for this organization

function getOrgDetails($org, $lang = "de"){
	
	$query = "
	
	prefix foaf: <http://xmlns.com/foaf/0.1/> 
	prefix geo: <http://www.w3.org/2003/01/geo/wgs84_pos#> 
	prefix vcard: <http://www.w3.org/2006/vcard/ns#>
	prefix lodum: <http://vocab.lodum.de/helper/>
	prefix ogc: <http://www.opengis.net/ont/OGC-GeoSPARQL/1.0/>
	
	SELECT ?name ?homepage ?address ?buildingname ?lat ?long ?wkt WHERE {
	  
	  <".$org."> foaf:name ?name.
	  
	  OPTIONAL { <".$org."> foaf:homepage ?homepage . }
	  
	  OPTIONAL { <".$org."> vcard:adr ?address . }
	  
	  OPTIONAL { <".$org."> lodum:building ?building . 
	             ?building foaf:name ?buildingname ;
	                       geo:lat ?lat ;
	                       geo:long ?long .
	             
	             OPTIONAL { ?building ogc:hasGeometry ?geometry .
	                        ?geometry ogc:asWKT ?wkt . }           
	           }                                                                                                                       .
	}
	";
	$orgDetails = sparql_get($query);	
	
	if( !isset($orgDetails) ) {
		print "<li>Fehler beim Abruf der Informationen über diese Organisation:</li>";
		echo "<p><strong>Anfrage</strong>: ".$query."</p>";
	}else{		

		// only start if there are any results:
		if($orgDetails->results->bindings){
			
			$thisOrg = $orgDetails->results->bindings[0];			
			
			echo '<h2>'.$thisOrg->name->value.'</h2>';
			
			echo '</ul>
				<ul data-role="listview" data-inset="true">
					<li id="themap"></li>
				</u>
								
				<ul data-role="listview" data-inset="true">
					<li>';
				
				if(isset($thisOrg->buildingname->value)){
					echo '<h3>'.$thisOrg->buildingname->value.'</h3><p>'.$thisOrg->address->value.'</p>';
				}else{
					echo $thisOrg->address->value;					
				}
				
				echo '</li>
					<li><h3>Wegbeschreibung per...</h3>
			 			<div data-role="controlgroup" data-type="horizontal">
			 				<a href="" data-role="button">Leeze</a>
			 				<a href="" data-role="button">Zu Fuß</a>
			 				<a href="http://efa.vrr.de/vrr/XSLT_TRIP_REQUEST2?language=de&itdLPxx_hideNavigationBar=1&itdLPxx_transpCompany=stwms&sessionID=0&requestID=0&language=de&useRealtime=1&place_origin=MS&type_origin=address&name_origin=Hubertistraße+12&place_destination=MS&type_destination=address&name_destination='.urlencode($thisOrg->address->value).'" data-role="button">Bus</a>
			 				<a href="" data-role="button">Auto</a>
			 			</div>
			 		</li>
			 		<li><a href="'.$thisOrg->homepage->value.'">Website</a></li>
			'; 		
 			echo '</ul>
 			'; 
 			
 			echo '
 					
 				  
 			';
 			
 			return $thisOrg;
 		}
 	}

}

?>