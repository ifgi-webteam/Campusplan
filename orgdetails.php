<?php 

	// load the additional CSS, we're gonna show a map:
	$leaftletCSS = true;
	
	require_once("functions.php");
	
	$org_uri   = $_GET["org_uri"]; 
	
	getHead();
?>

<div class="container">
		
		<?php 
			if(!isset($_GET['lang'])){
				$lang = 'de';
			} else {
				$lang = $_GET['lang'];
			}
			
			$orgDetails = getOrgDetails($org_uri, $lang);				
		?>	

	</div>

<?php addMapCode($orgDetails); ?>

<?php 
getFoot();


// generates the Leaflet JS Code
function addMapCode($orgDetails){

	//only show the map if we have any geodata:
	
	if(isset($orgDetails->wkt->value) || (isset($orgDetails->lat->value) && isset($orgDetails->long->value))){
	
	// spit out the JS code that works for buildings with and without WKT 
	
	echo"
	<script>
	
	function error(msg){
		alert(msg); // TODO: Make this a bit nicer
	}

	// wait until the page is loaded:
	$(function(event){

	 	// enable the navigation button:
	 	$('.route').click(function(){
	 		
	 		var id = $(this).attr('id');

	 		// get position via HTML5 geolocation API
	 		if (navigator.geolocation) {
	 			if(id == 'bus'){
	 				navigator.geolocation.getCurrentPosition(showBusRoute, error);
	 			} else {
	 				navigator.geolocation.getCurrentPosition(function(position){
	 					showRoute(position, id, map);	 					
	 				}, error);
	 			}
			} else {
				// todo - link to google maps only with destination, user has to put in start
			} 

	 		
	 	});


	 	$('#map').show();

	 	var map = new L.Map('map', {
	 		zoomControl: false
	 	});
	 	
	 	var osm = new L.TileLayer('tiles.php?z={z}&x={x}&y={y}', {
            attribution: ''
		});
	 	
	 	map.setView([51.9663, 7.6099], 14).addLayer(osm);
	 	
	 	map.on('locationfound', onLocationFound);	 		 	
	 	
	 	var destlat, destlng ; // we'll assign these later
	 	
	 	function onLocationFound(e) {
	 	    var marker = new L.Marker(e.latlng);
	 	    map.addLayer(marker);
	 	}
	 	
	 	// computes a route from currentLat/Lng to destLat/Lng
	 	// possible modes: bicycle, foot, car
	 	// languages: de, en (more? check http://developers.cloudmade.com/wiki/navengine/Documentation)
	 	function computeRoute(currentLat, currentLng, destLat, destLng, mode, lang){
	 		$.mobile.showPageLoadingMsg();
	 		$.ajax({
	 		    dataType: 'jsonp',
	 		    url: 'http://routes.cloudmade.com/' + cloudmadeAPIkey+'/api/0.3/'+currentLat+',' + currentLng + ',' + destLat + ',' + destLng + '/' + mode +'.js?lang=' + lang,	   
	 		    data: { 
	 		    	lang: lang 
	 		    }, 
	 		    success: function(json) {
	 			    
	 			    var polyline = L.polyline(json.route_geometry, {color: 'red'}).addTo(map);

					// zoom the map to the polyline
					map.fitBounds(polyline.getBounds());
	 		    	
	 		        // show instructions:
	 		        $.each(json.route_instructions, function(i){
	 		        	var thisInstruction = json.route_instructions[i];
	 		        	$('#instructions').append('<li class=\"ui-li ui-li-static ui-body-c ui-corner-top\">' + (i+1) + '. ' + thisInstruction[0] + ' (' + thisInstruction[4] + ')</li>');
	 		        })
	 		        
	 		        $('#page').trigger('create');
	 		        $.mobile.hidePageLoadingMsg();
	 		    }	   
	 		});
	 	}
	 	
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
	
		
		// let's add some info details to the geojson; they will be shown in the popup bubble:
		$json_geometry = '{'.substr($json_geometry, 1); 
	echo "	
		
		var center = new L.LatLng(" .$y. ", ".$x.");
		destlat =  " .$y. ";
		destlng =  " .$x. ";
		map.setView(center, 17);
		var geojsonLayer = new L.GeoJSON();
		
		geojsonLayer.on('featureparse', function (e) {
		    if (e.properties && e.properties.popupContent){
		        e.layer.bindPopup(e.properties.popupContent);
		    }
		});
		
		var geoJSONfeature = ".$json_geometry.";
		geojsonLayer.addData(geoJSONfeature);
						
		map.addLayer(geojsonLayer);
		";
	
		
	}else{  //handle orgs that only have lat/lon
		echo "
		var center = new L.LatLng(".$orgDetails->lat->value.", ".$orgDetails->long->value.");
		destlat =  " .$orgDetails->lat->value. ";
		destlng =  " .$orgDetails->long->value. ";
		map.setView(center, 17);
	
		var marker = new L.Marker(center);
		map.addLayer(marker);	
		";	
	}
		echo"			
		// fixes the problem where some map tiles are not shown initally:
		L.Util.requestAnimFrame(map.invalidateSize,map,!1,map._container);

		$('div.leaflet-control-attribution').hide();
	});	
	</script>
		";


	} else { // no lat/lon nor WKT - show notification that we don't have a map for this one:
		echo "
			<script>
				$(function(event){
					$('#address').after('<p class=\"lead alert\">Für diese Einrichtung steht leider keine Karte / Navigation zur Verfügung.</p>');
				});				
			</script>
		";		
	}
}

// loads the details for this organization

