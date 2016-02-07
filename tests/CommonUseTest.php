<?php

use Illuminate\Database\Eloquent\Model as Eloquent;
use Conner\Likeable\Likeable;
use Conner\Likeable\LikeCounter;

class CommonUseTest extends TestCase
{
	public function setUp()
	{
		parent::setUp();
		
		Eloquent::unguard();

		$this->artisan('migrate', [
		    '--database' => 'testbench',
		    '--realpath' => realpath(__DIR__.'/../migrations'),
		]);
	}
	
	protected function getEnvironmentSetUp($app)
	{
	    $app['config']->set('database.default', 'testbench');
	    $app['config']->set('database.connections.testbench', [
	        'driver'   => 'sqlite',
	        'database' => ':memory:',
	        'prefix'   => '',
	    ]);
	    
		\Schema::create('books', function ($table) {
			$table->increments('id');
			$table->string('name');
			$table->timestamps();
		});
	}
	
	public function tearDown()
	{
		\Schema::drop('books');
	}

	public function test_basic_like()
	{
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
	
	public function test_likes_get_deletes_with_record()
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
		
		$stub1->delete();
		
		$results = LikeCounter::all();
		$this->assertEquals(1, $results->count());
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
		
		LikeCounter::rebuild('Stub');
		
		$results = LikeCounter::all();
		$this->assertEquals(2, $results->count());
	}
}

class Stub extends Eloquent
{
	use Likeable;
	
	protected $morphClass = 'Stub';
	
	protected $connection = 'testbench';
	
	public $table = 'books';
}
