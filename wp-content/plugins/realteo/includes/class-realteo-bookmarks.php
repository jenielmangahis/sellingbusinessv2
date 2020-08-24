<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;
/**
 * Realteo_Property class
 */
class Realteo_Bookmarks {
	public function __construct() {

		add_action('wp_ajax_realteo_bookmark_this', array($this, 'bookmark_this'));
		add_action('wp_ajax_nopriv_realteo_bookmark_this', array($this, 'bookmark_this'));

		add_action('wp_ajax_realteo_unbookmark_this', array($this, 'remove_bookmark'));
		add_action('wp_ajax_nopriv_realteo_unbookmark_this', array($this, 'remove_bookmark'));

		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
		add_shortcode( 'realteo_bookmarks', array( $this, 'realto_bookmarks' ) );
	}

	/**
	 * frontend_scripts function.
	 *
	 * @access public
	 * @return void
	 */
	public function frontend_scripts() {
	
	}


	public function bookmark_this() {

	    if ( !wp_verify_nonce( $_REQUEST['nonce'], 'realteo_bookmark_this_nonce')) {
	    	exit('No naughty business please');
	    }   
	    $post_id = $_REQUEST['post_id'];

	    if(is_user_logged_in()){
		   	$userID = $this->get_user_id();
		   	if($this->check_if_added($post_id)) {
				$result['type'] = 'error';
				$result['message'] = __( 'You\'ve already added that post' , 'realteo' );
		   	} 
		   	else {
		   		$bookmarked_posts =  (array) $this->get_bookmarked_posts();
		   		$bookmarked_posts[] = $post_id;
				$action = update_user_meta( $userID, 'realteo-bookmarked-posts', $bookmarked_posts );
				
				if($action === false) {
					$result['type'] = 'error';
					$result['message'] = __( 'Oops, something went wrong, please try again' , 'realteo' );
				} else {
					$bookmarks_counter = get_post_meta( $post_id, 'bookmarks_counter', true );
			   		$bookmarks_counter++;
			   		update_post_meta( $post_id, 'bookmarks_counter', $bookmarks_counter );
			  		$bookmarked_posts[] = $post_id;
					$result['type'] = 'success';
					$result['message'] = __( 'Property was bookmarked' , 'realteo' );
				}
			}
		   
		} 
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
	      $result = json_encode($result);
	      echo $result;
	   }
	   else {
	      header('Location: '.$_SERVER['HTTP_REFERER']);
	   }
	   die();

	}	  	

	public function remove_bookmark() {

	   if ( !wp_verify_nonce( $_REQUEST['nonce'], 'realteo_remove_fav_nonce')) {
	      exit('No naughty business please');
	   }   
	   $post_id = $_REQUEST['post_id'];
	   if(is_user_logged_in()){
		   	$userID = $this->get_user_id();
		
	   		$bookmarked_posts = $this->get_bookmarked_posts();
	   		$bookmarked_posts = array_diff($bookmarked_posts, array($post_id));
	        $bookmarked_posts = array_values($bookmarked_posts);

			$action = update_user_meta( $userID, 'realteo-bookmarked-posts', $bookmarked_posts, false );
			if($action === false) {
				$result['type'] = 'error';
				$result['message'] = __('Oops, something went wrong, please try again','realteo');
			} else {
		   		$bookmarks_counter = get_post_meta( $post_id, 'bookmarks_counter', true );
		   		$bookmarks_counter++;
		   		update_post_meta( $post_id, 'bookmarks_counter', $bookmarks_counter );
		   		
				$result['type'] = 'success';
				$result['message'] = __('Property was removed from the list','realteo');
			}
		} 

	   	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
	      $result = json_encode($result);
	      echo $result;
	   	} else {
	      header('Location: '.$_SERVER['HTTP_REFERER']);
	   	}

	   die();

	}

	function get_user_id() {
	    global $current_user;
	    wp_get_current_user();
	    return $current_user->ID;
	}

	function get_bookmarked_posts() {
		return get_user_meta($this->get_user_id(), 'realteo-bookmarked-posts', true);
	}

	function check_if_added($id) {
		$bookmarked_post_ids = $this->get_bookmarked_posts();
		if ($bookmarked_post_ids) {
            foreach ($bookmarked_post_ids as $bookmarked_id) {
                if ($bookmarked_id == $id) { 
                	return true; 
                }
            }
        } 
        return false;
	}
	

	/**
	 * User bookmarks shortcode
	 */
	public function realto_bookmarks( $atts ) {
		
		if ( ! is_user_logged_in() ) {
			return __( 'You need to be signed in to manage your bookmarks.', 'realteo' );
		}

		extract( shortcode_atts( array(
			'posts_per_page' => '25',
		), $atts ) );

		ob_start();
		$template_loader = new Realteo_Template_Loader;

		$template_loader->set_template_data( array( 'current' => 'bookmarks' ) )->get_template_part( 'account/navigation' ); 
		$template_loader->set_template_data( array( 'ids' => $this->get_bookmarked_posts() ) )->get_template_part( 'account/bookmarks' ); 


		return ob_get_clean();
	}


	/**
	 * Get a user's bookmarks
	 * @param  integer $user_id
	 * @param  integer $limit
	 * @param  integer $offset
	 * @return array|object
	 */
	public function get_user_bookmarks( $user_id = 0, $limit = 0, $offset = 0 ) {
		global $wpdb;

		if ( ! $user_id && is_user_logged_in() ) {
			$user_id = get_current_user_id();
		} elseif ( ! $user_id ) {
			return false;
		}

		if ( $limit > 0 ) {
			$results     = $wpdb->get_results( $wpdb->prepare( 'SELECT SQL_CALC_FOUND_ROWS * FROM {$wpdb->prefix}job_manager_bookmarks WHERE user_id = %d ORDER BY date_created LIMIT %d, %d;', $user_id, $offset, $limit ) );
			$max_results = $wpdb->get_var( 'SELECT FOUND_ROWS()' );

			return (object) array(
				'max_found_rows' => $max_results,
				'max_num_pages'  => ceil( $max_results / $limit ),
				'results'        => $results
			);
		} else {
			return $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM {$wpdb->prefix}job_manager_bookmarks WHERE user_id = %d ORDER BY date_created;', $user_id ) );
		}
	}

}