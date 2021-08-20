<?php
	/*
	 * Start Job
	 * 
	 * By Selecting Location
	 * Shortcode for front end
	 * 
	 * @Since 3.53
	*/

	if(!function_exists("tofw_start_job_with_location")):
		function tofw_start_job_with_location() { 
			if(!is_user_logged_in()) {
				return "";
				exit;
			} 

			$user_role = tofw_get_user_roles_by_user_id(get_current_user_id());

			$content = '';

			if(in_array("administrator", $user_role) || in_array("technician", $user_role) || in_array("shop_manager", $user_role) || in_array("shop_employee", $user_role)) :
				wp_enqueue_script("foundation-js");
				wp_enqueue_script("tofw-cr-js");

				wp_enqueue_style("select2");
				wp_enqueue_script("select2");

				$content .= '<button class="button" data-open="WCstartJob">'.esc_html__("Start a Job", "on-the-bench").'</button>';

				$content .= '<div class="reveal large" id="WCstartJob" data-reveal><div class="startNewJob">';

				$content .= '<p class="lead">'.esc_html__("Start a New Job", "on-the-bench").'</p>';

				$content .= '<div class="form-message"></div><form data-async data-abide class="needs-validation" method="post"><div class="grid-container">';

				$content .= '<div class="grid-x grid-padding-x">';
				$content .= '<div class="medium-6 cell">';
				
				$content .= '<label for="deliveryDate">'.esc_html__("Delivery Date", "on-the-bench")." (*)";
				$content .= '<input type="date" name="delivery_date" id="deliveryDate" required class="form-control login-field" placeholder="">';
				$content .= '</label>';

				$content .= '</div><!-- Column /-->';

				$content .= '<div class="medium-6 cell">';

				$content .= '<label for="jobDetail">'.esc_html__("Select Customer", "on-the-bench");
				$content .= wp_dropdown_users( array( 'show_option_all' => esc_html__('Select Customer', 'on-the-bench'), 'name' => 'customer', 'role' => 'customer', 'echo' => 0, 'selected' => $user_value ) );
				$content .= '</label>';
				$content .= '<p class="help-text">'.esc_html__("Select customer if does not exist!", 'on-the-bench').' <a class="button button-primary button-small" id="customerFormReveal">'.esc_html__("Add New Customer", "on-the-bench").'</a></p>';

				$content .= '</div>';
				$content .= '</div><!-- Grid /-->';


/*CBA*/			$content .= '<div class="grid-x grid-padding-x">';
				$content .= '<div class="medium-6 cell">';
			
				$content .= '<label for="otb_locations">';
				$content .= esc_html__('Location', 'on-the-bench');
				$content .= '</label>';

				$content .= '<select id="otb_locations" name="location_post_id">';
				$content .= tofw_generate_location_options("data-list");
				$content .= '</select>';
			
				$content .= '</div><!-- column Ends /-->';
				$content .= '<div class="medium-6 cell">';

				$content .= '<label for="locationID">'.esc_html__("Location ID/LCTN", "on-the-bench");
				$content .= '<input type="text" name="locationID" id="locationID" class="form-control login-field" placeholder="">';
				$content .= '</label>';
			
				$content .= '</div><!-- column Ends /-->';  
/*CBA*/			$content .= '</div><!-- grid-x ends /-->';


				$content .= '<div class="addNewCustomer" id="addNewCustomer">';	
					$content .= '<div class="grid-x grid-padding-x">';
					$content .= '<div class="medium-6 cell">';
				
					$content .= '<label>'.esc_html__("First Name", "on-the-bench")." (*)";
					$content .= '<input type="text" name="firstName" id="firstName" class="form-control login-field" value="" placeholder="">';
					$content .= '</label>';
				 
					$content .= '</div><!-- column Ends /-->';
					$content .= '<div class="medium-6 cell">';
			
					$content .= '<label>'.esc_html__("Last Name", "on-the-bench")." (*)";
					$content .= '<input type="text" name="lastName" id="lastName" class="form-control login-field" placeholder="">';
					$content .= '</label>';
				 
					$content .= '</div><!-- column Ends /-->';  
					$content .= '</div><!-- grid-x ends /-->';
			
					$content .= '<div class="grid-x grid-padding-x">';
					$content .= '<div class="medium-6 cell">';
			
					$content .= '<label>'.esc_html__("Email", "on-the-bench")." (*)";
					$content .= '<input type="email" name="userEmail" id="userEmail" class="form-control login-field" placeholder="">';
					$content .= '</label>';
				 
					$content .= '</div><!-- column Ends /-->';
					$content .= '<div class="medium-6 cell">';
			
					$content .= '<label>'.esc_html__("Phone number", "on-the-bench");
					$content .= '<input type="text" name="phoneNumber" class="form-control login-field" placeholder="">';
					$content .= '</label>';
				 
					$content .= '</div><!-- column Ends /-->';  
					$content .= '</div><!-- grid-x ends /-->';
						
					$content .= '<div class="grid-x grid-padding-x">';
					$content .= '<div class="medium-6 cell">';
			
					$content .= '<label>'.esc_html__("City", "on-the-bench");
					$content .= '<input type="text" name="userCity" class="form-control login-field" placeholder="">';
					$content .= '</label>';
				 
					$content .= '</div><!-- column Ends /-->';
					$content .= '<div class="medium-6 cell">';
			
					$content .= '<label>'.esc_html__("Postal Code", "on-the-bench");
					$content .= '<input type="text" name="postalCode" class="form-control login-field" placeholder="">';
					$content .= '</label>';
				 
					$content .= '</div><!-- column Ends /-->';  
					$content .= '</div><!-- grid-x ends /-->';
			
					$content .= '<div class="grid-x grid-padding-x">';
					$content .= '<div class="medium-6 cell">';
			
					$content .= '<label>'.esc_html__("Company", "on-the-bench");
					$content .= '<input type="text" name="userCompany" class="form-control login-field" placeholder="">';
					$content .= '</label>';
				 
					$content .= '</div><!-- column Ends /-->';
					$content .= '<div class="medium-6 cell">';
			
					$content .= '<label>'.esc_html__("Address", "on-the-bench");
					$content .= '<input type="text" name="userAddress" class="form-control login-field" placeholder="">';
					$content .= '</label>';
				 	$content .= '<input type="hidden" name="verify_customer" id="verifyCustomer" value="0" />';
					$content .= '</div><!-- column Ends /-->';  
					$content .= '</div><!-- grid-x ends /-->';
				$content .= '</div>';//Add new customer wrapper

				$content .= '<div class="grid-x grid-padding-x">';
				$content .= '<div class="medium-12 cell">';
		
				$content .= '<label>'.esc_html__("Job Details", "on-the-bench")." (*)";
				$content .= '<textarea name="jobDetails" required class="form-control login-field" placeholder=""></textarea>';
				$content .= '</label>';

				$content .= '<input name="form_type" type="hidden" value="tofw_create_new_job_form" />';
				$content .=  wp_nonce_field( 'tofw_on_the_bench_nonce', 'tofw_add_job_nonce', $echo = false);

				$content .= '<input type="submit" value="'.esc_html__("Create Job", "on-the-bench").'" class="button button-primary" /> ';
				
				$content .= '</div><!-- column Ends /-->';  
				$content .= '</div><!-- grid-x ends /-->';

				$content .= '</div></form></div><button class="close-button" data-close aria-label="Close modal" type="button"><span aria-hidden="true">&times;</span></button></div>';

				return $content;
			endif;
		}//Function Ends.


