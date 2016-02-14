'use strict';
/*
	Controller Uni A-Z
*/
campusplanApp.controller('UniA-ZController', 
	function($scope, $routeParams, $http, $rootScope, $timeout) {

	$scope.name = 'UniA-ZController';
	$scope.params = $routeParams;
	$rootScope.$currentPageName = 'Uni-a-z';
	$rootScope.pageTitle = 'Uni A-Z';


	/* Request a search at api/unia-z.php and return results */
	$scope.search = function() {
		if($scope.inputsearchterm.length == 1 || $scope.inputsearchterm.length > 2) {
			$scope.searchterm = $scope.inputsearchterm;

			// query user search input to uni a-z api
			$scope.AZLoading = $http.post('api/unia-z.php', { data: $scope.searchterm })
			.success(function(data, status) {
				$scope.result = data;
				if(data.results != null && data.results.bindings.length > 0) {
					$scope.orgas = data.results.bindings;
					$scope.orgaSearchSuccess = true;
					$scope.orgaSearchFailed = false;
				} else {
					$scope.orgaSearchSuccess = false;
					$scope.orgaSearchFailed = true;
				}
			})
			.error(function(data, status) {
				$scope.data = data || 'Request failed';
				$scope.status = status;			
			});
		}
	};
	$scope.searchletter = function(letter) {
		$scope.inputsearchterm = letter;
		$scope.search();
	};
	
	// search-as-you-type with 500ms delay
	var _timeout;
	$scope.liveSearch = function() {
		if(_timeout){
		  $timeout.cancel(_timeout);
		}
		_timeout = $timeout(function(){
		  $scope.search();
		  _timeout = null;
		},500);
	}
})