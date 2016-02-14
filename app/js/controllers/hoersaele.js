'use strict';
/*
	Controller Hörsäle
	similar to Fachbereiche, Wohnheime
*/
campusplanApp.controller('HoersaeleController', 
	function($scope, $rootScope, $http) {

	$rootScope.$currentPageName = 'Hoersaele';
	$rootScope.pageTitle = 'Hörsaele';

	$scope.HoersaeleLoading = $http.get('api/hoersaele.php')
	.success(function(data, status) {
		$scope.result = data;
		if(data.results != null && data.results.bindings.length > 0) {
			$scope.hoersaele = data.results.bindings;
			$scope.hoersaeleSuccess = true;
			$scope.hoersaeleFailed = false;
		} else {
			$scope.hoersaeleSuccess = false;
			$scope.hoersaeleFailed = true;
		}
	})
	.error(function(data, status) {
		$scope.data = data || 'Request failed';
		$scope.status = status;			
	});
})