/*CBA*/	add_shortcode('tofw_start_job_with_location', 'tofw_start_job_with_location');

	endif;


	if(!function_exists("tofw_otb_create_new_job")):

		function tofw_otb_create_new_job() { 
			global $wpdb;

			if (!isset( $_POST['tofw_add_job_nonce'] ) 
				|| ! wp_verify_nonce( $_POST['tofw_add_job_nonce'], 'tofw_on_the_bench_nonce' )) :
					$values['message'] = esc_html__("Something is wrong with your submission!", "on-the-bench");
					$values['success'] = "YES";
			else:
				$error = 0;
				//Form processing
				if(!isset($_POST["form_type"]) || $_POST["form_type"] != "tofw_create_new_job_form") {
					$error = 1;
					$message = esc_html__("Unknown form.", "on-the-bench");
				}

				if(isset($_POST["verify_customer"]) && $_POST["verify_customer"] == '1' && ($_POST["customer"] == "0" || $_POST["customer"] == "")):
					$first_name 	= sanitize_text_field($_POST["firstName"]);
					$last_name 		= sanitize_text_field($_POST["lastName"]);
					$user_email 	= sanitize_email($_POST["userEmail"]);
					$username 		= sanitize_email($_POST["userEmail"]);
					$phone_number 	= sanitize_text_field($_POST["phoneNumber"]);
					$user_city 		= sanitize_text_field($_POST["userCity"]);
					$postal_code 	= sanitize_text_field($_POST["postalCode"]);
					$user_company 	= sanitize_text_field($_POST["userCompany"]);
					$user_address 	= sanitize_text_field($_POST["userAddress"]);
					$job_details 	= sanitize_text_field($_POST["jobDetails"]);
					$user_role 		= "customer";

					if(!$user_email) {
						$error = 1;
						$message = esc_html__("Email is not valid.", "on-the-bench");	
					} elseif(empty($first_name)) {
						$error = 1;
						$message = esc_html__("First name required.", "on-the-bench");
					} elseif(empty($job_details)) {
						$error = 1;
						$message = esc_html__("Please enter job details.", "on-the-bench");
					} if(!empty($username) && username_exists($username)) {
						$error = 1;
						$message = esc_html__("Duplicate User. Please login to submit your quote request.", "on-the-bench");
					} elseif(!empty($username) && !validate_username($username)) {
						$error = 1;
						$message = esc_html__("Not a valid username", "on-the-bench");
					} elseif(!empty($user_email) && !is_email($user_email)) {
						$error = 1;
						$message = esc_html__("Email is not valid", "on-the-bench");
					} elseif(email_exists($user_email)) {
						$error = 1;
						$message = esc_html__("Email already exists in customers, please use different email or search customer above.", "on-the-bench");
					}

					$password 	= wp_generate_password(8, false );
					
					if($error == 0) :
						if(!empty($username) && !empty($user_email)) {
							//We are all set to Register User.
							$userdata = array(
								'user_login' 	=> $username,
								'user_email' 	=> $user_email,
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
								$message = esc_html__("User account is created logins sent to email.", "on-the-bench")." ".$user_email;
								$user_id = $register_user;
						
								if(!empty($user_id)) {
									update_user_meta( $user_id, 'customer_phone', $phone_number);
									update_user_meta( $user_id, 'customer_address', $user_address);
									update_user_meta( $user_id, 'customer_city', $user_city);
									update_user_meta( $user_id, 'zip_code', $postal_code);
									update_user_meta( $user_id, 'company', $user_company);
								}
						
							} else {
								$message = '<strong>' . $register_user->get_error_message() . '</strong>';
							}
						}
					endif;	
				elseif(isset($_POST["customer"]) && $_POST["customer"] == "0" && isset($_POST["verify_customer"]) && $_POST["verify_customer"] != '1'):
					$error = 1;
					$message = esc_html__("Please select a customer or add new customer.", "on-the-bench");
				else: 
					//User is logged in
					if($_POST["customer"] != 0) :
						$user_id 	= sanitize_text_field($_POST["customer"]);

						$user = get_userdata( $user_id );

						if ( $user === false ) {
							$error = 1;
							$message = esc_html__("Invalid Customer.", "on-the-bench");
						}
					endif;	

/*CBA*/				if(!isset($_POST["location_post_id"]) || empty($_POST["location_post_id"])) {
						$error = 1;
						$message = esc_html__("Please select location.", "on-the-bench");
					}


					if(!isset($_POST["jobDetails"]) || empty($_POST["jobDetails"])) {
						$error = 1;
						$message = esc_html__("Please add job details.", "on-the-bench");
					}

					if(!isset($_POST["delivery_date"]) || empty($_POST["delivery_date"])) {
						$error = 1;
						$message = esc_html__("Please add delivery date.", "on-the-bench");
					}
				endif;

				//We have user ID here.
				if(isset($user_id) && $error == 0) {
					//Let's insert the Job
					//We have now User ID
					//We have now Job Details. 
					
					$case_number 	= tofw_generate_random_string(6).time();

					$job_details	= empty($_POST["jobDetails"])? "" : sanitize_text_field($_POST["jobDetails"]);

					$delivery_date  = empty($_POST["delivery_date"])? "" : sanitize_text_field($_POST["delivery_date"]);
					
/*CBA*/				$location_post_id	= empty($_POST["location_post_id"])? "" : sanitize_text_field($_POST["location_post_id"]);
/*CBA*/				$location_id	= empty($_POST["location_id"])? "" : sanitize_text_field($_POST["location_id"]);

					$order_status 	= "new";
					$customer_id	= $user_id;

					//Let's now prepare our WP Insert post.
					$post_data = array(
						'post_status'   => 'publish',
						'post_type' 	=> 'otb_jobs',
					);
					
					if(post_exists($case_number) == 0) {
						$post_id = wp_insert_post( $post_data );
						
						update_post_meta($post_id, '_case_number', $case_number);
						update_post_meta($post_id, '_customer', $customer_id);
						update_post_meta($post_id, '_case_detail', $job_details);
						update_post_meta($post_id, '_tofw_order_status', $order_status);
						update_post_meta($post_id, '_delivery_date', $delivery_date);
						
/*CBA*/					update_post_meta($post_id, '_location_post_id', $location_post_id);
/*CBA*/					update_post_meta($post_id, '_location_id', $location_id);

						if(isset($case_number)) {
							$title = $case_number;
							$where = array('ID' => $post_id );
							$wpdb->update( $wpdb->posts, array( 'post_title' => $title ), $where );
						}
						$message = esc_html__("A new case have been registered with Case# ".$case_number.".", "on-the-bench");
			
					} else {
						$message = esc_html__("Your case is already registered with us.", "on-the-bench");
					}
				}

				$values['message'] = $message;
				
				if($error == 0) {
					$values['success'] = "YES";
					$values['reset_select2'] = "YES";
				}

			endif;

			wp_send_json($values);
			wp_die();
		}
		add_action( 'wp_ajax_tofw_otb_create_new_job', 'tofw_otb_create_new_job' );
		add_action( 'wp_ajax_nopriv_tofw_otb_create_new_job', 'tofw_otb_create_new_job' );
	endif;