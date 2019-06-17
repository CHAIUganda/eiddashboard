<?php namespace EID\Closet;

//use HTML;
use Form;

class MyHTML{
	public static function text($name='',$val='',$clss='input_md',$id=null){
		return Form::text($name,$val,array('class'=>$clss,'id'=>$id));
	}

	public static function email($name='',$val='',$clss='input_md',$id=null){
		return Form::email($name,$val,array('class'=>$clss,'id'=>$id));
	}

	public static function hidden($name='',$val='',$id=null,$clss=null){
		return Form::hidden($name,$val,array('id'=>$id,'class'=>$clss));
	}

	public static function select($name,$arr,$default='',$id=null,$clss=null,$onchange=null){
		return Form::select($name,$arr,$default,array('id'=>$id,'class'=>$clss,'onchange'=>$onchange));
	}

	public static function submit($label='Submit',$clss='btn btn-primary',$name=null){
		return Form::submit($label,array('class'=>$clss,'name'=>$name));
	}

	public static function link_to($url='/',$label='link',$clss=null,$onclick=null){
		return link_to($url,$label,array('class'=>$clss,'onclick'=>$onclick));
	}

	public static function checkbox($name="",$value="",$label="",$id=null,$onclick=null){
		$checkbox=Form::checkbox($name,$value,0,['id'=>$id,'onclick'=>$onclick]);
		return "<label class='checkbox-inline'> $checkbox $label</label>";
	}

	public static function datepicker($name,$value,$id){
		$txt=MyHTMl::text($name,$value,null,$id);
		$script="<script> $(function() { $( \"#$id\" ).datepicker(); }); </script>";
		return "$txt $script";
	}

    public static function datepicker2($name,$value,$id){
		$txt=MyHTMl::text($name,$value,null,$id);
		$script="<script> $(function() { $( \"#$id\" ).datepicker(); });";
		return "$txt $script";
	}

	public static function tinyImg($img,$hite=25,$wdth=25){
		return "<img src='/images/$img' height='$hite' width='$wdth'>";
	}

	public static function radio($name="name",$value="1",$fld_value="",$label="",$clss="",$id="",$onchange=""){
		$sChecked=$value==$fld_value?'checked':'';
		$sClss=!empty($clss)?"class='$clss'":"";
		$sId=!empty($id)?"id='$id'":"";
		$sOnChange=!empty($onchange)?"onchange='$onchange'":"";
		return "<label><input type=radio name='$name' value='$value' $sChecked $sClss $sId $sOnChange > $label</label>";
	}

	public static function localiseDate($date,$format='m/d/Y'){
		return date($format,strtotime($date));
	}

	public static function formatDate2STD($date){
		$date_arr=explode("/", $date);
		if(count($date_arr)==3) return $date_arr[2]."-".$date_arr[1]."-".$date_arr[0];
		else return "";
		
	}

	public static function monthYear($name,$is_arr,$y=null,$m=''){
		$y_name=$is_arr==1?$name."_y[]":$name."_y";
		$m_name=$is_arr==1?$name."_m[]":$name."_m";
		if(empty($y) || $y==0) $y=date('Y');
		return MyHTML::selectMonth($m_name,$m)." ".Form::text($y_name,$y,array('class'=>'input_tn','place_holder'=>'YYYY','maxlength'=>'4'));
	}

	public static function selectMonth($name,$val,$id=null){
		$months=[1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sept',10=>'Oct',11=>'Nov',12=>'Dec'];
		return MyHTML::select($name,$months,$val,$id);
	}

	//to be used under javascript
	public static function text2($name,$val){
		return "<input type='text' name='$name' value='$val'>";
	}

	public static function select2($name='',$arr=array(),$default=""){
		$ret="<select name='$name'>";
		foreach ($arr as $k => $v) {
			$slcted=$k==$default?'selected':'';
			$ret.="<option $slcted value='$k'>$v</option>";
		}
		return $ret."</select>";
	}

	public static function getFileExt($file_name){
		$arrr=explode('.', $file_name);
		return array_pop($arrr);
	}

	public static function anchor($url="",$label="",$permission="",$attributes=[]){
		$lnk="";
		if(in_array($permission,session('permission_parents')) || in_array($permission,session('permissions')) || session('is_admin')==1){
			$attr_str="";
			foreach ($attributes as $k => $v)  $attr_str.=" $k='$v' ";
			$lnk="<a $attr_str href='$url'>".$label."</a>";
			//$lnk=Form::link_to($url,$label,$attributes);
		}
		return $lnk;
	}

	public static function permit($permission){
		if(in_array($permission,session('permission_parents')) || in_array($permission,session('permissions')) || session('is_admin')==1){
			return true;
		}else{
			return false;
		}

	}



	public static function lowNumberMsg($nSamples, $nSamplesNeeded = 22){
		$ret="";
		if($nSamples==0){
			$ret="<p class='alert alert-danger'>Sorry no samples approved for worksheet creation</p>";
		}elseif($nSamples<$nSamplesNeeded){
			$x=$nSamplesNeeded-$nSamples;
			$ret="<p class='alert alert-danger'>Sorry you need more $x samples for worksheet creation</p>";
		}else{
			$ret="";
		}
		return $ret;
	}

	public static function months(){
		return [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sept',10=>'Oct',11=>'Nov',12=>'Dec'];
	}

	public static function initMonths(){
		$ret=[];
		for($i=1;$i<=12;$i++){
			$ret[$i]=0;
		}
		return $ret;
	}

	public static function years($min="",$max=""){
		if(empty($min)) $min=1900;
		if(empty($max)) $max=date('Y');
		if($max<$min) return [];
		$yrs_arr=[];
		for($i=$max;$i>=$min;$i--) $yrs_arr[$i]=$i;
		return $yrs_arr;
	}
}