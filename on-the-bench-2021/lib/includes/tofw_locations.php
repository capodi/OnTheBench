<?php
	function tofw_on_the_1_bench_locations_init() {
		$labels = array(
			'add_new_item' 			=> esc_html__('Add new location', 'on-the-bench'),
			'singular_name' 		=> esc_html__('Location', 'on-the-bench'), 
			'menu_name' 			=> esc_html__('Locations', 'on-the-bench'),
			'all_items' 			=> esc_html__('Locations', 'on-the-bench'),
			'edit_item' 			=> esc_html__('Edit Location', 'on-the-bench'),
			'new_item' 				=> esc_html__('New Location', 'on-the-bench'),
			'view_item' 			=> esc_html__('View Location', 'on-the-bench'),
			'search_items' 			=> esc_html__('Search Location', 'on-the-bench'),
			'not_found' 			=> esc_html__('No location found', 'on-the-bench'),
			'not_found_in_trash' 	=> esc_html__('No location in trash', 'on-the-bench')
		);
		
		$args = array(
			'labels'             	=> $labels,
			'label'					=> esc_html__("Locations", "on-the-bench"),
			'description'        	=> esc_html__('Locations Section', 'on-the-bench'),
			'public'             	=> false,
			'publicly_queryable' 	=> true,
			'show_ui'            	=> true,
			'show_in_menu'       	=> false,
			'query_var'          	=> true,
			'rewrite'            	=> array('slug' => 'location'),
			'capability_type'    	=> array('otb_location', 'otb_locations'),
			'has_archive'        	=> true,
			'menu_icon'			 	=> 'dashicons-clipboard',
			'menu_position'      	=> 30,
			'supports'           	=> array( 'title', 'editor'), 	
			'register_meta_box_cb' 	=> 'tofw_locations_features',
			'taxonomies' 			=> array('location_brand')
		);
		register_post_type('otb_locations', $args);
	}
	add_action( 'init', 'tofw_on_the_1_bench_locations_init');
	//registeration of post type ends here.

	add_action( 'init', 'tofw_create_location_brand');
	function tofw_create_location_brand() {
		$labels = array(
			'name'              => esc_html__('Location Brands', 'on-the-bench'),
			'singular_name'     => esc_html__('Location Brand', 'on-the-bench'),
			'search_items'      => esc_html__('Search Location Brands', 'on-the-bench'),
			'all_items'         => esc_html__('All Location Brands', 'on-the-bench'),
			'parent_item'       => esc_html__('Parent Location Brand', 'on-the-bench'),
			'parent_item_colon' => esc_html__('Parent Location Brand:', 'on-the-bench'),
			'edit_item'         => esc_html__('Edit Location Brand', 'on-the-bench'),
			'update_item'       => esc_html__('Update Location Brand', 'on-the-bench'),
			'add_new_item'      => esc_html__('Add New Location Brand', 'on-the-bench'),
			'new_item_name'     => esc_html__('New Brand Name', 'on-the-bench'),
			'menu_name'         => esc_html__('Location Brand', 'on-the-bench')
		);
		
		$args = array(
				'label' => __( 'Location Brand', "on-the-bench"),
				'rewrite' => array('slug' => 'location-brand'),
				'public' => true,
				'labels' => $labels,
				'hierarchical' => true,
				'show_admin_column' => true,	
		);
		
		register_taxonomy(
			'location_brand',
			'otb_locations',
			$args
		);
	}
	//Registration of Taxanomy Ends here. 