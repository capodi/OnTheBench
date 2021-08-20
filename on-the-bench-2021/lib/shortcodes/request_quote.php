<?php
	/*
	 * Request Quote Shortcode
	 * 
	 * Generates a Form which requests Quote
	 * Quote is added into Jobs with Status Quote.
	*/

	if(!function_exists("tofw_request_quote_form()")):
	function tofw_request_quote_form() { 
		$content = '';
		
		$content .= '<div class="tofw_request_quote_form">';
		$content .= '<h2>'.esc_html__("Request a quote now!", "on-the-bench").'</h2>';
		$content .= '<p>'.esc_html__("Fill in the form below to get your quote.", "on-the-bench").'</p>';
		
		$content .= '<form data-async data-abide class="needs-validation" method="post"><div class="grid-container">';

		if(!is_user_logged_in()):
		$content .= '<div class="grid-x grid-padding-x">';
		$content .= '<div class="medium-6 cell">';
	
		$content .= '<label>'.esc_html__("First Name", "on-the-bench")." (*)";
		$content .= '<input type="text" name="firstName" id="firstName" required class="form-control login-field" value="" placeholder="">';
		$content .= '</label>';
	 
		$content .= '</div><!-- column Ends /-->';
		$content .= '<div class="medium-6 cell">';

		$content .= '<label>'.esc_html__("Last Name", "on-the-bench")." (*)";
		$content .= '<input type="text" name="lastName" id="lastName" required class="form-control login-field" placeholder="">';
		$content .= '</label>';
	 
	  	$content .= '</div><!-- column Ends /-->';  
		$content .= '</div><!-- grid-x ends /-->';

		$content .= '<div class="grid-x grid-padding-x">';
		$content .= '<div class="medium-6 cell">';

		$content .= '<label>'.esc_html__("Email", "on-the-bench")." (*)";
		$content .= '<input type="email" name="userEmail" id="userEmail" required class="form-control login-field" placeholder="">';
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
	 
	  	$content .= '</div><!-- column Ends /-->';  
		$content .= '</div><!-- grid-x ends /-->';
		endif;

		$content .= '<div class="grid-x grid-padding-x">';
		$content .= '<div class="medium-12 cell">';

		$content .= '<label>'.esc_html__("Job Details", "on-the-bench")." (*)";
		$content .= '<textarea name="jobDetails" required class="form-control login-field" placeholder=""></textarea>';
		$content .= '</label>';
	 
	  	$content .= '</div><!-- column Ends /-->';  
		$content .= '</div><!-- grid-x ends /-->';

		$content .= '<input name="form_type" type="hidden" 
		value="tofw_request_quote_form" />';

		$content .=  wp_nonce_field( 'tofw_on_the_bench_nonce', 'tofw_request_quote_nonce', $echo = false);
		$content .= '<input type="submit" class="button button-primary primary" value="'.esc_html__("Request Quote!", "on-the-bench").'" />';

		$content .= '</form>';

		$content .= '<p><small>* '.esc_html__("Required fields cannot be left empty.", "on-the-bench").'</small></p>';

		$content .= '<div class="form-message"></div></div><!-- grid-container ends /-->';
		$content .= '</div>';

		return $content;
	}//tofw_list_services.
	add_shortcode('tofw_request_quote_form', 'tofw_request_quote_form');
	endif;

	if(!function_exists("tofw_otb_submit_quote_form")):

		function tofw_otb_submit_quote_form() { 
			global $wpdb;

			if (!isset( $_POST['tofw_request_quote_nonce'] ) 
				|| ! wp_verify_nonce( $_POST['tofw_request_quote_nonce'], 'tofw_on_the_bench_nonce' )) :
					$values['message'] = esc_html__("Something is wrong with your submission!", "on-the-bench");
					$values['success'] = "YES";
			else:
				$error = 0;
				//Form processing
				if(!isset($_POST["form_type"]) || $_POST["form_type"] != "tofw_request_quote_form") {
					$error = 1;
					$message = esc_html__("Unknown form.", "on-the-bench");
				}

				if(!is_user_logged_in()):
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
						$message = esc_html__("Email already in user. Try resetting password if its your Email. Then login to submit your quote request.", "on-the-bench");
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
					
				else: 
					//User is logged in
					if($error == 0) :
						$job_details 	= sanitize_text_field($_POST["jobDetails"]);

						$current_user 	= wp_get_current_user();

						$first_name 	= $current_user->user_firstname;
						$last_name 		= $current_user->user_lastname;

						$user_id		= $current_user->ID;

						if(empty($job_details)) {
							$error = 1;
							$message = esc_html__("Please enter job details.", "on-the-bench");
						}
					endif;	
				endif;

				//We have user ID here.
				if(isset($user_id) && isset($job_details) && $error == 0) {
					//Let's insert the Job
					//We have now User ID
					//We have now Job Details. 
					
					$case_number 	= tofw_generate_random_string(6).time();
					$order_status 	= "quote";
					$customer_id	= $user_id;

					//Let's now prepare our WP Insert post.
					$post_data = array(
						'post_status'   => 'draft',
						'post_type' 	=> 'otb_jobs',
					);
					
					if(post_exists($case_number) == 0) {
						$post_id = wp_insert_post( $post_data );
						
						update_post_meta($post_id, '_case_number', $case_number);
						update_post_meta($post_id, '_customer', $customer_id);
						update_post_meta($post_id, '_case_detail', $job_details);
						update_post_meta($post_id, '_tofw_order_status', $order_status);

						if(isset($case_number)) {
							$title = $case_number;
							$where = array('ID' => $post_id );
							$wpdb->update( $wpdb->posts, array( 'post_title' => $title ), $where );
						}

						$message = esc_html__("We have received your quote request we would get back to you asap! Thanks.", "on-the-bench");
			
					} else {
						$message = esc_html__("Your case is already registered with us.", "on-the-bench");
					}
					
					$on_the_bench_email 	= get_option("on_the_bench_email");
					$menu_name_p 			= get_option("menu_name_p");
					
					if(empty($on_the_bench_email)) {
						$on_the_bench_email	= get_option("admin_email");	
					}

					$to 			= $on_the_bench_email;
					$subject 		= esc_html__("New quote request", "on-the-bench")." | ".esc_html($menu_name_p);
					$headers 		= array('Content-Type: text/html; charset=UTF-8');


					$body	 		= "<h2>".esc_html__("You have received a quote request.", "on-the-bench")."</h2>";
					$body	 		.= "<p>".esc_html__("First Name: .", "on-the-bench")." ".esc_html($first_name);
					$body	 		.= "<br>".esc_html__("Last Name: .", "on-the-bench")." ".esc_html($last_name);
					$body	 		.= "<br><br>".esc_html__("Job Details: .", "on-the-bench")." ".esc_html($job_details)."</p>";

					wp_mail( $to, $subject, $body, $headers );
				}

				$values['message'] = $message;
				$values['success'] = "YES";
			endif;

			wp_send_json($values);
			wp_die();
		}
		add_action( 'wp_ajax_tofw_otb_submit_quote_form', 'tofw_otb_submit_quote_form' );
		add_action( 'wp_ajax_nopriv_tofw_otb_submit_quote_form', 'tofw_otb_submit_quote_form' );
	endif;