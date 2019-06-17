<?php
echo "started at ".date("H:i:s")."\n";
$link=mysql_connect('localhost',"root","chai8910");
if(!$link){
	die('connection to server failed:' . mysql_error());
}

mysql_select_db("rev",$link) or die(mysql_error());

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
					  WHERE facility!='' LIMIT 500");
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

file_put_contents("public/json/regions.json", json_encode($regions));
file_put_contents("public/json/districts.json", json_encode($districts));
file_put_contents("public/json/districts_by_region.json", json_encode($districts_by_region));
file_put_contents("public/json/care_levels.json", json_encode($care_levels));
file_put_contents("public/json/facilities.json", json_encode($facilities));


$years=[2012,2013,2014,2015];
$x=1;
foreach ($years as $year) {
	$month=1;
	$results=[];
	while ($month <= 12) {
		foreach ($facilities as $facility) {
			$samples_received=rand(10,20);
			$hiv_positive_infants=rand(2,$samples_received-8);
			$initiated=rand(0,$hiv_positive_infants-1);
			$pcr_one=rand(10,$samples_received-1);
			$pcr_two=$samples_received-$pcr_one;

			$pcr_one_ages=[];
			$pcr_two_ages=[];
			$i=$pcr_one;
			while($i!=0){
				$pcr_one_ages[]=rand(14,16)/10;
				$i--;
			}

			$j=$pcr_two;
			while($j!=0) {
				$pcr_two_ages[]=rand(12,14);
				$j--;
			}

			$results[]=[
					'month'=>$month,
					'year'=>$year,
					'facility_id'=>$facility['id'],
					'samples_received'=>$samples_received,
					'hiv_positive_infants'=>$hiv_positive_infants,
					'initiated'=>$initiated,
					'pcr_one'=>$pcr_one,
					'pcr_two'=>$pcr_two,
					'pcr_one_ages'=>$pcr_one_ages,
					'pcr_two_ages'=>$pcr_two_ages
					];
				echo "$x record in results\n";
				$x++;
		}
		$month++;		
	}
	file_put_contents("public/json/data.$year.json", json_encode($results));
}

//$data['results']=$results;
//echo file_put_contents("public/json/data.json", json_encode($data));

//echo "\n".count($results)." rows found in results";

echo "finished at ".date("H:i:s")." with $x rows\n";

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