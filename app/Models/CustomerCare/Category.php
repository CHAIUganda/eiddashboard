<?php namespace EID\Models\CustomerCare;

use Illuminate\Database\Eloquent\Model;

class Category extends Model {

	//
	protected $table = 'cc_categories';

	public static $rules = [
		'category' => 'required'
	];
	
	protected $fillable = ['category','created','createdby'];

	public $timestamps = false;

    public static function catsArr(){
		$arr=array();
		foreach(Category::all() AS $category){
			$arr[$category->id]=$category->category;
		}
		return $arr;
	}

}
