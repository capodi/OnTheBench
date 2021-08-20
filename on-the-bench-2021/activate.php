<?php
	/*
		Default Status Data 

		Being used in both updated.php and active.php
	*/
	if(!function_exists("tofw_on_the_bench_default_status_data")):
		function tofw_on_the_bench_default_status_data() {
			global $wpdb;
			
			/*
				@Since 1.0
				Job Default Status 
				Default Data
			*/
			$on_the_bench_job_status = $wpdb->prefix.'tofw_otb_job_status';

			$result = $wpdb->get_results("SELECT `status_id` from `".$on_the_bench_job_status."` WHERE `status_id` IS NOT NULL");
			
			if(count($result) == 0) {
				$order_status = array(
					"new" 				=> esc_html__("New Order", "on-the-bench"),
					"quote" 			=> esc_html__("Quote", "on-the-bench"),
					"cancelled" 		=> esc_html__("Cancelled", "on-the-bench"),
					"inprocess" 		=> esc_html__("In Process", "on-the-bench"),
					"inservice" 		=> esc_html__("In Service", "on-the-bench"),
					"ready_complete" 	=> esc_html__("Ready/Complete", "on-the-bench"),
					"delivered"			=> esc_html__("Delivered", "on-the-bench")	
				);

				foreach( $order_status as $key=>$value ) {
					$wpdb->insert( 
						$on_the_bench_job_status, 
						array( 
							'status_id' 			=> "", 
							'status_name' 			=> $value, 
							'status_slug' 			=> $key,
							'status_description' 	=> "",
							'inventory_count'		=> "",
							'status_status' 		=> "active",
						)
					);
				}
			}
		}
	endif;


	//Installation of plugin starts here.
	function tofw_on_the_bench_install() {
		//Installs default values on activation.
		global $wpdb;
		require_once(ABSPATH .'wp-admin/includes/upgrade.php');
		
		update_option('offer_pic_de', 'on');
		
		update_option("tofw_ot_bench_version", tofw_ot_bench_VERSION);
		
		/*
			Add User Role
			
			@Role Customer
			
			@Since 1.0.0
		*/
		$tofw_role_existance = tofw_get_role("customer");
		
		if($tofw_role_existance == null) {
			add_role(
				'customer', 
				esc_html__('Customer', 'on-the-bench'), 
				array( 'read' => true, 'edit_posts' => false ) 
			);
		}

		$tofw_role_existance = tofw_get_role("technician");
		
		if($tofw_role_existance == null) {
			add_role(
				'technician', 
				esc_html__('Technician', 'on-the-bench'), 
				array( 'read' => true, 'edit_posts' => true, 'delete_posts' => false ) 
			);
		}

		$tofw_role_existance = tofw_get_role("shop_manager");
		
		if($tofw_role_existance == null) {
			add_role(
				'shop_manager', 
				esc_html__('Shop Manager', 'on-the-bench'), 
				array( 'read' => true, 'edit_posts' => true, 'delete_posts' => true ) 
			);
		}
		
		/*CBA*/	
		$tofw_role_existance = tofw_get_role("shop_employee");
		
		if($tofw_role_existance == null) {
			add_role(
				'shop_employee', 
				esc_html__('Shop Employee', 'on-the-bench'), 
				array( 'read' => true, 'edit_posts' => true, 'delete_posts' => true ) 
			);
		}
	
		
		/**
		 * Add Tables required
		 *
		 * @Since 2.0
		 */
		$on_the_bench_items 			= $wpdb->prefix.'tofw_otb_order_items';
		$on_the_bench_items_meta 	= $wpdb->prefix.'tofw_otb_order_itemmeta';
		$on_the_bench_taxes 			= $wpdb->prefix.'tofw_otb_taxes';
		$on_the_bench_job_status 	= $wpdb->prefix.'tofw_otb_job_status';
		
		
		$sql = 'CREATE TABLE '.$on_the_bench_items.'(
			`order_item_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`order_item_name` varchar(100) NOT NULL,
  			`order_item_type` varchar(50) NOT NULL,
			`order_id` bigint(20) NOT NULL,
  			PRIMARY KEY (`order_item_id`)
		)';	
		dbDelta($sql);
		
		
		$sql = 'CREATE TABLE '.$on_the_bench_items_meta.'(
			`meta_id` bigint(20) NOT NULL AUTO_INCREMENT,
			`order_item_id` bigint(20) NOT NULL,
  			`meta_key` varchar(250) NOT NULL,
			`meta_value` longtext NOT NULL,
  			PRIMARY KEY (`meta_id`),
			FOREIGN KEY (order_item_id) REFERENCES '.$on_the_bench_items.'(order_item_id)
		)';	
		dbDelta($sql);

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
		
		tofw_capability_shop_manager();
		
		tofw_on_the_bench_default_status_data();

	}//end of function tofw_OnTheBench_install()
	//'otb_locations' 'otb_location'
	//'otb_devices', 'otb_device'
	//'otb_services', 'otb_service'
	//'otb_products', 'otb_product'
	if(!function_exists("tofw_capability_shop_manager")):
		function tofw_capability_shop_manager() {
	
			// Add the roles you'd like to administer the custom post types
			$roles = array('shop_manager','editor','administrator', 'technician');
			
			// Loop through each role and assign capabilities
			foreach($roles as $the_role) { 
	
				$role = get_role($the_role);

				$role->add_cap( 'read' );

				if($this_role != "shop_manager") {
					//Repair Jobs
					$otb_jobs_cap = tofw_shop_manager_capabilities("otb_job", "otb_jobs");
					
					foreach($otb_jobs_cap as $capability_type) {
						$role->add_cap( $capability_type );	
					}

					//CBA: Locations
					$otb_locations_cap = tofw_shop_manager_capabilities("otb_location", "otb_locations");
					
					foreach($otb_locations_cap as $capability_type) {
						$role->add_cap( $capability_type );	
					}

					//Devices
					$otb_devices_cap = tofw_shop_manager_capabilities("otb_device", "otb_devices");
					
					foreach($otb_devices_cap as $capability_type) {
						$role->add_cap( $capability_type );	
					}

					//Services
					$otb_services_cap = tofw_shop_manager_capabilities("otb_service", "otb_services");

					foreach($otb_services_cap as $capability_type) {
						$role->add_cap( $capability_type );	
					}

					//Parts
					$otb_parts_cap = tofw_shop_manager_capabilities("otb_product", "otb_products");

					foreach($otb_parts_cap as $capability_type) {
						$role->add_cap( $capability_type );	
					}
				}

/*CBA bgn */		if($this_role != "shop_employee") {
					//Repair Jobs
					$otb_jobs_cap = tofw_shop_employee_capabilities("otb_job", "otb_jobs");
					
					foreach($otb_jobs_cap as $capability_type) {
						$role->add_cap( $capability_type );	
					}

					//Locations
					$otb_locations_cap = tofw_shop_employee_capabilities("otb_location", "otb_locations");
					
					foreach($otb_locations_cap as $capability_type) {
						$role->add_cap( $capability_type );	
					}

					//Devices
					$otb_devices_cap = tofw_shop_employee_capabilities("otb_device", "otb_devices");
					
					foreach($otb_devices_cap as $capability_type) {
						$role->add_cap( $capability_type );	
					}

					//Services
					$otb_services_cap = tofw_shop_employee_capabilities("otb_service", "otb_services");

					foreach($otb_services_cap as $capability_type) {
						$role->add_cap( $capability_type );	
					}

					//Parts
					$otb_parts_cap = tofw_shop_employee_capabilities("otb_product", "otb_products");

					foreach($otb_parts_cap as $capability_type) {
						$role->add_cap( $capability_type );	
					}
/*CBA end */	}


				if($this_role != "technician") {
					//Repair Jobs
					$otb_jobs_cap = tofw_technician_capabilities("otb_job", "otb_jobs");
					
					foreach($otb_jobs_cap as $capability_type) {
						$role->add_cap( $capability_type );	
					}

					//CBA: Locations
					$otb_locations_cap = tofw_technician_capabilities("otb_location", "otb_locations");
					
					foreach($otb_locations_cap as $capability_type) {
						$role->add_cap( $capability_type );	
					}

					//Devices
					$otb_devices_cap = tofw_technician_capabilities("otb_device", "otb_devices");
					
					foreach($otb_devices_cap as $capability_type) {
						$role->add_cap( $capability_type );	
					}

					//Services
					$otb_services_cap = tofw_technician_capabilities("otb_service", "otb_services");

					foreach($otb_services_cap as $capability_type) {
						$role->add_cap( $capability_type );	
					}

					//Parts
					$otb_parts_cap = tofw_technician_capabilities("otb_product", "otb_products");

					foreach($otb_parts_cap as $capability_type) {
						$role->add_cap( $capability_type );	
					}
				}

				if($the_role == "administrator" || $the_role == "editor"):
					
					//Repair Jobs
					$otb_jobs_cap = tofw_admin_capabilities("otb_job", "otb_jobs");
					
					foreach($otb_jobs_cap as $capability_type) {
						$role->add_cap( $capability_type );	
					}

					//CBA: Locations
					$otb_locations_cap = tofw_admin_capabilities("otb_location", "otb_locations");
					
					foreach($otb_locations_cap as $capability_type) {
						$role->add_cap( $capability_type );	
					}

					//Devices
					$otb_devices_cap = tofw_admin_capabilities("otb_device", "otb_devices");
					
					foreach($otb_devices_cap as $capability_type) {
						$role->add_cap( $capability_type );	
					}

					//Services
					$otb_services_cap = tofw_admin_capabilities("otb_service", "otb_services");

					foreach($otb_services_cap as $capability_type) {
						$role->add_cap( $capability_type );	
					}

					//Parts
					$otb_parts_cap = tofw_admin_capabilities("otb_product", "otb_products");

					foreach($otb_parts_cap as $capability_type) {
						$role->add_cap( $capability_type );	
					}
				endif;
			}
		}
	endif;	

	if(!function_exists("tofw_admin_capabilities")): 
		function tofw_admin_capabilities($singular = 'post', $plural = 'posts') {
			return [
				'edit_post'      		=> "edit_$singular",
				'read_post'      		=> "read_$singular",
				'delete_post'        	=> "delete_$singular",
				'edit_posts'         	=> "edit_$plural",
				'edit_others_posts'  	=> "edit_others_$plural",
				'publish_posts'      	=> "publish_$plural",
				'read_private_posts'     => "read_private_$plural",
				'delete_posts'           => "delete_$plural",
				'delete_private_posts'   => "delete_private_$plural",
				'delete_published_posts' => "delete_published_$plural",
				'delete_others_posts'    => "delete_others_$plural",
				'edit_private_posts'     => "edit_private_$plural",
				'edit_published_posts'   => "edit_published_$plural",
				'create_posts'           => "edit_$plural",
			];
		}
	endif;	

	if(!function_exists("tofw_technician_capabilities")):
		function tofw_technician_capabilities($singular = 'post', $plural = 'posts') {
			return [
				'edit_post'      		=> "edit_$singular",
				'read_post'      		=> "read_$singular",
				'edit_posts'         	=> "edit_$plural",
				'edit_others_posts'  	=> "edit_others_$plural",
				'publish_posts'      	=> "publish_$plural",
				'read_private_posts'     => "read_private_$plural",
				'edit_private_posts'     => "edit_private_$plural",
				'edit_published_posts'   => "edit_published_$plural",
				'create_posts'           => "edit_$plural",
			];
		}
	endif;

	if(!function_exists("tofw_shop_manager_capabilities")):
		function tofw_shop_manager_capabilities($singular = 'post', $plural = 'posts') {
			return [
				'edit_post'      		=> "edit_$singular",
				'read_post'      		=> "read_$singular",
				'edit_posts'         	=> "edit_$plural",
				'edit_others_posts'  	=> "edit_others_$plural",
				'publish_posts'      	=> "publish_$plural",
				'read_private_posts'     => "read_private_$plural",
				'edit_private_posts'     => "edit_private_$plural",
				'edit_published_posts'   => "edit_published_$plural",
				'create_posts'           => "edit_$plural",
			];
		}
	endif;
	
/*CBA*/	if(!function_exists("tofw_shop_employee_capabilities")):
		function tofw_shop_employee_capabilities($singular = 'post', $plural = 'posts') {
			return [
				'edit_post'      		=> "edit_$singular",
				'read_post'      		=> "read_$singular",
				'edit_posts'         	=> "edit_$plural",
				'edit_others_posts'  	=> "edit_others_$plural",
				'publish_posts'      	=> "publish_$plural",
				'read_private_posts'     => "read_private_$plural",
				'edit_private_posts'     => "edit_private_$plural",
				'edit_published_posts'   => "edit_published_$plural",
				'create_posts'           => "edit_$plural",
			];
		}
	endif;	