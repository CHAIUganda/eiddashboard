<?php

class UserType extends Eloquent
{

    protected $table = 'user_types';

    protected $fillable = [ 'type', 'descr' ];

    public $timestamps = false;


    public function setType($type_of_user){
        $this->type = $type_of_user;
    }
    public function getType(){
        return $this->type;
    }


    public function setDescr($descr_of_user_type){
        $this->descr = $descr_of_user_type;
    }
    public function getDescr(){
        return $this->descr;
    }

}
