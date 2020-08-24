<?php

class Advanced_Ads_Pro_Module_Inject_Content {

	public function __construct() {
		// add placement types
		add_filter( 'advanced-ads-placement-types', array( $this, 'add_placement_types' ) );
		// TODO load options
		add_filter( 'the_content', array( $this, 'inject_content' ), 100 );
		// action after ad output is created; used for js injection
		add_filter( 'advanced-ads-ad-output', array( $this, 'after_ad_output' ), 10, 2 );
		// action after group output is created; used for js injection
		add_filter( 'advanced-ads-group-output', array( $this, 'after_group_output' ), 10, 2 );
		// inject js after group output when passive cache-busting is used
		add_filter( 'advanced-ads-pro-passive-cb-group-data', array( $this, 'after_group_output_passive' ), 10, 3 );
		// check if content injection is limited for longer texts only
		add_filter( 'advanced-ads-can-inject-into-content', array( $this, 'check_content_length' ), 10, 3 );
		// Allow to prevent injection inside `the_content`.
		add_action( 'advanced-ads-can-inject-into-content', array( $this, 'prevent_injection_the_content' ), 10, 3 );
		// add more tags for content injection
		add_filter( 'advanced-ads-tags-for-injection', array( $this, 'content_injection_tags' ) );
		// add options for additional tags for injections
		add_filter( 'advanced-ads-placement-content-injection-options', array( $this, 'content_injection_tag_options' ), 10, 2 );
		// select items for injection
		add_filter( 'advanced-ads-placement-content-injection-items', array( $this, 'content_injection_items' ), 10, 3 );
		// convert "tag" to xPath expressions
		add_filter( 'advanced-ads-placement-content-injection-xpath', array( $this, 'content_injection_xpath_expressions' ), 10, 3 );		
		// change content injection node
		add_filter( 'advanced-ads-placement-content-injection-node', array( $this, 'content_injection_node' ), 10, 3 );
		// inject ad into footer
		add_action( 'wp_footer', array( $this, 'inject_footer' ), 20 );
		// inject ads into archive pages
		add_action( 'the_post', array( $this, 'inject_loop_post' ), 20, 2 );
		
		// support custom hook for content injections
		if( defined( 'ADVANCED_ADS_PRO_CUSTOM_CONTENT_FILTER' ) ){
			// run Advanced Ads content filter
			add_filter( ADVANCED_ADS_PRO_CUSTOM_CONTENT_FILTER, array( Advanced_Ads::get_instance(), 'inject_content' ) );
			// run Advanced Ads Pro content filter
			add_filter( ADVANCED_ADS_PRO_CUSTOM_CONTENT_FILTER, array( $this, 'inject_content' ) );
		}
	}


	/**
	 * add new placement types
	 *
	 * @since   1.0.0
	 * @param array $types
	 *
	 * @return array $types
	 */
	public function add_placement_types($types) {
		// ad injection on random position
		$types['post_content_random'] = array(
			'title' => __( 'Random Paragraph', 'advanced-ads-pro' ),
			'description' => __( 'After a random paragraph in the main content.', 'advanced-ads-pro' ),
			'image' => AAP_BASE_URL . 'modules/inject-content/assets/img/content-random.png',
			'options' => array( 'show_position' => true, 'uses_the_content' => true )
		);
		// ad injection above the post headline
		$types['post_above_headline'] = array(
			'title' => __( 'Above Headline', 'advanced-ads-pro' ),
			'description' => __( 'Above the main headline on the page (&lt;h1&gt;).', 'advanced-ads-pro' ),
			'image' => AAP_BASE_URL . 'modules/inject-content/assets/img/content-above-headline.png',
			'options' => array( 'show_position' => true, 'uses_the_content' => true )
		);
		// ad injection in the middle of a post
		$types['post_content_middle'] = array(
			'title' => __( 'Content Middle', 'advanced-ads-pro' ),
			'description' => __( 'In the middle of the main content based on the number of paragraphs.', 'advanced-ads-pro' ),
			'image' => AAP_BASE_URL . 'modules/inject-content/assets/img/content-middle.png',
			'options' => array( 'show_position' => true, 'uses_the_content' => true )
		);
		// ad injection at a hand selected element in the frontend
		$types['custom_position'] = array(
			'title' => __( 'Custom Position', 'advanced-ads-pro' ),
			'description' => __( 'Attach the ad to any element in the frontend.', 'advanced-ads-pro' ),
			'image' => AAP_BASE_URL . 'modules/inject-content/assets/img/custom-position.png',
			'options' => array( 'show_position' => true )
		);
		// ad injection at a hand selected element in the frontend
		$types['archive_pages'] = array(
			'title' => __( 'Post Lists', 'advanced-ads-pro' ),
			'description' => __( 'Display the ad between posts on post lists, e.g. home, archives, search etc.', 'advanced-ads-pro' ),
			'image' => AAP_BASE_URL . 'modules/inject-content/assets/img/post-list.png',
			'options' => array( 'show_position' => true, 'show_lazy_load' => true  )
		);
		return $types;
	}


