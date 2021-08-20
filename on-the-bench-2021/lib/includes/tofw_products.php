<?php
function tofw_on_the_1_bench_products_init() {
    $labels = array(
		'add_new_item' 			=> esc_html__('Add new Product', 'on-the-bench'),
		'singular_name' 		=> esc_html__('Product', 'on-the-bench'), 
		'menu_name' 			=> esc_html__('Products', 'on-the-bench'),
		'all_items' 			=> esc_html__('Products', 'on-the-bench'),
		'edit_item' 			=> esc_html__('Edit Product', 'on-the-bench'),
		'new_item' 				=> esc_html__('New Product', 'on-the-bench'),
		'view_item' 			=> esc_html__('View Product', 'on-the-bench'),
		'search_items' 			=> esc_html__('Search Products', 'on-the-bench'),
		'not_found' 			=> esc_html__('No product found', 'on-the-bench'),
		'not_found_in_trash' 	=> esc_html__('No product in trash', 'on-the-bench')
	);
	
	
	$args = array(
		'labels'             	=> $labels,
		'label'					=> esc_html__("Parts", "on-the-bench"),
		'description'        	=> esc_html__('Parts Section', 'on-the-bench'),
		'public'             	=> true,
		'publicly_queryable' 	=> true,
		'show_ui'            	=> true,
		'show_in_menu'       	=> false,
		'query_var'          	=> true,
		'rewrite'            	=> array('slug' => 'part'),
		'capability_type'    	=> array('otb_product', 'otb_products'),
		'has_archive'        	=> true,
		'menu_icon'			 	=> 'dashicons-clipboard',
		'menu_position'      	=> 30,
		'supports'           	=> array( 'title', 'editor', 'thumbnail'), 	
	  	'register_meta_box_cb' 	=> 'tofw_parts_features',
		'taxonomies' 			=> array('brand_type')
    );
    register_post_type('otb_products', $args);
}
add_action( 'init', 'tofw_on_the_1_bench_products_init');
//registeration of post type ends here.

add_action( 'init', 'tofw_create_parts_tax_brand');
function tofw_create_parts_tax_brand() {
    $labels = array(
		'name'              => esc_html__('Brands', 'on-the-bench'),
		'singular_name'     => esc_html__('Brand', 'on-the-bench'),
		'search_items'      => esc_html__('Search Brands', 'on-the-bench'),
		'all_items'         => esc_html__('All Brands', 'on-the-bench'),
		'parent_item'       => esc_html__('Parent Brand', 'on-the-bench'),
		'parent_item_colon' => esc_html__('Parent Brand:', 'on-the-bench'),
		'edit_item'         => esc_html__('Edit Brand', 'on-the-bench'),
		'update_item'       => esc_html__('Update Brand', 'on-the-bench'),
		'add_new_item'      => esc_html__('Add New Brand', 'on-the-bench'),
		'new_item_name'     => esc_html__('New Brand Name', 'on-the-bench'),
		'menu_name'         => esc_html__('Brand', 'on-the-bench')
	);
	
	$args = array(
			'label' 		=> esc_html__( 'Brand', "on-the-bench"),
			'rewrite' 		=> array('slug' => 'brand'),
			'public' 		=> true,
			'labels' 		=> $labels,
			'hierarchical' 	=> true	
	);
	
	register_taxonomy(
        'brand_type',
        'otb_products',
		$args
    );
}
//Registration of Taxanomy Ends here. 

function tofw_parts_features() { 
	$screens = array('otb_products');

	foreach ( $screens as $screen ) {
		add_meta_box(
			'myplugin_sectionid',
			'Product Details',
			'tofw_parts_features_callback',
			$screen
		);
	}
} //Parts features post.
add_action( 'add_meta_boxes', 'tofw_parts_features');



