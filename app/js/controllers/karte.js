'use strict';
/*
	Controller Karte
*/
campusplanApp.controller('KarteController', 
	function($scope, $routeParams, $http, $rootScope, leafletData) {

	$scope.name = 'KarteController';
	$scope.params = $routeParams;
	$rootScope.$currentPageName = 'Karte';
	$rootScope.pageTitle = 'Karte';

	// set map defaults
	angular.extend($scope, $rootScope.leafletDefaults);



	// Reset the view after AngularJS has loaded the page
	// Otherwise tiles don't load completely
	leafletData.getMap().then(function(map) {
		map.attributionControl.setPrefix('');

		$scope.$watch('$viewContentLoaded', function() {
			//map.invalidateSize();
			map.setView([51.96362, 7.61309], 16);
		});
	});


	$scope.karteLoading = $http.post('api/karte.php', { data: $scope.params.identifier })
	.success(function(data, status) {
		$scope.status = status;
		$scope.data = data;

		if(data != null) {
			$scope.orga = data;
			$scope.orgaSearchSuccess = true;
			$scope.orgaSearchFailed = false;

			/*
				Data returned from API has these attributes: lat, lng, message
				We add the custom icon attribute here.
			*/
			$scope.markers = angular.forEach($scope.orga, function(e, i){
				e.icon = $scope.icons.iconBlue;
				return e;
			});

			// load api results into marker variable on map
			angular.extend($scope, {
				orgMarkers: $scope.markers
			});
		} else {
			$scope.orgaSearchSuccess = false;
			$scope.orgaSearchFailed = true;
		}


	})
	.error(function(data, status) {
		$scope.data = data || 'Request failed';
		$scope.status = status;
	});
	
})