<?php 
$current = '';
if(isset($data)) :
	$current	 	= (isset($data->current)) ? $data->current : '' ;
endif;
if(is_user_logged_in()) : 
$current_user = wp_get_current_user();
$roles = $current_user->roles;
$role = array_shift( $roles ); 
?>
<div class="col-md-4">
	<div class="sidebar left">

		<div class="my-account-nav-container">
			
			<ul class="my-account-nav">
				<li class="sub-nav-title"><?php esc_html_e('Manage Account','realteo');?></li>
				<?php if(realteo_get_option( 'my_account_page' )) : ?>
				<li>
					<a href="<?php echo get_permalink(realteo_get_option( 'my_account_page' )); ?>" 
					<?php if( $current == 'profile' ) { echo 'class="current"'; }?>>
					<i class="sl sl-icon-user"></i> <?php esc_html_e('My Profile','realteo');?>
					</a>
				</li>
				<?php endif; ?>
				<?php if(realteo_get_option( 'bookmarks_page' )) : ?>
				<li>
					<a href="<?php echo get_permalink(realteo_get_option( 'bookmarks_page' ))?>" 
					<?php if( $current == 'bookmarks' ) { echo 'class="current"'; }?>>
						<i class="sl sl-icon-star"></i> <?php esc_html_e('Bookmarked Listings','realteo');?>
					</a>
				</li>	
				<?php endif; ?>
				<?php if(realteo_get_option( 'agency_page' ) && in_array($role,array('agent','administrator','admin'))) : ?>
				<li>
					<a href="<?php echo get_permalink(realteo_get_option( 'agency_page' ))?>" 
					<?php if( $current == 'agency' ) { echo 'class="current"'; }?>>
						<i class="sl sl-icon-note"></i> <?php esc_html_e('Agency Management','realteo');?>
					</a>
				</li>
				<?php endif; ?>
			</ul>
			<?php if(in_array($role,array('agent','administrator','admin','owner'))) : ?>
			<ul class="my-account-nav">
				<li class="sub-nav-title"><?php esc_html_e('Manage Listings','realteo');?></li>
				<?php if( realteo_get_option( 'my_properties_page' ) ) { ?>
				<li>
					<a href="<?php echo get_permalink( realteo_get_option( 'my_properties_page' ) ); ?>" 
					<?php if( $current == 'my_properties' ) { echo 'class="current"'; }?> >
						<i class="sl sl-icon-docs"></i> 
						<?php esc_html_e('My Properties','realteo');?>
					</a>
				</li>
				<?php } ?>
				<?php if( realteo_get_option( 'submit_property_page' ) ) { ?>
				<li>
					<a href="<?php echo get_permalink( realteo_get_option( 'submit_property_page' ) ); ?>"
					<?php if( $current == 'submit' ) { echo 'class="current"'; }?> >
						<i class="sl sl-icon-action-redo"></i> 
						<?php esc_html_e('Submit New Property','realteo');?>
					</a>
				</li>
				<?php } ?>	
				<?php if( realteo_get_option( 'property_packages_page' ) ) { ?>
				<li>
					<a href="<?php echo get_permalink( realteo_get_option( 'property_packages_page' ) ); ?>"
					<?php if( $current == 'my_packages' ) { echo 'class="current"'; }?> >
						<i class="sl sl-icon-basket"></i> 
						<?php esc_html_e('My Packages','realteo');?>
					</a>
				</li>
				<?php } ?>	
				<?php if( realteo_get_option( 'my_orders_page' ) ) { ?>
				<li>
					<a href="<?php echo get_permalink( realteo_get_option( 'my_orders_page' ) ); ?>">
						<i class="sl sl-icon-folder"></i> 
						<?php esc_html_e('My Orders','realteo');?>
					</a>
				</li>
				<?php } ?>
			</ul>
			<?php endif; ?>
			<ul class="my-account-nav">
				<?php if( realteo_get_option( 'change_password_page' ) ) { ?>
				<li>
					<a href="<?php echo get_permalink( realteo_get_option( 'change_password_page' ) ); ?>"
					<?php if( $current == 'password' ) { echo 'class="current"'; }?> >
					<i class="sl sl-icon-lock"></i> <?php esc_html_e('Change Password','realteo');?>
					</a>
				</li>
				<?php } ?>
				<li><a href="<?php echo wp_logout_url(get_permalink(realteo_get_option( 'my_account_page' ))); ?>"><i class="sl sl-icon-power"></i> <?php esc_html_e('Log Out','realteo');?></a></li>
			</ul>

		</div>

	</div>
</div>
<?php endif; ?>