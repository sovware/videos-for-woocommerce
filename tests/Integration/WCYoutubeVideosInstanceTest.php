<?php
namespace Tests\Integration;

beforeEach(function () {
	parent::setUp();
});

afterEach(function () {
	parent::tearDown();
});

test('get_wc_videos() actually returns the instance of WC_Videos', function () {
	// $this->assertEquals('WC_Videos', );
	expect(\get_wc_videos())->toBeInstanceOf(\WC_Videos::class);
});
