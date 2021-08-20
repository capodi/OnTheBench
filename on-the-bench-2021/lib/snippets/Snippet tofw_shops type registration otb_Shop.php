<?php
	function tofw_on_the_1_bench_shops_init() {
		$labels = array(
			'add_new_item' 			=> esc_html__('Add new shop', 'on-the-bench'),
			'singular_name' 		=> esc_html__('Shop', 'on-the-bench'), 
			'menu_name' 			=> esc_html__('Shops', 'on-the-bench'),
			'all_items' 			=> esc_html__('Shops', 'on-the-bench'),
			'edit_item' 			=> esc_html__('Edit shop', 'on-the-bench'),
			'new_item' 				=> esc_html__('New shop', 'on-the-bench'),
			'view_item' 			=> esc_html__('View shop', 'on-the-bench'),
			'search_items' 			=> esc_html__('Search shop', 'on-the-bench'),
			'not_found' 			=> esc_html__('No shop Related to your search', 'on-the-bench'),
			'not_found_in_trash' 	=> esc_html__('No shop in trash', 'on-the-bench')
		);

		$args = array(
			'labels'             	=> $labels,
			'label'					=> esc_html__("Shops", 'on-the-bench'),
			'description'        	=> esc_html__('Every shop have its own manager who have access to all jobs, technicians, clients of that shop. While admin can view all shops and its data.', 'on-the-bench'),
			'public'             	=> true,
			'publicly_queryable' 	=> true,
			'show_ui'            	=> true,
			'show_in_menu'       	=> false,
			'query_var'          	=> true,
			'rewrite'            	=> array('slug' => 'otb_shop'),
			'capability_type'    	=> 'post',
			'has_archive'        	=> true,
			'menu_icon'			 	=> 'dashicons-clipboard',
			'menu_position'      	=> 30,
			'supports'           	=> array( 'title'), 	
			'register_meta_box_cb' 	=> 'tofw_shop_features',
		);
		
		register_post_type('otb_shop', $args);
	}
	add_action('init', 'tofw_on_the_1_bench_shops_init');
	//registeration of post type ends here.


