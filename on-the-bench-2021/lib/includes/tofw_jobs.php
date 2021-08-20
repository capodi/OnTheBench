<?php
	/*
		* Function to Register Post Type
		*
		* @ For Jobs or Cases
		*
		* @ Since 2.0.0
	*/	

	function tofw_on_the_1_bench_jobs_init() {
		$labels = array(
			'add_new_item' 			=> esc_html__('Add new Job', 'on-the-bench'),
			'singular_name' 		=> esc_html__('Job', 'on-the-bench'), 
			'menu_name' 			=> esc_html__('Jobs', 'on-the-bench'),
			'all_items' 			=> esc_html__('Jobs', 'on-the-bench'),
			'edit_item' 			=> esc_html__('Edit Job', 'on-the-bench'),
			'new_item' 				=> esc_html__('New Job', 'on-the-bench'),
			'view_item' 			=> esc_html__('View Job', 'on-the-bench'),
			'search_items' 			=> esc_html__('Search Job', 'on-the-bench'),
			'not_found' 			=> esc_html__('No Job found', 'on-the-bench'),
			'not_found_in_trash' 	=> esc_html__('No Job in trash', 'on-the-bench')
		);

		$args = array(
			'labels'             	=> $labels,
			'label'					=> esc_html__('Jobs', 'on-the-bench'),
			'description'        	=> esc_html__('Jobs Section', 'on-the-bench'),
			'public'             	=> false,
			'publicly_queryable' 	=> false,
			'show_ui'            	=> true,
			'show_in_menu'       	=> false,
			'query_var'          	=> true,
			'rewrite'            	=> array('slug' => 'jobs'),
			'capability_type'    	=> array('otb_job', 'otb_jobs'),
			'has_archive'        	=> true,
			'menu_icon'			 	=> 'dashicons-clipboard',
			'menu_position'      	=> 30,
			'supports'           	=> array(''), 	
			'register_meta_box_cb' 	=> 'tofw_job_features',
		);

		register_post_type('otb_jobs', $args);
	}
	add_action('init', 'tofw_on_the_1_bench_jobs_init');
	//registeration of post type ends here.


function tofw_job_features() { 
	$screens = array('otb_jobs');

	foreach ( $screens as $screen ) {
		//Orer Status and Order Total
		add_meta_box(
			'tofw_order_info_id',
			esc_html__('Order Information', 'on-the-bench'),
			'tofw_jobs_features_c',
			$screen, 'side', 'high'
		);
		
		//Second Metabox
		add_meta_box(
			'tofw_job_details_box',
			esc_html__('Job Details', 'on-the-bench'),
			'tofw_jobs_features_callback',
			$screen, 'advanced', 'high'
		);
	}
} //Parts features post.
add_action( 'add_meta_boxes', 'tofw_job_features');


