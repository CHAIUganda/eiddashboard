<?php namespace EID\Models;

use Illuminate\Database\Eloquent\Model;
class IPFacility extends Model {

	protected $table = 'ips_facilities';

	public static $rules = [
		'ipID' => 'required',
		'facilityID' => 'required'
	];

	protected $fillable = ['ipID','facilityID','start_date','stopped','stop_date','created','createdby'];

	public $timestamps = false;

	public static function getFacilityIPs($facility_id){
        return IPFacility::leftjoin('ips AS i','i.id','=','if.ipID')
        				 ->select('if.*','i.ip')
        				 ->from('ips_facilities AS if')
        				 ->where('if.facilityID','=',$facility_id)
        				 ->get();
    }

    public static function facilityIPExists($arr){
    	return IPFacility::select('id')
    				   ->where('facilityID','=',$arr['facilityID'])
    				   ->where('ipID','=',$arr['ipID'])
    				   ->get();
    }

}
?>
