<?php namespace EID\Models\Location;

use Illuminate\Database\Eloquent\Model;

class Region extends Model {

	//

	protected $table = 'regions';

	public static $rules = [
		'region' => 'required'
	];
	
	protected $fillable = ['region','created','createdby'];

	public $timestamps = false;

	public function districts(){
        return $this->hasMany('EID\Models\Location\District','regionID','id');
    }

    public static function regionsArr(){
		$arr=array();
		foreach(Region::all() AS $region){
			$arr[$region->id]=$region->region;
		}
		return $arr;
	}

	public static function regionsInit(){
		$arr=array();
		foreach(Region::all() AS $region){
			$arr[$region->id]=0;
		}
		return $arr;
	}

	public static function regionsFacilitiesInit(){
		$arr=array();
		$res=Region::rightjoin('districts AS d','d.id','=','f.districtID')
			 ->rightjoin('regions AS r','r.id','=','d.regionID')
			 ->select('regionID','region','f.id AS facility_id')
			 ->from('facilities AS f')
			 ->get();
		foreach($res AS $row){
			$arr[$row->regionID][$row->facility_id]=0;
		}
		return $arr;
	}



}
