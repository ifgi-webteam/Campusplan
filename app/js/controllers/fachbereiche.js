'use strict';
/*
	Controller Fachbereiche 
	similar to Hörsäle, Wohnheime
*/
campusplanApp.controller('FachbereicheController', 
	function($scope, $rootScope, $http) {

	$rootScope.$currentPageName = 'Fachbereiche';
	$rootScope.pageTitle = 'Fachbereiche';
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
		$scope.data = data || 'Request failed';
		$scope.status = status;			
	});
})