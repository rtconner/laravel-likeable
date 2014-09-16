<?php

use Conner\Tagging\Taggable;
use Conner\Tagging\Tag;
use Conner\Tagging\TaggingUtil;
use Illuminate\Support\Facades\Config;
use Conner\Tagging\Tests\TaggingStub;

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

	public function testNothing() {
		$this->assertTrue(true);
	}
	
}