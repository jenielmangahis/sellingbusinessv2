<?php 

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Job Manager Widget base
 */
class Realteo_Widget extends WP_Widget {
/**
	 * Widget CSS class
	 *
	 * @access public
	 * @var string
	 */
	public $widget_cssclass;

	/**
	 * Widget description
	 *
	 * @access public
	 * @var string
	 */
	public $widget_description;

	/**
	 * Widget id
	 *
	 * @access public
	 * @var string
	 */
	public $widget_id;

	/**
	 * Widget name
	 *
	 * @access public
	 * @var string
	 */
	public $widget_name;

	/**
	 * Widget settings
	 *
	 * @access public
	 * @var array
	 */
	public $settings;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->register();

	}


	/**
	 * Register Widget
	 */
	public function register() {
		$widget_ops = array(
			'classname'   => $this->widget_cssclass,
			'description' => $this->widget_description
		);

		parent::__construct( $this->widget_id, $this->widget_name, $widget_ops );

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );

		
	}

	

	/**
	 * get_cached_widget function.
	 */
	public function get_cached_widget( $args ) {
		$cache = wp_cache_get( $this->widget_id, 'widget' );

		if ( ! is_array( $cache ) )
			$cache = array();

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return true;
		}

		return false;
	}

	/**
	 * Cache the widget
	 */
	public function cache_widget( $args, $content ) {
		$cache[ $args['widget_id'] ] = $content;

		wp_cache_set( $this->widget_id, $cache, 'widget' );
	}

	/**
	 * Flush the cache
	 * @return [type]
	 */
	public function flush_widget_cache() {
		wp_cache_delete( $this->widget_id, 'widget' );
	}

	/**
	 * update function.
	 *
	 * @see WP_Widget->update
	 * @access public
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		if ( ! $this->settings )
			return $instance;

		foreach ( $this->settings as $key => $setting ) {
			$instance[ $key ] = sanitize_text_field( $new_instance[ $key ] );
		}

		$this->flush_widget_cache();

		return $instance;
	}

	/**
	 * form function.
	 *
	 * @see WP_Widget->form
	 * @access public
	 * @param array $instance
	 * @return void
	 */
	function form( $instance ) {

		if ( ! $this->settings )
			return;

		foreach ( $this->settings as $key => $setting ) {

			$value = isset( $instance[ $key ] ) ? $instance[ $key ] : $setting['std'];

			switch ( $setting['type'] ) {
				case 'text' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
				break;			
				case 'checkbox' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="checkbox" <?php checked( esc_attr( $value ), 'on' ); ?> />
					</p>
					<?php
				break;
				case 'number' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="number" step="<?php echo esc_attr( $setting['step'] ); ?>" min="<?php echo esc_attr( $setting['min'] ); ?>" max="<?php echo esc_attr( $setting['max'] ); ?>" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
				break;
				case 'dropdown' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>	
						<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>">
	
						<?php foreach ($setting['options'] as $key => $option_value) { ?>
							<option <?php selected($value,$key); ?> value="<?php echo esc_attr($key); ?>"><?php echo esc_attr($option_value); ?></option>	
						<?php } ?></select>
					
					</p>
					<?php
				break;
			}
		}
	}

	/**
	 * widget function.
	 *
	 * @see    WP_Widget
	 * @access public
	 *
	 * @param array $args
	 * @param array $instance
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {}
}


/**
 * Featured properties Widget
 */
