'use strict';
/*
	Controller Favoriten
*/
campusplanApp.controller('FavoritenController', 
	function($scope, $rootScope, $http, $filter, localStorageService, FavService) {

	$rootScope.$currentPageName = 'Favoriten';
	$rootScope.pageTitle = 'Favoriten';
	$scope.favoriten = localStorageService.get('favoriten');

	// remove all orgas / clear the local storage
	$scope.clearFavs = function() {
		$scope.favoriten = {};
		$scope.favoriten.orgas = [];
		return localStorageService.clearAll();
	};

	// remove orga from localstorage, then reload fav list
	$scope.removeFav = function(orga) {
		FavService.toggleFav(orga);
		$scope.favoriten = localStorageService.get('favoriten');		
	};

	// do stuffs
	$scope.stuff = function($event) {
		$($event.target).toggleClass('fav');
	};
})