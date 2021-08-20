<?php
	$current_plugin_version = get_option("tofw_ot_bench_version");

	//Installation of plugin starts here.
	if(!function_exists("tofw_on_the_bench_update")):
		function tofw_on_the_bench_update() {
			//Installs default values on activation.
			global $wpdb;
			require_once(ABSPATH .'wp-admin/includes/upgrade.php');
		
			$on_the_bench_taxes 			= $wpdb->prefix.'tofw_otb_taxes';
			$on_the_bench_job_status 	= $wpdb->prefix.'tofw_otb_job_status';
			
			/*
				@Since 2.5

				Reactivate the Plugin required
			*/
			$sql = 'CREATE TABLE '.$on_the_bench_taxes.'(
				`tax_id` bigint(20) NOT NULL AUTO_INCREMENT,
				`tax_name` varchar(250) NOT NULL,
				`tax_description` varchar(250) NOT NULL,
				`tax_rate` varchar(50) NOT NULL,
				`tax_status` varchar(20) NOT NULL,
				PRIMARY KEY (`tax_id`)
			)';	
			dbDelta($sql);


			/*
				@Since 3.1

				Reactivate the Plugin required
			*/
			$sql = 'CREATE TABLE '.$on_the_bench_job_status.'(
				`status_id` bigint(20) NOT NULL AUTO_INCREMENT,
				`status_name` varchar(250) NOT NULL,
				`status_slug` varchar(250) NOT NULL,
				`status_description` varchar(250) NOT NULL,
				`status_email_message` varchar(600) NOT NULL,
				`inventory_count` varchar(20) NOT NULL,
				`status_status` varchar(20) NOT NULL,
				PRIMARY KEY (`status_id`)
			)';	
			dbDelta($sql);
			
			$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
				WHERE table_name = `".$on_the_bench_job_status."` AND column_name = `inventory_count`" );

			if(empty($row)){
				$wpdb->query("ALTER TABLE `".$on_the_bench_job_status."` ADD `inventory_count` varchar(20) NOT NULL AFTER `status_description`");
			}

			$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
				WHERE table_name = `".$on_the_bench_job_status."` AND column_name = `status_email_message`" );

			if(empty($row)){
				$wpdb->query("ALTER TABLE `".$on_the_bench_job_status."` ADD `status_email_message` varchar(600) NOT NULL AFTER `status_description`");
			}

			//Declared in active.php
			tofw_on_the_bench_default_status_data();

		}//end of function tofw_OnTheBench_install()
	endif;	


	/*
		check Update status and run functions
	*/
	if(	empty($current_plugin_version) || $current_plugin_version != tofw_ot_bench_VERSION) {
		tofw_on_the_bench_update();
		update_option("tofw_ot_bench_version", tofw_ot_bench_VERSION);
	}