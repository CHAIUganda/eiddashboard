//angular stuff

/*
Authors
Name                        @       Period      Role       
Logan Smith                 CHAI    2015(v1)    Interface Design, Q/A
Ina Foalea                  CHAI    2015(v1)    Req Specification, Q/A, UAT
Kitutu Paul                 CHAI    2015(v1)    System development

Credit to CHAI Uganda, CPHL and stakholders
*/
var app=angular.module('dashboard', ['datatables'], function($interpolateProvider) {
        $interpolateProvider.startSymbol('<%');
        $interpolateProvider.endSymbol('%>');
    });

app.filter('ssplit', function() {
        return function(input, splitChar,splitIndex) {
            // do some bounds checking here to ensure it has that index
            var arr=input.split(splitChar);
            return arr[splitIndex];
        }
    });

app.filter('slice', function() {
        return function(input, length) {
            return input.slice(length);
        }
    });

app.filter('d_format', function() {
        return function(y_m) {
            var month_labels={1:'Jan',2:'Feb',3:'Mar',4:'Apr',5:'May',6:'Jun',7:'Jul',8:'Aug',9:'Sept',10:'Oct',11:'Nov',12:'Dec'};
            var arr=y_m.split('-');
            var yr=arr[0]||"";
            var mth=arr[1]||"";
            return month_labels[mth]+" '"+yr.slice(-2);
        }
    });




var ctrllers={};

