'use strict';
/*
	Controller Info
	nothing fancy here since it's just more or less a static site
*/
campusplanApp.controller('InfoController', function($scope, $rootScope) {
	$rootScope.$currentPageName = 'Info';
	$rootScope.pageTitle = 'Info';
})