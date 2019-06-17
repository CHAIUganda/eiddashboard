<?php   namespace EID\Models;

use Illuminate\Database\Eloquent\Model;

class Sample extends Model {

	protected $guarded = array('id');
	protected $table = 'dbs_samples';
	protected $dates = array('f_date_results_collected', 'f_date_ART_initiated');

	public $timestamps = false;

	public function parent_batch()
	{
		return $this->belongsTo('Batch');
	}

	public function getEntryPoint(){
		return $this->attributes["infant_entryPoint"];
	}

	public function getAnteNatalPMTCT(){
		return $this->attributes["mother_antenatal_prophylaxis"];
	}

	public function getDeliveryPMTCT(){
		return $this->attributes["mother_delivery_prophylaxis"];	
	}

	public function getPostNatalPMTCT(){
		return $this->attributes["mother_postnatal_prophylaxis"];
	}

	public function getInfantPMTCT(){
		return $this->attributes["infant_prophylaxis"];
	}

	public function wasRejected(){
		if( empty ($this->attributes["sample_verified_by"]) ){
			return null;
		}
		
		$sample_rejected = ($this->attributes["sample_rejected"] === "YES") ? true : false;

		return 	$sample_rejected;
	}

	public function wasAccepted(){
		$status = $this->WasRejected();

		if($status === null)
			return null;
		else
			return !$status;
	}

	public static function quickStats($level,$case){
		$res=Sample::select('id','batch_id');
		$res=$level=='batches'?Sample::select('batch_id'):Sample::select('id');
		switch ($case) {
			case 'pending2approve':
				$res=$res->where('sample_rejected','=','NOT_YET_CHECKED');
				break;

			case 'ready4lab':
				$res=$res->where('sample_rejected','=','NO')->where('in_workSheet','=','NO');
				break;
			
			default:
				# code...
				break;
		}
		$res=$level=='batches'?$res->groupby('batch_id'):$res;
		return $res->get()->count();
	}
	

	public static function getNumberTotals($year,$pcr="",$ttl_inited=""){
		if(empty($year)) $year=date("Y");
		$res=$res=Sample::leftjoin("batches AS b","b.id","=","s.batch_id")
				  ->leftjoin("facilities AS f","f.id","=","b.facility_id")
				  ->leftjoin("districts AS d","d.id","=","f.districtID")
				  ->select(\DB::raw("facilityLevelID ,regionID,districtID,count(s.id) AS num"))
			 	  ->from("dbs_samples AS s")
			 	  ->whereYear('s.date_results_entered','=',$year);
		$res=!empty($pcr)?$res->where('s.pcr','=',$pcr):$res;
		$res=$ttl_inited==1?$res->where("f_ART_initiated",'=','YES'):$res;
		$res=$res->groupby('facilityLevelID','regionID','districtID')->get();

		$levels=FacilityLevel::facilityLevelsArr();
		$regs=Location\District::districtsByRegions();

		$ret=[];
		
		foreach ($levels as $lvl_id => $level) {
			foreach ($regs as $reg_id => $dists) {
				foreach ($dists as $dist_id => $dist) $ret[$lvl_id][$reg_id][$dist_id]=0;
			}			
		}
		
		foreach ($res as $rw) {
			$ret[$rw->facilityLevelID][$rw->regionID][$rw->districtID]=$rw->num;
		}
		unset($ret[""]);
		unset($ret[0]);
		return $ret;
	}

	public static function counts($year="",$postives="",$art_inits=""){
		if(empty($year)) $year=date("Y");
		$res=Sample::leftjoin("batches AS b","b.id","=","s.batch_id")
				->leftjoin("facilities AS f","f.id","=","b.facility_id")
				->leftjoin("districts AS d","d.id","=","f.districtID")
				->select(\DB::raw("d.regionID,f.facilityLevelID,f.facility,f.districtID,b.facility_id,month(date_results_entered) AS mth, count(s.id) AS number"))
				->from("dbs_samples AS s")
				->whereYear('s.date_results_entered','=',$year);
		$res=$postives==1?$res->where('s.accepted_result','=','POSITIVE'):$res;
		$res=$art_inits==1?$res->where('s.f_ART_initiated','=','YES'):$res;
		$res=$res->groupby("facility_id","mth")->get();


		$ret=[];
		foreach ($res as $row) {
			$ret[$row->facility_id."_".$row->mth]=[
					"facility_id"=>$row->facility_id,
					"month"=>$row->mth,
					"facility_name"=>$row->facility,
					"district_id"=>$row->districtID,
					"region_id"=>$row->regionID,
					"level_id"=>$row->facilityLevelID,
					"value"=>$row->number
					];
		}
		return $ret;
	}

