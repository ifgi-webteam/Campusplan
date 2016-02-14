'use strict';
/*
	Controller Wohnheime
	similar to Fachbereiche, Hörsäle
*/
campusplanApp.controller('WohnheimeController', function($scope, $rootScope, $http) {
	$rootScope.$currentPageName = 'Wohnheime';
	$rootScope.pageTitle = 'Wohnheime';

	$scope.WohnheimeLoading = $http.get('api/wohnheime.php')
	.success(function(data, status) {
		$scope.result = data;
		if(data.results != null && data.results.bindings.length > 0) {
			$scope.wohnheime = data.results.bindings;
			$scope.wohnheimeSuccess = true;
			$scope.wohnheimeFailed = false;
		} else {
			$scope.wohnheimeSuccess = false;
			$scope.wohnheimeFailed = true;
		}
	})
	.error(function(data, status) {
		$scope.data = data || 'Request failed';
		$scope.status = status;			
	});
})