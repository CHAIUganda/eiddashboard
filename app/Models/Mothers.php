<?php

class Mother extends \Eloquent {

// 	WARNING: 
//	these constants should really come from feedings table
		const YES = 1;
		const NO  = 2;
		const UNKNOWN = 3;


		protected $table = 'mothers';
		protected $guarded = array('ID');

		public $timestamps = false;


		public function setFeedingAttribute($breast_feeding){

			if($breast_feeding === "YES"){
				$this->attributes['feeding'] = self::YES;
				return;
			}

			if($breast_feeding === "NO"){
				$this->attributes['feeding'] = self::NO;
				return;
			}

			$this->attributes['feeding'] = self::UNKNOWN;
			return;
		}

}