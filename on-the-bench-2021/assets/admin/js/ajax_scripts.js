// JavaScript Document
(function($) {
    "use strict";
	
	$(document).ready(function() { tofw_grand_total_calculations(); });
	
	function tofw_grand_total_calculations() {
		var products_grand_total 	= 0;
		var parts_grand_total 		= 0;
		var service_grand_total 	= 0;
		var extra_grand_total 		= 0;
		var parts_tax_total 		= 0;
		var paroducts_tax_total 	= 0;
		var service_tax_total 		= 0;
		var extra_tax_total			= 0;
		
		if (undefined !== $(".tofw_product_price_total")){
			var i = 1;	
			$('.tofw_product_price_total').each(function(i) {
				var total = $(this).html();

				total = parseFloat(total);
				
				if(!isNaN(total)) {
					products_grand_total = parseFloat(total+products_grand_total);
				}
			});
		}

		if (undefined !== $(".tofw_price_total")){
			var i = 1;	
			$('.tofw_price_total').each(function(i) {
				var total = $(this).html();

				total = parseFloat(total);
				
				if(!isNaN(total)) {
					parts_grand_total = parseFloat(total+parts_grand_total);
				}
			});
		}
		
		var i = 1;	
		$('.tofw_service_price_total').each(function(i) {
			var total = $(this).html();

			total = parseFloat(total);
			
			if(!isNaN(total)) {
				service_grand_total = parseFloat(total+service_grand_total);
			}
		});
		
		var i = 1;	
		$('.tofw_extra_price_total').each(function(i) {
			var total = $(this).html();

			total = parseFloat(total);
			
			if(!isNaN(total)) {
				extra_grand_total = parseFloat(total+extra_grand_total);
			}
		});

		//Calculate Parts Tax if exists
		if (undefined !== $(".tofw_product_tax_price")){
			var i = 1;

			$('.tofw_product_tax_price').each(function(i) {
				var total = $(this).html();
	
				total = parseFloat(total);
				
				if(!isNaN(total)) {
					paroducts_tax_total = parseFloat(total+paroducts_tax_total);
				}
			});

			products_grand_total = products_grand_total-paroducts_tax_total;

			$(".tofw_products_tax_total .amount").html(paroducts_tax_total.toFixed(2));
		}

		//Calculate Parts Tax if exists
		if (undefined !== $(".tofw_part_tax_price")){
			var i = 1;

			$('.tofw_part_tax_price').each(function(i) {
				var total = $(this).html();
	
				total = parseFloat(total);
				
				if(!isNaN(total)) {
					parts_tax_total = parseFloat(total+parts_tax_total);
				}
			});

			parts_grand_total = parts_grand_total-parts_tax_total;

			$(".tofw_parts_tax_total .amount").html(parts_tax_total.toFixed(2));
		}
		
		//Calculate extra Tax if exists
		if (undefined !== $(".tofw_extra_tax_price")){
			var i = 1;

			$('.tofw_extra_tax_price').each(function(i) {
				var total = $(this).html();
	
				total = parseFloat(total);
				
				if(!isNaN(total)) {
					extra_tax_total = parseFloat(total+extra_tax_total);
				}
			});

			extra_grand_total = extra_grand_total-extra_tax_total;

			$(".tofw_extras_tax_total .amount").html(extra_tax_total.toFixed(2));
		}

		//Calculate service Tax if exists
		if (undefined !== $(".tofw_service_tax_price")){
			var i = 1;

			$('.tofw_service_tax_price').each(function(i) {
				var total = $(this).html();
	
				total = parseFloat(total);
				
				if(!isNaN(total)) {
					service_tax_total = parseFloat(total+service_tax_total);
				}
			});

			service_grand_total = service_grand_total-service_tax_total;

			$(".tofw_services_tax_total .amount").html(service_tax_total.toFixed(2));
		}
		
		var grand_total = parseFloat(products_grand_total)+parseFloat(service_grand_total)+parseFloat(parts_grand_total)+parseFloat(extra_grand_total)+parseFloat(parts_tax_total)+parseFloat(paroducts_tax_total)+parseFloat(service_tax_total)+parseFloat(extra_tax_total);
		
		$(".tofw_products_grandtotal .amount").html(products_grand_total.toFixed(2));
		$(".tofw_parts_grandtotal .amount").html(parts_grand_total.toFixed(2));
		$(".tofw_services_grandtotal .amount").html(service_grand_total.toFixed(2));
		$(".tofw_extras_grandtotal .amount").html(extra_grand_total.toFixed(2));
		
		$(".tofw_grandtotal .amount").html(grand_total.toFixed(2));
	}
	
	function calculate_part_item_total(array_index) {
		var product_price 		= $("[name='tofw_part_price[]']").get(array_index).value;
		var product_quantity 	= $("[name='tofw_part_qty[]']").get(array_index).value;
		
		
		if(isNaN(product_quantity)) { 
			alert("Quantity needs to be a number!");
			$("[name='tofw_part_qty[]']").get(array_index).value = 1;
			$("[name='tofw_part_qty[]']").get(array_index).focus();
			return false;
		}
		
		if(isNaN(product_price)) { 
			alert("Price needs to be a number!");
			$("[name='tofw_part_price[]']").get(array_index).value = 1;
			$("[name='tofw_part_price[]']").get(array_index).focus();
			return false;
		}

		var total 		= parseFloat(product_price)*parseFloat(product_quantity);
		
		var $calculated_tax = 0;
		var $calculated_tax_dp = 0;

		if (undefined !== $("[name='tofw_part_tax[]']").get(array_index)){
			var product_tax			= $("[name='tofw_part_tax[]']").get(array_index).value;

			if($("[name='tofw_part_tax[]']").get(array_index).value.length) {
				// do something here
				if(isNaN(product_tax)) { 
					alert("Your tax seems not a number.");
					return false;
				} else {
					$calculated_tax = (parseFloat(total)/100)*parseFloat(product_tax);

					$calculated_tax_dp = $calculated_tax.toFixed(2);

					$(".tofw_part_tax_price").get(array_index).innerHTML = $calculated_tax_dp;
				}	
			} else {
				$(".tofw_part_tax_price").get(array_index).innerHTML = $calculated_tax;	
			}
		}
		
		var grand_total = total+$calculated_tax;
		
		$(".tofw_price_total").get(array_index).innerHTML = grand_total.toFixed(2);
		
		tofw_grand_total_calculations();
	}

	function calculate_product_item_total(array_index) {
		var product_price 		= $("[name='tofw_product_price[]']").get(array_index).value;
		var product_quantity 	= $("[name='tofw_product_qty[]']").get(array_index).value;
	

		if(isNaN(product_quantity)) { 
			alert("Quantity needs to be a number!");
			$("[name='tofw_product_qty[]']").get(array_index).value = 1;
			$("[name='tofw_product_qty[]']").get(array_index).focus();
			return false;
		}
		
		if(isNaN(product_price)) { 
			alert("Price needs to be a number!");
			$("[name='tofw_product_price[]']").get(array_index).value = 1;
			$("[name='tofw_product_price[]']").get(array_index).focus();
			return false;
		}

		var total 				= parseFloat(product_price)*parseFloat(product_quantity);
		
		var $calculated_tax 	= 0;
		var $calculated_tax_dp 	= 0;

		if (undefined !== $("[name='tofw_product_tax[]']").get(array_index)){
			var product_tax			= $("[name='tofw_product_tax[]']").get(array_index).value;

			if($("[name='tofw_product_tax[]']").get(array_index).value.length) {
				// do something here
				if(isNaN(product_tax)) { 
					alert("Your tax seems not a number.");
					return false;
				} else {
					$calculated_tax = (parseFloat(total)/100)*parseFloat(product_tax);

					$calculated_tax_dp = $calculated_tax.toFixed(2);

					$(".tofw_product_tax_price").get(array_index).innerHTML = $calculated_tax_dp;
				}	
			} else {
				$(".tofw_product_tax_price").get(array_index).innerHTML = $calculated_tax;	
			}
		}
		
		var grand_total = total+$calculated_tax;
		
		$(".tofw_product_price_total").get(array_index).innerHTML = grand_total.toFixed(2);
		
		tofw_grand_total_calculations();
	}
	
	function calculate_service_item_total(array_index) {
		var service_price 		= $("[name='tofw_service_price[]']").get(array_index).value;
		var service_quantity 	= $("[name='tofw_service_qty[]']").get(array_index).value;
		
		if(isNaN(service_quantity)) { 
			alert("Quantity needs to be a number!");
			$("[name='tofw_service_qty[]']").get(array_index).value = 1;
			$("[name='tofw_service_qty[]']").get(array_index).focus();
			return false;
		}
		
		if(isNaN(service_price)) { 
			alert("Price needs to be a number!");
			$("[name='tofw_service_price[]']").get(array_index).value = 1;
			$("[name='tofw_service_price[]']").get(array_index).focus();
			return false;
		}
		
		var total 		= parseFloat(service_price)*parseFloat(service_quantity);
		
		var $calculated_tax = 0;
		var $calculated_tax_dp = 0;

		if (undefined !== $("[name='tofw_service_tax[]']").get(array_index)){
			var service_tax			= $("[name='tofw_service_tax[]']").get(array_index).value;

			if($("[name='tofw_service_tax[]']").get(array_index).value.length) {
				// do something here
				if(isNaN(service_tax)) { 
					alert("Your tax seems not a number.");
					return false;
				} else {
					$calculated_tax = (parseFloat(total)/100)*parseFloat(service_tax);

					$calculated_tax_dp = $calculated_tax.toFixed(2);

					$(".tofw_service_tax_price").get(array_index).innerHTML = $calculated_tax_dp;
				}	
			} else {
				$(".tofw_service_tax_price").get(array_index).innerHTML = $calculated_tax;	
			}
		}

		$(".tofw_service_price_total").get(array_index).innerHTML = total+$calculated_tax;
		
		tofw_grand_total_calculations();
	}
	
	function calculate_extra_item_total(array_index) {
		var service_price 		= $("[name='tofw_extra_price[]']").get(array_index).value;
		var service_quantity 	= $("[name='tofw_extra_qty[]']").get(array_index).value;
		
		if(isNaN(service_quantity)) { 
			alert("Quantity needs to be a number!");
			$("[name='tofw_extra_qty[]']").get(array_index).value = 1;
			$("[name='tofw_extra_qty[]']").get(array_index).focus();
			return false;
		}
		
		if(isNaN(service_price)) { 
			alert("Price needs to be a number!");
			$("[name='tofw_extra_price[]']").get(array_index).value = 1;
			$("[name='tofw_extra_price[]']").get(array_index).focus();
			return false;
		}
		
		var total 	= parseFloat(service_price)*parseFloat(service_quantity);
		
		var $calculated_tax = 0;
		var $calculated_tax_dp = 0;

		if (undefined !== $("[name='tofw_extra_tax[]']").get(array_index)){
			var extra_tax			= $("[name='tofw_extra_tax[]']").get(array_index).value;

			if($("[name='tofw_extra_tax[]']").get(array_index).value.length) {
				// do something here
				if(isNaN(extra_tax)) { 
					alert("Your tax seems not a number.");
					return false;
				} else {
					$calculated_tax = (parseFloat(total)/100)*parseFloat(extra_tax);
					
					$calculated_tax_dp = $calculated_tax.toFixed(2);
					
					$(".tofw_extra_tax_price").get(array_index).innerHTML = $calculated_tax_dp;
				}	
			} else {
				$(".tofw_extra_tax_price").get(array_index).innerHTML = $calculated_tax;	
			}
		}

		$(".tofw_extra_price_total").get(array_index).innerHTML = total+$calculated_tax;
		
		tofw_grand_total_calculations();
	}
	
	//On Quantity Change call function
	$(document).on("change", "[name='tofw_part_qty[]']", function(){
		var array_index 		= $(this).index("[name='tofw_part_qty[]']");
		
		calculate_part_item_total(array_index);
	});
	
	//On Quantity Change call function
	$(document).on("change", "[name='tofw_part_price[]']", function(){
		var array_index 		= $(this).index("[name='tofw_part_price[]']");
		
		calculate_part_item_total(array_index);
	});

	//On Tax Change call function
	$(document).on("change", "[name='tofw_part_tax[]']", function(){
		var array_index 		= $(this).index("[name='tofw_part_tax[]']");
		
		calculate_part_item_total(array_index);
	});

	//On Quantity Change call function
	$(document).on("change", "[name='tofw_product_qty[]']", function(){
		var array_index 		= $(this).index("[name='tofw_product_qty[]']");
		
		calculate_product_item_total(array_index);
	});
	
	//On Quantity Change call function
	$(document).on("change", "[name='tofw_product_price[]']", function(){
		var array_index 		= $(this).index("[name='tofw_product_price[]']");
		
		calculate_product_item_total(array_index);
	});

	//On Product Tax Change
	$(document).on("change", "[name='tofw_product_tax[]']", function(){
		var array_index 		= $(this).index("[name='tofw_product_tax[]']");
		
		calculate_product_item_total(array_index);
	});
	
	//On Quantity Change call function
	$(document).on("change", "[name='tofw_service_qty[]']", function(){
		var array_index 		= $(this).index("[name='tofw_service_qty[]']");
		
		calculate_service_item_total(array_index);
	});
	
	//On Quantity Change call function
	$(document).on("change", "[name='tofw_service_price[]']", function(){
		var array_index 		= $(this).index("[name='tofw_service_price[]']");
		
		calculate_service_item_total(array_index);
	});
	
	//On Tax Change call function
	$(document).on("change", "[name='tofw_service_tax[]']", function(){
		var array_index 		= $(this).index("[name='tofw_service_tax[]']");
		
		calculate_service_item_total(array_index);
	});

	//On Quantity Change call function
	$(document).on("change", "[name='tofw_extra_qty[]']", function(){
		var array_index 		= $(this).index("[name='tofw_extra_qty[]']");
		
		calculate_extra_item_total(array_index);
	});
	
	//On Quantity Change call function
	$(document).on("change", "[name='tofw_extra_price[]']", function(){
		var array_index 		= $(this).index("[name='tofw_extra_price[]']");
		
		calculate_extra_item_total(array_index);
	});
	
	//On Tax Change call function
	$(document).on("change", "[name='tofw_extra_tax[]']", function(){
		var array_index 		= $(this).index("[name='tofw_extra_tax[]']");
		
		calculate_extra_item_total(array_index);
	});

	$("form[data-async]").on("submit",function(e) {
	  e.preventDefault();
	  return false;
	});
	
	$("form[data-async]").on("forminvalid.zf.abide", function(e,target) {
	  console.log("form is invalid");
	});
	
	$("form[data-async]").on("formvalid.zf.abide", function(e,target) {
		var $form 		 = $(this);
		var formData 	 = $form.serialize();

		var $input = $(this).find("input[name=form_type]");

		if($input.val() == "tax_form") {
			var $perform_act = "tofw_post_taxes";	
		} else if($input.val() == "status_form") {
			var $perform_act = "tofw_post_status";
		} else if($input.val() == "update_user") {
			var $perform_act = "tofw_update_user_data";	
		} else {
			var $perform_act = "tofw_post_customer";	
		}

		$.ajax({
			type: $form.attr('method'),
			data: formData + '&action='+$perform_act,
			url: ajax_obj.ajax_url,
			dataType: 'json',

			beforeSend: function() {
				$('.form-message').html("<div class='spinner is-active'></div>");
			},
			success: function(response) {
				//console.log(response);
				var message 		= response.message;
				var success 		= response.success;
				
				$('.form-message').html('<div class="callout success" data-closable="slide-out-right">'+message+'<button class="close-button" aria-label="Dismiss alert" type="button" data-close><span aria-hidden="true">&times;</span></button></div>');
				
				if(success == "YES" && $perform_act != "tofw_update_user_data") {
					$form.trigger("reset");	
				}

				if ($('#updateStatus').length) {
					location.reload();
				}

				if($perform_act != "tofw_post_status") {
					$("#poststuff_wrapper").load(window.location + " #poststuff");
				} else {
					$("#job_status_wrapper").load(window.location + " #status_poststuff");
				}

				if($perform_act == "tofw_post_customer") {
					var user_id		= response.user_id;
					$('#tofw_job_details_box #customer').val(user_id).change();
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				console.log("Error Thrown: "+errorThrown);
				console.log("Text Status: "+textStatus);
				console.log("XMLHttpRequest: "+MLHttpRequest);
		   }
		});
	});
	
	
	$("#addPart").on("click", function(e,target) {
		
		var product_id = $("#select_otb_products").val();
		
		if(product_id == "") {
			alert("Please select part to add");
		} else {
			$.ajax({
				type: 'POST',
				data: {
					'action': 'tofw_update_parts_row',
					'product': product_id
				},
				url: ajax_obj.ajax_url,
				dataType: 'json',

				beforeSend: function() {
					$('.parts_body_message').html("<div class='spinner is-active'></div>");
				},
				success: function(response) {
					//console.log(response);
					$('.parts_body_message').html("");
					
					var row  = response.row;

					$('.parts_body').append(row);
					
					$("#select_otb_products").select2('val', 'All');
					
					//Calculations update //function defined in my-admin.js
					tofw_grand_total_calculations();
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					console.log("Error Thrown: "+errorThrown);
					console.log("Text Status: "+textStatus);
					console.log("XMLHttpRequest: "+MLHttpRequest);
			   }
			});
		}	
	});

	$("#addProduct").on("click", function(e,target) {
		
		var product_id = $("#select_product").val();
		
		if(product_id == "") {
			alert("Please select part to add");
		} else {
			$.ajax({
				type: 'POST',
				data: {
					'action': 'tofw_update_parts_row',
					'product': product_id,
					'product_type': 'woo'
				},
				url: ajax_obj.ajax_url,
				dataType: 'json',

				beforeSend: function() {
					$('.products_body_message').html("<div class='spinner is-active'></div>");
				},
				success: function(response) {
					//console.log(response);
					$('.products_body_message').html("");
					
					var row  = response.row;

					$('.products_body').append(row);
					
					$("#select_product").select2('val', 'All');
					
					//Calculations update //function defined in my-admin.js
					tofw_grand_total_calculations();
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					console.log("Error Thrown: "+errorThrown);
					console.log("Text Status: "+textStatus);
					console.log("XMLHttpRequest: "+MLHttpRequest);
			   }
			});
		}	
	});
	
	$("#addService").on("click", function(e,target) {
		
		var service_id = $("#select_otb_services").val();
		
		if(service_id == "") {
			alert("Please select service to add");
		} else {
			$.ajax({
				type: 'POST',
				data: {
					'action': 'tofw_update_services_row',
					'service': service_id
				},
				url: ajax_obj.ajax_url,
				dataType: 'json',

				beforeSend: function() {
					$('.services_body_message').html("<div class='spinner is-active'></div>");
				},
				success: function(response) {
					//console.log(response);
					$('.services_body_message').html("");
					
					var row  = response.row;

					$('.services_body').append(row);
					
					$("#select_otb_services").select2('val', 'All');
					
					//Calculations update //function defined in my-admin.js
					tofw_grand_total_calculations();
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					console.log("Error Thrown: "+errorThrown);
					console.log("Text Status: "+textStatus);
					console.log("XMLHttpRequest: "+MLHttpRequest);
			   }
			});
		}	
	});
	
	$("#addExtra").on("click", function(e,target) {
		
		$.ajax({
			type: 'POST',
			data: {
				'action': 'tofw_update_extra_row',
				'extra': 'yes'
			},
			url: ajax_obj.ajax_url,
			dataType: 'json',

			beforeSend: function() {
				$('.extra_body_message').html("<div class='spinner is-active'></div>");
			},
			success: function(response) {
				//console.log(response);
				$('.extra_body_message').html("");

				var row  = response.row;

				$('.extra_body').append(row);

				//Calculations update //function defined in my-admin.js
				tofw_grand_total_calculations();
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				console.log("Error Thrown: "+errorThrown);
				console.log("Text Status: "+textStatus);
				console.log("XMLHttpRequest: "+MLHttpRequest);
		   }
		});
	});
	
	$('.parts_body, .services_body, .extra_body, .products_body').on('click', '.delme', function() {
		$(this).parents('.item-row').remove();
		tofw_grand_total_calculations();
	});	

	
	//Change Tax Status Functionality
	$(document).on("click", ".change_tax_status", function(e, target){
		e.preventDefault();

		var recordID 	= $(this).attr("data-value");
		var recordType 	= $(this).attr("data-type");

		if(recordID == "" && recordType == "") {
			alert("Please select correct value");
		} else {
			
			$.ajax({
				type: 'POST',
				data: {
					'action': 'tofw_update_tax_or_status',
					'recordID': recordID, 
					'recordType': recordType 
				},
				url: ajax_obj.ajax_url,
				dataType: 'json',
	
				beforeSend: function() {
					$('.form-update-message').html("<div class='spinner is-active'></div>");
				},
				success: function(response) {
					//console.log(response);
					var message 	= response.message;
					var success 	= response.success;
					
					$('.form-update-message').html('<div class="callout success" data-closable="slide-out-right">'+message+'<button class="close-button" aria-label="Dismiss alert" type="button" data-close><span aria-hidden="true">&times;</span></button></div>');
	
					if(recordType == "tax") {
						//$('#poststuff_wrapper').load(document.URL +  ' #poststuff_wrapper');
						$("#poststuff_wrapper").load(document.location + " #poststuff");
					} else if(recordType == "status" || recordType == "inventory_count") {
						$("#job_status_wrapper").load(document.location + " #status_poststuff");
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					console.log("Error Thrown: "+errorThrown);
					console.log("Text Status: "+textStatus);
					console.log("XMLHttpRequest: "+MLHttpRequest);
			   }
			});
		}
	});
	
	//Change Tax Status Functionality
	$(document).on("change", ".update_status", function(e, target){
		e.preventDefault();

		var recordID 	= $(this).attr("data-post");
		var statusValue	= $(this).val();
		
		if(recordID == "") {
			alert("Please select correct value");
		} else {
			
			$.ajax({
				type: 'POST',
				data: {
					'action': 'tofw_update_job_status',
					'recordID': recordID,
					'orderStatus': statusValue
				},
				url: ajax_obj.ajax_url,
				dataType: 'json',
	
				beforeSend: function() {
					$( "table.wp-list-table" ).prepend( "<div class='spinner is-active'></div>" );
				},
				success: function(response) {
					//console.log(response);
					var message 	= response.message;
					
					$( "table.wp-list-table" ).prepend( message );

					//$('#poststuff_wrapper').load(document.URL +  ' #poststuff_wrapper');
					$("#wpbody").load(document.location + " #wpbody-content");

				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					console.log("Error Thrown: "+errorThrown);
					console.log("Text Status: "+textStatus);
					console.log("XMLHttpRequest: "+MLHttpRequest);
				}
			});
		}
	});

	jQuery(document).ready(function() {
        $('.bc-product-search').select2({
            ajax: {
                url: ajaxurl,
                data: function (params) {
                    return {
                        term         : params.term,
                        action       : 'woocommerce_json_search_products_and_variations',
                        security: $(this).attr('data-security'),
						exclude_type : $( this ).data( 'exclude_type' ),
						display_stock: $( this ).data( 'display_stock' )
                    };
                },
                processResults: function( data ) {
                    var terms = [];
                    if ( data ) {
                        $.each( data, function( id, text ) {
                            terms.push( { id: id, text: text } );
                        });
                    }
                    return {
                        results: terms
                    };
                },
                cache: true
            }
        });
    });
})(jQuery); //jQuery main function ends strict Mode on