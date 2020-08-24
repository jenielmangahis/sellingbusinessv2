<?php
/**
 * findeo Theme Customizer
 *
 * @package findeo
 */

/**
 * Add the theme configuration
 */
findeo_Kirki::add_config( 'findeo', array(
	'option_type' => 'option',
	'capability'  => 'edit_theme_options',
) );
	
	
require get_template_directory() . '/inc/customizer/var.php';
require get_template_directory() . '/inc/customizer/home.php';
require get_template_directory() . '/inc/customizer/blog.php';
require get_template_directory() . '/inc/customizer/header.php';
require get_template_directory() . '/inc/customizer/typography.php';
require get_template_directory() . '/inc/customizer/footer.php';
require get_template_directory() . '/inc/customizer/general.php';
require get_template_directory() . '/inc/customizer/properties.php';
require get_template_directory() . '/inc/customizer/shop.php';

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function findeo_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
}
add_action( 'customize_register', 'findeo_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function findeo_customize_preview_js() {
	wp_enqueue_script( 'findeo_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20151215', true );
}
add_action( 'customize_preview_init', 'findeo_customize_preview_js' );


/**
 * Add color styling from theme
 */
function findeo_custom_styles() {
    $maincolor = get_option('pp_main_color','#274abb' ); 
    
    $video_color = get_option('findeo_video_search_color','rgba(22,22,22,0.4)');
    $custom_css = '#backtotop a,#top-bar,.csm-trigger, .csm-content h4,.fp-accordion .accordion h3.ui-accordion-header-active { background-color: '.esc_attr($maincolor).'}
.custom-zoom-in:hover:before,.custom-zoom-out:hover:before,.infoBox-close:hover {-webkit-text-stroke: 1px '.esc_attr($maincolor).'}
.user-menu.active .user-name:after, .user-menu:hover .user-name:after, .user-menu.active .user-name, .user-menu:hover .user-name, .user-menu ul li a:hover, .list-4 li:before,.list-3 li:before,.list-2 li:before,.list-1 li:before, .nav-links div a:hover, #posts-nav li a:hover,li.checkboxed:before { color: '.esc_attr($maincolor).';}
.numbered.color ol > li::before { border: 1px solid '.esc_attr($maincolor).'; color: '.esc_attr($maincolor).';}
.numbered.color.filled ol > li::before { border: 1px solid '.esc_attr($maincolor).'; background-color: '.esc_attr($maincolor).';}
.pagination ul li span.current, .pagination .current, .pagination ul li a:hover, .pagination-next-prev ul li a:hover, .change-photo-btn:hover,table.manage-table th,table.shop_table th,mark.color,.comment-by a.comment-reply-link:hover,input[type="checkbox"].switch_1:checked { background-color: '.esc_attr($maincolor).';}
table.manage-table td.action a:hover,table.manage-table .title-container .title h4 a:hover,.my-account-nav li a.current,.my-account-nav li a:hover,.woocommerce-MyAccount-navigation ul li.is-active a,.woocommerce-MyAccount-navigation ul a:hover,#footer .social-icons li a:hover i,#navigation.style-1 > ul > .current-menu-item > a, #navigation.style-1 > ul > .current-menu-ancestor > a,#navigation.style-2 > ul > .current-menu-ancestor > a.nav-links div a:hover, #navigation.style-2 > ul > .current-menu-item > a.nav-links div a:hover, #posts-nav li a:hover,#top-bar .social-icons li a:hover i,.agent .social-icons li a:hover i,.agent-contact-details li a:hover,.agent-page .agent-name h4,.footer-links li a:hover,.header-style-2 .header-widget li i,.header-widget .sign-in:hover,.home-slider-desc .read-more i,.info-box,.info-box h4,.listing-title h4 a:hover,.map-box h4 a:hover,.plan-price .value,.plan.featured .listing-badges .featured,.post-content a.read-more,.post-content h3 a:hover,.post-meta li a:hover,.property-pricing,.style-2 .trigger a:hover,.style-2 .trigger.active a,.style-2 .ui-accordion .ui-accordion-header-active,.style-2 .ui-accordion .ui-accordion-header-active:hover,.style-2 .ui-accordion .ui-accordion-header:hover,vc_tta.vc_tta-style-tabs-style-1 .vc_tta-tab.vc_active a,.vc_tta.vc_tta-style-tabs-style-2 .vc_tta-tab.vc_active a,.tabs-nav li.active a,.wc-tabs li.active a.custom-caption,#backtotop a,.trigger.active a,.post-categories li a,.vc_tta.vc_tta-style-tabs-style-3.vc_general .vc_tta-tab a:hover,.vc_tta.vc_tta-style-tabs-style-3.vc_general .vc_tta-tab.vc_active a,.wc-tabs li a:hover,.tabs-nav li a:hover,.tabs-nav li.active a,.wc-tabs li a:hover,.wc-tabs li.active a,.testimonial-author h4,.widget-button:hover,.widget-text h5 a:hover,a,a.button.border,a.button.border.white:hover,.wpb-js-composer .vc_tta.vc_general.vc_tta-style-tabs-style-1 .vc_tta-tab.vc_active>a,.wpb-js-composer .vc_tta.vc_general.vc_tta-style-tabs-style-2 .vc_tta-tab.vc_active>a  { color: '.esc_attr($maincolor).'} #header-container.top-border { border-top: 4px solid '.esc_attr($maincolor).' } #navigation.style-1 > ul > .current-menu-ancestor > a,#navigation.style-1 > ul > .current-menu-item > a { background-color: transparent; border: 1px solid '.esc_attr($maincolor).' } #navigation.style-1 ul li:hover, #navigation.style-1 > ul > .current-menu-ancestor > a:hover, #navigation.style-1 > ul > .current-menu-ancestor > a:hover,#navigation.style-1 > ul > .current-menu-item > a:hover, #navigation.style-1 > ul > .current-menu-item > a:hover { background-color: '.esc_attr($maincolor).'} #navigation.style-2 { background-color: '.esc_attr($maincolor).' } .menu-responsive i { background: linear-gradient(to bottom, rgba(255, 255, 255, .07) 0, transparent); background-color: '.esc_attr($maincolor).'} .realteo-term-checklist input[type=checkbox]:checked + label:before, .checkboxes input[type=checkbox]:checked + label:before, .checkboxes input[type=checkbox]:checked+label:before, .range-slider .ui-widget-header, .search-type label.active, .search-type label:hover { background-color: '.esc_attr($maincolor).' } .range-slider .ui-slider .ui-slider-handle { border: 2px solid '.esc_attr($maincolor).' } .agent-avatar a:before { background: '.esc_attr($maincolor).'; background: linear-gradient(to bottom, transparent 50%, '.esc_attr($maincolor).')} .view-profile-btn { background-color: '.esc_attr($maincolor).' } .listing-img-container:after { background: linear-gradient(to bottom, transparent 60%, '.esc_attr($maincolor).') } .listing-badges .featured {
    background-color: '.esc_attr($maincolor).' } .list-layout .listing-img-container:after { background: linear-gradient(to bottom, transparent 55%, '.esc_attr($maincolor).') } #property_preview .property-titlebar span.property-badge, #titlebar.property-titlebar span.property-badge, .back-to-listings:hover, .home-slider-price, .img-box:hover:before, .layout-switcher a.active, .layout-switcher a:hover, .listing-hidden-content, .office-address h3:after, .pagination .current, .pagination ul li a.current-page, .pagination ul li a:hover, .pagination-next-prev ul li a:hover, .property-features.checkboxes li:before { background-color: '.esc_attr($maincolor).'} .post-img:after, .tip { background: '.esc_attr($maincolor).' } .property-slider-nav .item.slick-current.slick-active:before{ border-color: '.esc_attr($maincolor).' } .post-img:after {
    background: linear-gradient(to bottom, transparent 40%, '.esc_attr($maincolor).')
}