function tofw_jobs_features_c( $post ) {
	wp_nonce_field( 'tofw_meta_box_nonce', 'tofw_jobs_features_sub' );
	settings_errors();
	
	$system_currency 	= get_option('tofw_system_currency');
	$tofw_use_taxes 		= get_option("tofw_use_taxes");

	$parts_returned 		= tofw_print_existing_parts($post->ID);


	$content = '<div class="order_calculations">';
	
	$content .= '<table class="order_totals_calculations">';
	
	if(is_parts_switch_woo() == true) {
		//Product To Display!
		//WooCommerce Products Active
		$content .= '<tr>
						<th>'.esc_html__("Products Total", "on-the-bench").'</th>
						<td class="tofw_products_grandtotal">'.esc_attr($system_currency).'<span class="amount">0.00</span></td>
					</tr>';
	}

	if(is_parts_switch_woo() == false || !empty($parts_returned)):
		$content .= '<tr>
						<th>'.esc_html__("Parts Total", "on-the-bench").'</th>
						<td class="tofw_parts_grandtotal">'.esc_attr($system_currency).'<span class="amount">0.00</span></td>
					</tr>';
	endif;				
	
	$content .= '<tr>
				 	<th>'.esc_html__("Services Total", "on-the-bench").'</th>
				 	<td class="tofw_services_grandtotal">'.esc_attr($system_currency).'<span class="amount">0.00</span></td>
				 </tr>';
	
	$content .= '<tr>
				 	<th>'.esc_html__("Extras Total", "on-the-bench").'</th>
				 	<td class="tofw_extras_grandtotal">'.esc_attr($system_currency).'<span class="amount">0.00</span></td>
				 </tr>';

	if(is_parts_switch_woo() == true) {
		if($tofw_use_taxes == "on"):
			$content .= '<tr>
						<th>'.esc_html__("Products Tax", "on-the-bench").'</th>
						<td class="tofw_products_tax_total">'.esc_attr($system_currency).'<span class="amount">0.00</span></td>
					</tr>';	
		endif;	
	}

	if($tofw_use_taxes == "on"):
		if(is_parts_switch_woo() == false || !empty($parts_returned)):
			$content .= '<tr>
						<th>'.esc_html__("Parts Tax", "on-the-bench").'</th>
						<td class="tofw_parts_tax_total">'.esc_attr($system_currency).'<span class="amount">0.00</span></td>
					</tr>';
		endif;			

	$content .= '<tr>
				 <th>'.esc_html__("Services Tax", "on-the-bench").'</th>
				 <td class="tofw_services_tax_total">'.esc_attr($system_currency).'<span class="amount">0.00</span></td>
			 </tr>';

	$content .= '<tr>
				 <th>'.esc_html__("Extras Tax", "on-the-bench").'</th>
				 <td class="tofw_extras_tax_total">'.esc_attr($system_currency).'<span class="amount">0.00</span></td>
			 </tr>';
	endif;		 
	
	$content .= '<tr class="grand_total_row">
				 	<th>'.esc_html__("Grand Total", "on-the-bench").'</th>
				 	<td class="tofw_grandtotal">'.esc_attr($system_currency).'<span class="amount">0.00</span></td>
				 </tr>';
	
	$content .= '</table>';
	
	
	$content .= '<div class="tofw_order_status_wrap"><h3>';
	
	$tofw_order_status = get_post_meta($post->ID, "_tofw_order_status", true);
	$selected = " selected='selected'";
	
	
	$content .= esc_html__("Order Status", "on-the-bench");
	$content .= '</h3><select name="tofw_order_status">';

	if(empty($tofw_order_status)) {
		$tofw_order_status = "new";
	}
	$content .= tofw_generate_status_options($tofw_order_status);
	$content .= '</select></div>';
	
	$tofw_payment_status = get_post_meta($post->ID, "_tofw_payment_status", true);
	
	$content .= '<div class="tofw_order_status_wrap"><h3>';
	$content .= esc_html__("Payment Status", "on-the-bench");
	$content .= '</h3><select name="tofw_payment_status">';
/*CBA*/	$content .= '<option value="warranty"'.($tofw_payment_status == "warranty" ? $selected: "").'>'.esc_html__("Warranty", "on-the-bench").'</option>';
/*CBA*/	$content .= '<option value="pending"'.($tofw_payment_status == "pending" ? $selected: "").'>'.esc_html__("Pending", "on-the-bench").'</option>';
	$content .= '<option value="credit"'.($tofw_payment_status == "credit" ? $selected: "").'>'.esc_html__("Credit", "on-the-bench").'</option>';
	$content .= '<option value="paid"'.($tofw_payment_status == "paid" ? $selected: "").'>'.esc_html__("Paid", "on-the-bench").'</option>';
	$content .= '<option value="partial"'.($tofw_payment_status == "partial" ? $selected: "").'>'.esc_html__("Partially Paid", "on-the-bench").'</option>';
	$content .= '</select></div>';
	
	$tofw_file_attachment_in_job = get_option("tofw_file_attachment_in_job");

	if($tofw_file_attachment_in_job == "on"):
		$content .= '<div class="tofw_order_file_wrap"><h3>';
		$content .= esc_html__("Upload Files", "on-the-bench");
		$content .= "</h3>";
		$meta_key = 'tofw_job_file';
		$content .=  tofw_image_uploader_field( $meta_key, get_post_meta($post->ID, "_".$meta_key, true) );
		$content .= "</div>";
	endif; //File uploading ends.	

	$content .= '<div class="tofw_order_note_wrap"><h3>';
	
	$order_notes = get_post_meta($post->ID, "_tofw_order_note", true);
	$content .= esc_html__("Order Notes:", "on-the-bench");
	$content .= '</h3><textarea name="tofw_order_note">';
	$content .= $order_notes;
	$content .= '</textarea></div>';
	
	//CBA: Job Contact Begin
	$content .= '<div class="tofw_order_Cntct_wrap"><h3>';
	$order_Cntct = get_post_meta($post->ID, "_tofw_order_Cntct", true);
	$content .= esc_html__("Job Contact:", "on-the-bench");
	$content .= '</h3><textarea name="tofw_order_Cntct">';
	$content .= $order_Cntct;
	$content .= '</textarea></div>';
	$content .= '<div class="tofw_order_SMS_wrap"><h3>';
	$order_SMS = get_post_meta($post->ID, "_tofw_order_SMS", true);
	$content .= esc_html__("Job Cell:", "on-the-bench");
	$content .= '</h3><textarea name="tofw_order_SMS">';
	$content .= $order_SMS;
	$content .= '</textarea></div>';
	//CBA: Job Contact End

	$content .= "</div>";

	//CBA: SendSMSButton Begin
	$content .= '<center>';
	$content .= '<input type="submit" name="sndSMS" value="Send SMS" class="button button-primary button-large" id="sendSMS"/input>';	$content .= '&nbsp;&nbsp;&nbsp;';
	$content .= '</center>';
	//CBA: SendSMSButton End
	
	//Print Repair Order!
	$content .= '<div class="grid-y grid-margin-y"><div class="small-12 cell">';
	$content .= '<a target="_blank" href="admin.php?page=tofw_on_the_bench_print&print_type=repair_order&order_id='.$post->ID.'" class="button button-primary button-small pull-right" id="printRepairOrder">'.esc_html__("Print Repair Order", "on-the-bench").'</a>';
	$content .= '<a target="_blank" style="color:#FFF;margin-left:6px;" href="admin.php?page=tofw_on_the_bench_print&print_type=repair_label&order_id='.$post->ID.'" class="button success button-primary button-small pull-right" id="printRepairLabel">'.esc_html__("Print Repair Label", "on-the-bench").'</a>';
	$content .= '</div>';

	$content .= '<div class="small-12 cell">';
	$content .= '<a id="printorder" class="button button-primary button-large" target="_blank" href="admin.php?page=tofw_on_the_bench_print&order_id='.$post->ID.'">'.esc_html__("Print Order", "on-the-bench").'</a>';
	$content .= ' <a id="emailcustomer" style="color:#FFF;" class="button success button-primary button-large" target="_blank" href="admin.php?page=tofw_on_the_bench_print&order_id='.$post->ID.'&email_customer=yes">'.esc_html__("Email Customer", "on-the-bench").'</a>';

	$content .= "</div></div>";

	echo $content;

//CBA: SendSMS Begin

	$tocell = $order_SMS;
	$mycell = '4695837727';
	$to_SMS = array($tocell, $mycell);
	$is_flash = false;
	$cnv_flsh = $is_flash ? 'true' : 'false';
	$msgfrm = "\"On the Bench\"";
	
	$MyAction = get_option("MyAction");
/*	$help_me = '<br><center>bSEND '.$MyAction.' END</center>';
	echo $help_me;*/
	
	if( $MyAction == 'Yes' ) { 
	  wp_sms_send( $to_SMS, $order_notes, $is_flash, $msgfrm );
	  update_option('MyAction', 'No');
	}
	$MyAction = get_option("MyAction");
/*	$help_me = '<br><center>aSEND '.$MyAction.' END</center>';
	echo $help_me;*/
	
//CBA: SendSMS End
}


