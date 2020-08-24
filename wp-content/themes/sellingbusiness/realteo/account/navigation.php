<?php
$current = '';
if(isset($data)) :
	$current	 	= (isset($data->current)) ? $data->current : '' ;
endif;
if(is_user_logged_in()) :
$current_user = wp_get_current_user();
$roles = $current_user->roles;
$role = array_shift( $roles );

$if_agencyowner = $current_user->agency_admin_of;
if($if_agencyowner)
	$array_agency = array_shift($if_agencyowner);
//echo $array_agency;
$checkOwner = 0;
if($if_agencyowner!=""){
    $checkOwner=1;
}
?>
<div class="col-md-4 stick-me">
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
				<li>
					<a href="<?php echo get_permalink( realteo_get_option( 'change_password_page' ) ); ?>"
					<?php if( $current == 'password' ) { echo 'class="current"'; }?> >
					<i class="sl sl-icon-lock"></i> <?php esc_html_e('Change Password','realteo');?>
					</a>
				</li>
				<?php endif; ?>
				<?php if(realteo_get_option( 'bookmarks_page' )) : ?>
				<li>
					<a href="<?php echo get_permalink(realteo_get_option( 'bookmarks_page' ))?>"
					<?php if( $current == 'bookmarks' ) { echo 'class="current"'; }?>>
						<i class="sl sl-icon-star"></i> <?php esc_html_e('Saved Listings','realteo');?>
					</a>
				</li>
				<?php endif; ?>
				<?php if(realteo_get_option( 'agency_page' ) && in_array($role,array('agent','administrator','admin','broker'))) : ?>
				<li>
					<a href="<?php echo get_permalink(realteo_get_option( 'agency_page' ))?>"
					<?php if( $current == 'agency' ) { echo 'class="current"'; }?>>
						<i class="sl sl-icon-note"></i> <?php esc_html_e('Agency Management','realteo');?>
					</a>
				</li>
				<?php endif; 
				if($role=='buyer'){
				?>
                <li>
					<a href="/business-alerts" <?php if( $current == 'business-alerts' ) { echo 'class="current"'; }?> >
						<i class="sl sl-icon-cursor"></i> Featured Business Alerts
					</a>
				</li>
                <?php } ?>
			</ul>
			<?php if(in_array($role,array('agent','administrator','admin','owner','broker'))) : ?>
			<ul class="my-account-nav">
				<li class="sub-nav-title"><?php esc_html_e('Manage Listings','realteo');?></li>
				<?php if( realteo_get_option( 'my_properties_page' ) ) { ?>
				<li>
					<a href="<?php echo get_permalink( realteo_get_option( 'my_properties_page' ) ); ?>"
					<?php if( $current == 'my_properties' ) { echo 'class="current"'; }?> >
						<i class="sl sl-icon-docs"></i>
						<?php esc_html_e('Current Businesses Listed','realteo');?>
					</a>
				</li>
				<?php } ?>
				<?php if( realteo_get_option( 'submit_property_page' ) ) { ?>
				<li>
					<a href="<?php echo get_permalink( realteo_get_option( 'submit_property_page' ) ); ?>"
					<?php if( $current == 'submit' ) { echo 'class="current"'; }?> >
						<i class="sl sl-icon-action-redo"></i>
						<?php esc_html_e('Submit New Business Listing','realteo');?>
					</a>
				</li>
				<?php /*
				<li>
					<a href="/feed-import">
						<i class="sl sl-icon-cloud-upload"></i>
						<?php esc_html_e('Feed XML Import','realteo');?>
					</a>
				</li> */ ?>
				<?php } ?>
				<?php if( realteo_get_option( 'property_packages_page' ) ) { 
				if(in_array($role,array('agent','administrator','admin','broker'))) { ?>
				<li>
					<a href="<?php echo get_permalink( realteo_get_option( 'property_packages_page' ) ); ?>"
					<?php if( $current == 'my_packages' ) { echo 'class="current"'; }?> >
						<i class="sl sl-icon-basket"></i>
						<?php esc_html_e('My Packages','realteo');?>
					</a>
				</li>
				<?php }} ?>
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

            <?php if(in_array($role,array('agent','administrator','admin','owner','broker'))) : ?>
            <ul class="my-account-nav">
                <li class="sub-nav-title"><?php esc_html_e('Advertisment');?></li>
                <li><a class="ad-enquiry" href=""><i class="sl sl-icon-basket"></i> <?php esc_html_e('Enquire about Banner Ads');?></a></li>
            </ul>
            <?php endif; ?>

			<ul class="my-account-nav">
				<?php if( realteo_get_option( 'change_password_page' ) ) { ?>
				<?php } ?>
				<li><a href="<?php echo wp_logout_url(get_permalink(realteo_get_option( 'my_account_page' ))); ?>"><i class="sl sl-icon-power"></i> <?php esc_html_e('Log Out','realteo');?></a></li>
			</ul>

		</div>

	</div>
</div>
<?php endif; ?>