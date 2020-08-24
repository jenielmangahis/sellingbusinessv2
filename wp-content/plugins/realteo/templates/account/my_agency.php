<?php 
wp_enqueue_script( 'jquery-ui-autocomplete' );
wp_enqueue_style( 'jquery-ui-styles' );


$is_admin 		= '';
$is_temp_agent 	= '';
$is_agent 		= '';
if(isset($data)) :
	$is_agent	 	= (isset($data->is_agent)) ? $data->is_agent : '' ;
	$is_temp_agent	= (isset($data->is_temp_agent)) ? $data->is_temp_agent : '' ;
	$agencies	 	= (isset($data->is_admin)) ? $data->is_admin : '' ;
endif;

?>
<div class="col-md-8 my-agency-page">
<?php if(isset($data->message) && !empty($data->message) ) { echo $data->message;} ?>
<?php

if($is_temp_agent) : 
	foreach ($is_temp_agent as $id) {
		$has_temp_agencies = ($id && realteo_post_exists($id)) ? true : false ;
	} 
	if($has_temp_agencies) : ?>
	<div class="notification notice stick-to-table">
		<p><?php esc_html_e( "You've been invited to the following agencies", 'realteo' );	 ?></p>
	</div>
	<table class="manage-table invited-to-agency-table responsive-table">
		<tr>
			<th style="width:80%"><i class="fa fa-briefcase"></i> <?php esc_html_e('Agency','realteo'); ?></th>
			<th></th>
		</tr>
		<?php
			foreach ($is_temp_agent as $agency_id) {
				$agency = get_post($agency_id); 
				if( $agency && in_array($agency->post_status, array('publish','pending_payment','expired','draft','pending'))) : ?>
				<tr>
					<td>
						<h4><a href="<?php echo esc_url(get_permalink($agency->ID)) ?>"><?php echo $agency->post_title; ?></a></h4>
					</td>
					<td class="action">
						<?php 
							$actions = array();
							$actions['confirm'] = array( 'label' => __( 'Confirm', 'realteo' ), 'icon' => 'check-square', 'nonce' => true );
							$actions['reject'] = array( 'label' => __( 'Reject', 'realteo' ), 'icon' => 'remove', 'nonce' => true );
							$actions           = apply_filters( 'realteo_my_agencies_actions', $actions, $agency );

							foreach ( $actions as $action => $value ) {
								
								$action_url = add_query_arg( array( 'action' => $action,  'agency_id' => $agency_id ) );
								
								if ( $value['nonce'] ) {
									$action_url = wp_nonce_url( $action_url, 'realteo_my_agency_actions' );
								}
						
								echo '<a href="' . esc_url( $action_url ) . '" class="' . esc_attr( $action ) . ' realteo-dashboard-action-' . esc_attr( $action ) . '">';
								
								if(isset($value['icon']) && !empty($value['icon'])) {
									echo '<i class="fa fa-'.$value['icon'].'"></i>';
								}

								 echo esc_html( $value['label'] ) . '</a>';
							}
						?>
						
					</td>
				</tr>
			<?php endif;
			} ?>
	</table>
<?php endif;
endif;

// table for agents agencies
if($is_agent) : 
	foreach ($is_agent as $id) {
		$has_agencies = ($id && realteo_post_exists($id)) ? true : false ;
	} 
	if($has_agencies) : ?>
<div class="notification notice stick-to-table">
		<p><?php esc_html_e( "You're assigned as agent to the following agencies", 'realteo' );	 ?></p>
	</div>
	<table class="manage-table agent-of-agency-table responsive-table">
		<tr>
			<th style="width:80%"><i class="fa fa-briefcase"></i> <?php esc_html_e('Agency','realteo'); ?></th>
			<th></th>
		</tr>
		<?php
			foreach ($is_agent as $agency_id) {
				$agency = get_post($agency_id); ?>
				<tr>
					<td>
						<h4><a href="<?php echo esc_url(get_permalink($agency->ID)) ?>"><?php echo $agency->post_title; ?></a></h4>
					</td>
					<td class="action">
						<?php 
							$actions = array();
							
							$actions['remove'] = array( 'label' => __( 'Remove', 'realteo' ), 'icon' => 'remove', 'nonce' => true );
							$actions           = apply_filters( 'realteo_my_agencies_actions', $actions, $agency );

							foreach ( $actions as $action => $value ) {
								
								$action_url = add_query_arg( array( 'action' => $action,  'agency_id' => $agency_id ) );
								
								if ( $value['nonce'] ) {
									$action_url = wp_nonce_url( $action_url, 'realteo_my_agency_actions' );
								}
						
								echo '<a href="' . esc_url( $action_url ) . '" class="' . esc_attr( $action ) . ' realteo-dashboard-action-' . esc_attr( $action ) . '">';
								
								if(isset($value['icon']) && !empty($value['icon'])) {
									echo '<i class="fa fa-'.$value['icon'].'"></i>';
								}

								 echo esc_html( $value['label'] ) . '</a>';
							}
						?>
						
					</td>
				</tr>
			<?php } ?>
	</table>
