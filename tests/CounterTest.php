<?php

use Mockery as m;
use Conner\Likeable\LikeableTrait;

class CounterTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		m::close();
	}
	
	public function testLike()
	{
		$likable = m::mock('LikeableStub[incrementLikeCount]');
		$likable->shouldReceive('incrementLikeCount')->andReturn(null);
		
		$likable->like(0);
	}
	
	public function testUnlike()
	{
		$likable = m::mock('LikeableStub[decrementLikeCount]');
		$likable->shouldReceive('decrementLikeCount')->andReturn(null);
		
		$likable->unlike(0);
	}
	
}

class LikeableStub extends \Illuminate\Database\Eloquent\Model {
	use LikeableTrait;

	public function incrementLikeCount() {}
	public function decrementLikeCount() {}
}