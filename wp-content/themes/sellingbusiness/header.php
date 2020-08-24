<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package findeo
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link href="https://fonts.googleapis.com/css?family=Varela+Round" rel="stylesheet">
<?php /*
<script charset="UTF-8" src="//cdn.sendpulse.com/js/push/2988e9d754a92349f111e585183f1d25_1.js" async></script>
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<script>
  (adsbygoogle = window.adsbygoogle || []).push({
    google_ad_client: "ca-pub-9935254576425564",
    enable_page_level_ads: true
  });
</script>
*/ ?>
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<!-- Wrapper -->
<div id="wrapper">
<?php do_action('realteo_after_wrapper'); ?>
<?php 
$header_layout = get_option('findeo_header_layout') ;

$sticky = get_option('findeo_sticky_header') ;

if(is_singular()){

	$header_layout_single = get_post_meta($post->ID, 'findeo_header_layout', TRUE); 

	switch ($header_layout_single) {
		case 'on':
		case 'enable':
			$header_layout = 'fullwidth';
			break;

		case 'disable':
			$header_layout = false;
			break;	

		case 'use_global':
			$header_layout = get_option('findeo_header_layout'); 
			break;
		
		default:
			$header_layout = get_option('findeo_header_layout'); 
			break;
	}


	$sticky_single = get_post_meta($post->ID, 'findeo_sticky_header', TRUE); 
	switch ($sticky_single) {
		case 'on':
		case 'enable':
			$sticky = true;
			break;

		case 'disable':
			$sticky = false;
			break;	

		case 'use_global':
			$sticky = get_option('findeo_sticky_header'); 
			break;
		
		default:
			$sticky = get_option('findeo_sticky_header'); 
			break;
	}
	
}


$header_layout = apply_filters('findeo_header_layout_filter',$header_layout);
$sticky = apply_filters('findeo_sticky_header_filter',$sticky); 

?>
<!-- Header Container
================================================== -->
<header id="header-container" class="<?php echo ($sticky == true || $sticky == 1) ? "sticky-header" : ''; ?> <?php echo esc_attr($header_layout); ?>">

	<?php get_template_part( 'topbar' ); ?>
	<!-- Topbar / End -->
	<!-- Header -->
	<div id="header">
		<div class="container">
			
			<!-- Left Side Content -->
			<div <?php if( function_exists('realteo_get_option')) { ?> class="left-side" <?php } ?> >
				<div id="logo">
					<?php 
		                $logo = get_option( 'pp_logo_upload', '' ); 
		                $logo_retina = get_option( 'pp_retina_logo_upload', '' ); 
		             	if($logo) {
		                    if(is_front_page()){ ?>
		                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home"><img src="<?php echo esc_url($logo); ?>" data-rjs="<?php echo esc_url($logo_retina); ?>" alt="<?php esc_attr(bloginfo('name')); ?>"/></a>
		                    <?php } else { ?>
		                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><img src="<?php echo esc_url($logo); ?>" data-rjs="<?php echo esc_url($logo_retina); ?>" alt="<?php esc_attr(bloginfo('name')); ?>"/></a>
		                    <?php }
		                } else {
		                    if(is_front_page()) { ?>
		                    <h1><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
		                    <?php } else { ?>
		                    <h2><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h2>
		                    <?php }
		                }
	                ?>
                </div>
                <?php if(get_theme_mod('workscout_tagline_switch','hide') == 'show') { ?><div id="blogdesc"><?php bloginfo( 'description' ); ?></div><?php } ?>
				<!-- Logo -->

				<!-- Mobile Navigation -->
				<div class="mmenu-trigger">
					<button class="hamburger hamburger--collapse" type="button">
						<span class="hamburger-box">
							<span class="hamburger-inner"></span>
						</span>
					</button>
				</div>


				<!-- Main Navigation -->
				<nav id="navigation" class="style-1">
					<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_id' => 'responsive', 'container' => false ) );  ?>
			
				</nav>
				<div class="clearfix"></div>
				<!-- Main Navigation / End -->
				
			</div>
			<!-- Left Side Content / End -->
			<?php 

			$my_account_display = get_option('findeo_my_account_display', true );
			$submit_display = get_option('findeo_submit_display', true );
			if($my_account_display != false || $submit_display != false ) :	?>
			<!-- Right Side Content / End -->
			<div class="right-side">
				<!-- Header Widget -->
				<div class="header-widget">
					<div class="headtrans"><?php echo do_shortcode('[gtranslate]'); ?></div>
					<?php if( true == $my_account_display) : ?>
					
							<?php
							if ( is_user_logged_in() &&  function_exists('realteo_get_option')) { 
								$current_user = wp_get_current_user();
								$roles = $current_user->roles;
								$role = array_shift( $roles ); 
								if(!empty($current_user->user_firstname)){
									$name = $current_user->user_firstname;
								} else {
									$name =  $current_user->display_name;
								}?>
	
								<div class="user-menu">
									<div class="user-name"><span><?php echo get_avatar( $current_user->user_email, 32 );?></span>
										<?php esc_html_e('Hi,','findeo') ?> <?php echo $name; ?></div>
									<ul>
									<?php do_action('realteo_user_menu_before'); ?>	
									<li>
										<a href="<?php echo get_permalink(realteo_get_option( 'my_account_page' )); ?>" >
											<i class="sl sl-icon-user"></i> <?php esc_html_e('My Profile','findeo');?>
										</a>
									</li>
									<li>
										<a href="<?php echo get_permalink(realteo_get_option( 'bookmarks_page' ))?>" >
											<i class="sl sl-icon-star"></i> <?php esc_html_e('Bookmarks','findeo');?>
										</a>
									</li>
									<?php if(in_array($role,array('agent','administrator','admin','owner'))) : ?>
									<li>
										<a href="<?php echo get_permalink( realteo_get_option( 'my_properties_page' ) ); ?>">
											<i class="sl sl-icon-docs"></i> 
											<?php esc_html_e('My Businesses','findeo');?>
										</a>
									</li>
									<?php endif; ?>
									<?php do_action('realteo_user_menu_after'); ?>	
									<li><a href="<?php echo wp_logout_url(home_url()); ?>"><i class="sl sl-icon-power"></i> <?php esc_html_e('Log Out','findeo');?></a>
									</li>
									</ul>
								</div>
							<?php } else { ?>
									<a href="<?php echo get_permalink(realteo_get_option( 'my_account_page' ))?>" class="sign-in"><i class="fa fa-user"></i> <?php esc_html_e('Log In / Register','findeo');  ?>
							<?php }	?>
						</a>
					<?php endif;?>
					
					<?php 
			
					if( true == $submit_display ) : ?>
						<a href="<?php echo get_permalink( realteo_get_option( 'submit_property_page' ) ); ?>" class="button border"><?php esc_html_e('Submit Business','findeo'); ?></a>
					<?php endif; ?>
				</div>
			</div>
			<!-- Right Side Content / End -->
			<?php endif; ?>
				<!-- Header Widget / End -->
			

		</div>
	</div>
	<!-- Header / End -->

</header>
<div class="clearfix"></div>
<!-- Header Container / End -->