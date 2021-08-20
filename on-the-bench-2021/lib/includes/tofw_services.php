<?php
function tofw_on_the_1_bench_services_init() {
    $labels = array(
		'add_new_item' 			=> esc_html__('Add new Service', 'on-the-bench'),
		'singular_name' 		=> esc_html__('Service', 'on-the-bench'), 
		'menu_name' 			=> esc_html__('Services', 'on-the-bench'),
		'all_items' 			=> esc_html__('Services', 'on-the-bench'),
		'edit_item' 			=> esc_html__('Edit Service', 'on-the-bench'),
		'new_item' 				=> esc_html__('New Service', 'on-the-bench'),
		'view_item' 			=> esc_html__('View Service', 'on-the-bench'),
		'search_items' 			=> esc_html__('Search Services', 'on-the-bench'),
		'not_found' 			=> esc_html__('No service found', 'on-the-bench'),
		'not_found_in_trash' 	=> esc_html__('No service in trash', 'on-the-bench')
	);

	$args = array(
		'labels'             	=> $labels,
		'label'					=> esc_html__("Services", 'on-the-bench'),
		'description'        	=> esc_html__('Services Section', 'on-the-bench'),
		'public'             	=> true,
		'publicly_queryable' 	=> true,
		'show_ui'            	=> true,
		'show_in_menu'       	=> false,
		'query_var'          	=> true,
		'rewrite'            	=> array('slug' => 'services'),
		'capability_type'    	=> array('otb_service', 'otb_services'),
		'has_archive'        	=> true,
		'menu_icon'			 	=> 'dashicons-clipboard',
		'menu_position'      	=> 30,
		'supports'           	=> array( 'title', 'editor', 'thumbnail'), 	
	  	'register_meta_box_cb' 	=> 'tofw_service_features',
		'taxonomies' 			=> array('service_type')
    );
	
    register_post_type('otb_services', $args);
}
add_action('init', 'tofw_on_the_1_bench_services_init');
//registeration of post type ends here.

add_action( 'init', 'tofw_create_service_tax_type');
function tofw_create_service_tax_type() {
    $labels = array(
		'name'              => esc_html__('Service Types', 'on-the-bench'),
		'singular_name'     => esc_html__('Service Type', 'on-the-bench'),
		'search_items'      => esc_html__('Search Service Types', 'on-the-bench'),
		'all_items'         => esc_html__('All Service Types', 'on-the-bench'),
		'parent_item'       => esc_html__('Parent Type', 'on-the-bench'),
		'parent_item_colon' => esc_html__('Parent Type:', 'on-the-bench'),
		'edit_item'         => esc_html__('Edit Service Type', 'on-the-bench'),
		'update_item'       => esc_html__('Update Service Type', 'on-the-bench'),
		'add_new_item'      => esc_html__('Add New Service Type', 'on-the-bench'),
		'new_item_name'     => esc_html__('New Service Type Name', 'on-the-bench'),
		'menu_name'         => esc_html__('Service Type', 'on-the-bench')
	);
	
	$args = array(
			'label' 		=> esc_html__( 'Service Type', "on-the-bench"),
			'rewrite' 		=> array('slug' => 'service_type'),
			'public' 		=> true,
			'labels' 		=> $labels,
			'hierarchical' 	=> true,
			'show_ui'       => true,
			'query_var'     => true,
	);
	
	register_taxonomy(
        'service_type',
        'otb_services',
		$args
    );
}
//Registration of Taxanomy Ends here.

function tofw_service_features() { 
	$screens = array('otb_services');

	foreach ( $screens as $screen ) {
		add_meta_box(
			'myplugin_sectionid',
			'Service Details',
			'tofw_services_features_callback',
			$screen
		);
	}
} //Parts features post.
add_action( 'add_meta_boxes', 'tofw_service_features');



