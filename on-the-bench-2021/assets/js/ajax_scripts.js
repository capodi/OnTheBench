// JavaScript Document
(function($) {
    "use strict";

	$("form[data-async]").on("submit",function(e) {
	  	e.preventDefault();

	  	var $form 	= $(this);
	  	var $target = $($form.attr('data-target'));

	  	var formData = $form.serialize();
		var $input = $(this).find("input[name=form_type]");

		if($input.val() == "tofw_request_quote_form") {
			var $perform_act = "tofw_otb_submit_quote_form";	
		} else if($input.val() == "tofw_create_new_job_form") {
			var $perform_act = "tofw_otb_create_new_job";
		} else {
			var $perform_act = "tofw_cmp_otb_check_order_status";	
		}

		$.ajax({
			type: $form.attr('method'),
			data: formData + '&action='+$perform_act,
			url: ajax_obj.ajax_url,
			dataType: 'json',

			beforeSend: function() {
				$('.form-message').html("<div class='spinner is-active'>Loading ...</div>");
			},
			success: function(response) {
				//console.log(response);
				var message 		= response.message;
				var success 		= response.success;
				var reset_select2 	= response.reset_select2;

				$('.form-message').html('<div class="callout success" data-closable="slide-out-right">'+message+'</div>');
				
				if(success == "YES") {
					$form.trigger("reset");	
				
					if(reset_select2 == "YES") {
						$("#customer, #otb_devices").val(null).trigger('change');
/*CBA*/				    $("#customer, #otb_locations").val(null).trigger('change');
					}
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				console.log("Error Thrown: "+errorThrown);
				console.log("Text Status: "+textStatus);
				console.log("XMLHttpRequest: "+MLHttpRequest);
			}
		});
	});
})(jQuery); //jQuery main function ends strict Mode on