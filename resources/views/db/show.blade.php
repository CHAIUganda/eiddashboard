<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
     <title>@yield('meta-title', 'EID LIMS')</title>
    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/jquery.dataTables.css') }}" rel="stylesheet">    
    <link href="{{ asset('/css/jquery-ui.css')}}" rel="stylesheet" >
    <link href="{{ asset('/css/eid.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/tab.css') }}" rel="stylesheet">

    <script src="{{ asset('/js/general.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/js/jquery-2.1.3.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/js/jquery-ui.js')}}" type="text/javascript"></script>
`
    <script src="{{ asset('/js/tab.js')}}" type="text/javascript"></script>

    <script src="{{ asset('/js/Chart.js')}}" type="text/javascript"></script>
    <script src="{{ asset('/js/angular.min.js')}}" type="text/javascript"></script>
    <script src="{{ asset('/js/angular-route.js')}}" type="text/javascript"></script>

    
</head>

<body ng-app="dashboard" ng-controller="DashController" onload="init()">
<div class="navbar-custom navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="/"> <span class='glyphicon glyphicon-home'></span> EID LIMS</a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                    <li id='l1' class='active'>{!! link_to("/","DASH BOARD",['class'=>'hdr']) !!}</li> 
                    <li id='l2'>{!! link_to("#","REPORTS",['class'=>'hdrs']) !!}</li>             
            </ul>
        </div>
    </div>
</div> 

<div class='container'>
    <br>
    <?php if(!isset($filter_val)) $filter_val="National Metrics, ".date('Y')." thus far" ?>
     <!-- <p><label class='hdr hdr-grey'> FILTERS:</label> <label class='hdr val-grey'>{!! $filter_val !!}</label> </p> -->
    
     <table border='1' cellpadding='0' cellspacing='0' class='filter-tb'>
        <tr>
            <td width='25%'>{!! Form::select('time',[''=>'YEAR']+MyHTML::years(2010),$time,["id"=>"time_fltr"]) !!}</td>
            <td width='25%'>{!! Form::select('region',$regions,"all",['ng-init'=>"region='all'","ng-model"=>"region",'ng-change'=>"filter('region')","id"=>"region_slct"]) !!}</td>
            <td width='25%' id='dist_elmt'>{!! Form::select('district',[''=>'DISTRICT']+$districts,"",["ng-model"=>"district",'ng-change'=>"filter('district')"]) !!}</td>
            <!-- <td width='25%'>{!! Form::select('care_level',[''=>'CARE LEVEL']+$facility_levels) !!}</td> -->
        </tr>
     </table>
     <br>

     <div class="tabs">
        <ul id='tabs'>
            <li class="selected">
                <a href="#tab1" id='tb_hd1' ng-click="setCountPos()">
                <label class='tab-labels'>
                    <span ng-model="count_positives" ng-init="count_positives={!! $count_positives !!}">
                        <% count_positives | number:3 %>
                    </span>
                                
                    
                </label>
                </a>
            </li>
            <li>
                <a href="#tab2" id='tb_hd2'  ng-click="avUptakeRate()">
                    <label class='tab-labels'>
                        <span ng-model="total_samples" ng-init="total_samples={!! $total_samples !!}">
                            <% total_samples %>
                        </span>
                        <font class='tab-sm-ttl'>TOTAL TESTS</font>
                    </label>
                </a>
            </li>
            
            <li>
                <a href="#tab3" id='tb_hd3' ng-click="avInitRate()">
                    <label class='tab-labels'>
                        <span ng-model="av_initiation_rate" ng-init="av_initiation_rate={!! $av_initiation_rate !!}">
                            <% av_initiation_rate %>%
                        </span> 
                        <font class='tab-sm-ttl'>AVERAGE ART INITIATION RATE</font>
                    </label>
                </a>
            </li>
            <li>
                <a href="#tab4" id='tb_hd4' ng-click="avPositivity()">
                    <label class='tab-labels'>
                        <span ng-model="av_positivity" ng-init="av_positivity={!! $av_positivity !!}">
                            <% av_positivity %>%
                        </span>  
                        <font class='tab-sm-ttl'>AVERAGE POSITIVITY RATE</font>
                    </label>
                </a>
            </li>
        </ul>

        <?php $key_nat="<label class='sm_box national'>&nbsp;</label>&nbsp;National"   ?>

        <div>
            <div id="tab1" class="tabContent">   
                <div class="row">
                    <div class="col-lg-6">
                        {!!$key_nat !!}&nbsp;&nbsp;&nbsp;
                        <label class='sm_box hiv-positive-numbers'>&nbsp;</label>&nbsp;Selection
                        <br>
                        <br>
                        <br>
                        <canvas id="hiv_postive_infants" class='db-charts'></canvas> 
                    </div>
                    <!-- <div class="col-lg-6" ng-init='facility_pos_counts=1'>
                        <table class='table table-bordered table-striped table table-condensed facilities' id='tab_id'>
                            <thead>
                                <tr height="10">
                                    <th>Facility</th>
                                    <th>Absolute Positives</th>
                                </tr>
                            </thead>
                            <tbody  ng-model='facility_pos_counts' ng-repeat="fpc in facility_pos_counts">
                                <tr><td><% fpc.facility %></td><td><% fpc.pos_count %></td></tr>
                            </tbody>
                        </table>

                    </div> -->
                </div>                             
            </div>

            <div id="tab2" class="tabContent hide">
               {!!$key_nat !!}&nbsp;&nbsp;&nbsp;
                <label class='sm_box uptake'>&nbsp;</label>&nbsp;Selection
                <br>
                <canvas id="average_uptake_rate" class='db-charts'></canvas> 
            </div>
 
            <div id="tab3" class="tabContent hide">
               {!!$key_nat !!}&nbsp;&nbsp;&nbsp;
                <label class='sm_box init-rate'>&nbsp;</label>&nbsp;Selection
                <br>
                <canvas id="average_init_rate" class='db-charts'></canvas> 
            </div>
 
            <div id="tab4" class="tabContent hide">
                {!!$key_nat !!}&nbsp;&nbsp;&nbsp;
                <label class='sm_box hiv-positive-average'>&nbsp;</label>&nbsp;Selection<br>
                <canvas id="av_positivity" class='db-charts'></canvas>               
            </div>
        </div>
    </div>
    <br>
    <label class='hdr hdr-grey'> ADDITIONAL METRICS:</label>
    <div class='addition-metrics'>
       <div class='row'>
        <div class='col-sm-2'>
            <font class='addition-metrics figure' ng-init="first_pcr_total={!! $first_pcr_total !!}" ng-model='first_pcr_total'><% first_pcr_total %></font><br>
            <font class='addition-metrics desc'>TOTAL 1ST PCR</font>            
        </div>
        <div class='col-sm-2'>
            <font class='addition-metrics figure' ng-init="sec_pcr_total={!! $sec_pcr_total !!}" ng-model='sec_pcr_total'><% sec_pcr_total %></font><br>
            <font class='addition-metrics desc'>TOTAL 2ND PCR</font>            
        </div>       
        <div class='col-sm-2'>
            <font class='addition-metrics figure' ng-init="first_pcr_median_age={!! $first_pcr_median_age !!}" ng-model="first_pcr_median_age">
                <% first_pcr_median_age %>
            </font><br>
            <font class='addition-metrics desc'>MEDIAN MONTHS 1ST PCR</font>            
        </div>
        <div class='col-sm-2'>
            <font class='addition-metrics figure' ng-init="sec_pcr_median_age={!! $sec_pcr_median_age !!}" ng-model="sec_pcr_median_age">
                <% sec_pcr_median_age %>
            </font><br>
            <font class='addition-metrics desc'>MEDIAN MONTHS 2ND PCR</font>            
        </div>
        <div class='col-sm-2'>
            <font class='addition-metrics figure' ng-init="total_initiated={!! $total_initiated !!}" ng-model="total_initiated">
                <% total_initiated %>
            </font><br>
            <font class='addition-metrics desc'>TOTAL ART INITIATED CHILDREN</font>            
        </div>
        <div class='col-sm-2'>
            <font class='addition-metrics figure' ng-init="total_samples={!! $total_samples !!}" ng-model='total_samples'>
                <% total_samples %>
            </font><br>
            <font class='addition-metrics desc'>TOTAL TESTS</font>            
        </div>
       </div>
    </div>
</div>



</body>
<?php
/*
national -- #6D6D6D
blue -- #357BB8
green-- #5EA361
purple -- #9F82D1
yellow -- #F5A623
*/
$chart_stuff=[
    "fillColor"=>"rgba(0,0,0, 0)",
    "strokeColor"=>"#6D6D6D",
    "pointColor"=>"#6D6D6D",
    "pointStrokeColor"=>"#fff",
    "pointHighlightFill"=>"#fff",
    "pointHighlightStroke"=> "#6D6D6D"
    ];

$chart_stuff2=[
    "fillColor"=>"#FFFFCC",
    "strokeColor"=>"#FFCC99",
    "pointColor"=>"#FFCC99",
    "pointStrokeColor"=>"#fff",
    "pointHighlightFill"=>"#fff",
    "pointHighlightStroke"=> "#FFCC99"
    ];


$st2= ["Jan"=>2, "Feb"=>2, "Mar"=>3, "Apr"=>6, "May"=>3, "Jun"=>6, "Jul"=>6,"Aug"=>6,"Sept"=>6,"Oct"=>2,"Nov"=>6,"Dec"=>2];
?>


<script type="text/javascript">
var months=["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul","Aug","Sept","Oct","Nov","Dec"];
var reg_districts=<?php echo json_encode($reg_districts) ?>;
var districts_json=<?php echo json_encode($districts) ?>;
var count_positives_json=<?php echo json_encode($chart_stuff + ["data"=>$count_positives_arr]) ?>;
var count_positives_json2=<?php echo json_encode($chart_stuff2 + ["data"=>$st2]) ?>;
var av_positivity_json=<?php echo json_encode($chart_stuff + ["data"=>$av_positivity_arr]) ?>;

var nums_json=<?php echo json_encode($chart_stuff+["data"=>$nums_by_months]) ?>;


var first_pcr_total_reg=<?php echo json_encode($first_pcr_total_reg) ?>;
var sec_pcr_total_reg=<?php echo json_encode($sec_pcr_total_reg) ?>;
var total_samples_reg=<?php echo json_encode($total_samples_reg) ?>;
var total_initiated_reg=<?php echo json_encode($total_initiated_reg) ?>;


var first_pcr_total_dist=<?php echo json_encode($first_pcr_total_dist) ?>;
var sec_pcr_total_dist=<?php echo json_encode($sec_pcr_total_dist) ?>;
var total_samples_dist=<?php echo json_encode($total_samples_dist) ?>;
var total_initiated_dist=<?php echo json_encode($total_initiated_dist) ?>;


//average initiation rates
/*var av_initiation_rate_reg=<?php echo json_encode($av_initiation_rate_reg) ?>;
var av_initiation_rate_dist=<?php echo json_encode($av_initiation_rate_dist) ?>;
var av_initiation_rate_regM=<?php echo json_encode($av_initiation_rate_regM) ?>;
var av_initiation_rate_distM=<?php echo json_encode($av_initiation_rate_distM) ?>;*/

var av_initiation_rate_reg=<?php echo json_encode($av_initiation_rate_reg) ?>;
var av_initiation_rate_dist=<?php echo json_encode($av_initiation_rate_dist) ?>;
var av_initiation_rate_regM=<?php echo json_encode($av_initiation_rate_regM) ?>;
var av_initiation_rate_distM=<?php echo json_encode($av_initiation_rate_distM) ?>;

var av_initiation_rate_months_json=<?php echo json_encode($chart_stuff+["data"=>$av_initiation_rate_months]) ?>;


$(document).ready( function(){
    var ctx = $("#hiv_postive_infants").get(0).getContext("2d");
   // This will get the first returned node in the jQuery collection. 
   var data = {
        labels: months,
        datasets: [count_positives_json] 
    };
    var myLineChart = new Chart(ctx).Line(data);
});

/*$(document).ready(function() {
    setTimeout($('#tab_id').DataTable(),3000);
    
  });*/

$("#time_fltr").change(function(){
    return window.location.assign("/"+this.value);
});

$("#region_slct").change(function(){
   var items=reg_districts[this.value];
   var options=" ng-model='district' ng-change=\"filter('district')\" ";
   if(this.value=='all'){
    items=districts_json;
   }
   $("#dist_elmt").html(dropDown("district",items,options));
   
});



//angular stuff
var app=angular.module('dashboard', [], function($interpolateProvider) {
        $interpolateProvider.startSymbol('<%');
        $interpolateProvider.endSymbol('%>');
    });
var ctrllers={};

ctrllers.DashController=function($scope,$timeout){
    //for filtering by region
    $scope.positives_by_region=<?php echo json_encode($positives_by_region) ?>;   
    $scope.pos_by_reg_sums=<?php echo json_encode($pos_by_reg_sums) ?>;

    $scope.av_by_region=<?php echo json_encode($av_by_region) ?>;
    $scope.av_by_reg_mth=<?php echo json_encode($av_by_reg_mth) ?>;

    //for filtering by district
    $scope.positives_by_dist=<?php echo json_encode($positives_by_dist) ?>;   
    $scope.pos_by_dist_sums=<?php echo json_encode($pos_by_dist_sums) ?>;

    $scope.av_by_dist=<?php echo json_encode($av_by_dist) ?>;
    $scope.av_by_dist_mth=<?php echo json_encode($av_by_dist_mth) ?>;

    $scope.nums_by_region=<?php echo json_encode($nums_by_region) ?>;
    $scope.nums_by_dist=<?php echo json_encode($nums_by_dist) ?>;

    $scope.filter=function(filterer){
        $scope.setCountPos(filterer);
        $scope.avUptakeRate(filterer);
        $scope.avInitRate(filterer);
        $scope.avPositivity(filterer);
        $scope.setAdditionalMetrics(filterer);
    };

    $scope.setAdditionalMetrics=function(filterer){
        if(filterer=='region'){
            $scope.first_pcr_total=first_pcr_total_reg[$scope.region];
            $scope.sec_pcr_total=sec_pcr_total_reg[$scope.region];
            $scope.total_samples=total_samples_reg[$scope.region];
            $scope.total_initiated=total_initiated_reg[$scope.region];
        }else if(filterer=='district'){
            $scope.first_pcr_total=first_pcr_total_dist[$scope.district];
            $scope.sec_pcr_total=sec_pcr_total_dist[$scope.district];
            $scope.total_samples=total_samples_dist[$scope.district];
            $scope.total_initiated=total_initiated_dist[$scope.district];
        }        
    }

    $scope.setCountPos=function(filterer){
        var filtered_data={};
        if(filterer=='region'){
            $scope.count_positives=$scope.pos_by_reg_sums[$scope.region];
            filtered_data=$scope.positives_by_region[$scope.region];
            //$scope.facility_pos_counts=facility_pos_counts_regs[$scope.region];
            //$('#tab_id').DataTable();
        }else if(filterer=='district'){
            $scope.count_positives=$scope.pos_by_dist_sums[$scope.district];
            filtered_data=$scope.positives_by_dist[$scope.district];
            //$scope.facility_pos_counts=facility_pos_counts_dist[$scope.district];
        }else{
             if($scope.district!=null){
                $scope.count_positives=$scope.pos_by_dist_sums[$scope.district];
                filtered_data=$scope.positives_by_dist[$scope.district];
            }else if($scope.region!="all"){
                $scope.count_positives=$scope.pos_by_reg_sums[$scope.region];
                filtered_data=$scope.positives_by_region[$scope.region];
            }else{
                $scope.count_positives=<?php echo $count_positives ?>;
                filtered_data={};
            }   
        }
        
        $timeout(function(){
            if($("#tb_hd1").hasClass('selected')){
                var ctx = $("#hiv_postive_infants").get(0).getContext("2d");
                var data = {
                    labels: months,datasets: [
                    count_positives_json,
                    {
                        "fillColor":"rgba(0, 0, 0, 0)",
                        "strokeColor":"#357BB8",
                        "pointColor":"#357BB8",
                        "pointStrokeColor":"#fff",
                        "pointHighlightFill":"#fff",
                        "pointHighlightStroke":"#357BB8",
                        "data":filtered_data
                    }] 
                };
                var myLineChart = new Chart(ctx).Line(data);
            }

        },1);
    };

    $scope.avUptakeRate=function(filterer){
        var filtered_data={};
        if(filterer=='region'){
            filtered_data=$scope.nums_by_region[$scope.region];
        }else if(filterer=='district'){
            filtered_data=$scope.nums_by_dist[$scope.district];
        }else{
             if($scope.district!=null){
                filtered_data=$scope.nums_by_dist[$scope.district];
            }else if($scope.region!="all"){
                filtered_data=$scope.nums_by_region[$scope.region];
            }else{
                filtered_data={};
            }   
        }
        
        $timeout(function(){
            if($("#tb_hd2").hasClass('selected')){
                var ctx = $("#average_uptake_rate").get(0).getContext("2d");
                var data = {
                    labels: months,datasets: [
                    nums_json,
                    {
                        "fillColor":"rgba(0, 0, 0, 0)",
                        "strokeColor":"#5EA361",
                        "pointColor":"#5EA361",
                        "pointStrokeColor":"#fff",
                        "pointHighlightFill":"#fff",
                        "pointHighlightStroke":"#5EA361",
                        "data":filtered_data
                    }] 
                };
                var myLineChart = new Chart(ctx).Line(data);
            }

        },1);

    };

    $scope.avInitRate=function(filterer){
        var filtered_data={};
        if(filterer=='region'){
            $scope.av_initiation_rate=av_initiation_rate_reg[$scope.region];
            filtered_data=av_initiation_rate_regM[$scope.region];
        }else if(filterer=='district'){
            $scope.av_initiation_rate=av_initiation_rate_dist[$scope.district];
            filtered_data=av_initiation_rate_distM[$scope.district];
        }else{
             if($scope.district!=null){
                $scope.av_initiation_rate=av_initiation_rate_dist[$scope.district];
                filtered_data=av_initiation_rate_distM[$scope.district];
            }else if($scope.region!="all"){
                $scope.av_initiation_rate=av_initiation_rate_reg[$scope.region];
                filtered_data=av_initiation_rate_regM[$scope.region];
            }else{
                filtered_data={};
            }   
        }
        
        $timeout(function(){
            if($("#tb_hd3").hasClass('selected')){
                var ctx = $("#average_init_rate").get(0).getContext("2d");
                var data = {
                    labels: months,datasets: [
                    av_initiation_rate_months_json,
                    {
                        "fillColor":"rgba(0, 0, 0, 0)",
                        "strokeColor":"#F5A623",
                        "pointColor":"#F5A623",
                        "pointStrokeColor":"#fff",
                        "pointHighlightFill":"#fff",
                        "pointHighlightStroke":"#F5A623",
                        "data":filtered_data
                    }] 
                };
                var myLineChart = new Chart(ctx).Line(data);
            }

        },1);

    };


    $scope.avPositivity=function (filterer){
        var filtered_data={};
        if(filterer=='region'){
            $scope.av_positivity=$scope.av_by_region[$scope.region];
            filtered_data=$scope.av_by_reg_mth[$scope.region];
        }else if(filterer=='district'){
            $scope.av_positivity=$scope.av_by_dist[$scope.district];
            filtered_data=$scope.av_by_dist_mth[$scope.district];
        }else{
            if($scope.district!=null){
                $scope.av_positivity=$scope.av_by_dist[$scope.district];
                filtered_data=$scope.av_by_dist_mth[$scope.district];
            }else if($scope.region!="all"){
                $scope.av_positivity=$scope.av_by_region[$scope.region];
                filtered_data=$scope.av_by_reg_mth[$scope.region];
            }else{
                $scope.av_positivity=<?php echo $av_positivity ?>;
                filtered_data={};
            }       
        }
        
        $timeout(function(){
            if($("#tb_hd4").hasClass('selected')){
                var ctx = $("#av_positivity").get(0).getContext("2d");
                var data = {
                    labels: months,datasets: [
                    av_positivity_json,{
                        "fillColor":"rgba(0, 0, 0, 0)",
                        "strokeColor":"#9F82D1",
                        "pointColor":"#9F82D1",
                        "pointStrokeColor":"#fff",
                        "pointHighlightFill":"#fff",
                        "pointHighlightStroke":"#9F82D1",
                        "data":filtered_data
                    }] 
                };
                var myLineChart = new Chart(ctx).Line(data);
            };
        },1);
    }
};

app.controller(ctrllers);
</script>
</html>
