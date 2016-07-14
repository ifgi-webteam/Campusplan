'use strict';

// load Angular & modules
var campusplanApp = angular.module('CampusplanApp', 
	['ngRoute', 'leaflet-directive', 'cgBusy', 'LocalStorageModule', 'angulartics', 'angulartics.piwik']);

// configure cgBusy for loading animations
campusplanApp.value('cgBusyDefaults',{
	message:'',
	backdrop: true,
	templateUrl: 'templates/loading.html',
	delay: 1000,
	minDuration: 0
});

// set mapbox token here
var mapboxToken = '###INSERTTOKENHERE###';

// set default variables
campusplanApp.run(function($rootScope, $http, $interval) {
	$rootScope.$navbarBgCol = '#009dd1';

	// Leaflet map defaults
	$rootScope.leafletDefaults = {
		mapCenter: {
			lat: 51.96362,
			lng: 7.61309,
			zoom: 12
		},
		mapDefaults: {
			scrollWheelZoom: true, 
			minZoom: 10,
			// Mapbox tiles
			tileLayer: "https://api.mapbox.com/v4/mapbox.streets/{z}/{x}/{y}.png?access_token="+mapboxToken, 
			tileLayerOptions: {
				subdomains: "1234",
				attribution: "© <a href='https://www.mapbox.com/about/maps/'>Mapbox</a> © <a href='http://www.openstreetmap.org/copyright'>OpenStreetMap</a>"
			},
		},
		icons: { 
			iconBlue: {
				iconUrl: 'img/awesomemarkers-blue.png',
				iconSize: [30, 46],
				iconAnchor: [14, 43],
				popupAnchor: [1, -40],
				shadowUrl: 'img/awesomemarkers-shadow.png',
				shadowAnchor: [10, 12],
				shadowSize: [36, 16]
			},
			iconGreen: {
				iconUrl: 'img/awesomemarkers-green.png',
				iconSize: [30, 46],
				iconAnchor: [14, 43],
				popupAnchor: [1, -40],
				shadowUrl: 'img/awesomemarkers-shadow.png',
				shadowAnchor: [10, 12],
				shadowSize: [36, 16]
			} 
		},
		orgMarkers: {},
		paths: {},
		controls: { custom: [] }
	};

	// add leaflet-locatecontrol (geolocation / "Where am I?") button to map
	$rootScope.leafletDefaults.controls.custom.push(  
		L.control.locate({
			drawCircle: true,
			setView: true,
			metric: true,
			//keepCurrentZoomLevel: true,
			markerClass: L.marker,
			markerStyle: { 
				icon: L.icon($rootScope.leafletDefaults.icons.iconGreen) 
			},
			circleStyle: { 
				stroke:true, 
				fillColor: '#7ab51d', 
				color:'#7ab51d', 
				weight:3 
			},
			locateOptions: { 
				//minZoom: 12,  // not implemented
				maxZoom: 16 
			},
			showPopup: true,
			strings: {
				title: 'Wo bin ich?',
				popup: 'Du befindest dich innerhalb von {distance} Metern um diesem Punkt',
				outsideMapBoundsMsg: 'Du bist außerhalb des Kartenausschnitts'
			}
		}) 
	);

	// query Wetter api
	function fetchWeatherData(){
		$rootScope.weatherLoading = $http.get('api/wetter.php')
		.success(function(data, status) {
			$rootScope.result = data;

			if(data.currently != null) {
				$rootScope.wetter = data;
				$rootScope.wetterSuccess = true;
				$rootScope.wetterFailed = false;
			} else {
				$rootScope.wetterSuccess = false;
				$rootScope.wetterFailed = true;
			}
		})
		.error(function(data, status) {
			$rootScope.data = data || 'Request failed';
			$rootScope.status = status;
		});	
	}
	fetchWeatherData();
	$interval(fetchWeatherData, 10*60*1000);
});
