/*
	Config for 
		- template<->controller association
		- routing
		- locationprovider
		- localstorage
*/
campusplanApp.config(function($routeProvider, $locationProvider, localStorageServiceProvider) {
	$routeProvider
		.when('/', {
			templateUrl: 'templates/home.html',
			controller: 'HomeController',
		})
		.when('/Mensen/', {
			templateUrl: 'templates/mensen.html',
			controller: 'MensenController',
		})
		.when('/Karte/', {
			templateUrl: 'templates/karte.html',
			controller: 'KarteController'
		})
		.when('/UniA-Z/', {
			templateUrl: 'templates/uni-a-z.html',
			controller: 'UniA-ZController'
		})
		.when('/Info/', {
			controller: 'InfoController',
			templateUrl: 'templates/info.html'
		})
		.when('/Organisation/:identifier*', {
			templateUrl: 'templates/organisation.html',
			controller: 'OrgaController'
		})
		.when('/Fachbereiche/', {
			templateUrl: 'templates/fachbereiche.html',
			controller: 'NotImplementedController'
		})
		.when('/Hörsäle/', {
			templateUrl: 'templates/hoersaele.html',
			controller: 'HoersaeleController'
		})
		.when('/Wohnheime/', {
			templateUrl: 'templates/wohnheime.html',
			controller: 'WohnheimeController'
		})
		.when('/Favoriten/', {
			templateUrl: 'templates/favoriten.html',
			controller: 'FavoritenController'
		})
		.when('/ULB-Katalog/', {
			templateUrl: 'templates/ulb.html',
			controller: 'UlbController'
		})
		.when('/Wetter/', {
			templateUrl: 'templates/wetter.html',
			controller: 'WetterController'
		});

	$locationProvider.html5Mode(true).hashPrefix('!');

	localStorageServiceProvider
		.setPrefix('campusplan')
		.setStorageCookie(0, '/');

});