function tofw_jobs_features_callback( $post ) {
	wp_nonce_field( 'tofw_meta_box_nonce', 'tofw_jobs_features_call_sub' );
	settings_errors();
	
	$system_currency 	= get_option('tofw_system_currency');

	$content = '';
	
	$content .= '<div class="grid-x grid-margin-x">';

	$content .= '<div class="cell small-6">';
	$content .= '<label>';
	$content .= esc_html__('Case Number', 'on-the-bench');
	
	$random_string 	= tofw_generate_random_string(6).time();
	$case_number 	= get_post_meta($post->ID, "_case_number", true);
	
	$case_number 	= ($case_number == '') ? $random_string: $case_number;
	
	$content .= '<input type="text" name="case_number" value="'.$case_number.'" />';
	$content .= '</label>';
	$content .= '</div>'; //Column Ends
	
	
	$content .= '<div class="cell small-6">';
	$content .= '<label>';
	$content .= esc_html__('Delivery Date', 'on-the-bench');
	
	$delivery_date 	= get_post_meta($post->ID, "_delivery_date", true);

	$content .= '<input type="date" name="delivery_date" value="'.$delivery_date.'" />';
	$content .= '</label>';
	$content .= '</div>'; //Column Ends
	
	$content .= '</div>'; //Row Ends

	$content .= '<div class="grid-x grid-margin-x">';
	
	$content .= '<div class="cell small-6">';
	$content .= '<label>';
	$content .= esc_html__('Select Customer', 'on-the-bench');
	
	$selected_user 	= get_post_meta($post->ID, "_customer", true);
	$user_value 	= ($selected_user == '') ? '': $selected_user;
	
	$customer_args = array( 
			'show_option_all' => esc_html__('Select Customer', 'on-the-bench'), 
			'name' 			=> 'customer', 
			'role' 			=> 'customer', 
			'echo' 			=> 0, 
			'selected' 		=> $user_value );

	$content .= tofw_users_dropdown( $customer_args );
	$content .= '</label>';
	$content .= '<p class="help-text">'.esc_html__("Select customer if does not exist!", 'on-the-bench').' <a class="button button-primary button-small" data-open="customerFormReveal">'.esc_html__("Add New Customer", "on-the-bench").'</a></p>';
	$content .= '</div>'; //Column Ends
	//Add Reveal Form
	add_filter('admin_footer','tofw_add_user_form');

	$content .= '<div class="cell small-6">';
	$content .= '<label>';
	$content .= esc_html__('Select Technician', 'on-the-bench');
	
	$selected_user 	= get_post_meta($post->ID, "_technician", true);
	$user_value 	= ($selected_user == '') ? '': $selected_user;
	
	/*$tofw_add_admin_to_technician = get_option("tofw_add_admin_to_technician");

	if($tofw_add_admin_to_technician == "on") {
		$technician_role = '';	
	} else {
		$technician_role = array('technician');
	}*/

	$content .= wp_dropdown_users( array( 'show_option_all' => esc_html__('Select Technician', 'on-the-bench'), 'name' => 'technician', 'role' => 'technician', 'echo' => 0, 'selected' => $user_value ));
	$content .= '</label>';
	$content .= '<p class="help-text">'.esc_html__("Select technician if does not exist!", 'on-the-bench').' <a class="button button-primary button-small" data-open="technicianFormReveal">'.esc_html__("Add New Technician", "on-the-bench").'</a></p>';
	$content .= '</div>'; //Column Ends
	//Add Reveal Form
	add_filter('admin_footer','tofw_add_technician_form');

	$content .= '</div>'; //Row Ends


/*CBA: Location Integration	*/

	$content .= '<div class="grid-x grid-margin-x">';

	$content .= '<div class="cell small-6">';
	$content .= '<label>';
	$content .= esc_html__('Location', 'on-the-bench');
		
	$location_post_id 	= get_post_meta($post->ID, "_location_post_id", true);
	
	$content .= '<select id="otb_locations" name="location_post_id">';
	if(empty($location_post_id)) {
		$location_post_id = "";
	}
	$content .= tofw_generate_location_options($location_post_id);
	$content .= '</select>';

	$content .= '</label>';
	$content .= '</div>'; //Column Ends

	$content .= '<div class="cell small-6">';
	$content .= '<label>';
	$content .= esc_html__('Location ID/LCTN', 'on-the-bench');
	
	$location_id 	= get_post_meta($post->ID, "_location_id", true);

	$content .= '<input type="text" name="location_id" value="'.esc_html($location_id).'" />';
	$content .= '</label>';
	$content .= '</div>'; //Column Ends
		
	$content .= '</div>'; //Row Ends 

/* CBA: Location Integration ends */


	/*
		Device Integration
	*/

	$content .= '<div class="grid-x grid-margin-x">';

	$content .= '<div class="cell small-6">';
	$content .= '<label>';
	$content .= esc_html__('Device', 'on-the-bench');
		
	$device_post_id 	= get_post_meta($post->ID, "_device_post_id", true);
	
	$content .= '<select id="otb_devices" name="device_post_id">';
	if(empty($device_post_id)) {
		$device_post_id = "";
	}
	$content .= tofw_generate_device_options($device_post_id);
	$content .= '</select>';

	$content .= '</label>';
	$content .= '</div>'; //Column Ends
	
	$content .= '<div class="cell small-6">';
	$content .= '<label>';
	$content .= esc_html__('Device ID/IMEI', 'on-the-bench');
	
	$device_id 	= get_post_meta($post->ID, "_device_id", true);

	$content .= '<input type="text" name="device_id" value="'.esc_html($device_id).'" />';
	$content .= '</label>';
	$content .= '</div>'; //Column Ends
	
	$content .= '</div>'; //Row Ends 

	/*
		Device Integration ends.
	*/


	$content .= '<div class="grid-x grid-margin-x">';
	$content .= '<div class="cell small-12">';
	$content .= '<label>';
	$content .= esc_html__('Job Details', 'on-the-bench');
	
	$job_details = get_post_meta($post->ID, "_case_detail", true);
	
	$content .= '<textarea name="case_detail" placeholder="'.esc_html__("Enter details about job.", "on-the-bench").'">'.$job_details.'</textarea>';
	$content .= '</label>';
	$content .= '</div>'; //Column Ends
	//Add Reveal Form
	$content .= '</div>'; //Row Ends here. Today
	
	$parts_returned 		= tofw_print_existing_parts($post->ID);
	$tofw_use_taxes 			= get_option("tofw_use_taxes");
	$tofw_primary_tax			= get_option("tofw_primary_tax");

	if(is_parts_switch_woo() == true) {
		// Add Products by WooCommerce if on from Plugin options.
		$content .= "<h3>";
		$content .= esc_html__('Select Products', 'on-the-bench').'</h3>';

		$content .= '<div class="grid-x grid-margin-x">';
		$content .= '<div class="cell small-6">';
		$content .= '<select name="product" id="select_product" data-display_stock="true" data-exclude_type="variable" data-placeholder="'.esc_html__("Search product ...", "on-the-bench").'" data-security="'.wp_create_nonce( 'search-products' ).'" class="bc-product-search"></select>';
		$content .= '</div>'; //Column Ends
		$content .= '<div class="cell small-2">';
		$content .= '<a class="button button-primary button-small" id="addProduct">'.esc_html__("Add Product", "on-the-bench").'</a>';
		$content .= '</div>'; //Column Ends
		$content .= '</div>'; //Row Ends

		$content .= '<div class="grid-x grid-margin-x">';
		$content .= '<div class="cell small-12">';
		$content .= '<div class="products_body_message"></div>';
		$content .= '<table class="grey-bg tofw_table"><thead><tr>';
		$content .= '<th>'.esc_html__('Name', 'on-the-bench').'</th>';
		$content .= '<th>'.esc_html__('SKU', 'on-the-bench').'</th>';
		$content .= '<th>'.esc_html__('Qty', 'on-the-bench').'</th>';
		$content .= '<th>'.esc_html__('Price', 'on-the-bench').'</th>';
	
		if($tofw_use_taxes == "on"):
			$content .= '<th>'.esc_html__('Tax', 'on-the-bench').' (%)</th>';
			$content .= '<th>'.esc_html__('Tax', 'on-the-bench').' ('.$system_currency.')</th>';
		endif;

		$content .= '<th>'.esc_html__('Total', 'on-the-bench').'</th>';
		$content .= '</tr></thead><tbody class="products_body">'; 
		
		$content .= tofw_print_existing_products($post->ID);
		
		$content .= '</tbody></table></div>'; //Column Ends
		
		$content .= '</div>'; //Row Ends
	}

	if(is_parts_switch_woo() == false || !empty($parts_returned)):
		// Add parts section if turned off Woo and its products
		$content .= '<h3>';

		if(is_parts_switch_woo() == true) {
			$content .= esc_html__('Selected Parts', 'on-the-bench').'</h3>';
		} else {
			$content .= esc_html__('Select Parts', 'on-the-bench').'</h3>';

			$content .= '<div class="grid-x grid-margin-x">';
			$content .= '<div class="cell small-6">';
			$content .= tofw_post_select_options("otb_products");
			$content .= '</div>'; //Column Ends
			$content .= '<div class="cell small-2">';
			$content .= '<a class="button button-primary button-small" id="addPart">'.esc_html__("Add Part", "on-the-bench").'</a>';
			$content .= '</div>'; //Column Ends
			$content .= '</div>'; //Row Ends
		}
	
		$content .= '<div class="grid-x grid-margin-x">';
		$content .= '<div class="cell small-12">';
		$content .= '<div class="parts_body_message"></div>';
		$content .= '<table class="grey-bg tofw_table"><thead><tr>';
		$content .= '<th>'.esc_html__('Name', 'on-the-bench').'</th>';
		$content .= '<th>'.esc_html__('Code', 'on-the-bench').'</th>';
		$content .= '<th>'.esc_html__('Capacity', 'on-the-bench').'</th>';
		$content .= '<th>'.esc_html__('Qty', 'on-the-bench').'</th>';
		$content .= '<th>'.esc_html__('Price', 'on-the-bench').'</th>';
	
		if($tofw_use_taxes == "on"):
			$content .= '<th>'.esc_html__('Tax', 'on-the-bench').' (%)</th>';
			$content .= '<th>'.esc_html__('Tax', 'on-the-bench').' ('.$system_currency.')</th>';
		endif;

		$content .= '<th>'.esc_html__('Total', 'on-the-bench').'</th>';
		$content .= '</tr></thead><tbody class="parts_body">'; 
		
		$content .= tofw_print_existing_parts($post->ID);
		
		$content .= '</tbody></table></div>'; //Column Ends
		
		$content .= '</div>'; //Row Ends

	endif; // Enable WooCommerce Taxes	
	
	$content .= '<h3>';
	$content .= esc_html__('Select Services', 'on-the-bench').'</h3>';

	
	$content .= '<div class="grid-x grid-margin-x">';
	
	$content .= '<div class="cell small-6">';
	$content .= tofw_post_select_options("otb_services");
	$content .= '</div>'; //Column Ends
	$content .= '<div class="cell small-2">';
	$content .= '<a class="button button-primary button-small" id="addService">'.esc_html__("Add Service", "on-the-bench").'</a>';
	$content .= '</div>'; //Column Ends
	
	$content .= '</div>'; //Row Ends
	
	$content .= '<div class="grid-x grid-margin-x">';
	
	$content .= '<div class="cell small-12">';
	$content .= '<div class="services_body_message"></div>';
	$content .= '<table class="grey-bg tofw_table"><thead><tr>';
    $content .= '<th>'.esc_html__('Name', 'on-the-bench').'</th>';
    $content .= '<th>'.esc_html__('Service Code', 'on-the-bench').'</th>';
	$content .= '<th>'.esc_html__('Qty', 'on-the-bench').'</th>';
	$content .= '<th>'.esc_html__('Price', 'on-the-bench').'</th>';
	
	if($tofw_use_taxes == "on"):
		$content .= '<th>'.esc_html__('Tax', 'on-the-bench').' (%)</th>';
		$content .= '<th>'.esc_html__('Tax', 'on-the-bench').' ('.$system_currency.')</th>';
	endif;

	$content .= '<th>'.esc_html__('Total', 'on-the-bench').'</th>';
	$content .= '</tr></thead><tbody class="services_body">'; 
  
    $content .= tofw_print_existing_services($post->ID);
	
	$content .= '</tbody></table></div>'; //Column Ends
	
	$content .= '</div>'; //Row Ends
	
	$content .= '<h3>';
	$content .= esc_html__('Other Items ', 'on-the-bench').'<small>e.g Rent etc</small></h3>';

	
	$content .= '<div class="grid-x grid-margin-x">';
	
	$content .= '<div class="cell small-12">';
	$content .= '<a class="button button-primary button-small" id="addExtra">'.esc_html__("Add Field", "on-the-bench").'</a>';
	$content .= '</div>'; //Column Ends
	
	$content .= '</div>'; //Row Ends
	
	$content .= '<div class="grid-x grid-margin-x">';
	
	$content .= '<div class="cell small-12">';
	$content .= '<div class="extra_body_message"></div>';
	$content .= '<table class="grey-bg tofw_table"><thead><tr>';
    $content .= '<th>'.esc_html__('Name', 'on-the-bench').'</th>';
    $content .= '<th>'.esc_html__('Code', 'on-the-bench').'</th>';
	$content .= '<th>'.esc_html__('Qty', 'on-the-bench').'</th>';
	$content .= '<th>'.esc_html__('Price', 'on-the-bench').'</th>';
	
	if($tofw_use_taxes == "on"):
		$content .= '<th>'.esc_html__('Tax', 'on-the-bench').' (%)</th>';
		$content .= '<th>'.esc_html__('Tax', 'on-the-bench').' ('.$system_currency.')</th>';
	endif;

	$content .= '<th>'.esc_html__('Total', 'on-the-bench').'</th>';
	$content .= '</tr></thead><tbody class="extra_body">'; 
  
  	$content .= tofw_print_existing_extras($post->ID);
	
	$content .= '</tbody></table></div>'; //Column Ends
	
	$content .= '</div>'; //Row Ends
	
	echo $content;
}

