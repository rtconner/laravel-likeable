<?php

use Conner\Tagging\Taggable;
use Conner\Tagging\Tag;
use Conner\Tagging\TaggingUtil;
use Illuminate\Support\Facades\Config;
use Conner\Tagging\Tests\TaggingStub;
use Conner\Likeable\Tests\LikeableStub;

class TaggingTest extends \Orchestra\Testbench\TestCase {

	/**
	 * Define environment setup.
	 *
	 * @param Illuminate\Foundation\Application $app
	 * @return void
	 */
	protected function getEnvironmentSetUp($app) {
		// reset base path to point to our package's src directory
		$app['path.base'] = __DIR__ . '/../src';
		$app['config']->set('database.default', 'testbench');
		$app['config']->set('database.connections.testbench', array(
			'driver' => 'sqlite',
			'database' => ':memory:',
			'prefix' => '',
		));
	}
	
	public function setUp() {
		parent::setUp();
		
		$artisan = $this->app->make('artisan');
		
		$artisan->call('migrate', array(
			'--database' => 'testbench',
			'--package'=>'rtconner\laravel-likeable',
			'--path'=>'migrations',
		));

		$artisan->call('migrate', array(
			'--database' => 'testbench',
			'--package'=>'rtconner\laravel-likeable',
			'--path'=>'../tests/migrations',
		));
		
		include_once(dirname(__FILE__).'/Stub.php');
	}

	public function testLike() {
		$stub = $this->randomStub();

		$stub->like(1);
		$stub->like(2);
		$stub->like(3);
		
		$this->assertEquals(3, $stub->likeCount);
	}

	public function testUnlike() {
		$stub = $this->randomStub(100);

		$stub->unlike(1);
		$stub->unlike(2);
		$stub->unlike(3);
		
		$this->assertEquals(0, $stub->likeCount);
		
		$stub = $this->randomStub(100);
		$stub->like(2);
		$stub->like(3);
		
		$this->assertEquals(2, $stub->likeCount);
		
		$stub = $this->randomStub(100);
		$stub->like(4);
		$stub->like(4);
		
		$this->assertEquals(3, $stub->likeCount);
		
		$stub = $this->randomStub(100);
		$stub->unlike(4);
		
		$this->assertEquals(2, $stub->likeCount);
	}
	
	public function testMultiple() {
		$stub1 = $this->randomStub(99);
		$stub2 = $this->randomStub(100);
		
		$stub1->like(9);
		$stub1->like(8);
		$stub1->like(7);
		$stub1->like(6);
		
		$stub2->like(9);
		$stub2->like(8);
		$stub2->like(5);
		
		$this->assertEquals(4, $stub1->likeCount);
		$this->assertEquals(3, $stub2->likeCount);
	}

	public function test_loggedInUserId() {
		$stub = $this->randomStub();
		$this->assertEquals(1, $stub->loggedInUserId());
	}
	
	public function testNoUser() {
		$stub1 = $this->randomStub(6);
		
		$j = 6;
		for($i=0;$i<$j;$i++)
			$stub1->like(0);
		
		$this->assertEquals($j, $stub1->likeCount);
		
		$stub1 = $this->randomStub(6);
		
		$k = 2;
		for($i=0;$i<$k;$i++)
			$stub1->unlike(0);
		
		$this->assertEquals($j-$k, $stub1->likeCount);
		
		$stub2 = $this->randomStub(4);
		$stub2->like();
		$this->assertEquals(1, $stub2->likeCount);
	}

	public function testLiked() {
		$stub = $this->randomStub(1);
		$this->assertFalse($stub->liked());

		$stub = $this->randomStub(1);
		$stub->like();
		$this->assertTrue($stub->liked());
		
		$stub = $this->randomStub(1);
		$stub->unlike();
		$this->assertFalse($stub->liked());
	}
	
	public function testWithLiked() {
		$stub = $this->randomStub(1);
		$stub->like();
		$stub = $this->randomStub(2);
		$stub->like();
		
		$found = LikeableStub::whereLiked()->count();
		$this->assertEquals(2, $found);
	}

	private function randomStub($id=null) {
		LikeableStub::migrate();
		
		if(is_null($id)) { $id = rand(1,100); }
		
		$stub = LikeableStub::firstOrCreate(['id'=>$id]);
		
		return $stub;
	}
	
}