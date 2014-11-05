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
	delay: 300,
	minDuration: 250
})
/* 
	Page controllers 
*/
.controller('MainController', function($scope, $route, $routeParams, $location, $rootScope) {
	$scope.$route = $route;
	$scope.$location = $location;
	$scope.$routeParams = $routeParams;
	$rootScope.$navbarBgCol = "#009dd1";
})
.controller('HomeController', function($scope, $rootScope) {
	$rootScope.$navbarBgCol = "#009dd1";
})
.controller('MensenController', function($scope, $routeParams, $http, $rootScope) {
	$scope.name = "MensenController";
	$scope.params = $routeParams;
	$scope.mondayDate = getMonday(new Date());
	$scope.dayOfWeek = new Date().getDay();
	$rootScope.$navbarBgCol = "#2a6c8c";

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
	$rootScope.$navbarBgCol = "#7ab51d";
	
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
		icons: { iconA: {iconUrl: "img/marker.png" } }
			/*iconUrl: "img/marker.png",
			iconSize:     [5, 5],
			iconAnchor:   [2, 2]

			shadowUrl: 'img/leaf-shadow.png',
			shadowSize:   [50, 64],
			shadowAnchor: [4, 62],
		}*/
	});

	leafletData.getMap().then(function(map) {
		$scope.$watch('$viewContentLoaded', function() {
			map.invalidateSize();
			map.setView([51.96362, 7.61309], 14);
		});
	});

	$http.post('api/karte.php', { data: $scope.params.identifier })
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
	$rootScope.$navbarBgCol = "#009dd1";

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
	$rootScope.$navbarBgCol = "#ffd400";
})
.controller('OrgaController', function($scope, $routeParams, $http, leafletData, $document, $rootScope) {
	$scope.name = "OrgaController";
	$scope.params = $routeParams;
	$rootScope.$navbarBgCol = "#7ab51d";

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

	$http.post('api/orga.php', { data: $scope.params.identifier })
	.success(function(data, status) {
		$scope.status = status;
		$scope.data = data;
		$scope.result = data;
		if(data.results.bindings != null && data.results.bindings.length != 0) {
			$scope.orga = data.results.bindings[0];
			$scope.orgaSearchSuccess = true;
			$scope.orgaSearchFailed = false;

			//$scope.center.lat = parseFloat($scope.orga.lat.value);
			//$scope.center.lng = parseFloat($scope.orga.long.value);

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
	$rootScope.$navbarBgCol = "#009dd1";

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
	$rootScope.$navbarBgCol = "#009dd1";

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
	$rootScope.$navbarBgCol = "#009dd1";

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
	$rootScope.$navbarBgCol = "#009dd1";
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
		;
	// configure html5 to get links working on jsfiddle
	$locationProvider.html5Mode(true).hashPrefix('!');
});

