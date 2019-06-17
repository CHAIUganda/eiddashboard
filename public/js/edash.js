//angular stuff

/*
Authors
Name                        @       Period      Role       
Logan Smith                 CHAI    2015(v1)    Interface Design, Q/A
Ina Foalea                  CHAI    2015(v1)    Req Specification, Q/A, UAT
Kitutu Paul                 CHAI    2015(v1)    System development

Credit to CHAI Uganda, CPHL and stakholders
*/
var app=angular.module('dashboard', ['datatables','ngSanitize', 'ngCsv','highcharts-ng'], function($interpolateProvider) {
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

    var gender_json={};
    var pcrs_json={};
    var hubs_json={};
    $scope.source_val = 'cphl';

    var mother_prophylaxes_json={};
    var infant_prophylaxes_json={};

    $scope.districts_slct=[];
    $scope.districts_lables=[];

    $scope.facilities_slct=[];
    $scope.facilities_lables=[];
    $scope.show_art_init = true;
    $scope.show_results_printing = true;


    $scope.params = {
        'districts':[],'hubs':[],'regions':[],'care_levels':[],'age_ranges':[],'genders':[],'pcrs':[],
        'mother_prophylaxes':[],'infant_prophylaxes':[]
    };


    //Age groups ----
    var from_age_json = {};
    var to_age_json = {};
    

    var facilities_json={};   
    var results_json=[]; //to hold a big map will all processed data to later on be used in the generalFilter
    var loaded_years=[];

    var nat_sr_by_duration={};
    var nat_hpi_by_duration={};
    var nat_i_by_duration={};

    var data_init={};

   
    //charts
    $scope.months_array=[]; 
    $scope.first_pcr_array=[];
    $scope.second_pcr_array=[];
    $scope.positivity_array=[];
    $scope.hiv_positive_infants_array=[];


    $http.get("../json/districts_by_region.json").success(function(data){
        dists_by_region=data||{};
    });

    $http.get("../json/facilities.json").success(function(data){
        facilities_json=data||{};
    });

   

    //more filters
    $http.get("../json/gender.json").success(function(data){
        gender_json=data||{};
        $scope.gender_slct=pairize(gender_json);
        //initializeSys();        
    });
    $http.get("../json/pcrs.json").success(function(data){
        pcrs_json=data||{};
        $scope.pcrs_slct=pairize(pcrs_json);
        //initializeSys();        
    });
    
    
    $http.get("../json/from_age.json").success(function(data){
        from_age_json=data||{};
        $scope.from_age_slct=pairize(from_age_json);
        $scope.filter_from_age =pairize(from_age_json);

        //initializeSys();        
    });
    $http.get("../json/to_age.json").success(function(data){
        to_age_json=data||{};
        $scope.to_age_slct =pairize(to_age_json);
        $scope.filter_to_age =pairize(to_age_json);

        //initializeSys();        
    });

     $http.get("../json/mother_prophylaxis.json").success(function(data){
        mother_prophylaxes_json=data||{};
        $scope.mother_prophylaxis_slct=pairize(mother_prophylaxes_json);
        //initializeSys();      
    });

     $http.get("../json/infant_prophylaxis.json").success(function(data){
        infant_prophylaxes_json=data||{};
        $scope.infant_prophylaxis_slct=pairize(infant_prophylaxes_json);
        //initializeSys();        
    });

    $http.get("/other_data/").success(function(data){
        $scope.hubs_slct=[];
        for(var i in data.hubs){
            var obj = data.hubs[i];
            hubs_json[obj.id] = obj.name;
            $scope.hubs_slct.push({"id":obj.id,"name":obj.name});
        }

        $scope.regions_slct=[];
        for(var i in data.regions){
            var obj = data.regions[i];
            regions_json[obj.id] = obj.name;
            $scope.regions_slct.push({"id":obj.id,"name":obj.name});
        }

        
        for(var i in data.districts){
            var obj = data.districts[i];
            districts_json[obj.id] = obj.name;
            $scope.districts_slct.push({"id":obj.id,"name":obj.name});
            $scope.districts_lables[obj.id]=obj.name;
        }

        for(var i in data.facilities){
            var obj = data.facilities[i];
            
            $scope.facilities_slct.push({"id":obj.id,"name":obj.name});
            $scope.facilities_lables[obj.id]=obj.name;
        }

        $scope.care_levels_slct=[];
        for(var i in data.care_levels){
            var obj = data.care_levels[i];
            care_levels_json[obj.id] = obj.name;
            $scope.care_levels_slct.push({"id":obj.id,"name":obj.name});
        }

    });
    
    $http.get("/results_printing_stats/").success(function(data){
        $scope.facilities_array=data.facilities;
    });
   
    var initializeSys=function(){
        var d=new Date();
        var y=d.getFullYear();
        getYearData(y);
    }

    //
    var convertAgeRangesToAgeIds=function(scopeAgeRangesParam){
        var age_ranges_array = scopeAgeRangesParam;
        var age_ids_array=[];
        for (var i = 0; i<age_ranges_array.length ; i++) {
            var from_age_value = parseInt(age_ranges_array[i].from_age);
            var to_age_value = parseInt(age_ranges_array[i].to_age);
            var age_range = to_age_value - from_age_value;
            
            
            
            if(age_range == 1){//to_age becomes the id. This is what we put in the mongoDB.
                
                age_ids_array.push(from_age_value);
                age_ids_array.push(to_age_value);

            }else if(age_range == 0){
                age_ids_array.push(to_age_value);
            }
            else if(age_range > 1){
                var age_range_id = from_age_value;

                for(var age_index=0; age_index <= age_range; age_index++){
                   
                    age_ids_array.push(age_range_id);
                    age_range_id++;
                }//end inner loop
            }
        }//end outer loop

        return age_ids_array;
    };
     

    var getData=function(){
        $scope.loading = true;
            var prms = {};
         
            prms.fro_date = $scope.fro_date_parameter;
            prms.to_date = $scope.to_date_parameter;
            prms.age_ids = JSON.stringify(convertAgeRangesToAgeIds($scope.params.age_ranges));
            prms.districts = JSON.stringify($scope.params.districts);
            prms.regions = JSON.stringify($scope.params.regions);
            prms.hubs = JSON.stringify($scope.params.hubs);
            prms.care_levels = JSON.stringify($scope.params.care_levels);
            prms.genders = JSON.stringify($scope.params.genders);
            prms.pcrs = JSON.stringify($scope.params.pcrs);
            prms.source = $scope.source_val;
           
            $http({method:'GET',url:"/live/",params:prms}).success(function(data) {
                
                //console.log("we rrrr"+JSON.stringify($scope.params));

                
                $scope.district_numbers = data.dist_numbers||{};
                $scope.facility_numbers = data.facility_numbers||{};

                
                var whole_numbers=data.whole_numbers[0]||{};
                $scope.samples_received=whole_numbers.total_tests||0;
                $scope.hiv_positive_infants=whole_numbers.hiv_positive_infants||0;
                $scope.initiated=whole_numbers.art_initiated||0;
                $scope.pcr_one =whole_numbers.pcr_one||0;
                $scope.pcr_two =whole_numbers.pcr_two||0;
                
                
                $scope.duration_numbers = data.duration_numbers||0;
                $scope.duration_numbers_test = data.duration_numbers_test||0;

                $scope.displaySamplesRecieved();
                $scope.displayHIVPositiveInfants();
                $scope.displayPositivityRate();
                $scope.displayInitiationRate();


                //csv downloads
                $scope.export_facility_numbers = exportFacilityNumbers($scope);
                $scope.export_district_numbers = exportDistrictNumbers($scope);
                $scope.current_timestamp = getCurrentTimeStamp();

                $scope.export_district_hiv_positive_infants = exportDistrictHivPositiveInfants($scope);
                $scope.export_facility_hiv_positive_infants = exportFacilityHivPositiveInfant($scope);

                $scope.export_district_positivity_rate = exportDistrictPositivityRate($scope);
                $scope.export_facility_positivity_rate = exportFacilityPositivityRate($scope);


                $scope.export_district_initiation_rate = exportDistrictInitiationRate($scope);
                $scope.export_facility_initiation_rate = exportFacilityInitiationRate($scope);


                $scope.filtered = count($scope.filter_districts)>0||count($scope.filter_hubs)>0||count($scope.filtered_age_range)>0||$scope.date_filtered;    
                $scope.loading = false;

                //transposeDurationNumbers();
                //console.log("lalallalal:: samples_received:: "+data.samples_received+" suppressed:: "+data.suppressed+" "+data.valid_results);
            });
    }
    getData(); 
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
    function exportDistrictNumbers(scopeInstance){
       
        var export_district_numbers = [];
        var district_labels = scopeInstance.districts_lables;
        var district_numbers_from_scope = scopeInstance.district_numbers;

        for( var index = 0; index < district_numbers_from_scope.length; index++){
            var districtRecord = district_numbers_from_scope[index];

            var district_instance = {
                district_name : district_labels[districtRecord._id],
                total_tests : districtRecord.total_tests,
                total_first_pcr : districtRecord.pcr_one,
                total_second_pcr : districtRecord.pcr_two,
            }

            export_district_numbers.push(district_instance);
        }

        return export_district_numbers;
    }
    function exportFacilityNumbers(scopeInstance){
       
        var export_facility_numbers = [];
      
        var facility_details_labels = scopeInstance.facilities_lables;

        var facility_numbers_from_scope = scopeInstance.facility_numbers;

        for( var index = 0; index < facility_numbers_from_scope.length; index++){
            var facilityRecord = facility_numbers_from_scope[index];

            var facility_instance = {                
                facility_name : facility_details_labels[facilityRecord._id],
                total_tests : facilityRecord.total_tests,
                total_first_pcr : facilityRecord.pcr_one,
                total_second_pcr : facilityRecord.pcr_two    
            }

            export_facility_numbers.push(facility_instance);
        }

        return export_facility_numbers;
    }

    function getCurrentTimeStamp(){
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1; //January is 0!
        var yyyy = today.getFullYear();
        var hr = today.getHours();
        var min = today.getMinutes();


        if(dd<10) {
            dd='0'+dd
        } 

        if(mm<10) {
            mm='0'+mm
        } 

        if (min < 10) {
            min = "0" + min;
        }

        today = yyyy+''+mm+''+dd+''+hr+''+min;
        return today;
    }

    function exportDistrictHivPositiveInfants(scopeInstance){
       
        var export_district_numbers = [];
        var district_labels = scopeInstance.districts_lables;
        var district_numbers_from_scope = scopeInstance.district_numbers;

        for( var index = 0; index < district_numbers_from_scope.length; index++){
            var districtRecord = district_numbers_from_scope[index];

            var district_instance = {
                district_name : district_labels[districtRecord._id],
                hiv_positive_infants : districtRecord.hiv_positive_infants,
                total_tests : districtRecord.total_tests,
                
            }

            export_district_numbers.push(district_instance);
        }

        return export_district_numbers;
    }
    function exportFacilityHivPositiveInfant(scopeInstance){
       
        var export_facility_numbers = [];
      
        var facility_details_labels = scopeInstance.facilities_lables;

        var facility_numbers_from_scope = scopeInstance.facility_numbers;

        for( var index = 0; index < facility_numbers_from_scope.length; index++){
            var facilityRecord = facility_numbers_from_scope[index];

            var facility_instance = {                
                facility_name : facility_details_labels[facilityRecord._id],
                hiv_positive_infants : facilityRecord.hiv_positive_infants,
                total_tests : facilityRecord.total_tests,
            }

            export_facility_numbers.push(facility_instance);
        }

        return export_facility_numbers;
    }

    function exportDistrictPositivityRate(scopeInstance){
       
        var export_district_numbers = [];
        var district_labels = scopeInstance.districts_lables;
        var district_numbers_from_scope = scopeInstance.district_numbers;

        for( var index = 0; index < district_numbers_from_scope.length; index++){
            var districtRecord = district_numbers_from_scope[index];
   
            var positivityRate = Math.round((districtRecord.hiv_positive_infants/districtRecord.total_tests)*100);
            var district_instance = {
                district_name : district_labels[districtRecord._id],
                positivity_rate : positivityRate,
                hiv_positive_infants : districtRecord.hiv_positive_infants,
                total_tests : districtRecord.total_tests,
            }

            export_district_numbers.push(district_instance);
        }

        return export_district_numbers;
    }

    function exportFacilityPositivityRate(scopeInstance){
       
        var export_facility_numbers = [];
      
        var facility_details_labels = scopeInstance.facilities_lables;

        var facility_numbers_from_scope = scopeInstance.facility_numbers;

        for( var index = 0; index < facility_numbers_from_scope.length; index++){
            var facilityRecord = facility_numbers_from_scope[index];

            var positivityRate = Math.round((facilityRecord.hiv_positive_infants/facilityRecord.total_tests)*100);

            var facility_instance = {                
                facility_name : facility_details_labels[facilityRecord._id],
                positivity_rate : positivityRate,
                hiv_positive_infants : facilityRecord.hiv_positive_infants,
                total_tests : facilityRecord.total_tests,
            }

            export_facility_numbers.push(facility_instance);
        }

        return export_facility_numbers;
    }

    function exportDistrictInitiationRate(scopeInstance){
       
        var export_district_numbers = [];
        var district_labels = scopeInstance.districts_lables;
        var district_numbers_from_scope = scopeInstance.district_numbers;

        for( var index = 0; index < district_numbers_from_scope.length; index++){
            var districtRecord = district_numbers_from_scope[index];
   
            var initiationRate = Math.round((districtRecord.art_initiated/districtRecord.hiv_positive_infants)*100);
            var district_instance = {
                district_name : district_labels[districtRecord._id],
                initiation_rate : initiationRate,
                hiv_positive_infants : districtRecord.hiv_positive_infants,
                
            }

            export_district_numbers.push(district_instance);
        }

        return export_district_numbers;
    }

    function exportFacilityInitiationRate(scopeInstance){
       
        var export_facility_numbers = [];
      
        var facility_details_labels = scopeInstance.facilities_lables;

        var facility_numbers_from_scope = scopeInstance.facility_numbers;

        for( var index = 0; index < facility_numbers_from_scope.length; index++){
            var facilityRecord = facility_numbers_from_scope[index];

            var initiationRate = Math.round((facilityRecord.art_initiated/facilityRecord.hiv_positive_infants)*100);

            var facility_instance = {                
                facility_name : facility_details_labels[facilityRecord._id],
                initiation_rate : initiationRate,
                hiv_positive_infants : facilityRecord.hiv_positive_infants,
                
            }

            export_facility_numbers.push(facility_instance);
        }

        return export_facility_numbers;
    }

    // Returns the days between a & b date objects...
   function dateDiffInDays(a, b) {
        var _MS_PER_DAY = 1000 * 60 * 60 * 24;
        // Discard the time and time-zone information.
        var utc1 = Date.UTC(a.getFullYear(), a.getMonth(), a.getDate());
        var utc2 = Date.UTC(b.getFullYear(), b.getMonth(), b.getDate());
        return Math.floor((utc2 - utc1) / _MS_PER_DAY);
    }

    var convertStringIntoDate=function(date_timestamp_string){
        var time_stamp_array=date_timestamp_string.split(" ");
        var date_string=time_stamp_array[0];
        var date_array=date_string.split("-");

        var year=date_array[0];
        var month=date_array[1] - 1;
        var day=date_array[2];

        var new_date = new Date(year,month,day);
        return new_date;
    };

    // Calculate how many days between now and an event...
    $scope.generateDaysDifference=function(dateString1){
        var oldest =  convertStringIntoDate(dateString1);
        var latest = new Date();
        return dateDiffInDays(oldest,latest);
    };

    $scope.dateFilter=function(mode){
        if($scope.fro_date!="all" && $scope.to_date!="all"){

            

            var vals={};
            var fro_s=$scope.fro_date.split("-");
            var to_s=$scope.to_date.split("-");
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

                //initialize date parameters
                $scope.fro_date_parameter = generateNumericYearMonth(vals.from_year,vals.from_month);
                $scope.to_date_parameter = generateNumericYearMonth(vals.to_year,vals.to_month);

                getData();                
            }else{
                alert("Please make sure that the 'FROM DATE' is earlier than the 'TO DATE'");
                //console.log("duration expression failing eval1="+eval1+" eval2"+eval2+" eval3"+eval3);
                //console.log("fro yr="+vals.from_year+" fro m"+vals.from_month+" to yr="+vals.to_year+" to m"+vals.to_month);
            }
        }
    }
    var generateNumericYearMonth=function(year_value,month_value){
        var year_month_string="";
        if(month_value > 9){
            year_month_string=year_value+''+month_value;
        }else{
            year_month_string=year_value+''+'0'+month_value;
        }
        var year_month_numeric=Number(year_month_string);

        return year_month_numeric;
        
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
    var convertGenderInSex=function(gender_id){
        var sex="UNKNOWN";
        if(gender_id == 1){
            sex='f';
        }else if(gender_id == 2){
            sex='m';
        }

        return sex;
    }
    var convertPcrIDsToValues=function(pcr_id){
        var pcr_value="UNKNOWN";
        if(pcr_id == 1){
            pcr_value='FIRST';
        }else if(pcr_id == 2){
            pcr_value='SECOND';
        }
        return pcr_value;
    }
    $scope.filter=function(mode){
        switch(mode){
            case "region":
                $scope.filter_regions[$scope.region]=regions_json[$scope.region];
                $scope.params.regions.push(Number($scope.region));
                $scope.region='all';
                break;
            case "district":
                $scope.filter_districts[$scope.district]=districts_json[$scope.district];
                $scope.params.districts.push(Number($scope.district));
                $scope.district='all';            
                break;
            case "care_level":
                $scope.filter_care_levels[$scope.care_level]=care_levels_json[$scope.care_level];
                $scope.params.care_levels.push(Number($scope.care_level));
                $scope.care_level='all';
                break;
            case "gender":
                $scope.filter_gender[$scope.gender]=gender_json[$scope.gender];

                $scope.params.genders.push(convertGenderInSex($scope.gender));
                $scope.gender='all';
                break;
            case "pcr":
                $scope.filter_pcrs[$scope.pcrs]=pcrs_json[$scope.pcrs];
                $scope.params.pcrs.push(convertPcrIDsToValues($scope.pcrs));
                $scope.pcrs='all';
                break;
            case "hub":
                $scope.filter_hubs[$scope.hubs]=hubs_json[$scope.hubs];
                $scope.params.hubs.push(Number($scope.hubs));
                $scope.hubs='all';
                break;
            case "age_range":
                //--validate

                //push
                var from_age_object=JSON.parse($scope.from_age);
                var to_age_object=JSON.parse($scope.to_age);

                var age_range = {"from_age":from_age_object.name,"to_age":to_age_object.name};
                if(isAgeRageValid(age_range)){
                    $scope.filtered_age_range.push(age_range);
                    $scope.params.age_ranges.push(age_range);
                }else{
                    alert("Please make sure your range selection is realistic");
                }
                
                $scope.from_age="all";
                $scope.to_age="all";
                break;

              }

        delete $scope.filter_regions["all"];
        delete $scope.filter_districts["all"];        
        delete $scope.filter_care_levels["all"];

        delete $scope.filter_gender["all"];
        delete $scope.filter_pcrs["all"];
        delete $scope.filter_hubs["all"];
        delete $scope.filtered_age_range["all"];

        getData();
    }

    $scope.setSource = function(val){
        $scope.source_val = val;
        $('.sources').removeClass('active');
        $('#sos_'+val).addClass('active');
        if(val=='poc'){
            $scope.show_art_init = false;
            $scope.show_results_printing = false;
        }else{
            $scope.show_art_init = true;
            $scope.show_results_printing = true;
        }

        getData();
    };

    var isAgeRageValid=function(age_range_to_validate){
        var validated=true;
        var validate_from_age=parseInt(age_range_to_validate.from_age);
        var validate_to_age=parseInt(age_range_to_validate.to_age);

        if(validate_from_age > validate_to_age){
             validated=false;
             return validated;
        }

        for (var index = 0; index < $scope.filtered_age_range.length; index++) {
            var dummy_from_age = $scope.filtered_age_range[index].from_age;
            var dummy_to_age = $scope.filtered_age_range[index].to_age;
            //remove repeating age-ranges
            if(validate_from_age == dummy_from_age && validate_to_age == dummy_to_age){
                validated=false;
                return validated;
            }

            if(validate_from_age == dummy_from_age){
                validated=false;
                return validated;
            }

            if(validate_from_age > dummy_from_age && validate_from_age < dummy_to_age){
                validated=false;
                return validated;
            }

            if(validate_to_age > dummy_from_age && validate_to_age < dummy_to_age){
                validated=false;
                return validated;
            }
            if(validate_to_age == dummy_to_age){
                validated=false;
                return validated;
            }
        };
        return validated;
    };

    $scope.ageRangesCount = function() {
        return $scope.ageRangesCounter++;
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
            var f_smpls_rvd=$scope.facility_numbers[that.facility_id].samples_received||0;
            var f_pcr1=$scope.facility_numbers[that.facility_id].pcr_one||0;
            var f_hpi=$scope.facility_numbers[that.facility_id].hiv_positive_infants||0;
            var f_i=$scope.facility_numbers[that.facility_id].initiated||0;

            $scope.facility_numbers[that.facility_id].samples_received=f_smpls_rvd+that.samples_received;
            $scope.facility_numbers[that.facility_id].pcr_one=f_pcr1+that.pcr_one;
            $scope.facility_numbers[that.facility_id].hiv_positive_infants=f_hpi+that.hiv_positive_infants;
            $scope.facility_numbers[that.facility_id].initiated=f_i+that.initiated;
            $scope.facility_numbers[that.facility_id].name=that.facility_name;
            $scope.facility_numbers[that.facility_id].id=that.facility_id;
        }
    }

    var setDataByDistrict=function(that){
        if(that.district_name!=null){
            $scope.district_numbers[that.district_id]=$scope.district_numbers[that.district_id]||{};
            var d_smpls_rvd=$scope.district_numbers[that.district_id].samples_received||0;
            var d_pcr1=$scope.district_numbers[that.district_id].pcr_one||0;
            var d_hpi=$scope.district_numbers[that.district_id].hiv_positive_infants||0;
            var d_i=$scope.district_numbers[that.district_id].initiated||0;

            $scope.district_numbers[that.district_id].samples_received=d_smpls_rvd+that.samples_received;
            $scope.district_numbers[that.district_id].pcr_one=d_pcr1+that.pcr_one;
            $scope.district_numbers[that.district_id].hiv_positive_infants=d_hpi+that.hiv_positive_infants;
            $scope.district_numbers[that.district_id].initiated=d_i+that.initiated; 
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
        $scope.displaySamplesRecieved();
        $scope.displayHIVPositiveInfants();
        $scope.displayPositivityRate();
        $scope.displayInitiationRate();

        $scope.filtered=count($scope.filter_regions)>0||count($scope.filter_districts)>0||count($scope.filter_care_levels)>0
            ||count($scope.filter_gender)>0 ||count($scope.filter_pcrs)>0 ||count($scope.filter_hubs)>0||$scope.date_filtered
            ||count($scope.filtered_age_range)>0;
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
        /*
        $scope.samples_received=data_init.sr;
        $scope.hiv_positive_infants=data_init.hpi;
        $scope.initiated=data_init.i;
        */
        $scope.sr_by_duration=data_init.sr_bd;
        $scope.hpi_by_duration=data_init.hpi_bd;
        $scope.i_by_duration=data_init.i_bd;
        nat_sr_by_duration=data_init.nat_srd;
        nat_hpi_by_duration=data_init.nat_hbd;
        nat_i_by_duration=data_init.nat_ibd;
        $scope.facility_numbers=data_init.facility_numbers;
        $scope.district_numbers=data_init.district_numbers;
        //$scope.pcr_one=data_init.pcr_one;
        //$scope.pcr_two=data_init.pcr_two;
        $scope.first_pcr_median_age=data_init.first_pcr_median_age;
        $scope.sec_pcr_median_age=data_init.sec_pcr_median_age;

        //$scope.displaySamplesRecieved();
        //$scope.displayHIVPositiveInfants();
        //$scope.displayPositivityRate();
        //$scope.displayInitiationRate();
    }
    
    $scope.displaySamplesRecieved=function(){ 
        
        $scope.months_array=[]; 
        $scope.first_pcr_array=[];
        $scope.second_pcr_array=[];
        $scope.positivity_array=[];

        for(var i in $scope.duration_numbers){
            var obj=$scope.duration_numbers[i];
            var positivity_rate = ((obj.hiv_positive_infants/obj.total_tests)*100);
            

            $scope.months_array.push(dateFormatYearMonth(obj._id));
            $scope.first_pcr_array.push(obj.pcr_one); 
            $scope.second_pcr_array.push(obj.pcr_two);
            $scope.positivity_array.push(Math.round(positivity_rate));
        }


                     var chartConfig = {
                          chart: {
                              type: 'xy'
                          },
                          title: {
                              text: 'Samples Tested'
                          },
                          xAxis: {
                              categories: $scope.months_array
                          },
                         yAxis: [{ // Primary yAxis
                                labels: {
                                    format: '{value}',
                                    style: {
                                        color: Highcharts.getOptions().colors[1]
                                    }
                                },
                                title: {
                                    text: 'Tests',
                                    style: {
                                        color: Highcharts.getOptions().colors[1]
                                    }
                                }
                            }, { // Secondary yAxis
                                title: {
                                    text: 'Positivity',
                                    style: {
                                        color: '#F44336'
                                    }
                                },
                                labels: {
                                    format: '{value} %',
                                    style: {
                                        color: '#F44336'
                                    }
                                },
                                opposite: true
                            }],
                          legend: {
                              align: 'right',
                              x: -70,
                              verticalAlign: 'top',
                              y: 20,
                              floating: true,
                              backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
                              borderColor: '#CCC',
                              borderWidth: 1,
                              shadow: false
                          },
                          tooltip: {
                              formatter: function() {
                                  return '<b>'+ this.x +'</b><br/>'+
                                      this.series.name +': '+ this.y ;
                              }
                          },
                          plotOptions: {
                              column: {
                                  stacking: 'normal',
                                  dataLabels: {
                                      enabled: false,
                                      color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                                      style: {
                                          textShadow: '0 0 3px black, 0 0 3px black'
                                      }
                                  }
                              }

                          },

                          series: [ {
                              name: '2nd PCR',
                              type: 'column',
                              data: $scope.second_pcr_array
                          }, {
                              name: '1st PCR',
                              type: 'column',
                              data: $scope.first_pcr_array
                          },{
                                name: 'Positivity',
                                type: 'spline',
                                yAxis: 1,
                                color: '#d9534f',
                                data: $scope.positivity_array,
                                tooltip: {
                                    valueSuffix: '%'
                                }
                              }
                          ]
                      };
                    $scope.chartConfig = chartConfig;
                    
                    $('#divchart1').highcharts(chartConfig); 
       
    }

    
    $scope.displayHIVPositiveInfants=function(){  
           
        $scope.months_array=[]; 

        $scope.hiv_positive_infants_array=[];
        $scope.duration_pcr_one_hiv_positive_infants=[];
        $scope.duration_pcr_two_hiv_positive_infants=[];
        $scope.duration_pcr_one=[];
        $scope.duration_pcr_two=[];

        for(var i in $scope.duration_numbers){
            var obj=$scope.duration_numbers[i];
            $scope.months_array.push(dateFormatYearMonth(obj._id));
            $scope.hiv_positive_infants_array.push(obj.hiv_positive_infants); 

            $scope.duration_pcr_one_hiv_positive_infants.push(obj.pcr_one_hiv_positive_infants);
            $scope.duration_pcr_two_hiv_positive_infants.push(obj.pcr_two_hiv_positive_infants);

            $scope.duration_pcr_one.push(obj.pcr_one);
            $scope.duration_pcr_two.push(obj.pcr_two);
          
        }


        var chartConfig = {
              chart: {
                  type: 'column'
              },
              title: {
                  text: 'Hiv Positives'
              },
              xAxis: {
                  categories: $scope.months_array
              },
             yAxis: {
                title: {
                        text: 'Number of Hiv Positive Infants'
                    }
                },      
              legend: {
                  align: 'right',
                  x: -70,
                  verticalAlign: 'top',
                  y: 20,
                  floating: true,
                  backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
                  borderColor: '#CCC',
                  borderWidth: 1,
                  shadow: false
              },
              tooltip: {
                  formatter: function() {
                      return '<b>'+ this.x +'</b><br/>'+
                          this.series.name +': '+ this.y +'<br/>'+
                          'Total Postive Tests: '+ this.point.stackTotal;
                  }
              },
              plotOptions: {
                  column: {
                      stacking: 'normal',
                      dataLabels: {
                          enabled: false,
                          color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                          style: {
                              textShadow: '0 0 3px black, 0 0 3px black'
                          }
                      }
                  }
              },

              series: [ {
                    name: '2nd PCR Tests -Positive',
                    data: $scope.duration_pcr_two_hiv_positive_infants,
                    stack: 'positiveTests',
                    tooltip: {
                        valueSuffix: 'positive tests'
                    }
                  },
                  {
                    name: '1st PCR Tests -Positive',
                    data: $scope.duration_pcr_one_hiv_positive_infants,
                    stack: 'positiveTests',
                    tooltip: {
                        valueSuffix: 'positive tests'
                    }
                  }
                  
              ]
          };
        $scope.chartConfigHivPositiveInfants = chartConfig;
        
        $('#divcharthivpositiveinfants').highcharts(chartConfig);
    };


    $scope.displayPositivityRate=function(){ 
       
 

        $scope.months_array=[]; 

        $scope.hiv_positivity_rate_array=[];
        $scope.pcr1_positivity_rate_array=[];
        $scope.pcr2_positivity_rate_array=[];

        for(var i in $scope.duration_numbers){
            var obj=$scope.duration_numbers[i];
            $scope.months_array.push(dateFormatYearMonth(obj._id));
            $scope.hiv_positivity_rate_array.push(Math.round((obj.hiv_positive_infants/obj.total_tests)*100)); 

            $scope.pcr1_positivity_rate_array.push(Math.round((obj.pcr_one_hiv_positive_infants/obj.pcr_one)*100)); 
          
            $scope.pcr2_positivity_rate_array.push(Math.round((obj.pcr_two_hiv_positive_infants/obj.pcr_two)*100)); 
        }


        var chartConfig = {
              chart: {
                  type: 'spline'
              },
              title: {
                  text: 'Percentage of Positive Infants'
              },
              xAxis: {
                  categories: $scope.months_array
              },
             yAxis: {
                title: {
                        text: 'Positivity Rate'
                    }
                },      
              legend: {
                  align: 'right',
                  x: -70,
                  verticalAlign: 'top',
                  y: 20,
                  floating: true,
                  backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
                  borderColor: '#CCC',
                  borderWidth: 1,
                  shadow: false
              },
              tooltip: {
                  formatter: function() {
                      return '<b>'+ this.x +'</b><br/>'+
                          this.series.name +': '+ this.y +'<br/>';
                  }
              },
              plotOptions: {
                  column: {
                      stacking: 'normal',
                      dataLabels: {
                          enabled: true,
                          color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                          style: {
                              textShadow: '0 0 3px black, 0 0 3px black'
                          }
                      }
                  }
              },

              series: [ {
                    name: 'Positivity',
                    data: $scope.hiv_positivity_rate_array,
                    tooltip: {
                        valueSuffix: '%'
                    },
                    color: '#d9534f'
                  },
                  {
                    name: 'Positivity in PCR 1',
                    data: $scope.pcr1_positivity_rate_array,
                    tooltip: {
                        valueSuffix: '%'
                    }
                  },
                  {
                    name: 'Positivity in PCR 2',
                    data: $scope.pcr2_positivity_rate_array,
                    tooltip: {
                        valueSuffix: '%'
                    }
                  }
              ]
          };
        $scope.chartConfigHivPositivityRate = chartConfig;
        
        $('#divcharthivpositivityrate').highcharts(chartConfig);
    };



    $scope.displayInitiationRate=function(){  
        
       


        $scope.months_array=[]; 

        $scope.hiv_initiation_rate_array=[];
        $scope.pcr1_hiv_initiation_rate_array=[];
        $scope.pcr2_hiv_initiation_rate_array=[];

        for(var i in $scope.duration_numbers){
            var obj=$scope.duration_numbers[i];
            $scope.months_array.push(dateFormatYearMonth(obj._id));
            $scope.hiv_initiation_rate_array.push(Math.round((obj.art_initiated/obj.hiv_positive_infants)*100)); 
                      
            $scope.pcr1_hiv_initiation_rate_array.push(Math.round((obj.pcr_one_art_initiated/obj.pcr_one_hiv_positive_infants)*100)); 
            $scope.pcr2_hiv_initiation_rate_array.push(Math.round((obj.pcr_two_art_initiated/obj.pcr_two_hiv_positive_infants)*100)); 

        }


        var chartConfig = {
              chart: {
                  type: 'spline'
              },
              title: {
                  text: 'Percentage of Infants Initiated on ART'
              },
              xAxis: {
                  categories: $scope.months_array
              },
             yAxis: {
                title: {
                        text: 'Initiation Rate'
                    }
                },      
              legend: {
                  align: 'right',
                  x: -70,
                  verticalAlign: 'top',
                  y: 20,
                  floating: true,
                  backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
                  borderColor: '#CCC',
                  borderWidth: 1,
                  shadow: false
              },
              tooltip: {
                  formatter: function() {
                      return '<b>'+ this.x +'</b><br/>'+
                          this.series.name +': '+ this.y +'<br/>';
                          
                  }
              },
              plotOptions: {
                  column: {
                      stacking: 'normal',
                      dataLabels: {
                          enabled: true,
                          color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                          style: {
                              textShadow: '0 0 3px black, 0 0 3px black'
                          }
                      }
                  }
              },

              series: [ 
                    {
                    name: 'initiation Rate',
                    data: $scope.hiv_initiation_rate_array,
                    tooltip: {
                        valueSuffix: '%'
                    },
                    color: '#d9534f'
                  },

                  {
                    name: 'initiation Rate in PCR 1',
                    data: $scope.pcr1_hiv_initiation_rate_array,
                    tooltip: {
                        valueSuffix: '%'
                    }
                  },

                  {
                    name: 'initiation Rate in PCR 2',
                    data: $scope.pcr2_hiv_initiation_rate_array,
                    tooltip: {
                        valueSuffix: '%'
                    }
                  }
                  
              ]
          };
        $scope.chartConfigInitiationRate = chartConfig;
        
        $('#divchartinitiationrate').highcharts(chartConfig);

    };


    $scope.removeTag=function(mode,nr){
        switch(mode){
            case "region": delete $scope.filter_regions[nr];break;
            case "district": delete $scope.filter_districts[nr];break;            
            case "care_level": delete $scope.filter_care_levels[nr];break;
            case "gender": delete $scope.filter_gender[nr];break;
            case "pcr": delete $scope.filter_pcrs[nr];break;
            case "hub": delete $scope.filter_hubs[nr];break;
            case "age_range": 
                delete $scope.filtered_age_range[nr];
                $scope.params.age_ranges=removeAgeGroup(nr,$scope.params.age_ranges);
                break;
        }
        $scope.filter(mode);
    };

    var removeAgeGroup=function(index,age_range_array){
        var age_range_array_cleaned = [];
        for(var i =0; i< age_range_array.length;i++){
            if(index != i)
            {

               age_range_array_cleaned.push(age_range_array[i]); 
            }
        }

        return age_range_array_cleaned;
    };
    $scope.clearAllFilters=function(){
        $scope.filter_regions={};
        $scope.filter_districts={};        
        $scope.filter_care_levels={};
        $scope.filter_gender={};
        $scope.filter_pcrs={};
        $scope.filter_hubs={};

        $scope.filter_duration=$scope.init_duration;
        $scope.filtered=false;
        $scope.date_filtered=false;
        $scope.fro_date="all";
        $scope.to_date="all";
        $scope.filtered_age_range=[];

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
    var dateFormatYearMonth=function(x){
        var year_month_string = x+"";
        var year_key=parseInt(year_month_string.substring(2,4));
        var month_key= parseInt(year_month_string.substring(4));

        var desired_date = $scope.month_labels[month_key]+"'"+year_key;
        return desired_date;
    };

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
