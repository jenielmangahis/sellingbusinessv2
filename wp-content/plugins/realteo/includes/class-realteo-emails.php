<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;
/**
 * Realteo_Property class
 */
class Realteo_Emails {

	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since  1.0
	 */
	private static $_instance = null;

	/**
	 * Allows for accessing single instance of class. Class should only be constructed once per call.
	 *
	 * @since  1.0
	 * @static
	 * @return self Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {

		add_action( 'realteo_property_submitted', array($this, 'new_property_email'));
		add_action( 'realteo_expired_property', array($this, 'expired_property_email'));
		add_action( 'realteo_expiring_soon_property', array($this, 'expiring_soon_property_email'));

		add_action( 'pending_to_publish', array( $this, 'published_property_email' ) );
		add_action( 'pending_payment_to_publish', array( $this, 'published_property_email' ) );
		add_action( 'preview_to_publish', array( $this, 'published_property_email' ) );
		add_action( 'draft_to_publish', array( $this, 'published_property_email' ) );
		add_action( 'auto-draft_to_publish', array( $this, 'published_property_email' ) );
		add_action( 'expired_to_publish', array( $this, 'published_property_email' ) );

		add_action( 'realteo_agent_invited', array( $this, 'agent_invitation' ), 10, 2  );
	


	}

	function new_property_email($post_id ){
		$post = get_post($post_id);
		if ( $post->post_type !== 'property' ) {
			return;
		}

		if(!realteo_get_option('property_new_email')){
			return;
		}
		
		$author   	= 	get_userdata( $post->post_author ); 
		$email 		=  $author->data->user_email;

		$args = array(
			'user_name' 	=> $author->display_name,
			'user_mail' 	=> $email,
			'property_date' => $post->post_date,
			'property_name' => $post->post_title,
			'property_url'  => get_permalink( $post->ID ),
			);

		$subject 	 = realteo_get_option('property_new_email_subject');
		$subject 	 = $this->replace_shortcode( $args, $subject );

		$body 	 = realteo_get_option('property_new_email_content');
		$body 	 = $this->replace_shortcode( $args, $body );

		self::send( $email, $subject, $body );
	}

	function published_property_email($post ){
		if ( $post->post_type !== 'property' ) {
			return;
		}

		if(!realteo_get_option('property_published_email')){
			return;
		}
		
		$author   	= 	get_userdata( $post->post_author ); 
		$email 		=  $author->data->user_email;

		$args = array(
			'user_name' 	=> $author->display_name,
			'user_mail' 	=> $email,
			'property_date' => $post->post_date,
			'property_name' => $post->post_title,
			'property_url'  => get_permalink( $post->ID ),
			);

		$subject 	 = realteo_get_option('property_published_email_subject');
		$subject 	 = $this->replace_shortcode( $args, $subject );

		$body 	 = realteo_get_option('property_published_email_content');
		$body 	 = $this->replace_shortcode( $args, $body );

		self::send( $email, $subject, $body );
	}	

	function expired_property_email($post_id ){
		$post = get_post($post_id);
		if ( $post->post_type !== 'property' ) {
			return;
		}

		if(!realteo_get_option('property_expired_email')){
			return;
		}
		
		$author   	= 	get_userdata( $post->post_author ); 
		$email 		=  $author->data->user_email;

		$args = array(
			'user_name' 	=> $author->display_name,
			'user_mail' 	=> $email,
			'property_date' => $post->post_date,
			'property_name' => $post->post_title,
			'property_url'  => get_permalink( $post->ID ),
			);

		$subject 	 = realteo_get_option('property_expired_email_subject');
		$subject 	 = $this->replace_shortcode( $args, $subject );

		$body 	 = realteo_get_option('property_expired_email_content');
		$body 	 = $this->replace_shortcode( $args, $body );

		self::send( $email, $subject, $body );
	}

	function expiring_soon_property_email($post_id ){
		$post = get_post($post_id);
		if ( $post->post_type !== 'property' ) {
			return;
		}
		$already_sent = get_post_meta( $post_id, 'notification_email_sent', true );
		if($already_sent) {
			return;
		}

		if(!realteo_get_option('property_expiring_soon_email')){
			return;
		}
		
		$author   	= 	get_userdata( $post->post_author ); 
		$email 		=  $author->data->user_email;

		$args = array(
			'user_name' 	=> $author->display_name,
			'user_mail' 	=> $email,
			'property_date' => $post->post_date,
			'property_name' => $post->post_title,
			'property_url'  => get_permalink( $post->ID ),
			);

		$subject 	 = realteo_get_option('property_expiring_soon_email_subject');
		$subject 	 = $this->replace_shortcode( $args, $subject );

		$body 	 = realteo_get_option('property_expiring_soon_email_content');
		$body 	 = $this->replace_shortcode( $args, $body );
		add_post_meta($post_id, 'notification_email_sent', true );
		self::send( $email, $subject, $body );

	}

	function agent_invitation($agent_id, $agency_id ){
		
		
		if(!realteo_get_option('agent_invitation_email')){
			return;
		}

		$post = get_post($agency_id);

		
		$author   	= 	get_userdata( $agent_id ); 
		$email 		=  $author->data->user_email;

		$args = array(
			'user_name' 	=> $author->display_name,
			'user_mail' 	=> $email,
			'property_name' => $post->post_title,
			'property_url'  => get_permalink( $post->ID ),
			);

		$subject 	 = realteo_get_option('agent_invitation_email_subject');
		$subject 	 = $this->replace_shortcode( $args, $subject );

		$body 	 = realteo_get_option('agent_invitation_email_content');
		$body 	 = $this->replace_shortcode( $args, $body );

		self::send( $email, $subject, $body );
	}	


	/**
	 * general function to send email to agent with specify subject, body content
	 */
	public static function send( $emailto, $subject, $body ){

		$from_name 	= realteo_get_option('emails_name',get_bloginfo( 'name' ));
		$from_email = realteo_get_option('emails_from_email', get_bloginfo( 'admin_email' ));
		$headers 	= sprintf( "From: %s <%s>\r\n Content-type: text/html", $from_name, $from_email );

		if( empty($emailto) || empty( $subject) || empty($body) ){
			return ;
		}
		$template_loader = new Realteo_Template_Loader;
		ob_start();

			$template_loader->get_template_part( 'emails/header' ); ?>
			<tr>
				<td align="center" valign="top" style="border-collapse: collapse; border-spacing: 0; margin: 0; padding: 0; padding-left: 6.25%; padding-right: 6.25%; width: 87.5%; font-size: 16px; font-weight: 400; line-height: 160%;
				padding-top: 20px; 
				color: #333;
				font-family: sans-serif;" class="paragraph">
				<?php echo $body;?>
				</td>
			</tr>
		<?php
			$template_loader->get_template_part( 'emails/footer' ); 
			$content = ob_get_clean();
		wp_mail( @$emailto, @$subject, @$content, $headers );

	}

	public  function replace_shortcode( $args, $body ) {

		$tags =  array(
			'user_mail' 	=> "",
			'user_name' 	=> "",
			'property_date' => "",
			'property_name' => "",
			'property_url' => '',
			'agency_name' => "",
			'agency_url' => '',
			'site_name' => '',
			'site_url'	=> '',
			
		);
		$tags = array_merge( $tags, $args );

		extract( $tags );

		$tags 	 = array( '{user_mail}',
						  '{user_name}',
						  '{property_date}',
						  '{property_name}',
						  '{property_url}',
						  '{agency_name}',
						  '{agency_url}',
						  '{site_name}',
						  '{site_url}',
						);

		$values  = array(   $user_mail, 
							$user_name ,
							$property_date,
							$property_name,
							$property_url,
							$agency_name,
							$agency_url,
							get_bloginfo( 'name' ) ,
							get_home_url(), 
		);

		$message = str_replace($tags, $values, $body);

		return $message;
	}
}
?>