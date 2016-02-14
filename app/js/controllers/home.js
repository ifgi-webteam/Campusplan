'use strict';
/*
	Controller Hauptseite
*/
campusplanApp.controller('HomeController', function($scope, $rootScope) {
	$rootScope.$currentPageName = 'Default'; // used for background color CSS selector
	$rootScope.pageTitle = ''; // displayed in browser title bar
});