	/**
	 * injected ad randomly into post content
	 *
	 * @since 1.0.0
	 * @param str $content post content
	 */
	public function inject_content( $content = '' ) {
		global $post;

		$options = Advanced_Ads::get_instance()->options();

		// check if ads are disabled in secondary queries and this function was called by ajax (in secondary query)
		if ( ! empty( $options['disabled-ads']['secondary'] ) && ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			return $content;
		}

		// run only within the loop on single pages of public post types
		$public_post_types = get_post_types( array( 'public' => true, 'publicly_queryable' => true ), 'names', 'or' );

		// make sure that no ad is injected into another ad
		if ( get_post_type() == Advanced_Ads::POST_TYPE_SLUG ){
			return $content;
		}

		// check if admin allows injection in all places
		if( ! isset( $options['content-injection-everywhere'] ) ){
		    // check if this is a singular page within the loop or an amp page
		    $is_amp = function_exists( 'advads_is_amp' ) && advads_is_amp();
                    if ( ( ! is_singular( $public_post_types ) && ! is_feed() ) || ( ! $is_amp && ! in_the_loop() ) ) { return $content; }
		}
		
		$placements = get_option( 'advads-ads-placements', array() );
		
		if( ! apply_filters( 'advanced-ads-can-inject-into-content', true, $content, $placements )){
			return $content;
		}

		if( is_array( $placements ) ){
			foreach ( $placements as $_placement_id => $_placement ){
				if ( empty( $_placement['item'] ) ) {
				    continue;
				}

				if ( isset($_placement['type'])
					&& in_array( $_placement['type'],
						array('post_content_random',
						    'post_above_headline',
						    'post_content_middle')) ){

					// don’t inject above headline on non-singular pages
					if( 'post_above_headline' === $_placement['type'] && ! is_singular( $public_post_types ) ){
						continue;
					}

					// check if injection is ok for a specific placement id
					if( ! apply_filters( 'advanced-ads-can-inject-into-content-' . $_placement_id, true, $content, $_placement_id )){
						continue;
					}

					$_options = isset( $_placement['options'] ) ? $_placement['options'] : array();
					$_options['placement']['type'] = $_placement['type'];
					$content .= Advanced_Ads_Select::get_instance()->get_ad_by_method( $_placement_id, Advanced_Ads_Select::PLACEMENT, $_options );
				}
			}
		}

		return $content;
	}

	/**
	 * inject ad into footer
	 *
	 * @since 1.1.2
	 */
	public function inject_footer(){
		$placements = get_option( 'advads-ads-placements', array() );
		if( is_array( $placements ) ){
			foreach ( $placements as $_placement_id => $_placement ){
				if ( isset($_placement['type']) && 'custom_position' == $_placement['type'] ){
					// Do not inject on AMP pages.
					if ( function_exists( 'advads_is_amp' ) && advads_is_amp() ) { continue; }

					$_options = isset( $_placement['options'] ) ? $_placement['options'] : array();
					$_options['placement']['type'] = $_placement['type'];
					echo Advanced_Ads_Select::get_instance()->get_ad_by_method( $_placement_id, Advanced_Ads_Select::PLACEMENT, $_options );
				}
			}
		}
	}

