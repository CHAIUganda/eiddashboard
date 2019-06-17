<?php
echo "started at ".date("H:i:s")."\n";
$link=mysql_connect('localhost',"root","chai8910");
if(!$link){
	die('connection to server failed:' . mysql_error());
}

mysql_select_db("rev",$link) or die(mysql_error());


$res=mysql_query("DESC batches");
$cols=[];
while($row=mysql_fetch_array($res)){
	$cols[]="`".$row['Field']."`";
}

$cols_str=implode(",", $cols);

$csv_data = array_map('str_getcsv', file('batches.csv'));

//echo count($csv_data);

insert("batches",$cols_str,$csv_data);

function cleanVals($row){
	$ret=[];
	foreach ($row as $v) {
		$ret[]="'$v'";		
	}
	return $ret;
}

function insert($table,$cols_str,$data){
	foreach ($data as $row) {
		$row2=cleanVals($row);
		$vals_str=implode(",", $row2);
		$sql="INSERT INTO `$table` ($cols_str) VALUES ($vals_str)";	
		if(mysql_query($sql)){
			echo "going good\n";
		}else{
			echo "bad bad bad bad bad bad $sql\n";
		}
	}
}

