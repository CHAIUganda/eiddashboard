<?php namespace EID\Models;

use Illuminate\Database\Eloquent\Model;

class FacilityLevel extends Model {

	//

	protected $table = 'facility_levels';

	public static $rules = [
		'facility_level' => 'required'
	];
	
	protected $fillable = ['facility_level','created','createdby'];

	public $timestamps = false;

	public function facilities(){
        return $this->hasMany('EID\Models\Facility');
    }

    public static function facilityLevelsArr(){
		$arr=array();
		foreach(FacilityLevel::all() AS $fl){
			$arr[$fl->id]=$fl->facility_level;
		}
		return $arr;
	}


}



