<?php
/**
 * Alternative header for our theme
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

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<!-- Wrapper -->
<div id="wrapper">
<?php do_action('realteo_after_wrapper'); ?>

<!-- Header Container
================================================== -->
<?php 

$header_layout = get_option('findeo_header_layout') ;
if(is_singular()){
	$header_layout = get_post_meta($post->ID, 'findeo_header_layout', TRUE);
	if(empty($header_layout)) {
		$header_layout = get_option('findeo_header_layout') ;
	} else {
		$header_layout = 'fullwidth';
	}	
	

}
$header_layout = apply_filters('findeo_header_layout_filter',$header_layout);
?>
<header id="header-container"  class="header-style-2 <?php echo esc_attr($header_layout); ?>">

	<?php // get_template_part( 'topbar' ); ?>
	<!-- Topbar / End -->
	<!-- Header -->
	<div id="header">
		<div class="container">
			
			<!-- Left Side Content -->
			<div class="left-side">
				<div id="logo">
					<?php 
		                $logo = get_option( 'pp_logo_upload', '' ); 
		                $logo_retina = get_option( 'pp_retina_logo_upload', '' ); 
		                $sticky_logo_retina = get_option( 'pp_sticky_logo_upload', '' ); 
		             	if($logo) {
		                    if(is_front_page()){ ?>
		                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home"><img src="<?php echo esc_url($logo); ?>" data-rjs="<?php echo esc_url($logo_retina); ?>" alt="<?php esc_attr(bloginfo('name')); ?>"/></a>
		                    <?php } else { ?>
		                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><img src="<?php echo esc_url($logo); ?>" data-rjs="<?php echo esc_url($logo_retina); ?>" alt="<?php esc_attr(bloginfo('name')); ?>"/></a>
		                    <?php } ?>
		                    <!-- Logo for Sticky Header -->
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="sticky-logo"><img src="<?php echo esc_url($sticky_logo_retina); ?>" alt=""></a>
		                <?php } else {
		                    if(is_front_page()) { ?>
		                    <h1><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
		                    <?php } else { ?>
		                    <h2><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h2>
		                    <?php }
		                }
	                ?>
                </div>

                <!-- Mobile Navigation -->
				<div class="mmenu-trigger">
					<button class="hamburger hamburger--collapse" type="button">
						<span class="hamburger-box">
							<span class="hamburger-inner"></span>
						</span>
					</button>
				</div>
              
			</div>
			<!-- Left Side Content / End -->

			<!-- Right Side Content / End -->
			<div class="right-side">
				<?php dynamic_sidebar( 'header' ); ?>
				
			</div>
			<!-- Right Side Content / End -->

		</div>

		

		<!-- Main Navigation -->
		<nav id="navigation" class="style-2">
			<div class="container">
				<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_id' => 'responsive', 'container' => false ) );  ?>

			</div>
		</nav>

		<?php if( true == get_option('findeo_my_account_display', false ) ) : ?>
					
			<?php
			if ( is_user_logged_in() &&  function_exists('realteo_get_option')) { 
				$current_user = wp_get_current_user();
				if(!empty($current_user->user_firstname)){
					$name = $current_user->user_firstname;
				} else {
					$name =  $current_user->display_name;
				}?>
<div class="container">
			<div class="row">
				<div class="user-menu-container">
				<div class="user-menu">
					<div class="user-name"><span><?php echo get_avatar( $current_user->user_email, 32 );?></span>
						<?php esc_html_e('Hi,','findeo') ?> <?php echo $name; ?></div>
					<ul>
						<li>
						<a href="<?php echo get_permalink(realteo_get_option( 'my_account_page' )); ?>" >
							<i class="sl sl-icon-user"></i> <?php esc_html_e('My Profile','realteo', 'findeo');?>
						</a>
					</li>
					<li>
						<a href="<?php echo get_permalink(realteo_get_option( 'bookmarks_page' ))?>" >
							<i class="sl sl-icon-star"></i> <?php esc_html_e('Bookmarks','realteo', 'findeo');?>
						</a>
					</li>
					<li>
						<a href="<?php echo get_permalink( realteo_get_option( 'my_properties_page' ) ); ?>">
							<i class="sl sl-icon-docs"></i> 
							<?php esc_html_e('My Businesses','realteo', 'findeo');?>
						</a>
					</li>	
						<li><a href="<?php echo wp_logout_url(home_url()); ?>"><i class="sl sl-icon-power"></i> <?php esc_html_e('Log Out','realteo', 'findeo');?></a></li>
					</ul>
				</div>
				</div>
		</div>
	</div>
		<?php }
		 endif;?>


		<div class="clearfix"></div>

		<!-- Main Navigation / End -->

	</div>
	<!-- Header / End -->

</header>
<div class="clearfix"></div>
<!-- Header Container / End -->

<?php 
   ?>