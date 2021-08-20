<?php
	/*
	 * Repair Order Functionality
	 *	
	 * Function Returns repair Order 
	 * 
	 * Takes Order ID as an Argument.
	 */
    if(!function_exists("tofw_print_order_invoice")):
        function tofw_print_order_invoice($order_id, $post_type) {
            if(empty($order_id)) {
                return;
            }
            
            $tofw_use_taxes 	= get_option("tofw_use_taxes");
            //Let's do magic.
            $customer_id 	= get_post_meta($order_id, "_customer", true);
            $user 			= get_user_by('id', $customer_id);
            $user_email 	=  $user->user_email;
            
            $system_currency 	= get_option('tofw_system_currency');
            
            $content = '<div class="invoice-box">
            <table cellpadding="0" cellspacing="0">
                <tr class="top">
                    <td colspan="2">
                        <table>
                            <tr>
                                <td class="title">';
            if(!has_custom_logo()) { 
                $content .= '<h1 class="site-title">'.get_bloginfo( 'name' ).'</h1>';
            } else { 
                $on_the_bench_logo = get_option("on_the_bench_logo");
                
                $content .= '<img src="'.esc_url($on_the_bench_logo).'" style="width:auto; max-width:100%;height:60px;" />';
            }

            $content .= '</td>
                                <td class="invoice_headers">
                                    <strong>'.esc_html__("Order", "on-the-bench").' #:</strong> '.$order_id.'<br>
                                    <strong>'.esc_html__("Case Number", "on-the-bench").' :</strong> '.get_post_meta($order_id, "_case_number", true).'<br>
                                    <strong>'.esc_html__("Created", "on-the-bench").' :</strong> '.get_the_date('', $order_id).'<br>
                                    <strong>'.esc_html__("Payment Status", "on-the-bench").' :</strong> '.get_post_meta($order_id, "_tofw_payment_status_label", true).'<br>
                                    <strong>'.esc_html__("Order Status", "on-the-bench").' :</strong> '.get_post_meta($order_id, "_tofw_order_status_label", true).'
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                
                <tr class="information">
                    <td colspan="2">
                        <table class="invoice_headers">
                            <tr>
                                <td>
                                    '.get_post_meta($order_id, "_customer_label", true);

                                    $customer_phone  	= get_user_meta( $customer_id, 'customer_phone', true);
                                    $customer_address 	= get_user_meta( $customer_id, 'customer_address', true);
                                    $customer_city 		= get_user_meta( $customer_id, 'customer_city', true);
                                    $customer_zip		= get_user_meta( $customer_id, 'zip_code', true);
                                    $customer_company	= get_user_meta( $customer_id, 'company', true);

/*CBA*/                             $location_id 	      = get_post_meta($order_id, "_location_id", true);
/*CBA*/                             $location_post_number = get_post_meta($order_id, "_location_post_id", true);

                                    $device_id 	        = get_post_meta($order_id, "_device_id", true);
                                    $device_post_number = get_post_meta($order_id, "_device_post_id", true);
                            /*CBA        
                                    if(!empty($customer_phone)) {
                                        $content .= "<br><strong>".esc_html__("Phone", "on-the-bench")." :</strong> ".$customer_phone;	
                                    }
                                    if(!empty($user_email)) {
                                        $content .= "<br><strong>".esc_html__("Email", "on-the-bench")." :</strong> ".$user_email;	
                                    }
                             */       
                                    if(!empty($customer_company) || !empty($customer_zip) || !empty($customer_city) || !empty($customer_address)) {
                                        $content .= "<br><strong>".esc_html__("Address", "on-the-bench")." :</strong> ";

                                        $content .= !empty($customer_company) ? $customer_company.", " : " ";

                                        $content .= !empty($customer_address) ? $customer_address.", " : " ";
                                        $content .= !empty($customer_city) ? $customer_city.", " : " ";
                                        $content .= !empty($customer_zip) ? $customer_zip : " ";
                                    }

/*CBA*/                             if(!empty($location_post_number)):
                                        $content .= "<br><strong>".esc_html__("Location:", "on-the-bench")." </strong>".return_device_label($location_post_number);
                                    endif;

/*CBA*/                             if(!empty($location_id)):
                                        $content .= "<br><strong>".esc_html__("ID/LCTN:", "on-the-bench")." </strong>".esc_html($location_id);
                                    endif;
 
                                   if(!empty($device_post_number)):
                                        $content .= "<br><strong>".esc_html__("Device:", "on-the-bench")." </strong>".return_device_label($device_post_number);
                                    endif;

                                    if(!empty($device_id)):
                                        $content .= "<br><strong>".esc_html__("ID/IMEI:", "on-the-bench")." </strong>".esc_html($device_id);
                                    endif;

                            $content .= '
                                </td>
                            
                            <td>
                                '.get_bloginfo( 'name' ).'<br>
                                '.get_bloginfo( 'description' ).'
                            </td>
                            </tr>
                        </table>
                    </td>
                </tr>';	

                if(isset($post_type) && $post_type == "status_check") {
                    //Get File option
                    $tofw_file_attachment_in_job = get_option("tofw_file_attachment_in_job");
                    $meta_key 	= 'tofw_job_file';
                    $file_id 	=  get_post_meta($order_id, "_".$meta_key, true);
                    
                    if($tofw_file_attachment_in_job == "on" && !empty($file_id)) {
                        $content .= '<tr class="heading">';
                        $content .= '<th>'.esc_html__("File Attachment", "on-the-bench").'</th>';
                        $file_url = wp_get_attachment_url($file_id);
                        $content .= '<td><a href="'.esc_url($file_url).'" target="_blank">'.esc_html__("Click to view", "on-the-bench").'</a></td>';
                        $content .= '</tr>';
                    }
                }
                                    
                $content .= '<tr class="heading">
                    <td colspan="2">
                     '.esc_html__("Service/Repair Details", "on-the-bench").'
                    </td>
                </tr>
                
                <tr class="details">
                    <td colspan="2">
                        '.get_post_meta($order_id, "_case_detail", true).'
                    </td>
                </tr>
            </table>';
 
/*CBA*/      $order_notes = get_post_meta($order_id, '_tofw_order_note', true);
             $content .= '<table class="invoice_items"><tr><td>';
             if(!empty($order_notes)) {
               $content .= "<br><strong>".esc_html__("Notes", "on-the-bench")." :</strong> ".$order_notes;	
                   }
             $content .= '</td></tr></table>';

/*CBA     if(!empty(tofw_print_existing_parts($order_id))):
                $content .= '<table class="invoice-items">
                                <tr class="heading special_head">
                                    <td>'.esc_html__("Part Name", "on-the-bench").'</td>
                                    <td>'.esc_html__("Code", "on-the-bench").'</td>
                                    <td>'.esc_html__("Capacity", "on-the-bench").'</td>
                                    <td width="50">'.esc_html__("Qty", "on-the-bench").'</td>
                                    <td width="100">'.esc_html__("Price", "on-the-bench").'</td>';
                if($tofw_use_taxes == 'on'):
 //CBA              $content .= '<td>'.esc_html__("Tax (%)", "on-the-bench").'</td>';
                    $content .= '<td>'.esc_html__("Tax ($)", "on-the-bench").'</td>';	
                endif;
                $content	.= '<td>'.esc_html__("Total", "on-the-bench").'</td>
                                </tr>
                                '.tofw_print_existing_parts($order_id).'
                            </table>';
                $content .= '<div class="invoice_totals"><table><tr>';
                if($tofw_use_taxes == 'on'):
                    $content .= '<th>'.esc_html__("Parts Tax", "on-the-bench").'</th><td>'.$system_currency.tofw_order_grand_total($order_id, "parts_tax").'</td>';
                endif;
                $content .= '<th>'.esc_html__("Parts Total", "on-the-bench").'</th><td>'.$system_currency.tofw_order_grand_total($order_id, "parts_total").'</td>';
                $content .= '</tr></table></div>';
            endif;
*/
           
/*CBA       if(!empty(tofw_print_existing_products($order_id))):
                $content .= '<table class="invoice-items">
                                <tr class="heading special_head">
                                    <td>'.esc_html__("Product Name", "on-the-bench").'</td>
                                    <td>'.esc_html__("SKU", "on-the-bench").'</td>
                                    <td width="50">'.esc_html__("Qty", "on-the-bench").'</td>
                                    <td width="100">'.esc_html__("Price", "on-the-bench").'</td>';
                if($tofw_use_taxes == 'on'):
                    //CBA $content .= '<td>'.esc_html__("Tax (%)", "on-the-bench").'</td>';
                    $content .= '<td>'.esc_html__("Tax ($)", "on-the-bench").'</td>';	
                endif;

                $content	.= '<td>'.esc_html__("Total", "on-the-bench").'</td>
                                </tr>
                                '.tofw_print_existing_products($order_id).'
                            </table>';
                $content .= '<div class="invoice_totals"><table><tr>';
                if($tofw_use_taxes == 'on'):
                    $content .= '<th>'.esc_html__("Products Tax", "on-the-bench").'</th><td>'.$system_currency.tofw_order_grand_total($order_id, "products_tax").'</td>';
                endif;
                $content .= '<th>'.esc_html__("Products Total", "on-the-bench").'</th><td>'.$system_currency.tofw_order_grand_total($order_id, "products_total").'</td>';
                $content .= '</tr></table></div>';
            endif;
*/
            
/*CBA       if(!empty(tofw_print_existing_services($order_id))):
                $content .= '<table class="invoice-items">
                                <tr class="heading special_head">
                                    <td>'.esc_html__("Service Name", "on-the-bench").'</td>
                                    <td>'.esc_html__("Code", "on-the-bench").'</td>
                                    <td width="50">'.esc_html__("Qty", "on-the-bench").'</td>
                                    <td width="100">'.esc_html__("Price", "on-the-bench").'</td>';
                if($tofw_use_taxes == 'on'):
                    //CBA $content .= '<td>'.esc_html__("Tax (%)", "on-the-bench").'</td>';
                    $content .= '<td>'.esc_html__("Tax ($)", "on-the-bench").'</td>';	
                endif;

                $content .= '<td>'.esc_html__("Total", "on-the-bench").'</td>
                                </tr>
                                '.tofw_print_existing_services($order_id).'
                            </table>';
                $content .= '<div class="invoice_totals"><table><tr>';
                if($tofw_use_taxes == 'on'):
                    $content .= '<th>'.esc_html__("Services Tax", "on-the-bench").'</th><td>'.$system_currency.tofw_order_grand_total($order_id, "services_tax").'</td>';
                endif;
                $content .= '<th>'.esc_html__("Services Total", "on-the-bench").'</th><td>'.$system_currency.tofw_order_grand_total($order_id, "services_total").'</td>';
                $content .= '</tr></table></div>';
            endif;
*/
            
/*CBA       if(!empty(tofw_print_existing_extras($order_id))):
                $content .= '<table class="invoice-items">
                                <tr class="heading special_head">
                                    <td>'.esc_html__("Extra Name", "on-the-bench").'</td>
                                    <td>'.esc_html__("Code", "on-the-bench").'</td>
                                    <td width="50">'.esc_html__("Qty", "on-the-bench").'</td>
                                    <td width="100">'.esc_html__("Price", "on-the-bench").'</td>';
                if($tofw_use_taxes == 'on'):
                    //CBA $content .= '<td>'.esc_html__("Tax (%)", "on-the-bench").'</td>';
                    $content .= '<td>'.esc_html__("Tax ($)", "on-the-bench").'</td>';	
                endif;
                $content .= '<td>'.esc_html__("Total", "on-the-bench").'</td>
                                </tr>
                                '.tofw_print_existing_extras($order_id).'
                            </table>';
                
                $content .= '<div class="invoice_totals"><table><tr>';
                if($tofw_use_taxes == 'on'):
                    $content .= '<th>'.esc_html__("Extras Tax", "on-the-bench").'</th><td>'.$system_currency.tofw_order_grand_total($order_id, "extras_tax").'</td>';
                endif;
                $content .= '<th>'.esc_html__("Extras Total", "on-the-bench").'</th><td>'.$system_currency.tofw_order_grand_total($order_id, "extras_total").'</td>';
                $content .= '</tr></table></div>';
            endif;
*/
            
            $content .= '<div class="invoice_totals"><table>';
            $content .= '<tr><th>'.esc_html__("Service/Repair Total", "on-the-bench").'</th><td>'.$system_currency.tofw_order_grand_total($order_id, "grand_total").'</td></tr>';
            $content .= '</table></div>';
            $content .= "<p class='aligncenter'>Thank You for your trust!</p>";
            if(isset($post_type) && $post_type == "print"){
                $content .= '<button id="btnPrint" class="hidden-print button button-primary">'.esc_html__("Print", "on-the-bench").'</button>';
            }
            $content .= '</div>';
            
            
            return $content;
        }
    endif;    