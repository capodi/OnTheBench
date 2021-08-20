<?php
function tofw_on_the_1_bench_main() {
    if (!current_user_can('manage_options')) {
      wp_die( __('You do not have sufficient permissions to access this page.') );
	}
?>
	<div class="main-container On-The-Bench">
		<div class="grid-x grid-container grid-padding-x grid-padding-y">
			<div class="small-12 cell">
				<h1>
					<?php echo esc_html__( 'On Our Bench', 'on-the-bench'); ?>
				</h1>
				<div class="form-update-message"></div>
			</div>
			
			<div class="large-10 medium-10 small-12 cell">
				
				<div class="team-wrap">
					<?php
						if(isset($_GET["update_status"]) && !empty($_GET["update_status"])):
							$class_settings = "";
							$class_taxes 	= "";
							$class_status 	= " is-active";
						else: 
							$class_settings = " is-active";
							$class_taxes 	= "";
							$class_status 	= "";	
						endif;
					?>
					<ul class="tabs" data-tabs="82ulyt-tabs" id="example-tabs">
						<li class="tabs-title<?=esc_attr($class_settings);?>" role="presentation">
							<a href="#panel1" role="tab" aria-controls="panel1" aria-selected="false" id="panel1-label">
								<h2><?php echo esc_html__("Settings", "on-the-bench"); ?></h2>
							</a>
						</li>
						<li class="tabs-title<?=esc_attr($class_taxes);?>" role="presentation">
							<a href="#panel2" role="tab" aria-controls="panel2" aria-selected="true" id="panel2-label">
								<h2><?php echo esc_html__("Taxes", "on-the-bench"); ?></h2>
							</a>
						</li>
						<li class="tabs-title<?=esc_attr($class_status);?>" role="presentation">
							<a href="#panel3" role="tab" aria-controls="panel3" aria-selected="true" id="panel3-label">
								<h2><?php echo esc_html__("Job Status", "on-the-bench"); ?></h2>
							</a>
						</li>
					</ul>
                    
                    <div class="tabs-content" data-tabs-content="example-tabs">
                        
						<div class="tabs-panel team-wrap<?=esc_attr($class_settings);?>" id="panel1" role="tabpanel" aria-hidden="true" aria-labelledby="panel1-label">
						<?php
							//must check that the user has the required capability 
							$pick_deliver_charg = get_option('tofw_pick_delivery_charges'); //Getting charges we set in other function.
							
							$menu_name_p 		= get_option("menu_name_p");

							//Processing Logo
							$on_the_bench_logo = get_option("on_the_bench_logo");

							if(empty($on_the_bench_logo)) {
								$custom_logo_id 		= get_theme_mod( 'custom_logo' );
								$image 					= wp_get_attachment_image_src( $custom_logo_id , 'full' );

								$on_the_bench_logo	= $image[0];	
							}


							$on_the_bench_email = get_option("on_the_bench_email");

							if(empty($on_the_bench_email)) {
								$on_the_bench_email	= get_option("admin_email");	
							}

							//Checking if offer pick delivery. if on, make it checked else nothing.
							$tofw_offer_pick_deli = get_option('tofw_offer_pick_deli');
							
							//Checking if offer Warranty Period. if on, make it checked else nothing.
							$tofw_offer_Warranty 	= get_option('tofw_offer_Warranty');
							
							$offer_Warranty_one	= get_option('tofw_one_day');//Getting charges we set in other function.
							$offer_Warranty_week 	= get_option('tofw_one_week');//Getting charges we set in other function.
							$system_currency 	= get_option('tofw_system_currency');
							$business_terms 	= get_option('tofw_business_terms');

							$tofw_use_taxes 			= get_option("tofw_use_taxes");
							$tofw_enable_woo_products = get_option("tofw_enable_woo_products");
							
							$tofw_file_attachment_in_job = get_option("tofw_file_attachment_in_job");
							$tofw_primary_tax			= get_option("tofw_primary_tax");

							if($tofw_use_taxes == "on") {
								$usetaxes = 'checked="checked"';
							} else {
								$usetaxes = '';
							}

							if($tofw_enable_woo_products == "on") {
								$useWooProducts = 'checked="checked"';
							} else {
								$useWooProducts = '';
							}

							if($tofw_file_attachment_in_job == "on") {
								$use_file_attachment = 'checked="checked"';
							} else {
								$use_file_attachment = '';
							}

							$tofw_send_otb_notice 	= get_option("tofw_job_status_otb_notice");
							
							if($tofw_send_otb_notice == "on") {
								$send_notice = 'checked="checked"';
							} else {
								$send_notice = "";
							}
							
							/*$tofw_add_admin_to_technician = get_option("tofw_add_admin_to_technician");

							if($tofw_add_admin_to_technician == "on") {
								$adminrole = 'checked="checked"';
							} else {
								$adminrole = '';
							}
							<tr>
								<th scope="row">
									<label for="add_admin_to_technician">'.esc_html__("Add admin Role to Technicians Dropdown", 'on-the-bench').'</label>
								</th>
								<td>
									<input type="checkbox" '.$adminrole.' name="add_admin_to_technician" id="add_admin_to_technician" />
								</td>
							</tr>
							*/

							if($tofw_offer_pick_deli == "on") { 
								$instruct = 'checked="checked"';
							} else { 
								$instruct = '';
							}
							if($tofw_offer_Warranty == "on") { 
								$offer_Warranty = 'checked="checked"';
							} else { 
								$offer_Warranty = '';
							} ?>
	
    						<div class="wrap">
								<h2><?php esc_html_e("Settings", "on-the-bench"); ?></h2>

								<form action="" method="post">
									<table cellpadding="5" cellspacing="5" class="form-table">

										<tr>
											<th scope="row">
												<label for="menu_name"><?php esc_html_e("Menu Name e.g On The Bench", "on-the-bench"); ?></label>
											</th>
											<td>
												<input 
													name="menu_name" 
													id="menu_name" 
													class="regular-text" 
													value="<?php echo esc_html($menu_name_p); ?>" 
													type="text" 
													placeholder="<?php esc_html_e("Enter Menu Name Default On The Bench", "on-the-bench"); ?>"/>
											</td>
										</tr>

										<tr>
											<th scope="row">
												<label for="menu_name"><?php esc_html_e("Logo to use", "on-the-bench"); ?></label>
											</th>
											<td>
												<input 
													name="on_the_bench_logo" 
													id="on_the_bench_logo" 
													class="regular-text" 
													value="<?php echo esc_url($on_the_bench_logo); ?>" 
													type="text" 
													placeholder="<?php esc_html_e("Enter url of logo", "on-the-bench"); ?>"/>
											</td>
										</tr>

										<tr>
											<th scope="row">
												<label for="menu_name"><?php esc_html_e("Email", "on-the-bench"); ?><small> <?php esc_html_e("Where quote forms and other admin emails would be sent.", "on-the-bench"); ?></small></label>
											</th>
											<td>
												<input 
													name="on_the_bench_email" 
													id="on_the_bench_email" 
													class="regular-text" 
													value="<?php echo esc_html($on_the_bench_email); ?>" 
													type="text" 
													placeholder="<?php esc_html_e("Where to send emails like Quote and other stuff.", "on-the-bench"); ?>"/>
											</td>
										</tr>

										<tr>
											<th scope="row">
												<label for="tofw_job_status_otb_notice"><?php echo esc_html__("Email Customer", 'on-the-bench'); ?></label>
											</th>
											<td>
												<input type="checkbox" <?php echo esc_html__($send_notice); ?> name="tofw_job_status_otb_notice" id="tofw_job_status_otb_notice" />
												<p class="description"><?php echo esc_html__("Email customer everytime job status is changed", "on-the-bench"); ?></p>
											</td>
										</tr>

										<tr>
											<th scope="row">
												<label for="offer_pic_de"><?php esc_html_e("Offer pickup and delivery?", "on-the-bench"); ?></label>
											</th>
											<td>
												<input type="checkbox" <?php echo esc_html($instruct); ?> name="offer_pic_de" id="offer_pic_de" />
											</td>
										</tr>

										<tr>
											<th scope="row">
												<label for="pick_deliver"><?php esc_html_e("Pick up and delievery charges", "on-the-bench"); ?></label>
											</th>
											<td>
												<input 
													name="pick_deliver" 
													id="pick_deliver" 
													class="regular-text tofw_validate_number" 
													value="<?php echo esc_html($pick_deliver_charg); ?>" 
													type="text" 
													placeholder="<?php esc_html_e("Enter the Pick up and delievery charges here", "on-the-bench"); ?>"/>
											</td>
										</tr>

										<tr>
											<th scope="row">
												<label for="offer_Warranty"><?php esc_html_e("Offer Warranty", "on-the-bench"); ?></label>
											</th>
											<td>
												<input type="checkbox" <?php echo esc_html($offer_Warranty); ?> name="offer_Warranty" id="offer_Warranty" />
											</td>
										</tr>

										<tr>
											<th scope="row">
												<label for="offer_Warranty_one"><?php echo esc_html__("Warranty for One Day", "on-the-bench"); ?></label>
											</th>
											<td>
												<input 
													name="offer_Warranty_one" 
													id="offer_Warranty_one" 
													class="regular-text tofw_validate_number" 
													value="<?php echo esc_html($offer_Warranty_one); ?>" 
													type="text" 
													placeholder="<?php echo esc_html_e("Enter the Warranty for One Day", "on-the-bench"); ?>"/>
											</td>
										</tr>

										<tr>
											<th scope="row">
												<label for="offer_Warranty_week"><?php echo esc_html__("Warranty for One Week", "on-the-bench"); ?></label>
											</th>
											<td>
												<input 
													name="offer_Warranty_week" 
													id="offer_Warranty_week" 
													class="regular-text tofw_validate_number" 
													value="<?php echo esc_html($offer_Warranty_week); ?>" 
													type="text" 
													placeholder="<?php echo esc_html__("Enter the Warranty for One Week", "on-the-bench"); ?>"/>
											</td>
										</tr>

										<tr>
											<th scope="row">
												<label for="offer_Warranty_week"><?php echo esc_html__("Currency", "on-the-bench"); ?></label>
											</th>
											<td>
												<input 
													name="system_currency" 
													id="system_currency" 
													class="regular-text" 
													value="<?php echo esc_html($system_currency); ?>" 
													type="text" 
													placeholder="<?php echo esc_html__("Currency Symbol e.g $", "on-the-bench"); ?>"/>
											</td>
										</tr>

										<tr>
											<th scope="row">
												<label for="business_terms"><?php echo esc_html__("Terms & Conditions for Repair Order", "on-the-bench"); ?></label>
											</th>
											<td>
												<input 
													name="business_terms" 
													id="business_terms" 
													class="regular-text" 
													value="<?php echo esc_html($business_terms); ?>" 
													type="text" 
													placeholder="<?php echo esc_html__("On Repair Order QR Code would be generated with this link.", "on-the-bench"); ?>"/>
											</td>
										</tr>

										<tr>
											<th scope="row">
												<label for="tofw_add_taxes"><?php echo esc_html__("Enable Taxes", 'on-the-bench'); ?></label>
											</th>
											<td>
												<input type="checkbox" <?php echo esc_html__($usetaxes); ?> name="tofw_use_taxes" id="tofw_add_taxes" />
											</td>
										</tr>

										<tr>
											<th scope="row">
												<label for="tofw_primary_tax"><?php echo esc_html__("Primary Tax", 'on-the-bench'); ?></label>
											</th>
											<td>
												<select name="tofw_primary_tax" id="tofw_primary_tax" class="form-control">
													<option value=""><?php echo esc_html__("Select tax", "on-the-bench"); ?></option>
													<?php echo tofw_generate_tax_options($tofw_primary_tax); ?>
												</select>
											</td>
										</tr>


										<tr>
											<th scope="row">
												<label for="tofw_enable_woo_products">
													<?php echo esc_html__("Disable Parts and Use WooCommerce Products", 'on-the-bench'); ?>
												</label>
											</th>
											<td>
												<?php
													if(is_woocommerce_activated() == false) {
														echo esc_html__("Please install and activate WooCommerce to use it. Otherwise you can rely on parts by our plugin.", "on-the-bench");
													} else { ?>
														<input type="checkbox" <?php echo esc_html__($useWooProducts); ?> name="tofw_enable_woo_products" id="tofw_enable_woo_products" />
												<?php	}
												?>
											</td>
										</tr>

										<tr>
											<th scope="row">
												<label for="tofw_file_attachment_in_job">
													<?php echo esc_html__("Enable File Attachment in Job", 'on-the-bench'); ?>
												</label>
											</th>
											<td>
												<input type="checkbox" <?php echo esc_html__($use_file_attachment); ?> name="tofw_file_attachment_in_job" id="tofw_file_attachment_in_job" />
											</td>
										</tr>

										<tr>
											<td>
												<input 
													class="button button-primary" 
													type="Submit"  
													value="<?php echo esc_html__("Save Changes", "on-the-bench"); ?>"/>
											</td>
											<td>
												<input type="hidden" name="tofw_otb_settings" value="1" />
												&nbsp;
											</td>
										</tr>
									</table>
								</form>

								<h2><?php esc_html_e("Shortcodes", "on-the-bench"); ?></h2>
								<p>
								<?php echo esc_html__("To populate services create a page and insert shortcode", "on-the-bench"); ?> <strong>[tofw_list_services]</strong> <?php echo esc_html__("or use", "on-the-bench"); ?> &lt;?php echo tofw_list_services(); ?&gt; <?php echo esc_html__("in WordPress template", "on-the-bench"); ?></p>

								<p>
								<?php echo esc_html__("To populate parts/products create a page and insert shortcode", "on-the-bench"); ?> <strong>[tofw_list_products]</strong> <?php echo esc_html__("or use", "on-the-bench"); ?> &lt;?php echo tofw_list_products(); ?&gt; <?php echo esc_html__("in WordPress template", "on-the-bench"); ?></p>
								
								<p><?php echo esc_html__("To add check case status form create a page and insert shortcode", "on-the-bench"); ?> <strong>[tofw_order_status_form]</strong> <?php echo esc_html__("or use", "on-the-bench"); ?> &lt;?php echo tofw_order_status_form(); ?&gt; <?php echo esc_html__("in WordPress template", "on-the-bench"); ?></p>
								
								<p><?php echo esc_html__("To add request quote form into front end use", "on-the-bench"); ?> <strong>[tofw_request_quote_form]</strong> <?php echo esc_html__("or use", "on-the-bench"); ?> &lt;?php echo tofw_request_quote_form(); ?&gt; <?php echo esc_html__("in WordPress template", "on-the-bench"); ?></p>
								<p><?php echo esc_html__("To add user account page into front end use", "on-the-bench"); ?> <strong>[tofw_otb_my_account]</strong> <?php echo esc_html__("or use", "on-the-bench"); ?> &lt;?php echo tofw_otb_my_account(); ?&gt; <?php echo esc_html__("in WordPress template", "on-the-bench"); ?></p>
								<p><?php echo esc_html__("To add start new job by device on front end for loged in users only", "on-the-bench"); ?> <strong>[tofw_start_job_with_device]</strong> <?php echo esc_html__("or use", "on-the-bench"); ?> &lt;?php echo tofw_start_job_with_device(); ?&gt; <?php echo esc_html__("in WordPress template", "on-the-bench"); ?></p>
								<p><?php echo esc_html__("To add start new job by location on front end for loged in users only", "on-the-bench"); ?> <strong>[tofw_start_job_with_location]</strong> <?php echo esc_html__("or use", "on-the-bench"); ?> &lt;?php echo tofw_start_job_with_location(); ?&gt; <?php echo esc_html__("in WordPress template", "on-the-bench"); ?></p>    					
	</div>
						</div><!-- tab 1 ends -->
	

                        <div class="tabs-panel team-wrap<?=esc_attr($class_taxes);?>" id="panel2" role="tabpanel" aria-hidden="false" aria-labelledby="panel2-label">
							
							<p class="help-text">
								<a class="button button-primary button-small" data-open="taxFormReveal">
									<?php echo esc_html__("Add New Tax", "on-the-bench") ?>
								</a>
							</p>
							<?php add_filter('admin_footer','tofw_add_tax_form'); ?>

							<div id="poststuff_wrapper">
								<table id="poststuff" class="wp-list-table widefat fixed striped posts">
									<thead>
										<tr>
											<th><?php echo esc_html__("ID", "on-the-bench"); ?></th>
											<th><?php echo esc_html__("Name", "on-the-bench"); ?></th>
											<th><?php echo esc_html__("Description", "on-the-bench"); ?></th>
											<th><?php echo esc_html__("Rate (%)", "on-the-bench"); ?></th>
											<th><?php echo esc_html__("Status", "on-the-bench"); ?></th>
											<th><?php echo esc_html__("Actions", "on-the-bench"); ?></th>
										</tr>
									</thead>

									<tbody>
										<?php
											global $wpdb;

											$on_the_bench_taxes 	= $wpdb->prefix.'tofw_otb_taxes';

											$select_query 	= "SELECT * FROM `".$on_the_bench_taxes."`";
											$select_results = $wpdb->get_results($select_query);
											
											$output = '';
											foreach($select_results as $result) {
																					
												$output .= '<tr><td>'.$result->tax_id.'</td>';

												$output .= '<td><strong>'.$result->tax_name.'</strong></td>';
												$output .= '<td>'.$result->tax_description.'</td>';
												$output .= '<td>'.$result->tax_rate.'</td>';
												$output .= '<td>'.$result->tax_status.'</td>';
												$output .= '<td><a href="#" class="change_tax_status" data-type="tax" data-value="'.esc_attr($result->tax_id).'">'.esc_html__("Change Status", "on-the-bench").'</a></td></tr>';
											}
											echo wp_kses_post($output);
										?>	
									</tbody>
								</table>
							</div><!-- Post Stuff/-->

                        </div><!-- tab 2 Ends -->


						<div class="tabs-panel team-wrap<?=esc_attr($class_status);?>" id="panel3" role="tabpanel" aria-hidden="false" aria-labelledby="panel3-label">
							
							<p class="help-text">
								<a class="button button-primary button-small" data-open="statusFormReveal">
									<?php echo esc_html__("Add New Status", "on-the-bench") ?>
								</a>
							</p>
							<?php add_filter('admin_footer','tofw_add_status_form'); ?>

							<div id="job_status_wrapper">
								<table id="status_poststuff" class="wp-list-table widefat fixed striped posts">
									<thead>
										<tr>
											<th><?php echo esc_html__("ID", "on-the-bench"); ?></th>
											<th><?php echo esc_html__("Name", "on-the-bench"); ?></th>
											<th><?php echo esc_html__("Slug", "on-the-bench"); ?></th>
											<th><?php echo esc_html__("Description", "on-the-bench"); ?></th>

											<?php
												if(tofw_inventory_management_status() == true) :
											?>
											<th><?php echo esc_html__("Manage Woo Stock", "on-the-bench"); ?></th>
											<?php 
												endif;
											?>
											<th><?php echo esc_html__("Status", "on-the-bench"); ?></th>
											<th><?php echo esc_html__("Actions", "on-the-bench"); ?></th>
										</tr>
									</thead>

									<tbody>
										<?php
											global $wpdb;
											
											$on_the_bench_job_status = $wpdb->prefix.'tofw_otb_job_status';

											$select_query 	= "SELECT * FROM `".$on_the_bench_job_status."`";
											$select_results = $wpdb->get_results($select_query);
											
											$output = '';
											foreach($select_results as $result) {
																							
												$output .= '<tr><td>'.$result->status_id.'</td>';

												$output .= '<td><strong>'.$result->status_name.'</strong></td>';
												$output .= '<td>'.$result->status_slug.'</td>';
												$output .= '<td>'.$result->status_description.'</td>';
												
												if(tofw_inventory_management_status() == true) :
													if(empty($result->inventory_count) || $result->inventory_count == "off"): 
														$labelCount = "OFF";
													else:
														$labelCount = "ON";	
													endif;

													$output .= '<td><a href="#" class="change_tax_status" data-type="inventory_count" data-value="'.esc_attr($result->status_id).'">'.$labelCount.'</a></td>';
												endif;
												
												$output .= '<td><a href="#" title="'.esc_html__("Change Status", "on-the-bench").'" class="change_tax_status" data-type="status" data-value="'.esc_attr($result->status_id).'">'.$result->status_status.'</a></td>';
												$output .= '<td><a href="'.esc_url( add_query_arg( 'update_status', $result->status_id ) ).'" class="update_tax_status" data-type="status" data-value="'.esc_attr($result->status_id).'">'.esc_html__("Edit", "on-the-bench").'</a>';
												$output .= '</td></tr>';
											}

											echo wp_kses_post($output);
										?>	
									</tbody>
								</table>
							</div><!-- Post Stuff/-->

                        </div><!-- tab 3 Ends -->

                    </div><!-- tabs content ends -->

                </div>
			
			</div><!-- Main Content Div Ends /-->
						
			
			<div class="large-4 medium-4 small-12 cell">
				<?php //Sidebar // ?>
			</div><!-- Sidebar Div Ends /-->

		</div><!-- Row Ends /-->
	</div>

<?php	
}

	//Checking if form is submitted or not.
	if(isset($_POST['tofw_otb_settings']) && $_POST['tofw_otb_settings'] == '1') { 
		tofw_on_the_3_bench_settings_submission(); //running this function on form submission.
	}

	//Function to save data. 
	function tofw_on_the_3_bench_settings_submission() { 
		global $wpdb; //to use database functions inside function.

		$_POST['tofw_use_taxes'] 					= !isset($_POST['tofw_use_taxes'])? "" : $_POST['tofw_use_taxes'];
		$_POST['tofw_enable_woo_products'] 		= !isset($_POST['tofw_enable_woo_products'])? "" : $_POST['tofw_enable_woo_products'];
		$_POST['tofw_file_attachment_in_job'] 	= !isset($_POST['tofw_file_attachment_in_job'])? "" : $_POST['tofw_file_attachment_in_job'];

		$_POST['tofw_job_status_otb_notice'] 	= !isset($_POST['tofw_job_status_otb_notice'])? "" : $_POST['tofw_job_status_otb_notice'];

		update_option('tofw_pick_delivery_charges', strip_tags($_POST['pick_deliver'])); //Processing pickup and delivery charges.
		update_option('menu_name_p', strip_tags($_POST['menu_name']));
		update_option("on_the_bench_logo", esc_url_raw($_POST["on_the_bench_logo"]));
		update_option("on_the_bench_email", sanitize_email($_POST["on_the_bench_email"]));
		update_option('tofw_offer_pick_deli', strip_tags($_POST['offer_pic_de']));//Processing offer_pic_de checkbox.
		//update_option('tofw_add_admin_to_technician', strip_tags($_POST['add_admin_to_technician']));
		update_option("tofw_job_status_otb_notice", strip_tags($_POST["tofw_job_status_otb_notice"]));
		update_option('tofw_use_taxes', strip_tags($_POST['tofw_use_taxes']));//Processing offer_Warranty checkbox.
		update_option('tofw_enable_woo_products', strip_tags($_POST['tofw_enable_woo_products']));// Enable Woo Prouducts Replace Parts
		update_option('tofw_file_attachment_in_job', strip_tags($_POST['tofw_file_attachment_in_job']));// Enable Woo Prouducts Replace Parts
		update_option('tofw_primary_tax', strip_tags($_POST['tofw_primary_tax']));//Processing offer_Warranty checkbox.
		update_option('tofw_offer_Warranty', strip_tags($_POST['offer_Warranty']));//Processing offer_Warranty checkbox.
		update_option('tofw_one_day', strip_tags($_POST['offer_Warranty_one']));//Processing offer_Warranty for one day input box.
		update_option('tofw_one_week', strip_tags($_POST['offer_Warranty_week']));//Processing offer_Warranty for one week input box.
		update_option('tofw_system_currency', strip_tags($_POST['system_currency']));
		update_option('tofw_business_terms', strip_tags($_POST['business_terms']));

		add_action("admin_notices", "tofw_main_settings_saved");
	}//End of tofw_on_the_3_bench_settings_submission()

	if(!function_exists("tofw_main_settings_saved")):
		function tofw_main_settings_saved() {
			$content = '<div class="updated">';
			$content .= '<p>'.esc_html__("Settings saved!", "on-the-bench").'</p>';
			$content .= '</div>';

			echo $content;	
		}
	endif;