'use strict';
/*
	Controller Mensa
*/
campusplanApp.controller('MensenController', 
	function($scope, $routeParams, $http, $rootScope, DateService) {

	var doW = new Date().getDay();
	$scope.name = 'MensenController';
	$scope.params = $routeParams;
	$scope.mondayDate = DateService.mondayOfThisWeek(new Date());
	$scope.dayOfWeek = doW;
	$rootScope.$currentPageName = 'Mensen';
	$rootScope.pageTitle = 'Mensaplan';

	// chéck if it is saturday, sunday or monday
	// used in Mensaplan to expand Monday menu on these days
	$scope.expandMonday = (doW == 0 || doW == 1 || doW == 6);

	// query Mensa api
	$scope.mensaLoading = $http.get('api/mensen.php')
		.success(function(data, status) {
			$scope.result = data;
			if(Object.keys(data).length > 0) {
				$scope.mensaData = data;
				$scope.mensenQuerySuccess = true;
				$scope.mensenQueryFailed = false;
			} else {
				$scope.mensenQuerySuccess = false;
				$scope.mensenQueryFailed = true;
			}
		})
		.error(function(data, status) {
			$scope.data = data || 'Request failed';
			$scope.status = status;
		});

	// when collapsing menu entries, place plus/minus signs accordingly 
	$('#content').on('show.bs.collapse', 'div.mensa-table', function(e){
		$(this).find('.fa.fa-angle-right').removeClass('fa-angle-right').addClass('fa-angle-down');
	});
	$('#content').on('hide.bs.collapse', 'div.mensa-table', function(e){
		$(this).find('.fa.fa-angle-down').removeClass('fa-angle-down').addClass('fa-angle-right');
	});
}).controller('MensenFrameController', 
	function($scope, $routeParams, $http, $rootScope) {

	$scope.name = "MensenFrameController";
	$scope.params = $routeParams;
	$rootScope.$currentPageName = "Mensen";
	$rootScope.pageTitle = "Mensaplan";
	
	var url = "https://muenster.my-mensa.de/index.php?v=4894167&hyp=1#" + $scope.params.name + "_tage";  // TODO: Change to https for iPhones
	$('#mensa_frame').attr('src', url);
})