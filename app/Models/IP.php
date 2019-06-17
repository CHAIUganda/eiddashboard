<?php namespace EID\Models;

use Illuminate\Database\Eloquent\Model;

class IP extends Model {

	//

	protected $table = 'ips';

	public static $rules = [
		'ip' => 'required'
	];
	
	protected $fillable = [
		'ip',
	 	'full_name',
        'address',
        'focal_person',
        'focal_person_contact',
        'description',
        'funding_source',
        'created',
        'createdby'];

	public $timestamps = false;

	public function facilities(){
        return $this->hasMany('EID\Models\Facility');
    }

    public function hubs(){
        return $this->hasMany('EID\Models\Location\Hub','ipID');
    }

    public static function ipsArr(){
		$arr=array();
		foreach(IP::all() AS $ip){
			$arr[$ip->id]=$ip->ip;
		}
		return $arr;
	}



}



