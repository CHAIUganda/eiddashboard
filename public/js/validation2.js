/***************************/
//@Author: Adrian "yEnS" Mato Gondelle & Ivan Guardado Castro
//@website: www.yensdesign.com
//@email: yensamg@gmail.com
//@license: Feel free to use it, but keep this credits please!					
/***************************/

$(document).ready(function(){
	//global vars
	var form = $("#customForm");
	var lotno = $("#lotno");
	var lotInfo = $("#lotInfo");
	var spekkitno = $("#spekkitno");
	var spekkitnoInfo = $("#spekkitnoInfo");
	
	var rackno = $("#rackno");
	var rackInfo = $("#rackInfo");
	var hiqcap = $("#hiqcap");
	var hiqcapInfo = $("#hiqcapInfo");
			
	var kitexp = $("#kitexp");
	var kitexpInfo = $("#kitexpInfo");
	
		var datecut = $("#datecut");
	var datecutInfo = $("#datecutInfo");
	//On blur
	spekkitno.blur(validateSpekkitno);
	lotno.blur(validateLotno);
	rackno.blur(validateRackno);
	hiqcap.blur(validateHiqcap);
kitexp.blur(validateKitexp);
datecut.blur(validateDatecut);

	//On key press
	
	spekkitno.keyup(validateSpekkitno);
	lotno.keyup(validateLotno);
	rackno.keyup(validateRackno);
	hiqcap.keyup(validateHiqcap);
	 kitexp.keyup(validateKitexp);
	datecut.keyup(validateDatecut);
	
	
	//ON CLICK
		datecut.click(validateDatecut);
	kitexp.click(validateKitexp);
	//on change
		datecut.change(validateDatecut);
	kitexp.change(validateKitexp);
	
	//On Submitting
	form.submit(function(){
		if(validateSpekkitno()  & validateLotno()  & validateRackno() & validateHiqcap()  & validateKitexp() & validateDatecut() )
			return true
		else
			return false;
	});
	
	
	//ensure lot no is not null
	function validateSpekkitno(){
		//if it's NOT valid
		if(spekkitno.val().length < 1){
			spekkitno.addClass("error");
			spekkitnoInfo.text("Enter Spek Kit No	!");
			spekkitnoInfo.addClass("error");
			return false;
		}
		//if it's valid
		else{
			spekkitno.removeClass("error");
			spekkitnoInfo.text("");
			spekkitnoInfo.removeClass("error");
			return true;
		}
	}
	
	//ensure kit expiry not null
	function validateKitexp(){
		//if it's NOT valid
		if(kitexp.val().length < 1){
			kitexp.addClass("error");
			kitexpInfo.text("Enter Kit Expiry Date	!");
			kitexpInfo.addClass("error");
			return false;
		}
		//if it's valid
		else{
			kitexp.removeClass("error");
			kitexpInfo.text("");
			kitexpInfo.removeClass("error");
			return true;
		}
	}
	
	//ensure date cur not null
	function validateDatecut(){
		//if it's NOT valid
		if(datecut.val().length < 1){
			datecut.addClass("error");
			datecutInfo.text("Enter Date Cut!");
			datecutInfo.addClass("error");
			return false;
		}
		//if it's valid
		else{
			datecut.removeClass("error");
			datecutInfo.text("");
			datecutInfo.removeClass("error");
			return true;
		}
	}
	//ensure lot no is not null
	function validateLotno(){
		//if it's NOT valid
		if(lotno.val().length < 1){
			lotno.addClass("error");
			lotInfo.text("Enter Lot No!");
			lotInfo.addClass("error");
			return false;
		}
		//if it's valid
		else{
			lotno.removeClass("error");
			lotInfo.text("");
			lotInfo.removeClass("error");
			return true;
		}
	}
	//ensure rackno is not null
	function validateRackno(){
		//if it's NOT valid
		if(rackno.val().length < 1){
			rackno.addClass("error");
			rackInfo.text("Enter Rack No!");
			rackInfo.addClass("error");
			return false;
		}
		//if it's valid
		else{
			rackno.removeClass("error");
			rackInfo.text("");
			rackInfo.removeClass("error");
			return true;
		}
			
	}
	//ensure hiqq cap no not null
		function validateHiqcap(){
		//if it's NOT valid
		if(hiqcap.val().length < 1){
			hiqcap.addClass("error");
			hiqcapInfo.text("Enter HIQCAP Kit No!");
			hiqcapInfo.addClass("error");
			return false;
		}
		//if it's valid
		else{
			hiqcap.removeClass("error");
			hiqcapInfo.text("");
			hiqcapInfo.removeClass("error");
			return true;
		}
	}
	
});