/**
 * Save infor.
 *
 * @param int $post_id The ID of the post being saved.
 */
function tofw_jobs_features_save_box( $post_id ) {
	// Verify that the nonce is valid.
	if (!isset( $_POST['tofw_jobs_features_sub']) || ! wp_verify_nonce( $_POST['tofw_jobs_features_sub'], 'tofw_meta_box_nonce' )) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( isset( $_POST['post_type'] )) {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}
	
	//CBA: MyActionFlag Begin
	// Toggle Send SMS
	if ( isset($_POST['sndSMS'])) {
        update_option('MyAction', 'Yes');
    } else {
        update_option('MyAction', 'No');
    }
	$MyAction = get_option("MyAction");
	//CBA: MyActionFlag End
	
	global $wpdb;
	
	$old_job_status = get_post_meta($post_id, "_tofw_order_status", true);
	$new_job_status = sanitize_text_field($_POST["tofw_order_status"]);

	//Form PRocessing
	$submission_values = array (
							"customer",
							"technician",
							"delivery_date",
							"location_id",
                            "location_post_id",
                            "device_id",
							"device_post_id",
							"case_number",
							"case_detail",
							"tofw_order_status",
							"tofw_order_note",
							"tofw_job_file",
							"tofw_order_Cntct",
							"tofw_order_SMS",
							"tofw_payment_status"
						);

	foreach($submission_values as $submit_value) {
		$my_value = sanitize_text_field($_POST[$submit_value]);
		update_post_meta($post_id, '_'.$submit_value, $my_value);
		
		if($submit_value == "case_number") {
			$title = $my_value;
			$where = array( 'ID' => $post_id );
			$wpdb->update( $wpdb->posts, array( 'post_title' => $title ), $where );
		}
	}

	$user 			= get_user_by('id', $_POST["customer"]);

	$first_name		= empty($user->first_name)? "" : $user->first_name;
	$last_name 		= empty($user->last_name)? "" : $user->last_name;

	$insert_user 	= $first_name. ' ' .$last_name ;

	update_post_meta($post_id, '_customer_label', $insert_user);
	
	update_post_meta($post_id, '_order_id', $post_id);
	
	$order_status = tofw_return_status_name($_POST["tofw_order_status"]);		
	update_post_meta($post_id, '_tofw_order_status_label', $order_status);
	
	$payment_status = array(
/*CBA*/						"warranty" 	=> esc_html__("Warranty", "on-the-bench"),
/*CBA*/						"pending" 	=> esc_html__("Pending", "on-the-bench"),
							"credit" 	=> esc_html__("Credit", "on-the-bench"),
							"paid" 		=> esc_html__("Paid", "on-the-bench"),
							"partial" 	=> esc_html__("Partially Paid", "on-the-bench")
						);
	update_post_meta($post_id, '_tofw_payment_status_label', $payment_status[$_POST["tofw_payment_status"]]);
	
	//Let's save the data
	
	//Let's delete the data if that already exists. 
	$on_the_bench_items 		= $wpdb->prefix.'tofw_otb_order_items';
	$on_the_bench_items_meta = $wpdb->prefix.'tofw_otb_order_itemmeta';
	
	$select_query 	= "SELECT * FROM `".$on_the_bench_items."` WHERE order_id='".$post_id."'";
	
	$select_results = $wpdb->get_results($select_query);
	
	foreach($select_results as $result) {
		$order_item_id = $result->order_item_id;
		
		$delete_itemmeta_query = "DELETE  FROM `".$on_the_bench_items_meta."` WHERE order_item_id='".$order_item_id."'";
		
		$wpdb->query($delete_itemmeta_query);
	}
	$delete_items = "DELETE FROM `".$on_the_bench_items."` WHERE order_id='".$post_id."'";
	$wpdb->query($delete_items);
	
	//Now we can save the values into DAtabase 
	
	//Get Parts and save to database first.
	if(isset($_POST["tofw_part_id"])):
	for($i = 0; $i < count($_POST["tofw_part_id"]); $i++) {
		$tofw_part_name 		= $_POST["tofw_part_name"][$i];
		
		$tofw_part_id 		= $_POST["tofw_part_id"][$i];
		$tofw_part_code 		= $_POST["tofw_part_code"][$i];
		$tofw_part_capacity 	= $_POST["tofw_part_capacity"][$i];
		$tofw_part_qty 		= $_POST["tofw_part_qty"][$i];
		$tofw_part_price 		= $_POST["tofw_part_price"][$i];
		
		$process_part_array = array(
									"tofw_part_id"		=> $tofw_part_id, 
									"tofw_part_code"		=> $tofw_part_code, 
									"tofw_part_capacity"	=> $tofw_part_capacity, 
									"tofw_part_qty"		=> $tofw_part_qty, 
									"tofw_part_price"		=> $tofw_part_price
								);

		if(isset($_POST["tofw_part_tax"][$i])) {
			$tofw_part_tax = $_POST["tofw_part_tax"][$i];

			$process_part_array["tofw_part_tax"] = $tofw_part_tax;	
		}

		$insert_query =  "INSERT INTO `{$on_the_bench_items}` VALUES(NULL, %s, 'parts', %s)";
		
		$wpdb->query(
				$wpdb->prepare($insert_query, $tofw_part_name, $post_id)
		);
		$order_item_id = $wpdb->insert_id;
		
		foreach($process_part_array as $key => $value) {
			$part_insert_query =  "INSERT INTO `{$on_the_bench_items_meta}` VALUES(NULL, %s, %s, %s)";

			$wpdb->query(
				$wpdb->prepare($part_insert_query, $order_item_id, $key, $value)
			);
		}
	}//Parts Processed nicely
	endif;
	
	//Get Products and Save into Database
	if(isset($_POST["tofw_product_id"])):
		for($i = 0; $i < count($_POST["tofw_product_id"]); $i++) {
			$tofw_product_name 	= $_POST["tofw_product_name"][$i];
			$tofw_product_id 		= $_POST["tofw_product_id"][$i];
			$tofw_product_sku 	= $_POST["tofw_product_sku"][$i];
			$tofw_product_qty 	= $_POST["tofw_product_qty"][$i];
			$tofw_product_price 	= $_POST["tofw_product_price"][$i];
			
			$process_products_array = array(
										"tofw_product_id"		=> $tofw_product_id, 
										"tofw_product_sku"	=> $tofw_product_sku, 
										"tofw_product_qty"	=> $tofw_product_qty, 
										"tofw_product_price"	=> $tofw_product_price
									);
	
			if(isset($_POST["tofw_product_tax"][$i])) {
				$tofw_part_tax = $_POST["tofw_product_tax"][$i];
	
				$process_products_array["tofw_product_tax"] = $tofw_part_tax;	
			}
	
			$insert_query =  "INSERT INTO `{$on_the_bench_items}` VALUES(NULL, %s, 'products', %s)";
			
			$wpdb->query(
					$wpdb->prepare($insert_query, $tofw_product_name, $post_id)
			);
			$order_item_id = $wpdb->insert_id;
			
			foreach($process_products_array as $key => $value) {
				$part_insert_query =  "INSERT INTO `{$on_the_bench_items_meta}` VALUES(NULL, %s, %s, %s)";
	
				$wpdb->query(
					$wpdb->prepare($part_insert_query, $order_item_id, $key, $value)
				);
			}
		}//Parts Processed nicely
		endif;
	
	
	if(isset($_POST["tofw_service_id"])):
	//Get Services and save to database first.
	for($i = 0; $i < count($_POST["tofw_service_id"]); $i++) {
		$tofw_service_id			= $_POST["tofw_service_id"][$i];
		$tofw_service_name 		= $_POST["tofw_service_name"][$i];
		
		$tofw_service_code 		= $_POST["tofw_service_code"][$i];
		$tofw_service_qty 		= $_POST["tofw_service_qty"][$i];
		$tofw_service_price 		= $_POST["tofw_service_price"][$i];
		
		$process_service_array = array(
			"tofw_service_code"	=> $tofw_service_code, 
			"tofw_service_id"		=> $tofw_service_id, 
			"tofw_service_qty"	=> $tofw_service_qty, 
			"tofw_service_price"	=> $tofw_service_price
		);

		if(isset($_POST["tofw_service_tax"][$i])) {
			$tofw_service_tax = $_POST["tofw_service_tax"][$i];

			$process_service_array["tofw_service_tax"] = $tofw_service_tax;	
		}

		$insert_query =  "INSERT INTO `{$on_the_bench_items}` VALUES(NULL, %s, 'services', %s)";
		 $wpdb->query(
				$wpdb->prepare($insert_query, $tofw_service_name, $post_id)
		);
		$order_item_id = $wpdb->insert_id;
		
		foreach($process_service_array as $key => $value) {
			$service_insert_query =  "INSERT INTO `{$on_the_bench_items_meta}` VALUES(NULL, %s, %s, %s)";

			$wpdb->query(
				$wpdb->prepare($service_insert_query, $order_item_id, $key, $value)
			);
		}
	}//Services Processed nicely
	endif;

	if(isset($_POST["tofw_extra_name"])):
	//Get Services and save to database first.
	for($i = 0; $i < count($_POST["tofw_extra_name"]); $i++) {
		$tofw_extra_name 		= $_POST["tofw_extra_name"][$i];
		
		$tofw_extra_code 		= $_POST["tofw_extra_code"][$i];
		$tofw_extra_qty 		= $_POST["tofw_extra_qty"][$i];
		$tofw_extra_price 	= $_POST["tofw_extra_price"][$i];
		
		$process_extra_array = array(
			"tofw_extra_code"		=> $tofw_extra_code, 
			"tofw_extra_qty"		=> $tofw_extra_qty, 
			"tofw_extra_price"	=> $tofw_extra_price
		);

		if(isset($_POST["tofw_extra_tax"][$i])) {
			$tofw_extra_tax = $_POST["tofw_extra_tax"][$i];

			$process_extra_array["tofw_extra_tax"] = $tofw_extra_tax;	
		}

		$insert_query =  "INSERT INTO `{$on_the_bench_items}` VALUES(NULL, %s, 'extras', %s)";
		 $wpdb->query(
				$wpdb->prepare($insert_query, $tofw_extra_name, $post_id)
		);
		$order_item_id = $wpdb->insert_id;
		
		foreach($process_extra_array as $key => $value) {
			$extra_insert_query =  "INSERT INTO `{$on_the_bench_items_meta}` VALUES(NULL, %s, %s, %s)";

			$wpdb->query(
				$wpdb->prepare($extra_insert_query, $order_item_id, $key, $value)
			);
		}

	}//Services Processed nicely
	endif;

	if(($old_job_status != $new_job_status) || empty($old_job_status)) {
		$tofw_send_otb_notice 	= get_option("tofw_job_status_otb_notice");

		if($tofw_send_otb_notice == "on") {
			$_GET["tofw_case_number"] = $_POST["case_number"];

			tofw_otb_send_customer_update_email($post_id);
		}
	}
}
add_action( 'save_post', 'tofw_jobs_features_save_box' );
//Add filter to show Meta Data in front end of post!


