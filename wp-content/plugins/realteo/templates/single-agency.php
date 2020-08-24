<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$template_loader = new Realteo_Template_Loader;
get_header(get_option('header_bar_style','standard') );
$layout = get_post_meta( $post->ID, '_layout', true ); 
if(empty($layout)) { $layout = realteo_get_option('default_gallery');}?>


<?php if ( have_posts() ) : ?>
<!-- Titlebar
================================================== -->
<div id="titlebar" class="property-titlebar margin-bottom-0  print-only">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				
				
					<h2><?php the_title(); ?> </h2>

					<!-- Breadcrumbs -->
					<?php if(function_exists('bcn_display')) { ?>
	                    <nav id="breadcrumbs">
	                        <ul>
	                            <?php bcn_display_list(); ?>
	                        </ul>
	                    </nav>
	                <?php } ?>
				

			</div>
		</div>
	</div>
</div>

<!-- Content
================================================== -->
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<?php $template_loader->get_template_part( 'archive-agency/content-agency' ); ?>
		</div>
	</div>
</div>

<!-- Content
================================================== -->
<div class="container">
	<div class="row sticky-wrapper">

		<div class="col-lg-8 col-md-8">

			<div class="style-1 agency-tabs">

				<!-- Tabs Navigation -->
				<ul class="tabs-nav">
					<li class="active"><a href="#tab1"><?php esc_html_e('Our Properties','realteo') ?></a></li>
					<li><a href="#tab2"><?php esc_html_e('Our Agents','realteo') ?></a></li>
				</ul>

				<!-- Tabs Content -->
				<div class="tabs-container">
					<div class="tab-content" id="tab1">
						<!-- Main Search Input -->
					

						<!-- Sorting / Layout Switcher -->								
						<?php 
							$agency_var = get_query_var( 'agency' );
							if(is_numeric($agency_var)){
								$authors_id = get_post_meta( $agency_var,'agency_agent_of',true );
							} else {
								$agency_object = get_page_by_path( $agency_var, OBJECT, 'agency' ) ;
								$authors_id = get_post_meta($agency_object->ID,'realteo-agents',true);
							}
							if(!empty($authors_id)) {?>
								<form action="">
								<div class="main-search-input margin-bottom-35">							
									<input type="text" name="keyword_search" id="keyword_search" class="ico-01 " placeholder="<?php esc_html_e('Enter address e.g. street, city and state or zip','realteo') ?>" value="" autocomplete="off">
									<button class="button"><?php esc_html_e('Search','realteo'); ?></button>
								</div>
								</form>
								<?php
								echo do_shortcode('[properties agency="'.get_the_ID().'" with_keyword_search="yes"]'); 	
							} else {
								esc_html_e("This agency doesn't have any properties yet",'realteo');
							}
							

						?>
						<!-- Pagination / End -->
					</div>

					<div class="tab-content" id="tab2">
						<!-- Agents Container -->
						
			
							<?php 
							if($authors_id){
								$args = array(
									'include'  => $authors_id      
								);
								// Create the WP_User_Query object
		
								$wp_user_query = new WP_User_Query( $args );

								// Get the results
								$authors = $wp_user_query->get_results();
								$contact_details_flag = true;
								$contact_details_visibility = realteo_get_option('realteo_agent_contact_details');	
								if( $contact_details_visibility == 'loggedin' ) {
									$contact_details_flag = is_user_logged_in() ? true : false ;
								} 
								if( $contact_details_visibility == 'never' ) {
									$contact_details_flag = false;
								}
								echo '<div class="row">';
								foreach($authors as $agent) {
								    $agent_info = get_userdata( $agent->ID );
								  
								    $url = get_author_posts_url( $agent->ID );
								    $email = $agent_info->user_email;
								    ?>
								    <div class="col-lg-6 col-md-6 col-xs-12">
										<div class="agent">

											<div class="agent-avatar">
												<a href="<?php echo esc_url($url); ?>">
													<?php echo get_avatar($agent->user_email); ?>
													<span class="view-profile-btn"><?php esc_html_e('View Profile','findeo'); ?></span>
												</a>
											</div>

											<div class="agent-content">
												<div class="agent-name">
													<h4><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($agent_info->first_name); ?> <?php echo esc_html($agent_info->last_name); ?></a></h4>
													<?php if( isset( $agent_info->agent_title ) ) : ?><span><?php echo esc_html($agent_info->agent_title); ?></span><?php endif; ?>
												</div>
												<?php 
												if($contact_details_flag) { ?>
												<ul class="agent-contact-details">
													<?php if(isset($agent_info->phone) && !empty($agent_data->phone)): ?><li><i class="sl sl-icon-call-in"></i><a href="tel:<?php echo esc_html($agent_info->phone); ?>"><?php echo esc_html($agent_info->phone); ?></a></li><?php endif; ?>
													<?php $email_flag = realteo_get_option('realteo_agent_contact_email');
													if($email_flag != 'on' && isset($agent_info->user_email)): ?>
														<li><i class="fa fa-envelope-o "></i><a href="mailto:<?php echo esc_attr($email);?>"><?php echo esc_html($email);?></a></li>
													<?php endif; ?>
												</ul>
												<?php } ?>

												<ul class="social-icons">
													<?php
													$socials = array('facebook','twitter','gplus','linkedin');
													foreach ($socials as $social) {
														$social_value = get_user_meta($agent->ID, $social, true);
														if(!empty($social_value)){ ?>
															<li><a class="<?php echo esc_attr($social); ?>" href="<?php echo esc_url($social_value); ?>"><i class="icon-<?php echo esc_attr($social); ?>"></i></a></li>
														<?php }
													}
													?>
												</ul>
												<div class="clearfix"></div>
											</div>

										</div>
									</div>
									<?php
								}
								echo '</div>';
							} else {
								esc_html_e("This agency doesn't have any agents yet",'realteo');
							}?>
						
						<!-- Agents Container / End -->

					</div>

				</div>

			</div>

		</div>

		<!-- Sidebar -->
		<div class="col-lg-4 col-md-4">
			<div class="sidebar sticky right">
			<?php $agency_form =  realteo_get_option( 'agency_form' );
			if($agency_form): ?>
				<!-- Widget -->
				<div class="widget">
				<h3 class="margin-bottom-30 margin-top-30">Contact Us</h3>
					<!-- Agent Widget -->
					<?php  echo do_shortcode( sprintf( '[contact-form-7 id="%s" ]', realteo_get_option( 'agency_form' )) );?>
					<!-- Agent Widget / End -->

				</div>
				<!-- Widget / End -->
			<?php endif; ?>
			<?php 
				$latitude = get_post_meta( $post->ID, '_geolocation_lat', true ); 
				$longitude = get_post_meta( $post->ID, '_geolocation_long', true ); 
				

				if(!empty($latitude)) :  ?> 
				<!-- Widget -->
				<div class="widget">
					<h3 class="margin-bottom-30 margin-top-30">Our Location</h3>

					<div id="propertyMap-container" class="agency-map">
						<div id="propertyMap" data-latitude="<?php echo esc_attr($latitude); ?>" data-longitude="<?php echo esc_attr($longitude); ?>"></div>
					</div>

				</div>
				<!-- Widget / End -->
				<?php endif; ?>


			</div>
		</div>
		<!-- Sidebar / End -->

	</div>
</div>


<?php else : ?>

<?php get_template_part( 'content', 'none' ); ?>

<?php endif; ?>

	
<!-- Footer
================================================== -->
<div class="margin-top-55"></div>

<?php get_footer(); ?>