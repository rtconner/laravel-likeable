<?php

use Mockery as m;
use Conner\Likeable\Likeable;

class CounterTest extends PHPUnit_Framework_TestCase
{
	public function tearDown()
	{
		m::close();
	}
	
	public function testLike()
	{
		$likeable = m::mock('LikeableStub[incrementLikeCount]');
		$likeable->shouldReceive('incrementLikeCount')->andReturn(null);
		
		$likeable->like(0);
	}
	
	public function testUnlike()
	{
		$likeable = m::mock('LikeableStub[decrementLikeCount]');
		$likeable->shouldReceive('decrementLikeCount')->andReturn(null);
		
		$likeable->unlike(0);
	}
	
}

class LikeableStub extends \Illuminate\Database\Eloquent\Model
{
	use Likeable;

	public function incrementLikeCount() {}
	public function decrementLikeCount() {}
}