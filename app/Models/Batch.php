<?php  namespace EID\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model {

	protected $guarded = array('id');
	protected $table = 'batches';

	public $timestamps = false;


	public function samples()
	{
		return $this->hasMany('EID\Models\Sample', 'batch_id', 'id');		
	}

	public function getFacilityID(){
		return $this->attributes["facility_id"];
	}

	public static function  getBatches(){
		return Batch::leftjoin("dbs_samples AS d","d.batch_id","=","b.id")
			   ->select("b.*",\DB::raw("count(d.id) AS nr_smpls,
			   	SUM(CASE WHEN d.sample_rejected = 'NOT_YET_CHECKED' THEN 1 ELSE 0 END) AS nr_not_yet_checked,
			   	SUM(CASE WHEN d.sample_rejected = 'NO' THEN 1 ELSE 0 END) AS nr_approved,
			   	SUM(CASE WHEN d.sample_rejected = 'YES' THEN 1 ELSE 0 END) AS nr_rejected,
			   	IF(SUM(CASE WHEN d.sample_rejected = 'NOT_YET_CHECKED' THEN 1 ELSE 0 END)=0, 'YES', 'NO') AS batch_checked"
			   	))
			   ->from("batches AS b")
			   ->groupby("b.id")
			   ->orderby("b.envelope_number")
			   ->get();
	}
}