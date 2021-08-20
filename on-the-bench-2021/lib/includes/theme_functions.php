<?php
	/*
		Function to check if role exists
		
		If does not exist 
		
		Return null on not exists
		
		Return object with capabilities on exists
		
		@since 1.0.0
	*/
	if(!function_exists('tofw_get_role')):
		function tofw_get_role( $role ) {
			return wp_roles()->get_role( $role );
		}
	endif;

	// get taxonomies terms links
	function custom_taxonomies_terms_links($post_id, $post_type){
		$post 		= $post_id;
		$taxonomies = get_object_taxonomies( $post_type, 'objects' );

		$out = array();
		foreach ( $taxonomies as $taxonomy_slug => $taxonomy ){

			$terms = get_the_terms($post_id, $taxonomy_slug );

			if ( !empty( $terms ) ) {
				foreach ( $terms as $term ) {
					$out[] =
					'<a href="'
					.    get_term_link( $term->slug, $taxonomy_slug ) .'">'
					.    $term->name
					. "</a>";
				}
			}
		}
		return implode('', $out );
	}


	//Ajax Script Enque
	if(!function_exists("tofw_ajax_script_enqueue")):
		function tofw_ajax_script_enqueue() {
			wp_enqueue_script( 'ajax_script', plugin_dir_url(__FILE__ ).'../../assets/admin/js/ajax_scripts.js', array('jquery'), '1.0', true );
			wp_localize_script( 'ajax_script', 'ajax_obj', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		 }
		 add_action( 'admin_enqueue_scripts', 'tofw_ajax_script_enqueue' );
	endif;

	if(!function_exists("tofw_post_taxes")):

		function tofw_post_taxes() { 
			global $wpdb;

			$on_the_bench_taxes 		= $wpdb->prefix.'tofw_otb_taxes';

			$form_type 			= strip_tags($_POST["form_type"]);
			$tax_name 			= strip_tags($_POST["tax_name"]);
			$tax_description 	= strip_tags($_POST["tax_description"]);
			$tax_rate 			= strip_tags($_POST["tax_rate"]);
			$tax_status 		= strip_tags($_POST["tax_status"]);	

			if($form_type == "tax_form") {
				//Process form
				if(empty($tax_name)) {
					$message = esc_html__("Tax name required", "on-the-bench");
				} elseif(!is_numeric($tax_rate)) {
					$message = esc_html__("Tax rate is empty or not number", "on-the-bench");
				} else {

					$insert_query =  "INSERT INTO `".$on_the_bench_taxes."` VALUES(NULL, '".$tax_name."', '".$tax_description."', '".$tax_rate."', '".$tax_status."')";
					$wpdb->query(
							$wpdb->prepare($insert_query)
					);

					$tax_id = $wpdb->insert_id;

					$message = esc_html__("You have added tax rate.", "on-the-bench");
				}
			} else {
				$message = esc_html__("Invalid Form", "on-the-bench");	
			}

			$values['message'] = $message;
			$values['success'] = "YES";

			wp_send_json($values);
			wp_die();
		}
		add_action( 'wp_ajax_tofw_post_taxes', 'tofw_post_taxes' );
	endif;
	
	
	/*
		* Add Status Form
		* Ajax Form
	*/
	if(!function_exists("tofw_post_status")):
		function tofw_post_status() { 
			global $wpdb;

			$on_the_bench_job_status = $wpdb->prefix.'tofw_otb_job_status';

			$form_type 			= strip_tags($_POST["form_type"]);
			$status_name 		= strip_tags($_POST["status_name"]);
			$status_slug 		= strip_tags($_POST["status_slug"]);
			$status_description	= strip_tags($_POST["status_description"]);
			$status_status 		= strip_tags($_POST["status_status"]);	
			$status_email_msg 	= strip_tags($_POST["statusEmailMessage"]);

			if(isset($_POST["form_type_status"]) && $_POST["form_type_status"] == "update") {
				if(isset($_POST["status_id"]) && is_numeric($_POST["status_id"])) {
					$update_form = $_POST["status_id"];
				}
			}

			if($form_type == "status_form") {
				//Process form
				if(empty($status_name)) {
					$message = esc_html__("Name required", "on-the-bench");
				} elseif(empty($status_slug)) {
					$message = esc_html__("Slug is required", "on-the-bench");
				} else {

					if(isset($update_form) && is_numeric($update_form)) {
						//Update functionality
						$data 	= array(
							"status_name" 			=> $status_name,
							"status_slug" 			=> $status_slug,
							"status_description" 	=> $status_description,
							"status_email_message" 	=> $status_email_msg,
							"status_status" 		=> $status_status, 
						); 
						$where 	= ['status_id' 	=> $update_form];

						$update_row = $wpdb->update($on_the_bench_job_status, $data, $where);

						$message = esc_html__("You have updated status.", "on-the-bench");
					} else {

						$insert_query =  "INSERT INTO `".$on_the_bench_job_status."` 
						VALUES(NULL, '".$status_name."', '".$status_slug."', '".$status_description."', '".$status_email_msg."', '', '".$status_status."')";
						$wpdb->query(
								$wpdb->prepare($insert_query)
						);

						$status_id = $wpdb->insert_id;

						$message = esc_html__("You have added status.", "on-the-bench");
					}
				}
			} else {
				$message = esc_html__("Invalid Form", "on-the-bench");	
			}

			$values['message'] = $message;
			$values['success'] = "YES";

			wp_send_json($values);
			wp_die();
		}
		add_action('wp_ajax_tofw_post_status', 'tofw_post_status');
	endif;


	/*
	 * WC Update Tax or Status 
	 * 
	 * Helps to update the record
	 */
	if(!function_exists("tofw_update_tax_or_status")) {
		function tofw_update_tax_or_status() {
			global $wpdb;

			$cr_taxes_table 	= $wpdb->prefix.'tofw_otb_taxes';
			$cr_status_table 	= $wpdb->prefix.'tofw_otb_job_status';
		
			if(isset($_POST["recordID"]) && isset($_POST["recordType"])) {

				if($_POST["recordType"] == "tax") {
					$tofw_curr_tax_status	= $wpdb->get_row( "SELECT * FROM {$cr_taxes_table} WHERE `tax_id` = '{$_POST["recordID"]}'" );	
					$curr_status 		= $tofw_curr_tax_status->tax_status;

					if($curr_status == "active") {
						$curr_status = "inactive";
					} else {
						$curr_status = "active";
					}
					$data 	= ['tax_status' => $curr_status]; 
					$where 	= ['tax_id' 	=> $_POST["recordID"]];

					$update_row = $wpdb->update($cr_taxes_table, $data, $where);

					$message = esc_html__("Tax status updated!", "on-the-bench");


				} elseif($_POST["recordType"] == "status") {
					/*
					 * Updating Job Status
					 * Status
					 * In DB by Staus ID
					 */
					$tofw_curr_job_status	= $wpdb->get_row( "SELECT * FROM {$cr_status_table} WHERE `status_id` = '{$_POST["recordID"]}'" );	
					$curr_status 		= $tofw_curr_job_status->status_status;

					if($curr_status == "active") {
						$curr_status = "inactive";
					} else {
						$curr_status = "active";
					}
					$data 	= ['status_status' 	=> $curr_status]; 
					$where 	= ['status_id' 		=> $_POST["recordID"]];

					$update_row = $wpdb->update($cr_status_table, $data, $where);

					$message = esc_html__("Job status updated!", "on-the-bench");

				} elseif($_POST["recordType"] == "inventory_count") {
					/*
					 * Switch inventory counter
					 * For
					 * Products Sold Through CRM
					 */
					$tofw_curr_job_status	= $wpdb->get_row( "SELECT * FROM {$cr_status_table} WHERE `status_id` = '{$_POST["recordID"]}'" );	
					$curr_status 		= $tofw_curr_job_status->inventory_count;

					if(empty($curr_status) || $curr_status == "off") {
						$curr_status = "on";
					} else {
						$curr_status = "off";
					}
					$data 	= ['inventory_count' 	=> $curr_status]; 
					$where 	= ['status_id' 			=> $_POST["recordID"]];

					$update_row = $wpdb->update($cr_status_table, $data, $where);

					$message = esc_html__("Now products would automatically deduct with this status from WOO inventory balance.!", "on-the-bench");
				}

				//$message = esc_html__("Recort Type and Reecord ID missing", "on-the-bench");	
			} else {

				$message = esc_html__("Record updated!", "on-the-bench");	
			}

			$values['message'] = $message;
			$values['success'] = "YES";

			wp_send_json($values);
			wp_die();
		}
		add_action( 'wp_ajax_tofw_update_tax_or_status', 'tofw_update_tax_or_status');
	}

	/*
	 * WC Update Job Status 
	 * 
	 * From Job list page
	 */
	if(!function_exists("tofw_update_job_status")) {
		function tofw_update_job_status() {
			global $wpdb;

			if(isset($_POST["recordID"]) && isset($_POST["orderStatus"])) {

				$tofw_send_otb_notice 	= get_option("tofw_job_status_otb_notice");
				$old_job_status 	= get_post_meta($_POST["recordID"], "_tofw_order_status", true);
				$new_job_status 	= sanitize_text_field($_POST["orderStatus"]);

				if($old_job_status != $new_job_status) {
					if($tofw_send_otb_notice == "on") {
						$_GET["tofw_case_number"] = get_post_meta($_POST["recordID"], "_case_number", true);
						tofw_otb_send_customer_update_email($_POST["recordID"]);
					}
				}
				update_post_meta($_POST["recordID"], "_tofw_order_status", $_POST["orderStatus"]);

				$order_status = tofw_return_status_name($_POST["orderStatus"]);		
				update_post_meta($_POST["recordID"], '_tofw_order_status_label', $order_status);

				$message = esc_html__("Record updated!", "on-the-bench");	
				$values['success'] = "YES";
			} else {
				$message = esc_html__("Order Id or Order Status missing!", "on-the-bench");
				$values['success'] = "NO";
			}

			$values['message'] = $message;

			wp_send_json($values);
			wp_die();
		}
		add_action( 'wp_ajax_tofw_update_job_status', 'tofw_update_job_status');
	}

	if(!function_exists("tofw_post_customer")):
		function tofw_post_customer() {
			//Register User
			$first_name 		= sanitize_text_field($_POST["reg_fname"]);
			$last_name 			= sanitize_text_field($_POST["reg_lname"]);
			$username 			= sanitize_email($_POST["reg_email"]);
			$email 				= sanitize_email($_POST["reg_email"]);
			$customer_phone 	= sanitize_text_field($_POST["customer_phone"]);
			$customer_city 		= sanitize_text_field($_POST["customer_city"]);
			$zip_code 			= sanitize_text_field($_POST["zip_code"]);
			$company 			= ($_POST["customer_company"])? sanitize_text_field($_POST["customer_company"]) : "";
			$customer_address 	= ($_POST["customer_address"])? sanitize_text_field($_POST["customer_address"]) : "";
			$user_role 			= ($_POST["userrole"])? "technician" : "customer";

			if($_POST["userrole"] == "shop_manager") {
				$user_role = "shop_manager";
			}
			
/*CBA*/		if($_POST["userrole"] == "shop_employee") {
				$user_role = "shop_employee";
			}

			$password 	= wp_generate_password(8, false );

			if(!empty($username) && username_exists($username)) {
				$message = esc_html__("Duplicate User", "on-the-bench");
			} elseif(!empty($username) && !validate_username($username)) {
				$message = esc_html__("Not a valid username", "on-the-bench");
			} elseif(!empty($email) && !is_email($email)) {
				$message = esc_html__("Email is not valid", "on-the-bench");
			} elseif(email_exists($email)) {
				$message = esc_html__("Email already in user. Try resetting password if its your Email.", "on-the-bench");
			} elseif(!empty($username) && !empty($email)) {
				//We are all set to Register User.
				$userdata = array(
					'user_login' 	=> $username,
					'user_email' 	=> $email,
					'user_pass' 	=> $password,
					'first_name' 	=> $first_name,
					'last_name' 	=> $last_name,
					'role'			=> $user_role
				);

				//Insert User Data
				$register_user = wp_insert_user($userdata);

				//If Not exists
				if (!is_wp_error($register_user)) {
					//Use user instead of both in case sending notification to only user
					wp_new_user_notification($register_user, null, 'both');
					$message = esc_html__("User account is created logins sent to email.", "on-the-bench");
					$user_id = $register_user;

					if(!empty($user_id)) {
						update_user_meta( $user_id, 'customer_phone', $customer_phone);
						update_user_meta( $user_id, 'customer_address', $customer_address);
						update_user_meta( $user_id, 'customer_city', $customer_city);
						update_user_meta( $user_id, 'zip_code', $zip_code);
						update_user_meta( $user_id, 'company', $company);
					}

				} else {
					$message = '<strong>' . $register_user->get_error_message() . '</strong>';
				}
			}

			$values['message'] = $message;
			$values['success'] = "YES";
			$values['user_id'] = $user_id;

			wp_send_json($values);
			wp_die();
		}
		add_action( 'wp_ajax_tofw_post_customer', 'tofw_post_customer' );
	endif;


	if(!function_exists('tofw_print_existing_parts')): 
		function tofw_print_existing_parts($order_id) {
			global $wpdb;
			
			$tofw_use_taxes 				= get_option("tofw_use_taxes");

			if(isset($_POST["tofw_case_number"]) || isset($_GET["tofw_case_number"])) {
				$print_values = "YES";
			} elseif(isset($_GET["page"]) && $_GET["page"] == "tofw_on_the_bench_print") {
				$print_values = "YES";
			} else {
				$print_values = "NO";
			}

			$on_the_bench_items 		= $wpdb->prefix.'tofw_otb_order_items';
			$on_the_bench_items_meta = $wpdb->prefix.'tofw_otb_order_itemmeta';
			
			$select_items_query = "SELECT * FROM `{$on_the_bench_items}` WHERE `order_id`='{$order_id}' AND `order_item_type`='parts'";
			
			$items_result = $wpdb->get_results($select_items_query);
			
			$content = '';
			
			foreach($items_result as $item) {
				$order_item_id 	 = $item->order_item_id;
				$order_item_name = $item->order_item_name;
				
				$tofw_part_id 		= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_part_id'" );
				$tofw_part_code		= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_part_code'" );
				$tofw_part_capacity	= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_part_capacity'" );
				$tofw_part_qty		= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_part_qty'" );
				$tofw_part_price		= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_part_price'" );
				
				$tofw_part_tax		= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_part_tax'" );

				
				$content .= "<tr class='item-row tofw_part_row'>";
				$content .= "<td class='tofw_part_name'><a class='delme' href='javascript:;' title='Remove row'>X</a>";
				$content .= $order_item_name."<input type='hidden' name='tofw_part_id[]' value='".$tofw_part_id->meta_value."' /><input type='hidden' name='tofw_part_name[]' value='".$order_item_name."'></td>";
				$content .= "<td class='tofw_part_code'>".$tofw_part_code->meta_value."<input type='hidden' name='tofw_part_code[]' value='".$tofw_part_code->meta_value."'></td>";
				$content .= "<td class='tofw_capacity'>".$tofw_part_capacity->meta_value."<input type='hidden' name='tofw_part_capacity[]' value='".$tofw_part_capacity->meta_value."'></td>";
				
				if($print_values == "YES") {
					$content .= "<td class='tofw_qty'>".$tofw_part_qty->meta_value."</td>";
					$content .= "<td class='tofw_price'>".$tofw_part_price->meta_value."</td>";
				} else {
					$content .= "<td class='tofw_qty'><input type='number' class='tofw_validate_number tofw_special_input' name='tofw_part_qty[]' value='".$tofw_part_qty->meta_value."' /></td>";
					$content .= "<td class='tofw_price'><input type='number' step='any' class='tofw_validate_number tofw_special_input' name='tofw_part_price[]' value='".$tofw_part_price->meta_value."' /></td>";
				}	

				$calculate_tax = 0;

				if(!empty($tofw_part_tax) || $tofw_use_taxes == "on") {
					
					if(!empty($tofw_part_tax)) {
						$tax_rate		= $tofw_part_tax->meta_value;
						$tofw_tax_id 		= tofw_return_tax_id($tax_rate);
					} else {
						$tax_rate		= "0";
						$tofw_tax_id 		= "0";
					}
					

					$content .= "<td class='tofw_tax'>";

					if($print_values != "YES") {
						$content .= '<select class="regular-text tofw_part_tax tofw_small_select form-control" name="tofw_part_tax[]">';
						$content .= '<option value="">'.esc_html__("Select tax", "on-the-bench").'</option>';

						$tofw_part_tax_arr = array(
							"tofw_default_tax_value"	=> $tofw_tax_id,
							"value_type"		=> "tax_rate"
						);
						$content .= tofw_generate_tax_options($tofw_part_tax_arr);	
						$content .= '</select>';
					} else {
						$content .= $tax_rate;
					}	
					$content .= "</td>";

					$content .= "<td class='tofw_part_tax_price'>";
					
					$total_price 	= $tofw_part_qty->meta_value*$tofw_part_price->meta_value;

					if(empty($tax_rate)) {
						$tax_rate = 0;
					}

					$calculate_tax 	= ($total_price/100)*$tax_rate;

					$content .= $calculate_tax;
					$content .= "</td>";
				}

				$content .= "<td class='tofw_price_total'>".number_format((($tofw_part_price->meta_value*$tofw_part_qty->meta_value)+$calculate_tax), 2, '.', '')."</td>";
				$content .= "</tr>";
			}
			
			return $content;
			
		}
	endif;

	if(!function_exists('tofw_print_existing_products')): 
		function tofw_print_existing_products($order_id) {
			global $wpdb;
			
			$tofw_use_taxes 				= get_option("tofw_use_taxes");

			if(isset($_POST["tofw_case_number"]) || isset($_GET["tofw_case_number"])) {
				$print_values = "YES";
			} elseif(isset($_GET["page"]) && $_GET["page"] == "tofw_on_the_bench_print") {
				$print_values = "YES";
			} else {
				$print_values = "NO";
			}

			$on_the_bench_items 		= $wpdb->prefix.'tofw_otb_order_items';
			$on_the_bench_items_meta = $wpdb->prefix.'tofw_otb_order_itemmeta';
			
			$select_items_query = "SELECT * FROM `{$on_the_bench_items}` WHERE `order_id`='{$order_id}' AND `order_item_type`='products'";
			
			$items_result = $wpdb->get_results($select_items_query);
			
			$content = '';
			
			foreach($items_result as $item) {
				$order_item_id 	 = $item->order_item_id;
				$order_item_name = $item->order_item_name;
				
				$tofw_product_id 		= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_product_id'" );
				$tofw_product_sku		= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_product_sku'" );
				$tofw_product_qty		= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_product_qty'" );
				$tofw_product_price	= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_product_price'" );
				
				$tofw_product_tax		= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_product_tax'" );

				
				$content .= "<tr class='item-row tofw_product_row'>";

				$content .= '<td class="tofw_product_name">
								<a class="delme" href="javascript:;" title="Remove row">X</a>
								'.$order_item_name.'<input type="hidden" name="tofw_product_id[]" value="'.$tofw_product_id->meta_value.'">
								<input type="hidden" name="tofw_product_name[]" value="'.$order_item_name.'">
							</td>';

				$content .= '<td class="tofw_product_sku">
								'.$tofw_product_sku->meta_value.'
								<input type="hidden" name="tofw_product_sku[]" value="'.$tofw_product_sku->meta_value.'">
							</td>';	

				if($print_values == "YES") {
					$content .= '<td class="tofw_qty">
									'.$tofw_product_qty->meta_value.'
								</td>';
					$content .= '<td class="tofw_price">
									'.$tofw_product_price->meta_value.'
								</td>';
				} else {
					$content .= '<td class="tofw_qty">
									<input type="number" class="tofw_validate_number tofw_special_input" name="tofw_product_qty[]" value="'.$tofw_product_qty->meta_value.'">
								</td>';
					$content .= '<td class="tofw_price">
									<input type="number" step="any" class="tofw_validate_number tofw_special_input" name="tofw_product_price[]" value="'.$tofw_product_price->meta_value.'">
								</td>';
				}	

				$calculate_tax = 0;


				if(!empty($tofw_product_tax) || $tofw_use_taxes == "on") {
					
					if(!empty($tofw_product_tax)) {
						$tax_rate		= $tofw_product_tax->meta_value;
						$tofw_tax_id 		= tofw_return_tax_id($tax_rate);
					} else {
						$tax_rate		= "0";
						$tofw_tax_id 		= "0";
					}
					

					$content .= "<td class='tofw_tax'>";

					if($print_values != "YES") {
						$content .= '<select class="regular-text tofw_part_tax tofw_small_select form-control" name="tofw_product_tax[]">';
						$content .= '<option value="">'.esc_html__("Select tax", "on-the-bench").'</option>';

						$tofw_part_tax_arr = array(
							"tofw_default_tax_value"	=> $tofw_tax_id,
							"value_type"		=> "tax_rate"
						);
						$content .= tofw_generate_tax_options($tofw_part_tax_arr);	
						$content .= '</select>';
					} else {
						$content .= $tax_rate;
					}	
					$content .= "</td>";

					$content .= "<td class='tofw_product_tax_price'>";
					
					$total_price 	= $tofw_product_qty->meta_value*$tofw_product_price->meta_value;

					if(empty($tax_rate)) {
						$tax_rate = 0;
					}
					$calculate_tax 	= ($total_price/100)*$tax_rate;

					$content .= tofw_number_format($calculate_tax);
					$content .= "</td>";
				}

				$content .= "<td class='tofw_product_price_total'>".tofw_number_format((($tofw_product_price->meta_value*$tofw_product_qty->meta_value)+$calculate_tax))."</td>";
				$content .= "</tr>";
			}
			return $content;
		}
	endif; // End Existing Products

	if(!function_exists('tofw_print_existing_services')): 
		function tofw_print_existing_services($order_id) {
			global $wpdb;
			
			$tofw_use_taxes 	= get_option("tofw_use_taxes");

			if(isset($_POST["tofw_case_number"]) || isset($_GET["tofw_case_number"])) {
				$print_values = "YES";
			} elseif(isset($_GET["page"]) && $_GET["page"] == "tofw_on_the_bench_print") {
				$print_values = "YES";
			} else {
				$print_values = "NO";
			}

			$on_the_bench_items 		= $wpdb->prefix.'tofw_otb_order_items';
			$on_the_bench_items_meta = $wpdb->prefix.'tofw_otb_order_itemmeta';
			
			$select_items_query = "SELECT * FROM `{$on_the_bench_items}` WHERE `order_id`='{$order_id}' AND `order_item_type`='services'";
			
			$items_result = $wpdb->get_results($select_items_query);
			
			$content = '';
			
			foreach($items_result as $item) {
				$order_item_id 	 = $item->order_item_id;
				$order_item_name = $item->order_item_name;
				
				$tofw_service_id 		= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_service_id'" );
				$tofw_service_code	= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_service_code'" );
				$tofw_service_qty		= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_service_qty'" );
				$tofw_service_price	= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_service_price'" );
				
				$tofw_service_tax		= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_service_tax'" );

				$content .= "<tr class='item-row tofw_service_row'>";
				$content .= "<td class='tofw_service_name'><a class='delme' href='javascript:;' title='Remove row'>X</a>";
				$content .= $order_item_name."<input type='hidden' name='tofw_service_id[]' value='".$tofw_service_id->meta_value."' /><input type='hidden' name='tofw_service_name[]' value='".$order_item_name."' /></td>";
				$content .= "<td class='tofw_service_code'>".$tofw_service_code->meta_value."<input type='hidden' name='tofw_service_code[]' value='".$tofw_service_code->meta_value."' /></td>";
				if($print_values == "YES") {
					$content .= "<td class='tofw_service_qty'>".$tofw_service_qty->meta_value."</td>";
				} else {
					$content .= "<td class='tofw_service_qty'><input type='number' class='tofw_validate_number tofw_special_input' name='tofw_service_qty[]' value='".$tofw_service_qty->meta_value."' /></td>";
				}	
				
				if($print_values == "YES") {
					$content .= "<td class='tofw_service_price'>".$tofw_service_price->meta_value."</td>";
				} else {
					$content .= "<td class='tofw_service_price'><input type='number' step='any' class='tofw_validate_number tofw_special_input' name='tofw_service_price[]' value='".$tofw_service_price->meta_value."' /></td>";
				}

				$calculate_tax = 0;

				if(!empty($tofw_service_tax) || $tofw_use_taxes == "on") {
					if(!empty($tofw_service_tax)){
						$tax_rate		= $tofw_service_tax->meta_value;
						$tofw_tax_id 		= tofw_return_tax_id($tax_rate);
					} else {
						$tax_rate		= "0";
						$tofw_tax_id 		= "0";
					}
					

					$content .= "<td class='tofw_tax'>";

					if($print_values != "YES") {
						$content .= '<select class="regular-text tofw_service_tax tofw_small_select form-control" name="tofw_service_tax[]">';
						$content .= '<option value="">'.esc_html__("Select tax", "on-the-bench").'</option>';

						$tofw_service_tax_arr = array(
							"tofw_default_tax_value"	=> $tofw_tax_id,
							"value_type"		=> "tax_rate"
						);
						$content .= tofw_generate_tax_options($tofw_service_tax_arr);	
						$content .= '</select>';
					} else {
						$content .= $tax_rate;
					}	
					$content .= "</td>";

					$content .= "<td class='tofw_service_tax_price'>";
					
					$total_price 	= $tofw_service_price->meta_value*$tofw_service_qty->meta_value;

					if(empty($tax_rate)) {
						$tax_rate = 0;
					}

					$calculate_tax 	= ($total_price/100)*$tax_rate;

					$content .= $calculate_tax;
					$content .= "</td>";
				}

				$content .= "<td class='tofw_service_price_total'>".(($tofw_service_price->meta_value*$tofw_service_qty->meta_value)+$calculate_tax)."</td>";
				$content .= "</tr>";
			}
			
			return $content;
			
		}
	endif;

	if(!function_exists('tofw_print_existing_extras')): 
		function tofw_print_existing_extras($order_id) {
			global $wpdb;
			
			$tofw_use_taxes 				= get_option("tofw_use_taxes");

			if(isset($_POST["tofw_case_number"]) || isset($_GET["tofw_case_number"])) {
				$print_values = "YES";
			} elseif(isset($_GET["page"]) && $_GET["page"] == "tofw_on_the_bench_print") {
				$print_values = "YES";
			} else {
				$print_values = "NO";
			}

			$on_the_bench_items 		= $wpdb->prefix.'tofw_otb_order_items';
			$on_the_bench_items_meta = $wpdb->prefix.'tofw_otb_order_itemmeta';
			
			$select_items_query = "SELECT * FROM `{$on_the_bench_items}` WHERE `order_id`='{$order_id}' AND `order_item_type`='extras'";
			
			$items_result = $wpdb->get_results($select_items_query);
			
			$content = '';
			
			foreach($items_result as $item) {
				$order_item_id 	 = $item->order_item_id;
				$order_item_name = $item->order_item_name;
				
				$tofw_extra_code	= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_extra_code'" );
				$tofw_extra_qty	= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_extra_qty'" );
				$tofw_extra_price	= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_extra_price'" );

				$tofw_extra_tax	= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_extra_tax'" );
				
				$content .= "<tr class='item-row tofw_extra_row'>";
				$content .= "<td class='tofw_extra_name'><a class='delme' href='javascript:;' title='Remove row'>X</a>";

				if($print_values == "YES") {
					$content .= $order_item_name."</td>";
				} else {
					$content .= "<input type='text' class='tofw_special_input' name='tofw_extra_name[]' value='".$order_item_name."' placeholder='".esc_html__("Extra name here...", "on-the-bench")."' /></td>";
				}

				if($print_values == "YES") {
					$content .= "<td class='tofw_extra_code'>".$tofw_extra_code->meta_value."</td>";
				} else {
					$content .= "<td class='tofw_extra_code'><input type='text' class='tofw_special_input' name='tofw_extra_code[]' value='".$tofw_extra_code->meta_value."' /></td>";
				}	
				
				if($print_values == "YES") {
					$content .= "<td class='tofw_extra_qty'>".$tofw_extra_qty->meta_value."</td>";
				} else {
					$content .= "<td class='tofw_extra_qty'><input type='number' class='tofw_validate_number tofw_special_input' name='tofw_extra_qty[]' value='".$tofw_extra_qty->meta_value."' /></td>";	
				}
				
				if($print_values == "YES") {
					$content .= "<td class='tofw_extra_price'>".$tofw_extra_price->meta_value."</td>";
				} else {
					$content .= "<td class='tofw_extra_price'><input type='number' step='any' class='tofw_validate_number tofw_special_input' name='tofw_extra_price[]' value='".$tofw_extra_price->meta_value."' /></td>";
				}

				$calculate_tax = 0;

				if(!empty($tofw_extra_tax) || $tofw_use_taxes == "on") {

					if(!empty($tofw_extra_tax)) {
						$tax_rate		= $tofw_extra_tax->meta_value;
						$tofw_tax_id 		= tofw_return_tax_id($tax_rate);	
					} else {
						$tax_rate		= "0";
						$tofw_tax_id 		= "0";
					}

					$content .= "<td class='tofw_tax'>";

					if($print_values != "YES") {
						$content .= '<select class="regular-text tofw_extra_tax tofw_small_select form-control" name="tofw_extra_tax[]">';
						$content .= '<option value="">'.esc_html__("Select tax", "on-the-bench").'</option>';

						$tofw_extra_tax_arr = array(
							"tofw_default_tax_value"	=> $tofw_tax_id,
							"value_type"		=> "tax_rate"
						);
						$content .= tofw_generate_tax_options($tofw_extra_tax_arr);	
						$content .= '</select>';
					} else {
						$content .= $tax_rate;
					}	
					$content .= "</td>";

					$content .= "<td class='tofw_extra_tax_price'>";
					
					$total_price 	= $tofw_extra_price->meta_value*$tofw_extra_qty->meta_value;
					
					if(empty($tax_rate)) {
						$tax_rate = 0;
					}
					
					$calculate_tax 	= ($total_price/100)*$tax_rate;

					$content .= $calculate_tax;
					$content .= "</td>";
				}

				$content .= "<td class='tofw_extra_price_total'>".(($tofw_extra_price->meta_value*$tofw_extra_qty->meta_value)+$calculate_tax)."</td>";
				$content .= "</tr>";
			}
			
			return $content;
		}
	endif;


	if(!function_exists("tofw_update_parts_row")) {
		function tofw_update_parts_row() {
			
			if(!isset($_POST["product"]) || empty($_POST["product"])) {
				$values['row'] = esc_html__('No ID selected', 'on-the-bench');
			} elseif($_POST["product_type"] == "woo" && !empty($_POST["product"])) {
				$product_obj 	= tofw_get_product( $_POST["product"] );

				$product_id 	= $product_obj->get_id();

				$tofw_use_taxes 		= get_option("tofw_use_taxes");
				$tofw_primary_tax		= get_option("tofw_primary_tax");

				$tofw_part_tax_value 	= $tofw_primary_tax;

				$part_name 			= $product_obj->get_name();
				$part_code 			= $product_obj->get_sku();
				$part_price 		= $product_obj->get_price();

				$content = "<tr class='item-row tofw_product_row'>";
				$content .= "<td class='tofw_product_name'><a class='delme' href='javascript:;' title='Remove row'>X</a>";
				$content .= $part_name."<input type='hidden' name='tofw_product_id[]' value='".$product_id."' /><input type='hidden' name='tofw_product_name[]' value='".$part_name."'></td>";
				$content .= "<td class='tofw_product_sku'>".$part_code."<input type='hidden' name='tofw_product_sku[]' value='".$part_code."'></td>";
				$content .= "<td class='tofw_qty'><input type='number' class='tofw_validate_number tofw_special_input' name='tofw_product_qty[]' value='1' /></td>";
				$content .= "<td class='tofw_price'><input type='number' step='any' class='tofw_validate_number tofw_special_input' name='tofw_product_price[]' value='".$part_price."' /></td>";

				if($tofw_use_taxes == "on"):
					$content .= "<td class='tofw_tax'>";
					$content .= '<select class="regular-text tofw_part_tax tofw_small_select form-control" name="tofw_product_tax[]">';
					$content .= '<option value="">'.esc_html__("Select tax", "on-the-bench").'</option>';

					$tofw_part_tax_arr = array(
						"tofw_default_tax_value"	=> $tofw_part_tax_value,
						"value_type"		=> "tax_rate"
					);
					$content .= tofw_generate_tax_options($tofw_part_tax_arr);	
					$content .= '</select>';
					$content .= "</td>";

					$content .= "<td class='tofw_product_tax_price'>";
					
					$tax_rate		= tofw_return_tax_rate($tofw_part_tax_value);
					$calculate_tax 	= ($part_price/100)*$tax_rate;

					$content .= $calculate_tax;
					$content .= "</td>";
				endif;	

				if(!isset($calculate_tax)) { $calculate_tax = 0; }

				$content .= "<td class='tofw_product_price_total'>".($part_price+$calculate_tax)."</td>";
				$content .= "</tr>";
				
				$values['row'] = $content;

			} else {
				$post_obj 		= get_post($_POST['product']);
				$post_id 		= $post_obj->ID;

				$tofw_use_taxes 		= get_option("tofw_use_taxes");
				$tofw_primary_tax		= get_option("tofw_primary_tax");
				$tofw_special_tax 	= get_post_meta( $post_id, '_tofw_use_tax', true );
				
				$tofw_part_tax_value 	= '';

				if(empty($tofw_special_tax)) {
					$tofw_part_tax_value = $tofw_primary_tax;	
				} else {
					$tofw_part_tax_value = $tofw_special_tax;
				}

				$part_name 		= $post_obj->post_title;
				$part_code 		= get_post_meta($post_id, "_stock_code", true);
				$part_capacity 	= get_post_meta($post_id, "_capacity", true);
				$part_price 	= get_post_meta($post_id, "_price", true);
				
				
				$content = "<tr class='item-row tofw_part_row'>";
				$content .= "<td class='tofw_part_name'><a class='delme' href='javascript:;' title='Remove row'>X</a>";
				$content .= $part_name."<input type='hidden' name='tofw_part_id[]' value='".$post_id."' /><input type='hidden' name='tofw_part_name[]' value='".$part_name."'></td>";
				$content .= "<td class='tofw_part_code'>".$part_code."<input type='hidden' name='tofw_part_code[]' value='".$part_code."'></td>";
				$content .= "<td class='tofw_capacity'>".$part_capacity."<input type='hidden' name='tofw_part_capacity[]' value='".$part_capacity."'></td>";
				$content .= "<td class='tofw_qty'><input type='number' class='tofw_validate_number tofw_special_input' name='tofw_part_qty[]' value='1' /></td>";
				$content .= "<td class='tofw_price'><input type='number' step='any' class='tofw_validate_number tofw_special_input' name='tofw_part_price[]' value='".$part_price."' /></td>";

				if($tofw_use_taxes == "on"):
					$content .= "<td class='tofw_tax'>";
					$content .= '<select class="regular-text tofw_part_tax tofw_small_select form-control" name="tofw_part_tax[]">';
					$content .= '<option value="">'.esc_html__("Select tax", "on-the-bench").'</option>';

					$tofw_part_tax_arr = array(
						"tofw_default_tax_value"	=> $tofw_part_tax_value,
						"value_type"		=> "tax_rate"
					);
					$content .= tofw_generate_tax_options($tofw_part_tax_arr);	
					$content .= '</select>';
					$content .= "</td>";

					$content .= "<td class='tofw_part_tax_price'>";
					
					$tax_rate		= tofw_return_tax_rate($tofw_part_tax_value);
					$calculate_tax 	= ($part_price/100)*$tax_rate;

					$content .= $calculate_tax;
					$content .= "</td>";
				endif;	

				if(!isset($calculate_tax)) { $calculate_tax = 0; }

				$content .= "<td class='tofw_price_total'>".($part_price+$calculate_tax)."</td>";
				$content .= "</tr>";
				
				$values['row'] = $content;
			}
			
			wp_send_json($values);
			wp_die();
		}
		add_action( 'wp_ajax_tofw_update_parts_row', 'tofw_update_parts_row' );
	}


	if(!function_exists("tofw_update_services_row")) {
		function tofw_update_services_row() {
			
			if(!isset($_POST["service"]) || empty($_POST["service"])) {
				$values['row'] = 'No ID selected';
			} else {
				$post_obj 		= get_post($_POST['service']);
				$post_id 		= $post_obj->ID;

				$tofw_use_taxes 		= get_option("tofw_use_taxes");
				$tofw_primary_tax		= get_option("tofw_primary_tax");
				$tofw_special_tax 	= get_post_meta( $post_id, '_tofw_use_tax', true );
				
				$tofw_service_tax_value 	= '';

				if(empty($tofw_special_tax)) {
					$tofw_service_tax_value = $tofw_primary_tax;	
				} else {
					$tofw_service_tax_value = $tofw_special_tax;
				}
				
				$service_name 	= $post_obj->post_title;
				$service_code  	= get_post_meta($post_id, "_service_code", true);
				$service_price 	= get_post_meta($post_id, "_cost", true);
				
				
				$content = "<tr class='item-row tofw_service_row'>";
				$content .= "<td class='tofw_service_name'><a class='delme' href='javascript:;' title='Remove row'>X</a>";
				$content .= $service_name."<input type='hidden' name='tofw_service_id[]' value='".$post_id."' /><input type='hidden' name='tofw_service_name[]' value='".$service_name."' /></td>";
				$content .= "<td class='tofw_service_code'>".$service_code."<input type='hidden' name='tofw_service_code[]' value='".$service_code."' /></td>";
				$content .= "<td class='tofw_service_qty'><input type='number' class='tofw_validate_number tofw_special_input' name='tofw_service_qty[]' value='1' /></td>";
				$content .= "<td class='tofw_service_price'><input type='number' step='any' class='tofw_validate_number tofw_special_input' name='tofw_service_price[]' value='".$service_price."' /></td>";

				if($tofw_use_taxes == "on"):
					$content .= "<td class='tofw_tax'>";
					$content .= '<select class="regular-text tofw_service_tax tofw_small_select form-control" name="tofw_service_tax[]">';
					$content .= '<option value="">'.esc_html__("Select tax", "on-the-bench").'</option>';

					$tofw_service_tax_arr = array(
						"tofw_default_tax_value"	=> $tofw_service_tax_value,
						"value_type"		=> "tax_rate"
					);
					$content .= tofw_generate_tax_options($tofw_service_tax_arr);	
					$content .= '</select>';
					$content .= "</td>";

					$content .= "<td class='tofw_service_tax_price'>";
					
					$tax_rate		= tofw_return_tax_rate($tofw_service_tax_value);
					$calculate_tax 	= ($service_price/100)*$tax_rate;

					$content .= $calculate_tax;
					$content .= "</td>";
				endif;	

				if(!isset($calculate_tax)) { $calculate_tax = 0; }

				$content .= "<td class='tofw_service_price_total'>".($service_price+$calculate_tax)."</td>";
				$content .= "</tr>";
				
				$values['row'] = $content;
			}
			
			wp_send_json($values);
			wp_die();
		}
		add_action( 'wp_ajax_tofw_update_services_row', 'tofw_update_services_row' );
	}

	if(!function_exists("tofw_update_extra_row")) {
		function tofw_update_extra_row() {
			
			if(!isset($_POST["extra"]) || empty($_POST["extra"])) {
				$values['row'] = esc_html__('No ID selected', "on-the-bench");
			} else {
				$tofw_use_taxes 		= get_option("tofw_use_taxes");
				$tofw_primary_tax		= get_option("tofw_primary_tax");
								
				$tofw_extra_tax_value 	= $tofw_primary_tax;

				$content = "<tr class='item-row tofw_extra_row'>";
				$content .= "<td class='tofw_extra_name'><a class='delme' href='javascript:;' title='Remove row'>X</a>";
				$content .= "<input type='text' class='tofw_special_input' name='tofw_extra_name[]' value='' placeholder='".esc_html__("Extra name here...", "on-the-bench")."' /></td>";
				$content .= "<td class='tofw_extra_code'><input type='text' class='tofw_special_input' name='tofw_extra_code[]' value='' /></td>";
				$content .= "<td class='tofw_extra_qty'><input type='number' class='tofw_validate_number tofw_special_input' name='tofw_extra_qty[]' value='0' /></td>";
				$content .= "<td class='tofw_extra_price'><input type='number' step='any' class='tofw_validate_number tofw_special_input' name='tofw_extra_price[]' value='' /></td>";

				if($tofw_use_taxes == "on"):
					$content .= "<td class='tofw_tax'>";
					$content .= '<select class="regular-text tofw_extra_tax tofw_small_select form-control" name="tofw_extra_tax[]">';
					$content .= '<option value="">'.esc_html__("Select tax", "on-the-bench").'</option>';

					$tofw_part_tax_arr = array(
						"tofw_default_tax_value"	=> $tofw_extra_tax_value,
						"value_type"		=> "tax_rate"
					);
					$content .= tofw_generate_tax_options($tofw_part_tax_arr);	
					$content .= '</select>';
					$content .= "</td>";

					$content .= "<td class='tofw_extra_tax_price'>";
					$content .= "0";
					$content .= "</td>";
				endif;

				$content .= "<td class='tofw_extra_price_total'>0</td>";
				$content .= "</tr>";
				
				$values['row'] = $content;
			}
			
			wp_send_json($values);
			wp_die();
		}
		add_action( 'wp_ajax_tofw_update_extra_row', 'tofw_update_extra_row');
	}


	function tofw_add_user_form() {
	?>
	<!-- Modal for Post Entry /-->
	<div class="small reveal" id="customerFormReveal" data-reveal>
		<h2><?php echo esc_html__("Add a new customer", "on-the-bench"); ?></h2>

		<div class="form-message"></div>

		<form data-async data-abide class="needs-validation" novalidate method="post">
			<div class="grid-x grid-margin-x">
				<div class="cell">
					<div data-abide-error class="alert callout" style="display: none;">
						<p><i class="fi-alert"></i> <?php echo esc_html__("There are some errors in your form.", "on-the-bench"); ?></p>
					</div>
				</div>
			</div>

			<!-- Login Form Starts /-->
			<div class="grid-x grid-margin-x">

				<div class="cell medium-6">
					<label><?php echo esc_html__("First Name", "on-the-bench"); ?>*
						<input name="reg_fname" type="text" class="form-control login-field"
							   value="" required id="reg-fname"/>
						<span class="form-error">
							<?php echo esc_html__("First Name Is Required.", "on-the-bench"); ?>
						</span>
					</label>
				</div>

				<div class="cell medium-6">
					<label><?php echo esc_html__("Last Name", "on-the-bench"); ?>
						<input name="reg_lname" type="text" class="form-control login-field"
							   value="" id="reg-lname"/>
					</label>
				</div>

			</div>

			<div class="grid-x grid-margin-x">

				<div class="cell medium-6">
					<label><?php echo esc_html__("Email", "on-the-bench"); ?>*
						<input name="reg_email" type="email" class="form-control login-field"
							   value="" id="reg-email" required/>
						<span class="form-error">
							<?php echo esc_html__("Email Is Required.", "on-the-bench"); ?>
						</span>
					</label>
				</div>

				<div class="cell medium-6">
					<label><?php echo esc_html__("Phone Number", "on-the-bench"); ?>
						<input name="customer_phone" type="text" class="form-control login-field"
							value="" id="customer_phone" />
					</label>
				</div>

			</div>
			<!-- Login Form Ends /-->

			<div class="grid-x grid-margin-x">

				<div class="cell medium-6">
					<label><?php echo esc_html__("City", "on-the-bench"); ?>
						<input name="customer_city" type="text" class="form-control login-field"
							value="" id="customer_city" />
					</label>
				</div>

				<div class="cell medium-6">
					<label><?php echo esc_html__("Postal Code", "on-the-bench"); ?>
						<input name="zip_code" type="text" class="form-control login-field"
							value="" id="zip_code" />
					</label>
				</div>

			</div>
			<!-- Login Form Ends /-->

			<div class="grid-x grid-margin-x">
				<div class="cell medium-6">
					<label><?php echo esc_html__("Company", "on-the-bench"); ?>
						<input name="customer_company" type="text" class="form-control login-field"
							value="" id="customer_company" />
					</label>
				</div>

				<div class="cell medium-6">
					<label><?php echo esc_html__("Address", "on-the-bench"); ?>
						<input name="customer_address" type="text" class="form-control login-field"
							value="" id="customer_address" />
					</label>
				</div>

			</div>

			<div class="grid-x grid-margin-x">
				<fieldset class="cell medium-6">
					<button class="button" type="submit" value="Submit"><?php echo esc_html__("Add Customer", "on-the-bench"); ?></button>
				</fieldset>
				<small>
					<?php echo esc_html__("(*) fields are required", "on-the-bench"); ?>
				</small>	
			</div>
		</form>

		<button class="close-button" data-close aria-label="Close modal" type="button">
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
	<?php
	}

	/*
		* Update User Data
		*
		* Accepts only Technician and Customer user types.
		* Returns Success or Failure
	*/
	if(!function_exists("tofw_update_user_data")) {
		function tofw_update_user_data() {
			if(isset($_POST["update_user"]) && !empty($_POST["update_user"])) {
				$user_id = $_POST["update_user"];	
			} else {
				return;
			}

			$user_fields = array(
				'ID'           => $user_id,
				'first_name'   => esc_attr($_POST["reg_fname"]),
				'last_name'    => esc_attr($_POST["reg_lname"]),
				'user_email'   => esc_attr($_POST["reg_email"]),
			);
			
			$user_data = wp_update_user($user_fields);

			if ( is_wp_error($user_data) ) {
				// There was an error; possibly this user doesn't exist.
				$message = "Error ".$user_data->get_error_message();
			} else {
				// Success!
				$customer_phone 	= esc_attr($_POST["customer_phone"]);
				$customer_city 		= esc_attr($_POST["customer_city"]);
				$zip_code 			= esc_attr($_POST["zip_code"]);
				$company 			= ($_POST["customer_company"])? $_POST["customer_company"] : "";
				$customer_address 	= ($_POST["customer_address"])? $_POST["customer_address"] : "";

				$company 			= esc_attr($company);
				$customer_address	= esc_attr($customer_address);

				$update_type 		= ($_POST["update_type"])? "technician" : "customer";

				update_user_meta( $user_id, 'customer_phone', $customer_phone);
				update_user_meta( $user_id, 'customer_address', $customer_address);
				update_user_meta( $user_id, 'customer_city', $customer_city);
				update_user_meta( $user_id, 'zip_code', $zip_code);
				update_user_meta( $user_id, 'company', $company);


				$message = esc_html__("User Updated!", "on-the-bench");
			}

			$values['message'] = $message;
			$values['success'] = "YES";

			wp_send_json($values);
			wp_die();
		}
		add_action( 'wp_ajax_tofw_update_user_data', 'tofw_update_user_data' );
	}


	/*
		* Generate Reveal Form
		*
		* Form Handles User update.
	*/
	if(!function_exists("tofw_update_user_form")):
		function tofw_update_user_form() {

			if(isset($_GET["page"])) {
				if($_GET["page"] == "tofw-on-the-2-bench-clients") {
					$update_label 	= esc_html__("Customer", "on-the-bench");
					$update_role	= "customer";
				}elseif($_GET["page"] == "tofw-on-the-2-bench-managers") {
					$update_label 	= esc_html__("Shop Manager", "on-the-bench");
					$update_role	= "shop_manager";	
/*CBA*/			}elseif($_GET["page"] == "tofw-on-the-2-bench-employees") {
					$update_label 	= esc_html__("Shop Employee", "on-the-bench");
					$update_role	= "shop_employee";	
				} else {
					$update_label 	= esc_html__("Technician", "on-the-bench");
					$update_role	= "technician";
				}	
			} else {
				return;
			}

			if(isset($_GET["update_user"])) {
				$user_id 		= 	$_GET["update_user"];

				$user 			= get_user_by('ID', $user_id);
				
				if($user) {
					$user_role		= $user->roles;
					
					if ( in_array( $update_role, (array) $user_role ) ) {
						//The user has the "author" role
						$first_name		= $user->first_name;
						$last_name		= $user->last_name;
						$user_email		= $user->user_email;

						$phone_number 	= get_user_meta($user_id, "customer_phone", true);
						$company 		= get_user_meta($user_id, "company", true);
						$address 		= get_user_meta($user_id, "customer_address", true);
						$city 			= get_user_meta($user_id, "customer_city", true);
						$zip_code 		= get_user_meta($user_id, "zip_code", true);
					}

				} else { return; }	

			} else { return; }
			?>

			<!-- Modal for Post Entry /-->
			<div class="small reveal" id="updateUserFormReveal" data-reveal="active">
				<h2><?php echo esc_html__("Update", "on-the-bench")." ".$update_label; ?></h2>
				<div class="form-message"></div>
		
				<form data-async data-abide class="needs-validation" novalidate method="post">
					<div class="grid-x grid-margin-x">
						<div class="cell">
							<div data-abide-error class="alert callout" style="display: none;">
								<p><i class="fi-alert"></i> <?php echo esc_html__("There are some errors in your form.", "on-the-bench"); ?></p>
							</div>
						</div>
					</div>
		
					<!-- Login Form Starts /-->
					<div class="grid-x grid-margin-x">
		
						<div class="cell medium-6">
							<label><?php echo esc_html__("First Name", "on-the-bench"); ?>*
								<input name="reg_fname" type="text" class="form-control login-field"
									value="<?php echo esc_html($first_name); ?>" required id="reg-fname"/>
								<span class="form-error">
									<?php echo esc_html__("First Name Is Required.", "on-the-bench"); ?>
								</span>
							</label>
						</div>
		
						<div class="cell medium-6">
							<label><?php echo esc_html__("Last Name", "on-the-bench"); ?>
								<input name="reg_lname" type="text" class="form-control login-field"
									value="<?php echo esc_html($last_name); ?>" id="reg-lname"/>
							</label>
						</div>
		
					</div>
		
					<div class="grid-x grid-margin-x">
		
						<div class="cell medium-6">
							<label><?php echo esc_html__("Email", "on-the-bench"); ?>*
								<input name="reg_email" type="email" class="form-control login-field"
									value="<?php echo esc_html($user_email); ?>" id="reg-email" required/>
								<span class="form-error">
									<?php echo esc_html__("Email Is Required.", "on-the-bench"); ?>
								</span>
							</label>
						</div>
		
						<div class="cell medium-6">
							<label><?php echo esc_html__("Phone Number", "on-the-bench"); ?>
								<input name="customer_phone" type="text" class="form-control login-field"
									value="<?php echo esc_html($phone_number); ?>" id="customer_phone" />
							</label>
						</div>
		
					</div>
					<!-- Login Form Ends /-->
		
					<div class="grid-x grid-margin-x">
		
						<div class="cell medium-6">
							<label><?php echo esc_html__("City", "on-the-bench"); ?>
								<input name="customer_city" type="text" class="form-control login-field"
									value="<?php echo esc_html($city); ?>" id="customer_city" />
							</label>
						</div>
		
						<div class="cell medium-6">
							<label><?php echo esc_html__("Postal Code", "on-the-bench"); ?>
								<input name="zip_code" type="text" class="form-control login-field"
									value="<?php echo esc_html($zip_code); ?>" id="zip_code" />
							</label>
						</div>
		
					</div>
					<!-- Login Form Ends /-->
		
					<div class="grid-x grid-margin-x">
						
						<div class="cell medium-6">
							<label><?php echo esc_html__("Company", "on-the-bench"); ?>
								<input name="customer_company" type="text" class="form-control login-field"
									value="<?php echo esc_html($company); ?>" id="customer_company" />
							</label>
						</div>
		
						<div class="cell medium-6">
							<label><?php echo esc_html__("Address", "on-the-bench"); ?>
								<input name="customer_address" type="text" class="form-control login-field"
									value="<?php echo esc_html($address); ?>" id="customer_address" />
							</label>
						</div>
		
					</div>
					<input type="hidden" name="form_type" value="update_user" />
					<input type="hidden" name="update_type" value="<?php echo esc_html($update_role); ?>" />
					<input type="hidden" name="update_user" value="<?php echo esc_html($user_id); ?>" />
					<div class="grid-x grid-margin-x">
						<fieldset class="cell medium-6">
							<button class="button" type="submit" value="Submit"><?php echo esc_html__("Update ", "on-the-bench").esc_html($update_role); ?></button>
						</fieldset>
						<small>
							<?php echo esc_html__("(*) fields are required", "on-the-bench"); ?>
						</small>	
					</div>
				</form>
		
				<button class="close-button" data-close aria-label="Close modal" type="button">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<?php
			}
		endif;

	/***
	 * Add Technician Form Modal into Footer
	 * 
	*/
	function tofw_add_technician_form() {
		?>
		<!-- Modal for Post Entry /-->
		<div class="small reveal" id="technicianFormReveal" data-reveal>
			<h2><?php echo esc_html__("Add a new technician", "on-the-bench"); ?></h2>
	
			<div class="form-message"></div>
	
			<form data-async data-abide class="needs-validation" novalidate method="post">
				<div class="grid-x grid-margin-x">
					<div class="cell">
						<div data-abide-error class="alert callout" style="display: none;">
							<p><i class="fi-alert"></i> <?php echo esc_html__("There are some errors in your form.", "on-the-bench"); ?></p>
						</div>
					</div>
				</div>
	
				<!-- Login Form Starts /-->
				<div class="grid-x grid-margin-x">
	
					<div class="cell medium-6">
						<label><?php echo esc_html__("First Name", "on-the-bench"); ?>*
							<input name="reg_fname" type="text" class="form-control login-field"
								   value="" required id="reg-fname"/>
							<span class="form-error">
								<?php echo esc_html__("First Name Is Required.", "on-the-bench"); ?>
							</span>
						</label>
					</div>
	
					<div class="cell medium-6">
						<label><?php echo esc_html__("Last Name", "on-the-bench"); ?>
							<input name="reg_lname" type="text" class="form-control login-field"
								   value="" id="reg-lname"/>
						</label>
					</div>
	
				</div>
	
				<div class="grid-x grid-margin-x">
	
					<div class="cell medium-6">
						<label><?php echo esc_html__("Email", "on-the-bench"); ?>*
							<input name="reg_email" type="email" class="form-control login-field"
								   value="" id="reg-email" required/>
							<span class="form-error">
								<?php echo esc_html__("Email Is Required.", "on-the-bench"); ?>
							</span>
						</label>
					</div>
	
					<div class="cell medium-6">
						<label><?php echo esc_html__("Phone Number", "on-the-bench"); ?>
							<input name="customer_phone" type="text" class="form-control login-field"
								value="" id="customer_phone" />
						</label>
					</div>
	
				</div>
				<!-- Login Form Ends /-->
	
				<div class="grid-x grid-margin-x">
	
					<div class="cell medium-6">
						<label><?php echo esc_html__("City", "on-the-bench"); ?>
							<input name="customer_city" type="text" class="form-control login-field"
								value="" id="customer_city" />
						</label>
					</div>
	
					<div class="cell medium-6">
						<label><?php echo esc_html__("Postal Code", "on-the-bench"); ?>
							<input name="zip_code" type="text" class="form-control login-field"
								value="" id="zip_code" />
						</label>
					</div>
	
				</div>
				<!-- Login Form Ends /-->
				<input name="userrole" type="hidden" 
								value="technician" />
	
				<div class="grid-x grid-margin-x">
					<fieldset class="cell medium-6">
						<button class="button" type="submit"><?php echo esc_html__("Add Technician", "on-the-bench"); ?></button>
					</fieldset>
					<small>
						<?php echo esc_html__("(*) fields are required", "on-the-bench"); ?>
					</small>	
				</div>
			</form>
	
			<button class="close-button" data-close aria-label="Close modal" type="button">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php
		}


	/***
	 * @since 2.5
	 * 
	 * Adds Tax form in footer.
	*/
	function tofw_add_tax_form() {
	?>
		<!-- Modal for Post Entry /-->
		<div class="small reveal" id="taxFormReveal" data-reveal>
			<h2><?php echo esc_html__("Add new tax", "on-the-bench"); ?></h2>
	
			<div class="form-message"></div>
	
			<form data-async data-abide class="needs-validation" name="tax_form_sync" novalidate method="post">
				<div class="grid-x grid-margin-x">
					<div class="cell">
						<div data-abide-error class="alert callout" style="display: none;">
							<p><i class="fi-alert"></i> There are some errors in your form.</p>
						</div>
					</div>
				</div>
	
				<!-- Login Form Starts /-->
				<div class="grid-x grid-margin-x">
	
					<div class="cell medium-6">
						<label><?php echo esc_html__("Tax Name", "on-the-bench"); ?>*
							<input name="tax_name" type="text" class="form-control login-field"
								   value="" required id="tax_name"/>
							<span class="form-error">
								<?php echo esc_html__("Name of tax to recognize.", "on-the-bench"); ?>
							</span>
						</label>
					</div>
	
					<div class="cell medium-6">
						<label><?php echo esc_html__("Tax Description", "on-the-bench"); ?>
							<input name="tax_description" type="text" class="form-control login-field"
								   value="" id="tax_description"/>
						</label>
					</div>
	
				</div>
	
				<div class="grid-x grid-margin-x">
	
					<div class="cell medium-6">
						<label><?php echo esc_html__("Tax Rate", "on-the-bench"); ?>*
							<input name="tax_rate" type="number" class="form-control login-field"
								   value="" id="tax_rate" required/>
							<span class="form-error" style="display:block;">
								<?php echo esc_html__("Only numbers like 15 for 15% , 0 for 0%, 25 for 25%.", "on-the-bench"); ?>
							</span>
						</label>
					</div>
	
					<div class="cell medium-6">
						<label><?php echo esc_html__("Tax Status", "on-the-bench"); ?>
							<select class="form-control" name="tax_status">
								<option value="active"><?php echo esc_html__("Active", "on-the-bench"); ?>
								<option value="inactive"><?php echo esc_html__("Inactive", "on-the-bench"); ?>
							</select>
						</label>
					</div>
	
				</div>
				<!-- Login Form Ends /-->
	
				<!-- Login Form Ends /-->
				<input name="form_type" type="hidden" 
								value="tax_form" />
	
				<div class="grid-x grid-margin-x">
					<fieldset class="cell medium-6">
						<button class="button" type="submit"><?php echo esc_html__("Add Tax", "on-the-bench"); ?></button>
					</fieldset>
					<small>
						<?php echo esc_html__("(*) fields are required", "on-the-bench"); ?>
					</small>	
				</div>
			</form>
	
			<button class="close-button" data-close aria-label="Close modal" type="button">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php
		}


		/***
		 * @since 3.2
		 * 
		 * Adds Post Status form in footer.
		*/
		function tofw_add_status_form() {
			$status_name = $status_slug = $status_description = $status_email_message = "";
			$button_label = $modal_label = esc_html__("Add new", "on-the-bench");

			if(isset($_GET["update_status"]) && !empty($_GET["update_status"])):
				global $wpdb;

				$update_status = $_GET["update_status"];
				$button_label = $modal_label = esc_html__("Update", "on-the-bench");

				$status_id = esc_attr($_GET["update_status"]);

				$on_the_bench_job_status 	= $wpdb->prefix.'tofw_otb_job_status';
				$tofw_status_row					= $wpdb->get_row( "SELECT * FROM {$on_the_bench_job_status} WHERE `status_id` = '{$status_id}'" );
				
				$status_name 			= $tofw_status_row->status_name;
				$status_slug 			= $tofw_status_row->status_slug;
				$status_description		= $tofw_status_row->status_description;
				$status_status			= $tofw_status_row->status_status;
				$status_email_message 	= $tofw_status_row->status_email_message;
			endif;
			?>
			<!-- Modal for Post Entry /-->
			<div class="small reveal" id="statusFormReveal" data-reveal>
				<h2><?php echo esc_html($modal_label)." ".esc_html__("Status", "on-the-bench"); ?></h2>
		
				<div class="form-message"></div>
		
				<form data-async data-abide class="needs-validation" name="status_form_sync" novalidate method="post">
					<div class="grid-x grid-margin-x">
						<div class="cell">
							<div data-abide-error class="alert callout" style="display: none;">
								<p><i class="fi-alert"></i> <?php echo esc_html__("There are some errors in your form.", "on-the-bench"); ?></p>
							</div>
						</div>
					</div>
		
					<!-- Login Form Starts /-->
					<div class="grid-x grid-margin-x">
		
						<div class="cell medium-6">
							<label><?php echo esc_html__("Status Name", "on-the-bench"); ?>*
								<input name="status_name" type="text" class="form-control login-field"
									   value="<?=esc_html($status_name);?>" required id="status_name"/>
								<span class="form-error">
									<?php echo esc_html__("Name the status to recognize.", "on-the-bench"); ?>
								</span>
							</label>
						</div>
		
						<div class="cell medium-6">
							<label><?php echo esc_html__("Status Slug", "on-the-bench"); ?>*
								<input name="status_slug" type="text" class="form-control login-field"
									   value="<?=esc_html($status_slug);?>" required id="status_slug"/>
								<span class="form-error">
									<?php echo esc_html__("Slug is required to recognize the status make sure to not change it.", "on-the-bench"); ?>
								</span>	   
							</label>
						</div>
		
					</div>
		
					<div class="grid-x grid-margin-x">
		
						<div class="cell medium-6">
							<label><?php echo esc_html__("Description", "on-the-bench"); ?>
								<input name="status_description" type="text" class="form-control login-field"
									   value="<?=esc_html($status_description);?>" id="status_description" />
							</label>
						</div>
		
						<div class="cell medium-6">
							<label><?php echo esc_html__("Status", "on-the-bench"); ?>
								<select class="form-control" name="status_status">
									<option <?=($status_status == "active")? "selected" : ""?> value="active"><?php echo esc_html__("Active", "on-the-bench"); ?>
									<option <?=($status_status == "inactive")? "selected" : ""?> value="inactive"><?php echo esc_html__("Inactive", "on-the-bench"); ?>
								</select>
							</label>
						</div>
		
					</div>

					<div class="grid-x grid-margin-x">
		
						<div class="cell medium-12">
							<label><?php echo esc_html__("Status Email Message", "on-the-bench"); ?>
								<textarea rows="5" placeholder="<?php echo esc_html__("This message would be sent when a job status is changed to this.", "on-the-bench"); ?>" name="statusEmailMessage"><?=esc_html($status_email_message);?></textarea>
							</label>
						</div>
		
					</div>
					<!-- Login Form Ends /-->
		
					<!-- Login Form Ends /-->
					<input name="form_type" type="hidden" 
									value="status_form" />

					<?php if(!empty($update_status)): ?>
						<input name="form_type_status" type="hidden" value="update" />
						<input name="status_id" type="hidden" value="<?=esc_html($update_status);?>" />
					<?php else: ?>
						<input name="form_type_status" type="hidden" value="add" />
					<?php endif; ?>

					<div class="grid-x grid-margin-x">
						<fieldset class="cell medium-6">
							<button class="button" type="submit"><?=esc_html($button_label);?></button>
						</fieldset>
						<small>
							<?php echo esc_html__("(*) fields are required", "on-the-bench"); ?>
						</small>	
					</div>
				</form>
		
				<button class="close-button" data-close aria-label="Close modal" type="button">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<?php
				if(!empty($update_status)) {
					echo "<div id='updateStatus'></div>";
				}
			}
	

	/*
		Generate Select options
		
		Accepts Post Type
		
		return select field
	*/	
	if(!function_exists("tofw_post_select_options")):
		function tofw_post_select_options($post_type) {

			$brand_args = array(
							'post_type' 		=> $post_type,
							'orderby'			=> 'title',
							'order' 			=> 'ASC',
							'posts_per_page' 	=> -1,
						);

			$brand_query = new WP_Query($brand_args);
			
			$select_id = $post_type;
			
			$tofw_options = '<select name="'.$post_type.'" id="select_'.$select_id.'">';
			$tofw_options .= '<option value="">----</option>';

			if ($brand_query->have_posts() ) { 
				while($brand_query->have_posts()) {
					$brand_query->the_post();

					$brand_id 		= $brand_query->post->ID;
					$brand_title 	= get_the_title(); 
					
					$extra_field = '';
					
					if($post_type == "otb_products") {
						$_stock_code = get_post_meta($brand_id, "_stock_code", true);
						
						if(!empty($_stock_code)) {
							$extra_field = $_stock_code.' | ';	
						}
					} elseif($post_type == "otb_services") {
						$_service_code = get_post_meta($brand_id, "_service_code", true);
						
						if(!empty($_service_code)) {
							$extra_field = $_service_code.' | ';	
						}
					}

					$tofw_options .= '<option value="'.$brand_id.'">'.$extra_field.$brand_title.'</option>';
				}
			} else {
				return esc_html_e("Sorry nothing to display!", "on_the_bench");
			}

			$tofw_options .= '</select>';

			return $tofw_options;
		}
	endif;


	/*
		Generate Select options
		
		only works with WooCommerce Enabled products
		
		Wouldn't work if WooCommerce is not active

		return select field with options
	*/	
	if(!function_exists("tofw_woo_select_options")):
		function tofw_woo_select_options($post_type) {

			if(is_woocommerce_activated() == false) {
				return;
			}

			$product_args = array(
							'post_type' 		=> $post_type,
							'orderby'			=> 'title',
							'order' 			=> 'ASC',
							'posts_per_page' 	=> -1,
						);

			$product_query = new WP_Query($product_args);
			
			$select_id = $post_type;
			
			$tofw_options = '<select name="'.$post_type.'" id="select_'.$select_id.'">';
			$tofw_options .= '<option value="">----</option>';

			if ($product_query->have_posts() ) { 
				while($product_query->have_posts()) {
					$product_query->the_post();

					$_product_id 	= $product_query->post->ID;
					
					$product_obj 	= tofw_get_product( $_product_id );
					
					$product_title 	= $product_obj->get_name(); 
					
					$extra_field = '';
					
					$type =  $product_obj->get_type();

					$product_sku = $product_obj->get_sku();

					if($type == 'variable') {
						
						$extra_field = (!empty($product_sku))? $product_sku." | " : "";

						$tofw_options .= '<optgroup label="'.$extra_field.$product_title.'">';
						
						foreach ( $product_obj->get_children( ) as $child_id ) {
							$variation = tofw_get_product( $child_id ); 
				
							if ( ! $variation || !$variation->exists() ) {
								continue;
							}
							
							$variation_sku 	= $variation->get_sku();
							$variation_name = $variation->get_name();

							if(!empty($variation_sku)) {
								$extra_field = $product_sku." | ";
							}
							$tofw_options .= '<option value="'.$child_id.'">'.$extra_field.$variation_name.'</option>';
						}
					
						$tofw_options .= '</optgroup>';
					} else {

						if(!empty($product_sku)) {
							$extra_field = $product_sku." | ";
						}
						$tofw_options .= '<option value="'.$_product_id.'">'.$extra_field.$product_title.'</option>';	
					}
				}
			} else {
				return esc_html_e("Sorry nothing to display!", "on_the_bench");
			}

			$tofw_options .= '</select>';

			return $tofw_options;
		}
	endif;

	if(!function_exists('tofw_generate_random_string')):
		function tofw_generate_random_string($length = 10) {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$charactersLength = strlen($characters);
			$randomString = '';
			for ($i = 0; $i < $length; $i++) {
				$randomString .= $characters[rand(0, $charactersLength - 1)];
			}
			return $randomString;
		}
	endif;

	if(!function_exists("tofw_order_grand_total")):
		function tofw_order_grand_total($post_id, $term) {
			global $wpdb;
			
			if(empty($post_id)) {
				return;
			}
			
			$order_id = $post_id;
			
			$on_the_bench_items 		= $wpdb->prefix.'tofw_otb_order_items';
			$on_the_bench_items_meta = $wpdb->prefix.'tofw_otb_order_itemmeta';
			
			$select_items_query = "SELECT * FROM `{$on_the_bench_items}` WHERE `order_id`='{$order_id}' AND `order_item_type`='extras'";
			
			$items_result = $wpdb->get_results($select_items_query);
			
			$extras_total = 0;
			$extra_tax 	 = 0;

			foreach($items_result as $item) {
				$order_item_id 	 = $item->order_item_id;
				
				$tofw_extra_qty		= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_extra_qty'" );
				$tofw_extra_price		= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_extra_price'" );
				
				$tofw_extra_tax		= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_extra_tax'" );

				$row_total =	$tofw_extra_price->meta_value*$tofw_extra_qty->meta_value;	

				$extras_total += $row_total;

				if(isset($tofw_extra_tax)) {
					$tofw_extra_tax_value = (float)$tofw_extra_tax->meta_value;
					$extra_tax += ($row_total/100)*$tofw_extra_tax_value;
				}
			}
			
			//Getting Services Total
			$services_total = 0;
			$service_tax 	 = 0;

			$select_items_query = "SELECT * FROM `{$on_the_bench_items}` WHERE `order_id`='{$order_id}' AND `order_item_type`='services'";
			
			$items_result = $wpdb->get_results($select_items_query);
			
			foreach($items_result as $item) {
				$order_item_id 	 = $item->order_item_id;
				
				$tofw_service_qty		= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_service_qty'" );
				$tofw_service_price	= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_service_price'" );

				$tofw_service_tax		= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_service_tax'" );

				$row_total =	$tofw_service_price->meta_value*$tofw_service_qty->meta_value;	

				$services_total += $row_total;

				if(isset($tofw_service_tax)) {
					$tofw_service_tax_value = (float)$tofw_service_tax->meta_value;
					$service_tax += ($row_total/100)*$tofw_service_tax_value;
				}
			}
			
			
			//Getting Parts Total
			$parts_total = 0;
			$part_tax 	 = 0;

			$select_items_query = "SELECT * FROM `{$on_the_bench_items}` WHERE `order_id`='{$order_id}' AND `order_item_type`='parts'";
			
			$items_result = $wpdb->get_results($select_items_query);
			
			foreach($items_result as $item) {
				$order_item_id 	 = $item->order_item_id;
				
				$tofw_part_qty		= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_part_qty'" );
				$tofw_part_price		= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_part_price'" );

				$tofw_part_tax		= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_part_tax'" );
				
				$row_total =	$tofw_part_price->meta_value*$tofw_part_qty->meta_value;	

				$parts_total += $row_total;

				if(isset($tofw_part_tax)) {
					$tofw_part_tax_new = (float)$tofw_part_tax->meta_value;

					$part_tax += ($row_total/100)*$tofw_part_tax_new;
				}
			}

			//Getting Parts Total
			$products_total 	= 0;
			$products_tax 	 	= 0;

			$select_items_query = "SELECT * FROM `{$on_the_bench_items}` WHERE `order_id`='{$order_id}' AND `order_item_type`='products'";
			
			$items_result = $wpdb->get_results($select_items_query);
			
			foreach($items_result as $item) {
				$order_item_id 	 = $item->order_item_id;
				
				$tofw_product_qty		= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_product_qty'" );
				$tofw_product_price	= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_product_price'" );

				$tofw_product_tax		= $wpdb->get_row( "SELECT * FROM {$on_the_bench_items_meta} WHERE `order_item_id` = '{$order_item_id}' AND `meta_key` = 'tofw_product_tax'" );
				
				$row_total 			=	$tofw_product_price->meta_value*$tofw_product_qty->meta_value;	

				$products_total += $row_total;

				if(isset($tofw_product_tax)) {
					$tofw_product_tax_new = (float)$tofw_product_tax->meta_value;

					$products_tax += ($row_total/100)*$tofw_product_tax_new;
				}
			}
			
			$grand_total = $products_total+$parts_total+$services_total+$extras_total+$products_tax+$part_tax+$service_tax+$extra_tax;
			
			if($term == "grand_total") {
				return number_format($grand_total, 2, '.', '');
			} elseif($term == "parts_total") {
				return number_format($parts_total+$part_tax, 2, '.', '');
			} elseif($term == "products_total") {
				return number_format($products_total+$products_tax, 2, '.', '');
			} elseif($term == "services_total") {
				return number_format($services_total+$service_tax, 2, '.', '');
			} elseif($term == "extras_total") {
				return number_format($extras_total+$extra_tax, 2, '.', '');	
			} elseif($term == "parts_tax") {
				return number_format($part_tax, 2, '.', '');
			} elseif($term == "products_tax") {
				return number_format($products_tax, 2, '.', '');
			} elseif($term == "services_tax") {
				return number_format($service_tax, 2, '.', '');
			} elseif($term == "extras_tax") {
				return number_format($extra_tax, 2, '.', '');
			}
		}
	endif;


	/**
	 * Function Creates Tax Options
	 * 
	 * This doesn't create select around options
	 * $tofw_part_tax_value = array(
	 *					"tofw_default_tax_value"	=> $tofw_part_tax_value,
	 *					"value_type"		=> "tax_rate"
	 *				);
	 *	
     * Single argument of selected ID.
	 * 	
	 * Takes parameter for selected option.
	 */
	if(!function_exists("tofw_generate_tax_options")):
		function tofw_generate_tax_options($tofw_primary_tax) {
			global $wpdb;

			$field_to_select 	= "tax_id";
			$selected_field 	= $tofw_primary_tax;

			if(is_array($tofw_primary_tax)) {
				$field_to_select 	= $tofw_primary_tax["value_type"];
				$selected_field 	= $tofw_primary_tax["tofw_default_tax_value"];
			}

			if(!isset($selected_field)) {
				$selected_field = "";
			}
			
			//Table
			$on_the_bench_taxes 	= $wpdb->prefix.'tofw_otb_taxes';

			$select_query 	= "SELECT * FROM `".$on_the_bench_taxes."` WHERE `tax_status`='active'";
			$select_results = $wpdb->get_results($select_query);
			
			$output = '';
			foreach($select_results as $result) {

				if($result->tax_id == $selected_field) {
					$selected = 'selected="selected"';
				} else {
					$selected = '';
				}

				$output .= '<option '.$selected.' value="'.esc_attr($result->$field_to_select).'">';
				$output .= esc_attr($result->tax_name);
				$output .= '</option>';

			} // End Foreach	

			return $output;
		}
	endif;


	/**
	 * Function Creates Job Status Options
	 * 
	 * @Snce 3.2
	 * 
	 * This doesn't create select around options
	 *	
     * Single argument of selected ID.
	 * 	
	 * Takes parameter for selected option.
	 */
	if(!function_exists("tofw_generate_status_options")):
		function tofw_generate_status_options($tofw_selected_status) {
			global $wpdb;

			$field_to_select 	= "status_slug";
			$selected_field 	= $tofw_selected_status;

			if(!isset($selected_field)) {
				$selected_field = "";
			}
			
			//Table
			$on_the_bench_job_status 	= $wpdb->prefix.'tofw_otb_job_status';

			$select_query 	= "SELECT * FROM `".$on_the_bench_job_status."` WHERE `status_status`='active'";
			$select_results = $wpdb->get_results($select_query);
			
			$output = '';
			foreach($select_results as $result) {

				if($result->status_slug == $selected_field) {
					$selected = 'selected="selected"';
				} else {
					$selected = '';
				}

				$output .= '<option '.$selected.' value="'.esc_attr($result->$field_to_select).'">';
				$output .= esc_attr($result->status_name);
				$output .= '</option>';

			} // End Foreach	

			return $output;
		}
	endif;


/* CBA Function Creates Location Options 
 *     Accepts the Post ID Location
 */
	if(!function_exists("tofw_generate_location_options")):
		function tofw_generate_location_options($tofw_location_id) {

			if(isset($tofw_location_id) && $tofw_location_id == "data-list") {
				$type_return 	= "data-list";
				$tofw_location_id 	= "";
			}

			$cat_terms = get_terms(
				array(
						'taxonomy'		=> 'location_brand',
						'hide_empty'    => true,
						'orderby'       => 'name',
						'order'         => 'ASC',
						'number'        => 0
					)
			);

			$output = "<option value=''>".esc_html__("Select Location", "on-the-bench")."</option>";

			if( $cat_terms ) :
				foreach( $cat_terms as $term ) :

					$output .= '<optgroup label="'.esc_html($term->name).'">';

					$args = array(
							'post_type'             => 'otb_locations',
							'posts_per_page'        => -1, //specify yours
							'post_status'           => 'publish',
							'tax_query'             => array(
														array(
															'taxonomy' => 'location_brand',
															'field'    => 'slug',
															'terms'    => $term->slug,
														),
													),
							'ignore_sticky_posts'   => true //caller_get_posts is deprecated since 3.1
						);
					$_posts = new WP_Query( $args );

					if( $_posts->have_posts() ) :
						while( $_posts->have_posts() ) : $_posts->the_post();

							$the_title = $term->name." | ".get_the_title();

							if($tofw_location_id == $_posts->post->ID) {
								$selected = 'selected="selected"';
							} else {
								$selected = '';
							}

							$output .= '<option '.esc_html($selected).' value="'.$_posts->post->ID.'">'.esc_html($the_title).'</option>';

						endwhile;
					endif;
					wp_reset_postdata(); //important
					
					$output .= '</optgroup>';

				endforeach;
			endif;

			return $output;
		}
	endif;

	/**
	 * Function Creates Device Options
	 * 
	 * @Snce 3.5
	 * 
	 * This doesn't create select around options
	 *	
     * Accepts the Post ID Device
	 * 	
	 */
	if(!function_exists("tofw_generate_device_options")):
		function tofw_generate_device_options($tofw_device_id) {

			if(isset($tofw_device_id) && $tofw_device_id == "data-list") {
				$type_return 	= "data-list";
				$tofw_device_id 	= "";
			}

			$cat_terms = get_terms(
				array(
						'taxonomy'		=> 'device_brand',
						'hide_empty'    => true,
						'orderby'       => 'name',
						'order'         => 'ASC',
						'number'        => 0
					)
			);

			$output = "<option value=''>".esc_html__("Select Device", "on-the-bench")."</option>";

			if( $cat_terms ) :
				foreach( $cat_terms as $term ) :

					$output .= '<optgroup label="'.esc_html($term->name).'">';

					$args = array(
							'post_type'             => 'otb_devices',
							'posts_per_page'        => -1, //specify yours
							'post_status'           => 'publish',
							'tax_query'             => array(
														array(
															'taxonomy' => 'device_brand',
															'field'    => 'slug',
															'terms'    => $term->slug,
														),
													),
							'ignore_sticky_posts'   => true //caller_get_posts is deprecated since 3.1
						);
					$_posts = new WP_Query( $args );

					if( $_posts->have_posts() ) :
						while( $_posts->have_posts() ) : $_posts->the_post();

							$the_title = $term->name." | ".get_the_title();

							if($tofw_device_id == $_posts->post->ID) {
								$selected = 'selected="selected"';
							} else {
								$selected = '';
							}

							$output .= '<option '.esc_html($selected).' value="'.$_posts->post->ID.'">'.esc_html($the_title).'</option>';

						endwhile;
					endif;
					wp_reset_postdata(); //important
					
					$output .= '</optgroup>';

				endforeach;
			endif;

			return $output;
		}
	endif;




	/**
	 * Function Creates Job Status links
	 * 
	 * @Snce 3.2
	 * 
	 * Created links with <li> around it for my Account with Job statuses.
	 *	
	 */
	if(!function_exists("tofw_generate_status_links_myaccount")):
		function tofw_generate_status_links_myaccount() {
			global $wpdb;

			$field_to_select 	= "status_slug";
			$selected_field 	= "";

			if(!isset($selected_field)) {
				$selected_field = "";
			}
			
			//Table
			$on_the_bench_job_status 	= $wpdb->prefix.'tofw_otb_job_status';

			$select_query 	= "SELECT * FROM `".$on_the_bench_job_status."` WHERE `status_status`='active'";
			$select_results = $wpdb->get_results($select_query);
			
			$output = '';
			foreach($select_results as $result) {

				if($result->status_slug == $selected_field) {
					$selected = 'class="active"';
				} else {
					$selected = '';
				}

				$output .= '<li '.$selected.'>';
				$output .= '<a href="'.get_the_permalink().'?job_status='.esc_attr($result->$field_to_select).'">';
				$output .= esc_attr($result->status_name);
				$output .= '</a></li>';

			} // End Foreach	

			return $output;
		}
	endif;
	

	/**
	 * Function Returns jobs 
	 * 
	 * Returns job tables
	 * 
	 * Filter jobs by customer and job status
	 */
	if(!function_exists("tofw_print_jobs_by_customer_table")):
		function tofw_print_jobs_by_customer_table($job_status, $customer_id) {
			if(!is_user_logged_in()) {
				return esc_html__("You are not logged in.", "on_the_bench");
				exit;
			} 

			if(empty($customer_id)) {
				return esc_html__("Requires a customer id.", "on_the_bench");
				exit;	
			}

			$page_id = get_queried_object_id();

			$user_role = tofw_get_user_roles_by_user_id($customer_id);

			if(in_array("customer", $user_role)) {
				$user_role_string = "_customer";
			} elseif(in_array("technician", $user_role)) {
				$user_role_string = "_technician";
			}

			if(isset($_GET["job_status"]) && !empty($_GET["job_status"])):
				$meta_query_arr = array(
										array(
											'key' 		=> $user_role_string,
											'value' 	=> $customer_id,
											'compare' 	=> '=',
										),
										array(
											'key'		=> "_tofw_order_status",
											'value'		=> sanitize_text_field($_GET["job_status"]),
											'compare'	=> '=',
										)
									);
			else: 						
				$meta_query_arr = array(
										array(
											'key' 		=> $user_role_string,
											'value' 	=> $customer_id,
											'compare' 	=> '=',
										)
									);
			endif;	

			//WordPress Query for Rep Jobs
			$jobs_args = array(
				'post_type' 		=> "otb_jobs",
				'orderby'			=> 'id',
				'order' 			=> 'DESC',
				'posts_per_page' 	=> -1,
				'post_status'		=> 'all',
				'meta_query' 		=> $meta_query_arr,
			);

			$jobs_query = new WP_Query($jobs_args);

			$system_currency 	= get_option('tofw_system_currency');

			$content = '<div class="jobs_table_list"><table>';

			$content .= '<thead><tr>';
			$content .= '<th>'.esc_html__("ID", "on_the_bench").'</th>';
			$content .= '<th>'.esc_html__("Case#", "on_the_bench").'</th>';
			$content .= '<th>'.esc_html__("Assigned To", "on_the_bench").'</th>';
			$content .= '<th>'.esc_html__("Order Date", "on_the_bench").'</th>';
			$content .= '<th>'.esc_html__("Total", "on_the_bench").'</th>';
			$content .= '<th>'.esc_html__("Order Status", "on_the_bench").'</th>';
			$content .= '<th>'.esc_html__("Payment", "on_the_bench").'</th>';
			$content .= '<th>'.esc_html__("View", "on_the_bench").'</th>';
			$content .= '</tr></thead><tbody>';

			if($jobs_query->have_posts()): while($jobs_query->have_posts()): 
				$jobs_query->the_post();

				$job_id 		= $jobs_query->post->ID;
				$case_number 	= get_post_meta($job_id, "_case_number", true); 
				$order_date 	= get_the_date('', $job_id);
				$payment_status = get_post_meta($job_id, "_tofw_payment_status_label", true);
				$job_status		= get_post_meta($job_id, "_tofw_order_status_label", true);
				$order_total 	= $system_currency.tofw_order_grand_total($job_id, "grand_total");
				$technician 	= get_post_meta($job_id, "_technician", true);

				$tech_user 		= get_user_by('id', $technician);
				$tech_name 		=  $tech_user->first_name . ' ' . $tech_user->last_name;

				$content .= '<tr>';
				$content .= '<td>'.esc_html($job_id).'</td>';
				$content .= '<td>'.esc_html($case_number).'</td>';
				$content .= '<td>'.esc_html($tech_name).'</td>';
				$content .= '<td>'.esc_html($order_date).'</td>';
				$content .= '<td>'.esc_html($order_total).'</td>';
				$content .= '<td>'.esc_html($job_status).'</td>';
				$content .= '<td>'.esc_html($payment_status).'</td>';
				$content .= '<td><a href="'.get_the_permalink($page_id).'?tofw_case_number='.esc_attr($case_number).'&print=yes&order_id='.esc_attr($job_id).'">'.esc_html__("View", "on_the_bench").'</a></td>';
				$content .= '</tr>';

			endwhile;
			else:
				$content .= esc_html__("No job found!", "on_the_bench");
			endif;

			$content .= "</tbody></table><!-- Table Ends here. --></div>";

			wp_reset_postdata();

			return $content;
		}
	endif;	

	/**
	 * Function Returns Status Label
	 * 
	 * Accepts Status Slug as Parameter
	 * 
	 * Returns Rate of Tax.
	 */
	if(!function_exists("tofw_return_status_name")):
		function tofw_return_status_name($tofw_status_slug) {
			global $wpdb;

			if(!isset($tofw_status_slug)) {
				$tofw_status_slug = "";
			}
			
			//Table
			$on_the_bench_job_status 	= $wpdb->prefix.'tofw_otb_job_status';

			$select_query 	= "SELECT * FROM `".$on_the_bench_job_status."` WHERE `status_slug`='".$tofw_status_slug."'";
			$select_results = $wpdb->get_row($select_query);
			
			$output = $select_results->status_name;

			return $output;
		}
	endif;


	/**
	 * Function Returns Tax Rate
	 * 
	 * Accepts Tax ID as Parameter
	 * 
	 * Returns Rate of Tax.
	 */
	if(!function_exists("tofw_return_tax_rate")):
		function tofw_return_tax_rate($tofw_rate_to_return) {
			global $wpdb;

			if(!isset($tofw_rate_to_return)) {
				$tofw_rate_to_return = "";
			}
			
			//Table
			$on_the_bench_taxes 	= $wpdb->prefix.'tofw_otb_taxes';

			$select_query 	= "SELECT * FROM `".$on_the_bench_taxes."` WHERE `tax_id`='".$tofw_rate_to_return."'";
			$select_results = $wpdb->get_row($select_query);
			
			$output = $select_results->tax_rate;

			return $output;
		}
	endif;

	/**
	 * Function Returns Tax ID
	 * 
	 * Accepts Tax Rate
	 * 
	 * Returns TAX ID
	 */
	if(!function_exists("tofw_return_tax_id")):
		function tofw_return_tax_id($tofw_id_to_return) {
			global $wpdb;

			if(!isset($tofw_id_to_return) || empty($tofw_id_to_return)) {
				$tofw_id_to_return = "";
			}
			
			//Table
			$on_the_bench_taxes 	= $wpdb->prefix.'tofw_otb_taxes';

			$select_query 	= "SELECT * FROM `".$on_the_bench_taxes."` WHERE `tax_rate`='".$tofw_id_to_return."'";
			$select_results = $wpdb->get_row($select_query);
			
			if( $select_results ) {
				$output = $select_results->tax_id;
			} else {
				$output = "";
			}	

			return $output;
		}
	endif;

	
	/*
		* Returns Number of Jobs
		*
		* Accepts User ID 
		* Accepts user type (Customer and Technician)
	*/
	if(!function_exists("tofw_return_jobs_by_user")) {
		function tofw_return_jobs_by_user($user_id, $user_type) {
			//_technician //_customer

			$user_type = ($user_type == "customer") ? "_customer" : "_technician";	

			$query = new WP_Query(
								array( 
									'post_type' => 'otb_jobs',
									'meta_key' 	=> $user_type, 
									'meta_value' => $user_id 
								)
							);
			return $query->found_posts;

			wp_reset_postdata();
		}
	}


	/*
		* Get User Role
		*
		* By User ID
	*/
	if(!function_exists("tofw_get_user_roles_by_user_id")):
		function tofw_get_user_roles_by_user_id( $user_id ) {
			$user = get_userdata( $user_id );
			return empty( $user ) ? array() : $user->roles;
		}
	endif;	


	/*
		* Sends Email to Customer
		*
		* Requires Job ID to send Email to customer. 
		* Sends Job Status Email
	*/
	if(!function_exists("tofw_otb_send_customer_update_email")):
		function tofw_otb_send_customer_update_email($job_id) {
			global $wpdb;

			if(empty($job_id)) {
				return;
				exit;
			}

			$customer_id 		= get_post_meta($job_id, "_customer", true);
			$status_label 		= get_post_meta($order_id, "_tofw_order_status_label", true);
			$tofw_order_status 	= get_post_meta($post_id, '_tofw_order_status', true);

			$ext_message = "";

			if(!empty($tofw_order_status) && is_numeric($tofw_order_status)) {
				$on_the_bench_job_status 	= $wpdb->prefix.'tofw_otb_job_status';
				$tofw_status_row					= $wpdb->get_row( "SELECT * FROM {$on_the_bench_job_status} WHERE `status_id` = '{$tofw_order_status}'" );
				
				$status_name 			= $tofw_status_row->status_name;
				$message 				= $tofw_status_row->status_email_message;
			}

			if(empty($customer_id)) {
				return;
				exit;
			}

			$user_info 		= get_userdata($customer_id);
			$user_name 		= $user_info->first_name." ".$user_info->last_name;
			$user_email 	= $user_info->user_email;

			if(empty($user_email)) {
				return;
				exit;
			}

			$menu_name_p 	= get_option("menu_name_p");

			$to 			= $user_email;
			$subject 		= esc_html__("Your Job Status is Updated!", "on-the-bench")." | ".esc_html($menu_name_p);
			$headers 		= array('Content-Type: text/html; charset=UTF-8');


			$body	 		= "<h2>".esc_html__("Your Job Status Is Updated.", "on-the-bench")."</h2>";
			if(!empty($message)) {
				$body	 		.= "<p>".$message."</p>";
			} else {
				$body	 		.= "<p>".esc_html__("Your Job update is below if you have any questions please reach us.", "on-the-bench")."</p>";
			}
			
			$body	 		.= '<style type="text/css">.repair_box table tr td, .repair_box table tr th {border:1px solid #666;} .repair_box table {margin-top:35px;}</style><div class="repair_box">'.tofw_print_order_invoice($job_id, "email").'</div>';

			wp_mail( $to, $subject, $body, $headers );
		}
	endif;	

	/**
	* Check if WooCommerce is activated
	*/
	if ( ! function_exists( 'is_woocommerce_activated' ) ) {
		function is_woocommerce_activated() {
			if ( class_exists( 'woocommerce' ) ) { return true; } else { return false; }
		}
	}


	/**
	 * Check If Parts Are Deactive
	 * 
	 * Check if Woo is Active/Deactive
	 */
	if(!function_exists("is_parts_switch_woo")) {
		function is_parts_switch_woo() {
			$tofw_enable_woo_products = get_option("tofw_enable_woo_products");

			if($tofw_enable_woo_products == "on") {
				if(is_woocommerce_activated() == true) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
	}


	if(!function_exists("tofw_number_format")) {
		function tofw_number_format($number) {
			if(!isset($number)) {
				return;
			}

			$return_number = number_format($number, 2, '.', '');

			return $return_number;
		}
	}


/* CBA Takes Location ID
 * Returns Location Name and Brand
 */
	if(!function_exists("return_location_label")):
		function return_location_label($location_id) {
			if(empty($location_id)) {
				return;
			}

			$terms = get_the_terms($location_id, array( 'location_brand') );

			$i = 1;
			$term_output = "";
			foreach ( $terms as $term ) {
				$term_output .= $term->name;
				$term_output .= ($i < count($terms))? " / " : "";
				$i++;
			}

			if(!empty($term_output)) {
				$term_output = $term_output." ";
			}

			$location_title = $term_output.get_the_title($location_id);

			return $location_title;
		}
	endif;

	/*
	 * Takes Device ID
	 * 
	 * Returns Device Name and Brand
	 * 
	*/
	if(!function_exists("return_device_label")):
		function return_device_label($device_id) {
			if(empty($device_id)) {
				return;
			}

			$terms = get_the_terms($device_id, array( 'device_brand') );

			$i = 1;
			$term_output = "";
			foreach ( $terms as $term ) {
				$term_output .= $term->name;
				$term_output .= ($i < count($terms))? " / " : "";
				$i++;
			}

			if(!empty($term_output)) {
				$term_output = $term_output." ";
			}

			$device_title = $term_output.get_the_title($device_id);

			return $device_title;
		}
	endif;

	/*
	 * File Upload Function
	 * 
	 * @Since 3.5
	 */
	if(!function_exists("tofw_image_uploader_field")) :
		function tofw_image_uploader_field( $name, $value = '') {
			
			$image = ' button">'.esc_html__("Upload File", "on-the-bench");

			$display 	= 'none'; // display state ot the "Remove image" button

			$feat_image_url = wp_get_attachment_url($value);
			
			if(!isset($file_html)) {
				$file_html = "";
			}
			

			if(!empty($feat_image_url)) {
				$file_html 	.= '<a href="'.esc_url($feat_image_url).'" class="true_pre_image" target="_blank"><span class="dashicons dashicons-media-document"></span></a>';
				$display 	= "inline-block";
			} 
			return '
			<div>
				<a href="#" class="misha_upload_image_button' . $image . '</a>
				<input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $value . '" />
				
				'.$file_html.'

				<a href="#" class="misha_remove_image_button" style="display:inline-block;display:' . $display . '">'.esc_html__("Remove File", "on-the-bench").'</a>
			</div>';
		}
	endif;


	if(!function_exists("tofw_inventory_management_status")):
		function tofw_inventory_management_status() {
			//Inventory Management Header
			if(is_woocommerce_activated() == true) {
				$stockManagement 		= get_option("woocommerce_manage_stock");
				$tofw_enable_woo_products = get_option("tofw_enable_woo_products");

				if($stockManagement == "yes" && $tofw_enable_woo_products == "on") {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
	endif;


	if(!function_exists("tofw_users_dropdown")):
		function tofw_users_dropdown($args) {
			if(!is_array($args)) {
				return;
			}
			if(empty($args["name"]) || empty($args["role"])) {
				return;
			}
			// query array
			$user_args = array(
				'role' => $args["role"]
			);

			$users = get_users($user_args);

			if( empty($users) ) {
				return;
			}

			$output = '<select id="'.$args["name"].'" name="'.$args["name"].'">';
			
			if(isset($args["show_option_all"])) {
				$output .= '<option value="0">'.$args["show_option_all"].'</option>';
			}
			
			foreach( $users as $user ) {
				$selected = ($args["selected"] == $user->ID)? " selected": "";

				$output .= '<option '.$selected.' value="'.$user->ID.'">'.$user->ID.' | '.$user->display_name.'</option>';
			}
			$output .= '</select>';

			if($args["echo"] == 0) {
				return $output;
			} else {
				echo $output;
			}
		}
	endif;