'use strict';
/*
	Controller ULB-Katalog
*/
campusplanApp.controller('UlbController', 
	function($scope, $rootScope, $route, $routeParams, $location) {

	$scope.$route = $route;
	$scope.$location = $location;
	$scope.$routeParams = $routeParams;
	$rootScope.$currentPageName = 'ULB-Katalog';
	$rootScope.pageTitle= 'ULB-Katalog';
})