function tofw_services_features_callback( $post ) {

	wp_nonce_field( 'tofw_meta_box_nonce', 'tofw_services_features_sub' );
	settings_errors();
	echo '<table class="form-table">';
	
	$value = get_post_meta( $post->ID, '_service_code', true );
	
	echo '<tr><td scope="row"><label for="service_code">'.esc_html__("Service Code", "on-the-bench").'</label></td><td>';
	echo '<input type="text" class="regular-text" name="service_code" id="service_code" value="'.esc_attr($value). '" />';
	echo '</td></tr>';
	
	$value = get_post_meta( $post->ID, '_time_required', true );
	
	echo '<tr><td scope="row"><label for="time_required">'.esc_html__("Time Required", "on-the-bench").'</label></td><td>';
	echo '<input type="text" class="regular-text" name="time_required" id="time_required" value="'.esc_attr($value). '" />';
	echo '</td></tr>';
	
	$value = get_post_meta( $post->ID, '_cost', true );
	
	echo '<tr><td scope="row"><label for="cost">'.esc_html__("Cost", "on-the-bench").'</label></td><td>';
	echo '<input type="text" class="regular-text" name="cost" id="tofw_cost" value="'.esc_attr($value). '" />';
	echo '<p class="description" id="tagline-description">Numbers only.</p>';
	echo '</td></tr>';
	
	
	//tofw_use_tax
	$tofw_use_taxes 		= get_option("tofw_use_taxes");
	$tofw_primary_tax		= get_option("tofw_primary_tax");

	if($tofw_use_taxes == "on"):
		$value = get_post_meta( $post->ID, '_tofw_use_tax', true );

		if(empty($value)) {
			$value = $tofw_primary_tax;
		}

		echo '<tr><td scope="row"><label for="tofw_use_tax">'.esc_html__("Select Service Tax", "on-the-bench").'</label></td><td>';
		echo '<select class="regular-text form-control" name="tofw_use_tax" id="tofw_use_tax">';
		echo '<option value="">'.esc_html__("Select tax for service", "on-the-bench").'</option>';
		echo tofw_generate_tax_options($value);
		echo "</select>";
		echo '</td></tr>';

	endif; // Tax enabled

	$value = get_post_meta( $post->ID, '_warranty', true );
	
	echo '<tr><td scope="row"><label for="warranty">'.esc_html__("Warranty", "on-the-bench").'</label></td><td>';
	echo '<input type="text" class="regular-text" name="warranty" id="warranty" value="'.esc_attr($value). '" />';
	echo '</td></tr>';

	if(get_post_meta( $post->ID, '_pick_deliver', true ) == "on") { 
		$mystring_pick = 'checked';
	} else { 
		$mystring_pick = '';
	}
	//Checking if offer pick delivery. if on, make it checked else nothing.
	$tofw_offer_pick_deli = get_option('tofw_offer_pick_deli');
	if($tofw_offer_pick_deli == "on"){
			echo '<tr><td scope="row"><label for="pick_deliver">'.esc_html__("Pick Up & Delivery Available", "on-the-bench").'</label></td><td>';
			echo '<input type="checkbox"  name="pick_deliver" id="pick_deliver" value="on" '.$mystring_pick.' />';
		echo '</td></tr>';
	}
	else {}
	if(get_post_meta( $post->ID, '_Warranty_Period', true ) == "on") { 
		$mystring_rent = 'checked';
	} else { 
		$mystring_rent = '';
	}
	$tofw_offer_Warranty = get_option('tofw_offer_Warranty');

	if($tofw_offer_Warranty == "on"){
		echo '<tr><td scope="row"><label for="Warranty_Period">'.esc_html__("Warranty Period Availability", "on-the-bench").'</label></td><td>';
		echo '<input type="checkbox" name="Warranty_Period" id="Warranty_Period" value="on" '.$mystring_rent.' />';
		echo '</td></tr>';
	}
	else {}
	echo '</table>';
}

/**
 * Save infor.
 *
 * @param int $post_id The ID of the post being saved.
 */
