<?php namespace EID\Models\Commodities;

use Illuminate\Database\Eloquent\Model;

class CommodityReqApproval extends Model {

	//
	protected $table = 'commodity_req_approvals';

	public static $rules = [
		'requisitionID' => 'required'
	];
	
	protected $fillable = [
		'requisitionID',
		'quantity_approved',
		'comments',
		'created',
		'createdby'
		];

	public $timestamps = false;

}
