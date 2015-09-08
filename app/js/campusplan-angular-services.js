'use strict';

/*
	Angular Services
*/
// read/save/add/remove favourites
campusplanApp.factory('FavService', function(localStorageService) {
	return {
		test: function() {
			console.log(localStorageService.get('favoriten'));
		},

		// check if orga in favourites list
		// returns index nr. on success, -1 if false
		inFavs: function(orga) {
			var favourites = localStorageService.get('favoriten');
			var dupe = -1;

			if(angular.isObject(favourites) && angular.isObject(favourites.orgas)) {
				for(var i in favourites.orgas) {
					if(angular.equals(favourites.orgas[i], orga)) {
						dupe = i;
						break;
					}
				}
			}
			return dupe;
		},

		// add or remove orga to favourites
		toggleFav: function(orga) {
			var favourites = localStorageService.get('favoriten');

			if(angular.isObject(favourites) && angular.isObject(favourites.orgas)) {
				// check for duplicates
				// in case of duplicate, remove fav
				var dupe = this.inFavs(orga);
				if(dupe != -1) {
					favourites.orgas.splice(dupe, 1); // remove favourite from list
					//$scope.inFav = false;
				} else {
					favourites.orgas.push(orga); // add favourite to list
					//$scope.inFav = true;
				}
			} else {
				// if favorites are empty, create new object
				favourites = {};
				favourites.orgas = [];
				favourites.orgas.push(orga);
				//$scope.inFav = true;
			}
			return localStorageService.set('favoriten', favourites);
		}
	};
});

// Wicket library
// convert 'Well Known Text' geometries to GeoJSON objects
campusplanApp.service('WicketService', function() {
	
	this.WktToObj = function(wktInput) {
		var wkt = new Wkt.Wkt();
		wkt.read(wktInput);
		return wkt.toJson();//{reverseInnerPolygons: true}
	};

});