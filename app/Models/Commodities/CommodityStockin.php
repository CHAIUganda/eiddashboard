<?php namespace EID\Models\Commodities;

use Illuminate\Database\Eloquent\Model;

class CommodityStockin extends Model {

	//
	protected $table = 'commodity_stockin';

	public static $rules = [
		'commodityID' => 'required'
	];
	
	protected $fillable = [
		'commodityID',
		'quantity',
		'arrival_date',
		'expiry_date',
		'batchno',
		'created',
		'createdby'
		];

	public $timestamps = false;

	public function commodity(){
		return $this->belongsTo('EID\Models\Commodities\Commodity');
	}

	public static function getCommodityStockin($id){
		return CommodityStockin::leftjoin('commodities AS c','c.id', '=','commodity_stockin.commodityID')
						->select('commodity_stockin.*','c.commodity')
    					->findOrFail($id);
	}

	public static function getAllCommodityStockin(){
		return CommodityStockin::leftjoin('commodities AS c','c.id', '=','commodity_stockin.commodityID')
						->select('commodity_stockin.*','c.commodity')
    					->get();
	}

}
