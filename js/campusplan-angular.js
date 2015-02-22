// load Angular & modules
var campusplanApp = angular.module('CampusplanApp', ['ngRoute', 'leaflet-directive', 'cgBusy', 'LocalStorageModule'])

// configure cgBusy for loading animations
campusplanApp.value('cgBusyDefaults',{
	message:'',
	backdrop: true,
	templateUrl: 'templates/loading.html',
	delay: 1000,
	minDuration: 0
});