<?php endif;
endif;

if($agencies) : ?>
	<div class="notification notice stick-to-table">
		<p><?php esc_html_e( 'Agencies that you can manage', 'realteo' );	 ?></p>
	</div>
	<table class="manage-table admin-of-agency-table responsive-table">
		<tr>
			<th style="width:25%"><i class="fa fa-briefcase"></i> <?php esc_html_e('Agency','realteo'); ?></th>
			<th style="width:70%"><i class="fa fa-user"></i> <?php esc_html_e('Assigned Agents','realteo'); ?></th>
			
			<th></th>
		</tr>
		<?php foreach ($agencies as $agency_id) { 
			$agency = get_post($agency_id);
			if($agency &&  in_array($agency->post_status,array('publish','pending_payment','expired','draft','pending'))) : ?>
			<tr>
				<td>
					<h4><a href="<?php echo esc_url(get_permalink($agency->ID)) ?>"><?php echo $agency->post_title; ?></a></h4>	
					<small class="<?php echo esc_attr(get_post_status($agency_id)); ?>"><?php echo realteo_get_post_status($agency_id) ?></small>
				</td>
				<td>
					<?php 
					$authors_id = get_post_meta($agency->ID,'realteo-agents',true);
					if($authors_id){
						$args = array(
							'include'  => $authors_id      
						);
						$wp_user_query = new WP_User_Query( $args );

						// Get the results
						$authors = $wp_user_query->get_results();
						
						echo '<ul class="list-4 color">';
						foreach($authors as $agent) {

						    $agent_info = get_userdata( $agent->ID ); ?>
							
							<li>
								<?php echo esc_html($agent_info->first_name); ?> <?php echo esc_html($agent_info->last_name); ?>
								
									<a data-agent="<?php echo $agent->ID; ?>" data-agency="<?php echo $agency->ID; ?>" class="remove-agent-list" href="#"><?php esc_html_e('Remove','realteo'); ?></a>
							
							</li>

						<?php } //eof foreach ?>
						</ul>

					<?php } ?>
					<?php if ( get_post_status ( $agency_id ) == 'publish' ) { ?>
						<div class="add-agent-2-agency-form">
							<span class="add-new-agent-title"><?php esc_html_e('Add New Agent','realteo'); ?></span>
							<form action="" id="search_agent_<?php echo $agency->ID ?>"  class="search_agent">
								<input  placeholder="Type agent email or login" type="text" data-agency="<?php echo $agency->ID ?>" name="find_agents" id="add_agent"> <button class="button"><i class="fa fa-search" aria-hidden="true"></i><div id="search-results-loading" style="display: none;"><i class="fa fa-circle-o-notch fa-spin fa-fw"></i></div></button>
								<div id="search-results"></div>
							</form>
							
						</div>
					<?php } ?>
				</td>
				
				<td class="action">
					<?php
						$actions = array();

				
						$actions['edit-agency'] = array( 'label' => __( 'Edit', 'realteo' ), 'icon' => 'pencil', 'nonce' => false );
						$actions['delete'] = array( 'label' => __( 'Delete', 'realteo' ), 'icon' => 'remove', 'nonce' => true );
						$actions           = apply_filters( 'realteo_my_agencies_actions', $actions, $agency );

						foreach ( $actions as $action => $value ) {
							if($action == 'edit-agency' ){
								$action_url = add_query_arg( array( 'action' => $action,  'agency_id' => $agency->ID ), get_permalink( realteo_get_option( 'agency_submit_page' )) );
							} else {
								$action_url = add_query_arg( array( 'action' => $action,  'agency_id' => $agency->ID ) );
							}
							if ( $value['nonce'] ) {
								$action_url = wp_nonce_url( $action_url, 'realteo_my_agency_actions' );
							}
					
							echo '<a href="' . esc_url( $action_url ) . '" class="' . esc_attr( $action ) . ' realteo-dashboard-action-' . esc_attr( $action ) . '">';
							
							if(isset($value['icon']) && !empty($value['icon'])) {
								echo '<i class="fa fa-'.$value['icon'].'"></i>';
							}

							 echo esc_html( $value['label'] ) . '</a>';
						}
					?>

				</td>
			</tr>

		<?php else : ?>
			<tr>
				<td colspan="2">
					<?php 
					esc_html_e('It seems this agency was removed or is hidden','realteo'); 
					$action_url = add_query_arg( array( 'action' => 'remove_admin',  'agency_id' => $agency_id ) );
					$action_url = wp_nonce_url( $action_url, 'realteo_my_agency_actions' ); ?>				
					<a href="<?php echo esc_url( $action_url ); ?>"> <?php esc_html_e('You can remove yourself as admin', 'realteo'); ?></a>
				</td>
			
			</tr>
		<?php endif;
		} ?>

	</table>

<?php endif; ?>

	<a class="button" href="<?php echo get_permalink(realteo_get_option( 'agency_submit_page' ))?>"> <?php esc_html_e('Create agency ', 'realteo'); ?>
	</a>


</div>