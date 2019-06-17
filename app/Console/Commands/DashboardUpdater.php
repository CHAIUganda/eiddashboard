<?php

namespace EID\Console\Commands;

use Illuminate\Console\Command;
use EID\Mongo;
use EID\Models\LiveData;

class DashboardUpdater extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eiddashboard:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates eiddashboard Daily';

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
        $this->comment("Dashboard update has started at :: ".date('YmdHis'));
        

        $this->_loadData();
        
        $this->comment("Dashboard updated at :: ".date('YmdHis'));

    }
    private function _loadData(){
        
       
        $turnAroundTimeInMonths=env('TAT_MONTHS', 3);//Number of Months to consider for worst turn -around-time

        //while($year<=$current_year){
        //    $samples_records = LiveData::getSamples($year);
        //    $counter=0;
            
        for ($month=0; $month < $turnAroundTimeInMonths; $month++) { 
            $turnAroundYear=intval(date("Y",strtotime("-$month month")));
            $turnAroundMonth=intval(date("m",strtotime("-$month month")));

            $dummyYearMonthString=$turnAroundYear.str_pad($turnAroundMonth,2,0,STR_PAD_LEFT);
            $dummyYearMonth = intval($dummyYearMonthString);

            $recordsRemoved =  $this->removeSamples($dummyYearMonth);

            $samples_records = LiveData::getSamplesRecordsByMonth($turnAroundYear,$turnAroundMonth);
            $year = $turnAroundYear;

            $recordsInserted=0;

            try {
                foreach($samples_records AS $s){
                    $data=[];
                    $year_month = $year.str_pad($s->month_of_year,2,0,STR_PAD_LEFT);
            
                    $data["sample_id"]=isset($s->id)? (int)$s->id: 0;
                    $data["infant_exp_id"]=isset($s->infant_exp_id)? $s->infant_exp_id: "UNKNOWN";//
                    $data["year_month"] = (int)$year_month;
                    
                    $data['district_id']=isset($s->districtID)?(int)$s->districtID:0;
                    $data['hub_id']=isset($s->hubID)?(int)$s->hubID:0;

                    $data['region_id']=isset($s->regionID)?(int)$s->regionID:0;
                    $data['care_level_id']=isset($s->care_level_id)?(int)$s->care_level_id:0;
                    $data["facility_id"] = isset($s->facility_id)?(int)$s->facility_id:0;

                    $data["age_in_months"] = isset($s->age_in_months)?(int)$s->age_in_months:-1;
                 
                    $data["sex"] = isset($s->sex)?$s->sex:0;
                    
                    $data["art_initiation_status"] = isset($s->f_ART_initiated)?$s->f_ART_initiated:"UNKNOWN";
                    $data["art_initiation_date"] = isset($s->f_date_ART_initiated)?$s->f_date_ART_initiated:"UNKNOWN";
                    
                    
                    $data["pcr_test_requested"]=isset($s->PCR_test_requested)? $s->PCR_test_requested: "UNKNOWN";//
                    $data["testing_completed"]=isset($s->testing_completed)? $s->testing_completed: "UNKNOWN";
                    $data["accepted_result"]=isset($s->accepted_result)? $s->accepted_result: "UNKNOWN";
                    $data["pcr"]=isset($s->pcr)? $s->pcr: "UNKNOWN";//

                    $dat["source"] = "cphl";
                   

                   $this->mongo->eid_dashboard->insert($data);
                   $recordsInserted ++;
                }//end of for loop
              echo " Removed $recordsRemoved records for $turnAroundYear-$turnAroundMonth\n";
              echo " Inserted $recordsInserted records for $turnAroundYear-$turnAroundMonth\n";
            } catch (Exception $e) {
                var_dump($e);
            }//end catch

        }//end of while loop
    }

    /*
    * return 1 for when a record has been successfully removed,0 when nothing has been found.
    */
    private function removeSample($numberSampleID){
        $options=[];
        $options['justOne']=false;
        $result=$this->mongo->eid_dashboard->remove(array('sample_id' => $numberSampleID), $options);
        return $result['n'];//return 1 for when a record has been successfully removed,0 when nothing has been found.
    }
     private function removeSamples($yearMonth){
        $options=[];
        $options['justOne']=false;
        $result=$this->mongo->eid_dashboard->remove(array('year_month' => $yearMonth), $options);
        return $result['n'];//return 1 for when a record has been successfully removed,0 when nothing has been found.
    }

    public function _loadHubs(){
        $this->mongo->hubs->drop();
        $res=LiveData::getHubs();
        foreach($res AS $row){
            $data=['id'=>$row->id,'name'=>$row->hub];
            $this->mongo->hubs->insert($data);
        }
    }

    public function _loadDistricts(){
        $this->mongo->districts->drop();
        $res=LiveData::getDistricts();
        foreach($res AS $row){
            $data=['id'=>$row->id,'name'=>$row->district];
            $this->mongo->districts->insert($data);
        }
    }

    public function _loadRegions(){
        $this->mongo->regions->drop();
        $res=LiveData::getRegions();
        foreach($res AS $row){
            $data=['id'=>$row->id,'name'=>$row->region];
            $this->mongo->regions->insert($data);
        }
    }

    public function _loadCareLevels(){
        $this->mongo->care_levels->drop();
        $res=LiveData::getCareLevels();
        foreach($res AS $row){
            $data=['id'=>$row->id,'name'=>$row->facility_level];
            $this->mongo->care_levels->insert($data);
        }
    }

    public function _loadFacilities(){
        $this->mongo->facilities->drop();
        $res=LiveData::getFacilities();
        foreach($res AS $row){
            $data=['id'=>$row->id,'name'=>$row->facility];
            $this->mongo->facilities->insert($data);
        }
    }


}
