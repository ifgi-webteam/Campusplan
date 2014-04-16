angular.module('ngRouteExample', ['ngRoute'])
/* 
	Controllers 
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
.controller('UniA-ZController', function($scope, $routeParams) {
	$scope.name = "ChapterController";
	$scope.params = $routeParams;
})
/*
	Config
*/
.config(function($routeProvider, $locationProvider) {
	$routeProvider
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
		});

	// configure html5 to get links working on jsfiddle
	$locationProvider.html5Mode(true).hashPrefix('!');
});