<?php
namespace Tests\Integration;

beforeEach(function () {
	parent::setUp();
});

afterEach(function () {
	parent::tearDown();
});

test('wc_youtube_videos() actually returns the instance of WC_Youtube_Videos', function () {
	// $this->assertEquals('WC_Youtube_Videos', );
	expect(\wc_youtube_videos())->toBeInstanceOf(\WC_Youtube_Videos::class);
});
