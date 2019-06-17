<?php namespace EID\Models\Location;

use Illuminate\Database\Eloquent\Model;

class District extends Model {

	//

	protected $table = 'districts';

	public static $rules = [
		'district_nr' => 'required',
		'district' => 'required'
	];

	protected $fillable=[
	   'district_nr',
	   'district',
	   'regionID',
	   'created',
	   'createdby'
	   ];


	public $timestamps = false;

	public function region() {
        return $this->belongsTo('EID\Models\Location\Region');
    }

    public function facilities(){
        return $this->hasMany('EID\Models\Location\Facility');
    }

    public static function districtsList(){
    	return District::leftjoin("regions AS r","r.id","=","d.regionID")->select("d.*","r.region")->from("districts AS d")->get();
    }

    public static function districtsArr(){
		$arr=array();
		foreach(District::all() AS $d){
			$arr[$d->id]=$d->district;
		}
		return $arr;
	}

	public static function districtsInit(){
		$arr=array();
		foreach(District::all() AS $d){
			$arr[$d->id]=0;
		}
		return $arr;
	}

	public static function districtsByRegions(){
		$res=District::all();
		$ret=[];
		foreach ($res as $rw) {
			$ret[$rw->regionID][$rw->id]=$rw->district;
		}
		return $ret;
	}

	public static function districtsFacilitiesInit(){
		$arr=array();
		$res=District::rightjoin('districts AS d','d.id','=','f.districtID')
			 ->select('districtID','district','f.id AS facility_id')
			 ->from('facilities AS f')
			 ->get();
		foreach($res AS $row){
			$arr[$row->districtID][$row->facility_id]=0;
		}
		return $arr;
	}

	public static function distsNregs(){
		$res=District::all();
		$ret=[];
		foreach ($res as $rw) {
			$ret[$rw->id]=$rw->regionID;
		}
		return $ret;
	}
}
