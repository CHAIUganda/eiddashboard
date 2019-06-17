<?php
echo "started at ".date("H:i:s")."\n";
require_once(".connect.php");
mysql_select_db($db1,$link) or die(mysql_error());

/*function districts(){
	$ret=[];
	$res=mysql_query("SELECT id,district from districts");
	while ($row=mysql_fetch_array($res)){
		extract($row);
		$ret[]=['id'=>$id,'name'=>$district];
	}
	return $ret;
}

function hubs(){
	$ret=[];
	$res=mysql_query("SELECT id,hub from hubs");
	while ($row=mysql_fetch_array($res)){
		extract($row);
		$ret[]=['id'=>$id,'name'=>$hub];
	}
	return $ret;
}*/
function districts(){
	$ret=["dists","reg_dists"];
	$res=mysql_query("SELECT id,district,regionID from districts");
	while ($row=mysql_fetch_array($res)){
		extract($row);
		$ret["dists"][$id]=$district;
		$ret["reg_dists"][$regionID][$id]=$district;
	}
	return $ret;
}

function hubs(){
	$ret=[];
	$res=mysql_query("SELECT id,hub from hubs");
	while ($row=mysql_fetch_array($res)){
		extract($row);
		$ret[$id]=$hub;
	}
	return $ret;
}

function careLevels(){
	$ret=[];
	$res=mysql_query("SELECT id,facility_level FROM facility_levels");
	while ($row=mysql_fetch_array($res)){
		extract($row);
		$ret[$id]=$facility_level;
	}
	return $ret;
}

function regions(){
	$ret=[];
	$res=mysql_query("SELECT id,region FROM regions");
	while ($row=mysql_fetch_array($res)){
		extract($row);
		$ret[$id]=$region;
	}
	return $ret;
}