class Realteo_Featured_Properties extends Realteo_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		global $wp_post_types;

		$this->widget_cssclass    = 'realteo widget_featured_properties';
		$this->widget_description = __( 'Display a list of featured properties on your site.', 'realteo' );
		$this->widget_id          = 'widget_featured_properties';
		$this->widget_name        =  __( 'Featured Properties', 'realteo' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Featured Properties', 'realteo' ),
				'label' => __( 'Title', 'realteo' )
			),
			'number' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 10,
				'label' => __( 'Number of listings to show', 'realteo' )
			)
		);
		$this->register();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {
		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		ob_start();

		extract( $args );

		$title  = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$number = absint( $instance['number'] );
		$properties   = new WP_Query( array(
			'posts_per_page' => $number,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'post_type' 	 => 'property',
			'meta_query'     =>  array( 
				array(
					'key'     => '_featured',
					'value'   => 'on',
					'compare' => '=',
				),
				array('key' => '_thumbnail_id')
			)
		) );
	
		$template_loader = new Realteo_Template_Loader;
		if ( $properties->have_posts() ) : ?>

			<?php echo $before_widget; ?>

			<?php if ( $title ) echo $before_title . $title . $after_title; ?>

			<div class="listing-carousel outer">
				<?php while ( $properties->have_posts() ) : $properties->the_post(); ?>
					<?php $template_loader->get_template_part( 'property-widget','content' ); ?>
				<?php endwhile; ?>
			</div>

			<?php echo $after_widget; ?>

		<?php else : ?>

			<?php $template_loader->get_template_part( 'property-widget','no-content' ); ?>

		<?php endif;

		wp_reset_postdata();

		$content = ob_get_clean();

		echo $content;

		$this->cache_widget( $args, $content );
	}
}


/**
 * Save & Print properties Widget
 */
class Realteo_Save_Print_Widget extends Realteo_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		global $wp_post_types;

		$this->widget_cssclass    = 'realteo widget_buttons';
		$this->widget_description = __( 'Display a Print and Save buttons for real property.', 'realteo' );
		$this->widget_id          = 'widget_buttons_properties';
		$this->widget_name        =  __( 'Findeo Buttons', 'realteo' );
		$this->settings           = array(
			'save' => array(
				'type'  => 'checkbox',
				'std'	=> 'on',
				'label' => __( 'Save button', 'realteo' )
			),			
			'print' => array(
				'type'  => 'checkbox',
				'std'	=> 'on',
				'label' => __( 'Print button', 'realteo' )
			),
		
		);
		$this->register();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {
		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		ob_start();

		extract( $args );

		global $post;
		$print = $instance['print'];
		$save = $instance['save'];
		echo $before_widget; 
		if(!empty($print)):	
			?>
			<button class="widget-button print-simple with-tip" data-tip-content="<?php _e('Print','realteo'); ?>"><i class="sl sl-icon-printer"></i></button>
			<?php 
		endif; ?>
		<?php 
		if(!empty($save)):
			if(!empty(realteo_get_option( 'bookmarks_page' ))) {
			$nonce = wp_create_nonce("realteo_bookmark_this_nonce");
	
			$classObj = new Realteo_Bookmarks;
			if( $classObj->check_if_added($post->ID) ) { ?>
				<button onclick="window.location.href='<?php echo get_permalink(realteo_get_option( 'bookmarks_page' ))?>'"  class="widget-button with-tip save liked" 
					data-tip-content="<?php esc_html_e('Bookmarked','realteo'); ?>"
					><i class="fa fa-star-o"></i></a>
			<?php } else { 
				if(is_user_logged_in()){ ?>
				<button class="widget-button save realteo-bookmark-it with-tip" 
					data-tip-content="<?php esc_html_e('Bookmark This Property','realteo'); ?>"
					data-tip-content-bookmarking="<?php esc_html_e('Adding To Bookmarks','realteo'); ?> <?php echo esc_attr('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i>'); ?>"
					data-tip-content-bookmarked="<?php esc_html_e('Bookmarked!','realteo'); ?>"   
					data-post_id="<?php echo esc_attr($post->ID); ?>" 
					data-nonce="<?php echo esc_attr($nonce); ?>" 
					><i class="fa fa-star"></i></button>
			<?php } else { ?>
			
			<button href="#" class="widget-button with-tip" data-tip-content="<?php esc_html_e('Login To Bookmark Items','realteo'); ?>"><i class="fa fa-star"></i></button>
			
					
			<?php }
			} 
		}?>
		<?php 
		endif; 
		
		if(!empty(realteo_get_option( 'compare_page' ))) {
			$nonce = wp_create_nonce("realteo_compare_this_nonce");
			$compareObj = Realteo_Compare::instance();
			$compare_post_ids = $compareObj->get_compare_posts(); 
			?>
			<button 
				class="widget-button with-tip compare-widget-button <?php if(in_array($post->ID,$compare_post_ids)) { echo "already-added"; } ?>"  
				data-post_id="<?php echo esc_attr($post->ID); ?>" 
				data-nonce="<?php echo esc_attr($nonce); ?>" 
				data-tip-content="<?php esc_html_e('Add To Compare','realteo'); ?>" 
				data-tip-adding-content="<?php esc_html_e('Adding To Compare','realteo'); ?> <?php echo esc_attr('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i>'); ?>" 
				data-tip-added-content="<?php esc_html_e('Added To Compare!','realteo'); ?>"><i class="icon-compare"></i></button>
			<?php
		}

		echo '<div class="clearfix"></div>'.$after_widget; 

		$content = ob_get_clean();

		echo $content;

		$this->cache_widget( $args, $content );
	}
}


