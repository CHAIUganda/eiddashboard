<?php

namespace EID\Console\Commands;

use Illuminate\Console\Command;
use EID\Mongo;
use EID\Models\LiveData;

class POCEngine extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pocengine:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Loads POC data into Mongo';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->mongo=Mongo::connect();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set('memory_limit', '2500M');
        //
        $this->comment("Engine has started at :: ".date('YmdHis'));
        
        $this->_loadData();
        
        $this->comment("Engine has stopped at :: ".date('YmdHis'));

    }
    private function _loadData(){
        $year=2014;
        $current_year=date('Y');
       
       
        //while($year<=$current_year){
            $samples_records = LiveData::getPOCSamples($year);
            $counter=0;
            
            try {
                foreach($samples_records AS $s){
                    $data=[];
                    //$year_month = $year.str_pad($s->month_of_year,2,0,STR_PAD_LEFT);

                    $data["sample_id"]= "POC";
                    $data["infant_exp_id"]=isset($s->exp_no)? $s->exp_no: "UNKNOWN";//
                    $ref_date_str = !empty($s->test_date)?strtotime($s->test_date):strtotime($s->created_at);
                    $data["year_month"] = (int) date('Ym', $ref_date_str);
                    
                    $data['district_id']=isset($s->districtID)?(int)$s->districtID:0;
                    $data['hub_id']=isset($s->hubID)?(int)$s->hubID:0;

                    $data['region_id']=isset($s->regionID)?(int)$s->regionID:0;
                    $data['care_level_id']=isset($s->care_level_id)?(int)$s->care_level_id:0;
                    $data["facility_id"] = isset($s->facility_id)?(int)$s->facility_id:0;

                    $data["age_in_months"] = isset($s->age)?(int)$s->age:-1;
                 
                    $data["sex"] = !empty($s->gender)?strtolower(substr($s->gender, 0, 1)):"UNKNOWN";
                    
                    $data["art_initiation_status"] = "UNKNOWN";
                    $data["art_initiation_date"] = "UNKNOWN";
                    
                    
                    $data["pcr_test_requested"]="YES";//
                    $data["testing_completed"]="YES";
                    $data["accepted_result"]=$s->results=='Error'?'UNKNOWN':strtoupper($s->results);
                    $data["pcr"] = $this->getPCRLevel($s->pcr_level);
                    $data["source"] = "poc";                   

                   $this->mongo->eid_dashboard->insert($data);
                   $counter ++;
                }//end of for loop
              echo " inserted $counter records for $year\n";
              $year++;
            } catch (Exception $e) {
                var_dump($e);
            }//end catch

        //}//end of while loop
    }

    private function getPCRLevel($val=''){
        $ret = "";
        if($val=='R1' or $val=='R2'){
            $ret = 'NON_ROUTINE';
        }elseif($val=='1st PCR'){
            $ret = 'FIRST';
        }elseif($val=='2nd PCR'){
            $ret = 'SECOND';
        }else{
            $ret = 'UNKNOWN';
        }
        return $ret;
    }

}
