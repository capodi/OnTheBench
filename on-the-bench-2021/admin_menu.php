<?php
	// action function for above hook
	function tofw_add_otb_3_pages() {
		// main_sub Menu Page
		$menu_name_p 		= get_option("menu_name_p");
		
		if(empty($menu_name_p)) {
			$menu_name_p = esc_html__("On The Bench", "on-the-bench");
		}

		add_menu_page(
			$menu_name_p, 
			$menu_name_p, 'manage_options', 'tofw-on-the-2-bench-handle', 'tofw_on_the_1_bench_main', plugins_url('assets/admin/images/tofw-otb.png', __FILE__), '70');
		
/*CBA*/	add_submenu_page('tofw-on-the-2-bench-handle', __('Locations', 'on-the-bench'), __('Locations','on-the-bench'), 'edit_posts', 'edit.php?post_type=otb_locations');

		add_submenu_page('tofw-on-the-2-bench-handle', __('Services', 'on-the-bench'), __('Services', 'on-the-bench'), 'edit_posts' , 'edit.php?post_type=otb_services');

		if(is_parts_switch_woo() == true) {
			add_submenu_page('tofw-on-the-2-bench-handle', __('Products', 'on-the-bench'), __('Products', 'on-the-bench'), 'edit_posts' , 'edit.php?post_type=product');	
		} else {
			add_submenu_page('tofw-on-the-2-bench-handle', __('Parts', 'on-the-bench'), __('Parts', 'on-the-bench'), 'edit_posts' , 'edit.php?post_type=otb_products');
		}

		add_submenu_page('tofw-on-the-2-bench-handle', __('Devices', 'on-the-bench'), __('Devices','on-the-bench'), 'edit_posts', 'edit.php?post_type=otb_devices');

		add_submenu_page('tofw-on-the-2-bench-handle', __('Jobs', 'on-the-bench'), __('Jobs','on-the-bench'), 'edit_posts', 'edit.php?post_type=otb_jobs');
		
		add_submenu_page('edit.php?post_type=otb_jobs', __('Print Screen','on-the-bench'), __('Print Screen','on-the-bench'), 'read', 'tofw_on_the_bench_print', "tofw_on_the_bench_print_functionality");

		add_submenu_page('tofw-on-the-2-bench-handle', __('Clients','on-the-bench'), __('Clients','on-the-bench'), 'delete_posts', 'tofw-on-the-2-bench-clients', 'tofw_on_the_3_bench_clients');

		add_submenu_page('tofw-on-the-2-bench-handle', __('Technicians','on-the-bench'), __('Technicians','on-the-bench'), 'delete_posts', 'tofw-on-the-2-bench-technicians', 'tofw_on_the_3_bench_technicians');

/*CBA*/	add_submenu_page('tofw-on-the-2-bench-handle', __('Employees','on-the-bench'), __('Employees','on-the-bench'), 'manage_options', 'tofw-on-the-2-bench-employees', 'tofw_on_the_3_bench_shop_employee');
		
		add_submenu_page('tofw-on-the-2-bench-handle', __('Managers','on-the-bench'), __('Managers','on-the-bench'), 'manage_options', 'tofw-on-the-2-bench-managers', 'tofw_on_the_3_bench_shop_manager');
	
	}
	add_action('admin_menu', 'tofw_add_otb_3_pages');