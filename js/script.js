// get monday of this week
function getMonday(d) {
  d = new Date(d);
  var day = d.getDay(),
	  diff = d.getDate() - day + (day == 0 ? -6:1); // adjust when day is sunday
  return new Date(d.setDate(diff));
}
// german date names
var monthsGerman = ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'];
var daysGerman = ['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'];

// load Angular & modules
angular.module('CampusplanApp', ['ngRoute', 'leaflet-directive', 'cgBusy', 'LocalStorageModule'])
// configure cgBusy for loading animations
.value('cgBusyDefaults',{
	message:'',
	backdrop: true,
	templateUrl: 'templates/loading.html',
	delay: 1000,
	minDuration: 0
})
/* 
	Page controllers 
*/
.controller('MainController', function($scope, $route, $routeParams, $location, $rootScope, $http) {
	$scope.$route = $route;
	$scope.$location = $location;
	$scope.$routeParams = $routeParams;
	$rootScope.$navbarBgCol = "#009dd1";
	// Leaflet map defaults
	$rootScope.leafletDefaults = {
		mapCenter: {
			lat: 51.96362,
			lng: 7.61309,
			zoom: 14
		},
		mapDefaults: {
			scrollWheelZoom: true, 	
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
			map.setView([51.96362, 7.61309], 14);
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
		if($scope.inputsearchterm.length > 0) {
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
.controller('OrgaController', function($scope, $routeParams, $http, leafletData, $document, $rootScope, localStorageService) {
	$scope.name = "OrgaController";
	$scope.params = $routeParams;
	$rootScope.$currentPageName = "Orga";
	$scope.orgaHasCoords = false;

	// set the map default settings
	angular.extend($scope, $rootScope.leafletDefaults);

	// check if orga in favourites list
	// returns index nr. on success, -1 if false
	$scope.inFavs = function() {
		var favourites = localStorageService.get('favoriten');
		var dupe = -1;

		if(angular.isObject(favourites) && angular.isObject(favourites.orgas)) {
			for(i in favourites.orgas) {
				if(angular.equals(favourites.orgas[i], $scope.orga)) {
					dupe = i;
					break;
				}
			}
		}
		return dupe;
	}

	// add orga to favourites
	$scope.addFav = function() {
		var favourites = localStorageService.get('favoriten');

		if(angular.isObject(favourites) && angular.isObject(favourites.orgas)) {
			// check for duplicates
			// in case of duplicate, remove fav
			var dupe = $scope.inFavs();
			if(dupe != -1) {
				favourites.orgas.splice(dupe, 1); // remove favourite from list
				$scope.inFav = false;
			} else {
				favourites.orgas.push($scope.orga); // add favourite to list
				$scope.inFav = true;
			}
		} else {
			// if favorites are empty, create new object
			favourites = {};
			favourites.orgas = []
			favourites.orgas.push($scope.orga);
			$scope.inFav = true;
		}
		return localStorageService.set('favoriten', favourites);
	}

	// query orga from API
	$scope.orgaLoading = $http.post('api/orga.php', { data: $scope.params.identifier })
	.success(function(data, status) {
		$scope.status = status;
		$scope.data = data;
		$scope.result = data;
		if(data.results != null && data.results.bindings.length > 0) {
			$scope.orga = data.results.bindings[0];
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
						map.setView([$scope.orga.lat.value, $scope.orga.long.value], 16);
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
			$scope.orgaLoading = $http.post('api/mensen.php', { data: $scope.params.identifier })
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
	Controller Favoriten
*/
.controller('FavoritenController', function($scope, $rootScope, $http, $filter, localStorageService) {
	$rootScope.$currentPageName = "Favoriten";
	$scope.favoriten = localStorageService.get('favoriten');

	$scope.clearFavs = function() {
		$scope.favoriten = {};
		$scope.favoriten.orgas = [];
		return localStorageService.clearAll();
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
})
/*
	Config for template<->controller association
*/
.config(function($routeProvider, $locationProvider, localStorageServiceProvider) {
	$routeProvider
		.when('/', {
			templateUrl: 'templates/home.html',
			controller: 'HomeController',
		})
		.when('/Mensen/', {
			templateUrl: 'templates/mensen.html',
			controller: 'MensenController',
		})
		.when('/Karte/', {
			templateUrl: 'templates/karte.html',
			controller: 'KarteController'
		})
		.when('/UniA-Z/', {
			templateUrl: 'templates/uni-a-z.html',
			controller: 'UniA-ZController'
		})
		.when('/Info/', {
			controller: 'InfoController',
			templateUrl: 'templates/info.html'
		})
		.when('/Organisation/:identifier*', {
			templateUrl: 'templates/organisation.html',
			controller: 'OrgaController'
		})
		.when('/Fachbereiche/', {
			templateUrl: 'templates/fachbereiche.html',
			controller: 'NotImplementedController'
		})
		.when('/Hörsäle/', {
			templateUrl: 'templates/hoersaele.html',
			controller: 'HoersaeleController'
		})
		.when('/Wohnheime/', {
			templateUrl: 'templates/wohnheime.html',
			controller: 'WohnheimeController'
		})
		.when('/Favoriten/', {
			templateUrl: 'templates/favoriten.html',
			controller: 'FavoritenController'
		})
		.when('/ULB-Katalog/', {
			templateUrl: 'templates/empty.html',
			controller: 'NotImplementedController'
		})
		.when('/Wetter/', {
			templateUrl: 'templates/wetter.html',
			controller: 'WetterController'
		});

	$locationProvider.html5Mode(true).hashPrefix('!');

	localStorageServiceProvider
		.setPrefix('campusplan')
		.setStorageCookie(0, '/');
});