	/**
	 * inject ad output and js code
	 *
	 * @since 1.1
	 * @param str $content ad content
	 * @param obj $ad ad object
	 */
	public function after_ad_output( $content = '', Advanced_Ads_Ad $ad ) {
		if ( isset( $ad->args['previous_method'] ) && Advanced_Ads_Select::GROUP === $ad->args['previous_method'] ) {
			return $content;
		}

		// If cache-busting is used.
		if ( isset( $ad->args['cache_busting_elementid'] ) ) {
			// First, move the empty wrapper because some ad networks do not allow to move ads inserted to the DOM.
			$content = $this->get_output_js( $ad->args['cache_busting_elementid'], $ad->args ) . $content;
		} elseif ( isset( $ad->wrapper['id'] ) ) {
			$content .= $this->get_output_js( $ad->wrapper['id'], $ad->args );
		}

		return $content;
	}

	/**
	 * inject js code after group output
	 *
	 * @param str $output_string final group output
	 * @param obj $group Advanced_Ads_Group
	 */
	public function after_group_output( $output_string, Advanced_Ads_Group $group ) {
		if ( $output_string ) {
			// If cache-busting is used.
			if ( isset( $group->ad_args['cache_busting_elementid'] ) ) {
				// First, move the empty wrapper because some ad networks do not allow to move ads inserted to the DOM.
				$output_string = $this->get_output_js( $group->ad_args['cache_busting_elementid'], $group->ad_args ) . $output_string;
			} else {
				$wrapper_id = Advanced_Ads_Pro_Utils::generate_wrapper_id();

				if ( $js_output = $this->get_output_js( $wrapper_id, $group->ad_args ) ) {
					$output_string = '<div id="' . $wrapper_id . '">' . $output_string . '</div>' . $js_output;
				}
			}
		}

		return $output_string;
	}

	/**
	 * inject js code after group output (passive cache-busting)
	 *
	 * @param arr $group_data
	 * @param obj $group Advanced_Ads_Group
	 * @param string $element_id
	 */
	public function after_group_output_passive( $group_data, Advanced_Ads_Group $group, $element_id ) {
		if ( $element_id && $js_output = $this->get_output_js( $element_id, $group->ad_args ) ) {
			$group_data['group_wrap'][] = array( 'min_ads' => 1, 'before' => $js_output, 'after' => '' );
		}

		return $group_data;
	}


	/**
	 * get js to append after ad/group output
	 *
	 * @return string
	 */
	private function get_output_js( $wrapper_id, $args ) {
		$content = '';
		// Do not inject js on AMP pages.
		if ( function_exists( 'advads_is_amp' ) && advads_is_amp() ) { return $content; }

		// Group refresh: do not move if the top level wrapper was moved earlier.
		if ( isset( $args['group_refresh'] ) && ! $args['group_refresh']['is_top_level'] ) {
			return $content;
		}

		// Move only the most outer group wrapper.
		$top_level = ! isset( $args['previous_method'] ) || 'placement' === $args['previous_method'];
		if ( ! $top_level ) {
			return $content;
		}

		if ( isset ( $args['placement']['type'] ) ) {
			switch( $args['placement']['type'] ){
				case 'post_content_random' :
					$paragraphs_selector = $this->get_paragraph_selector( $args );
					$content .= 'var advads_content_p = jQuery("#'. $wrapper_id .'")' . $paragraphs_selector . ';'
						. 'var advads_content_random_p = advads_content_p.eq( Math.round(Math.random() * ( advads_content_p.length - 1) ) );'
						. 'if( advads_content_random_p.length ) { advads.move("#'. $wrapper_id .'", advads_content_random_p, { method: "insertAfter" }); }';
				break;
				case 'post_above_headline' :
					$content .= 'advads.move("#'. $wrapper_id .'", "h1", { method: "insertBefore" });';
				break;
				case 'post_content_middle' :
					$paragraphs_selector = $this->get_paragraph_selector( $args );
					$content .= 'var advads_content_p = jQuery("#'. $wrapper_id .'")' . $paragraphs_selector . ';'
						. 'var advads_content_center_p = advads_content_p.eq( Math.round( ( advads_content_p.length - 3 ) / 2 ) );'
						. 'if( advads_content_center_p.length ) { advads.move("#'. $wrapper_id .'", advads_content_center_p, { method: "insertAfter" }); }';
				break;
				case 'custom_position' :
					// By element Selector.
					if ( ! isset( $args['inject_by'] ) || $args['inject_by'] === 'pro_custom_element'  ) {
						$target = isset( $args['pro_custom_element'] ) ? $args['pro_custom_element'] : '';
						$position = isset( $args['pro_custom_position'] ) ? $args['pro_custom_position'] : 'insertBefore';
					// By HTML container.
					} else {
						$target = isset( $args['container_id'] ) ? $args['container_id'] : '';
						$position = 'appendTo';
					}
					$options[] = 'method: "'. $position . '"';
					// check if can be moved into hidden elements
					if( defined( 'ADVANCED_ADS_PRO_CUSTOM_POSITION_MOVE_INTO_HIDDEN') ){
						$options[] = 'moveintohidden: "true"';
					}
					$content .= 'advads.move("#'. $wrapper_id .'", "'. $target .'", { '. implode( ', ', $options ) .' });';
				break;
			}

			if ( $content ) {
				if ( ! empty( $args['cache_busting_elementid'] ) ) {
					// Document is ready. Do not use another 'ready' block so that the wrapper is moved before executing js in ad content.
					$content = '<script>' . $content . '</script>';
				} else  {
					$content = '<script>( window.advanced_ads_ready || jQuery( document ).ready ).call( null, function() {' . $content . '});</script>';
				}
			}

		}

		return $content;
	}

