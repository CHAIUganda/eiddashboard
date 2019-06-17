<?php namespace EID\Models\CustomerCare;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model {

	//
	protected $table = 'cc_complaints';
	public $timestamps = false;


    /**
     * Fillable fields
     *
     * @var array
     */

    public static $rules = [
        'categoryID'=>'required',
		'complaint' => 'required'
	];

    protected $fillable = [
        'categoryID',
        'complaint',
        'status',
        'resolved',
        'facilityID',
        'complainant',
        'complainant_telephone',
        'complainant_email',
        'created',
        'createdby'
    ];

    public static function getComplaints(){
    	return Complaint::leftjoin('cc_categories AS cat','cmplt.categoryID','=','cat.id')
    	       ->leftjoin('facilities AS f','cmplt.facilityID','=','f.id')
    		   ->select('cmplt.*','cat.category','f.facility',\DB::raw("(CASE WHEN cmplt.resolved = 1 THEN 'Yes'  ELSE 'No' END) AS resolved"))
    		   ->from('cc_complaints AS cmplt')->get();
    }

    public static function getComplaint($id){
    	return Complaint::leftjoin('cc_categories AS cat','cc_complaints.categoryID','=','cat.id')
    		   ->leftjoin('facilities AS f','cc_complaints.facilityID','=','f.id')
    	       ->select('cc_complaints.*','cat.category','f.facility')->findOrFail($id);
    }

}
