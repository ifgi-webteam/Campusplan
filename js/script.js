function getMonday(d) {
  d = new Date(d);
  var day = d.getDay(),
	  diff = d.getDate() - day + (day == 0 ? -6:1); // adjust when day is sunday
  return new Date(d.setDate(diff));
}
var monthsGerman = ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'];
var daysGerman = ['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'];

angular.module('CampusplanApp', ['ngRoute', 'leaflet-directive', 'cgBusy'])
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

	$scope.weatherLoading = $http.get('api/wetter.php')
	.success(function(data, status) {
		$scope.result = data;

		if(data.temp != null) {
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
.controller('HomeController', function($scope, $rootScope) {
	$rootScope.$currentPageName = "Default";
})
.controller('MensenController', function($scope, $routeParams, $http, $rootScope) {
	var doW = new Date().getDay();
	$scope.name = "MensenController";
	$scope.params = $routeParams;
	$scope.mondayDate = getMonday(new Date());
	$scope.dayOfWeek = doW;
	$rootScope.$currentPageName = "Mensen";
	
	// chéck if it is saturday, sunday or monay
	// used in Mensaplan to expand Monday menu on these days
	$scope.expandMonday = (doW == 0 || doW == 1 || doW == 6);

	$scope.mensaLoading = $http.get('api/mensen.php')
		.success(function(data, status) {
			$scope.result = data;
			if(data != null) { // && data.results.bindings.length != 0) {
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
.controller('KarteController', function($scope, $routeParams, $http, $rootScope, leafletData) {
	$scope.name = "KarteController";
	$scope.params = $routeParams;
	$rootScope.$currentPageName = "Karte";
	
	angular.extend($scope, {
		mapCenter: {
			lat: 51.96362,
			lng: 7.61309,
			zoom: 14
		},
		mapDefaults: {
			scrollWheelZoom: true, 	
			tileLayer: "http://otile{s}.mqcdn.com/tiles/1.0.0/map/{z}/{x}/{y}.jpeg",
			tileLayerOptions: {
				subdomains: "1234",
				attribution: 'Map data © OpenStreetMap contributors | Tiles Courtesy of <a href="http://www.mapquest.com/" target="_blank">MapQuest</a> <img src="http://developer.mapquest.com/content/osm/mq_logo.png">'
			},
		},
		icons: { 
			iconA: {
				iconUrl: "img/marker.png",
				iconSize:     [6, 6],
				iconAnchor:   [2, 2]
			} 
		}
	});

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

			angular.extend($scope, {
				orgMarkers: data
			});

			// Reset the view after AngularJS has loaded the page
			// Otherwise tiles don't load completely
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
.controller('UniA-ZController', function($scope, $routeParams, $http, $rootScope) {
	$scope.name = "UniA-ZController";
	$scope.params = $routeParams;
	$rootScope.$currentPageName = "Uni-a-z";

	/* Request a search at api/unia-z.php and return results */
	$scope.search = function() {
		if($scope.inputsearchterm.length > 0) {
			$scope.searchterm = $scope.inputsearchterm;
			$scope.AZLoading = $http.post('api/unia-z.php', { data: $scope.searchterm })
			.success(function(data, status) {
				$scope.result = data;
				if(data.results != null && data.results.bindings.length != 0) {
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
})
.controller('InfoController', function($scope, $rootScope) {
	$rootScope.$currentPageName = "Info";
})
.controller('OrgaController', function($scope, $routeParams, $http, leafletData, $document, $rootScope) {
	$scope.name = "OrgaController";
	$scope.params = $routeParams;
	$rootScope.$currentPageName = "Orga";

	angular.extend($scope, {
		mapCenter: {
			lat: 51.96362,
			lng: 7.61309,
			zoom: 14
		},
		mapDefaults: {
			scrollWheelZoom: true, 	
			tileLayer: "http://otile{s}.mqcdn.com/tiles/1.0.0/map/{z}/{x}/{y}.jpeg",
			tileLayerOptions: {
				subdomains: "1234",
				attribution: 'Map data © OpenStreetMap contributors | Tiles Courtesy of <a href="http://www.mapquest.com/" target="_blank">MapQuest</a> <img src="http://developer.mapquest.com/content/osm/mq_logo.png">'
			}
		},
		orgMarkers: {},
	});

	var myIcon = { iconA: { iconUrl: "img/marker.png" } }

	$scope.orgaLoading = $http.post('api/orga.php', { data: $scope.params.identifier })
	.success(function(data, status) {
		$scope.status = status;
		$scope.data = data;
		$scope.result = data;
		if(data.results.bindings != null && data.results.bindings.length != 0) {
			$scope.orga = data.results.bindings[0];
			$scope.orgaSearchSuccess = true;
			$scope.orgaSearchFailed = false;

			if($scope.orga.lat.value != null && $scope.orga.long.value != null) {
				$scope.orgaHasCoords = true;
				/* geocoding from address here? */
			}
			angular.extend($scope, {
				orgMarkers: {
					orgaMarker: {
						lat: parseFloat($scope.orga.lat.value),
						lng: parseFloat($scope.orga.long.value),
						focus: true,
						message: $scope.orga.name.value,
						icon: myIcon.iconA
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
.controller('FachbereicheController', function($scope, $rootScope, $http) {
	$rootScope.$currentPageName = "Fachbereiche";
	$scope.splitNamePattern = /(Fachbereich [0-9]{2}) - (.+)/;

	$scope.FachbereicheLoading = $http.get('api/fachbereiche.php')
	.success(function(data, status) {
		$scope.result = data;
		if(data.results != null && data.results.bindings.length != 0) {
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
.controller('HoersaeleController', function($scope, $rootScope, $http) {
	$rootScope.$currentPageName = "Hoersaele";

	$scope.HoersaeleLoading = $http.get('api/hoersaele.php')
	.success(function(data, status) {
		$scope.result = data;
		if(data.results != null && data.results.bindings.length != 0) {
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
.controller('WohnheimeController', function($scope, $rootScope, $http) {
	$rootScope.$currentPageName = "Wohnheime";

	$scope.WohnheimeLoading = $http.get('api/wohnheime.php')
	.success(function(data, status) {
		$scope.result = data;
		if(data.results != null && data.results.bindings.length != 0) {
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
.controller('NotImplementedController', function($scope, $rootScope, $route, $routeParams, $location) {
	$scope.$route = $route;
	$scope.$location = $location;
	$scope.$routeParams = $routeParams;
	$rootScope.$currentPageName = "Default";
})
/*
	Config
*/
.config(function($routeProvider, $locationProvider) {
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
			templateUrl: 'templates/empty.html',
			controller: 'NotImplementedController'
		})
		.when('/ULB-Katalog/', {
			templateUrl: 'templates/empty.html',
			controller: 'NotImplementedController'
		})
		.when('/Wetter/', {
			templateUrl: 'templates/wetter.html',
			controller: 'NotImplementedController'
		});

	$locationProvider.html5Mode(true).hashPrefix('!');
});


