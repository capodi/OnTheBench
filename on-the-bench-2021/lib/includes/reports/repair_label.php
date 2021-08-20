<?php
	/*
	 * Repair Label Functionality
	 *	
	 * Function Returns repair label 
	 * 
	 * Takes Order ID as an Argument.
	 */
	if(!function_exists("tofw_print_repair_label")) {
		function tofw_print_repair_label($order_id) {
			if(empty($order_id)) {
				return;
			}

			$tofw_use_taxes 	= get_option("tofw_use_taxes");

			//Let's do magic.
			$customer_id 	= get_post_meta($order_id, "_customer", true);
            $device_id 	    = get_post_meta($order_id, "_device_id", true);
			

			$system_currency 	= get_option('tofw_system_currency');
			

			$content = '';

			$content .= '<div class="invoice-box ticket-box"><div class="ticket">';

            $content .= '<p class="centered">';
            $content .= "<span id='current_date' colspan='2'></span><br>";


            $content .= '<strong>'.esc_html__("Case#", "on-the-bench").' :</strong> '.get_post_meta($order_id, "_case_number", true);
            
            if(!empty($customer_id)):
                $content .= '<br><strong>'.esc_html__("Customer ID", "on-the-bench").' :</strong> '.$customer_id;
            endif;

            if(!empty($device_id)):
                $content .= '<br><strong>'.esc_html__("Device ID", "on-the-bench").' :</strong> '.esc_html($device_id);
            endif;

            $content .= "</p>";

        $content .= '</div>';
        
		$content .= '<button id="btnPrint" class="hidden-print button button-primary" style="display:block;clear:both;float:none;width:100%;">'.esc_html__("Print", "on-the-bench").'</button>';
        $content .= '<p class="hidden-print">'.esc_html__("Print label to paste on device or parts for validation of claim.", "on-the-bench").'</p>';
		$content .= '</div><!-- Invoice-box Ends /-->';

		return $content;

		}
	}