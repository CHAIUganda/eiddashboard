<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('meta-title', 'Uganda EID Dashboard')</title>
    <link rel="Shortcut Icon" href="{{ asset('/images/icon.png') }}" />
    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/jquery.dataTables.css') }}" rel="stylesheet">    
    <link href="{{ asset('/css/jquery-ui.css')}}" rel="stylesheet" >

     <link href="{{ asset('/css/nv.d3.min.css') }}" rel="stylesheet" type="text/css">

    <link rel="stylesheet" type="text/css" href="{{ asset('/css/demo.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('/css/tabs.css') }} " />
    <link rel="stylesheet" type="text/css" href="{{ asset('/css/tabstyles.css') }}" />

    <link rel="stylesheet" type="text/css" href="{{ asset('/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('/css/font-awesome.min.css') }}" />
    <link href="{{ asset('/css/dash.css') }}" rel="stylesheet"/>

    <script src="{{ asset('/js/modernizr.custom.js') }}"></script>

    
    <script src="{{ asset('/js/general.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/js/jquery-2.1.3.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
    <!--script type = "text/javascript" 
         src = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js">
      </script-->
    <script src="{{ asset('/js/jquery-ui.min.js')}}" type="text/javascript"></script>
    <script src="{{ asset('/twitter-bootstrap-3.3/js/bootstrap.min.js') }}" type="text/javascript" ></script>

    <script src="{{ asset('/js/angular.min.js')}}" type="text/javascript"></script>
    <script src="{{ asset('/js/angular-route.js')}}" type="text/javascript"></script>

    <script src="{{ asset('/js/angular-datatables.min.js') }}" type="text/javascript"></script>


   
    <!--script src="{{ asset('/js/d3.min.js') }}" charset="utf-8"></script>
    <script src="{{ asset('/js/nv.d3.min.js') }}"></script>
    <script src="{{ asset('/js/stream_layers.js') }}"></script-->
    <script src="{{ asset('/js/highcharts/highcharts.src.js') }}"></script>

    <!--script src="https://code.highcharts.com/highcharts.js"></script-->
    <script src="{{ asset('/js/highcharts/exporting.js') }}"></script>
    <script src="{{ asset('/js/highcharts/export-data.js') }}"></script>
    <script src="{{ asset('/js/highcharts-ng.js')}}"></script>
    <!--script src="https://code.highcharts.com/modules/series-label.js"></script-->
    
    <!--script src="https://highcharts.github.io/export-csv/export-csv.js"></script-->
 


    <script>
    $(document).ready(function(){

       $("#highchart1").addClass("hidden");
       $("#highcharthivpositiveinfants").addClass("hidden");
       $("#highcharthivpositivityrate").addClass("hidden");
        //$("#highchart1").remove();


    

     $( "#datepicker_start" ).datepicker({
        format: 'dd-mm-yyyy'
     }
     );
     $( "#datepicker_end" ).datepicker();

    });
    </script>

    <style type="text/css">
    .nv-point {
        stroke-opacity: 1!important;
        stroke-width: 5px!important;
        fill-opacity: 1!important;
    }
    </style>


    <!-- CSS and Javascript for pop -->
        <style type="text/css">
            
            /* Popup box BEGIN */
        .hover_bkgr_fricc{
            background:rgba(0,0,0,.4);
            cursor:pointer;
            display:none;
            height:100%;
            position:fixed;
            text-align:center;
            top:0;
            width:100%;
            z-index:10000;
        }
        .hover_bkgr_fricc .helper{
            display:inline-block;
            height:100%;
            vertical-align:middle;
        }
        .hover_bkgr_fricc > div {
            background-color: #fff;
            box-shadow: 10px 10px 60px #555;
            display: inline-block;
            height: auto;
            max-width: 551px;
            min-height: 100px;
            vertical-align: middle;
            width: 60%;
            position: relative;
            border-radius: 8px;
            padding: 15px 5%;
        }
        .popupCloseButton {
            background-color: #fff;
            border: 3px solid #999;
            border-radius: 50px;
            cursor: pointer;
            display: inline-block;
            font-family: arial;
            font-weight: bold;
            position: absolute;
            top: -20px;
            right: -20px;
            font-size: 25px;
            line-height: 30px;
            width: 30px;
            height: 30px;
            text-align: center;
        }
        .popupCloseButton:hover {
            background-color: #ccc;
        }
        .trigger_popup_fricc {
            cursor: pointer;
            font-size: 20px;
            margin: 20px;
            display: inline-block;
            font-weight: bold;
        }
        /* Popup box BEGIN */
        </style>

        <script type="text/javascript">

         $(document).ready(function(){
            $(window).load(function () {
                $('.hover_bkgr_fricc').show();

                $(".trigger_popup_fricc").click(function(){
                   $('.hover_bkgr_fricc').show();
                });
                $('.hover_bkgr_fricc').click(function(){
                    $('.hover_bkgr_fricc').hide();
                });
                $('.popupCloseButton').click(function(){
                    $('.hover_bkgr_fricc').hide();
                });
            });
            
        });
        </script>
    <!-- end of CSS and Javascript for pop -->


    
</head>

<body ng-app="dashboard" ng-controller="DashController">
@if(!Auth::check())
        <div class="hover_bkgr_fricc">
                <span class="helper"></span>
                <div>
                    <div class="popupCloseButton">X</div>
                    <div class="modal-header text-danger">Why wait? - Print EID and Viral load results now!</div>

                    <div class="modal-body text-left text-danger">
                        <small>EID and Viral Load results can be printed real time from the Viral load and EID dashboards by the 
                            Electronic results printing (e-RD).</small>

                        <ul class="list-unstyled hover">
                        <li>Advantages/Benefits</li>
                            <ul><small>Instant access to completed results</small></ul>
                            <ul><small>Continuous access to old results</small></ul>
                            <ul><small>Ability to store electronic copy of results</small></ul>
                            
                        <li>What you need to print</li>
                            <ul><small>Computer</small></ul>
                            <ul><small>Printer</small></ul>
                            <ul><small>Paper, A4</small></ul>
                            <ul><small>Envelopes</small></ul>
                            <ul><small>Internet Access</small></ul>
                        <li>How to enroll for e-RD</li>
                <ul><small>We call upon all facilities with capacity to print to contact our 
                    customer care on 0800221100 or send email 
                    to  <a>customercare@cphl.go.ug</a> for enrollment.</small></ul>
                    </ul>

                    </div>
                    

                </div>
        </div>
    @endif
