<?php
	/*
	 * Repair Order Functionality
	 *	
	 * Function Returns repair Order 
	 * 
	 * Takes Order ID as an Argument.
	 */
	if(!function_exists("tofw_print_repair_order")) {
		function tofw_print_repair_order($order_id) {
			if(empty($order_id)) {
				return;
			}

			$tofw_use_taxes 	= get_option("tofw_use_taxes");
			//Let's do magic.
			$customer_id 	= get_post_meta($order_id, "_customer", true);
			$user 			= get_user_by('id', $customer_id);
			$user_email 	=  $user->user_email;
			
            $customer_phone  	= get_user_meta( $customer_id, 'customer_phone', true);
            $customer_address 	= get_user_meta( $customer_id, 'customer_address', true);
            $customer_city 		= get_user_meta( $customer_id, 'customer_city', true);
            $customer_zip		= get_user_meta( $customer_id, 'zip_code', true);
            $customer_company	= get_user_meta( $customer_id, 'company', true);

			$system_currency 	= get_option('tofw_system_currency');
			

			$content = '';

			$content .= '<div class="invoice-box ticket-box"><div class="ticket">';

            if(!has_custom_logo()) { 
                $content .= '<h1 class="site-title">'.get_bloginfo( 'name' ).'</h1>';
            } else { 
                $on_the_bench_logo = get_option("on_the_bench_logo");
                
                $content .= '<img src="'.esc_url($on_the_bench_logo).'" style="width:auto; max-width:100%;height:auto;" />';
            }

            $content .= "<table>";
            $content .= "<tr>";
            $content .= "<th id='current_date' colspan='2'></th>";
            $content .= "</tr>";
            $content .="</table>";

            $content .= '<p class="centered"> <strong>'.esc_html__("Case#", "on-the-bench").' :</strong> '.get_post_meta($order_id, "_case_number", true).'</p>
            <table>
                <tbody>
                    <tr>
                        <td class="description"><strong>'.esc_html__("Order", "on-the-bench").' #</strong></td>
                        <td class="price">'.esc_html($order_id).'</td>
                    </tr>

                    <tr>
                        <td class="description"><strong>'.esc_html__("Customer", "on-the-bench").':</strong></td>
                        <td class="price">'.get_post_meta($order_id, "_customer_label", true).'</td>
                    </tr>';

                    if(!empty($customer_phone)) {
                        $content .= "<tr>";
                        $content .= "<td><strong>".esc_html__("Phone", "on-the-bench")." :</strong></td><td>".$customer_phone;	
                        $content .= "</td></tr>";
                    }
                    /*if(!empty($user_email)) {
                        $content .= "<tr>";
                        $content .= "<td><strong>".esc_html__("Email", "on-the-bench")." :</strong></td><td>".$user_email;	
                        $content .= "</td></tr>";
                    }
                    if(!empty($customer_company) || !empty($customer_zip) || !empty($customer_city) || !empty($customer_address)) {
                        $content .= "<tr>";
                        $content .= "<td><strong>".esc_html__("Address", "on-the-bench")." :</strong></td><td>";

                        $content .= !empty($customer_company) ? $customer_company.", " : " ";

                        $content .= !empty($customer_address) ? $customer_address.", " : " ";
                        $content .= !empty($customer_city) ? $customer_city.", " : " ";
                        $content .= !empty($customer_zip) ? $customer_zip : " ";
                        $content .= "</td></tr>";
                    }*/

                    $device_id 	        = get_post_meta($order_id, "_device_id", true);
                    $device_post_number = get_post_meta($order_id, "_device_post_id", true);


                    if(!empty($device_post_number)):
                        $content .= '<tr>
                                        <td><strong>'.esc_html__("Device", "on-the-bench").':</strong></td>
                                        <td class="price">'.return_device_label($device_post_number).'</td>
                                    </tr>';
                    endif;            

                    if(!empty($device_id)):
                        $content .= '<tr>
                                        <td><strong>'.esc_html__("ID/IMEI", "on-the-bench").':</strong></td>
                                        <td class="price">'.esc_html($device_id).'</td>
                                    </tr>';
                    endif;                                    

                    $content .= '<tr>
                                    <td><strong>'.esc_html__("Cost", "on-the-bench").':</strong></td>
                                    <td class="price">'.$system_currency.tofw_order_grand_total($order_id, "grand_total").'</td>
                                </tr>';

                    $case_detail = get_post_meta($order_id, "_case_detail", true);
                    
                    if(!empty($case_detail)):
                        $content .= '<tr>
                                    <td><strong>'.esc_html__("Problem", "on-the-bench").':</strong></td>
                                    <td>'.esc_html($case_detail).'</td>
                                    </tr>';                                
                    endif;                                    
                    
                    $tofw_business_terms = get_option("tofw_business_terms");

                    if(!empty($tofw_business_terms)):
                        $content .= '<tr>
                                        <td class="centered" colspan="2">
                                            <img src="https://chart.googleapis.com/chart?chs=177x177&cht=qr&chl='.esc_url($tofw_business_terms).'&choe=UTF-8" title="'.esc_html__("Scan to read policy", "on-the-bench").'" />
                                        </td>
                                    </td>
                                    ';

                        $content .= '<tr>
                                        <td class="centered" colspan="2"><strong>'.esc_html__("Scan for Terms and Conditions", "on-the-bench").'</strong></td>
                                    </tr>';

                        $content .= '<tr>
                                    <td class="centered" colspan="2"><strong>'.esc_html__("I agree to the terms and conditions.", "on-the-bench").'</strong></td>
                                </tr>'; 
                    endif;                                

                    $content .= '<tr class="signature">
                                    <td><strong>'.esc_html__("Signature", "on-the-bench").'</strong></td>
                                    <td>&nbsp;</td>
                                 </tr>';                             
                    
                    $content .= '<tr class="pickup">
                                    <td><strong>'.esc_html__("Picked Up", "on-the-bench").'</strong></td>
                                    <td>&nbsp;</td>
                                </tr>';     

                    $content .= '</tbody>
            </table>
            <p class="centered">'.esc_html__("Thank You for your trust!", "on-the-bench").'</p>
        </div>';

		$content .= '<button id="btnPrint" class="hidden-print button button-primary">'.esc_html__("Print", "on-the-bench").'</button>';
		$content .= '</div><!-- Invoice-box Ends /-->';

		return $content;

		}
	}