function tofw_parts_features_callback( $post ) {

	wp_nonce_field( 'tofw_meta_box_nonce', 'tofw_parts_features_sub' );
	settings_errors();
	echo '<table class="form-table">';
	
	$value = get_post_meta( $post->ID, '_manufacturing_code', true );
	
	echo '<tr><td scope="row"><label for="manufacturing_code">'.esc_html__("Manufacturing Code", "on-the-bench").'</label></td><td>';
	echo '<input type="text" class="regular-text" name="manufacturing_code" id="manufacturing_code" value="'.esc_attr($value). '" />';
	echo '</td></tr>';
	
	$value = get_post_meta( $post->ID, '_stock_code', true );
	
	echo '<tr><td scope="row"><label for="stock_code">'.esc_html__("Stock Code", "on-the-bench").'</label></td><td>';
	echo '<input type="text" class="regular-text" name="stock_code" id="stock_code" value="'.esc_attr($value). '" />';
	echo '</td></tr>';
	
	$value = get_post_meta( $post->ID, '_core_features', true );
	
	echo '<tr><td scope="row"><label for="core_features">'.esc_html__("Core Features", "on-the-bench").'</label></td><td>';
	echo '<textarea class="large-text" name="core_features" id="core_features" rows="4">'.esc_attr($value).'</textarea>';
	echo '</td></tr>';
	
	$value = get_post_meta( $post->ID, '_capacity', true );
	
	echo '<tr><td scope="row"><label for="capacity">'.esc_html__("Capacity", "on-the-bench").'</label></td><td>';
	echo '<input type="text" class="regular-text" name="capacity" id="capacity" value="'.esc_attr($value). '" />';
	echo '</td></tr>';
	
	$value = get_post_meta( $post->ID, '_price', true );
	
	echo '<tr><td scope="row"><label for="price">'.esc_html__("Price", "on-the-bench").'</label></td><td>';
	echo '<input type="text" class="regular-text tofw_validate_number" name="price" id="tofw_price" value="'.esc_attr($value). '" />';
	echo '<p class="description" id="tagline-description">'.esc_html__("Numbers only", "on-the-bench").'.</p>';
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
	


	$value = get_post_meta( $post->ID, '_installation_charges', true );
	
	echo '<tr><td scope="row"><label for="installation_charges">'.esc_html__("Installation Charges", "on-the-bench").'</label></td><td>';
	echo '<input type="text" class="regular-text tofw_validate_number" name="installation_charges" id="installation_charges" value="'.esc_attr($value). '" />';
	echo '<p class="description" id="tagline-description">'.esc_html__("Leave blank to hide", "on-the-bench").'.</p>';
	echo '</td></tr>';
	
	$value = get_post_meta( $post->ID, '_installation_message', true );
	
	echo '<tr><td scope="row"><label for="installation_message">'.esc_html__("Installation Message", "on-the-bench").'</label></td><td>';
	echo '<input type="text" class="regular-text" name="installation_message" id="installation_message" value="'.esc_attr($value). '" />';
	echo '<p class="description" id="tagline-description">'.esc_html__("Leave blank to hide", "on-the-bench").'.</p>';
	echo '</td></tr>';
	
	echo '</table>';
}


/**
 * Save infor.
 *
 * @param int $post_id The ID of the post being saved.
 */
function tofw_parts_features_save_box( $post_id ) {
	// Verify that the nonce is valid.
	if (!isset( $_POST['tofw_parts_features_sub']) || ! wp_verify_nonce( $_POST['tofw_parts_features_sub'], 'tofw_meta_box_nonce' )) {
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
						"manufacturing_code",
						"stock_code",
						"core_features",
						"capacity",
						"price",
						"warranty",
						"installation_charges",
						"installation_message",
						"tofw_use_tax"
						);

	foreach($submission_values as $submit_value) {
		$my_value = sanitize_text_field($_POST[$submit_value]);
		update_post_meta($post_id, '_'.$submit_value, $my_value);
	}
}
add_action( 'save_post', 'tofw_parts_features_save_box' );


/*
*Add meta data to table fields post list.. 
*/
add_filter('manage_edit-otb_products_columns', 'tofw_table_list_products_type_columns') ;

function tofw_table_list_products_type_columns( $columns ) {
	$columns = array(
		'cb' 			=> '<input type="checkbox" />',
		'title' 		=> esc_html__('Part Name', "on-the-bench"),
		'stock_code' 	=> esc_html__('Stock Code', "on-the-bench"),
		'capacity' 		=> esc_html__('Capacity', "on-the-bench"),
		'price' 		=> esc_html__('Price', "on-the-bench"),
		'warranty' 		=> esc_html__('Wrranty', "on-the-bench")
	);
	return $columns;
}

add_action( 'manage_otb_products_posts_custom_column', 'tofw_table_list_meta_data', 10, 2 );