	public static function niceCounts($year="",$postives="",$art_inits="",$grpby_fclts=""){
		if(empty($year)) $year=date("Y");
		$res=Sample::leftjoin("batches AS b","b.id","=","s.batch_id")
				->leftjoin("facilities AS f","f.id","=","b.facility_id")
				->leftjoin("districts AS d","d.id","=","f.districtID")
				->select(\DB::raw("d.regionID,f.facilityLevelID,f.facility,f.districtID,b.facility_id,month(date_results_entered) AS mth, count(s.id) AS number"))
				->from("dbs_samples AS s")
				->whereYear('s.date_results_entered','=',$year);
		$res=$postives==1?$res->where('s.accepted_result','=','POSITIVE'):$res;
		$res=$art_inits==1?$res->where('s.f_ART_initiated','=','YES'):$res;
		$res=$grpby_fclts==1?$res->groupby("facility_id"):$res->groupby('facilityLevelID','regionID','districtID','mth');
		$res=$res->get();

		$levels=FacilityLevel::facilityLevelsArr();
		$months=\MyHTML::initMonths();
		$regs=Location\District::districtsByRegions();

		$ret=[];

		if($grpby_fclts==1){
			foreach ($res as $row) {
				$ret[$row->facility_id]=[
						"facility_id"=>$row->facility_id,
						"facility_name"=>$row->facility,
						"district_id"=>$row->districtID,
						"region_id"=>$row->regionID,
						"level_id"=>$row->facilityLevelID,
						"value"=>$row->number
						];
			}

		}else{

			foreach ($levels as $lvl_id => $level) {
				foreach ($regs as $reg_id => $dists) {
					foreach ($dists as $dist_id => $dist) $ret[$lvl_id][$reg_id][$dist_id]=$months;
				}		
			}

			foreach ($res as $rw) {
				$ret[$rw->facilityLevelID][$rw->regionID][$rw->districtID][$rw->mth]=$rw->number;
			}
			unset($ret[""]);
			unset($ret[0]);
		}
		return $ret;
	}

	public static function PCRAges($year,$pcr=""){
		$res=Sample::select("s.infant_age")
			 ->from("dbs_samples AS s")
			 ->whereYear('s.date_results_entered','=',$year);
		$res=!empty($pcr)?$res->where('s.pcr','=',$pcr):$res;
		$res=$res->get();
		$ret=[];
		foreach ($res as $rw) {
			$ret[]=Sample::cleanAge($rw->infant_age);	
		}
		//if($pcr=="SECOND") print_r($ret);
		return $ret;
	}


	private static function cleanAge($age=0){
		$ret=0;
		$age_arr=explode(" ", $age);
		$years=0;$months=0;$weeks=0;$days=0;

		foreach ($age_arr as $k => $val) {
			if($val=='year'||$val=='years'){
				$years=str_replace(" ", "",$age_arr[($k-1)]);
			}elseif($val=='months'||$val=='month'){
				$months=str_replace(" ", "",$age_arr[($k-1)]);
			}elseif($val=='weeks'||$val=='week'){
				$weeks=str_replace(" ", "",$age_arr[($k-1)]);
			}elseif($val=='days'||$val=='day'){
				$days=str_replace(" ", "",$age_arr[($k-1)]);
			}else{
				$months=$val;
			}
		}
		$ret= ($years*12)+$months+($weeks/4)+($days/30);
		return round($ret,2);
	}

	private static function regionMonthsInit(){
		$regions=Location\Region::regionsArr();
		$months=\MyHTML::initMonths();
		$ret=[];
		foreach ($regions as $regID => $reg)  $ret[$regID]=$months;
		return $ret;
	}

	private static function districtMonthsInit(){
		$districts=Location\District::districtsArr();
		$months=\MyHTML::initMonths();
		$ret=[];
		foreach ($districts as $dID => $d)  $ret[$dID]=$months;
		return $ret;
	}



}


/*Array ( 
	[Central 1] => Array ( [9] => 7 [10] => 10 ) 
	[Central 2] => Array ( [9] => 3 [10] => 3 ) 
	[East Central] => Array ( [9] => 1 [10] => 4 ) 
	[Kampala] => Array ( [9] => 8 [10] => 9 ) 
	[Mid Eastern] => Array ( [9] => 1 [10] => 4 ) 
	[Mid Northern] => Array ( [9] => 4 [10] => 9 ) 
	[Mid Western] => Array ( [9] => 5 [10] => 5 ) http://www.azlyrics.com/lyrics/hoobastank/incomplete.html
	[North East] => Array ( [9] => 1 [10] => 1 ) 
	[South Western] => Array ( [9] => 5 [10] => 9 ) 
	[West Nile] => Array ( [9] => 1 ) )*/


/*
Your lim

 $scope.filteredfcltys=function(option,val){
        var ret=[];
        for (var i $scope.facility_numbers_init){
            var arr=$scope.facility_numbers_init[i];
            if(val==arr[option]){
                ret[i]=arr;
            }
        }
        return ret;
    }
*/
