// JavaScript Document
(function($) {
    "use strict";
	
	//calling foundation js
	jQuery(document).foundation();
	
	jQuery.fn.exists = function(){ return this.length > 0; }

	jQuery(document).on("keyup", "#tofw_price, #tofw_cost, .tofw_validate_number", function(){
		var valid = /^-?[0-9]\d*(\.\d+)?$/.test(this.value),
		//var valid = /^\d{0,8}(\.\d{0,2})?$/.test(this.value),
		val = this.value;

		if(!valid){
			console.log("Invalid input!");
			this.value = val.substring(0, val.length - 1);
		}
	});

	jQuery("#btnPrint").on("click", function() {
		window.print();
	});

	jQuery(document).ready(function() {
		$('#customer, #technician, #select_otb_products, #select_product, #otb_locations, #otb_devices, #select_otb_services, #job_technician, #job_customer').select2();

		if($('#updateUserFormReveal').exists()) {
			$('#updateUserFormReveal').foundation('open');
		}

		$("#current_date").text(return_date());
	});

	function return_time() {
		var d = new Date();
		var time = d.getHours() + ":" + d.getMinutes() + ":" + d.getSeconds();

		return time;
	}

	function return_date() {
		var d 		= new Date();
		var date 	= d.toLocaleString();   

		return date;
	}

	if ($('#updateStatus').length) {
		$('#statusFormReveal').foundation('toggle');
	}

})(jQuery); //jQuery main function ends strict Mode on