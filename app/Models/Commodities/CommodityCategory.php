<?php namespace EID\Models\Commodities;

use Illuminate\Database\Eloquent\Model;

class CommodityCategory extends Model {

	//
	protected $table = 'commodity_categories';

	public static $rules = [
		'category' => 'required'
	];
	
	protected $fillable = ['category','created','createdby'];

	public $timestamps = false;

    public static function commodityCatsArr(){
		$arr=array();
		foreach(CommodityCategory::all() AS $category){
			$arr[$category->id]=$category->category;
		}
		return $arr;
	}

}
