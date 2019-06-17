<?php

namespace EID;


class Mongo{

	public static function connect(){
		$host=env('MONGO_HOST');
		$db=env('MONGO_DB');
		$user=env('MONGO_USER');
		$pass=env('MONGO_PWD');
		$client = new \MongoClient("mongodb://$user:$pass@$host/$db");
		return $client->$db;
	}

	public static function mDate($date_str){
		if(empty($date_str)) $date_str = date("Y-m-d");
		return new \MongoDate(strtotime($date_str));
	}


}