/*
*Add meta data to table fields post list.. 
*/
add_filter('manage_edit-otb_jobs_columns', 'tofw_table_list_jobs_type_columns') ;

function tofw_table_list_jobs_type_columns( $columns ) {
	$columns = array(
		'cb' => '<input type="checkbox" />',
		'order_id' 			=> __('Order ID', "on-the-bench"),
		'title' 			=> __('Case Number', "on-the-bench"),
		'customers' 		=> __('Customer', "on-the-bench"),
		'location' 			=> __('Location', "on-the-bench"),
		'device' 			=> __('Device', "on-the-bench"),
		'assigned_to' 		=> __('Assigned To', "on-the-bench"),
		'delivery_date' 	=> __('Delivery Date', "on-the-bench"),
		'invoice_total' 	=> __('Order Total', "on-the-bench"),
		'tofw_order_status' => __('Order Status', "on-the-bench"),
		'tofw_order_Cntct' 	=> __('Job Contact', "on-the-bench"),
		'tofw_order_SMS' 	=> __('Job SMS', "on-the-bench"),
		'tofw_payment_status' => __('Payment Status', "on-the-bench")
	);
	return $columns;
}

add_action( 'manage_otb_jobs_posts_custom_column', 'tofw_table_jobs_list_meta_data', 10, 2 );

