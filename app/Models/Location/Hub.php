<?php namespace EID\Models\Location;

use Illuminate\Database\Eloquent\Model;

class Hub extends Model {

	//

	protected $table = 'hubs';

	public static $rules = [
		'hub' => 'required'
	];

	protected $fillable = ['hub','email','ipID','coordinator','coordinator_contact','created','createdby'];

	public $timestamps = false;

	public function facilities(){
        return $this->hasMany('EID\Models\Location\Facility','hubID');
    }

    public function ip(){
        return $this->belongsTo('EID\Models\IP');
    }

    public static function hubsList(){
    	return Hub::leftjoin('ips AS i','i.id', '=','h.ipID')->select('h.*','i.ip')->from("hubs AS h")->get();
    }

    public static function hubsArr(){
		$arr=array();
		foreach(Hub::all() AS $h){
			$arr[$h->id]=$h->hub;
		}
		return $arr;
	}



}
