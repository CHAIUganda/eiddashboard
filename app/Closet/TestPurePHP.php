<?php namespace EID\Closet;

use EID\Http\Controllers\PilotController;
class TestPurePHP{
	public function pop()
	{
		$xx=new PilotController;
		return $xx->index();
	}
}
$lala=new TestPurePHP;

var_dump($lala->pop());
?>