/**
 * Featured properties Widget
 */
class Realteo_Contact_Agent_Widget extends Realteo_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		global $wp_post_types;

		$this->widget_cssclass    = 'realteo widget_contact_agent';
		$this->widget_description = __( 'Display a Contact form.', 'realteo' );
		$this->widget_id          = 'widget_contact_widget_findeo';
		$this->widget_name        =  __( 'Findeo Contact Widget', 'realteo' );
		$this->settings           = array(
			'phone' => array(
				'type'  => 'checkbox',
				'std'	=> 'on',
				'label' => __( 'Show phone number', 'realteo' )
			),	
			'email' => array(
				'type'  => 'checkbox',
				'std'	=> 'on',
				'label' => __( 'Show email', 'realteo' )
			),		
			'desc' => array(
				'type'  => 'checkbox',
				'std'	=> 'on',
				'label' => __( 'Show agent description', 'realteo' )
			),			
			'contact' => array(
				'type'  => 'dropdown',
				'std'	=> '',
				'options' => $this->get_forms(),
				'label' => __( 'Choose contact form', 'realteo' )
			),			
		);
		$this->register();

		//add_filter( 'wpcf7_mail_components', array( $this, 'set_question_form_recipient' ), 10, 3 );

	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {
		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		ob_start();

		extract( $args );
		/*
		$print = $instance['print'];
		$save = $instance['save'];*/
		echo $before_widget; 
		$agentID = get_the_author_meta( 'ID' );
	
		if($agentID) :
		?>
		<!-- Agent Widget -->
		<div class="agent-widget">
			<div class="agent-title">
				<div class="agent-photo"><?php echo get_avatar( $agentID, 72 );  ?></div>
				<div class="agent-details">
					<?php  $agent_data = get_userdata( $agentID ); ?>
					
					<h4><a href="<?php echo esc_url(get_author_posts_url( $agentID )); ?>"><?php echo $agent_data->first_name; ?> <?php echo $agent_data->last_name; ?></a></h4>
					<?php 
					if(isset($instance['phone']) && !empty($instance['phone'])) { 
						if(isset($agent_data->phone) && !empty($agent_data->phone)): ?><span><i class="sl sl-icon-call-in"></i><a href="tel:<?php echo esc_html($agent_data->phone); ?>"><?php echo esc_html($agent_data->phone); ?></a></span><?php endif; 
					}
					if(isset($instance['email']) && !empty($instance['email'])) { 	
						if(isset($agent_data->user_email)): $email = $agent_data->user_email; ?>
							<br><span><i class="fa fa-envelope-o "></i><a href="mailto:<?php echo esc_attr($email);?>"><?php echo esc_html($email);?></a></span>
						<?php endif; ?>
					<?php } ?>
				</div>

				<div class="clearfix"></div>
				
				
			</div>
			<?php if(isset($instance['desc']) && !empty($instance['desc'])) {  ?>
				<div class="agent-desc">
				<?php 
				 	$allowed_tags = wp_kses_allowed_html( 'post' );
	      			echo wp_kses($agent_data->description,$allowed_tags);
				?>
				</div>
				<?php } ?>
			<?php 
			if(isset($instance['contact']) && !empty($instance['contact'])) {
				echo do_shortcode( sprintf( '[contact-form-7 id="%s"]', $instance['contact'] ) );	
			} ?>
		</div>

		<!-- Agent Widget / End -->
		<?php
		endif;
		 echo $after_widget; 

		$content = ob_get_clean();

		echo $content;

		$this->cache_widget( $args, $content );
	}

	public function get_forms() {
		$forms  = array( 0 => __( 'Please select a form', 'realteo' ) );

		$_forms = get_posts(
			array(
				'numberposts' => -1,
				'post_type'   => 'wpcf7_contact_form',
			)
		);

		if ( ! empty( $_forms ) ) {

			foreach ( $_forms as $_form ) {
				$forms[ $_form->ID ] = $_form->post_title;
			}
		}

		return $forms;
	}

	

}




