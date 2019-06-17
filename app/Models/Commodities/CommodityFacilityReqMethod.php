<?php namespace EID\Models\Commodities;

use Illuminate\Database\Eloquent\Model;

class CommodityFacilityReqMethod extends Model {

	//
	protected $table = 'commodity_facility_req_methods';

	public static $rules = [
		'method' => 'required'
	];
	
	protected $fillable = ['method','created','createdby'];

	public $timestamps = false;

    public static function reqMethodsArr(){
		$arr=array();
		foreach(CommodityFacilityReqMethod::all() AS $method){
			$arr[$method->id]=$method->method;
		}
		return $arr;
	}

}
