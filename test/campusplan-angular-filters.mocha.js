describe('filter directionIcon', function() {
	var directionIconFilter;

	//beforeEach(module('CampusplanApp'));
	beforeEach(function() {
		module('CampusplanApp')
		inject(function(_directionIconFilter_) {
			directionIconFilter = _directionIconFilter_;
		});
	});

	it('should show the correct turn direction', function() {
		expect( directionIconFilter({turnType: 1, direction: 5}) ).to.equal('turn2');
	});
})