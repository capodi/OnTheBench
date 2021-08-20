<?php
    //Include Shortcode Files.

    include_once(tofw_on_the_bench_DIR.'lib'.DS.'shortcodes'.DS.'list_products.php');
    include_once(tofw_on_the_bench_DIR.'lib'.DS.'shortcodes'.DS.'list_services.php');
    include_once(tofw_on_the_bench_DIR.'lib'.DS.'shortcodes'.DS.'order_status.php');
    include_once(tofw_on_the_bench_DIR.'lib'.DS.'shortcodes'.DS.'request_quote.php');
    include_once(tofw_on_the_bench_DIR.'lib'.DS.'shortcodes'.DS.'my_account.php');

    /**
     * Start Job
     * Front End
     * 
     * Selecting location
     * @popup
     * @Since 3.53
     */
    include_once(tofw_on_the_bench_DIR.'lib'.DS.'shortcodes'.DS.'start_job_by_location.php');


    /**
     * Register Scripts
     * Register Styles
     * 
     * To Enque within Shortcodes 
     */
    if(!function_exists("tofw_otb_3_register_foundation")):
        function tofw_otb_3_register_foundation() {
            wp_register_script('foundation-js', tofw_on_the_bench_DIR_URL.'/assets/admin/js/foundation.min.js', array('jquery'), '6.5.3', true);
            wp_register_script('tofw-cr-js', tofw_on_the_bench_DIR_URL.'/assets/js/tofw_otb_scripts.js', array('jquery'), tofw_ot_bench_VERSION, true);

            wp_register_style('select2', tofw_on_the_bench_DIR_URL.'/assets/admin/css/select2.min.css', array(),'4.0.13','all');
            wp_register_script('select2', tofw_on_the_bench_DIR_URL.'/assets/admin/js/select2.min.js', array('jquery'),  '4.0.13', true);
        }//end of adding styles and scripts for wordpress admin.
        add_action( 'init', 'tofw_otb_3_register_foundation' );
    endif; 