function tofw_table_jobs_list_meta_data($column, $post_id) {
	global $post;
	$system_currency 	= get_option('tofw_system_currency');
	
	switch( $column ) {
		case 'order_id' :
			echo "# ".$post_id;
			break;
		case 'customers' :
			$customer 		= get_post_meta($post_id, '_customer', true);
			
			if(!empty($customer)) {
				$user 			= get_user_by('id', $customer);
				$phone_number 	= get_user_meta($customer, "customer_phone", true);
				$company 		= get_user_meta($customer, "company", true);

				$first_name		= empty($user->first_name)? "" : $user->first_name;
				$last_name 		= empty($user->last_name)? "" : $user->last_name;
				echo  $first_name. ' ' .$last_name ;

				if(!empty($phone_number)) {
					echo "<br>".esc_html__("Phone", "on-the-bench").": ".$phone_number;	
				}
				if(!empty($company)) {
					echo "<br>".esc_html__("Company", "on-the-bench").": ".$company;	
				}
			}	
		break;

/*CBA*/	case 'location' :
			$location_id 		= get_post_meta($post_id, '_location_id', true);
			$location_post_id	= get_post_meta($post_id, '_location_post_id', true);

			if(!empty($location_post_id)) {
				echo get_the_title($location_post_id);	
			}
			if(!empty($location_id)) {
				echo "<br>".esc_html__("ID/LCTN", "on-the-bench").": ".esc_html($location_id);	
			}
/*CBA*/	break;

		case 'device' :
			$device_id 		= get_post_meta($post_id, '_device_id', true);
			$device_post_id	= get_post_meta($post_id, '_device_post_id', true);

			if(!empty($device_post_id)) {
				echo get_the_title($device_post_id);	
			}
			if(!empty($device_id)) {
				echo "<br>".esc_html__("ID/IMEI", "on-the-bench").": ".esc_html($device_id);	
			}
		break;
		case 'assigned_to' :
			$technician 	= get_post_meta($post_id, '_technician', true);
			if(!empty($technician)) {
				$tech_user 		= get_user_by('id', $technician);

				echo $tech_user->first_name . ' ' . $tech_user->last_name;
			}	
		break;	
		case 'delivery_date':
			$delivery_date = get_post_meta($post_id, '_delivery_date', true);
			
			if(!empty($delivery_date)) {
				$date_format = get_option( 'date_format' );
				$delivery_date = date_i18n($date_format, strtotime($delivery_date));
				echo esc_html($delivery_date);
			}
		break;
		case 'invoice_total':
			$price = tofw_order_grand_total($post_id, 'grand_total');
			echo $system_currency.$price;
		break;
		case 'tofw_order_status' :
			$tofw_order_status = get_post_meta($post_id, '_tofw_order_status', true);

			$order_statuses = '<select name="tofw_update_order_status" class="update_status" data-post="'.$post_id.'">';

			if(empty($tofw_order_status)) {
				$tofw_order_status = "new";
			}
			$order_statuses .= tofw_generate_status_options($tofw_order_status);
			$order_statuses .= '</select>';

			echo $order_statuses;
			break;
		case 'tofw_payment_status' :
			$tofw_payment_status = get_post_meta($post_id, '_tofw_payment_status', true);
			$payment_status = array(
/*CBA*/						"warranty" 	=> esc_html__("Warranty", "on-the-bench"),
/*CBA*/						"pending" 	=> esc_html__("Pending", "on-the-bench"),
							"credit" 	=> esc_html__("Credit", "on-the-bench"),
							"paid" 		=> esc_html__("Paid", "on-the-bench"),
							"partial" 	=> esc_html__("Partially Paid", "on-the-bench")
		
			);
/*CBA*/		$tofw_payment_status = empty($tofw_payment_status)? "pending" : $tofw_payment_status;
			
			echo $payment_status[$tofw_payment_status];
			break;	
				
//CBA: Order Contact and SMS Begin
		case 'tofw_order_Cntct':
			$tofw_order_Cntct = get_post_meta($post_id, '_tofw_order_Cntct', true);
			echo esc_html($tofw_order_Cntct);
		break;

		case 'tofw_order_SMS':
			$tofw_order_SMS = get_post_meta($post_id, '_tofw_order_SMS', true);
			echo esc_html($tofw_order_SMS);
		break;
			
//CBA: Order Contact and SMS End

		//Break for everything else to show default things.
		default :
			break;
	}
}


