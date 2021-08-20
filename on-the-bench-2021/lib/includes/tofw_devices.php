<?php
	function tofw_on_the_1_bench_devices_init() {
		$labels = array(
			'add_new_item' 			=> esc_html__('Add new device', 'on-the-bench'),
			'singular_name' 		=> esc_html__('Device', 'on-the-bench'), 
			'menu_name' 			=> esc_html__('Devices', 'on-the-bench'),
			'all_items' 			=> esc_html__('Devices', 'on-the-bench'),
			'edit_item' 			=> esc_html__('Edit Device', 'on-the-bench'),
			'new_item' 				=> esc_html__('New Device', 'on-the-bench'),
			'view_item' 			=> esc_html__('View Device', 'on-the-bench'),
			'search_items' 			=> esc_html__('Search Device', 'on-the-bench'),
			'not_found' 			=> esc_html__('No device found', 'on-the-bench'),
			'not_found_in_trash' 	=> esc_html__('No device in trash', 'on-the-bench')
		);
		
		$args = array(
			'labels'             	=> $labels,
			'label'					=> esc_html__("Devices", "on-the-bench"),
			'description'        	=> esc_html__('Devices Section', 'on-the-bench'),
			'public'             	=> false,
			'publicly_queryable' 	=> true,
			'show_ui'            	=> true,
			'show_in_menu'       	=> false,
			'query_var'          	=> true,
			'rewrite'            	=> array('slug' => 'device'),
			'capability_type'    	=> array('otb_device', 'otb_devices'),
			'has_archive'        	=> true,
			'menu_icon'			 	=> 'dashicons-clipboard',
			'menu_position'      	=> 30,
			'supports'           	=> array( 'title', 'editor'), 	
			'register_meta_box_cb' 	=> 'tofw_device_features',
			'taxonomies' 			=> array('device_brand')
		);
		register_post_type('otb_devices', $args);
	}
	add_action( 'init', 'tofw_on_the_1_bench_devices_init');
	//registeration of post type ends here.

	add_action( 'init', 'tofw_create_device_brand');
	function tofw_create_device_brand() {
		$labels = array(
			'name'              => esc_html__('Device Brands', 'on-the-bench'),
			'singular_name'     => esc_html__('Device Brand', 'on-the-bench'),
			'search_items'      => esc_html__('Search Device Brands', 'on-the-bench'),
			'all_items'         => esc_html__('All Device Brands', 'on-the-bench'),
			'parent_item'       => esc_html__('Parent Device Brand', 'on-the-bench'),
			'parent_item_colon' => esc_html__('Parent Device Brand:', 'on-the-bench'),
			'edit_item'         => esc_html__('Edit Device Brand', 'on-the-bench'),
			'update_item'       => esc_html__('Update Device Brand', 'on-the-bench'),
			'add_new_item'      => esc_html__('Add New Device Brand', 'on-the-bench'),
			'new_item_name'     => esc_html__('New Brand Name', 'on-the-bench'),
			'menu_name'         => esc_html__('Device Brand', 'on-the-bench')
		);
		
		$args = array(
				'label' => __( 'Device Brand', "on-the-bench"),
				'rewrite' => array('slug' => 'device-brand'),
				'public' => true,
				'labels' => $labels,
				'hierarchical' => true,
				'show_admin_column' => true,	
		);
		
		register_taxonomy(
			'device_brand',
			'otb_devices',
			$args
		);
	}
	//Registration of Taxanomy Ends here. 