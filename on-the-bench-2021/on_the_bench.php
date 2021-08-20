<?php
	/*
		Plugin Name: On The Bench
		Plugin URI: https://www.theoldfashionway.com/
		Description: WordPress OTB Plugin which helps you manage your jobs, parts, services and extras better client and jobs management system.
		Version: 1.00
		Author: CapoDi Business Associates
		Author URI: https://www.hardwoodsound.com/
		License: GPLv2 or later.
		Text Domain: on-the-bench
		Domain Path: languages
	*/

	if(!defined('DS')) {
		define('DS','/'); //Defining Directory seprator, not using php default Directory seprator to avoide problem in windows.
	}

	define("tofw_ot_bench_VERSION", "1.00");

	if(!function_exists("tofw_language_plugin_init")):
		function tofw_language_plugin_init() {
			load_plugin_textdomain( 'on-the-bench', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
		}
		add_action( 'plugins_loaded', 'tofw_language_plugin_init' );
	endif;

	//Define folder name.
	define('tofw_on_the_bench_FOLDER', dirname(plugin_basename(__FILE__)));
	define('tofw_on_the_bench_DIR', plugin_dir_path( __FILE__ ));
	
	define( 'tofw_on_the_bench_DIR_URL', plugins_url('', __FILE__ ) );

	require_once(tofw_on_the_bench_DIR.'activate.php');
	require_once(tofw_on_the_bench_DIR.'lib'.DS.'includes'.DS.'update.php');

	register_activation_hook(__FILE__, 'tofw_on_the_bench_install'); //plugin activation hook.


	require_once(tofw_on_the_bench_DIR.'admin_menu.php'); //include admin menu file.

	//admin pages starts here.
	require_once(tofw_on_the_bench_DIR.'lib'.DS.'includes'.DS.'main_page.php');
	require_once(tofw_on_the_bench_DIR.'lib'.DS.'includes'.DS.'tofw_services.php');
	require_once(tofw_on_the_bench_DIR.'lib'.DS.'includes'.DS.'tofw_products.php');
/*CBA*/	require_once(tofw_on_the_bench_DIR.'lib'.DS.'includes'.DS.'tofw_locations.php');
	require_once(tofw_on_the_bench_DIR.'lib'.DS.'includes'.DS.'tofw_devices.php');
	require_once(tofw_on_the_bench_DIR.'lib'.DS.'includes'.DS.'tofw_jobs.php');
	require_once(tofw_on_the_bench_DIR.'lib'.DS.'includes'.DS.'tofw_clients.php');
	require_once(tofw_on_the_bench_DIR.'lib'.DS.'includes'.DS.'tofw_technicians.php');
	require_once(tofw_on_the_bench_DIR.'lib'.DS.'includes'.DS.'tofw_managers.php');
	require_once(tofw_on_the_bench_DIR.'lib'.DS.'includes'.DS.'tofw_print_functionality.php');
	require_once(tofw_on_the_bench_DIR.'lib'.DS.'includes'.DS.'reports'.DS.'load.php');
	require_once(tofw_on_the_bench_DIR.'lib'.DS.'includes'.DS.'tofw_employees.php');
	require_once(tofw_on_the_bench_DIR.'lib'.DS.'includes'.DS.'theme_functions.php');
	require_once(tofw_on_the_bench_DIR.'lib'.DS.'shortcodes'.DS.'shortcodes.php');   //Devices
	require_once(tofw_on_the_bench_DIR.'lib'.DS.'shortcodes'.DS.'shortcodes_l.php'); //Locations

	//admin pages ends here.

	//Adding styles and scripts for wordpress frontEnd.
	add_action('wp_enqueue_scripts', 'tofw_cmp_otb_ser_front_scripts');
	function tofw_cmp_otb_ser_front_scripts() {
		global $post;
		
		// enqueue foundation.min
		wp_enqueue_style('foundation-css', plugins_url('assets/css/foundation.min.css', __FILE__), array(), '6.5.3', 'all');

		wp_enqueue_style('plugin-styles-wc', plugins_url('assets/css/style.css', __FILE__ ), array(), tofw_ot_bench_VERSION,'all');
	}//end of adding styles and scripts for wordpress admin.
	


	//adding styles and scripts for worpress admin
	if(!function_exists("tofw_cmp_otb_ser_admin_scripts")) :
		function tofw_cmp_otb_ser_admin_scripts() {
			global $pagenow;

			$current_page = get_current_screen();

			if(
				($current_page->post_type === 'otb_jobs' && ($pagenow === 'post-new.php' || $pagenow === 'post.php' || $pagenow === 'edit.php')) ||
				($current_page->post_type === 'otb_products' && ($pagenow === 'post-new.php' || $pagenow === 'post.php')) ||
				($current_page->post_type === 'otb_services' && ($pagenow === 'post-new.php' || $pagenow === 'post.php')) ||
				(isset($_GET["page"]) && 
				($_GET["page"] === 'tofw-on-the-2-bench-handle' || 
				$_GET["page"] === 'tofw_on_the_bench_print' || 
				$_GET["page"] === 'tofw-on-the-2-bench-reports' ||
				$_GET["page"] === 'tofw-on-the-2-bench-managers' ||
/*CBA*/			$_GET["page"] === 'tofw-on-the-2-bench-employees' ||
				$_GET["page"] === 'tofw-on-the-2-bench-technicians' ||
				$_GET["page"] === "tofw-on-the-2-bench-clients"))
			) { 
				if($pagenow !== 'edit.php') {
					//Foundation CSS enque
					wp_register_style('foundation-css', plugins_url('assets/admin/css/foundation.min.css', __FILE__ ), array(), '6.5.3', 'all', true);
					wp_enqueue_style( 'foundation-css' );

					wp_enqueue_style('tofw-admin-style', plugins_url('assets/admin/css/style.css', __FILE__ ), array(),'1.0','all');
				}
				
				//Admin styles enque
				wp_enqueue_style('select2', plugins_url('assets/admin/css/select2.min.css', __FILE__ ), array(),'4.0.13','all');

				//Admin JS enque
				wp_enqueue_script('foundation-js', plugins_url('assets/admin/js/foundation.min.js', __FILE__ ), array('jquery'), '6.5.3', true);
				
				wp_enqueue_script('select2', plugins_url('assets/admin/js/select2.min.js', __FILE__ ), array('jquery'),  '4.0.13', true);
				wp_enqueue_script('tofw-js', plugins_url('assets/admin/js/my-admin.js', __FILE__ ), array('jquery'),  tofw_ot_bench_VERSION, true);

				$tofw_file_attachment_in_job = get_option("tofw_file_attachment_in_job");
				
				if($tofw_file_attachment_in_job == "on") {
					if($current_page->post_type === 'otb_jobs') {
						wp_enqueue_script('tofw-file-js', plugins_url('assets/admin/js/file_upload.js', __FILE__ ), array('jquery'),  tofw_ot_bench_VERSION, true);		
						if ( ! did_action( 'wp_enqueue_media' ) ) {
							wp_enqueue_media();
						}
					}
				}
			}

		}//end of adding styles and scripts for wordpress admin.
		add_action('admin_enqueue_scripts', 'tofw_cmp_otb_ser_admin_scripts', 1);
	endif;

	//Ajax Script Enque
	if(!function_exists("tofw_cmp_otb_ajax_script_enqueue")):
		function tofw_cmp_otb_ajax_script_enqueue() {
			wp_enqueue_script( 'ajax_script', plugin_dir_url(__FILE__ ).'assets/js/ajax_scripts.js', array('jquery'), '1.0', true );
			wp_localize_script( 'ajax_script', 'ajax_obj', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		 }
		 add_action('wp_enqueue_scripts', 'tofw_cmp_otb_ajax_script_enqueue');
	endif;