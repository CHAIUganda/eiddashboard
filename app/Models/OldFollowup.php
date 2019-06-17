<?php  namespace EID\Models;

use Illuminate\Database\Eloquent\Model;

class OldFollowup extends Model {
	protected $connection = 'mysql2';
	protected $table = 'patientsfollowup';

	public $timestamps = false;


}