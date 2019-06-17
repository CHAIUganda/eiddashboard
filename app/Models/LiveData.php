<?php

namespace EID\Models;

use Illuminate\Database\Eloquent\Model;
use Log;

class LiveData extends Model
{
	const AGE_IN_MONTHS_STRING = "FLOOR(DATEDIFF(s.date_dbs_taken,s.infant_dob)/30)";


	const GENDER_CASE = "CASE WHEN infant_gender ='MALE' THEN 'm' WHEN infant_gender='FEMALE' 
	THEN 'f' ELSE 'UNKNOWN' END";

	const RESULT_CASE = "CASE WHEN accepted_result ='POSITIVE' THEN 'POSITIVE' WHEN accepted_result='NEGATIVE' 
	THEN 'NEGATIVE' ELSE 'UNKNOWN' END";

	public static function getHubs(){

    	$sql = "SELECT * FROM hubs";
        $hubs =  \DB::connection('live_db')->select($sql);
        
        return $hubs;
    }

    public static function getDistricts(){

    	$sql = "SELECT * FROM districts";
        $districts =  \DB::connection('live_db')->select($sql);
        
        return $districts;
    }

    public static function getRegions(){

    	$sql = "SELECT * FROM regions";
        $regions =  \DB::connection('live_db')->select($sql);
        
        return $regions;
    }

    public static function getCareLevels(){

    	$sql = "SELECT * FROM facility_levels";
        $care_levels =  \DB::connection('live_db')->select($sql);
        
        return $care_levels;
    }

    public static function getFacilities(){

    	$sql = "SELECT * FROM facilities";
        $facilities =  \DB::connection('live_db')->select($sql);
        
        return $facilities;
    }

    public static function getSamples($year){

    	$sql = "SELECT s.id,s.infant_exp_id,".self::GENDER_CASE." as sex,s.infant_dob,month(s.date_dbs_taken) as month_of_year,
		 ".self::AGE_IN_MONTHS_STRING." as age_in_months ,b.facility_id,f.hubID,f.facilityLevelID as care_level_id,f.districtID,
		 d.regionID, s.f_ART_initiated,s.f_date_ART_initiated,".self::RESULT_CASE." as accepted_result,s.testing_completed, s.PCR_test_requested,s.pcr FROM dbs_samples s 
		inner join batches b on s.batch_id =b.id 
		inner join facilities f on f.id = b.facility_id 
		inner join districts d on d.id = f.districtID

		where s.PCR_test_requested like 'YES' 
		and year(s.date_dbs_taken)=$year";

		$samples = \DB::connection('live_db')->select($sql);

		return $samples;
    }

    public static function getPOCSamples($year){
        $sql = "SELECT * FROM poc_data p inner join facilities f on p.facility_id=f.id";
        return \DB::connection('live_db')->select($sql);
    }

    public static function getSamplesRecordsByMonth($year,$month){
      $sql = "SELECT s.id,s.infant_exp_id,".self::GENDER_CASE." as sex,s.infant_dob,month(s.date_dbs_taken) as month_of_year,
         ".self::AGE_IN_MONTHS_STRING." as age_in_months ,b.facility_id,f.hubID,f.facilityLevelID as care_level_id,f.districtID,
         d.regionID, s.f_ART_initiated,s.f_date_ART_initiated,".self::RESULT_CASE." as accepted_result,s.testing_completed, s.PCR_test_requested,s.pcr FROM dbs_samples s 
        inner join batches b on s.batch_id =b.id 
        inner join facilities f on f.id = b.facility_id 
        inner join districts d on d.id = f.districtID

        where s.PCR_test_requested like 'YES' 
        and year(s.date_dbs_taken)=$year and MONTH(s.date_dbs_taken)=$month";

        $samples = \DB::connection('live_db')->select($sql);

        return $samples;
    }

    public static function getAdhocResults($year,$month){
        Log::info('...1...');
        $sql = " SELECT DISTINCT d.id as specimen_id,
                    d.infant_exp_id as exp_number,
                    b.facility_id,
                    b.facility_name as facility_sample_came_from,
                    d.pcr as type_of_pcr,
                    d.accepted_result,
                    d.date_dbs_taken as date_of_sample_collection,
                    b.date_dispatched_from_facility,
                    b.date_rcvd_by_cphl,
                    d.date_dbs_tested,
                    d.date_results_entered,
                    fp.dispatch_at as date_printed,
                    u.family_name AS printed_by,
                    fp.dispatch_by 

                    from dbs_samples d inner join batches b on d.batch_id = b.id 
                    left join facility_printing fp on b.id = fp.batch_id 
                    left join users u on u.id=fp.dispatch_by 
                    

                    where YEAR(d.date_dbs_taken)=$year and MONTH(d.date_dbs_taken)=$month ";
                Log::info('...2...');
             $samples = \DB::connection('live_db')->select($sql);
                   

        //
    
        return $samples;
    }

    public static function getUsers(){
        $sql = "SELECT * FROM users";
        $facilities =  \DB::connection('live_db')->select($sql);
        
        return $facilities;
    }

}

?>