<?php namespace EID\Models\Commodities;

use Illuminate\Database\Eloquent\Model;

class CommodityFacilityRequisition extends Model {

	//
	protected $table = 'commodity_facility_requisitions';

	public static $rules = [
		'commodityID' => 'required',
		'facilityID'  => 'required'
	];
	
	protected $fillable = [
		'commodityID',
		'facilityID',
		'quantity_requisitioned',
		'requisition_date',
		'req_methodID',
		'approved',
		'created',
		'createdby'
		];

	public $timestamps = false;

	public static function getCommodityFacilityRequisition($id){
		return CommodityFacilityRequisition::
							leftjoin("commodities AS c","c.id","=","commodity_facility_requisitions.commodityID")
							->leftjoin("facilities AS f","f.id","=","commodity_facility_requisitions.facilityID")
							->leftjoin("commodity_facility_req_methods AS m","m.id","=","commodity_facility_requisitions.req_methodID")
							->select("commodity_facility_requisitions.*","c.commodity","f.facility","m.method")
							->findOrFail($id);

	}


	private static  function _getCommodityFacilityRequisitions(){
		return CommodityFacilityRequisition::
							leftjoin("commodities AS c","c.id","=","commodity_facility_requisitions.commodityID")
							->leftjoin("facilities AS f","f.id","=","commodity_facility_requisitions.facilityID")
							->leftjoin("commodity_facility_req_methods AS m","m.id","=","commodity_facility_requisitions.req_methodID")
							->select("commodity_facility_requisitions.*","c.commodity","f.facility","m.method");
								
	}

	public static function getCommodityFacilityRequisitions(){
		return CommodityFacilityRequisition::_getCommodityFacilityRequisitions()->get();
	}

	public static function getPendingFacilityRequisitions(){
		return  CommodityFacilityRequisition::_getCommodityFacilityRequisitions()->whereNull("approved")->get();
	}


}
