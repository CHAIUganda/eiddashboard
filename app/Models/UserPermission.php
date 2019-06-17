<?php namespace EID\Models;

use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model {

	//
	protected $table = 'user_permissions';

	public static function permsArr(){
		$perms_obj=UserPermission::all();
		$perm_parents=[];
		$perm_children=[];
		foreach ($perms_obj as $perm) {
			if($perm->parent_id==0){
				$perm_parents[$perm->id]=$perm->description;
			}else{
				$perm_children[$perm->parent_id][$perm->id]=$perm->description;
			}
		}

		return compact('perm_parents','perm_children');
	}

	public static function permsListArr(){
        $arr=array();
        foreach(UserPermission::all() AS $p){
            $arr[$p->id]=$p->description;
        }
        return $arr;
    }

}