/**
 * Save & Print properties Widget
 */
class Realteo_Search_Widget extends Realteo_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		global $wp_post_types;

		$this->widget_cssclass    = 'realteo widget_buttons';
		$this->widget_description = __( 'Display a Advanced Search Form.', 'realteo' );
		$this->widget_id          = 'widget_search_form_properties';
		$this->widget_name        =  __( 'Findeo Search Form', 'realteo' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Find New Home', 'realteo' ),
				'label' => __( 'Title', 'realteo' )
			),
			'action' => array(
				'type'  => 'dropdown',
				'std'	=> 'archive',
				'options' => array(
					'current_page' => __( 'Redirect to current page', 'realteo' ),
					'archive' => __( 'Redirect to properties archive page', 'realteo' ),
					),
				'label' => __( 'Choose form action', 'realteo' )
			),	
		
		);
		$this->register();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {
		if ( $this->get_cached_widget( $args ) ) {
			return;
		}


		extract( $args );

		echo $before_widget; 
			$title  = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
			if(isset($instance['action'])){
				$action  = apply_filters( 'realteo_search_widget_action', $instance['action'], $instance, $this->id_base);	
			}
			
			
			if ( $title ) {
				echo $before_title . $title;
				if(isset($_GET['keyword_search'])) : echo '<a id="realteo_reset_filters" href="#">'.esc_html__('Reset Filters','realteo').'</a>'; endif;
			 	echo $after_title; 
			}
			if(isset($action) && $action == 'archive') {
				echo do_shortcode('[realteo_search_form action='.get_post_type_archive_link( 'property' ).']');
			} else {
				echo do_shortcode('[realteo_search_form]');
			}

		echo $after_widget; 

		

	}
}


/**
 * Save & Print properties Widget
 */
