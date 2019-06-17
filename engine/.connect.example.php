<?php 
$link=mysql_connect('localhost',"username","secret");
if(!$link){
	die('connection to server failed:' . mysql_error());
}

$db1="db1";
$db2="db2";
$db3="db3";
?>