ctrllers.DashController=function($scope,$http){
    var regions_json={};
    var dists_by_region={};
    var districts_json={};    
    var care_levels_json={};   
    var facilities_json={};   
    var results_json=[]; //to hold a big map will all processed data to later on be used in the generalFilter
    var loaded_years=[];

    var nat_sr_by_duration={};
    var nat_hpi_by_duration={};
    var nat_i_by_duration={};

    var data_init={};

    $http.get("../json/care_levels.json").success(function(data){
        care_levels_json=data||{};       
        $scope.care_levels_slct=pairize(care_levels_json);
    });

    $http.get("../json/districts.json").success(function(data){
        districts_json=data||{};     
        $scope.districts_slct=pairize(districts_json);
    });

    $http.get("../json/districts_by_region.json").success(function(data){
        dists_by_region=data||{};
    });

    $http.get("../json/facilities.json").success(function(data){
        facilities_json=data||{};
    });

    $http.get("../json/regions.json").success(function(data){
        regions_json=data||{};
        $scope.regions_slct=pairize(regions_json);
        initializeSys();        
    });

    var initializeSys=function(){
        var d=new Date();
        var y=d.getFullYear();
        getYearData(y);
    }

    //

    var getYearData=function(year){
        $http.get("../json/data.json").success(function(data) {
            var res=data.results||{};
            $scope.data_date=data.data_date||"bbbb";
            //var res=data||{};
            for(var i in res){
                //console.log("mapping");
                var that=res[i];
                var facility_details=facilities_json[that.facility_id]||{};
                this_obj={};
                this_obj.year_month=that.year+"-"+that.month;
                this_obj.facility_id=that.facility_id;
                this_obj.facility_name=facility_details.name||"";
                this_obj.region_id=facility_details.region_id;
                this_obj.district_id=facility_details.district_id;
                this_obj.district_name=districts_json[this_obj.district_id];
                this_obj.care_level_id=facility_details.care_level_id;

                this_obj.samples_received=Number(that.samples_received)||0;
                this_obj.hiv_positive_infants=Number(that.hiv_positive_infants)||0;
                this_obj.initiated=Number(that.initiated)||0;
                this_obj.pcr_one=Number(that.pcr_one)||0;
                this_obj.pcr_two=Number(that.pcr_two)||0;
                this_obj.pcr_one_ages=that.pcr_one_ages;
                this_obj.pcr_two_ages=that.pcr_two_ages; 
                results_json.push(this_obj);
            }

            //loaded_years.push(year);
            //if(!$scope.date_filtered){ generalFilter(); }//call the filter for the first time
            generalFilter(1);
        });
    }

    $scope.dateFilter=function(mode){
        if($scope.fro_date!="all" && $scope.to_date!="all"){
            var vals={};var fro_s=$scope.fro_date.split("-");var to_s=$scope.to_date.split("-");
            vals.from_year=Number(fro_s[0]);
            vals.from_month=Number(fro_s[1]);
            vals.to_year=Number(to_s[0]);
            vals.to_month=Number(to_s[1]);

            var eval1=vals.from_year<=vals.to_year;
            var eval2=(vals.from_month>vals.to_month)&&(vals.from_year<vals.to_year);
            var eval3=(vals.from_month<=vals.to_month);

            if(eval1 && (eval2||eval3)){
                //console.log("duration expression passed");
                $scope.date_filtered=true;
                computeDuration(vals);                
                $scope.filter("duration");                
            }else{
                alert("Please make sure that the fro date is earlier than the to date");
                //console.log("duration expression failing eval1="+eval1+" eval2"+eval2+" eval3"+eval3);
                //console.log("fro yr="+vals.from_year+" fro m"+vals.from_month+" to yr="+vals.to_year+" to m"+vals.to_month);
            }
        }
    }

    var computeDuration=function(vals){
        $scope.filter_duration=[];
        var i=vals.from_year;
        while(i<=vals.to_year){                       
            var stat=(i==vals.from_year)?vals.from_month:1;
            var end=(i==vals.to_year)?vals.to_month:12;
            var j=stat;
            while(j<=end){
                $scope.filter_duration.push(i+"-"+j);
                j++;   
            }   
            i++;  
            //if(!inArray(i,loaded_years)){ getYearData(i);  }
        }
    }

    var reduceDistsByReg=function(){
        var regs=count($scope.filter_regions);
        if(regs==0){
            $scope.districts_slct=pairize(districts_json);
        }else{
            var nu_dists=[];
            for(var i in $scope.filter_regions){
                var dsts=pairize(dists_by_region[i]);
                nu_dists=nu_dists.concat(dsts);
            }
            $scope.districts_slct=nu_dists;
        }
    }

    $scope.filter=function(mode){
        switch(mode){
            case "region":
            $scope.filter_regions[$scope.region]=regions_json[$scope.region];
            $scope.region='all';
            break;
            case "district":
            $scope.filter_districts[$scope.district]=districts_json[$scope.district];
            $scope.district='all';            
            break;
            case "care_level":
            $scope.filter_care_levels[$scope.care_level]=care_levels_json[$scope.care_level];
            $scope.care_level='all';
            break;
        }

        delete $scope.filter_regions["all"];
        delete $scope.filter_districts["all"];        
        delete $scope.filter_care_levels["all"];

        generalFilter(0); //filter the results for each required event
    }



    var evaluator=function(that){  
        var r_num=count($scope.filter_regions);
        var d_num=count($scope.filter_districts);
        var c_num=count($scope.filter_care_levels);

        var time_eval=inArray(that.year_month,$scope.filter_duration);
        var reg_eval=$scope.filter_regions.hasOwnProperty(that.region_id);
        var dist_eval=$scope.filter_districts.hasOwnProperty(that.district_id);        
        var cl_eval=$scope.filter_care_levels.hasOwnProperty(that.care_level_id);

        var eval1=r_num==0&&d_num==0&&c_num==0;     // regions(OFF) and districts(OFF) and care_levels (OFF)
        var eval2=reg_eval&&d_num==0&&c_num==0;     // regions(ON)  and districts(OFF) and care_levels (OFF)
        var eval3=(reg_eval||dist_eval)&&c_num==0;    // regions(ON)  or  districts(ON)  and care_levels (OFF)
        var eval4=reg_eval&&d_num==0&&cl_eval;      // regions(ON)  and districts(OFF) and care_levels (ON)
        var eval5=(reg_eval||dist_eval)&&cl_eval;     // regions(ON)  or  districts(ON)  and care_levels (ON)
        var eval6=r_num==0&&dist_eval&&cl_eval;     // regions(OFF) and districts(ON)  and care_levels (ON)
        var eval7=r_num==0&&dist_eval&&c_num==0;    // regions(OFF) and districts(ON)  and care_levels (OFF)
        var eval8=r_num==0&&d_num==0&&cl_eval;      // regions(OFF) and districts(OFF) and care_levels (ON)

        if( time_eval && (eval1||eval2||eval3||eval4||eval5||eval6||eval7||eval8)){
            return true;
        }else{
            return false;
        }
    }

    var setKeyIndicators=function(that){
        $scope.samples_received+=that.samples_received;
        $scope.hiv_positive_infants+=that.hiv_positive_infants;
        $scope.initiated+=that.initiated;
    }

   /* var reduceDistsByReg=function(){ clinic - hc2, hc, health care center, lab,medical center arrange accordingin to hierac
    AIC - hc2, 
        var regs=count($scope.filter_regions);
        if(regs==0){
            $scope.districts_slct=pairize(districts_json);
        }else{
            var nu_dists=[];
            for(var i in $scope.filter_regions){
                var dsts=pairize(dists_by_region[i]);
                nu_dists=nu_dists.concat(dsts);
            }
            $scope.districts_slct=nu_dists;
        }
    }*/

    var setOtherIndicators=function(that){
        $scope.pcr_one+=that.pcr_one;
        $scope.pcr_two+=that.pcr_two;
        //console.log("one :"+JSON.stringify(that.pcr_one_ages))

        $scope.pcr_one_ages=$scope.pcr_one_ages.concat(that.pcr_one_ages)||[];
        $scope.pcr_two_ages=$scope.pcr_two_ages.concat(that.pcr_two_ages)||[];
    }

    var setDataByDuration=function(that){
        var prev_sr=$scope.sr_by_duration[that.year_month]||0;
        var prev_hpi=$scope.hpi_by_duration[that.year_month]||0;
        var prev_i=$scope.i_by_duration[that.year_month]||0;

        $scope.sr_by_duration[that.year_month]=prev_sr+that.samples_received;
        $scope.hpi_by_duration[that.year_month]=prev_hpi+that.hiv_positive_infants;
        $scope.i_by_duration[that.year_month]=prev_i+that.initiated;
    }

    var setNationalDataByDuration=function(that){
        var prev_sr=nat_sr_by_duration[that.year_month]||0;
        var prev_hpi=nat_hpi_by_duration[that.year_month]||0;
        var prev_i=nat_i_by_duration[that.year_month]||0;

        nat_sr_by_duration[that.year_month]=prev_sr+that.samples_received;
        nat_hpi_by_duration[that.year_month]=prev_hpi+that.hiv_positive_infants;
        nat_i_by_duration[that.year_month]=prev_i+that.initiated;
    }


    var setDataByFacility=function(that){
        if(that.facility_name!=""){
            $scope.facility_numbers[that.facility_id]=$scope.facility_numbers[that.facility_id]||{};
            $scope.facility_numbers[that.facility_id].month_data=$scope.facility_numbers[that.facility_id].month_data||{};
            $scope.facility_numbers[that.facility_id].month_data[that.year_month]=$scope.facility_numbers[that.facility_id].month_data[that.year_month]||{};
            var m_data=$scope.facility_numbers[that.facility_id].month_data[that.year_month];
            var f_smpls_rvd=m_data.samples_received||0;
            var f_hpi=m_data.hiv_positive_infants||0;
            var f_i=m_data.initiated||0;

            $scope.facility_numbers[that.facility_id].month_data[that.year_month].samples_received=f_smpls_rvd+that.samples_received;
            $scope.facility_numbers[that.facility_id].month_data[that.year_month].hiv_positive_infants=f_hpi+that.hiv_positive_infants;
            $scope.facility_numbers[that.facility_id].month_data[that.year_month].initiated=f_i+that.initiated;
            $scope.facility_numbers[that.facility_id].name=that.facility_name;
            $scope.facility_numbers[that.facility_id].id=that.facility_id;
        }
    }

    /*var setDataByDuration=function(that){
        var prev_sr=$scope.sr_by_duration[that.year_month]||0;
        var prev_hpi=$scope.hpi_by_duration[that.year_month]||0;
        var prev_i=$scope.i_by_duration[that.year_month]||0;

        $scope.sr_by_duration[that.year_month]=prev_sr+that.samples_received;
        $scope.hpi_by_duration[that.year_month]=prev_hpi+that.hiv_positive_infants;
        $scope.i_by_duration[that.year_month]=prev_i+that.initiated;
    }*/


    var setDataByDistrict=function(that){
        if(that.district_name!=null){
            $scope.district_numbers[that.district_id]=$scope.district_numbers[that.district_id]||{};
            $scope.district_numbers[that.district_id].month_data=$scope.district_numbers[that.district_id].month_data||{};
            $scope.district_numbers[that.district_id].month_data[that.year_month]=$scope.district_numbers[that.district_id].month_data[that.year_month]||{};
            var m_data=$scope.district_numbers[that.district_id].month_data[that.year_month];
            var d_smpls_rvd=m_data.samples_received||0;
            var d_hpi=m_data.hiv_positive_infants||0;
            var d_i=m_data.initiated||0;

            $scope.district_numbers[that.district_id].month_data[that.year_month].samples_received=d_smpls_rvd+that.samples_received;
            $scope.district_numbers[that.district_id].month_data[that.year_month].hiv_positive_infants=d_hpi+that.hiv_positive_infants;
            $scope.district_numbers[that.district_id].month_data[that.year_month].initiated=d_i+that.initiated; 
            $scope.district_numbers[that.district_id].name=that.district_name;
            $scope.district_numbers[that.district_id].id=that.district_id;
        }
    }

    var generalFilter=function(init){
        //console.log("entered the general filter");
        //reduceDistsByReg();
        $scope.loading=true;
        $scope.samples_received=0;$scope.hiv_positive_infants=0;$scope.initiated=0;
        $scope.sr_by_duration={};$scope.hpi_by_duration={};$scope.i_by_duration={};
        var do_nat=$scope.date_filtered||init==1;
        if(do_nat){ nat_sr_by_duration={};nat_hpi_by_duration={};nat_i_by_duration={}; }

        $scope.facility_numbers={};//data to be used in the facility lists for each key indicator
        $scope.district_numbers={};//data to be used in the district lists for each key indicator

        $scope.pcr_one=0;$scope.pcr_two=0;
        $scope.pcr_one_ages=[];//create list to be used for 1st pcr median age
        $scope.pcr_two_ages=[];//create list to be used for 2nd pcr median age     

        for(var i in results_json){
            var that = results_json[i];
            if(inArray(that.year_month,$scope.filter_duration) && do_nat){
                setNationalDataByDuration(that);
            }  
            if(evaluator(that)){
                setKeyIndicators(that); //set the values for the key indicators
                setOtherIndicators(that); //set the values for other indicators
                setDataByDuration(that); //set data by duration to be displayed in graphs                    
                setDataByFacility(that); //set data by facility to be displayed in tables
                setDataByDistrict(that); //set data by district to be displayed in tables
            }         
        }
        //console.log("pcr 1 ages: "+JSON.stringify($scope.pcr_one_ages));
        $scope.first_pcr_median_age=median($scope.pcr_one_ages);
        $scope.sec_pcr_median_age=median($scope.pcr_two_ages);
        

        $scope.filtered=count($scope.filter_regions)>0||count($scope.filter_districts)>0||count($scope.filter_care_levels)>0||$scope.date_filtered;
        if(init==1){ setInits(); }
        $scope.loading=false;  
    };

    var setInits=function(){
        data_init.sr=$scope.samples_received;
        data_init.hpi=$scope.hiv_positive_infants;
        data_init.i=$scope.initiated;
        data_init.sr_bd=$scope.sr_by_duration;
        data_init.hpi_bd=$scope.hpi_by_duration;
        data_init.i_bd=$scope.i_by_duration;
        data_init.nat_srd=nat_sr_by_duration;
        data_init.nat_hbd=nat_hpi_by_duration;
        data_init.nat_ibd=nat_i_by_duration;
        data_init.facility_numbers=$scope.facility_numbers;
        data_init.district_numbers=$scope.district_numbers;
        data_init.pcr_one=$scope.pcr_one;
        data_init.pcr_two=$scope.pcr_two;
        data_init.first_pcr_median_age=$scope.first_pcr_median_age;
        data_init.sec_pcr_median_age=$scope.sec_pcr_median_age;
    }


    var getInits=function(){
        $scope.samples_received=data_init.sr;
        $scope.hiv_positive_infants=data_init.hpi;
        $scope.initiated=data_init.i;
        $scope.sr_by_duration=data_init.sr_bd;
        $scope.hpi_by_duration=data_init.hpi_bd;
        $scope.i_by_duration=data_init.i_bd;
        nat_sr_by_duration=data_init.nat_srd;
        nat_hpi_by_duration=data_init.nat_hbd;
        nat_i_by_duration=data_init.nat_ibd;
        $scope.facility_numbers=data_init.facility_numbers;
        $scope.district_numbers=data_init.district_numbers;
        $scope.pcr_one=data_init.pcr_one;
        $scope.pcr_two=data_init.pcr_two;
        $scope.first_pcr_median_age=data_init.first_pcr_median_age;
        $scope.sec_pcr_median_age=data_init.sec_pcr_median_age;

        $scope.displaySamplesRecieved();
        $scope.displayHIVPositiveInfants();
        $scope.displayPositivityRate();
        $scope.displayInitiationRate();
    }


    $scope.displaySamplesRecieved=function(){     
        var srd=$scope.sr_by_duration; 
        var data=[{"key":"Selection","values":[],"color":"#000" }];

        var labels=[];
        var x=0;
        var y_vals=[];

        for(var i in srd){
            var y_val=Math.round(srd[i]);
            y_vals.push(y_val);
            data[0].values.push({"x":x,"y":y_val});
            labels.push(dateFormat(i));
            x++;
        }

        nv.addGraph(function() {
            var chart = nv.models.lineChart()
                        .margin({right: 50})
                        .useInteractiveGuideline(true)
                        .x(function(d) { return d.x })
                        .y(function(d) { return d.y })
                        .forceY(y_terminals(y_vals));
            
            chart.xAxis.tickFormat(function(d) {
                return labels[d];
            });

            chart.yAxis.tickFormat(d3.format(',.0d'));

            d3.select('#visual1 svg').datum(data).transition().duration(500).call(chart);
            return chart;
        });
    };

    $scope.displayHIVPositiveInfants=function(){  
        var hbd=$scope.hpi_by_duration; 
        var data=[{"key":"Selection","values":[],"color":"#5EA361" }];

        var labels=[];
        var x=0;
        var y_vals=[];

        for(var i in hbd){
            var y_val=Math.round(hbd[i]);
            y_vals.push(y_val);
            data[0].values.push({"x":x,"y":y_val});
            labels.push(dateFormat(i));
            x++;
        }

        nv.addGraph(function() {
            var chart = nv.models.lineChart()
                        .margin({right: 50})
                        .useInteractiveGuideline(true)
                        .x(function(d) { return d.x })
                        .y(function(d) { return d.y })
                        .forceY(y_terminals(y_vals));
            
            chart.xAxis.tickFormat(function(d) {
                return labels[d];
            });

            chart.yAxis.tickFormat(d3.format(',.0d'));

            d3.select('#visual2 svg').datum(data).transition().duration(500).call(chart);
            return chart;
        });
    };


     $scope.displayPositivityRate=function(){ 
        var srd=$scope.sr_by_duration; 
        var hbd=$scope.hpi_by_duration;
        var nat_srd=nat_sr_by_duration; 
        var nat_hbd=nat_hpi_by_duration;

        var data=[{"key":"National","values":[],"color":"#6D6D6D" }];

        var slct=count($scope.filter_regions)>0||count($scope.filter_districts)>0||count($scope.filter_care_levels)>0;
        if(slct){ data.push({"key":"Selection","values":[],"color":"#F5A623" }); }
        var labels=[];
        var x=0;

        for(var i in nat_hbd){             
            data[0].values.push({"x":x,"y":Math.round((nat_hbd[i]/nat_srd[i])*100)});
            if(slct){ data[1].values.push({"x":x,"y":Math.round((hbd[i]/srd[i])*100)}); }
            labels.push(dateFormat(i));
            x++;
        }

        nv.addGraph(function() {
            var chart = nv.models.lineChart()
                        .margin({right: 50})
                        .useInteractiveGuideline(true)
                        .x(function(d) { return d.x })
                        .y(function(d) { return d.y })
                        .forceY([0,20]);
            
            chart.xAxis.tickFormat(function(d) {
                return labels[d];
            });

            chart.yAxis.tickFormat(d3.format(',.0d'));

            d3.select('#visual3 svg').datum(data).transition().duration(500).call(chart);
            return chart;
        });
    };


     $scope.displayInitiationRate=function(){  
        var hbd=$scope.hpi_by_duration;
        var ibd=$scope.i_by_duration; 
        var nat_hbd=nat_hpi_by_duration;
        var nat_ibd=nat_i_by_duration; 

        var data=[{"key":"National","values":[],"color":"#6D6D6D" }];

        var slct=count($scope.filter_regions)>0||count($scope.filter_districts)>0||count($scope.filter_care_levels)>0;
        if(slct){ data.push({"key":"Selection","values":[],"color":"#9F82D1" }); }

        var labels=[];
        var x=0;

        for(var i in nat_ibd){
            data[0].values.push({"x":x,"y":Math.round((nat_ibd[i]/nat_hbd[i])*100)});
            if(slct){ data[1].values.push({"x":x,"y":Math.round((ibd[i]/hbd[i])*100)}); }
            labels.push(dateFormat(i));
            x++;
        }

        nv.addGraph(function() {
            var chart = nv.models.lineChart()
                        .margin({right: 50})
                        .useInteractiveGuideline(true)
                        .x(function(d) { return d.x })
                        .y(function(d) { return d.y })
                        .forceY([0,100]);
            
            chart.xAxis.tickFormat(function(d) {
                return labels[d];
            });

            chart.yAxis.tickFormat(d3.format(',.0d'));

            d3.select('#visual4 svg').datum(data).transition().duration(500).call(chart);
            return chart;
        });
    };





     $scope.removeTag=function(mode,nr){
        switch(mode){
            case "region": delete $scope.filter_regions[nr];break;
            case "district": delete $scope.filter_districts[nr];break;            
            case "care_level": delete $scope.filter_care_levels[nr];break;
        }
        $scope.filter(mode);
    };

    $scope.clearAllFilters=function(){
        $scope.filter_regions={};
        $scope.filter_districts={};        
        $scope.filter_care_levels={};

        $scope.filter_duration=$scope.init_duration;
        $scope.filtered=false;
        $scope.date_filtered=false;
        $scope.fro_date="all";
        $scope.to_date="all";

        getInits();
        $scope.loading=false;
        //generalFilter(1);
    }

    $scope.compare = function(prop,comparator, val){
        return function(item){
            if(comparator=='eq'){
                return item[prop] == val;
            }else if (comparator=='ne'){
               return item[prop] != val;
            }else if (comparator=='gt'){
               return item[prop] > val;
            }else if (comparator=='lt'){
               return item[prop] < val;
            }else if (comparator=='ge'){
               return (item[prop] > val)||(item[prop] == val);
            }else if (comparator=='le'){
               return (item[prop] < val)||(item[prop] == val);
            }else{
                return false;
            }
        }
    };

    $scope.empty=function(prop,status){
        return function(item){
            switch(item[prop]) {
                case "":
                case 0:
                case "0":
                case null:
                case false:
                case typeof this == "undefined":
                if(status=='no'){ return false; } else { return true; };
                    default :  if(status=='no'){ return true; } else { return false; };
                }
        }
           
    };

    var empty=function(val,status){
        switch(val) {
            case "":
            case 0:
            case "0":
            case null:
            case false:
            case typeof this == "undefined":
            if(status=='no'){ return false; } else { return true; };
            default : if(status=='no'){ return true; } else { return false; };
        }
    }

    $scope.showF=function(i){
        var show_f=false;
        switch(i){
            case 1:
            show_f=$scope.show_fclties1;
            $scope.show_fclties1=show_f==false?true:false;        
            break;

            case 2:
            show_f=$scope.show_fclties2;
            $scope.show_fclties2=show_f==false?true:false;
            break;

            case 3:
            show_f=$scope.show_fclties3;
            $scope.show_fclties3=show_f==false?true:false;
            break;

            case 4:
            show_f=$scope.show_fclties4;
            $scope.show_fclties4=show_f==false?true:false;
            break;
        }
        if(show_f==true){
            $("#d_shw"+i).attr("class","active");
            $("#f_shw"+i).attr("class","");
        }else{
            $("#f_shw"+i).attr("class","active");
            $("#d_shw"+i).attr("class","");
        }
    }

    var inArray=function(val,arr){
        var ret=false;
        for(var i in arr){
            if(val==arr[i]) ret=true;
        }
        return ret;
    }

    var dateFormat=function(y_m){
        var arr=y_m.split('-');
        var yr=arr[0];
        var mth=arr[1];
        return $scope.month_labels[mth]+" '"+yr.slice(-2);
    }

    var count=function(json_obj){
        return Object.keys(json_obj).length;
    }

    var pairize=function(arr){
        var ret=[];
        for(var i in arr){
            ret.push({"id":i,"name":arr[i]});
        }
        return ret;
    }

    var y_terminals=function(y_vals){
        var first_y=Math.min.apply(Math, y_vals);
        first_y=Math.round(first_y-(0.5*first_y));
        var last_y=Math.max.apply(Math, y_vals);
        last_y=Math.round(last_y+(0.5*last_y));
        return [first_y,last_y];
    }

    var median=function(values) {
        values.sort( function(a,b) {return a - b;} );
        var half = Math.floor(values.length/2);
        if(values.length % 2)
            return values[half];
        else
            return (values[half-1] + values[half]) / 2.0;
    }

};

app.controller(ctrllers);