function tofw_table_list_meta_data($column, $post_id) {
	global $post;
	$system_currency 	= get_option('tofw_system_currency');
	
	switch( $column ) {
		case 'stock_code' :
			$stock_code = get_post_meta($post_id, '_stock_code', true );
			echo $stock_code;
			break;
		case 'capacity' :
			$capacity = get_post_meta($post_id, '_capacity', true);
			echo $capacity;
			break;
			
		case 'price' :
			$price = get_post_meta($post_id, '_price', true);
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


if(!function_exists("tofw_extend_products_admin_search")):
	function tofw_extend_products_admin_search( $query ) {

		// Extend search for document post type
		$post_type = 'otb_products';
		// Custom fields to search for
		$custom_fields = array(
			"_stock_code",
			"_capacity"
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
					'key' => $custom_field,
					'value' => $search_term,
					'compare' => 'LIKE'
				));
			}
			$query->set( 'meta_query', $meta_query );
		};
	}
	add_action( 'pre_get_posts', 'tofw_extend_products_admin_search', 6, 2);

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

//Add filter to show Meta Data in front end of post!
add_filter('the_content', 'tofw_front_products_filter', 0);

function tofw_front_products_filter($content) {
	if ( is_singular('otb_products') ) {
		global $post;
		
		$manufacturing_code 	= get_post_meta($post->ID, '_manufacturing_code', true);
		$stock_code 			= get_post_meta($post->ID, '_stock_code', true);
		$core_features 			= get_post_meta($post->ID, '_core_features', true);
		$capacity 				= get_post_meta($post->ID, '_capacity', true);
		$price 					= get_post_meta($post->ID, '_price', true);
		$warranty 				= get_post_meta($post->ID, '_warranty', true);
		$installation_charges 	= get_post_meta($post->ID, '_installation_charges', true);
		$installation_message 	= get_post_meta($post->ID, '_installation_message', true);
		
		$content = '<strong>'.esc_html__("Product Description", "on-the-bench").':</strong> '.$content;
		
		$content .= '<div class="grid-container grid-x grid-padding-x grid-padding-y">';
		$content .= '<h2 class="small-12 cell">'.esc_html__("Product Details", "on-the-bench").'</h2>';
		$content .= '<div class="large-4 medium-4 small-4 cell"><strong>'.esc_html__("Manufacturing Code", "on-the-bench").'</strong></div>';
		$content .= '<div class="large-8 medium-8 small-8 cell">'.$manufacturing_code.'</div>';
		$content .= "<hr />";
		
		$content .= '<div class="large-4 medium-4 small-4 cell"><strong>'.esc_html__("Stock Code", "on-the-bench").'</strong></div>';
		$content .= '<div class="large-8 medium-8 small-8 cell">'.$stock_code.'</div>';
		$content .= "<hr />";
		
		$content .= '<div class="large-4 medium-4 small-4 cell"><strong>'.esc_html__("Core Features", "on-the-bench").'</strong></div>';
		$content .= '<div class="large-8 medium-8 small-8 cell">'.$core_features.'</div>';
		$content .= "<hr />";
		
		$content .= '<div class="large-4 medium-4 small-4 cell"><strong>'.esc_html__("Capacity", "on-the-bench").'</strong></div>';
		$content .= '<div class="large-8 medium-8 small-8 cell">'.$capacity.'</div>';
		$content .= "<hr />";
		
		$content .= '<div class="large-4 medium-4 small-4 cell"><strong>'.esc_html__("Price", "on-the-bench").'</strong></div>';
		$content .= '<div class="large-8 medium-8 small-8 cell">'.get_option('tofw_system_currency').$price.'</div>';
		$content .= "<hr />";
		
		if($installation_charges != '') { 
		$content .= '<div class="large-4 medium-4 small-4 cell"><strong>'.esc_html__("Installation Charges", "on-the-bench").'</strong></div>';
		$content .= '<div class="large-8 medium-8 small-8 cell">'.get_option('tofw_system_currency').$installation_charges.' '.$installation_message.'</div>';
		$content .= "<hr />";
		}
		
		$content .= '<div class="large-4 medium-4 small-4 cell"><strong>'.esc_html__("Warranty", "on-the-bench").'</strong></div>';
		$content .= '<div class="large-8 medium-8 small-8 cell">'.$warranty.'</div>';
		$content .= "<hr />";
		
		$content .= '<div class="large-4 medium-4 small-4 cell"><strong>'.esc_html__("Brand", "on-the-bench").'</strong></div>';
		$content .= '<div class="large-8 medium-8 small-8 cell">'.custom_taxonomies_terms_links($post->ID, $post->post_type).'</div>';
		$content .= "<hr />";
		
		$content .= '</div><!--row ends here.-->';
	}
	return $content;
}