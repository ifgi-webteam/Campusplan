'use strict';
/*
	Controller Organization
*/
campusplanApp.controller('OrgaController', 
	function($scope, $routeParams, $http, leafletData, $document, $rootScope, localStorageService, FavService, WicketService) {

	$scope.name = 'OrgaController';
	$scope.params = $routeParams;
	$rootScope.$currentPageName = 'Orga';
	$rootScope.pageTitle = 'Einrichtungen';
	$scope.orgaHasCoords = false;
	$scope.inFav = false;
	$scope.hasRoute = false;


	// set the map default settings
	angular.extend($scope, $rootScope.leafletDefaults);

	// add or remove orga
	$scope.addFav = function() {
		FavService.toggleFav($scope.orga);
		$scope.inFav = ($scope.inFavs() < 0) ? false : true;
	};
	// check position in favourites list
	$scope.inFavs = function() {
		return FavService.inFavs($scope.orga);
	};

	// query orga from API
	$scope.orgaLoading = $http.post('api/orga.php', { data: $scope.params.identifier })
	.success(function(data, status) {
		$scope.status = status;
		$scope.data = data;
		$scope.result = data;

		if(data.results != null && data.results.bindings.length > 0) {
			$scope.orga = data.results.bindings[0];
			$rootScope.pageTitle = 'Einrichtungen: '+$scope.orga.name.value;

			$scope.orgaSearchSuccess = true;
			$scope.orgaSearchFailed = false;
			$scope.inFav = ($scope.inFavs() < 0) ? false : true;
			if($scope.orga.lat != null && $scope.orga.long != null) {
				// organization has lat/lon coordinates in results
				$scope.orgaHasCoords = true;
			} else {
				// try to geocode address otherwise
				var address = ($scope.orga.address != null) ? $scope.orga.address.value : $scope.orga.street.value +', '+  $scope.orga.zip.value +' '+ $scope.orga.city.value;
				$scope.geocodeLoading = $http.post('api/geocode.php', { data: address })
				.success(function(data, status) {
					if(Object.keys(data.results).length > 0) {
						$scope.orga.lat = [];
						$scope.orga.lat.value = data.results[0].locations[0].displayLatLng.lat;
						$scope.orga.long = [];
						$scope.orga.long.value = data.results[0].locations[0].displayLatLng.lng;
						$scope.orgaHasCoords = true;
					}
				});
			}
			
			if($scope.orga.wkt != null && $scope.orga.wkt.value != null) {
				// has wkt geometries
				var geometryObj;
				geometryObj = WicketService.WktToObj($scope.orga.wkt.value);
				
				angular.extend($scope, {
					geojson: { 
						data: geometryObj, 
						style: { weight: 2, opacity: 1, color: 'red', fillOpacity: 0 } 
					}
				});
			}

			// show map if organization has coordinates
			$scope.$watch('orgaHasCoords', function() {
				// prepare map defaults
				angular.extend($scope, {
					orgMarkers: {
						orgaMarker: {
							lat: parseFloat($scope.orga.lat.value),
							lng: parseFloat($scope.orga.long.value),
							//focus: true,
							icon: $scope.icons.iconBlue,
							//message: $scope.orga.name.value
						}
					}
				});

				// Reset the view after AngularJS has loaded the page
				// Otherwise tiles don't load completely
				leafletData.getMap().then(function(map) {
					//var popup = L.popup({ offset:[-95,-36], minWidth:150, closeButton: false })
					//	.setContent($scope.orga.name.value)
					//	.setLatLng([$scope.orgMarkers.orgaMarker.lat, $scope.orgMarkers.orgaMarker.lng]);
					//popup.openOn(map);

					map.attributionControl.setPrefix('');

					map.setView([$scope.orga.lat.value, $scope.orga.long.value], 17);
					//$scope.$watch('$viewContentLoaded', function() {
					//	map.invalidateSize();
					//	map.setView([$scope.orga.lat.value, $scope.orga.long.value], 17);
					//});
				});
			});

			// load sub organizations (used for Fachbereiche)
			$scope.orgaLoading = $http.post('api/orgasub.php', { data: $scope.params.identifier })
			.success(function(data, status) {
				if(Object.keys(data.results.bindings).length > 0) {
					$scope.orgaHasSubOrga = true;
					$scope.orgaSubOrgaData = data.results.bindings;
				}
			});

			// load mensaplan for mensa organizations
			$scope.orgaLoading = $http.post('api/mensen.php', { data: $scope.params.identifier.split('/').pop() })
			.success(function(data, status) {
				if(Object.keys(data).length > 0) {
					$scope.orgaHasMensa = true;
					$scope.mensaData = data;
				}
			});

			// query user's Geolocation
			// if successful call routing API
			// save the position info in variable
			$scope.userPosition = {};
			$scope.getUserLocation = function(type) {
				$scope.waitForGeolocation = true;
				$scope.routingType = type;
				if($scope.userPosition.coords) {
					$scope.getRoute($scope.userPosition);
				} else {
					if (navigator.geolocation) {
						navigator.geolocation.getCurrentPosition($scope.getRoute, function() {
							$scope.geoLocationError = true;
						});
						return true;
					} 
					$scope.geoLocationError = true;
					return false;
				}
			};

			// call routing API
			// type = pedestrian, bicycle or fastest (car)
			$scope.getRoute = function(position) {
				$scope.userPosition = position;
				$scope.waitForGeolocation = false;

				$scope.routeLoading = $http.post('api/routing.php', { 
					type: $scope.routingType, 
					fromLat: $scope.userPosition.coords.latitude, 
					fromLng: $scope.userPosition.coords.longitude, 
					toLat: $scope.orga.lat.value, 
					toLng: $scope.orga.long.value })
				.success(function(data, status) {
					$scope.route = data;
					$scope.hasRoute = true;

					// iterate over result's 'shapePoints'
					// lat and lng aren't stored in pairs, so we skip every 2nd entry in that list
					$scope.routeNodes = [];
					angular.forEach($scope.route.route.shape.shapePoints, function(point, index) {
						if(index%2==0)$scope.routeNodes.push( [$scope.route.route.shape.shapePoints[index],$scope.route.route.shape.shapePoints[index+1]] );
					});

					// same as starting point
					$scope.orgMarkers.routeStart = {
						lat: $scope.route.route.locations[0].latLng.lat,
						lng: $scope.route.route.locations[0].latLng.lng,
						icon: $scope.icons.iconGreen
					}

					// route destination point, can be omitted since already on map
					//$scope.orgMarkers.routeEnd = {
					//	lat: $scope.route.route.locations[1].latLng.lat,
					//	lng: $scope.route.route.locations[1].latLng.lng,
					//	icon: $scope.icons.iconGreen
					//}

					// add polyline to map
					angular.extend($scope, {
						paths: { 
							route: {
								color: 'blue',
								weight: 3,
								latlngs: $scope.routeNodes
							}
						}
					});

					// Reset the view / zoom to route bounding box
					leafletData.getMap().then(function(map) {
						map.fitBounds([$scope.route.route.boundingBox.ul, $scope.route.route.boundingBox.lr]);
					});
					
				});


			}

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