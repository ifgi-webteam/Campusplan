describe('Array', function() {
	describe('getMonday()', function() {
		it('should return last monday\'s date', function() {
			assert.equal(
				1426536000000,
				getMonday('2015-03-22T20:00:00.000Z').getTime()
			);
		});
	});
});
