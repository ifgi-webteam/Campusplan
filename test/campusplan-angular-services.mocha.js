describe('service FavService', function() {
	var FavService;

	beforeEach(module('CampusplanApp'));
	beforeEach(	
		inject(function(_FavService_) {
			FavService = _FavService_;
		})
	);
	beforeEach(function() {
		localStorage.clear()
		FavService.toggleFav({orgName:'doesExist'});
		FavService.toggleFav({orgName:'doesExistAsWell'});
	});

	it('should return -1 when favorite not in list', function() {
		expect( FavService.inFavs({orgName:'doesNotExist'}) ).to.equal(-1);
	});

	it('should return >=0 when favorite in list', function() {
		expect( FavService.inFavs({orgName:'doesExist'}) ).to.equal('0');
		expect( FavService.inFavs({orgName:'doesExistAsWell'}) ).to.equal('1');
	});

	it('should remove favorites from list', function() {
		FavService.toggleFav({orgName:'doesExist'});
		FavService.toggleFav({orgName:'doesExistAsWell'});
		expect( FavService.inFavs({orgName:'doesExist'}) ).to.equal(-1);
		expect( FavService.inFavs({orgName:'doesExistAsWell'}) ).to.equal(-1);
	});

})