/* 
	Page controllers 
*/

campusplanApp.controller('MainController', function($scope, $route, $routeParams, $location, $rootScope, $http) {
	$scope.$route = $route;
	$scope.$location = $location;
	$scope.$routeParams = $routeParams;
	$rootScope.$navbarBgCol = "#009dd1";
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
			tileLayer: "http://otile{s}.mqcdn.com/tiles/1.0.0/map/{z}/{x}/{y}.jpeg", // Mapquest Open
			tileLayerOptions: {
				subdomains: "1234",
				attribution: 'Map data © OpenStreetMap contributors | Tiles Courtesy of <a href="http://www.mapquest.com/" target="_blank">MapQuest</a> <img src="http://developer.mapquest.com/content/osm/mq_logo.png">'
			},
		},
		icons: { 
			iconBlue: {
				iconUrl: "img/awesomemarkers-blue.png",
				iconSize: [30, 46],
				iconAnchor: [14, 43],
				popupAnchor: [1, -40],
				shadowUrl: "img/awesomemarkers-shadow.png",
				shadowAnchor: [10, 12],
				shadowSize: [36, 16]
			} 
		},
		orgMarkers: {}
	};

	// query Wetter api
	$scope.weatherLoading = $http.get('api/wetter.php')
	.success(function(data, status) {
		$scope.result = data;

		if(data.currently != null) {
			$scope.wetter = data;
			$scope.wetterSuccess = true;
			$scope.wetterFailed = false;
		} else {
			$scope.wetterSuccess = false;
			$scope.wetterFailed = true;
		}
	})
	.error(function(data, status) {
		$scope.data = data || "Request failed";
		$scope.status = status;
	});	
})
/*
	Controller Hauptseite
*/
.controller('HomeController', function($scope, $rootScope) {
	$rootScope.$currentPageName = "Default";
})
/*
	Controller Mensa
*/
.controller('MensenController', function($scope, $routeParams, $http, $rootScope) {
	var doW = new Date().getDay();
	$scope.name = "MensenController";
	$scope.params = $routeParams;
	$scope.mondayDate = getMonday(new Date());
	$scope.dayOfWeek = doW;
	$rootScope.$currentPageName = "Mensen";
	
	// chéck if it is saturday, sunday or monday
	// used in Mensaplan to expand Monday menu on these days
	$scope.expandMonday = (doW == 0 || doW == 1 || doW == 6);

	// query Mensa api
	$scope.mensaLoading = $http.get('api/mensen.php')
		.success(function(data, status) {
			$scope.result = data;
			if(data.results != null && data.results.bindings.length > 0) {
				$scope.mensaData = data;
				$scope.mensenQuerySuccess = true;
				$scope.mensenQueryFailed = false;
			} else {
				$scope.mensenQuerySuccess = false;
				$scope.mensenQueryFailed = true;
			}
		})
		.error(function(data, status) {
			$scope.data = data || "Request failed";
			$scope.status = status;
		});
})
/*
	Controller Karte
*/
.controller('KarteController', function($scope, $routeParams, $http, $rootScope, leafletData) {
	$scope.name = "KarteController";
	$scope.params = $routeParams;
	$rootScope.$currentPageName = "Karte";

	// set map defaults
	angular.extend($scope, $rootScope.leafletDefaults);

	// Reset the view after AngularJS has loaded the page
	// Otherwise tiles don't load completely
	leafletData.getMap().then(function(map) {
		$scope.$watch('$viewContentLoaded', function() {
			map.invalidateSize();
			map.setView([51.96362, 7.61309], 16);
		});
	});

	$scope.karteLoading = $http.post('api/karte.php', { data: $scope.params.identifier })
	.success(function(data, status) {
		$scope.status = status;
		$scope.data = data;

		if(data != null) {
			$scope.orga = data;
			$scope.orgaSearchSuccess = true;
			$scope.orgaSearchFailed = false;

			/*
				Data returned from API has these attributes: lat, lng, message
				We add the custom icon attribute here.
			*/
			$scope.markers = angular.forEach($scope.orga, function(e, i){
					e.icon = $scope.icons.iconBlue;
					return e;
				});

			// load api results into marker variable on map
			angular.extend($scope, {
				orgMarkers: $scope.markers
			});

			// Reset the view after AngularJS has loaded the page
			// Otherwise tiles don't load completely
			// (again)
			leafletData.getMap().then(function(map) {
				$scope.$watch('$viewContentLoaded', function() {
					map.invalidateSize();
					//map.setView([$scope.orga.lat.value, $scope.orga.long.value], 16);
				});
			});
		} else {
			$scope.orgaSearchSuccess = false;
			$scope.orgaSearchFailed = true;
		}


	})
	.error(function(data, status) {
		$scope.data = data || "Request failed";
		$scope.status = status;
	});
	
})
/*
	Controller Uni A-Z
*/
.controller('UniA-ZController', function($scope, $routeParams, $http, $rootScope, $timeout) {
	$scope.name = "UniA-ZController";
	$scope.params = $routeParams;
	$rootScope.$currentPageName = "Uni-a-z";

	/* Request a search at api/unia-z.php and return results */
	$scope.search = function() {
		if($scope.inputsearchterm.length == 1 || $scope.inputsearchterm.length > 2) {
			$scope.searchterm = $scope.inputsearchterm;

			// query user search input to uni a-z api
			$scope.AZLoading = $http.post('api/unia-z.php', { data: $scope.searchterm })
			.success(function(data, status) {
				$scope.result = data;
				if(data.results != null && data.results.bindings.length > 0) {
					$scope.orgas = data.results.bindings;
					$scope.orgaSearchSuccess = true;
					$scope.orgaSearchFailed = false;
				} else {
					$scope.orgaSearchSuccess = false;
					$scope.orgaSearchFailed = true;
				}
			})
			.error(function(data, status) {
				$scope.data = data || "Request failed";
				$scope.status = status;			
			});
		}
	}
	$scope.searchletter = function(letter) {
		$scope.inputsearchterm = letter;
		$scope.search();
	}
	
	// search-as-you-type with 500ms delay
	var _timeout;
	$scope.liveSearch = function() {
		if(_timeout){
		  $timeout.cancel(_timeout);
		}
		_timeout = $timeout(function(){
		  $scope.search();
		  _timeout = null;
		},500);
	}
})
/*
	Controller Info
	nothing fancy here since it's just more or less a static site
*/
.controller('InfoController', function($scope, $rootScope) {
	$rootScope.$currentPageName = "Info";
})
/*
	Controller Organization
*/
.controller('OrgaController', function($scope, $routeParams, $http, leafletData, $document, $rootScope, localStorageService, FavService, WicketService) {
	$scope.name = "OrgaController";
	$scope.params = $routeParams;
	$rootScope.$currentPageName = "Orga";
	$scope.orgaHasCoords = false;
	$scope.inFav = false;

	// set the map default settings
	angular.extend($scope, $rootScope.leafletDefaults);

	// add or remove orga
	$scope.addFav = function() {
		FavService.toggleFav($scope.orga);
		$scope.inFav = ($scope.inFavs() < 0) ? false : true;
	}
	// check position in favourites list
	$scope.inFavs = function() {
		return FavService.inFavs($scope.orga);
	}

	// query orga from API
	$scope.orgaLoading = $http.post('api/orga.php', { data: $scope.params.identifier })
	.success(function(data, status) {
		$scope.status = status;
		$scope.data = data;
		$scope.result = data;
		if(data.results != null && data.results.bindings.length > 0) {
			$scope.orga = data.results.bindings[0];
			console.log($scope.orga);
			$scope.orgaSearchSuccess = true;
			$scope.orgaSearchFailed = false;
			$scope.inFav = ($scope.inFavs() < 0) ? false : true;
			if($scope.orga.lat != null && $scope.orga.long != null) {
				// organization has lat/lon coordinates in results
				$scope.orgaHasCoords = true;
			} else {
				// try to geocode address otherwise
				$scope.geocodeLoading = $http.post('api/geocode.php', { data: $scope.orga.address.value })
				.success(function(data, status) {
					if(Object.keys(data.results).length > 0) {
						$scope.orga.lat = [];
						$scope.orga.lat.value = data.results[0].locations[0].displayLatLng.lat;
						$scope.orga.long = [];
						$scope.orga.long.value = data.results[0].locations[0].displayLatLng.lng;
						$scope.orgaHasCoords = true;
					}
				});
			}
			
			if($scope.orga.wkt != null && $scope.orga.wkt.value != null) {
				// has wkt geometries
				var geometryObj;
				geometryObj = WicketService.WktToObj($scope.orga.wkt.value);
				
				angular.extend($scope, {
					geojson: { 
						data: geometryObj, 
						style: { weight: 2, opacity: 1, color: 'red', fillOpacity: 0 } 
					}
				});
			}

			// show map if organization has coordinates
			$scope.$watch('orgaHasCoords', function() {
				// prepare map defaults
				angular.extend($scope, {
					orgMarkers: {
						orgaMarker: {
							lat: parseFloat($scope.orga.lat.value),
							lng: parseFloat($scope.orga.long.value),
							focus: true,
							message: $scope.orga.name.value,
							icon: $scope.icons.iconBlue
						}
					}
				});

				// Reset the view after AngularJS has loaded the page
				// Otherwise tiles don't load completely
				leafletData.getMap().then(function(map) {
					$scope.$watch('$viewContentLoaded', function() {
						map.invalidateSize();
						map.setView([$scope.orga.lat.value, $scope.orga.long.value], 17);
					});
				});
			});

			// load sub organizations (used for Fachbereiche)
			$scope.orgaLoading = $http.post('api/orgasub.php', { data: $scope.params.identifier })
			.success(function(data, status) {
				if(Object.keys(data.results.bindings).length > 0) {
					console.log(data);
					$scope.orgaHasSubOrga = true;
					$scope.orgaSubOrgaData = data.results.bindings;
				}
			});

			// load mensaplan for mensa organizations
			$scope.orgaLoading = $http.get('http://localhost:9000/all', { data: $scope.params.identifier })
			.success(function(data, status) {
				if(Object.keys(data).length > 0) {
					console.log(data);
					$scope.orgaHasMensa = true;
					$scope.mensaData = data;
				}
			});
		} else {
			$scope.orgaSearchSuccess = false;
			$scope.orgaSearchFailed = true;
		}


	})
	.error(function(data, status) {
		$scope.data = data || "Request failed";
		$scope.status = status;
	});
})
/*
	Controller Fachbereiche 
	similar to Hörsäle, Wohnheime
*/
.controller('FachbereicheController', function($scope, $rootScope, $http) {
	$rootScope.$currentPageName = "Fachbereiche";
	$scope.splitNamePattern = /(Fachbereich [0-9]{2}) - (.+)/;

	$scope.FachbereicheLoading = $http.get('api/fachbereiche.php')
	.success(function(data, status) {
		$scope.result = data;
		if(data.results != null && data.results.bindings.length > 0) {
			$scope.fachbereiche = data.results.bindings;
			$scope.fachbereichSuccess = true;
			$scope.fachbereichFailed = false;
		} else {
			$scope.fachbereichSuccess = false;
			$scope.fachbereichFailed = true;
		}
	})
	.error(function(data, status) {
		$scope.data = data || "Request failed";
		$scope.status = status;			
	});
})
/*
	Controller Hörsäle
	similar to Fachbereiche, Wohnheime
*/
.controller('HoersaeleController', function($scope, $rootScope, $http) {
	$rootScope.$currentPageName = "Hoersaele";

	$scope.HoersaeleLoading = $http.get('api/hoersaele.php')
	.success(function(data, status) {
		$scope.result = data;
		if(data.results != null && data.results.bindings.length > 0) {
			$scope.hoersaele = data.results.bindings;
			$scope.hoersaeleSuccess = true;
			$scope.hoersaeleFailed = false;
		} else {
			$scope.hoersaeleSuccess = false;
			$scope.hoersaeleFailed = true;
		}
	})
	.error(function(data, status) {
		$scope.data = data || "Request failed";
		$scope.status = status;			
	});
})
/*
	Controller Wohnheime
	similar to Fachbereiche, Hörsäle
*/
.controller('WohnheimeController', function($scope, $rootScope, $http, $filter) {
	$rootScope.$currentPageName = "Wohnheime";

	$scope.WohnheimeLoading = $http.get('api/wohnheime.php')
	.success(function(data, status) {
		$scope.result = data;
		if(data.results != null && data.results.bindings.length > 0) {
			$scope.wohnheime = data.results.bindings;
			$scope.wohnheimeSuccess = true;
			$scope.wohnheimeFailed = false;
		} else {
			$scope.wohnheimeSuccess = false;
			$scope.wohnheimeFailed = true;
		}
	})
	.error(function(data, status) {
		$scope.data = data || "Request failed";
		$scope.status = status;			
	});
})
/*
	Controller Wetter
*/
.controller('WetterController', function($scope, $rootScope, $route, $routeParams, $location) {
	$scope.$route = $route;
	$scope.$location = $location;
	$scope.$routeParams = $routeParams;
	$rootScope.$currentPageName = "Wetter";
})
/*
	Controller ULB-Katalog
*/
.controller('UlbController', function($scope, $rootScope, $route, $routeParams, $location) {
	$scope.$route = $route;
	$scope.$location = $location;
	$scope.$routeParams = $routeParams;
	$rootScope.$currentPageName = "ULB-Katalog";
})
/*
	Controller Favoriten
*/
.controller('FavoritenController', function($scope, $rootScope, $http, $filter, localStorageService, FavService, WicketService) {
	$rootScope.$currentPageName = "Favoriten";
	$scope.favoriten = localStorageService.get('favoriten');

	// remove all orgas / clear the local storage
	$scope.clearFavs = function() {
		$scope.favoriten = {};
		$scope.favoriten.orgas = [];
		return localStorageService.clearAll();
	}

	// remove orga from localstorage, then reload fav list
	$scope.removeFav = function(orga) {
		FavService.toggleFav(orga);
		$scope.favoriten = localStorageService.get('favoriten');		
	}

	// do stuffs
	$scope.stuff = function($event) {
		$($event.target).toggleClass('fav');
	}
})
/*
	Dummy Controller
*/
.controller('NotImplementedController', function($scope, $rootScope, $route, $routeParams, $location) {
	$scope.$route = $route;
	$scope.$location = $location;
	$scope.$routeParams = $routeParams;
	$rootScope.$currentPageName = "NotImplemented";
});