<?php namespace EID\Models;

use Illuminate\Database\Eloquent\Model;
class Worksheet extends Model {

	protected $table = 'lab_worksheets';

	protected $guarded = array('id');
	protected $dates = array('Kit_ExpiryDate');

	public $timestamps = false;
}
