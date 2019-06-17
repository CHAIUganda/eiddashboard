<?php namespace EID\Models;

use Illuminate\Database\Eloquent\Model;

class Appendix extends Model {

	//

	protected $table = 'appendices';

	public static $rules = [
		'appendix'=>"required|unique:appendices,appendix,null,id,categoryID,",
		'code'=>"unique:appendices,code,null,id,categoryID,"
	];
	
	protected $fillable = ['code','appendix','categoryID','created','createdby'];

	public $timestamps = false;

    public static function appendicesArr(){
		$arr=array();
		foreach(Appendix::select('id','appendix')->get() AS $a){
			$arr[$a->id]=$a->appendix;
		}
		return $arr;
	}

	public static function appendicesArr2($cat_id,$code_prepend=true){
		$arr=array();
		$apps=Appendix::select('id','appendix','code')
			  ->where('inactive',0)
			  ->where('categoryID',$cat_id)
			  ->orderby('code')
			  ->get();
		foreach($apps AS $a){
			$arr[$a->id]=$code_prepend==false?$a->appendix:$a->code.':'.$a->appendix;
		}
		return $arr;
	}

	public static function getByCat($cat_id){
		return Appendix::where('categoryID',$cat_id)->get();
	}


}



