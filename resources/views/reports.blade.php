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

    <link href="{{ asset('/css/dash.css') }}" rel="stylesheet">

    <script src="{{ asset('/js/modernizr.custom.js') }}"></script>

    
    <script src="{{ asset('/js/general.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/js/jquery-2.1.3.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/js/jquery-ui.min.js')}}" type="text/javascript"></script>
    <script src="{{ asset('/twitter-bootstrap-3.3/js/bootstrap.min.js') }}" type="text/javascript" ></script>

    <script src="{{ asset('/js/angular.min.js')}}" type="text/javascript"></script>
    <script src="{{ asset('/js/angular-route.js')}}" type="text/javascript"></script>

    <script src="{{ asset('/js/angular-datatables.min.js') }}" type="text/javascript"></script>


   
    <script src="{{ asset('/js/d3.min.js') }}" charset="utf-8"></script>
    <script src="{{ asset('/js/nv.d3.min.js') }}"></script>
    <script src="{{ asset('/js/stream_layers.js') }}"></script>

    <style type="text/css">
    .nv-point {
        stroke-opacity: 1!important;
        stroke-width: 5px!important;
        fill-opacity: 1!important;
    }
    </style>

    
</head>

<body ng-app="dashboard" ng-controller="DashController">

<div class="navbar-custom navbar navbar-inverse navbar-fixed-top" role="navigation">
    <img src="{{ asset('/images/uganda_flag2.png') }}" style="width:100%;height:10px;margin:0px">
    <div class="container">

        <div class="navbar-header"> 
            <a class="navbar-brand" href="/" style="font-weight:800px;color:#FFF"> UGANDA EID</a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li id='l1'>{!! link_to("/","DASHBOARD",['class'=>'hdr']) !!}</li>  
                <li id='l2' class='active'>{!! link_to("/reports","REPORTS",['class'=>'hdr']) !!}</li>            
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><span style="font-size: 30px;vertical-align: middle;margin-right:25px;"> <img src="{{ asset('/images/ug.png') }}" height="35" width="35"> </span></li>
            </ul>
        </div>

    </div>
</div> 

