<?php namespace EID\Models\Commodities;

use Illuminate\Database\Eloquent\Model;

class CommodityConfig extends Model {

	//
	protected $table = 'commodity_config';

	public static $rules = [
		'item' => 'required'
	];
	
	protected $fillable = [
		'item',
		'value',
		'created',
		'createdby'
		];

	public $timestamps = false;

	public static function getItem($item){
		return CommodityConfig::where('item',$item)->firstOrFail();
	}
}
