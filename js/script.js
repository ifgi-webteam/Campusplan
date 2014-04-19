angular.module('ngRouteExample', ['ngRoute'])
/* 
	Page controllers 
*/
.controller('MainController', function($scope, $route, $routeParams, $location) {
	$scope.$route = $route;
	$scope.$location = $location;
	$scope.$routeParams = $routeParams;
})
.controller('MensenController', function($scope, $routeParams) {
	$scope.name = "MensenController";
	$scope.params = $routeParams;
})
.controller('KarteController', function($scope, $routeParams) {
	$scope.name = "KarteController";
	$scope.params = $routeParams;
})
.controller('UniA-ZController', function($scope, $routeParams, $http) {
	$scope.name = "UniA-ZController";
	$scope.params = $routeParams;

	$scope.search = function() {
		$scope.params.letter = $scope.searchword;
		$http.post('/Campusplan/search.php', { data: $scope.searchword })
		.success(function(data, status) {
			$scope.status = status;
			$scope.data = data;
			$scope.result = data;
			$scope.orgas = data.results.bindings;
		})
		.error(function(data, status) {
			$scope.data = data || "Request failed";
			$scope.status = status;			
		});
	}
	$scope.searchletter = function(lt) {
		$scope.params.letter = lt;
		$scope.searchword = lt;
		$scope.search();
	}
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
		});
	// configure html5 to get links working on jsfiddle
	$locationProvider.html5Mode(true).hashPrefix('!');
});