<div class='container'>
    <br>
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

     <div class='row'>
        <div class='col-md-1' style="padding-top:17px; font-size:bolder">
            <span class='hdr hdr-grey'>FILTERS:</span> 
        </div>
        <div class="filter-section col-md-11">   

        <span ng-model='filter_duration' ng-init='filter_duration={!! json_encode($init_duration) !!};init_duration={!! json_encode($init_duration) !!};'>
          <span class="filter-val ng-cloak">
            <% filter_duration[0] |d_format %> - <% filter_duration[filter_duration.length-1] | d_format %> 
        </span>
        </span>
        &nbsp;

        <span style="font-size:15px;cursor:pointer;color:#000" onclick="alert('{!! $filtering_info !!}')" class='glyphicon glyphicon-info-sign' title="{!! $filtering_info !!}"></span>


        <span ng-model='filter_regions' ng-init='filter_regions={}'>
            <span ng-repeat="(r_nr,r_name) in filter_regions">
                <span class="filter-val ng-cloak"> <% r_name %> (r) <x class='glyphicon glyphicon-remove' ng-click='removeTag("region",r_nr)'></x></span> 
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

        <span ng-show="filtered" class="filter_clear" ng-click="clearAllFilters()">reset all</span>
        </div>
     </div>

     <table border='1' cellpadding='0' cellspacing='0' class='filter-tb'>
        <tr>
            <td width='20%' >
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
            <td width='20%' >
                <span ng-model='to_date_slct' ng-init='to_date_slct={!! json_encode($months_by_years) !!}'></span>
                <select ng-model="to_date" ng-init="to_date='all'" ng-change="dateFilter('to')">
                    <option value='all'>TO DATE</option>
                    <optgroup class="ng-cloak" ng-repeat="dt in to_date_slct | orderBy:'-yr'" label="<% dt.yr %>">
                        <option class="ng-cloak" ng-repeat="mth in dt.mths" value="<% dt.yr %>-<% mth %>"> 
                            <% month_labels[mth] %> '<% dt.y %>
                        </option>
                    </optgroup>
                </select>
            </td>
             <td width='20%'>
                <select ng-model="region" ng-init="region='all'" ng-change="filter('region')">
                    <option value='all'>REGIONS</option>
                    <option class="ng-cloak" ng-repeat="rg in regions_slct|orderBy:'name'" value="<% rg.id %>">
                        <% rg.name %>
                    </option>
                </select>
            </td>
            <td width='20%'>
                <select ng-model="district" ng-init="district='all'" ng-change="filter('district')">
                    <option value='all'>DISTRICTS</option>
                    <option class="ng-cloak" ng-repeat="dist in districts_slct | orderBy:'name'" value="<% dist.id %>">
                        <% dist.name %>
                    </option>
                </select>
            </td>           
            <td width='20%'>
                <select ng-model="care_level" ng-init="care_level='all'" ng-change="filter('care_level')">
                    <option value='all'>CARE LEVELS</option>
                    <option class="ng-cloak" ng-repeat="cl in care_levels_slct | orderBy:'name'" value="<% cl.id %>">
                        <% cl.name %>
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
                        <span class="desc">hiv positive infants</span>
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
                <li id='tb_hd4'>
                    <a href="#tab4" id='tb_lnk4' ng-click="displayInitiationRate()">
                        <span class="num ng-cloak" ng-model="initiated" ng-init="initiated=0">
                            <% ((initiated/hiv_positive_infants)*100)|number:1 %>% <sup>*</sup>
                        </span>
                        <span class="desc">initiation rate</span>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="content-wrap">
            <section id="tab1">
                <div class="row">
                                     
                    <div class="facilties-sect facilties-sect-list1" >
                        <br>
                        <span class='dist_faclty_toggle sect1' ng-model="show_fclties1" ng-init="show_fclties1=false" ng-click="showF(1)">
                            <span class='active' id='d_shw1'>&nbsp;&nbsp;DISTRICTS&nbsp;&nbsp;</span>
                            <span id='f_shw1'>&nbsp;&nbsp;FACILITIES &nbsp;&nbsp;</span>
                        </span>
                        <div ng-hide="show_fclties1">
                          <table datatable="ng" ng-hide="checked" class="row-border hover table table-bordered table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th>District</th>
                                    <th ng-repeat="m in filter_duration"><% m|d_format %></th>
                                </tr>
                            </thead>
                            <tbody>                                
                                <tr ng-repeat="d in district_numbers" >
                                    <td class="ng-cloak"><% d.name %></td>
                                    <td ng-repeat="m in filter_duration"><% d.month_data[m].samples_received|number %></td>
                                </tr>                        
                             </tbody>
                           </table>
                        </div>

                        <div ng-show="show_fclties1">
                          <table datatable="ng" ng-hide="checked" class="row-border hover table table-bordered table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th>Facility</th>
                                    <th ng-repeat="m in filter_duration"><% m|d_format %></th>
                                </tr>
                            </thead>
                            <tbody>                                
                                <tr ng-repeat="f in facility_numbers" >
                                    <td class="ng-cloak"><% f.name %></td>
                                    <td ng-repeat="m in filter_duration"><% f.month_data[m].samples_received|number %></td>
                                </tr>                        
                             </tbody>
                         </table>
                        </div>
                    </div>
                </div>
            </section>

            <section id="tab2">
                <div class="row">
                   
                    <div class=" facilties-sect facilties-sect-list2" >
                        <br>
                        <span class='dist_faclty_toggle sect2' ng-model="show_fclties2" ng-init="show_fclties2=false" ng-click="showF(2)">
                            <span class='active' id='d_shw2'>&nbsp;&nbsp;DISTRICTS&nbsp;&nbsp;</span>
                            <span id='f_shw2'>&nbsp;&nbsp;FACILITIES &nbsp;&nbsp;</span>
                        </span>
                        <div ng-hide="show_fclties2">
                          <table datatable="ng" ng-hide="checked" class="row-border hover table table-bordered table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th>District</th>
                                    <th ng-repeat="m in filter_duration"><% m|d_format %></th>
                                </tr>
                            </thead>
                            <tbody>                                
                                <tr ng-repeat="d in district_numbers" >
                                    <td class="ng-cloak"><% d.name %></td>
                                    <td class="ng-cloak" ng-repeat="m in filter_duration"><% d.month_data[m].hiv_positive_infants|number %></td>
                                </tr>                        
                             </tbody>
                           </table>
                        </div>

                        <div ng-show="show_fclties2">
                         <table datatable="ng" ng-hide="checked" class="row-border hover table table-bordered table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th>Facility</th>
                                    <th ng-repeat="m in filter_duration"><% m|d_format %></th>                                   
                                </tr>
                            </thead>
                            <tbody>                                
                                <tr ng-repeat="f in facility_numbers" >
                                    <td class="ng-cloak"><% f.name %></td>
                                    <td ng-repeat="m in filter_duration"><% f.month_data[m].hiv_positive_infants|number %></td>
                                </tr>                        
                             </tbody>
                         </table>
                        </div>
                    </div>
                </div> 
            </section>
            <section id="tab3">
                <div class="row">
                                      
                    <div class="facilties-sect facilties-sect-list3" >
                        <br>
                        <span class='dist_faclty_toggle sect3' ng-model="show_fclties3" ng-init="show_fclties3=false" ng-click="showF(3)">
                            <span class='active' id='d_shw3'>&nbsp;&nbsp;DISTRICTS&nbsp;&nbsp;</span>
                            <span id='f_shw3'>&nbsp;&nbsp;FACILITIES &nbsp;&nbsp;</span>
                        </span>
                        <div ng-hide="show_fclties3">
                          <table datatable="ng" ng-hide="checked" class="row-border hover table table-bordered table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th >District</th>
                                    <th ng-repeat="m in filter_duration"><% m|d_format %></th>
                                </tr>
                            </thead>
                            <tbody>                                
                                <tr ng-repeat="d in district_numbers" >
                                    <td class="ng-cloak"><% d.name %></td>
                                    <td class="ng-cloak" ng-repeat="m in filter_duration"><% ((d.month_data[m].hiv_positive_infants/d.month_data[m].samples_received)*100)||0 |number:1 %>%</td>
                                </tr>                        
                             </tbody>
                           </table>
                        </div>

                        <div ng-show="show_fclties3">
                          <table datatable="ng" ng-hide="checked" class="row-border hover table table-bordered table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th>Facility</th>
                                    <th ng-repeat="m in filter_duration"><% m|d_format %></th>
                                </tr>
                            </thead>
                            <tbody>                                
                                <tr ng-repeat="f in facility_numbers" >
                                    <td class="ng-cloak"><% f.name %></td>
                                    <td class="ng-cloak" ng-repeat="m in filter_duration"><% ((f.month_data[m].hiv_positive_infants/f.month_data[m].samples_received)*100)|number:1 %>%</td>
                                </tr>                        
                             </tbody>
                         </table>
                        </div>
                    </div>
                </div>                
            </section>
            <section id="tab4">
                <div class="row">
                                    
                    <div class="facilties-sect facilties-sect-list4" >
                        <br>
                        <span class='dist_faclty_toggle sect4' ng-model="show_fclties4" ng-init="show_fclties4=false" ng-click="showF(4)">
                            <span class='active' id='d_shw4'>&nbsp;&nbsp;DISTRICTS&nbsp;&nbsp;</span>
                            <span id='f_shw4'>&nbsp;&nbsp;FACILITIES &nbsp;&nbsp;</span>
                        </span>
                        <div ng-hide="show_fclties4">
                          <table datatable="ng" ng-hide="checked" class="row-border hover table table-bordered table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th>District</th>
                                    <th ng-repeat="m in filter_duration"><% m|d_format %></th>
                                </tr>
                            </thead>
                            <tbody>                                
                                <tr ng-repeat="d in district_numbers" >
                                    <td class="ng-cloak"><% d.name %></td>
                                    <td class="ng-cloak" ng-repeat="m in filter_duration"><% ((d.month_data[m].initiated/d.month_data[m].hiv_positive_infants)*100)|number:1 %>%</td> 
                                </tr>                        
                             </tbody>
                           </table>
                        </div>
                        <div ng-show="show_fclties4">
                           <table datatable="ng" ng-hide="checked" class="row-border hover table table-bordered table-condensed table-striped">
                             <thead>
                                <tr>
                                    <th>Facility</th>
                                    <th ng-repeat="m in filter_duration"><% m|d_format %></th>                                  
                                </tr>
                            </thead>
                            <tbody>                                
                                <tr ng-repeat="f in facility_numbers" >
                                    <td class="ng-cloak"><% f.name %></td>
                                    <td class="ng-cloak" ng-repeat="m in filter_duration"><% ((f.month_data[m].initiated/f.month_data[m].hiv_positive_infants)*100)|number:1 %>%</td>
                                                       
                                </tr>                        
                             </tbody>
                         </table>
                        </div>
                    </div>
                </div> 
                <i style="font-size:12px;color:#9F82D1">* ART Initiation Rate is a preliminary estimate based on data collected at CPHL. CPHL is still revising the data collection mechanism</i>               
            </section>
        </div><!-- /content -->
    </div><!-- /tabs -->
    
    <br>
     <label class='hdr hdr-grey'> ADDITIONAL METRICS</label>
    <div class='addition-metrics'>
       <div class='row'>
        <div class='col-sm-1'></div>
        <div class='col-sm-2'>
            <font class='addition-metrics figure ng-cloak' ng-model='pcr_one' ng-init="pcr_one=0"><% pcr_one|number %></font><br>
            <font class='addition-metrics desc'>TOTAL 1ST PCR</font>            
        </div>
        <div class='col-sm-2'>
            <font class='addition-metrics figure ng-cloak' ng-model='pcr_two' ng-init="pcr_two=0"><% pcr_two|number %></font><br>
            <font class='addition-metrics desc'>TOTAL 2ND PCR</font>            
        </div>       
        <div class='col-sm-2'>
            <font class='addition-metrics figure ng-cloak' ng-model="first_pcr_median_age" ng-init="first_pcr_median_age=0">
                <% first_pcr_median_age|number:1 %>
            </font><br>
            <font class='addition-metrics desc'>MEDIAN MONTHS 1ST PCR</font>            
        </div>
        <div class='col-sm-2'>
            <font class='addition-metrics figure ng-cloak' ng-model="sec_pcr_median_age" ng-init="sec_pcr_median_age=0">
                <% sec_pcr_median_age|number:1 %>
            </font><br>
            <font class='addition-metrics desc'>MEDIAN MONTHS 2ND PCR</font>            
        </div>
        <div class='col-sm-2'>
            <font class='addition-metrics figure ng-cloak' ng-model="initiated" ng-init="initiated=0">
                <% initiated|number %>
            </font><br>
            <font class='addition-metrics desc'>TOTAL ART INITIATED CHILDREN</font>            
        </div>
        <div class='col-sm-1'></div>
       </div>
    </div>
    <br>
</div>
<script src=" {{ asset('js/cbpFWTabs.js') }} "></script>
<script>
(function() {
    [].slice.call( document.querySelectorAll( '.tabss' ) ).forEach( function( el ) {
        new CBPFWTabs( el );
    });
})();
</script>


</body>

<script type="text/javascript" src=" {{ asset('js/edash2.js') }} "></script>
</html>
