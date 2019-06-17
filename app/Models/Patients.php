<?php

class Patient extends \Eloquent {

	const MALE = "M";
	const FEMALE  = "F";
	const LEFT_BLANK = "Left Blank";


	protected $table = 'patients';
	protected $guarded = array('AutoID');


	public $timestamps = false;


	public function setGenderAttribute($infant_gender){

		if($infant_gender === "MALE"){
			$this->attributes['gender'] = self::MALE;
			return;
		}

		if($infant_gender === "FEMALE"){
			$this->attributes['gender'] = self::FEMALE;
			return;
		}

		$this->attributes['gender'] = self::LEFT_BLANK;
		return;
	}

}