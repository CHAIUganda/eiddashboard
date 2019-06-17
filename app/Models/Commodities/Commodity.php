<?php namespace EID\Models\Commodities;

use Illuminate\Database\Eloquent\Model;

class Commodity extends Model {

	//
	protected $table = 'commodities';

	public static $rules = [
		'commodity' => 'required'
	];
	
	protected $fillable = [
		'commodity',
		'categoryID',
		'initial_quantity',
		'alert_quantity',
		'correlates_to_tests',
		'tests_per_unit',
		'created',
		'createdby'
		];

	public $timestamps = false;

	public function stockins(){
		return $this->hasMany('EID\Models\Commodities\CommodityStockin','commodityID');
	}

    public static function commoditiesArr(){
		$arr=array();
		foreach(Commodity::all() AS $commodity){
			$arr[$commodity->id]=$commodity->commodity;
		}
		return $arr;
	}

	public static function getCommodity($id){
		return Commodity::leftjoin('commodity_categories AS cat','cat.id', '=','commodities.categoryID')
						->select('commodities.*','cat.category')
    					->findOrFail($id);
	}

	public static function getCommodities(){
		return Commodity::leftjoin('commodity_categories AS cat','cat.id', '=','commodities.categoryID')
						->select('commodities.*','cat.category')
    					->get();
	}

	public static function getCmdtyInitQuantity($id){
		return Commodity::select('initial_quantity')->findOrFail($id)->initial_quantity;
	}

	public static function getStockinSum($commodity_id){
		return CommodityStockin::where('commodityID',$commodity_id)->sum("quantity");
	}

	public static function getReqSum($commodity_id){
		return CommodityReqApproval::leftjoin('commodity_requisitions AS r','r.id','=','a.requisitionID')
				->where('commodityID',$commodity_id)
				->from("commodity_req_approvals AS a")
				->sum("quantity_approved");
	}

	public static function getFacilityReqSum($commodity_id){
		return CommodityFacilityReqApproval::leftjoin('commodity_facility_requisitions AS r','r.id','=','a.requisitionID')
				->where('commodityID',$commodity_id)
				->from("commodity_facility_req_approvals AS a")
				->sum("quantity_approved");
	}

	public function getCurrentQuantity($commodity_id){
		$init_quant=$this->getCmdtyInitQuantity($commodity_id);
		$ttl_stokin=$this->getStockinSum($commodity_id);
		$ttl_req=$this->getReqSum($commodity_id);
		$ttl_f_req=$this->getFacilityReqSum($commodity_id);
		return ($init_quant+$ttl_stokin)-($ttl_req+$ttl_f_req);
	}

	public static function getLowQuantCommodities(){
		$ret=array();
		$cmdts=Commodity::getCommodities();
		foreach ($cmdts as $cmdty) {
			$bal=$cmdty->getCurrentQuantity($cmdty->id);
			if($bal<=$cmdty->alert_quantity){
				$ret[$cmdty->id]=$cmdty->commodity;
			}
		}
		return $ret;
	}

}
