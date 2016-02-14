'use strict';
/*
	Controller Wetter
*/
campusplanApp.controller('WetterController', 
	function($scope, $rootScope, $route, $routeParams, $location) {

	$scope.$route = $route;
	$scope.$location = $location;
	$scope.$routeParams = $routeParams;
	$rootScope.$currentPageName = 'Wetter';
	$rootScope.pageTitle = 'Wetter';
})