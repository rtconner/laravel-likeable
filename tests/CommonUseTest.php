<?php

namespace Conner\Tests\Likeable;

use Conner\Likeable\Like;
use Illuminate\Database\Eloquent\Model;
use Conner\Likeable\Likeable;
use Conner\Likeable\LikeCounter;

class CommonUseBaseTest extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Model::unguard();
    }
    
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        \Schema::create('books', function ($table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
        });
    }
    
    public function tearDown(): void
    {
        \Schema::drop('books');
    }

    public function test_basic_like()
    {
        /** @var Stub $stub */
        $stub = Stub::create(['name'=>123]);

        $stub->like();
        
        $this->assertEquals(1, $stub->likeCount);
    }
    
    public function test_multiple_likes()
    {
        $stub = Stub::create(['name'=>123]);
        
        $stub->like(1);
        $stub->like(2);
        $stub->like(3);
        $stub->like(4);
        
        $this->assertEquals(4, $stub->likeCount);
    }
    
    public function test_unlike()
    {
        /** @var Stub $stub */
        $stub = Stub::create(['name'=>123]);
        
        $stub->unlike(1);
        
        $this->assertEquals(0, $stub->likeCount);
    }
    
    public function test_where_liked_by()
    {
        Stub::create(['name'=>'A'])->like(1);
        Stub::create(['name'=>'B'])->like(1);
        Stub::create(['name'=>'C'])->like(1);
        
        $stubs = Stub::whereLikedBy(1)->get();
        $shouldBeEmpty = Stub::whereLikedBy(2)->get();
        
        $this->assertEquals(3, $stubs->count());
        $this->assertEmpty($shouldBeEmpty);
    }
    
    public function test_deleteModel_deletesLikes()
    {
        /** @var Stub $stub1 */
        $stub1 = Stub::create(['name'=>456]);
        /** @var Stub $stub2 */
        $stub2 = Stub::create(['name'=>123]);
        /** @var Stub $stub3 */
        $stub3 = Stub::create(['name'=>888]);
        
        $stub1->like(1);
        $stub1->like(7);
        $stub1->like(8);
        $stub2->like(1);
        $stub2->like(2);
        $stub2->like(3);
        $stub2->like(4);

        $stub3->delete();
        $this->assertEquals(7, Like::count());
        $this->assertEquals(2, LikeCounter::count());

        $stub1->delete();
        $this->assertEquals(4, Like::count());
        $this->assertEquals(1, LikeCounter::count());

        $stub2->delete();
        $this->assertEquals(0, Like::count());
        $this->assertEquals(0, LikeCounter::count());
    }
    
    public function test_rebuild_test()
    {
        $stub1 = Stub::create(['name'=>456]);
        $stub2 = Stub::create(['name'=>123]);

        $stub1->like(1);
        $stub1->like(7);
        $stub1->like(8);
        $stub2->like(1);
        $stub2->like(2);
        $stub2->like(3);
        $stub2->like(4);
        
        LikeCounter::truncate();
        
        LikeCounter::rebuild(Stub::class);

        $this->assertEquals(2, LikeCounter::count());
    }
}

/**
 * @mixin \Eloquent
 */
class Stub extends Model
{
    use Likeable;
    
    public $table = 'books';
}