.floorplans-submit-item td .fm-move,
.add-pricing-submenu.button:hover, .add-floorplans-submit-item.button:hover,
.comment-by a.reply:hover,
.post-img:before {
    background-color: '.esc_attr($maincolor).'
}

.map-box .listing-img-container:after {
    background: linear-gradient(to bottom, transparent 50%, '.esc_attr($maincolor).')
}

#geoLocation:hover,
#mapnav-buttons a:hover,
#scrollEnabling.enabled,
#scrollEnabling:hover,
#streetView:hover,
.cluster div,
.custom-zoom-in:hover,
.custom-zoom-out:hover,
.infoBox-close:hover,
.listing-carousel.owl-theme .owl-controls .owl-next:after,
.listing-carousel.owl-theme .owl-controls .owl-prev:before,
.listing-carousel.owl-theme.outer .owl-controls .owl-next:hover::after,
.listing-carousel.owl-theme.outer .owl-controls .owl-prev:hover::before,
.slick-next:after,
.slick-prev:after {
    background-color: '.esc_attr($maincolor).'
}

.cluster div:before {
    border: 7px solid '.esc_attr($maincolor).';
    box-shadow: inset 0 0 0 4px '.esc_attr($maincolor).'
}

.mfp-arrow:hover {
    background: '.esc_attr($maincolor).'
}

