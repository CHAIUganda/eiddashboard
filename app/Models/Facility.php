<?php namespace EID\Models;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model {

	//

	protected $table = 'facilities';

	public static $rules = [
		'facility' => 'required',
		'districtID' => 'required',
		'hubID' => 'required',
		'facilityLevelID'=>'required',
        'email'=>'email'
	];

	protected $fillable = [
	  'facilityCode',
	  'facility',
	  'facilityLevelID',
	  'districtID',
	  'hubID',
	  'phone',
	  'email',
	  'contactPerson',
	  'physicalAddress',
	  'returnAddress',
	  'created',
	  'createdby'];

	public $timestamps = false;

	public function district(){
        return $this->belongsTo('EID\Models\Location\District');
    }

    public function hub(){
        return $this->belongsTo('EID\Models\Location\Hub');
    }

    public function facility_level(){
        return $this->belongsTo('EID\Models\Location\FacilityLevel');
    }

    /*public static function getFacility($id){
    	return Facility::leftjoin('facility_levels AS fl','fl.id', '=','f.facilityLevelID')
    					->leftjoin('districts AS d','d.id','=','f.districtID')
    					->select('f.*','fl.facility_level','d.district')
    					->from("facilities AS f")
    					->where('f.id',$id)->get();
    }*/


    public static function getFacility($id){
    	return Facility::leftjoin('facility_levels AS fl','fl.id', '=','facilities.facilityLevelID')
    					->leftjoin('districts AS d','d.id','=','facilities.districtID')
    					->leftjoin('hubs AS h','h.id','=','facilities.hubID')
    					->select('facilities.*','fl.facility_level','d.district','h.hub')
    					->findOrFail($id);
    }


    private static function _getFacilities(){
    	return Facility::leftjoin('facility_levels AS fl','fl.id','=','f.facilityLevelID')
    					->leftjoin('districts AS d','d.id','=','f.districtID')
    					->leftjoin('hubs AS h','h.id','=','f.hubID')
                        ->leftjoin('regions AS r','r.id','=','d.regionID')
    					->select('f.*','fl.facility_level','d.district','h.hub','r.region')
    					->from('facilities AS f');

    }

    public static function getFacilitiesAll(){
    	return Facility::_getFacilities()->get();
    }

    public static function getFacilitiesByHub($hub_id){
    	return Facility::_getFacilities()->where('f.hubID','=',$hub_id)->get();
    }

    public static function getFacilitiesByDistrict($district_id){
    	return Facility::_getFacilities()->where('f.districtID','=',$district_id)->get();
    }

    public static function getFacilitiesByRegion($region_id){
        return Facility::_getFacilities()->where('d.regionID','=',$region_id)->get();
    }

    public static function searchFacilityByName($q){
        return Facility::select('id','facility')->where('facility','LIKE',"%$q%")->get();
    }

    public static function facilitiesArr(){
        $arr=array();
        foreach(Facility::all() AS $f){
            $arr[$f->id]=$f->facility;
        }
        return $arr;
    }

    public static function facilitiesByDistrictsArr(){
        $arr=array();
        $districts= Location\District::districtsArr();
        foreach(Facility::select('id','facility','districtID')->get() AS $f){
           $d_name=array_key_exists($f->districtID, $districts)?$districts[$f->districtID]:'';
           $arr[$d_name][$f->id]=$f->facility;
        }
       ksort($arr);  
       return $arr;     
    }

    public static function facilitiesByDistrictsArr2(){
        $arr=array();
        $districts= Location\District::districtsArr();
        foreach(Facility::select('id','facility','districtID')->get() AS $f){
           $d_name=array_key_exists($f->districtID, $districts)?$districts[$f->districtID]:'';
           $v=json_encode(['facility_id'=>$f->id,'facility_name'=>$f->facility,'district'=>$d_name], JSON_FORCE_OBJECT);
           $arr[$d_name][$v]=$f->facility;
        }
       ksort($arr);  
       return $arr;     
    }
}
