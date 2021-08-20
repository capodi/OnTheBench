<?php
	//List Services shortcode
	//Used to display Services on a page.
	//Linked to single service pages. 

	function tofw_list_services() { 
		$content = '';
		$args = array( 'post_type' => 'otb_services' );

		$services_query = new WP_Query( $args );

		if($services_query->have_posts()) : 
		$content .= "<div class='grid-container grid-x grid-padding-x grid-padding-y'>";
		
		while ($services_query->have_posts()) : $services_query->the_post();
			$content .= '<div class="large-4 medium-6 small-12 cell">';
			$content .= "<div class='tofw-product'>";
			$feat_image =   wp_get_attachment_image_src(get_post_thumbnail_id($services_query->ID ), 'thumbnail');
			if(!empty($feat_image)) {
				$content .= '<a href="'.get_the_permalink().'"><img src="'.$feat_image[0].'" class="thumbnail" /></a>';
			}
			$content .= "<a href='".get_the_permalink()."'><h3 class='tofw-product-title'>".get_the_title()."</h3></a>";
			$content .= '</div></div>'; //Columns ends here.
		endwhile;
		
		//<!-- end of the loop -->
		$content .= "</div><!--row ends here.-->";
		//<!-- pagination here -->

		wp_reset_postdata();

		else :
			echo "<p>";
				esc_html_e( 'Sorry, no product matched your criteria.', "on-the-bench" );
			echo "</p>";
		endif;

		return $content;
	}//tofw_list_services.
	add_shortcode('tofw_list_services', 'tofw_list_services');