function facilities(){
	$ret=[];
	$res=mysql_query("SELECT f.id,facility,districtID,hubID,f.facilityLevelID,d.regionID
					  FROM facilities AS f
					  LEFT JOIN districts AS d ON f.districtID=d.id 
					  WHERE facility!=''");
	while ($row=mysql_fetch_array($res)){
		extract($row);
		$ret[$id]=[ 'id'=>$id,
					'name'=>$facility,
					'district_id'=>$districtID,
					'region_id'=>$regionID,
					'care_level_id'=>$facilityLevelID];
	}
	return $ret;
}

$regions=regions();
$ds=districts();
$districts=$ds["dists"];
$districts_by_region=$ds["reg_dists"];
$care_levels=careLevels();
$facilities=facilities();

file_put_contents("../public/json/regions.json", json_encode($regions));
file_put_contents("../public/json/districts.json", json_encode($districts));
file_put_contents("../public/json/districts_by_region.json", json_encode($districts_by_region));
file_put_contents("../public/json/care_levels.json", json_encode($care_levels));
file_put_contents("../public/json/facilities.json", json_encode($facilities));

mysql_select_db($db2,$link) or die(mysql_error());

function getData($year,$cond=1){
	$ret=[];
	$sql="SELECT count(s.ID) AS num,MONTH(s.datetested) AS mth,s.facility AS facility_id 
		  FROM samples AS s
		  LEFT JOIN patientsfollowup AS f ON s.parentid=f.parentID
		  WHERE YEAR(s.datetested)=$year AND s.repeatt!=1  AND $cond
		  GROUP BY facility_id,mth";
	$res=mysql_query($sql) or die(mysql_error());
	$x=0;
	while($row=mysql_fetch_array($res)){ 
		extract($row);
		$ret[$mth][$facility_id]=$num;
		$x++;
	}
	return $ret;
}

function getAges($year,$pcr){
	$ret=[];
	$sql="SELECT p.age AS infant_age,MONTH(s.datetested) AS mth,s.facility AS facility_id 
		  FROM patients AS p 
		  LEFT JOIN samples AS s ON p.ID=s.accessionno
		  WHERE YEAR(s.datetested)=$year AND s.pcr='$pcr'
		  GROUP BY facility_id,mth,infant_age";
	$res=mysql_query($sql);
	while($row=mysql_fetch_array($res)){ 
		extract($row);
		$ret[$mth][$facility_id][]=cleanAge($infant_age);
	}
	return $ret;
}

/*function getAges($year,$month,$facility_id,$pcr,$cond=1){
	$ret=[];
	$sql="SELECT infant_age,MONTH(s.date_results_entered) AS mth,facility_id FROM dbs_samples AS s 
		  LEFT JOIN batches AS b ON s.batch_id=b.id
		  LEFT JOIN facilities AS f ON b.facility_id=f.id
		  WHERE YEAR(s.date_results_entered)=$year AND MONTH(s.date_results_entered)=$month 
		  AND s.PCR_test_requested='YES' AND facility_id=$facility_id AND $cond
		  ";
	$res=mysql_query($sql);
	while($row=mysql_fetch_array($res)){ 
		extract($row);
		$ret[]=cleanAge($infant_age);
	}
	return $ret;
}*/

function cleanAge($age=0){
	$ret=0;
	$age_arr=explode(" ", $age);
	$years=0;$months=0;$weeks=0;$days=0;
	foreach ($age_arr as $k => $val) {
		if($val=='year'||$val=='years'){
			$years=str_replace(" ", "",$age_arr[($k-1)]);
		}elseif($val=='months'||$val=='month'){
			$months=str_replace(" ", "",$age_arr[($k-1)]);
		}elseif($val=='weeks'||$val=='week'){
			$weeks=str_replace(" ", "",$age_arr[($k-1)]);
		}elseif($val=='days'||$val=='day'){
			$days=str_replace(" ", "",$age_arr[($k-1)]);
		}else{
			$months=$val;
		}
	}
	$ret= ($years*12)+$months+($weeks/4)+($days/30);
	return round($ret,2);
}



/*$data=[];
$data['districts']=districts();
$data['hubs']=hubs();
$data['facilities']=facilities();
$data['age_group']=[1=>" < 5",2=>" 5 - 9",3=>" 10 - 18",4=>"19 - 25","26+"];*/
$kitutu_results=[];
function res($db=1){
global $kitutu_results;

$all_results=[];
$current_yr=date("Y");
$year=2014;
$x=1;
while ($year<=$current_yr) {
	$month=1;
	$results=[];
	if($db==2){
		$samples_received_res=getData2($year);
		$hpi_res=getData2($year," accepted_result='POSITIVE'");
		$i_res=getData2($year," f_ART_initiated='YES'");
		$pcr1_res=getData2($year," pcr='FIRST'");
		$pcr2_res=getData2($year," pcr='SECOND'");
		$pcr_one_ages_res=getAges2($year,'FIRST');
		$pcr_two_ages_res=getAges2($year,'SECOND');
	}else{
		$samples_received_res=getData($year);
		$hpi_res=getData($year," result=2");
		$i_res=getData($year," childEnrolledOnART='Yes'");
		$pcr1_res=getData($year," pcr='1'");
		$pcr2_res=getData($year," pcr='2'");
		$pcr_one_ages_res=getAges($year,'1');
		$pcr_two_ages_res=getAges($year,'2');
	}

	ksort($samples_received_res);	

	foreach ($samples_received_res as $mth => $f_arr) {
		foreach ($f_arr as $f_id => $num) {
			$samples_received=$num;
			$hpi= isset($hpi_res[$mth][$f_id])?$hpi_res[$mth][$f_id]:0;
			$intd=isset($i_res[$mth][$f_id])?$i_res[$mth][$f_id]:0;
			$pcr_one=isset($pcr1_res[$mth][$f_id])?$pcr1_res[$mth][$f_id]:0;
			$pcr_two=isset($pcr2_res[$mth][$f_id])?$pcr2_res[$mth][$f_id]:0;
			$pcr_one_ages=isset($pcr_one_ages_res[$mth][$f_id])?$pcr_one_ages_res[$mth][$f_id]:[];
			$pcr_two_ages=isset($pcr_two_ages_res[$mth][$f_id])?$pcr_two_ages_res[$mth][$f_id]:[];

			$rw=[
			'month'=>$mth,
			'year'=>$year,
			'facility_id'=>$f_id,
			'samples_received'=>$samples_received,
			'hiv_positive_infants'=>$hpi,
			'initiated'=>$intd,
			'pcr_one'=>$pcr_one,
			'pcr_two'=>$pcr_two,
			'pcr_one_ages'=>$pcr_one_ages,
			'pcr_two_ages'=>$pcr_two_ages
			];
			$results[]=$rw;
			$all_results[]=$rw;
			$kitutu_results[]=$rw;
		echo "record :: $x\n";
		$x++;		
		}

	}
	//file_put_contents("public/json/data.$year.json", json_encode($results));
	$year++;
}

//return $all_results;
}

res(1);

mysql_select_db($db3,$link) or die(mysql_error());

function getData2($year,$cond=1){
	$ret=[];
	$sql="SELECT count(s.id) AS num,MONTH(s.date_results_entered) AS mth,facility_id FROM dbs_samples AS s 
		  LEFT JOIN batches AS b ON s.batch_id=b.id
		  LEFT JOIN facilities AS f ON b.facility_id=f.id
		  WHERE YEAR(s.date_results_entered)=$year AND s.PCR_test_requested='YES'  AND $cond
		  GROUP BY facility_id,mth";
	$res=mysql_query($sql);
	$x=0;
	while($row=mysql_fetch_array($res)){ 
		extract($row);
		$ret[$mth][$facility_id]=$num;
		$x++;
	}
	return $ret;
}

function getAges2($year,$pcr){
	$ret=[];
	$sql="SELECT infant_age,MONTH(s.date_results_entered) AS mth,facility_id FROM dbs_samples AS s 
		  LEFT JOIN batches AS b ON s.batch_id=b.id
		  LEFT JOIN facilities AS f ON b.facility_id=f.id
		  WHERE YEAR(s.date_results_entered)=$year AND s.PCR_test_requested='YES' AND pcr='$pcr'
		  GROUP BY facility_id,mth,infant_age";
	$res=mysql_query($sql);
	while($row=mysql_fetch_array($res)){ 
		extract($row);
		$ret[$mth][$facility_id][]=cleanAge($infant_age);
	}
	return $ret;
}

res(2);

//$data['results']+=res(2);
/*
$data['results']=$kitutu_results;
$data['data_date']="Data last updated at ".date("H:i:s")." on ".date("d/m/Y");
echo file_put_contents("../public/json/data.json", json_encode($data));

//echo "\n".count($all_results)." rows found in results";

echo "finished at ".date("H:i:s")."\n";
*/
//var_dump($data['facilities']);


/*

from_year=2013,from_month=2
to_year=2015,to_month=5

i=from_year;
duration=[];
while(i<=to_year){
	stat=(i==from_year)?from_month:1;
	end=(i==to_year)?to_month:12;
	j=stat;
	while(j<=end){
		duration=[i."-".j];
		j++;	
	}	
	i++;	
}

*/
?>