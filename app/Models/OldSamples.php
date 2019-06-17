<?php

class OldSample extends \Eloquent {

// 	WARNING: 
// 	these constants should come from receivedstatus table.
		const ACCEPTED = 1;
		const REJECTED = 2;


		protected $table = 'samples';
		protected $guarded = array('ID');


		public $timestamps = false;


		public function setReceivedstatusAttribute($dbs_sample_rejected){

			if($dbs_sample_rejected === "NO"){
				$this->attributes['receivedstatus'] = self::ACCEPTED;
				return;
			}

			if($dbs_sample_rejected === "YES"){
				$this->attributes['receivedstatus'] = self::REJECTED;
				return;
			}
		}
}