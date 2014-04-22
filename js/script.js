function getMonday(d) {
  d = new Date(d);
  var day = d.getDay(),
      diff = d.getDate() - day + (day == 0 ? -6:1); // adjust when day is sunday
  return new Date(d.setDate(diff));
}
var monthsGerman = ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'];

angular.module('ngRouteExample', ['ngRoute'])
/* 
	Page controllers 
*/
.controller('MainController', function($scope, $route, $routeParams, $location) {
	$scope.$route = $route;
	$scope.$location = $location;
	$scope.$routeParams = $routeParams;
})
.controller('MensenController', function($scope, $routeParams, $http) {
	$scope.name = "MensenController";
	$scope.params = $routeParams;
	$scope.mondayDate = getMonday(new Date());

	$http.get('api/mensen.php')
	.success(function(data, status) {
		$scope.result = data;
		if(data.results != null && data.results.bindings.length != 0) {
			$scope.mensaData = data.results.bindings;
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
.controller('KarteController', function($scope, $routeParams) {
	$scope.name = "KarteController";
	$scope.params = $routeParams;
})
.controller('UniA-ZController', function($scope, $routeParams, $http) {
	$scope.name = "UniA-ZController";
	$scope.params = $routeParams;

	/* Request a search at api/unia-z.php and return results */
	$scope.search = function() {
		if($scope.inputsearchterm.length > 0) {
			$scope.searchterm = $scope.inputsearchterm;
			$http.post('api/unia-z.php', { data: $scope.searchterm })
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
.controller('OrgaController', function($scope, $routeParams, $http) {
	$scope.name = "OrgaController";
	$scope.params = $routeParams;

	$http.post('api/orga.php', { data: $scope.params.identifier })
	.success(function(data, status) {
		$scope.status = status;
		$scope.data = data;
		$scope.result = data;
		if(data.results.bindings != null && data.results.bindings.length != 0) {
			$scope.orga = data.results.bindings[0];
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
})
/*
	Config
*/
.config(function($routeProvider, $locationProvider) {
	$routeProvider
		.when('/', {
			templateUrl: 'templates/home.html'
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
			templateUrl: 'templates/info.html'
		})
		.when('/Organisation/:identifier', {
			templateUrl: 'templates/organisation.html',
			controller: 'OrgaController'
		});
	// configure html5 to get links working on jsfiddle
	$locationProvider.html5Mode(true).hashPrefix('!');
});

var map = L.map('map');
L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
    maxZoom: 18
}).addTo(map);

map.setView([51.505, -0.09], 13);