<?php
	function tofw_on_the_bench_print_functionality() {
		if (!current_user_can('read')) {
		  wp_die( esc_html__('You do not have sufficient permissions to access this page.', "on-the-bench") );
		}
		
		if(isset($_GET["order_id"]) && !empty($_GET["order_id"])) {
			if(isset($_GET["email_customer"]) && !empty($_GET["email_customer"])) {
				tofw_otb_send_customer_update_email($_GET["order_id"]);

				echo "<h2>".esc_html__("The email have been sent to the customer.", "on-the-bench")."</h2>";
			}

			if(isset($_GET["print_type"]) && $_GET["print_type"] == "repair_order") {
				//Repair Order to print.
				echo tofw_print_repair_order($_GET["order_id"]);
			} elseif($_GET["print_type"] == "repair_label") {
				//Repair label to print.
				echo tofw_print_repair_label($_GET["order_id"]);	
			} else {
				//Let's call or Print our order Invoice Here.
				echo tofw_print_order_invoice($_GET["order_id"], "print");
			}
		}
		
	}//add category function ends here.