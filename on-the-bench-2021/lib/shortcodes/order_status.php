<?php
	//List Services shortcode
	//Used to display Services on a page.
	//Linked to single service pages. 

	function tofw_order_status_form() { 
		$content = '';
		
		$content .= '<div class="tofw_order_status_form">';
		$content .= '<h2>'.esc_html__("Service/Repair Status", "on-the-bench").'</h2>';
		$content .= '<p>'.esc_html__("Please Enter Your Ticket Number", "on-the-bench").'</p>';
		
		$content .= '<form data-async="" method="post">';
		$content .= '<input type="text" required autofocus placeholder="'.esc_html__("Ticket Number...", "on-the-bench").'" name="tofw_case_number" />';
		$content .=  wp_nonce_field( 'tofw_on_the_bench_nonce', 'tofw_job_status_nonce', $echo = false);
		$content .= '<input type="submit" class="button button-primary primary" value="'.esc_html__("Review Status", "on-the-bench").'" />';
		$content .= '</form>';

		$content .= '<div class="form-message"></div>';
		$content .= '</div>';

		return $content;
	}//tofw_list_services.
	add_shortcode('tofw_order_status_form', 'tofw_order_status_form');


	if(!function_exists("tofw_cmp_otb_check_order_status")):

		function tofw_cmp_otb_check_order_status() { 
			if (!isset( $_POST['tofw_job_status_nonce'] ) 
				|| ! wp_verify_nonce( $_POST['tofw_job_status_nonce'], 'tofw_on_the_bench_nonce' )) :
					$values['message'] = esc_html__("Something is wrong with your submission!", "on-the-bench");
					$values['success'] = "YES";
			else:
				//Register User
				$wcCasaeNumber 		= $_POST["tofw_case_number"];

				if(!empty($wcCasaeNumber)) {
					$tofw_otb_args = array(
						'posts_per_page'   => 1,
						'post_type'        => 'otb_jobs',
						'meta_key'         => '_case_number',
						'meta_value'       => $wcCasaeNumber
					);
					$tofw_otb_query = new WP_Query($tofw_otb_args);

					if($tofw_otb_query->have_posts()): 

						while($tofw_otb_query->have_posts()): 
							$tofw_otb_query->the_post();

							$order_id = get_the_ID();

							$post_output = tofw_print_order_invoice($order_id, "status_check");
						endwhile;

						$values['message'] = $post_output;
					else: 
						$values['message'] = esc_html__("We haven't found any job with your given ticket number!", "on-the-bench");
					endif; 	
					wp_reset_postdata();
				}
				$values['success'] = "YES";

			endif;
			
			wp_send_json($values);
			wp_die();
		}
		add_action( 'wp_ajax_tofw_cmp_otb_check_order_status', 'tofw_cmp_otb_check_order_status' );
		add_action( 'wp_ajax_nopriv_tofw_cmp_otb_check_order_status', 'tofw_cmp_otb_check_order_status' );
	endif;