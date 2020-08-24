<?php 
if ( !defined('ABSPATH')) exit;

/**
 * Custom Business Email Alert
 *
 * An email sent to the buyer user.
 * 
 * @class       Custom_Email
 * @extends     WC_Email
 *
 */
class Custom_Email extends WC_Email {

	/**
	 * Customer note.
	 *
	 * @var string
	 */
	public $customer_note;
	
	public $listings = array();
	public $custom_title = array();
    

	/**
	 * Constructor.
	 */
	public function __construct() {
		
		$this->id                   = 'custom_email';
		$this->customer_email = true;
		$this->title                = __( 'Business alert notification', 'custom-email' );
        $this->description          = __( 'This email will received by buyer user only', 'custom-email' );
		
		$this->template_html  = 'emails/business-alert-notification.php';
		$this->template_plain = 'emails/plain/business-alert-notification.php';
		$this->placeholders   = array(
			'{site_title}'   => $this->get_blogname()
		);

		// Triggers
		add_action( 'custom_business_alert_notification', array( $this, 'trigger' ), 10, 3 );

		// Call parent constructor
		parent::__construct();
	}

	/**
	 * Trigger.
	 *
	 * @param  $posts, $recipient, $title
	 */
	public function trigger( $posts, $recipient, $title='' ) {
		$this->setup_locale();
		
		$this->recipient = $recipient;
		$this->listings = $posts;
		if($title)
			$this->custom_title = $title;
		else
			$this->custom_title = $this->get_heading();
		
		if ( $this->is_enabled() && $recipient ) {
			$this->send( $recipient, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}

		$this->restore_locale();
	}

	/**
	 * Get content html.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_html() {
		return wc_get_template_html( $this->template_html, array(
			'posts'         => $this->listings,
			'email_heading' => $this->custom_title,
			'body_content'  => $this->get_option( 'body_content'),
			'sent_to_admin' => false,
			'plain_text'    => false,
			'email'			=> $this,
		) );
	}

	/**
	 * Get content plain.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_plain() {
		return wc_get_template_html( $this->template_plain, array(
			'posts'         => $this->listings,
			'email_heading' => $this->custom_title,
			'body_content'  => $this->get_option( 'body_content'),
			'sent_to_admin' => false,
			'plain_text'    => true,
			'email'			=> $this,
		) );
	}

	/**
	 * Initialise settings form fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'   => __( 'Enable/Disable', 'woocommerce' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this email notification', 'woocommerce' ),
				'default' => 'yes',
			),
			'subject' => array(
				'title'       => __( 'Email Subject', 'woocommerce' ),
				'type'        => 'text',
				'desc_tip'      => false,
				//'description'   => sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>{site_title}, {order_date}, {order_number}</code>' ),
				//'placeholder' => $this->get_default_subject(),
				'default'     => '',
			),
			'heading' => array(
				'title'       => __( 'Email heading', 'woocommerce' ),
				'type'        => 'text',
				'desc_tip'      => true,
				'description'   => 'Default heading for notification name',
				'placeholder' => '',
				'default'     => '',
			),
			'body_content' => array(
				'title'       => __( 'Additional Email content', 'woocommerce' ),
				'type'        => 'textarea',
				'desc_tip'      => false,
				'default'     => '',
			),
			'email_type' => array(
				'title'       => __( 'Email type', 'woocommerce' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'woocommerce' ),
				'default'     => 'html',
				'class'       => 'email_type wc-enhanced-select',
				'options'     => $this->get_email_type_options(),
				'desc_tip'    => true,
			),
		);
	}
}

return new Custom_Email();