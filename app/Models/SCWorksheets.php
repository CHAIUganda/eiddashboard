<?php

class SCWorksheet extends \Eloquent {// Sickle Cell Worksheet

	protected $table = 'sc_worksheets';

	protected $guarded = array('id');
	// protected $dates = array('DateTested');

	public $timestamps = false;
}
