<?php

namespace Conner\Tests\Likeable;

use Illuminate\Database\Eloquent\Model;
use Mockery as m;
use Conner\Likeable\Likeable;

class CounterBaseTest extends BaseTestCase
{
    public function testLike()
    {
        $likeable = m::mock('Conner\Tests\Likeable\LikeableStub[incrementLikeCount]');
        $likeable->shouldReceive('incrementLikeCount')->andReturn(null);
        
        $likeable->like(0);
    }
    
    public function testUnlike()
    {
        $likeable = m::mock('Conner\Tests\Likeable\LikeableStub[decrementLikeCount]');
        $likeable->shouldReceive('decrementLikeCount')->andReturn(null);
        
        $likeable->unlike(0);
    }
}

class LikeableStub extends Model
{
    use Likeable;

    public function incrementLikeCount()
    {
    }
    public function decrementLikeCount()
    {
    }
}
