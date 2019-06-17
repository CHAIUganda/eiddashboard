<?php namespace EID\Models\Commodities;

use Illuminate\Database\Eloquent\Model;

class CommodityRequisition extends Model {

	//
	protected $table = 'commodity_requisitions';

	public static $rules = [
		'commodityID' => 'required'
	];
	
	protected $fillable = [
		'commodityID',
		'requisition_number',
		'quantity_requisitioned',
		'approved',
		'created',
		'createdby'
		];

	public $timestamps = false;

	public static  function getCommodityRequisition($id){
		return CommodityRequisition::leftjoin("commodities AS c","c.id","=","commodity_requisitions.commodityID")
								->select("commodity_requisitions.*","c.commodity")
								->findOrFail($id);
	}

	private static  function _getCommodityRequisitions(){
		return CommodityRequisition::leftjoin("commodities AS c","c.id","=","commodity_requisitions.commodityID")
								->select("commodity_requisitions.*","c.commodity");
								
	}

	public static function getCommodityRequisitions(){
		return CommodityRequisition::_getCommodityRequisitions()->get();
	}

	public static function getPendingRequisitions(){
		return  CommodityRequisition::_getCommodityRequisitions()->whereNull("approved")->get();
	}

}
