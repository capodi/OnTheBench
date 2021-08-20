// JavaScript Document
(function($) {
    "use strict";
	
	//calling foundation js
	jQuery(document).foundation();
	
	jQuery(document).ready(function() {
		$('#otb_devices, #customer').select2();
/*CBA*/ $('#otb_locations, #customer').select2();

		$("#customerFormReveal").on("click", function() {
			$("#addNewCustomer").toggleClass('displayBlock');

			if($('.addNewCustomer.displayBlock').length) {
				$("#verifyCustomer").val("1");
			} else {
				$("#verifyCustomer").val("0");
			}
			
		});
	});
})(jQuery); //jQuery main function ends strict Mode on