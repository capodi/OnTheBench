<?php
	/*
	 * Request Quote Shortcode
	 * 
	 * Generates a Form which requests Quote
	 * Quote is added into Jobs with Status Quote.
	*/

	if(!function_exists("tofw_otb_my_account")):
	function tofw_otb_my_account() { 
		$content = '';
		
		if(!is_user_logged_in()):
			//Content for non logged in users
			$content .= '<!-- Content section -->';
			$content .= '<div class="content-area cr_content_area">';
			$content .= '<div class="grid-container grid-x grid-padding-x">';

			$content .= '<div class="large-12 medium-6 small-12 cell">';
			$content .= '<div class="have-meta no_thumb blog-post blog post-1 post type-post status-publish format-standard hentry category-uncategorized">';

			$content .= '<div class="blog-text">';
			$content .= '<h3>';
			$content .= esc_html__('Login Below!', "on-the-bench");
			$content .= '</h3>';
			$content .= '<div class="post-content blog-page-content">';
			$content .= wp_login_form(array('echo' => false));
			$content .= '<a href="<?php echo wp_lostpassword_url( get_permalink() ); ?>">'.esc_html__("Lost Password", "on-the-bench").'</a>';
			$content .= '</div><!-- Post Content /-->';
			$content .= '</div>';
			$content .= '</div><!-- Blog Post /-->
							</div><!-- Column /-->
							<div class="clearfix"></div>    
						</div><!-- Row / Posts Container /-->
					</div>
			<!-- Content Section Ends /-->';
		else: 
			//Content for loggedin users ?>
			<!-- Content section -->
    <div class="content-area default-page module">
        <div class="grid-container grid-x grid-padding-x">

			<div id="sidebarcollapse" class="large-3 c medium-8 small-12 cell padding-top-bottom sidebarcollapse myaccount sidebar" data-toggler data-animate="fade-in fade-out">
                <div class="widget">
					<h2 class="widget-title"><?php echo esc_html__("Account Links", "on-the-bench"); ?></h2>
					
					<div class="widget-content">
						<ul>
							<li>
								<a href="<?php echo get_the_permalink($object_id); ?>">
									<?php echo esc_html__("Dashboard", "on-the-bench"); ?>
								</a>
							</li>

							<li>
								<a href="<?php echo get_the_permalink($object_id); ?>?request_quote=YES">
									<?php echo esc_html__("Request Quote", "on-the-bench"); ?>
								</a>
							</li>
							<li>
								<a href="<?php echo get_edit_user_link(); ?>">
									<?php echo esc_html__("Edit Profile", "on-the-bench"); ?>
								</a>
							</li>
							<li>
								<a href="<?php echo wp_logout_url( home_url() ); ?>">
									<?php echo esc_html__("Logout", "on-the-bench"); ?>
								</a>	
							</li>
						</ul>
					</div>
				</div>

			</div><!-- right bar ends here /-->


			<div class="large-9 medium-8 small-12 cell content-side padding-top-bottom posts-wrap content-side">
				<?php 
					$current_user 	= wp_get_current_user();
					$customer_id	= $current_user->ID;

					if(isset($_GET["print"]) && isset($_GET["order_id"]) && !empty($_GET["order_id"])):
						$case_number 		= get_post_meta($_GET["order_id"], "_case_number", true);
						$curr_case_number 	= $_GET["tofw_case_number"];

						if($case_number != $curr_case_number) {
							echo esc_html__("You do not have permission to view this record.", "on-the-bench");
						} else {
							tofw_on_the_bench_print_functionality();
						}
					elseif(isset($_GET["request_quote"]) && $_GET["request_quote"] == "YES"):
						echo tofw_request_quote_form();
					else:
						echo "<h2>".esc_html__("Welcom", "on-the-bench")." ".$current_user->user_firstname." ".$current_user->user_lastname."</h2>";
						echo "<p>".esc_html__("Here you can check your jobs and their statuses also you can request new quote.", "on-the-bench")."</p>";
						echo "<h3>".esc_html__("Filter Jobs", "on-the-bench")."</h3>";		
						echo "<div class='job_status_holder'><ul class='horizontal tofw_menu'>";
						echo tofw_generate_status_links_myaccount();
						echo "</ul></div>";
						
						$job_status = "all";

						echo tofw_print_jobs_by_customer_table($job_status, $customer_id);
					endif;
				?>
			</div><!-- Posts wrap /-->
			
        </div><!-- Row Ends /-->
    </div>
    <!-- Content Section Ends /-->

<?php
		endif;

		return $content;
	}//tofw_list_services.
	add_shortcode('tofw_otb_my_account', 'tofw_otb_my_account');
	endif;