function getOrgDetails($org, $lang = "de"){
	
	$query = "

prefix foaf: <http://xmlns.com/foaf/0.1/> 
prefix geo: <http://www.w3.org/2003/01/geo/wgs84_pos#> 
prefix vcard: <http://www.w3.org/2006/vcard/ns#>
prefix lodum: <http://vocab.lodum.de/helper/>
prefix ogc: <http://www.opengis.net/ont/OGC-GeoSPARQL/1.0/>
prefix xsd: <http://www.w3.org/2001/XMLSchema#> 

SELECT DISTINCT ?name ?homepage ?address ?street ?zip ?city ?buildingaddress ?lat ?long ?wkt WHERE {
  
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

	";
	$orgDetails = sparql_get($query);	
	
	if( !isset($orgDetails) ) {
		print '<p class="alert alert-error">Fehler beim Abruf der Informationen über diese Organisation:</li>';		
	}else{		

		// only start if there are any results:
		if($orgDetails->results->bindings){
			
			$thisOrg = $orgDetails->results->bindings[0];			
			
			$orgName = $thisOrg->name->value;

			if(endsWith($orgName, " Institut für")){
				$orgName = "Institut für ".substr($orgName, 0, -13);
			}

			echo '<div class="row-fluid"><div class="span12" id="orgInfo"><h1><span id="favorite">&#9733;</span><span id="title">'.$orgName.'</span></h1>
						

				<span id="instructions"></span>
				

				<p class="lead" id="address">
				';
				
				$dest = '';
				$destAddr = '';

				// use the coords as destination for the navigation
				if((isset($thisOrg->lat->value) && isset($thisOrg->long->value))){
					$dest = $thisOrg->lat->value.','.$thisOrg->long->value;
				}


				// ... or the address
				if(isset($thisOrg->address->value)){
					$destAddr = urlencode($thisOrg->address->value);
					echo $thisOrg->address->value.' ';
				} else if(isset($thisOrg->street->value) && isset($thisOrg->zip->value) && isset($thisOrg->city->value)) {
					$destAddr = urlencode($thisOrg->street->value.', '.$thisOrg->zip->value.' '.$thisOrg->city->value);
					echo $thisOrg->street->value.', '.$thisOrg->zip->value.' '.$thisOrg->city->value.' ';
				}

				if(isset($thisOrg->homepage->value)){

					// remove http:// and trailing slash from the website for display:
					$www = str_replace('http://', '', $thisOrg->homepage->value);
					if ( endsWith($www, '/') ) { $www = substr($www, 0, -1); }

					echo '<a class="btn visible-phone" style="float:right; margin-left: 20px" href="'.$thisOrg->homepage->value.'">Website</a><p class="lead hidden-phone">Website: <a href="'.$thisOrg->homepage->value.'">'.$www.'</a></p>
					<p class="visible-phone">';
				}

				
				echo "</p>
					
					<script>
						// forward to google maps for public transport options
						function showBusRoute(position) {
			  
						  // add the following parameters to the URI in case we want to distinguish the 
					      // different routing options inside the web app at some point:
		
						  // dirflg=r: rail / public transport
						  // dirflg=w: walk
						  // default: car
		
						  var uri = 'https://maps.google.com/maps?saddr='+position.coords.latitude+','+position.coords.longitude+'&daddr=";
						  
						  if($destAddr != ''){ echo $destAddr; } else { echo $dest; }

						  echo "&hl=de&ie=UTF8&ttype=now&dirflg=r&noexp=0&noal=0&sort=def&mra=ltm&t=m&start=0';

						  location.href = uri;
		
						}

						// all other routing requests:
						function showRoute(position, mode, map) {
			  			  
						  
						  var url = 'route.php?coords='+position.coords.latitude+','+position.coords.longitude+',".$dest."&mode='+mode+'&lang=de';

						  console.log(url);

						  $.ajax({
					 		    url: url,	   
					 		    success: function(json) {
					 			    
					 		    	console.log(json);

					 			    var polyline = L.polyline(json.route_geometry, {color: 'red'});
					 			    map.addLayer(polyline);

									// zoom the map to the polyline
									map.fitBounds(polyline.getBounds());
					 		    	
					 		        // show instructions:
					 		        $.each(json.route_instructions, function(i){
					 		        	var thisInstruction = json.route_instructions[i];
					 		        	$('#instructions').append('<li class=\"ui-li ui-li-static ui-body-c ui-corner-top\">' + (i+1) + '. ' + thisInstruction[0] + ' (' + thisInstruction[4] + ')</li>');
					 		        })
					 		        
					 		        
					 		    }	   
					 		});						  
		
						}
					</script>
				
				";
				
				
				
				
					if(isset($thisOrg->wkt->value) || (isset($thisOrg->lat->value) && isset($thisOrg->long->value))){
						// echo '<a href="#" class="lead hidden-phone route">Navigation</a><a class="btn btn-info btn-phone btn-phone-right visible-phone route" href="#">Navigation</a>
						// '; ?>
						<div class="btn-group" style="width: 100%">
  							<button class="btn btn-warning"><img src="img/route.png" style="height: 1.3em" /></button>
  							<button class="btn route" id="bicycle">Fahrrad</button>
  							<button class="btn route" id="foot">Zufuß</button>
  							<button class="btn route" id="car">Auto</button>
  							<button class="btn route" id="bus">ÖPNV (<img src="img/google.png" style="height: 1.3em" />)</button>
						</div>
					<?php
			 		} //end if

			 	

			 	 
				
 			echo '</div>
 			</div>
 			';  			 			
 			
 			return $thisOrg;
 		}
 	}

}

?>  