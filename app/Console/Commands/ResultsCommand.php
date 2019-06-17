<?php

namespace EID\Console\Commands;

use Illuminate\Console\Command;
use EID\Models\LiveData;

class ResultsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'results:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Results';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
         ini_set('memory_limit', '3500M');

        $this->comment("Engine has started at :: ".date('YmdHis'));
        
       
        
       try {
           $this->generateResults();
       } catch (Exception $e) {
           $this->comment($e->message());
       }
        
        
        $this->comment("Engine has stopped at :: ".date('YmdHis'));
        
    }

    private function generateResults(){
        //LIVE_HOST2=localhost:3306
        //LIVE_DATABASE2=eidlive
        //LIVE_USERNAME2=root
        //LIVE_PASSWORD2=7hankujc


        //pick data
        $year=2018;
        $month=1;
        $res=LiveData::getAdhocResults($year,$month);
        $this->comment("Results retrieved at :: ".date('YmdHis'));
        //foreach($res AS $row){
        //    $data=['id'=>$row->id,'name'=>$row->facility];
            
        //}

        //Load periferal data
        $users_map = $this->getUsersMap();
        $hubs_map = $this->getHubsMap();
        $facilities_map=$this->getFacilitiesMap();
        $districts_map = $this->getDistrictsMap();
                
        $file_name="/tmp/file_".$year."_".$month.".csv";

        $fp = fopen($file_name, 'w');

        $header=['specimen_id','exp_number','district',
        'facility_sample_came_from','type_of_pcr','accepted_result',
        'date_of_sample_collection','date_dispatched_from_facility','date_rcvd_by_cphl',
        'date_dbs_tested','date_results_entered','date_printed','printed_by','printed_at_hub'];
        fputcsv($fp, $header);

        foreach ($res as $obj) {
            $record=[];
            array_push($record, $obj->specimen_id);
            array_push($record, $obj->exp_number);
            array_push($record, $this->getDistrictName($districts_map,$facilities_map,$obj->facility_id));

            array_push($record, $obj->facility_sample_came_from);
            array_push($record, $obj->type_of_pcr);
            array_push($record, $obj->accepted_result);
            
            array_push($record, $obj->date_of_sample_collection);
            array_push($record, $obj->date_dispatched_from_facility);
            array_push($record, $obj->date_rcvd_by_cphl);

            array_push($record, $obj->date_dbs_tested);
            array_push($record, $obj->date_results_entered);
            array_push($record, $obj->date_printed);

            array_push($record, $obj->printed_by);
            array_push($record, $this->getHubName($hubs_map,$users_map,$obj->dispatch_by));
            //var_dump($fields);
            //break;
            fputcsv($fp, $record);
        }

        fclose($fp);
        //generate csv
    }

    private function getHubName($hubs_map,$users_map,$user_id){
        if(!isset($user_id)){
            return NULL;
        }elseif ($user_id == 0) {
           return 'Unknown';
        }
        $user_key = 'k_'.$user_id;
        $user_object = $users_map[$user_key];
        $hub_id = $user_object->hubID;

        if ($hub_id == 0) {return 'Unknown';}
        $hub_key='k_'.$hub_id;
        $hub_object = $hubs_map[$hub_key];

        return $hub_object->hub;

    }

    private function getDistrictName($districts_map,$facilities_map,$facility_id){
        if(!isset($facility_id)){
            return NULL;
        }elseif ($facility_id == 0) {
           return 'Unknown';
        }

        $facility_key = 'k_'.$facility_id;
        $facility_object = $facilities_map[$facility_key];

        $district_key = 'k_'.$facility_object->districtID;
        $district_object = $districts_map[$district_key];

        return $district_object->district;
    }
    private function getUsersMap(){
        $res=LiveData::getUsers();
        $users_map=[];
        foreach ($res as $obj) {
            $key = 'k_'.$obj->id;
            $users_map[$key]=$obj;
        }
        
        return $users_map;
    }

    private function getHubsMap(){
        $res=LiveData::getHubs();
        $new_map=[];
        foreach ($res as $obj) {
            $key = 'k_'.$obj->id;
            $new_map[$key]=$obj;
        }
        
        return $new_map;
    }

    private function getDistrictsMap(){
        $res=LiveData::getDistricts();
        $new_map=[];
        foreach ($res as $obj) {
            $key = 'k_'.$obj->id;
            $new_map[$key]=$obj;
        }
        
        return $new_map;
    }

    private function getFacilitiesMap(){
        $res=LiveData::getFacilities();
        $new_map=[];
        foreach ($res as $obj) {
            $key = 'k_'.$obj->id;
            $new_map[$key]=$obj;
        }
        
        return $new_map;
    }
}