function tofw_services_features_save_box( $post_id ) {
	// Verify that the nonce is valid.
	if (!isset( $_POST['tofw_services_features_sub']) || ! wp_verify_nonce( $_POST['tofw_services_features_sub'], 'tofw_meta_box_nonce' )) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( isset( $_POST['post_type'] )) {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	//Form PRocessing
	$submission_values = array(
						"service_code",
						"time_required",
						"cost",
						"warranty",
						"pick_deliver",
						"Warranty_Period",
						"tofw_use_tax"
						);

	foreach($submission_values as $submit_value) {
		$my_value = sanitize_text_field($_POST[$submit_value]);
		update_post_meta($post_id, '_'.$submit_value, $my_value);
	}
}
add_action( 'save_post', 'tofw_services_features_save_box' );
//Add filter to show Meta Data in front end of post!
add_filter('the_content', 'tofw_front_services_filter', 0);

function tofw_front_services_filter($content) {
	
	if ( is_singular('otb_services') ) {
		
		global $post;
		$tofw_offer_pick_deli = get_option('tofw_offer_pick_deli');
		$tofw_offer_Warranty = get_option('tofw_offer_Warranty');
		$pick_deliver_charg = get_option('tofw_pick_delivery_charges'); //Getting charges we set in other function.

		$content = '<strong>'.esc_html__("Service Description", "on-the-bench").':</strong> '.$content;
		$content .= '<div class="grid-container grid-x grid-padding-x grid-padding-y">';
		$content .= '<h2 class="small-12 cell">'.esc_html__("Service Details", "on-the-bench").'</h2>';
		if($tofw_offer_pick_deli == "on"){

  		if(get_post_meta( $post->ID, '_pick_deliver', true ) == "on") 
		{
			$content .= '<div class="large-8 medium-8 small-8 cell"><strong>'.esc_html__("Pickup and delivery", "on-the-bench").'</strong></div>';
			$content .= '<div class="large-4 medium-4 small-4 cell">'.esc_html__("Yes", "on-the-bench").'</div>';
			$content .= "<hr />";
			$content .= '<div class="large-8 medium-8 small-8 cell"><strong>'.esc_html__("Pick and delivery charges", "on-the-bench").'</strong></div>';
			$content .= '<div class="large-4 medium-4 small-4 cell">'.get_option('tofw_system_currency').$pick_deliver_charg.'</div>';
			$content .= "<hr />";
		}
		}
		//Getting the value of required fields
			$tofw_one_week	= get_option('tofw_one_week');
		    $tofw_one_day 	= get_option('tofw_one_day');
			$tofw_cost 		= get_post_meta( $post->ID, '_cost', true );
			$tofw_time 		= get_post_meta( $post->ID, '_time_required', true );
			$tofw_servicecode	= get_post_meta( $post->ID, '_service_code', true );
			$tofw_warranty	= get_post_meta( $post->ID, '_warranty', true );
			
		if($tofw_offer_Warranty == "on"){
	    if(get_post_meta( $post->ID, '_Warranty_Period', true ) == "on") { 
		
			$content .= '<div class="large-8 medium-8 small-8 cell"><strong>'.esc_html__("Warranty Period Service", "on-the-bench").'</strong></div>';
			$content .= '<div class="large-4 medium-4 small-4 cell">'.esc_html__("Yes", "on-the-bench").'</div>';
			$content .= '<div class="large-8 medium-8 small-8 cell"><strong>'.esc_html__("For One Day", "on-the-bench").'</strong></div>';
			$content .= '<div class="large-4 medium-4 small-4 cell">'.get_option('tofw_system_currency').$tofw_one_day.'</div>';
			$content .= '<div class="large-8 medium-8 small-8 cell"><strong>'.esc_html__("For one week", "on-the-bench").'</strong></div>';
			$content .= '<div class="large-4 medium-4 small-4 cell">'.get_option('tofw_system_currency').$tofw_one_week.'</div>';
			$content .= "<hr />";
		}
		}
			$content .= '<div class="large-8 medium-8 small-8 cell"><strong>'.esc_html__("Service Price", "on-the-bench").'</strong></div>';
			$content .= '<div class="large-4 medium-4 small-4 cell">'.get_option('tofw_system_currency').$tofw_cost.'</div>';
			$content .= "<hr />";
			$content .= '<div class="large-8 medium-8 small-8 cell"><strong>'.esc_html__("Time Required", "on-the-bench").'</strong></div>';
			$content .= '<div class="large-4 medium-4 small-4 cell">'.$tofw_time.'</div>';
			$content .= "<hr />";
			$content .= '<div class="large-8 medium-8 small-8 cell"><strong>'.esc_html__("Service Code", "on-the-bench").'</strong></div>';
			$content .= '<div class="large-4 medium-4 small-4 cell">'.$tofw_servicecode.'</div>';
			$content .= "<hr />";
			$content .= '<div class="large-8 medium-8 small-8 cell"><strong>'.esc_html__("Service Type", "on-the-bench").'</strong></div>';
			$content .= '<div class="large-4 medium-4 small-4 cell">'.custom_taxonomies_terms_links($post->ID, $post->post_type).'</div>';
			$content .= "<hr />";
	 		$content .= '<div class="large-8 medium-8 small-8 cell"><strong>'.esc_html__("Warranty", "on-the-bench").'</strong></div>';
			$content .= '<div class="large-4 medium-4 small-4 cell">'.$tofw_warranty.'</div>';
			$content .= "<hr />";
	 		$content .= '</div><!--row ends here.-->'; 
	}
	return $content;
}


/*
*Add meta data to table fields post list.. 
*/
add_filter('manage_edit-otb_services_columns', 'tofw_table_list_services_type_columns') ;

function tofw_table_list_services_type_columns( $columns ) {
	$columns = array(
		'cb' => '<input type="checkbox" />',
		'title' 		=> esc_html__('Service Name', "on-the-bench"),
		'service_code' 	=> esc_html__('Service Code', "on-the-bench"),
		'time_required' => esc_html__('Time Required', "on-the-bench"),
		'cost' 			=> esc_html__('Service Cost', "on-the-bench"),
		'warranty' 		=> esc_html__('Wrranty', "on-the-bench")
	);
	return $columns;
}

add_action( 'manage_otb_services_posts_custom_column', 'tofw_table_service_list_meta_data', 10, 2 );

function tofw_table_service_list_meta_data($column, $post_id) {
	global $post;
	$system_currency 	= get_option('tofw_system_currency');
	
	switch( $column ) {
		case 'service_code' :
			$stock_code = get_post_meta($post_id, '_service_code', true );
			echo $stock_code;
			break;
		case 'time_required' :
			$capacity = get_post_meta($post_id, '_time_required', true);
			echo $capacity;
			break;
			
		case 'cost' :
			$price = get_post_meta($post_id, '_cost', true);
			echo $price.$system_currency;
			break;	
		
		case 'warranty' :
			$warranty = get_post_meta($post_id, '_warranty', true);
			echo $warranty;
			break;
				
		//Break for everything else to show default things.
		default :
			break;
	}
}


if(!function_exists("tofw_extend_services_admin_search")):
	function tofw_extend_services_admin_search( $query ) {

		// Extend search for document post type
		$post_type = 'otb_services';
		// Custom fields to search for
		$custom_fields = array(
			"_service_code",
			"_time_required"
		);

		if( ! is_admin() )
			return;

		if ( $query->query['post_type'] != $post_type )
			return;

		$search_term = $query->query_vars['s'];

		// Set to empty, otherwise it won't find anything
		$query->query_vars['s'] = '';

		$query->set('_meta_or_title', $search_term);

		if ( $search_term != '' ) {
			$meta_query = array( 'relation' => 'OR' );

			foreach( $custom_fields as $custom_field ) {
				array_push( $meta_query, array(
					'key' 		=> $custom_field,
					'value' 	=> $search_term,
					'compare' 	=> 'LIKE'
				));
			}
			$query->set( 'meta_query', $meta_query );
		};
	}
	add_action( 'pre_get_posts', 'tofw_extend_services_admin_search', 6, 2);

	add_action( 'pre_get_posts', function( $q )
	{
		if( $title = $q->get( '_meta_or_title' ) )
		{
			add_filter( 'get_meta_sql', function( $sql ) use ( $title )
			{
				global $wpdb;

				// Only run once:
				static $nr = 0; 
				if( 0 != $nr++ ) return $sql;

				// Modified WHERE
				$sql['where'] = sprintf(
					" AND ( %s OR %s ) ",
					$wpdb->prepare( "{$wpdb->posts}.post_title like '%%%s%%'", $title),
					mb_substr( $sql['where'], 5, mb_strlen( $sql['where'] ) )
				);

				return $sql;
			}, 12, 1);
		}
	}, 12, 1);
endif;