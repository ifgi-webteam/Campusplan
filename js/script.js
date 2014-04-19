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

	/* Request a search at search.php and return results */
	$scope.search = function() {
		$scope.params.letter = $scope.searchword;
		$http.post('api/unia-z.php', { data: $scope.searchword })
		.success(function(data, status) {
			$scope.status = status;
			$scope.data = data;
			$scope.result = data;
			if(data.results.bindings != null && data.results.bindings.length != 0) {
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
	$scope.searchletter = function(letter) {
		$scope.params.letter = letter;
		$scope.searchword = letter;
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