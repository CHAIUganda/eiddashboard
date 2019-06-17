"use strict";
/* jshint quotmark: false */
/* global $, moment */


$(function(){


function getAge(str){/* handles most input except things like "1 year and 2 months" */

	var MAX_ALLOWED_MONTHS = 18;
	var MAX_ALLOWED_WEEKS = 78;
	var MAX_ALLOWED_YEARS = 1;

	var WEEKS_IN_A_YEAR = 52;
	// var WEEKS_IN_A_MONTH = 4;

	var MONTHS_IN_YEAR = 12; 

	var input_str = str.trim().toLowerCase();

    var age = {};
        age.src = input_str;
        age.hasWords = false;// age is words (e.g. week, month, year). convert to fraction.
        age.hasSlash = false; 
        age.hasSlashAt = -1; 
        
        // fraction: this is where we store age data as we calculate it. 
		// represents the infants age as a mixed fraction or a proper fraction (if age <= 1yr)
        age.fraction = {};	
        age.fraction.whole = 0; // represents age in years. valid values = 1 or 0
        age.fraction.numerator = 0; // must always be less than denominator
        age.fraction.denominator = 0; // valid values: {12 = age is in months, 52 = in weeks, 1 = in months (value is in numerator )}

        // this is where we store data after we are done calculating the age.
        age.weeks = 0;
        age.months = 0;
        age.years = 0;

        age.warning = null;
        age.error = null;

        age.toString = function(){
        	var s = "";
        	var y = "", m = "", w = "";

        	if (this.years > 0){
        		y = " year" + ( (this.years > 1) ? "s" : "" );
        		s = s + " " + this.years + y;
        	}

        	if (this.months > 0){
        		m = " month" + ( (this.months > 1) ? "s" : "" );
        		s = s + " "  + this.months + m;
        	}

        	if (this.weeks > 0){
        		w = " week" + ( (this.weeks > 1) ? "s" : "" );
        		s = s + " " + this.weeks + w;
        	}

        	return s;
        };

        age.getErrors = function(){
        	return this.error;
        };

// do sanity checks on entered data:
	input_str = input_str.replace(/\\/g , "/"); // make sure all slashes are fwd slashes
	var slashes = input_str.match(/\//g);// 
	var dots = input_str.match(/\./g);


	var nDots = dots ? dots.length : 0;
	var nSlashes = slashes ? slashes.length : 0;

	if(nSlashes > 1){

		age.error = "Too many slashes '/' =>  max allowed  = 1";			
		return age;
	}


	if(nDots > 1){
		age.error = "Too many dots (.) =>  max allowed  = 1";
		return age;	
	}


	if( (nSlashes > 0) && (nDots > 0) ){
		age.error = "Its not allowed to have BOTH a slash and a dot";
		return age;
	}


	var numbers_found = input_str.match(/\d+(\.\d+)?/g);
	var words_found = input_str.match(/[a-z]/) || "";

	if(!numbers_found){

		age.error = "Invalid age given: Type the child's age correctly";
		return age;
	}

	var input_has_1_number = (numbers_found.length === 1)? true : false;

	if(input_has_1_number){

		var n = parseFloat(numbers_found[0]);
		var first_letter = (words_found === null)? 'm' : words_found[0];
			first_letter = ( first_letter != 'w' && first_letter != 'm' &&  first_letter != 'y' ) ? 'm' : first_letter;


		if( first_letter == 'w'){// weeks

			alert("w");

			var nWeeks = n;

			if(nWeeks > MAX_ALLOWED_WEEKS){
				age.warning = "Too old: max allowed age = " + MAX_ALLOWED_WEEKS + " weeks or (" + MAX_ALLOWED_MONTHS + " months)";
			}

			age.years = 0;  // unused
			age.months = 0; // unused
			age.weeks = nWeeks;

			return age;
		}


		if( first_letter === 'm' ){// months

			var nMonths = n;

			if(nMonths > MAX_ALLOWED_MONTHS){
				age.warning = "Too old: max allowed age = " + MAX_ALLOWED_MONTHS + " months";
			}

			// initialize
			age.years = 0;
			age.months = nMonths;
			age.weeks = 0;

			if( nMonths > MONTHS_IN_YEAR ){

				age.years = Math.floor(nMonths / MONTHS_IN_YEAR);
				age.months = nMonths % MONTHS_IN_YEAR;
				age.weeks = 0; // unused
			}

			return age;
		}

		if( first_letter === 'y'){// years

			var nYears = n;

			if(nYears > MAX_ALLOWED_MONTHS){
				age.warning = "Too old: max allowed age = " + MAX_ALLOWED_MONTHS + " months";
			}

			age.years = nYears;
			age.months = 0; // unused
			age.weeks = 0; // unused

			return age;
		}
	}


	if(nSlashes === 0){// error. we expected a fraction.
		age.error = "Bad input: There's Extra space or a missing slash(/) or missing decimal point (.)";
		return age;
	}


//
// if we reach here, it means user entered more than one number plus a slash
// this means data is likely to be in fraction format e.g.
//		5/12 = 5 months
//		7/52 = 7 weeks
//		1 and 3/12 = 1 year and 3 months				
//


	var ans = input_str.match(/\d+\s*[a-z]*[\\\/]?/g);

	numbers_found = ans.length;
	
	if(numbers_found > 3){ // fractions can't have more than 3 numbers:
		age.error = "Bad value given for age";
		return age;
	}


	ans[0] = ans && ans[0] || "";
	ans[1] = ans && ans[1] || "";
	ans[2] = ans && ans[2] || "";
	

	var n1 = ans[0].match(/[\d]*/);
	var n2 = ans[1].match(/[\d]*/);
	var n3 = ans[2].match(/[\d]*/);

	var filter1 = ans[0].match(/[^\d*^\s*]/);
	var filter2 = ans[1].match(/[^\d*^\s*]/);
	// var filter3 = ans[2].match(/[^\d*^\s*]/);


	var num1 = parseInt( n1 );
	var num2 = parseInt( n2 );
	var num3 = parseInt( n3 );


	if( $.isNumeric(parseInt(num3)) ){// mixed fraction


	// console.log("filtered '" + ans[0] + "' => num = '" + num1 + "', filter1 ='" + filter1 + "'");
	// console.log("filtered '" + ans[1] + "' => num = '" + num2 + "', filter2 ='" + filter2 + "'");
	// console.log("filtered '" + ans[2] + "' => num = '" + num3 + "', filter3 ='" + filter3 + "'");

		if(filter2 != '/'){

			age.error =  "1: Your slash (/) is in the wrong place (num3 = " + num3 + ")";
			return age;
		}

        age.fraction.whole = num1;
        age.fraction.numerator = num2;
        age.fraction.denominator = num3;

        // console.log( age.fraction );

	}else{

	// console.log("filtered-- '" + ans[0] + "' => num = '" + num1 + "', filter1 ='" + filter1 + "'");
	// console.log("filtered-- '" + ans[1] + "' => num = '" + num2 + "', filter2 ='" + filter2 + "'");
	// console.log("filtered-- '" + ans[2] + "' => num = '" + num3 + "', filter3 ='" + filter3 + "'");

		if(filter1 != '/'){

			age.error =  "#2: Your slash (/) is in the wrong place isNum(num3 = " + $.isNumeric(num3) + ")";
			return age;
		}

        age.fraction.whole = 0;
        age.fraction.numerator = parseInt(num1);
        age.fraction.denominator = parseInt(num2);
	}

	if(age.fraction.denominator != MONTHS_IN_YEAR && age.fraction.denominator != WEEKS_IN_A_YEAR){
		age.error = age.fraction.denominator + 
			"\nThe number after the slash must be 12 or 52\n\n " +
			"(i.e. 12 = months in a year, 52 = weeks in a year)";
		return age;
	}

	if (age.fraction.numerator > age.fraction.denominator){// improper fraction. fix it.

		age.fraction.whole += Math.floor(age.fraction.numerator / age.fraction.denominator);
        age.fraction.numerator = age.fraction.numerator % age.fraction.denominator;

	}

	if(age.fraction.whole > MAX_ALLOWED_YEARS){

		age.error = "Age limit = " + MAX_ALLOWED_MONTHS + " months";
		return age;
	}


	
	if(	age.fraction.denominator === MONTHS_IN_YEAR){

		age.years = age.fraction.whole;
		age.months = age.fraction.numerator;
		age.weeks = 0; // unused


		var ageInMonths = (age.years * MONTHS_IN_YEAR) +  age.months;

		if( ageInMonths > MAX_ALLOWED_MONTHS){
			age.error = "Age limit = " + MAX_ALLOWED_MONTHS + " months";
			return age;
		}
	}
	else if(age.fraction.denominator === WEEKS_IN_A_YEAR){


		age.years = age.fraction.whole;
		age.months = 0; // unused
		age.weeks = age.fraction.numerator;

		// console.log("years = " + age.years + ", weeks = " + age.weeks);


		var ageInWeeks = (age.years * WEEKS_IN_A_YEAR) +  age.weeks;
		if( ageInWeeks > MAX_ALLOWED_WEEKS){
			age.error = "Age limit = " + MAX_ALLOWED_WEEKS + " weeks (" + MAX_ALLOWED_MONTHS + "months)";
			return age;
		}
	}

	return age;
}


		
		function estimate_dob(age) {

			var dbst = moment($("#date_dbs_taken_1").val(), 'Do MMM YYYY');
			var date_of_birth = dbst.subtract({year: age.years, months: age.months, weeks: age.weeks});

				return date_of_birth.format('YYYY-MM-DD');
		}

		$("td").on("change", ".ageFmt", function() {


            var age = getAge(this.value);
            var err = age.error ? age.error : "";
            var warning = age.warn ? age.warn : "";


            if(err) window.alert(err);
            if(warning) window.alert(err);


            this.value = age.toString();
            

            var dob = this.id.replace("infant_age_", "infant_dob_");
            $("#"+dob).val( estimate_dob(age) );

            document.getElementById("dbs_status").innerHTML = err;
            document.getElementById("dbs_status").innerHTML = warning;
        });
});