<div class="navbar-custom navbar navbar-inverse navbar-fixed-top" role="navigation">
    <img src="{{ asset('/images/uganda_flag2.png') }}" style="width:100%;height:10px;margin:0px">
    <div class="container">

        <div class="navbar-header"> 
            <a class="navbar-brand" href="/" style="font-weight:800px;color:#FFF"> UGANDA EID</a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li id='l1' class='active'>{!! link_to("/","DASHBOARD",['class'=>'hdr']) !!}</li>  
               <!--  <li id='l2'>{!! link_to("/reports","REPORTS",['class'=>'hdr']) !!}</li>  -->  
               <li id='l3'><a href='https://www.cphluganda.org/results'>RESULTS</a></li>  
                     
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><span style="font-size: 30px;vertical-align: middle;margin-right:25px;"> <img src="{{ asset('/images/ug.png') }}" height="35" width="35"> </span></li>
            </ul>
        </div>

    </div>
</div> 

<div class='container'>
    
    <?php //if(!isset($filter_val)) $filter_val="National Metrics, ".$time." thus far" ?>
      
     <?php
     function latestNMonths($n=12){
        $ret=[];
        $m=date('n');
        $y=date('Y');
        for($i=1;$i<=$n;$i++){
            if($m==0){
                $m=12;
                $y--;
            }
            array_unshift($ret, "$y-$m");
            $m--;
        }
        return $ret;
    }

    function yearByMonths($from_year=1900,$from_month=1,$to_year="",$to_month=""){
        if(empty($to_year)) $to_year=date("Y");
        if(empty($to_month)) $to_month=date("m");
        $ret=[];
        $i=$from_year;
        while($i<=$to_year){
            $yr_arr=["yr"=>$i,"y"=>substr($i,-2),"mths"=>[]];
            $stat=($i==$from_year)?$from_month:1;
            $end=($i==$to_year)?$to_month:12;
            $j=$stat;
            while($j<=$end){
                $yr_arr["mths"][]=$j;
                $j++;   
            } 
            $i++; 
            $ret[]=$yr_arr;
        }
        return $ret;
    }



     //$start_year=2011,$start_month=1;
    $init_duration=latestNMonths(12);
    //echo json_encode($init_duration);
    //echo json_encode($init_duration);
    //print_r($init_duration);
     $months_by_years=yearByMonths(2014,1); 
     //krsort($months_by_years);
     $filtering_info="Filters allow you to see aggregate data. So if you select Region: Central 1 and District: Gulu, you will see statistics for all facilties in Central 1 and Gulu. If you then select HCIII, it will filter the data to only show HCIII numbers for Central 1 and Gulu facilities.";
     ?>
     <span ng-model="month_labels" ng-init='month_labels={!! json_encode(MyHTML::months()) !!}'></span>
     <span ng-model="filtered" ng-init='filtered=false'></span>
     <span class="hdr hdr-grey" style="float:right;font-size:11px"><% data_date %></span><br>

    <div class="btn-group souces">
        <button type="button" class="btn btn-default active sources" ng-click="setSource('cphl')" id='sos_cphl'> 
            <span class='hdr hdr-dark'>CPHL</span>
        </button>
        <button type="button" class="btn btn-default sources" ng-click="setSource('poc')" id='sos_poc'> 
            <span class='hdr hdr-dark'>POC</span>
        </button>
        <button type="button" class="btn btn-default sources"  ng-click="setSource('all')" id='sos_all'>
            <span class='hdr hdr-dark'>ALL</span>
        </button>
    </div>

    <div class='row'>
        <div class='col-md-1' style="padding-top:17px; font-size:bolder">
            <span class='hdr hdr-grey'>FILTERS:</span> 
        </div>
        <div class="filter-section col-md-11">   

        

        <span style="font-size:15px;cursor:pointer;color:#000" onclick="alert('{!! $filtering_info !!}')" class='glyphicon glyphicon-info-sign' title="{!! $filtering_info !!}"></span>

        <span ng-model='filtered_date_range'>
          <span class="filter-val ng-cloak">
            <% filtered_date_range[0] |uganda_date_format %> -> <% filtered_date_range[1] |uganda_date_format %> 
        </span>

        <span ng-repeat="filtered_age_range_instance in filtered_age_range" ng-init="age_range_index = ageRangesCount()">
                <span class="filter-val ng-cloak"> <% filtered_age_range_instance.from_age %> 
                    - <% filtered_age_range_instance.to_age %>
                    (months) <x ng-click='filtered_age_range.splice($index, 1)'>&#120;</x>
                </span> 
            </span>
        </span>
        <span ng-model='filtered_age_range' ng-init='filtered_age_range=[]'>
            <span ng-repeat="filtered_age_range_instance in filtered_age_range" ng-init="age_range_index = ageRangesCount()">
                <span class="filter-val ng-cloak"> <% filtered_age_range_instance.from_age %> 
                    - <% filtered_age_range_instance.to_age %>
                    (months) <x ng-click='filtered_age_range.splice($index, 1)'>&#120;</x>
                </span> 
            </span>
        </span>
        <span ng-model='filter_gender' ng-init='filter_gender={}'>
            <span ng-repeat="(g_nr,g_name) in filter_gender">
                <span class="filter-val ng-cloak"> <% g_name %> (g) <x class='glyphicon glyphicon-remove' ng-click='removeTag("gender",g_nr)'></x></span> 
            </span>
        </span>
        <span ng-model='filter_regions' ng-init='filter_regions={}'>
            <span ng-repeat="(r_nr,r_name) in filter_regions">
                <span class="filter-val ng-cloak"> <% r_name %> (r) <x class='glyphicon glyphicon-remove' ng-click='removeTag("region",r_nr)'></x></span> 
            </span>
        </span>
        <span ng-model='filter_hubs' ng-init='filter_hubs={}'>
            <span ng-repeat="(hub_id,hub_name) in filter_hubs">
                <span class="filter-val ng-cloak"> <% hub_name %> (h) <x class='glyphicon glyphicon-remove' ng-click='removeTag("hub",hub_id)'></x></span> 
            </span>
        </span>
        <span ng-model='filter_districts' ng-init='filter_districts={}'>
            <span ng-repeat="(d_nr,d_name) in filter_districts"> 
                <span class="filter-val ng-cloak"> <% d_name %> (d) <x class='glyphicon glyphicon-remove' ng-click='removeTag("district",d_nr)'></x></span> 
            </span>
        </span>

        

        <span ng-model='filter_care_levels' ng-init='filter_care_levels={}'>
            <span ng-repeat="(cl_nr,cl_name) in filter_care_levels">
                <span class="filter-val ng-cloak"> <% cl_name %> (a) <x class='glyphicon glyphicon-remove' ng-click='removeTag("care_level",cl_nr)'></x></span> 
            </span>
        </span>

        
        <span ng-model='filter_pcrs' ng-init='filter_pcrs={}'>
            <span ng-repeat="(pcr_id,pcr_name) in filter_pcrs">
                <span class="filter-val ng-cloak"> <% pcr_name %> (p) <x class='glyphicon glyphicon-remove' ng-click='removeTag("pcr",pcr_id)'></x></span> 
            </span>
        </span>
        
        <span ng-model='filter_mother_prophylaxis' ng-init='filter_mother_prophylaxis={}'>
            <span ng-repeat="(mother_prophylaxis_id,mother_prophylaxis_name) in filter_mother_prophylaxis">
                <span class="filter-val ng-cloak"> <% mother_prophylaxis_name %> (mp) <x class='glyphicon glyphicon-remove' ng-click='removeTag("MotherProphylaxis",mother_prophylaxis_id)'></x></span> 
            </span>
        </span>

        <span ng-show="filtered" class="filter_clear" ng-click="clearAllFilters()">reset all</span>
        </div>
    </div>

    <table border='1' cellpadding='0' cellspacing='0' class='filter-tb'>
        <tr>
         <td width='9%' >
            <input type = "text" id = "datepicker_start" ng-model="selected_start_date"/>
         </td>
         <td width='9%' >
            <input type = "text" id = "datepicker_end" ng-model="selected_end_date" ng-change="dateRangeFilter('to')"/>
         </td>

            
            <!--td width='7%' >


                <span ng-model='fro_date_slct' ng-init='fro_date_slct={!! json_encode($months_by_years) !!}'></span>
                <select ng-model="fro_date" ng-init="fro_date='all'">
                    <option value='all'>FROM DATE</option>
                    <optgroup class="ng-cloak" ng-repeat="dt in fro_date_slct | orderBy:'-yr'" label="<% dt.yr %>">
                        <option class="ng-cloak" ng-repeat="mth in dt.mths" value="<% dt.yr %>-<% mth %>"> 
                            <% month_labels[mth] %> '<% dt.y %>
                        </option>
                    </optgroup>
                </select>
            </td>
            <td width='7%' >
                <span ng-model='to_date_slct' ng-init='to_date_slct={!! json_encode($months_by_years) !!}'></span>
                <select ng-model="to_date" ng-init="to_date='all'" ng-change="dateFilter('to')">
                    <option value='all'>TO DATE</option>
                    <optgroup class="ng-cloak" ng-repeat="dt in to_date_slct | orderBy:'-yr'" label="<% dt.yr %>">
                        <option class="ng-cloak" ng-repeat="mth in dt.mths" value="<% dt.yr %>-<% mth %>"> 
                            <% month_labels[mth] %> '<% dt.y %>
                        </option>
                    </optgroup>
                </select>
            </td-->
            <td width='9%'>
                <select ng-model="from_age" ng-init="from_age='all'">
                    <option value='all'>From Age</option>
                    <option class="ng-cloak" ng-repeat="fro_age in from_age_slct|orderBy:'name' " value="<% fro_age %>">
                        <% fro_age.name %>
                    </option>
                </select>

            </td>
            <td width='9%'>
                <select ng-model="to_age" ng-init="to_age='all'" ng-change="filter('age_range')">
                    <option value='all'>To Age</option>
                    <option class="ng-cloak" ng-repeat="to_age in to_age_slct|orderBy:'name' " value="<% to_age %>">
                        <% to_age.name %>
                    </option>
                </select>
            </td>
            <td width='9%'>
                <select ng-model="gender" ng-init="gender='all'" ng-change="filter('gender')">
                    <option value='all'>SEX</option>
                    <option class="ng-cloak" ng-repeat="gl in gender_slct | orderBy:'name'" value="<% gl.id %>">
                        <% gl.name %>
                    </option>
                </select>
            </td>
            <td width='9%'>
                <select ng-model="region" ng-init="region='all'" ng-change="filter('region')">
                    <option value='all'>REGIONS</option>
                    <option class="ng-cloak" ng-repeat="rg in regions_slct|orderBy:'name'" value="<% rg.id %>">
                        <% rg.name %>
                    </option>
                </select>
            </td>
            <td width='9%'>
                <select ng-model="hubs" ng-init="hubs='all'" ng-change="filter('hub')">
                    <option value='all'>HUBS</option>
                    <option class="ng-cloak" ng-repeat="hub_instance in hubs_slct | orderBy:'name'" value="<% hub_instance.id %>">
                        <% hub_instance.name %>
                    </option>
                </select>
            </td>
            <td width='9%'>
                <select ng-model="district" ng-init="district='all'" ng-change="filter('district')">
                    <option value='all'>DISTRICTS</option>
                    <option class="ng-cloak" ng-repeat="dist in districts_slct | orderBy:'name'" value="<% dist.id %>">
                        <% dist.name %>
                    </option>
                </select>
            </td>           
            <td width='9%'>
                <select ng-model="care_level" ng-init="care_level='all'" ng-change="filter('care_level')">
                    <option value='all'>CARE LEVELS</option>
                    <option class="ng-cloak" ng-repeat="cl in care_levels_slct | orderBy:'name'" value="<% cl.id %>">
                        <% cl.name %>
                    </option>
                </select>
            </td> 
            <!-- new filters-->
            
            <td width='9%'>
                <select ng-model="pcrs" ng-init="pcrs='all'" ng-change="filter('pcr')">
                    <option value='all'>PCR</option>
                    <option class="ng-cloak" ng-repeat="pcr_instance in pcrs_slct | orderBy:'name'" value="<% pcr_instance.id %>">
                        <% pcr_instance.name %>
                    </option>
                </select>
            </td>
             
             <td width='9%'>
                <select ng-model="mother_prophylaxes" ng-init="mother_prophylaxes='all'" ng-change="filter('mother_prophylaxis')">
                    <option value='all'>Mother Prophylaxis</option>
                    <option class="ng-cloak" ng-repeat="mother_prophylaxis_instance in mother_prophylaxis_slct | orderBy:'name'" value="<% mother_prophylaxis_instance.id %>">
                        <% mother_prophylaxis_instance.name %>
                    </option>
                </select>
            </td>
             
        </tr>
     </table>
      <span ng-model="loading" ng-init="loading=true"></span>
      <div ng-show="loading" style="text-align: center;padding:10px;"> <img src="{{ asset('/images/loading.gif') }}" height="20" width="20"> processing</div>
     <br>
     <label class='hdr hdr-grey'> KEY METRICS</label>
     <br>
     <div class="tabss tabs-style-flip">
        <nav>
            <ul>
                <li id='tb_hd1'>
                    <a href="#tab1" id='tb_lnk1' ng-click="displaySamplesRecieved()">
                        <span class="num ng-cloak" ng-model="samples_received" ng-init="samples_received=0">
                            <% samples_received|number %>
                        </span>
                        <span class="desc">total tests</span>
                    </a>
                </li>
                <li id='tb_hd2'>
                    <a href="#tab2" id='tb_lnk2'  ng-click="displayHIVPositiveInfants()">
                        <span class="num ng-cloak" ng-model="hiv_positive_infants" ng-init="hiv_positive_infants=0">
                            <% hiv_positive_infants|number %>
                        </span>
                        <span class="desc">hiv positive tests</span>
                    </a>
                </li>
                <li id='tb_hd3'>
                    <a href="#tab3" id='tb_lnk3' ng-click="displayPositivityRate()">
                        <span class="num ng-cloak">
                           <% ((hiv_positive_infants/samples_received)*100) |number:1 %>%
                        </span>
                        <span class="desc">positivity rate</span>
                    </a>
                </li>
                <!--li id='tb_hd4' ng-show="show_art_init">
                    <a href="#tab4" id='tb_lnk4' ng-click="displayInitiationRate()">
                        <span class="num ng-cloak" ng-model="initiated" ng-init="initiated=0">
                            <% ((initiated/hiv_positive_infants)*100)|number:1 %>% <sup>*</sup>
                        </span>
                        <span class="desc">initiation rate</span>
                    </a>
                </li -->
                <li id='tb_hd5' ng-show="show_results_printing">
                    <a href="#tab5" id='tb_lnk5'>
                        <span style="font-size:10px">Results Printing Status</span> 
                        <span class="desc">Results Printing Status</span>
                    </a>
                </li>
                <li id='tb_hd6' ng-show="show_poc_sites">
                    <a href="#tab6" id='tb_lnk6'>
                        <span style="font-size:10px">POC Sites Status</span> 
                        <span class="desc">POC Sites Status</span>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="content-wrap">
            <section id="tab1">
                <div class="row">
                    <div id="divchart1" class="col-lg-12">                        
                      <highchart id="highchart1" config="chartConfig" class="span10"></highchart>
                    </div>

                </div>
                <div class="row">
                    <div class="panel panel-info">
                      <div class="panel-heading collapsed" data-toggle="collapse" data-target="#tests_div">
                        <i class="fa fa-fw fa-chevron-down text-nowrap"> Shrink ...</i>
                        <i class="fa fa-fw fa-chevron-right text-nowrap"> Click here to view tabular data for districts and facilities ...</i>
                      </div>
                      <div class="panel-body">
                        
                        <div id="tests_div" class="col-lg-12 facilties-sect facilties-sect-list1 collapse" >
                        <span class='dist_faclty_toggle sect1' ng-model="show_fclties1" ng-init="show_fclties1=false" ng-click="showF(1)">
                            <span class='active' id='d_shw1'>&nbsp;&nbsp;DISTRICTS&nbsp;&nbsp;</span>
                            <span id='f_shw1'>&nbsp;&nbsp;FACILITIES &nbsp;&nbsp;</span>
                        </span>
                        <div ng-hide="show_fclties1">
                          <table datatable="ng" ng-hide="checked" class="row-border hover table table-bordered table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width='20%'>District</th>
                                    <th width='5%'>Total Tests</th>

                                    <th width='5%'>0 - 2 months Total Tests</th>
                                    <th width='5%'>0 - 2 months Total Tests(1<sup>st</sup> PCR)</th>
                                    
                                    <th width='5%'>0 - 2 months +ve Tests</th>

                                    
                                    <th width='5%'>Total 1<sup>st</sup> PCR</th>
                                    <th width='5%'>Positive 1<sup>st</sup> PCR </th>
                                    
                                    
                                    <th width='5%'>Total 2<sup>nd</sup> PCR</th>
                                    <th width='5%'>Positive 2<sup>nd</sup> PCR</th>

                                    <th width='5%'>Total 3<sup>rd</sup> PCR</th>
                                    <th width='5%'>Positive 3<sup>rd</sup> PCR</th>

                                    <th width='5%'>Total R1</th>
                                    <th width='5%'>Positive R1</th>

                                    <th width='4%'>Total R2</th>
                                    <th width='4%'>Positive R2</th>

                                    <th width='4%'>Total R3</th>
                                    <th width='4%'>Positive R3</th>
                                    

                                    <th width='4'>% of Positives in 1<sup>st</sup> PCR</th>
                                </tr>
                            </thead>
                            <tbody>                                
                                <tr ng-repeat="d in district_numbers" >
                                    <td class="ng-cloak"><% districts_lables[d._id].name %></td>
                                    <td class="ng-cloak"><% d.total_tests|number %></td>

                                    <td class="ng-cloak"><% district_numbers_zero_to_two_months[d._id].total_tests %></td>
                                    <td class="ng-cloak"><% district_numbers_zero_to_two_months_pcr1[d._id].total_tests %></td>

                                    <td class="ng-cloak"><% district_numbers_postives_zero_to_two_months[d._id].total_tests != null ? district_numbers_postives_zero_to_two_months[d._id].total_tests : 0 %></td>
                                    
                                    

                                    <td class="ng-cloak"><% d.pcr_one|number %></td>
                                    <td class="ng-cloak"><% district_numbers_positives[d._id].pcr_one_hiv_positive_infants != null ? district_numbers_positives[d._id].pcr_one_hiv_positive_infants : 0 %></td>
                                    
                                    <td class="ng-cloak"><% d.pcr_two|number %></td>
                                    <td class="ng-cloak"><% district_numbers_positives[d._id].pcr_two_hiv_positive_infants != null ? district_numbers_positives[d._id].pcr_two_hiv_positive_infants : 0 %></td>

                                    <td class="ng-cloak"><% d.pcr_three|number %></td>
                                    <td class="ng-cloak"><% district_numbers_positives[d._id].pcr_three_hiv_positive_infants != null ? district_numbers_positives[d._id].pcr_three_hiv_positive_infants : 0%></td>

                                    <td class="ng-cloak"><% d.pcr_R1|number %></td>
                                    <td class="ng-cloak"><% district_numbers_positives[d._id].pcr_hiv_positive_infants_R1 != null ? district_numbers_positives[d._id].pcr_hiv_positive_infants_R1 : 0%></td>

                                    <td class="ng-cloak"><% d.pcr_R2|number %></td>
                                    <td class="ng-cloak"><% district_numbers_positives[d._id].pcr_hiv_positive_infants_R2 != null ? district_numbers_positives[d._id].pcr_hiv_positive_infants_R2 : 0%></td>

                                    <td class="ng-cloak"><% d.pcr_R3|number %></td>
                                    <td class="ng-cloak"><% district_numbers_positives[d._id].pcr_hiv_positive_infants_R3 != null ? district_numbers_positives[d._id].pcr_hiv_positive_infants_R3 : 0 %></td>


                                    <td class="ng-cloak"><% (d.pcr_one_hiv_positive_infants > 0? ((d.pcr_one_hiv_positive_infants/d.pcr_one)*100):0) %>%</td>


                                </tr>                        
                             </tbody>
                           </table>

                        </div>
                        
                        <div ng-show="show_fclties1">
                          <table datatable="ng" ng-hide="checked" class="row-border hover table table-bordered table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width='15%'>Facility</th>

                                    <th width='10%'>DHIS2 Facility Name</th>
                                    <th width='6%'>District</th>

                                    <th width='5%'>Total Tests</th>

                                    <th width='5%'>0 - 2 months Total Tests</th>
                                    <th width='5%'>0 - 2 months Total Tests(1<sup>st</sup> PCR)</th>
                                    <th width='5%'>0 - 2 months +ve Tests</th>

                                    <th width='5%'>Total 1<sup>st</sup> PCR</th>
                                    <th width='5%'>Positive 1<sup>st</sup> PCR </th>
                                    
                                    
                                    <th width='4%'>Total 2<sup>nd</sup> PCR</th>
                                    <th width='4%'>Positive 2<sup>nd</sup> PCR</th>

                                    <th width='4%'>Total 3<sup>rd</sup> PCR</th>
                                    <th width='4%'>Positive 3<sup>rd</sup> PCR</th>

                                    <th width='4%'>Total R1</th>
                                    <th width='4%'>Positive R1</th>

                                    <th width='4%'>Total R2</th>
                                    <th width='4%'>Positive R2</th>

                                    <th width='4%'>Total R3</th>
                                    <th width='4%'>Positive R3</th>

                                    <th width='4%'>% of Positives in 1<sup>st</sup> PCR</th>
                                </tr>
                            </thead>
                            <tbody>                                
                                <tr ng-repeat="f in facility_numbers" >
                                    <td class="ng-cloak"><% facilities_lables[f._id].name %></td>
                                    
                                    <td class="ng-cloak"><% facilities_lables[f._id].dhis2_name %></td>
                                    <td class="ng-cloak"><% districts_lables[facilities_lables[f._id].district_id].name %></td>

                                    <td class="ng-cloak"><% f.total_tests|number %></td>

                                    <td class="ng-cloak"><% facility_numbers_zero_to_two_months[f._id].total_tests != null ? facility_numbers_zero_to_two_months[f._id].total_tests : 0 %></td>
                                    <td class="ng-cloak"><% facility_numbers_zero_to_two_months_pcr1[f._id].total_tests != null ? facility_numbers_zero_to_two_months_pcr1[f._id].total_tests : 0 %></td>

                                    <td class="ng-cloak"><% facility_numbers_positves_zero_to_two_months[f._id].total_tests != null ? facility_numbers_positves_zero_to_two_months[f._id].total_tests : 0 %></td>

                                    <td class="ng-cloak"><% f.pcr_one|number %></td>
                                    <td class="ng-cloak"><% facility_numbers_for_positives[f._id].pcr_one_hiv_positive_infants != null ? facility_numbers_for_positives[f._id].pcr_one_hiv_positive_infants : 0 %></td>

                                    <td class="ng-cloak"><% f.pcr_two|number %></td>
                                    <td class="ng-cloak"><% facility_numbers_for_positives[f._id].pcr_two_hiv_positive_infants != null ? facility_numbers_for_positives[f._id].pcr_two_hiv_positive_infants : 0 %></td>

                                    <td class="ng-cloak"><% f.pcr_three|number %></td>
                                    <td class="ng-cloak"><% facility_numbers_for_positives[f._id].pcr_three_hiv_positive_infants != null ? facility_numbers_for_positives[f._id].pcr_three_hiv_positive_infants : 0%></td>

                                    <td class="ng-cloak"><% f.pcr_R1|number %></td>
                                    <td class="ng-cloak"><% facility_numbers_for_positives[f._id].pcr_hiv_positive_infants_R1 != null ? facility_numbers_for_positives[f._id].pcr_hiv_positive_infants_R1 : 0 %></td>

                                    <td class="ng-cloak"><% f.pcr_R2|number %></td>
                                    <td class="ng-cloak"><% facility_numbers_for_positives[f._id].pcr_hiv_positive_infants_R2 != null ? facility_numbers_for_positives[f._id].pcr_hiv_positive_infants_R2 : 0%></td>

                                    <td class="ng-cloak"><% f.pcr_R3|number %></td>
                                    <td class="ng-cloak"><% facility_numbers_for_positives[f._id].pcr_hiv_positive_infants_R3 != null ? facility_numbers_for_positives[f._id].pcr_hiv_positive_infants_R3 : 0%></td>

                                    <td class="ng-cloak"><% (f.pcr_one_hiv_positive_infants > 0? (f.pcr_one_hiv_positive_infants/f.pcr_one)*100:0)|number:1 %>%</td>


                                </tr>                        
                             </tbody>
                         </table>

                        </div>
                        
                        <br>
                        <br>
                        <button ng-hide="show_fclties1" id="exportDistricts" type="button" ng-csv="export_district_numbers"  class="btn btn-success" filename="eid_district_samples_<%current_timestamp%>.csv" csv-header="['District', 'Total Tests','0 - 2 months Total Tests','0 - 2 months Total Tests(First PCR)','0 - 2 months +ve Tests', 'First PCR','Positves in 1st PCR','Second PCR','Positives in 2nd PCR','Third PCR','Positives in 3rd PCR','Total R1','Positives in R1','Total R2','Positives in R2','Total R3','Positives in R3','Positives in 1st PCR']">Download CSV</button>

                        <br>
                        <br>
                        <button ng-show="show_fclties1" id="exportFacilities" type="button" ng-csv="export_facility_numbers" filename="eid_facility_samples_<%current_timestamp%>.csv" class="btn btn-success" csv-header="['Facility','dhis2_uid','dhis2_name','district','Total Tests','0 - 2 months Total Tests','0 - 2 months Total Tests(First PCR)','0 - 2 months +ve Tests', 'First PCR','Positves in 1st PCR','Second PCR','Positives in 2nd PCR','Third PCR','Positives in 3rd PCR','Total R1','Positives in R1','Total R2','Positives in R2','Total R3','Positives in R3','Positives in 1st PCR']">Download CSV</button>

                    </div>
                      </div>
                    </div>
                </div>
            </section>

            <section id="tab2">
                <div class="row">
                    <div id="divcharthivpositiveinfants" class="col-lg-12">                        
                      <highchart id="highcharthivpositiveinfants" config="chartConfigHivPositiveInfants" class="span10"></highchart>
                    </div>
                   
                </div> 
                <div class="row">
                    <div class="panel panel-info">
                      <div class="panel-heading collapsed" data-toggle="collapse" data-target="#hiv_positive_infants_div">
                        <i class="fa fa-fw fa-chevron-down text-nowrap"> Shrink ...</i>
                        <i class="fa fa-fw fa-chevron-right text-nowrap"> Click here to view tabular data for districts and facilities ..</i>
                      </div>
                      <div class="panel-body">
                        
                      <div id="hiv_positive_infants_div" class="col-lg-6 facilties-sect facilties-sect-list2 collapse" >
                        
                        <span class='dist_faclty_toggle sect2' ng-model="show_fclties2" ng-init="show_fclties2=false" ng-click="showF(2)">
                            <span class='active' id='d_shw2'>&nbsp;&nbsp;DISTRICTS&nbsp;&nbsp;</span>
                            <span id='f_shw2'>&nbsp;&nbsp;FACILITIES &nbsp;&nbsp;</span>
                        </span>
                        <div ng-hide="show_fclties2">
                          <table datatable="ng" ng-hide="checked" class="row-border hover table table-bordered table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width='70%'>District</th>
                                    <th width='10%'>Absolute Positives</th>
                                    <th width='20%'>Total Tests</th>
                                </tr>
                            </thead>
                            <tbody>                                
                                <tr ng-repeat="d in district_numbers" >
                                    <td class="ng-cloak"><% districts_lables[d._id].name %></td>
                                    <td class="ng-cloak"><% d.hiv_positive_infants|number %></td>
                                    <td class="ng-cloak"><% d.total_tests|number %></td>
                                </tr>                        
                             </tbody>
                           </table>
                        </div>

                        <div ng-show="show_fclties2">
                         <table datatable="ng" ng-hide="checked" class="row-border hover table table-bordered table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width='30%'>Facility</th>

                                    <th width='30%'>DHIS2 Facility Name</th>
                                    <th width='20%'>District</th>

                                    <th width='10%'>Absolute Positives</th>
                                    <th width='10%'>Total Tests</th>
                                </tr>
                            </thead>
                            <tbody>                                
                                <tr ng-repeat="f in facility_numbers" >
                                    <td class="ng-cloak"><% facilities_lables[f._id].name %></td>
                                    
                                    <td class="ng-cloak"><% facilities_lables[f._id].dhis2_name %></td>
                                    <td class="ng-cloak"><% districts_lables[facilities_lables[f._id].district_id].name %></td>


                                    
                                    <td class="ng-cloak"><% f.hiv_positive_infants|number %></td>
                                    <td class="ng-cloak"><% f.total_tests|number %></td>
                                </tr>                        
                             </tbody>
                         </table>
                        </div>

                        <br>
                        <br>
                        <button ng-hide="show_fclties2" id="exportDistrictHivPositiveInfants" type="button" ng-csv="export_district_hiv_positive_infants"  class="btn btn-success" filename="eid_district_hiv_positives_<%current_timestamp%>.csv" csv-header="['District','Absolute Positives', 'Total Tests']">Download CSV</button>

                        <br>
                        <br>
                        <button ng-show="show_fclties2" id="exportFacilitiesHivPositiveInfants" type="button" ng-csv="export_facility_hiv_positive_infants" filename="eid_facility_hiv_positives_<%current_timestamp%>.csv" class="btn btn-success" csv-header="['Facility','dhis2_uid','dhis2_name','district','Absolute Positives','Total Tests']">Download CSV</button>

                    
                      </div>
                      </div>
                    </div>
                </div>

            </section>
            <section id="tab3">
                <div class="row">
                    <div id="divcharthivpositivityrate" class="col-lg-12">                        
                      <highchart id="highcharthivpositivityrate" config="chartConfigHivPositivityRate" class="span10"></highchart>
                    </div>
                   
                </div> 
                <div class="row">
                    <div class="panel panel-info">
                      <div class="panel-heading collapsed" data-toggle="collapse" data-target="#hiv_positivity_rate_div">
                        <i class="fa fa-fw fa-chevron-down text-nowrap"> Shrink ...</i>
                        <i class="fa fa-fw fa-chevron-right text-nowrap"> Click here to view tabular data for districts and facilities ..</i>
                      </div>
                      <div class="panel-body">
                        
                      <div id="hiv_positivity_rate_div" class="col-lg-6 facilties-sect facilties-sect-list3 collapse" >
                        
                        <span class='dist_faclty_toggle sect3' ng-model="show_fclties3" ng-init="show_fclties3=false" ng-click="showF(3)">
                            <span class='active' id='d_shw3'>&nbsp;&nbsp;DISTRICTS&nbsp;&nbsp;</span>
                            <span id='f_shw3'>&nbsp;&nbsp;FACILITIES &nbsp;&nbsp;</span>
                        </span>
                        <div ng-hide="show_fclties3">
                          <table datatable="ng" ng-hide="checked" class="row-border hover table table-bordered table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width='70%'>District</th>
                                    <th width='10%'>Positivity Rate</th>
                                    <th width='10%'>Absolute Positives</th>
                                    <th width='10%'>Total Tests</th>
                                </tr>
                            </thead>
                            <tbody>                                
                                <tr ng-repeat="d in district_numbers" >
                                    <td class="ng-cloak"><% districts_lables[d._id].name %></td>
                                    <td class="ng-cloak"><% ((d.hiv_positive_infants/d.total_tests)*100)|number:1 %>%</td>
                                    <td class="ng-cloak"><% d.hiv_positive_infants|number %></td>
                                    <td class="ng-cloak"><% d.total_tests|number %></td>
                                </tr>                        
                             </tbody>
                           </table>
                        </div>

                        <div ng-show="show_fclties3">
                            <br>
                          <table datatable="ng" ng-hide="checked" class="row-border hover table table-bordered table-condensed table-striped">
                            <thead>
                                <tr>
                                     <th width='30%'>CPHL Facility Name</th>
                                    <th width='20%'>DHIS2 Facility Name</th>
                                    <th width='20%'>District</th>

                                    <th width='10%'>Positivity Rate</th>
                                    <th width='10%'>Absolute Positives</th>
                                    <th width='10%'>Total Tests</th>
                                </tr>
                            </thead>
                            <tbody>                                
                                <tr ng-repeat="f in facility_numbers" >
                                    <td class="ng-cloak"><% facilities_lables[f._id].name %></td>
                                    
                                    <td class="ng-cloak"><% facilities_lables[f._id].dhis2_name %></td>
                                    <td class="ng-cloak"><% districts_lables[facilities_lables[f._id].district_id].name %></td>

                                    
                                    <td class="ng-cloak"><% ((f.hiv_positive_infants/f.total_tests)*100)|number:1 %>%</td>
                                    <td class="ng-cloak"><% f.hiv_positive_infants|number %></td>
                                    <td class="ng-cloak"><% f.total_tests|number %></td>
                                </tr>                        
                             </tbody>
                         </table>
                        </div>

                        <br>
                        <br>
                        <button ng-hide="show_fclties3" id="exportDistrictPositivityRate" type="button" ng-csv="export_district_positivity_rate"  class="btn btn-success" filename="eid_district_positivity_rate_<%current_timestamp%>.csv" csv-header="['District','Positivity Rate','Absolute Positives', 'Total Tests']">Download CSV</button>

                        <br>
                        <br>
                        <button ng-show="show_fclties3" id="exportFacilitiesPositivityRate" type="button" ng-csv="export_facility_positivity_rate" filename="eid_facility_positivity_rate_<%current_timestamp%>.csv" class="btn btn-success" csv-header="['Facility','dhis2_uid','dhis2_name','district','Positivity Rate','Absolute Positives','Total Tests']">Download CSV</button>

                    </div>   
                     
                      </div>
                    </div>

                </div>                
            </section>
            <!-- section id="tab4">
               
                <div class="row">
                    <div id="divchartinitiationrate" class="col-lg-12">                        
                      <highchart id="highchartinitiationrate" config="chartConfigInitiationRate" class="span10"></highchart>
                    </div>
                   
                </div>
                
                 <div class="row">
                    <div class="panel panel-info">
                      <div class="panel-heading collapsed" data-toggle="collapse" data-target="#initiation_rate_div">
                        <i class="fa fa-fw fa-chevron-down text-nowrap"> Shrink ...</i>
                        <i class="fa fa-fw fa-chevron-right text-nowrap"> Click here to view tabular data for districts and facilities ..</i>
                      </div>
                      <div class="panel-body">
                        
                      <div id="initiation_rate_div" class="col-lg-6 facilties-sect facilties-sect-list4 collapse">
                                         <span class='dist_faclty_toggle sect4' ng-model="show_fclties4" ng-init="show_fclties4=false" ng-click="showF(4)">
                            <span class='active' id='d_shw4'>&nbsp;&nbsp;DISTRICTS&nbsp;&nbsp;</span>
                            <span id='f_shw4'>&nbsp;&nbsp;FACILITIES &nbsp;&nbsp;</span>
                        </span>
                        <div ng-hide="show_fclties4">
                          <table datatable="ng" ng-hide="checked" class="row-border hover table table-bordered table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width='80%'>District</th>
                                    <th width='10%'>Initiation Rate</th>
                                    <th width='10%'>Absolute Positives</th> 
                                </tr>
                            </thead>
                            <tbody>                                
                                <tr ng-repeat="d in district_numbers" >
                                    <td class="ng-cloak"><% districts_lables[d._id] %></td>
                                    <td class="ng-cloak"><% ((d.art_initiated/d.hiv_positive_infants)*100)|number:1 %>%</td>
                                    <td class="ng-cloak"><% d.hiv_positive_infants %></td>   
                                </tr>                        
                             </tbody>
                           </table>
                        </div>
                        <div ng-show="show_fclties4">
                           <table datatable="ng" ng-hide="checked" class="row-border hover table table-bordered table-condensed table-striped">
                             <thead>
                                <tr>
                                    <th width='80%'>Facility</th>
                                    <th width='10%'>Initiation Rate</th>
                                    <th width='10%'>Absolute Positives</th>                                   
                                </tr>
                            </thead>
                            <tbody>                                
                                <tr ng-repeat="f in facility_numbers" >
                                    <td class="ng-cloak"><% facilities_lables[f._id] %></td>
                                    <td class="ng-cloak"><% ((f.art_initiated/f.hiv_positive_infants)*100)|number:1 %>%</td>
                                    <td class="ng-cloak"><% f.hiv_positive_infants %></td>                    
                                </tr>                        
                             </tbody>
                         </table>
                        </div>

                        <br>
                        <br>
                        <button ng-hide="show_fclties4" id="exportDistrictInitiationRate" type="button" ng-csv="export_district_initiation_rate"  class="btn btn-success" filename="eid_district_initiation_rate_<%current_timestamp%>.csv" csv-header="['District','Initiation Rate','Absolute Positives']">Download CSV</button>

                        <br>
                        <br>
                        <button ng-show="show_fclties4" id="exportFacilitiesInitiationRate" type="button" ng-csv="export_facility_initiation_rate" filename="eid_facility_initiation_rate_<%current_timestamp%>.csv" class="btn btn-success" csv-header="['Facility','Initiation Rate','Absolute Positives']">Download CSV</button>

                      </div>   
                     
                      </div>
                    </div>
                </div> 
                <i style="font-size:12px;color:#9F82D1">* ART Initiation Rate is a preliminary estimate based on data collected at CPHL. CPHL is still revising the data collection mechanism</i>               
            </section -->
            <section id="tab5"> @include('sections._results_printing_statistics')</section>
            <section id="tab6"> @include('sections._poc_sites_statistics')</section>
        </div><!-- /content -->
    </div><!-- /tabs -->
    <br>
     <label class='hdr hdr-grey'> SUMMARY OF KEY INDICATORS </label>
         @include('sections._eid_indicators')
    <br>
     <label class='hdr hdr-grey'> ADDITIONAL METRICS</label>
    <div class='addition-metrics'>
       <div class='row'>
        
        
        <div class='col-sm-1'>
            <font class='addition-metrics figure ng-cloak' ng-model='pcr_one' ng-init="pcr_one=0"><% pcr_one|number %></font><br>
            <font class='addition-metrics desc'>TOTAL 1ST PCR</font>            
        </div>
        <div class='col-sm-1'></div>  
        <div class='col-sm-1'>
            <font class='addition-metrics figure ng-cloak' ng-model='pcr_two' ng-init="pcr_two=0"><% pcr_two|number %></font><br>
            <font class='addition-metrics desc'>TOTAL 2ND PCR</font>            
        </div>
        <div class='col-sm-1'></div>  
        <div class='col-sm-1'>
            <font class='addition-metrics figure ng-cloak' ng-model='pcr_three' ng-init="pcr_three=0"><% pcr_three|number %></font><br>
            <font class='addition-metrics desc'>TOTAL 3RD PCR</font>            
        </div>
        <div class='col-sm-1'></div>  
        <div class='col-sm-1'>
            <font class='addition-metrics figure ng-cloak' ng-model='repeat_one' ng-init="repeat_one=0"><% repeat_one|number %></font><br>
            <font class='addition-metrics desc'>TOTAL R1</font>            
        </div>
        <div class='col-sm-1'></div>       
        <div class='col-sm-1'>
            <font class='addition-metrics figure ng-cloak' ng-model='repeat_two' ng-init="repeat_two=0"><% repeat_two|number %></font><br>
            <font class='addition-metrics desc'>TOTAL R2</font>            
        </div>
        <div class='col-sm-1'>
            <font class='addition-metrics figure ng-cloak' ng-model='repeat_three' ng-init="repeat_three=0"><% repeat_three|number %></font><br>
            <font class='addition-metrics desc'>TOTAL R3</font>            
        </div>
        <div class='col-sm-2' ng-show="show_art_init">
            <font class='addition-metrics figure ng-cloak' ng-model="initiated" ng-init="initiated=0">
                <% initiated|number %>
            </font><br>
            <font class='addition-metrics desc'>TOTAL ART INITIATED CHILDREN</font>            
        </div>
       
    </div>
    <br>
