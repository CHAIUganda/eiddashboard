<?php namespace EID\Models;

use Illuminate\Database\Eloquent\Model;

class AppendixCategory extends Model {

	//
	protected $table = 'appendix_categories';

	public $timestamps = false;

    public static function appendixCatsArr(){
		$arr=array();
		foreach(AppendixCategory::all() AS $a){
			$arr[$a->id]=$a->category;
		}
		return $arr;
	}
}



