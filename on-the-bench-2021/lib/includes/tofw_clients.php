<?php
	function tofw_on_the_3_bench_clients() {
		if (!current_user_can('manage_options')) {
			wp_die( esc_html__('You do not have sufficient permissions to access this page.', "on-the-bench") );
		}
		
		if ( ! current_user_can( 'list_users' ) ) {
			wp_die(
				'<h1>' . esc_html__( 'You need a higher level of permission.', "on-the-bench" ) . '</h1>' .
				'<p>' . esc_html__( 'Sorry, you are not allowed to list users.', "on-the-bench" ) . '</p>',
				403
			);
		}

		// Pagination vars
		$current_page 	= isset($_GET['paged']) ? $_GET['paged'] : 1; //get_query_var('paged') ? (int) get_query_var('paged') : 1;
		$users_per_page = 20;

		$current_page = (int)$current_page;
		$users_per_page = (int)$users_per_page;

		$args 			= array( 
								'role' 		=> 'customer', 
								'echo' 		=> 0,
								'orderby'	=> 'ID',
								'order'		=> 'DESC',
								'number' 	=> $users_per_page,
								'paged' 	=> $current_page
						);
		//$users_array 	= get_users($args);

		$users_obj 		= new WP_User_Query( $args );

		$total_users 	= $users_obj->get_total();
		$num_pages 		= ceil($total_users / $users_per_page);
?>		
		<div class="wrap" id="poststuff">
				<h1 class="wp-heading-inline">
					<?php 
						echo esc_html__( "Manage Clients", "on-the-bench" );
					?>
				</h1>
				<a data-open="customerFormReveal" class="page-title-action"><?php echo esc_html__("Add New", "on-the-bench"); ?></a>
				<br class="clear" />
				<?php
					$display_from 	= (($current_page*$users_per_page)-$users_per_page);
					$display_to 	= ($current_page*$users_per_page);

					$display_to 	= ($display_to >= $total_users) ? $total_users : $display_to;
				?>
				<p><?php echo esc_html__("Display", "on-the-bench")." ".$display_from." - ".$display_to." ".esc_html__("From Total", "on-the-bench")." ".$total_users." ".esc_html__("Clients.", "on-the-bench"); ?></p>
			
			<table class="wp-list-table widefat fixed striped users">
				<thead>
				<tr>
						<th class="manage-column column-id">
							<span><?php echo esc_html__("ID", "on-the-bench"); ?></span>
						</th>
						<th class="manage-column column-name">
							<span><?php echo esc_html__("Name", "on-the-bench"); ?></span>
						</th>
						<th class="manage-column column-email">
							<span><?php echo esc_html__("Email", "on-the-bench"); ?></span>
						</th>
						<th class="manage-column column-phone">
							<?php echo esc_html__("Phone", "on-the-bench"); ?>
						</th>
						<th class="manage-column column-address">
							<?php echo esc_html__("Address", "on-the-bench"); ?>
						</th>
						<th class="manage-column column-company">
							<?php echo esc_html__("Company", "on-the-bench"); ?>
						</th>
						<th class="manage-column column-jobs">
							<?php echo esc_html__("Jobs", "on-the-bench"); ?>
						</th>
					</tr>
				</thead>

				<tbody data-wp-lists="list:user">

					<?php 
						$content = '';

						foreach($users_obj->get_results() as $userdata) {
							$user 			= get_user_by('id', $userdata->ID);
							$phone_number 	= get_user_meta($userdata->ID, "customer_phone", true);
							$company 		= get_user_meta($userdata->ID, "company", true);
							$address 		= get_user_meta($userdata->ID, "customer_address", true);
							$city 			= get_user_meta($userdata->ID, "customer_city", true);
							$zip_code 		= get_user_meta($userdata->ID, "zip_code", true);

							$content .= '<tr>';
							$content .= '<td class="id column-id num" data-colname="ID">';
							$content .= $userdata->ID;
							$content .= '</td>';	

							$content .= '<td class="username column-username has-row-actions column-primary" data-colname="Username">
											<strong>
												<a href="edit.php?post_type=otb_jobs&job_customer='.$userdata->ID.'">
													'.$user->first_name . ' ' . $user->last_name.'
												</a>
											</strong><br>
											<div class="row-actions">
												<span class="edit">
													<a href="'.add_query_arg(array('update_user' => $userdata->ID)).'" class="update_user_form">
														'.esc_html__("Edit", "on-the-bench").'
													</a> | 
												</span>
												<span class="remove">
													<a href="edit.php?post_type=otb_jobs&job_customer='.$userdata->ID.'">
														'.esc_html__("View Jobs", "on-the-bench").'
													</a> 
												</span>
											</div>
											<button type="button" class="toggle-row"><span class="screen-reader-text">'.esc_html__("Show more details", "on-the-bench").'</span></button>
										</td>';
						
							$content .= '<td class="email column-email" data-colname="Email">
											<a href="mailto:'.$userdata->user_email.'">
											'.$userdata->user_email.'
											</a>
										</td>';

							$content .= '<td class="phone column-phone" data-colname="phone">';
											if(!empty($phone_number)):
												$content .= '<a href="tel:'.$phone_number.'">'.$phone_number.'</a>';
											endif;
							$content .= '</td>';			
							
							$content .= '<td class="address column-address" data-colname="Address">';
											if(!empty($address)) {
												$content .= $address.", ";
											}
											if(!empty($city)) {
												$content .= $city.", ";	
											}
											if(!empty($zip_code)) {
												$content .= $zip_code;
											}
							$content .= '</td>';

							$content .= '<td class="company column-company" data-colname="Company">';
							$content .= $company;
							$content .= '</td>';
			
							$content .= '<td class="jobs column-jobs num" data-colname="Jobs">';
							$content .= tofw_return_jobs_by_user($userdata->ID, "customer");
							$content .= '</td></tr>';
						}

						echo $content;
					?>
				</tbody>

				<tfoot>
					<tr>
						<th class="manage-column column-id">
							<span><?php echo esc_html__("ID", "on-the-bench"); ?></span>
						</th>
						<th class="manage-column column-name">
							<span><?php echo esc_html__("Name", "on-the-bench"); ?></span>
						</th>
						<th class="manage-column column-email">
							<span><?php echo esc_html__("Email", "on-the-bench"); ?></span>
						</th>
						<th class="manage-column column-phone">
							<?php echo esc_html__("Phone", "on-the-bench"); ?>
						</th>
						<th class="manage-column column-address">
							<?php echo esc_html__("Address", "on-the-bench"); ?>
						</th>
						<th class="manage-column column-company">
							<?php echo esc_html__("Company", "on-the-bench"); ?>
						</th>
						<th class="manage-column column-jobs">
							<?php echo esc_html__("Jobs", "on-the-bench"); ?>
						</th>
					</tr>
				</tfoot>
			</table>


			<div class="tablenav-pages" style="float:right;margin-top:20px;">
				<span class="displaying-num">
					<?php 
						echo $total_users." ".esc_html__("items", "on-the-bench"); 
					?>
				</span>

				<span class="pagination-links">
			
					<?php
						// Previous page
						if ( $current_page > 1 ) {
						?>
							<a class="first-page button" href="<?php echo add_query_arg(array('paged' => 1)); ?>">
								<span aria-hidden="true">«</span>
							</a>
							<a class="prev-page button" href="<?php echo add_query_arg(array('paged' => $current_page-1)); ?>">
								<span aria-hidden="true">‹</span>
							</a>
						<?php } ?>

					<span id="table-paging" class="paging-input">
						<span class="tablenav-paging-text"><?php echo $current_page; ?> <?php echo esc_html__("of", "on-the-bench"); ?> <span class="total-pages"><?php echo $num_pages; ?></span></span></span>
				
					<?php
					// Next page
					if ( $current_page < $num_pages ) {
					?>
						<a class="next-page button" href="<?php echo add_query_arg(array('paged' => $current_page+1)); ?>"><span aria-hidden="true">›</span></a>
						<a class="last-page button" href="<?php echo add_query_arg(array('paged' => $num_pages)); ?>"><span aria-hidden="true">»</span></a></span>
					<?php } ?>
			</div>
			</div> <!-- Wrap Ends /-->
		<?php
		add_filter('admin_footer','tofw_add_user_form');

		add_filter('admin_footer','tofw_update_user_form');
	}//add category function ends here.