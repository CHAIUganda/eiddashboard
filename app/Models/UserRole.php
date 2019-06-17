<?php namespace EID\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model {

	//

	protected $table = 'user_roles';

	public static $rules = [
		'description' => 'required|unique:user_roles,description,null,id'
	];

	protected $fillable = ['description','permissions','permission_parents','created','createdby'];

	public $timestamps = false;

	public static function userRolesArr(){
		$arr=array();
		foreach(UserRole::all() AS $user_role){
			$arr[$user_role->id]=$user_role->description;
		}
		return $arr;
	}

}
