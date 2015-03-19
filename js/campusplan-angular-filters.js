campusplanApp.filter('directionIcon', function() {
	return function(maneuver) {
		if(maneuver.turnType == 0) {
			return 'dir'+maneuver.direction;
		} else {
			var num;
			switch(maneuver.turnType) {
				//straight
				case 0:
				case 18:
					img = 'turn'+1;
					break;
				//right
				case 2:
				case 3:
				case 8:
					img = 'turn'+3;
					break;
				//slight right
				case 1:
				case 10:
				case 12:
				case 14:
				case 16:
					img = 'turn'+2;
					break;
				//left
				case 5:
				case 6:
				case 9:
					img = 'turn'+7;
					break;
				//slight left
				case 7:
				case 11:
				case 13:
				case 15:
				case 17:
					img = 'turn'+8;
					break;
				case -1:
					img = 'stop';
					break;
				default:
					img = 'turn'+0;
					break;
			}
			return img;
		}
		return false;
	};
});