	/**
	 * get paragraph selector for js depending on cache busting settings
	 *
	 * @return str $paragraph_selector
	 * @since 1.2.3
	 */
	private function get_paragraph_selector( $args ) {
	    
		// check if level limitation is disabled
		$plugin_options = Advanced_Ads_Plugin::get_instance()->options();
		$content_injection_level_disabled = isset( $plugin_options['content-injection-level-disabled'] );
	    
		/**
		 * find paragraphs
		 *  which are not within tables
		 *  which are not within blockquotes
		 *  which are not empty
		 *  which are not within an image caption
		 * 
		 * depending on "Disable injection limitation" setting, 
		 *  either inject into all p tags, including subordinated 
		 *  or only direct and preceding siblings
		 */
		if( $content_injection_level_disabled ){
			$paragraphs_selector = '.parent().find("p:not(table p):not(blockquote p):not(div.wp-caption p)").filter(function(){return jQuery.trim(this.innerHTML)!==""})';
		} else {
			// Do not use 'prevAll' because it returns elements in reverse order.
			$paragraphs_selector = '.parent().children("p:not(table p):not(blockquote p):not(div.wp-caption p)").filter(function(){return jQuery.trim(this.innerHTML)!==""})';
		}

		// TODO: Deprecated.
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && ! isset( $args['cache_busting_elementid'] ) ) {
			$paragraphs_selector = '.parent()' . $paragraphs_selector;
		}

