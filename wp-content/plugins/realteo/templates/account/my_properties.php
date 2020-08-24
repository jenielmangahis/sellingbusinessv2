<?php 
$ids = '';
if(isset($data)) :
	$ids	 	= (isset($data->ids)) ? $data->ids : '' ;
endif;
$message = $data->message;

?> 
<div class="col-md-8">
	<?php if(empty($ids)) : ?>
		<div class="notification notice margin-bottom-20">
			<p><?php printf( _e( 'You haven\'t submitted any properties yet, you can add your first one <a href="%s">below</a>', 'realteo' ), get_permalink( realteo_get_option( 'submit_property_page' ) ) );	 ?></p>
		</div><a href="<?php echo get_permalink( realteo_get_option( 'submit_property_page' ) ); ?>" class="margin-top-20 button"><?php esc_html_e('Submit New Property','realteo'); ?></a>
	<?php else: ?>
	<?php if(!empty($message )) { echo $message; } ?>
	<table class="manage-table responsive-table">
		
		<tr>
			<th><i class="fa fa-file-text"></i> <?php esc_html_e('Property','realteo'); ?></th>
			<th class="expire-date"><i class="fa fa-calendar"></i> <?php esc_html_e('Expiration Date','realteo'); ?></th>
			<th></th>
		</tr>
		<?php 
			foreach ($ids as $property_id) {
			$property = get_post($property_id) ?> 
			<!-- Item #1 -->
			<tr>
				<td class="title-container">
					<?php 
					if(has_post_thumbnail()){ 
						echo get_the_post_thumbnail($property);
					} else {
						$gallery = (array) get_post_meta( $property_id, '_gallery', true );
						$ids = array_keys($gallery);
						if(!empty($ids[0]) && $ids[0] !== 0){ 
							echo  wp_get_attachment_image($ids[0]); 
						}	
					}
					

					?>
					<div class="title">
						<h4><a href="<?php echo get_permalink( $property ) ?>"><?php echo get_the_title( $property ); ?></a>
						<small class="<?php echo esc_attr(get_post_status($property_id)); ?>">(<?php echo realteo_get_post_status($property_id) ?>)</small></h4>
						<span><?php the_property_address($property); ?></span>
						<span class="table-property-price"><?php the_property_price($property); ?></span>
					</div>
				</td>
				<td class="expire-date">
					<?php echo realteo_get_expiration_date($property_id); ?></td>
				<td class="action">
					<?php
						$actions = array();

						switch ( $property->post_status ) {
							case 'publish' :
								$actions['edit'] = array( 'label' => __( 'Edit', 'realteo' ), 'icon' => 'pencil', 'nonce' => false );
 								$actions['hide'] = array( 'label' => __( 'Hide', 'realteo' ), 'icon' => 'eye-slash', 'nonce' => true );
								break;
							
							case 'pending_payment' :
							case 'pending' :
								
								$actions['edit'] = array( 'label' => __( 'Edit', 'realteo' ), 'icon' => 'pencil', 'nonce' => false );
								
							break;

							case 'expired' :
								
								$actions['renew'] = array( 'label' => __( 'Renew', 'realteo' ), 'icon' => 'refresh', 'nonce' => false );
								
							break;
						}

						$actions['delete'] = array( 'label' => __( 'Delete', 'realteo' ), 'icon' => 'remove', 'nonce' => true );
						$actions           = apply_filters( 'realteo_my_properties_actions', $actions, $property );

						foreach ( $actions as $action => $value ) {
							if($action == 'edit' || $action == 'renew'){
								$action_url = add_query_arg( array( 'action' => $action,  'property_id' => $property->ID ), get_permalink( realteo_get_option( 'submit_property_page' )) );
							} else {
								$action_url = add_query_arg( array( 'action' => $action,  'property_id' => $property->ID ) );
							}
							if ( $value['nonce'] ) {
								$action_url = wp_nonce_url( $action_url, 'realteo_my_properties_actions' );
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
	</p>
	<?php if(realteo_get_option('submit_property_page')){ ?>
		<a href="<?php echo get_permalink( realteo_get_option( 'submit_property_page' ) ); ?>" class="margin-top-20 button"><?php esc_html_e('Submit New Property','realteo'); ?></a>
	<?php } ?>
	
	<?php endif; ?>
</div>

</div>