if(!function_exists("tofw_extend_jobs_admin_search")):
	function tofw_extend_jobs_admin_search( $query ) {

		// Extend search for document post type
		$post_type = 'otb_jobs';
		// Custom fields to search for
		$custom_fields = array(
			"_order_id",
			"_customer_label",
			"_tofw_order_status_label",
			"_tofw_payment_status_label",
/*CBA*/		"_location_id",
			"_device_id"
		);

		if( ! is_admin() )
			return;

		if ( $query->query['post_type'] != $post_type )
			return;

		$search_term = $query->query_vars['s'];

		// Set to empty, otherwise it won't find anything
		$query->query_vars['s'] = '';

		$query->set('_meta_or_title', $search_term);

		if ( $search_term != '' ) {
			$meta_query = array( 'relation' => 'OR' );

			foreach( $custom_fields as $custom_field ) {
				array_push( $meta_query, array(
					'key' => $custom_field,
					'value' => $search_term,
					'compare' => 'LIKE'
				));
			}
			$query->set( 'meta_query', $meta_query );
		};
	}
	add_action( 'pre_get_posts', 'tofw_extend_jobs_admin_search', 6, 2);

	add_action( 'pre_get_posts', function( $q ) {
		if( $title = $q->get( '_meta_or_title' ) ) {
			add_filter( 'get_meta_sql', function( $sql ) use ( $title ) {
				global $wpdb;

				// Only run once:
				static $nr = 0; 
				if( 0 != $nr++ ) return $sql;

				// Modified WHERE
				$sql['where'] = sprintf(
					" AND ( %s OR %s ) ",
					$wpdb->prepare( "{$wpdb->posts}.post_title like '%%%s%%'", $title),
					mb_substr( $sql['where'], 5, mb_strlen( $sql['where'] ) )
				);

				return $sql;
			}, 12, 1);
		}
	}, 12, 1);
endif;


/**
 * Job Status Filter
 * @since 3.0
 * @return void
 */