</div>
<br>
<footer>
    This platform and its content is copyright of CPHL Ministry of Health ,Uganda - © CPHL Ministry of Health ,Uganda 2019. All rights reserved.
    <br>
    <small>Any redistribution or reproduction of part or all of the contents in any form is prohibited other than the following:</small>
        <ul>
            <li><small>You may print or download to a local hard disk extracts for your personal and non-commercial use only</small></li>
            <li><small>You may copy the content to individual third parties for their personal use, but only if you acknowledge the website as the source of the material</small></li>
            <li><small>You may not, except with our express written permission, distribute or commercially exploit the content. Nor may you transmit it or store it in any other website or other form of electronic retrieval system.</small></li>
        </ul>
    
</footer>


<script src="{{ asset('js/angular-sanitize-1.2.15.min.js') }}"></script>
<script src="{{ asset('js/ng-csv.js') }}"></script>
<script src="{{ asset('js/ng-csv.min.js') }}"></script>
<script src=" {{ asset('js/cbpFWTabs.js') }} "></script>
<script>
(function() {
    [].slice.call( document.querySelectorAll( '.tabss' ) ).forEach( function( el ) {
        new CBPFWTabs( el );
    });

})();
</script>


</body>

<script type="text/javascript" src=" {{ asset('js/edash.js') }} "></script>
</html>