class Realteo_Mortgage_Calculator_Widget extends Realteo_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->widget_cssclass    = 'realteo widget_calculator';
		$this->widget_description = __( 'Shows Mortgage Calculator.', 'realteo' );
		$this->widget_id          = 'widget_mortgage_calc_properties';
		$this->widget_name        =  __( 'Realteo Mortgage Calc', 'realteo' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Mortgage Calculator', 'realteo' ),
				'label' => __( 'Title', 'realteo' )
			),
			'only_sale' => array(
				'type'  => 'checkbox',
				'std'	=> 'on',
				'label' => __( 'Show only on Properties with offer type "For Sale"', 'realteo' )
			),			
			
		
		);
		$this->register();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {
		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		ob_start();

		extract( $args );
		$title  = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$queried_object = get_queried_object();

		if ( $queried_object ) {
		    $post_id = $queried_object->ID;
		    $price = get_post_meta( $post_id, '_price', true );
		    $offer_type = get_post_meta( $post_id, '_offer_type', true );
		} else {
			$offer_type = false;
			$price = false;
		}
		if($instance['only_sale']){
			if($offer_type != 'sale') {
				return;
			}
		}
		echo $before_widget;
		if ( $title ) {
				echo $before_title . $title . $after_title; 
			} 
		$currency_abbr = realteo_get_option( 'currency' );

		?>
<!-- Mortgage Calculator -->
			<form action="javascript:void(0);" autocomplete="off" class="mortgageCalc" data-calc-currency="<?php echo Realteo_Property::get_currency_symbol($currency_abbr); ?>">
				<div class="calc-input">
					<!-- <div class="pick-price tip" data-tip-content="Set This Property Price"></div> -->
				    <input type="text" id="amount" name="amount" placeholder="<?php esc_html_e('Sale Price','realteo'); ?>" required <?php if($price) { ?> value="<?php echo esc_attr($price) ?>"<?php } ?>>
				    <label for="amount" class="fa fa-money"></label>
				</div>

				<div class="calc-input">
				    <input type="text" id="downpayment" placeholder="<?php esc_html_e('Down Payment','realteo'); ?>">
				    <label for="downpayment" class="fa fa-money"></label>
				</div>

				<div class="calc-input">
					<input type="text" id="years" placeholder="<?php esc_html_e('Loan Term (Years)','realteo'); ?>" required>
					<label for="years" class="fa fa-calendar-o"></label>
				</div>

				<div class="calc-input">
					<input type="text" id="interest" placeholder="<?php esc_html_e('Interest Rate','realteo'); ?>" required>
					<label for="interest" class="fa fa-percent"></label>
				</div>

				<button class="button calc-button" formvalidate><?php esc_html_e('Calculate','realteo'); ?></button>
				<div class="calc-output-container"><div class="notification success"><?php esc_html_e('Monthly Payment:','realteo'); ?> <strong class="calc-output"></strong></div></div>
			</form>
			<!-- Mortgage Calculator / End -->
		<?php
		

		echo $after_widget; 

		$content = ob_get_clean();

		echo $content;

		$this->cache_widget( $args, $content );
	}
}


register_widget( 'Realteo_Featured_Properties' );
register_widget( 'Realteo_Save_Print_Widget' );
register_widget( 'Realteo_Contact_Agent_Widget' );
register_widget( 'Realteo_Search_Widget' );
register_widget( 'Realteo_Mortgage_Calculator_Widget' );

function custom_get_post_author_email($atts){
	$value = '';
	global $post;
	$post_id = $post->ID;
	$object = get_post( $post_id );
	//just get the email of the listing author
	$owner_ID = $object->post_author;
	//retrieve the owner user data to get the email
	$owner_info = get_userdata( $owner_ID );
	if ( false !== $owner_info ) {
		$value = $owner_info->user_email;
	}

  return $value;
}
add_shortcode('CUSTOM_POST_AUTHOR_EMAIL', 'custom_get_post_author_email');
add_shortcode('PROPERTY_AGENT_EMAIL', 'custom_get_post_author_email');

function custom_get_agency_author_email($atts){
	$value = '';
	global $post;
	$post_id = $post->ID;
	$object = get_post( $post_id );
	$email = get_post_meta($post_id,'realteo_email',true);
	if(!$email){
		//just get the email of the listing author
		$owner_ID = $object->post_author;
		//retrieve the owner user data to get the email
		$owner_info = get_userdata( $owner_ID );
		if ( false !== $owner_info ) {
			$value = $owner_info->user_email;
		}
	} else {
		$value = $email;
	}
	

  return $value;
}
add_shortcode('AGENCY_EMAIL', 'custom_get_agency_author_email');

function custom_get_post_property_title($atts){
	$value = '';
	global $post;
	$post_id = $post->ID;
	if($post_id){
		$value = get_the_title($post_id);
	}
  return $value;
}
add_shortcode('CUSTOM_POST_PROPERTY_TITLE', 'custom_get_post_property_title');
add_shortcode('PROPERTY_TITLE', 'custom_get_post_property_title');


function custom_get_post_property_url($atts){
	$value = '';
	global $post;
	$post_id = $post->ID;
	if($post_id){
		$value = get_the_permalink();($post_id);
	}
  return $value;
}

add_shortcode('PROPERTY_URL', 'custom_get_post_property_url');