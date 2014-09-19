<?php namespace Conner\Likeable\Tests;

use Conner\Likeable\LikeableTrait;
use \Schema;
use Illuminate\Database\Schema\Blueprint;

class LikeableStub extends \Illuminate\Database\Eloquent\Model {
	use LikeableTrait;
	
	protected $table = 'likeable_stubs';
	public $fillable = array('id');
	
	public static function migrate() {
	
		if(\Schema::hasTable('likeable_stubs')) { return; }
		
		Schema::create('likeable_stubs', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
		});
	}
}