		return apply_filters( 'advanced-ads-pro-inject-content-selector', $paragraphs_selector );
	}

	/**
	 * check content length for injecting ads into the post content
	 *
	 * @since 1.1
	 * @param bol $inject whether to inject or not
	 * @param str $content post content
	 * @param arr $placements array with all placements
	 * @return bol true, if injection is ok
	 * @deprecated since version 1.2.3 now included in placement options
	 */
	public function check_content_length( $inject = true, $content = '', $placements = array() ){

	    // content injection placements
	    $cj_placements = array( 'post_top', 'post_bottom', 'post_content', 'post_content_random', 'post_content_middle' );

	    // find out of content injection placements are defined at all
	    $injection = false;
	    foreach( $placements as $_placement_id => $_placement ){
		    if( isset( $_placement['type'] ) && in_array( $_placement['type'], $cj_placements ) ){
			    $injection = true;

			    // register filter for placement specific length check
			    add_filter( 'advanced-ads-can-inject-into-content-' . $_placement_id, array( $this, 'check_placement_minimum_length' ), 10, 3 );

			    // count content length
			    // replace html
			    $content = preg_replace( '/<[a-zA-Z\/][^<>]*>/', '', $content );
			    // replace punctuation
			    $content = preg_replace( '/[0-9.(),;:!?%#$¿\'"_+=\\/-]+/', '', $content );

			    // $length = preg_match_all( '/\S\s+/', $content ); // alternative method I might try later
			    // $length = str_word_count( $content, 0 ); // doesn’t work with Cyrillic
			    $length = count( preg_split('/[[:space:]]/', $content ) );

			    // save content length in globals
			    if ( !defined('ADVADS_CURRENT_CONTENT_LENGTH') ){
				    define( 'ADVADS_CURRENT_CONTENT_LENGTH', $length );
			    }
		    }
	    }

	    // get length option
	    $options = Advanced_Ads_Pro::get_instance()->get_options();
	    if( $injection && isset( $options['inject-content']['minimum_length'] ) && '' != $options['inject-content']['minimum_length'] ){

		    error_log( 'Advanced Ads: The check for minimum content length before content injection was moved to placement options. Please remove the value on the Settings page.');

		    if( $length < absint( $options['inject-content']['minimum_length'] )){
			    return false;
		    }
	    }

	    return $inject;
	}

	/**
	 * Allow to prevent injections inside `the_content`.
	 *
	 * @param bool $inject whether to inject or not
	 * @param str $content post content
	 * @param arr $placements array with all placements
	 * @return bool true, if injection is ok
	 */
	public function prevent_injection_the_content( $inject = true, $content = '', $placements = array() ) {
		if ( ! $inject ) {
			return false;
		}

		global $post;
		if( empty( $post->ID ) ){
		    return true;
		}
		
		$post_ad_options = get_post_meta( $post->ID, '_advads_ad_settings', true );

		return empty( $post_ad_options['disable_the_content'] );
	}

	/**
	 * check content length setting of content injection placements
	 *
	 * @since 1.2.3
	 * @param bol $return whether to inject or not
	 * @param str $content post content
	 * @param str $placement_id id of the placement
	 * @return bool false if placement should not show up in current article
	 */
	public function check_placement_minimum_length( $return, $content = '', $placement_id ){

	    // get all placements
	    $placements = Advanced_Ads::get_ad_placements_array();
	    
	    if( ! isset( $placements[ $placement_id ]['options']['pro_minimum_length'] ) || ! $placements[ $placement_id ]['options']['pro_minimum_length'] ){
		    return $return;
	    }
	    
	    if( defined('ADVADS_CURRENT_CONTENT_LENGTH') && ADVADS_CURRENT_CONTENT_LENGTH < absint( $placements[ $placement_id ]['options']['pro_minimum_length'] ) ){
		    return false;
	    }

	    return $return;
	}

	/**
	 * echo ad before/after posts in loops on archive pages
	 *
	 * @since 1.2.1
	 * @param arr $post post object
	 * @param WP_Query $wp_query query object
	 */
	public function inject_loop_post( $post, $wp_query = null ) {
		if ( ! $wp_query instanceof WP_Query || is_feed() || is_admin() || is_single() ) {
			return;
		}

		if( ! isset( $wp_query->current_post )) {
			return;
		};

		$curr_index = $wp_query->current_post + 1; // normalize index

		// 'wp_reset_postdata()' does 'the_post' action
		// handle the situation when wp_reset_postdata() is used after secondary query inside main query
		static $handled_indexes = array();
		if ( $wp_query->is_main_query() ) {
			if ( in_array( $curr_index, $handled_indexes ) ) {
				return;
			}
			$handled_indexes[] = $curr_index;
		}

		$placements = get_option( 'advads-ads-placements', array() );
		if( is_array( $placements ) ){
			foreach ( $placements as $_placement_id => $_placement ){
				if ( empty($_placement['item']) ) {
					continue;
				}

				if ( isset($_placement['type']) && 'archive_pages' === $_placement['type'] ){
					$_options = isset( $_placement['options'] ) ? $_placement['options'] : array();

					if ( empty( $_options['in_any_loop'] )
						&& ( $wp_query->is_singular() || ! $wp_query->in_the_loop || ! $wp_query->is_main_query() ) ) {
						continue;
					}

					if  ( ! did_action( 'wp_head' ) ) {
						continue;
					}

					// don’t attach if not container attachment selected
					/*if( ! isset( $_options['pro_archive_pages_type'] ) || 'container' !== $_options['pro_archive_pages_type'] ){
						continue;
					}*/

					if( isset( $_options['pro_archive_pages_index'] ) ){
						$ad_index = absint( $_options['pro_archive_pages_index'] );
						if( $ad_index === $curr_index ){
							$_options['placement']['type'] = $_placement['type'];
							echo Advanced_Ads_Select::get_instance()->get_ad_by_method( $_placement_id, Advanced_Ads_Select::PLACEMENT, $_options );
						}
					}
				}
			}
		}
	}

	/**
	 * add more tags for content injection
	 *
	 * @since 1.2.4
	 * @param arr $tags registered ad tags
	 * @return arr $tags
	 */
	public function content_injection_tags( $tags ){

		$tags['div'] = sprintf( __( 'container (%s)', 'advanced-ads-pro' ), '&lt;div&gt;' );
		$tags['img'] = sprintf( __( 'image (%s)', 'advanced-ads-pro' ), '&lt;img&gt;' );
		$tags['table'] = sprintf( __( 'table (%s)', 'advanced-ads-pro' ), '&lt;table&gt;' );
		$tags['li'] = sprintf( __( 'list item (%s)', 'advanced-ads-pro' ), '&lt;li&gt;' );
                $tags['blockquote'] = sprintf( __( 'quote (%s)', 'advanced-ads-pro' ), '&lt;blockquote&gt;' );

		$headlines = apply_filters( 'advanced-ads-headlines-for-ad-injection', array( 'h2', 'h3', 'h4' ) );
		$headlines_imploded = '&lt;' . implode( '&gt;, &lt;', $headlines ) . '&gt;';
		$tags['headlines'] = sprintf( __( 'any headline (%s)', 'advanced-ads-pro' ), $headlines_imploded );
		$tags['anyelement'] = 'any element';

		return $tags;
	}

	/**
	 * change options for new content injection tags
	 *
	 * @since 1.2.4
	 * @parem arr $options the options to change
	 * @param str $tag the tag for which the options are
	 * @return arr $options
	 */
	public function content_injection_tag_options( $options, $tag ){
		if( 'img' === $tag  ){
			$options['allowEmpty'] = true; // image tags don’t need text content
		}

		return $options;
	}

	/**
	 * change items for the content injection
	 *
	 * @since 1.2.4
	 * @param obj $items the existing items
	 * @param obj $xpath dom object
	 * @param str $tag the tag for which the options are
	 * @return obj $items
	 */
	public function content_injection_items( $items, $xpath, $tag ){

		if ( 'img' === $tag ) {
			// get any images from the content
			$items = $xpath->query( '/html/body//img[not(ancestor::table)]' );
		}

		return $items;
	}

	/**
	 * convert "tag" to xPath expressions
	 *
	 * @param str $tag the tag for which the options are
	 * @param arr $options placement options
	 * @return xPath expression
	 */
	public function content_injection_xpath_expressions( $tag, $options ) {
		if ( 'headlines' === $tag  ) {
			$headlines = apply_filters( 'advanced-ads-headlines-for-ad-injection', array( 'h2', 'h3', 'h4' ) );

			foreach ( $headlines as &$headline ) {
				$headline = 'self::' . $headline;
			}
			$tag = '*[' . implode( ' or ', $headlines ) . ']'; // /html/body/*[self::h2 or self::h3 or self::h4]
		}

		if ( 'anyelement' === $tag ) {
			$exclude = array(
				'html', 'body', 'script', 'style', 'tr', 'td',
				// Inline tags.
				'a', 'abbr', 'b', 'bdo', 'br', 'button', 'cite', 'code', 'dfn', 'em', 'i',
				'img', 'kbd', 'label', 'option',
				'q', 'samp', 'select', 'small', 'span', 'strong', 'sub', 'sup', 'textarea', 'time', 'tt', 'var', );
			$tag = '*[not(self::' . implode( ' or self::', $exclude ) . ')]';
		}
		return $tag;
	}

	/**
	 * change content injection node
	 *
	 * @since 1.2.4
	 * @parem obj $node content node
	 * @param str $tag the tag for which the options are
	 * @param bool $before if the ad is inserted before the element
	 * @return obj $node
	 */
	public function content_injection_node( $node, $tag, $before ){
		// Prevent injection into image caption and gallery.
		$parent = $node;
		for ( $i = 0; $i < 4; $i++ ) {
			$parent = $parent->parentNode;
			if ( ! $parent instanceof DOMElement ) {
				break;
			}
			if ( preg_match(  '/\b(wp-caption|gallery-size)\b/', $parent->getAttribute( 'class' ) ) ) {
				$node = $parent;
				break;
			}
		}

		// make sure that the ad is injected outside the link
		if( 'img' === $tag && 'a' === $node->parentNode->tagName ){
			if( $before ){
				return $node->parentNode;
			} else {
				// go one level deeper if inserted after to not insert the ad into the link; probably after the paragraph
				return $node->parentNode->parentNode;
			}
		}

		return $node;
	}
}