if(!function_exists("tofw_filter_jobs_by_status")):
	function tofw_filter_jobs_by_status() {
		global $typenow;
		global $wp_query;

		if ( $typenow == 'otb_jobs' ) { // Your custom post type slug
			
			$current_status = '';
			if( isset( $_GET['tofw_job_status'] ) ) {
				$current_status = $_GET['tofw_job_status']; // Check if option has been selected
			} ?>
			<select name="tofw_job_status" id="tofw_job_status">
				<option value="all" <?php selected( 'all', $current_status); ?>><?php _e( 'Job Status (All)', 'On-The-Bench-plugin' ); ?></option>
			<?php
				echo tofw_generate_status_options($current_status);
			?>
			</select>
            <select id="otb_locations" name="location_post_id">
			<?php
				$location_post_id = (isset($_GET["location_post_id"])) ? esc_attr($_GET["location_post_id"]): "";
				echo tofw_generate_location_options($location_post_id);
			?>	
            <select>
			<select id="otb_devices" name="device_post_id">
			<?php
				$device_post_id = (isset($_GET["device_post_id"])) ? esc_attr($_GET["device_post_id"]): "";
				echo tofw_generate_device_options($device_post_id);
			?>	
			</select>
		<?php 
			//Payment Status Processing
			$payment_status = array(
/*CBA*/			"warranty" 	=> esc_html__("Warranty", "on-the-bench"),
/*CBA*/			"pending" 	=> esc_html__("Pending", "on-the-bench"),
				"credit" 	=> esc_html__("Credit", "on-the-bench"),
				"paid" 		=> esc_html__("Paid", "on-the-bench"),
				"partial" 	=> esc_html__("Partially Paid", "on-the-bench")
			);

			$current_payment_status = '';
			if( isset( $_GET['tofw_payment_status'] ) ) {
				$current_payment_status = $_GET['tofw_payment_status']; // Check if option has been selected
			} ?>
			<select name="tofw_payment_status" id="tofw_payment_status">
				<option value="all" <?php selected( 'all', $current_payment_status); ?>><?php _e( 'Payment Status (All)', 'On-The-Bench-plugin' ); ?></option>
			<?php foreach( $payment_status as $key=>$value ) { ?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $current_payment_status); ?>><?php echo esc_attr( $value ); ?></option>
			<?php } ?>
			</select>	

		<?php
			//By Customer
			$current_job_customer = '';
			if( isset( $_GET['job_customer'] ) ) {
				$current_job_customer = $_GET['job_customer']; // Check if option has been selected
			} 
			
			tofw_users_dropdown( array( 'show_option_all' => esc_html__('Customer (All)', 'on-the-bench'), 'name' => 'job_customer', 'role' => 'customer', 'echo' => 1, 'selected' => $current_job_customer ));

			//By Technician
			$current_job_technician = '';
			if( isset( $_GET['job_technician'] ) ) {
				$current_job_technician = $_GET['job_technician']; // Check if option has been selected
			} 

			wp_dropdown_users( array( 'show_option_all' => esc_html__('Technician (All)', 'on-the-bench'), 'name' => 'job_technician', 'role' => 'technician', 'echo' => 1, 'selected' => $current_job_technician));
		}
	}
	add_action( 'restrict_manage_posts', 'tofw_filter_jobs_by_status');
endif;	

/**
 * Update job status query
 * @since 3.0
 * @return void
 */
if(!function_exists("tofw_filter_jobs_by_status_query")):
	function tofw_filter_jobs_by_status_query( $query ) {
		global $pagenow;
		$type = 'otb_jobs';
		
		if (isset($_GET['post_type']) && 
		$query->query["post_type"] == $type && 
		$_GET["post_type"] == $type &&
		$pagenow=='edit.php') {

			$queryParamsCounter = 0;
			if (isset( $_GET['tofw_job_status']) && $_GET['tofw_job_status'] !='all') {
				$tofw_job_status = $_GET['tofw_job_status'];
				$queryParamsCounter++;
			}

/*CBA*/		if(isset($_GET["location_post_id"]) && !empty($_GET["location_post_id"])) {
				$queryParamsCounter++;
				$location_post_id = $_GET['location_post_id'];
			}

			if(isset($_GET["device_post_id"]) && !empty($_GET["device_post_id"])) {
				$queryParamsCounter++;
				$device_post_id = $_GET['device_post_id'];
			}

			if (isset( $_GET['tofw_payment_status'] ) && $_GET['tofw_payment_status'] !='all') {
				$queryParamsCounter++;
				$tofw_payment_status = $_GET['tofw_payment_status'];
			}

			if (isset( $_GET['job_customer'] ) && $_GET['job_customer'] !='0') {
				$queryParamsCounter++;
				$tofw_job_customer = (int)$_GET['job_customer'];
			}

			if (isset( $_GET['job_technician'] ) && $_GET['job_technician'] !='0') {
				$queryParamsCounter++;
				$tofw_job_technician = (int)$_GET['job_technician'];
			}

			$meta_query = array();

			if ($queryParamsCounter > 1) {
				$meta_query['relation'] = 'AND';
			}

			if (isset($tofw_job_status)) {
				$meta_query[] =	array(
					'key' 		=> '_tofw_order_status',
					'value'    	=> $tofw_job_status,
					'compare' 	=> '=',
					'type'    	=> 'CHAR',  
				);
			}
/*CBA*/		if(isset($location_post_id)) {
				$meta_query[] =	array(
					'key' 		=> '_location_post_id',
					'value'    	=> $location_post_id,
					'compare' 	=> '=',
					'type'    	=> 'NUMERIC',  
				);
			}
			if(isset($device_post_id)) {
				$meta_query[] =	array(
					'key' 		=> '_device_post_id',
					'value'    	=> $device_post_id,
					'compare' 	=> '=',
					'type'    	=> 'NUMERIC',  
				);
			}
			if (isset($tofw_payment_status)) {
				$meta_query[] =	array(
					'key' 		=> '_tofw_payment_status',
					'value'    	=> $tofw_payment_status,
					'compare' 	=> '=',
					'type'    	=> 'CHAR',  
				);
			}
			if(isset($tofw_job_customer)) {
				$meta_query[] =	array(
					'key' 		=> '_customer',
					'value'    	=> $tofw_job_customer,
					'compare' 	=> '=',
					'type'    	=> 'NUMERIC',  
				);
			}
			if(isset($tofw_job_technician)) {
				$meta_query[] =	array(
					'key' 		=> '_technician',
					'value'    	=> $tofw_job_technician,
					'compare' 	=> '=',
					'type'    	=> 'NUMERIC',  
				);
			}
			$query->set( 'meta_query', $meta_query);
		}
	}
	add_filter( 'parse_query', 'tofw_filter_jobs_by_status_query');
endif;	