<?php
	if(!defined("tofw_ot_bench_VERSION")) {
		esc_html_e("Something is wrong please check your url.", "on-the-bench");
		exit();
	}

	/*
	 * Repair Order
	 * 
	 * Print Functionality
	 * 
	 * @Since 3.50
	 */
	require_once(tofw_on_the_bench_DIR.'lib'.DS.'includes'.DS.'reports'.DS.'repair_order.php');
	
	/*
	 * Repair Label
	 * 
	 * Print Functionality
	 * 
	 * @Since 3.52
	 */
	require_once(tofw_on_the_bench_DIR.'lib'.DS.'includes'.DS.'reports'.DS.'repair_label.php');


	/*
	 * A4 Size Invoice Print
	 * 
	 * Print invoice Functionality
	 * 
	 * @Since 1.0
	 */
	require_once(tofw_on_the_bench_DIR.'lib'.DS.'includes'.DS.'reports'.DS.'large_invoice.php');