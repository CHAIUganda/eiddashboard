<?php

namespace EID\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use EID\Mongo;
use EID\Models\LiveData;

class EidColumnUpdater extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eidupdate:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds a new column, source. It sets cphl to be the default source of data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->addOption(
            'update_pcr_field',
            null,
            InputOption::VALUE_REQUIRED,
            'Should the pcr field be updated?',
            false
        );

        $this->addOption(
            'year',
            null,
            InputOption::VALUE_REQUIRED,
            'Year for which the data should be pick',
            2014
        );

        $this->addOption(
            'month',
            null,
            InputOption::VALUE_REQUIRED,
            'Month for which the data should be pick',
            0
        );
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

        if($this->option('update_pcr_field')){
            $this->updateSourceField();
        }else if($this->option('year')>0 && $this->option('month') > 0 ){
            $year = $this->option('year');
            $month = $this->option('month');
            $this->updatePCR($year,$month);
        }
        
        
        $this->comment("Engine has stopped at :: ".date('YmdHis'));

    }

   
   /*
        db.collection.update(
           {source:{$ne:'poc'}},
           {source:'cphl'},
           {
             
             multi: true,
             
           }
        )
   */
    private function updateSourceField(){
        $this->comment("Source update started");

        $update_array = array(
            'source' => array('$ne' => 'poc'),
            'source' => 'cphl',
            array('multi'=> true)
            );

        $addNewFieldArray = array('$set' => array(
            
                'source' => 'cphl',
            ));
        $optionsArray = array('multiple' => true );

        $result=$this->mongo->eid_dashboard->update(
                array('source' => array('$ne' => 'poc')),
                $addNewFieldArray,
                $optionsArray
            );
    
       $this->comment("Source update ended successfully");
    }


    private function updatePCR($year,$month){
        $this->comment("PCR updates started");

       
            try {
                
                        $samples_records = LiveData::getPCRs($year,$month);
                        $counter=0;
                        foreach($samples_records AS $s){
                            $this->augmentSampleRecord(
                            $s->id,
                            'pcr',$s->pcr_name
                            );

                           $counter ++;

                        }//end of for loop-samples_records
                        echo " Updated $counter PCR records for $year - $month \n";
                
             
            } catch (Exception $e) {
                var_dump($e);
            }//end catch

     

        $this->comment("PCR updates ended successfully");

    }

    private function augmentSampleRecord($sampleId,$field,$value){
        
        $addNewFieldArray = array('$set' => array(
            $field=>$value
            ));
        $result=$this->mongo->eid_dashboard->update(array('sample_id' => $sampleId), $addNewFieldArray);
       // var_dump($result);
        //return $result['n'];//return 1 for when a record has been successfully removed,0 when nothing has been found.
    }


}
