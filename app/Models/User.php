<?php namespace EID\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;	

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	use Authenticatable, CanResetPassword;

	protected $table = 'users';
	public $timestamps = false;


    /**
     * Fillable fields
     *
     * @var array
     */

    public static $rules = [
		'username' => 'required',
		'type'=>'required',
		'email'=>'required'
	];

    protected $fillable = [
        'username',
        'type',
        'email',
        'password',
        'signature',
        'other_name',
        'family_name',
        'telephone',
        'facilityID',
        'hubID',
        'ipID',
        'created',
        'createdby'
    ];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password');

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the token value for the "remember me" session.
	 *
	 * @return string
	 */
	public function getRememberToken()
	{
		return $this->remember_token;
	}

	/**
	 * Set the token value for the "remember me" session.
	 *
	 * @param  string  $value
	 * @return void
	 */
	public function setRememberToken($value)
	{
		$this->remember_token = $value;
	}

	/**
	 * Get the column name for the "remember me" token.
	 *
	 * @return string
	 */
	public function getRememberTokenName()
	{
		return 'remember_token';
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}
    
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = \Hash::make($password);
    }

    public static function getUsers(){
    	return User::leftjoin('user_roles AS r','u.type','=','r.id')
    		   ->select('u.*','r.description AS user_role',\DB::raw("(CASE WHEN u.deactivated = 1 THEN 'Yes'  ELSE 'No' END) AS deactive"))
    		   ->from('users AS u')->get();
    }

    public static function getUser($id){
    	return User::leftjoin('user_roles AS r','users.type','=','r.id')
    	       ->leftjoin('facilities AS fclty','users.facilityID','=','fclty.id')
    	       ->leftjoin('hubs AS hb','users.hubID','=','hb.id')
    	       ->leftjoin('ips','users.ipID','=','ips.id')
    	       ->select('users.*','r.description AS user_role','fclty.facility','hb.hub','ips.ip')
    	       ->findOrFail($id);
    }

    public static function getUserByUsername($username){
    	return User::leftjoin('user_roles AS r','users.type','=','r.id')
    	       ->select('users.*','r.description AS user_role','r.permissions')
    	       ->where('username',$username)
    	       ->first();
    }

    public static function role_users_arr(){
    	$users=User::all();
    	$arr=[];
    	foreach ($users as $user) {
    		$arr[$user->type][$user->id]="$user->family_name $user->other_name ($user->username)";
    	}
    	return $arr;
    }

}