.dropzone:hover {
    border: 2px dashed '.esc_attr($maincolor).'
}

.dropzone:before {
    background: linear-gradient(to bottom, rgba(255, 255, 255, .95), rgba(255, 255, 255, .9));
    background-color: '.esc_attr($maincolor).'
}

.chosen-container .chosen-results li.highlighted,
.chosen-container-multi .chosen-choices li.search-choice,
.select-options li:hover,
a.button,
a.button.border:hover,
button.button,
input[type=button],
input[type=submit] {
    background-color: '.esc_attr($maincolor).'
}

.dropzone:hover .dz-message,
.sort-by .chosen-container-single .chosen-default,
.sort-by .chosen-container-single .chosen-single div b:after {
    color: '.esc_attr($maincolor).'
}

a.button.border {
    border: 1px solid '.esc_attr($maincolor).'
}

.plan.featured .plan-price {
    background: linear-gradient(to bottom, rgba(255, 255, 255, .1) 0, transparent);
    background-color: '.esc_attr($maincolor).'
}

.trigger.active a,
.ui-accordion .ui-accordion-header-active,
.ui-accordion .ui-accordion-header-active:hover {
    background-color: '.esc_attr($maincolor).';
    border-color: '.esc_attr($maincolor).'
}
.vc_tta.vc_general.vc_tta-style-style-1 .vc_active .vc_tta-panel-heading,
.wpb-js-composer .vc_tta.vc_general.vc_tta-style-tabs-style-2 .vc_tta-tab.vc_active>a,
.wpb-js-composer .vc_tta.vc_general.vc_tta-style-tabs-style-2 .vc_tta-tab:hover>a,
.wpb-js-composer .vc_tta.vc_general.vc_tta-style-tabs-style-1 .vc_tta-tab.vc_active>a,
.wpb-js-composer .vc_tta.vc_general.vc_tta-style-tabs-style-1 .vc_tta-tab:hover>a,
.tabs-nav li a:hover,
.tabs-nav li.active a {
    border-bottom-color: '.esc_attr($maincolor).'
}

.style-3 .tabs-nav li a:hover,
.style-3 .tabs-nav li.active a {
    border-color: '.esc_attr($maincolor).';
    background-color: '.esc_attr($maincolor).'
}

.style-4 .tabs-nav li.active a,
.style-5 .tabs-nav li.active a,
table.basic-table th {
    background-color: '.esc_attr($maincolor).'
}

.info-box {
    border-top: 2px solid '.esc_attr($maincolor).';
    background: linear-gradient(to bottom, rgba(255, 255, 255, .98), rgba(255, 255, 255, .95));
    background-color: '.esc_attr($maincolor).'
}

.info-box.no-border {
    background: linear-gradient(to bottom, rgba(255, 255, 255, .96), rgba(255, 255, 255, .93));
    background-color: '.esc_attr($maincolor).'
}

.icon-box-1 .icon-container {
    background-color: '.esc_attr($maincolor).'
}

.dark-overlay .video-container:before {
    background: '.esc_attr($video_color).'
}';

$ordering = get_option( 'pp_shop_ordering' ); 
if($ordering == 'hide') {
     $custom_css .= '.woocommerce-ordering { display: none; }
    .woocommerce-result-count { display: none; }';
 }

wp_add_inline_style( 'findeo-style', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'findeo_custom_styles' );

