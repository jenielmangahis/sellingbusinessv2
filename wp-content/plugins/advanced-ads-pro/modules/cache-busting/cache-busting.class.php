<?php

// -TODO should use a constant for option key as it is shared at multiple positions
class Advanced_Ads_Pro_Module_Cache_Busting {
    /** @#+
     * Cache-busting option values.
     *
     * @var string
     */
    const OPTION_ON = 'on';
    const OPTION_OFF = 'off';
    const OPTION_AUTO = 'auto';
    // Ignore any cache-busting, even for no placement.
    const OPTION_IGNORE = 'ignore';
    /** @#- */

    /**
     * Internal global ad block count.
     *
     * @var integer
     */
    protected static $adOffset = 0;

    /**
     * Module options
     *
     * @var array
     */
    protected $options = array();

    /**
     * Context-switch used for ad override.
     *
     * @var boolean
     */
    protected $isHead = true;

    /**
     * True if ajax, false otherwise.
     *
     * @var boolean
     */
    protected $is_ajax;

    /**
     * Ads, Groups, Placements for JavaScript.
     *
     * @var arrays
     */
    protected $passive_cache_busting_ads = array();
    protected $passive_cache_busting_groups = array();
    protected $passive_cache_busting_placements = array();

    /**
     * Simple items injected using js.
     * Their conditions are not checked for every visitor of a cached page.
     *
     * @var arrays
     */
    protected $js_items = array();

    /**
     * Info about simple items for tracking purpose.
     *
     * @var array
     */
    protected $has_js_items = array();

    /**
     * Ads loaded without cache-busting
     *
     * @var array
     */
    protected $has_ads = array();

    /**
     *  Queries for ads, that need to be loaded with AJAX
     *
     * @var array
     */
    protected static $ajax_queries = array();

    public function __construct() {
        // load options (and only execute when enabled)
        $options = Advanced_Ads_Pro::get_instance()->get_options();
        $this->lazy_load_module_enabled = ! empty( $options['lazy-load']['enabled'] );
        $this->lazy_load_module_offset = ! empty( $options['lazy-load']['offset'] ) ? absint( $options['lazy-load']['offset'] ) : 0;

        if ( isset( $options['cache-busting'] ) ) {
            $this->options = $options['cache-busting'];
        }

        $this->cache_busting_module_enabled = isset( $this->options['enabled'] ) && $this->options['enabled'];
        $this->is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;


        if ( ! $this->is_ajax ) {
            if ( ! is_admin() ) {
                add_action( 'wp', array( $this, 'init_fronend' ) );
                // load Advads Tracking header scripts
                add_filter( 'advanced-ads-tracking-load-header-scripts', array( $this, 'load_tracking_scripts' ), 10, 1 );
            } else {
                // only execute when enabled
                if ( $this->cache_busting_module_enabled ) {
                    new Advanced_Ads_Pro_Module_Cache_Busting_Admin_UI();
                }
            }
        }

        add_filter( 'advanced-ads-ad-output-debug-content', array( $this, 'add_debug_content' ), 10, 2 );
    }

    /**
     *  Init cache-busting frontend after the `parse_query` hook.
     *  Not ajax, not admin.
     */
    public function init_fronend() {
        global $wp_the_query;

        if ( apply_filters( 'advanced-ads-pro-cb-frontend-disable', false )
            // Disable cache-busting on AMP pages.
            || ( function_exists( 'advads_is_amp' ) && advads_is_amp() )
            || $wp_the_query->is_feed()
        ) { return; }


        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_head', array( $this, 'watch_wp_head'), PHP_INT_MAX );
        add_filter( 'advanced-ads-ad-output', array( $this, 'watch_ad_output' ), 100, 2 );
        add_filter( 'advanced-ads-group-output', array( $this, 'watch_group_output' ), 100, 2 );
        // override output based on the Advanced_Ads_Ad object conditions
        add_filter( 'advanced-ads-ad-select-override-by-ad', array( $this, 'override_ad_select_by_ad' ), 10, 3 );
        // override output based on the Advanced_Ads_Group object conditions
        add_filter( 'advanced-ads-ad-select-override-by-group', array( $this, 'override_ad_select_by_group' ), 10, 4 );
        add_action( 'wp_footer', array( $this, 'passive_cache_busting_output' ), 21 );

        if ( ! $this->cache_busting_module_enabled ) {
            return;
        }
        add_filter( 'advanced-ads-ad-select-args', array( $this, 'override_ad_select' ), 100 );
        add_filter( 'advanced-ads-ad-select-args', array( $this, 'disable_global_output_passive' ), 101 );
        add_action( 'advanced-ads-can-display-placement', array( $this, 'placement_can_display' ), 12, 2 );
    }

    /**
     * Output passive cache-busting array
     */
    public function passive_cache_busting_output() {
        // if ( true === WP_DEBUG ) {
        //     echo '<pre>' . htmlentities( print_r( $this->passive_cache_busting_placements, true ) ) . '</pre>';
        // }

        $js_ads = ( $this->passive_cache_busting_ads ) ? json_encode( $this->passive_cache_busting_ads ) : '{}';
        $js_groups = ( $this->passive_cache_busting_groups ) ? json_encode( $this->passive_cache_busting_groups ) : '{}';
        $js_placements = ( $this->passive_cache_busting_placements ) ? json_encode( $this->passive_cache_busting_placements ) : '{}';

        echo '<script>'
        . 'var advads_placement_tests = ' . Advanced_Ads_Pro_Placement_Tests::get_instance()->get_placement_tests_js() . ";\n"
        . 'var advads_passive_ads = ' . $js_ads . ";\n"
        . 'var advads_passive_groups = ' . $js_groups . ";\n"
        . 'var advads_passive_placements = ' . $js_placements . ";\n"
        . 'var advads_ajax_queries = ' . json_encode( self::$ajax_queries ) . ";\n"
        . 'var advads_has_ads = ' . json_encode( $this->has_ads ) . ";\n"
        . 'var advads_js_items = ' . json_encode( $this->js_items ) . ";\n"
		. '( window.advanced_ads_ready || jQuery( document ).ready ).call( null, function() {'
        .     'if ( window.advanced_ads_pro ) {'
        .         'advanced_ads_pro.process_passive_cb();'
        .     '} else if ( window.console && window.console.log ) {'
        .         'console.log(\'Advanced Ads Pro: cache-busting can not be initialized\');'
        .     '} '
        . '});'
        . '</script>';
    }

    public function enqueue_scripts() {
        $advads_plugin = Advanced_Ads::get_instance();
        $uriRelPath = plugin_dir_url( __FILE__ );

        // Include in footer to prevent conflict when Autoptimize and NextGen Gallery are used at the same time.
        $in_footer = defined( 'AUTOPTIMIZE_PLUGIN_DIR' );

        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            wp_register_script( 'krux/postscribe', $uriRelPath . 'inc/postscribe.js', array(), '1.4.0', $in_footer );
            wp_register_script( 'krux/htmlParser', $uriRelPath . 'inc/htmlParser.js', array(), '1.4.0', $in_footer );
            wp_register_script( 'advanced-ads-pro/cache_busting', $uriRelPath . 'inc/base.js', array( 'jquery', 'krux/htmlParser', 'krux/postscribe' ), AAP_VERSION, $in_footer );
        } else {
            // minified
            wp_register_script( 'advanced-ads-pro/cache_busting', $uriRelPath . 'inc/base.min.js', array( 'jquery' ), AAP_VERSION, $in_footer );
        }

        $options = Advanced_Ads_Pro::get_instance()->get_options();
        $info['ajax_url'] = admin_url( 'admin-ajax.php' );
        $info['lazy_load_module_enabled'] = $this->lazy_load_module_enabled;
        $info['lazy_load'] = array (
            'default_offset' => $this->lazy_load_module_offset,
            'offsets'=> apply_filters( 'advanced-ads-lazy-load-placement-offsets', array() ),
        );
        wp_localize_script( 'advanced-ads-pro/cache_busting', 'advanced_ads_pro_ajax_object', $info );        

        wp_enqueue_script( 'advanced-ads-pro/cache_busting' );

        if ( is_admin_bar_showing() ) {
            // add loaded ads to admin bar
            wp_register_script( 'advanced-ads-pro/cache_busting_admin_bar', $uriRelPath . 'inc/admin_bar.js', array( 'advanced-ads-pro/cache_busting' ), AAP_VERSION, $in_footer );
            wp_enqueue_script( 'advanced-ads-pro/cache_busting_admin_bar' );
        }
    }

    /**
     * Provide current_ad propery to client.
     *
     * @param string          $content
     * @param Advanced_Ads_Ad $ad
     *
     * @return string
     */
    public function watch_ad_output( $content, $ad = null ) {
        if ( isset( $ad ) && $ad instanceof Advanced_Ads_Ad ) {
            // build content (arguments are: id, method, title)
            if ( ! empty( $ad->global_output ) ) {
                $this->has_ads[] = array( "$ad->id", 'ad', null );
            } else {
                $this->has_js_items[] = array( 'id' => $ad->id, 'type' => 'ad', 'title' => $ad->title, 'blog_id' => get_current_blog_id() );
            }
        }

        return $content;
    }

    /**
     * Provide current group propery to client.
     *
     * @param string $content
     * @param Advanced_Ads_Group $group
     *
     * @return string
     */
    public function watch_group_output( $content, Advanced_Ads_Group $group ) {
        if ( empty( $group->ad_args['global_output'] ) ) {
            $this->has_js_items[] = array( 'id' => $group->id, 'type' => 'group', 'title' => $group->id );
        }

        return $content;
    }

    /**
     * Turn off head optimisation.
     */
    public function watch_wp_head() {
        $this->isHead = false;
    }

    /**
     * Replace ad content with placeholder.
     *
     * @param array $arguments
     *
     * @return array
     */
    public function override_ad_select( $arguments ) {
        // placements and not Feed only
        $not_feed = empty( $arguments['wp_the_query']['is_feed'] );
        if ( $arguments['method'] === Advanced_Ads_Select::PLACEMENT && $not_feed ) {
            $placements = Advanced_Ads::get_ad_placements_array();
            if ( empty( $placements[ $arguments['id'] ]['item'] ) || ! isset( $placements[ $arguments['id'] ]['type'] ) ) {
                // placement was created but no item was selected in dropdown
                unset( $arguments['override'] );
                return $arguments;
            }

            $arguments['placement_type'] = $placements[ $arguments['id'] ]['type'];
            $options =  isset( $placements[ $arguments['id'] ]['options'] ) ? (array) $placements[ $arguments['id'] ]['options'] : array();

            foreach ( $options as $_k => $_v ) {
                if ( ! isset( $arguments[ $_k ] ) ) {
                    $arguments[ $_k ] = $_v;
                }
            }

            $query = self::build_js_query( $arguments );

            // allow to disable feature
            if ( $this->can_override( $query ) ) {
                $arguments['override'] = $this->get_override_content( $query );
            }
        }

        return $arguments;
    }

    /**
     * Disable global output for passive cache-busting.
     *
     * @param array $arguments
     * @return array $arguments
     */
    public function disable_global_output_passive( $arguments ) {
        $cb_auto = ( isset( $arguments['cache-busting'] ) && $arguments['cache-busting'] === self::OPTION_AUTO );
        if ( $cb_auto && $this->is_passive_method_used() && ! isset( $arguments['global_output'] ) ) {
            $arguments['global_output'] = false;
        }

        return $arguments;
    }

	/**
	 * return ad, prepared for js handler if the conditions are met
	 *
	 * @param string $overriden_ad ad content to override
	 * @param obj $ad Advanced_Ads_Ad
	 * @param array $args argument passed to the 'get_ad_by_id' function
	 * @return string ad content prepared for js handler if the conditions are met 
	 */
	public function override_ad_select_by_ad( $overriden_ad, Advanced_Ads_Ad $ad, $args ) {
        if ( ! $this->can_override_passive( $args ) ) {
            return $overriden_ad;
        }

        if ( $this->cache_busting_module_enabled ) {
            // Cache busting 'auto'.
            $overriden_ad = $this->cache_busting_auto_for_ad( $overriden_ad, $ad, $args );
        }

        if ( false === $overriden_ad ) {
            // The cache-busting module is disabled or the 'off' fallback has been aplied.
            $overriden_ad = $this->get_simple_js_ad( $overriden_ad, $ad, $args );
        }

        return $overriden_ad;
    }

    /**
     * return group, prepared for js handler if the conditions are met
     *
     * @param string $overriden_group group content to override
     * @param obj $adgroup Advanced_Ads_Group
     * @param array/null $ordered_ad_ids ordered ids of the ads that belong to the group
     * @param array $args argument passed to the 'get_ad_by_group' function
     * @return string/false group content prepared for js handler if the conditions are met
     */
    public function override_ad_select_by_group( $overriden_group, Advanced_Ads_Group $adgroup, $ordered_ad_ids, $args ) {
        if ( ! $this->can_override_passive( $args ) ) {
            return $overriden_group;
        }

        if ( $this->cache_busting_module_enabled ) {
            // Cache busting 'auto'.
            $overriden_group = $this->cache_busting_auto_for_group( $overriden_group, $adgroup, $ordered_ad_ids, $args );
        }

        if ( false === $overriden_group ) {
            // The cache-busting module is disabled or the 'off' fallback has been aplied.
            $overriden_group = $this->get_simple_js_group( $overriden_group, $adgroup, $ordered_ad_ids, $args );
        }

        return $overriden_group;
    }


    public function cache_busting_auto_for_ad( $overriden_ad, Advanced_Ads_Ad $ad, $args ) {
        //if it was requested by placement; if cache-busting option does not exists yet, or exist and = 'auto'
        $cache_busting_auto = isset( $args['placement_type'] ) && ( ! isset( $args['cache-busting'] ) || $args['cache-busting'] === self::OPTION_AUTO );
        $cache_busting_off = isset( $args['cache-busting'] ) && $args['cache-busting'] === self::OPTION_OFF;
        $prev_is_placement = isset( $args['previous_method'] ) && $args['previous_method'] === 'placement' && isset( $args['previous_id'] );
        $test_id = isset( $args['test_id'] ) ? $args['test_id'] : null;
        $is_passive_all = ! empty( $this->options['passive_all'] );

        if ( $cache_busting_auto  && ! $this->is_passive_method_used() ) { // ajax method
            $ad->args['cache-busting'] = self::OPTION_ON;
            $overriden_ad = $this->get_overridden_ajax_ad( $ad, $args );
        }
        elseif ( ! $cache_busting_off && ( $cache_busting_auto || $is_passive_all ) ) { // passive method
            // ad was requested by group `placement->group->ad` or `group->ad`
            if ( isset( $args['previous_method'] ) && $args['previous_method'] === 'group' && isset( $args['previous_id'] ) ) {
                return $ad;
            }

            $needs_backend = $this->ad_needs_backend_request( $ad );

            // ad was requested by placement `placement->ad` or `ad`
            // check if ad can be delivered without any cache-busting
            if ( 'static' === $needs_backend && ! $is_passive_all && ! $test_id ) {
                $ad->args['cache-busting'] = self::OPTION_OFF;
                $ad->global_output = true;
                if ( isset( $args['output']['placement_id'] ) ) {
                    if ( ! $this->placement_can_display_not_passive( $args['output']['placement_id'] ) ) { return ''; }
                    $this->add_placement_to_current_ads( $args['output']['placement_id'] );
                }

                return $overriden_ad;
            }
            // check if ad cannot be delivered with passive cache-busting
            if ( 'off' === $needs_backend || 'ajax' === $needs_backend ) {
                $is_ajax_fallbback = 'ajax' === $needs_backend;

                if ( isset( $args['output']['placement_id'] ) && ! $this->placement_can_display_not_passive( $args['output']['placement_id'] ) ) {
                    // prevent selection of this placement using JavaScript
                    if ( $test_id ){
                        Advanced_Ads_Pro_Placement_Tests::get_instance()->no_cb_fallbacks[] = $args['previous_id'];
                    }
                    return '';
                }

                if ( $is_ajax_fallbback && $cache_busting_auto ) {
                    $ad->args['cache-busting'] = self::OPTION_ON;
                    return $this->get_overridden_ajax_ad( $ad, $args );
                }

                // `No cache-busting` fallback
                if ( $test_id ) {
                    if ( in_array( $args['previous_id'], Advanced_Ads_Pro_Placement_Tests::get_instance()->get_random_placements() ) ) {
                        Advanced_Ads_Pro_Placement_Tests::get_instance()->delivered_tests[ $args['previous_id'] ] = $test_id;
                    } else {
                        // prevent selection of this placement using JavaScript
                        Advanced_Ads_Pro_Placement_Tests::get_instance()->no_cb_fallbacks[] = $args['previous_id'];
                        return '';
                    }
                }
                $ad->args['cache-busting'] = self::OPTION_OFF;
                $ad->args['global_output'] = true;
                $ad->global_output = true;
                if ( $prev_is_placement ) {
                    $this->add_placement_to_current_ads( $args['previous_id'] );
                }
                return $overriden_ad;
            }

            if ( ! $ad->can_display( array( 'passive_cache_busting' => true ) ) ) {
                if ( $test_id && array_key_exists( $args['previous_id'], Advanced_Ads_Pro_Placement_Tests::get_instance()->delivered_tests ) ) {
                    Advanced_Ads_Pro_Placement_Tests::get_instance()->delivered_tests[ $args['previous_id'] ] = $test_id;
                }

                return '';
            }
            // deliver ad using passive cache-busting
            // add new info to the passive cache-busting array
            $overriden_ad = $this->get_passive_overriden_ad( $ad, $args );
        }

        if ( $prev_is_placement && false === $overriden_ad && $test_id ) {
            Advanced_Ads_Pro_Placement_Tests::get_instance()->delivered_tests[ $args['previous_id'] ] = $test_id ;
        }

        return $overriden_ad;
    }

    public function cache_busting_auto_for_group( $overriden_group, Advanced_Ads_Group $adgroup, $ordered_ad_ids, $args ) {
        $prev_is_placement = isset( $args['previous_method'] ) && $args['previous_method'] === 'placement' && isset( $args['previous_id'] );
        $cache_busting_auto = isset( $args['placement_type'] ) && ( ! isset( $args['cache-busting'] ) || $args['cache-busting'] === self::OPTION_AUTO );
        $test_id = isset( $args['test_id'] ) ? $args['test_id'] : null;
        $is_passive_all = ! empty( $this->options['passive_all'] );
        $cache_busting_off = isset( $args['cache-busting'] ) && $args['cache-busting'] === self::OPTION_OFF;

        if ( $cache_busting_auto && ! $this->is_passive_method_used() ) { // ajax method
            // if > 0 active ad in this group.
            if ( is_array( $ordered_ad_ids ) && count( $ordered_ad_ids ) > 0 ) {
                $adgroup->ad_args['cache-busting'] = self::OPTION_ON;
                $query = self::build_js_query( $args);
                return $this->get_override_content( $query );
            }
        }
        elseif ( ! $cache_busting_off && ( $cache_busting_auto || $is_passive_all ) ) { // passive method
            if ( is_array( $ordered_ad_ids ) && count( $ordered_ad_ids ) > 0 ) {
                // add info about the group to the passive cache-busting array
                $uniq_key = ++self::$adOffset;
                $output_string = $this->get_passive_overriden_group( $adgroup, $ordered_ad_ids, $args, $uniq_key );

                $ad_label = isset( $args['ad_label'] ) ? $args['ad_label'] : 'default';
                foreach ( $ordered_ad_ids as $_ad_id ) {
                    $args['global_output'] = false;
                    $args['ad_label'] = 'disabled';
                    $args['group_info'] = array (
                        'passive_cb' => true,
                        'type' => $adgroup->type,
                        'refresh_enabled' => Advanced_Ads_Pro_Group_Refresh::is_enabled( $adgroup ),
                    );
                    // get result from the 'override_ad_select_by_ad' method
                    $ad = Advanced_Ads_Select::get_instance()->get_ad_by_method( $_ad_id, Advanced_Ads_Select::AD, $args );
                    if ( ! $ad instanceof Advanced_Ads_Ad || ! $ad->can_display( array( 'passive_cache_busting' => true ) ) ) {
                        continue;
                    }

                    $needs_backend = $this->ad_needs_backend_request( $ad );

                    if ( 'off' === $needs_backend || 'ajax' === $needs_backend ) {
                        $is_ajax_fallbback = 'ajax' === $needs_backend;

                        // delete info from the passive cache-busting array
                        $this->delete_passive_group( $adgroup, $args, $uniq_key );
                        $args['ad_label'] = $ad_label;


                        if ( isset( $args['output']['placement_id'] ) && ! $this->placement_can_display_not_passive( $args['output']['placement_id'] ) ) {
                            // prevent selection of this placement using JavaScript
                            if ( $test_id ){
                                Advanced_Ads_Pro_Placement_Tests::get_instance()->no_cb_fallbacks[] = $args['previous_id'];
                            }
                            return '';
                        }

                        if ( $is_ajax_fallbback && $cache_busting_auto ) {
                            $adgroup->ad_args['cache-busting'] = self::OPTION_ON;
                            $query = self::build_js_query( $args);
                            return $this->get_override_content( $query );
                        } else {
                            // `No cache-busting` fallback
                            if ( $test_id ) {
                                if ( in_array( $args['previous_id'], Advanced_Ads_Pro_Placement_Tests::get_instance()->get_random_placements() ) ) {
                                    Advanced_Ads_Pro_Placement_Tests::get_instance()->delivered_tests[ $args['previous_id'] ] = $test_id;
                                } else {
                                    // prevent selection of this placement using JavaScript
                                    Advanced_Ads_Pro_Placement_Tests::get_instance()->no_cb_fallbacks[] = $args['previous_id'];
                                    return '';
                                }
                            }

                            $adgroup->ad_args['cache-busting'] = self::OPTION_OFF;
                            unset( $adgroup->ad_args['cache_busting_elementid'] );
                            $adgroup->ad_args['global_output'] = true;
                            if ( $prev_is_placement ) {
                                $this->add_placement_to_current_ads( $args['previous_id'] );
                            }

                            return $overriden_group;
                        }
                    }
                    // add info about the ad to the passive cache-busting array
                    $this->add_passive_ad_to_group( $ad, $args, $uniq_key );
                }

                $overriden_group = $output_string;
            }
        }

        if ( $prev_is_placement && false === $overriden_group && $test_id ) {
            Advanced_Ads_Pro_Placement_Tests::get_instance()->delivered_tests[ $args['previous_id'] ] = $test_id;
        }

        return $overriden_group;

    }

    /**
     * Get simple js ad.
     * Conditions are not checked for every visitor of a cached page.
     */
    public function get_simple_js_ad( $overriden_ad, Advanced_Ads_Ad $ad, $args ) {
        $cp_placement = isset( $args['placement_type'] ) && $args['placement_type'] === 'custom_position';

        if ( ! $cp_placement
            // Check if collecting of simple ads has been started.
            || ! empty( $ad->args['_collect_simple_item'] ) ) {
            return $overriden_ad;
        }

        if ( $ad->can_display() ) {
            // Disable global output because the ads will be tracked using an AJAX request.
            $ad->args['global_output'] = false;
            $ad->global_output = false;
            $ad->args['_collect_simple_item'] = true;

            $l = count( $this->has_js_items );
            $overriden_ad = $this->add_simple_js_item( $ad->output(), $l, $args );

            $ad->args['global_output'] = true;
            $ad->global_output = true;
        }
        return $overriden_ad;
    }

    /**
     * Get simple js group.
     * Conditions are not checked for every visitor of a cached page.
     */
    public function get_simple_js_group( $overriden_group, Advanced_Ads_Group $adgroup, $ordered_ad_ids, $args ) {
        $cp_placement = isset( $args['placement_type'] ) && $args['placement_type'] === 'custom_position';

        if ( ! $cp_placement
            // Check if collecting of simple ads has been started.
            || ! empty( $adgroup->ad_args['_collect_simple_item'] ) ) {
            return $overriden_group;
        }

        // Disable global output because the ads will be tracked using an AJAX request.
        $adgroup->ad_args['global_output'] = false;
        $adgroup->ad_args['_collect_simple_item'] = true;

        $l = count( $this->has_js_items );
        $overriden_group = $this->add_simple_js_item( $adgroup->output( $ordered_ad_ids ), $l, $args );

        $adgroup->ad_args['global_output'] = true;
        return $overriden_group;
    }

    /**
     * Add simple js item.
     *
     * @param string $output Ad/Group output.
     * @param int $l Number of existing simple js items.
     * @param array $args Placement options.
     * @return string Wrapper id.
     */
    function add_simple_js_item( $output, $l, $args ) {
        $elementid = $this->generate_elementid();
        $this->js_items[] = array(
            'output' => $output,
            'elementid' => $elementid,
            'args' => $args,
            'has_js_items' => array_slice( $this->has_js_items, $l ),
        );
        /**
         * Collect blog data before `restore_current_blog` is called
         */
        if ( class_exists( 'Advanced_Ads_Tracking_Util', false ) && method_exists( 'Advanced_Ads_Tracking_Util', 'collect_blog_data' ) ) {
            $tracking_utils = Advanced_Ads_Tracking_Util::get_instance();
            $tracking_utils->collect_blog_data();
        }
        return $this->create_wrapper( $elementid );
    }

    /**
     * add data related to ad and ad placement to js array
     *
     * @param obj $ad Advanced_Ads_Ad
     * @param array $args argument passed to the 'get_ad_by_id' function
     * @return string
     */
    private function get_passive_overriden_ad( Advanced_Ads_Ad $ad, $args ) {
        $cache_busting_auto = isset( $args['placement_type'] ) && ( ! isset( $args['cache-busting'] ) || $args['cache-busting'] === self::OPTION_AUTO );

        if ( $cache_busting_auto ) {
            $js_array = & $this->passive_cache_busting_placements;
            $id = $args['previous_id'];
        } else {
            $js_array = & $this->passive_cache_busting_ads;
            $id = $args['id'];
        }
        $uniq_key = $id . '_' . ++self::$adOffset;

        $not_head = ! $this->isHead || ( isset( $args['placement_type'] ) && $args['placement_type'] !== 'header' );
        $elementid = $not_head ? $this->generate_elementid() : null;
        $ad->args['cache_busting_elementid'] = $elementid;
        $output_string = $not_head ? $this->create_wrapper( $elementid, $args ) : '';


        $js_array[ $uniq_key ] = array(
            'elementid' => array( $elementid ),
            'ads' => array( $ad->id => $this->get_passive_cb_for_ad( $ad ) ), // only 1 ad
        );

        if ( $cache_busting_auto ) {
            $placements = Advanced_Ads::get_ad_placements_array();
            $placement_info = $placements[ $id ];
            $placement_info['id'] = (string) $id;
            $test_id = isset( $args['test_id'] ) ? $args['test_id'] : null;

            $js_array[ $uniq_key ]['type'] = 'ad';
            $js_array[ $uniq_key ]['id'] = $ad->id;
            $js_array[ $uniq_key ]['placement_info']  = $placement_info;
            $js_array[ $uniq_key ]['test_id'] = $test_id;

            if ( $ad_for_adblocker = Advanced_Ads_Pro_Module_Ads_For_Adblockers::get_ad_for_adblocker( $args ) ) {
                $js_array[ $uniq_key ]['ads_for_ab'] = array( $ad_for_adblocker->id => $this->get_passive_cb_for_ad( $ad_for_adblocker ) );
            }
        }


        return $output_string;
    }

    /**
     * add data related to group and group placement to js array
     *
     * @param obj $adgroup Advanced_Ads_Group
     * @param array/null $ordered_ad_ids ordered ids of the ads that belong to the group
     * @param array $args argument passed to the 'get_ad_by_group' function
     * @param str $uniq_key Property name in JS array.
     * @return string
     */
    private function get_passive_overriden_group( Advanced_Ads_Group $adgroup, $ordered_ad_ids, $args, $uniq_key ) {
        $cache_busting_auto = isset( $args['placement_type'] ) && ( ! isset( $args['cache-busting'] ) || $args['cache-busting'] === self::OPTION_AUTO );

        if ( $cache_busting_auto ) {
            $js_array = & $this->passive_cache_busting_placements;
            $id = $args['previous_id'];
        } else {
            $js_array = & $this->passive_cache_busting_groups;
            $id = $args['id'];
        }
        $uniq_key = $id . '_' . $uniq_key;

        $not_head = ! $this->isHead || ( isset( $args['placement_type'] ) && $args['placement_type'] !== 'header' );
        $elementid = $not_head ? $this->generate_elementid() : null;
        $adgroup->ad_args['cache_busting_elementid'] = $elementid;
        $output_string = $not_head ? $this->create_wrapper( $elementid, $args ) : '';

        // remove ads with 0 ad weight
        $weights = $adgroup->get_ad_weights();
        foreach ( $weights as $_ad_id => $_ad_weight ){
            if ( $_ad_weight === 0 ){
                unset( $weights[ $_ad_id ] );
            }
        }

        if ( ( $ad_count = apply_filters( 'advanced-ads-group-ad-count', $adgroup->ad_count, $adgroup ) ) === 'all' ) {
            $ad_count = 999;
        }

        $js_array[ $uniq_key ] = array (
            'type'=> 'group',
            'id' => $adgroup->id,
            'elementid' => array( $elementid ),
            'ads' =>array(),
            'group_info' => array(
                'id' => $adgroup->id,
                'name' => $adgroup->name,
                'weights' => $weights,
                'type' => $adgroup->type,
                'ordered_ad_ids' => $ordered_ad_ids,
                'ad_count' => $ad_count,
            ),
        );

        // deprecated after Advaned Ads Slider > 1.3.1
        if ( 'slider' === $adgroup->type && defined( 'AAS_VERSION' ) && version_compare( AAS_VERSION, '1.3.1', '<=' ) ) {
            $slider_options = Advanced_Ads_Slider::get_slider_options( $adgroup );
            $js_array[ $uniq_key ]['group_info']['slider_options'] = $slider_options;
        }



        if ( Advanced_Ads_Pro_Group_Refresh::is_enabled( $adgroup ) ) {
            $js_array[ $uniq_key ]['group_info']['refresh_enabled'] = true;
            $js_array[ $uniq_key ]['group_info']['refresh_interval_for_ads'] = Advanced_Ads_Pro_Group_Refresh::get_ad_intervals( $adgroup );
        }

        if ( $cache_busting_auto ) {
            $placements = Advanced_Ads::get_ad_placements_array();
            $placement_info = $placements[ $id ];
            $placement_info['id'] = (string) $id;
            $js_array[ $uniq_key ]['placement_info'] = $placement_info;
            $js_array[ $uniq_key ]['test_id'] = isset( $args['test_id'] ) ? $args['test_id'] : null;

            if ( $ad_for_adblocker = Advanced_Ads_Pro_Module_Ads_For_Adblockers::get_ad_for_adblocker( $args ) ) {
                $js_array[ $uniq_key ]['ads_for_ab'] = array( $ad_for_adblocker->id => $this->get_passive_cb_for_ad( $ad_for_adblocker ) );
            }
        }

        $js_array[ $uniq_key ] = apply_filters( 'advanced-ads-pro-passive-cb-group-data', $js_array[ $uniq_key ], $adgroup, $elementid );


        $advads_plugin = Advanced_Ads::get_instance();
        $label = '';
        if ( method_exists( $advads_plugin, 'get_label' ) ) {
            $placement_state = isset( $args['ad_label'] ) ? $args['ad_label'] : 'default';
            $label = Advanced_Ads::get_instance()->get_label( $placement_state );
        }

        // Add wrapper around group.
        if ( ( ! empty( $adgroup->wrapper ) || $label )
            && is_array( $adgroup->wrapper )
            && class_exists( 'Advanced_Ads_Utils' ) && method_exists( 'Advanced_Ads_Utils' , 'build_html_attributes' )
        ) {
            $before = '<div' . Advanced_Ads_Utils::build_html_attributes( $adgroup->wrapper ) . '>' . $label;

            $js_array[ $uniq_key ]['group_wrap'][] = array(
                'before' => $before,
                'after' => apply_filters( 'advanced-ads-output-wrapper-after-content-group', '', $adgroup )
                . '</div>'
            );
        }


        return $output_string;
    }

    /**
     * add new passive ad to passive cb js array
     *
     * @param obj $ad Advanced_Ads_Ad
     * @param array $args argument passed to the 'get_ad_by_id' function
     * @param str $uniq_key Property name in JS array.
     */
    private function add_passive_ad_to_group( Advanced_Ads_Ad $ad, $args, $uniq_key ) {
        $cache_busting_auto = isset( $args['placement_type'] ) && ( ! isset( $args['cache-busting'] ) || $args['cache-busting'] === self::OPTION_AUTO );

        if ( $cache_busting_auto ) {
            $uniq_key = $args['previous_id'] . '_' . $uniq_key;
            $this->passive_cache_busting_placements[ $uniq_key ]['ads'][ $ad->id ] = $this->get_passive_cb_for_ad( $ad );
        } else {
            $uniq_key = $args['id'] . '_' . $uniq_key;
            $this->passive_cache_busting_groups[ $uniq_key ]['ads'][ $ad->id ] = $this->get_passive_cb_for_ad( $ad );
        }
    }

    /**
     * delete an ad from passive cb js array
     *
     * @param $adgroup Advanced_Ads_Group
     * @param array $args argument passed to the 'get_ad_by_id' function
     * @param str $uniq_key Property name in JS array.
     */
    private function delete_passive_group( Advanced_Ads_Group $adgroup, $args, $uniq_key ) {
        $cache_busting_auto = isset( $args['placement_type'] ) && ( ! isset( $args['cache-busting'] ) || $args['cache-busting'] === self::OPTION_AUTO );

        if ( $cache_busting_auto ) {
            $uniq_key = $args['previous_id'] . '_' . $uniq_key;
            unset( $this->passive_cache_busting_placements[ $uniq_key ] );
        } else {
            $uniq_key = $args['id'] . '_' . $uniq_key;
            unset( $this->passive_cache_busting_groups[ $uniq_key ] );
        }
    }

    /**
     * get ad info for passive cache-busting
     *
     * @param obj $ad Advanced_Ads_Ad
     * @return array
     */
    public function get_passive_cb_for_ad( Advanced_Ads_Ad $ad ) {
        $ad_options = $ad->options();
        $ad->args['cache-busting'] = self::OPTION_AUTO;

        $passive_cb_for_ad = apply_filters( 'advanced-ads-pro-passive-cb-for-ad', array(
            'id' => $ad->id,
            'title' => $ad->title,
            'expiry_date' => (int) $ad->expiry_date,
            'visitors' => ( ! empty( $ad_options['visitors'] ) && is_array( $ad_options['visitors'] ) ) ? array_values( $ad_options['visitors'] ) : array(),
            'content' => $ad->output( array( 'global_output' => false ) ),
            'once_per_page' => ( ! empty( $ad_options['output']['once_per_page'] ) ) ? 1 : 0,
            'debugmode' => isset( $ad->output['debugmode'] ),
			'blog_id' => get_current_blog_id(),
			'type' => $ad->type
        ), $ad );

        if ( ! empty( $ad_options['privacy']['ignore-consent'] ) ) {
            $passive_cb_for_ad['privacy']['ignore'] = true;
        }
		
		/**
		 * Collect blog data before `restore_current_blog` is called 
		 */
		if ( class_exists( 'Advanced_Ads_Tracking_Util', false ) && method_exists( 'Advanced_Ads_Tracking_Util', 'collect_blog_data' ) ) {
			$tracking_utils = Advanced_Ads_Tracking_Util::get_instance();
			$tracking_utils->collect_blog_data();
		}
		
        return $passive_cb_for_ad;
    }

    /**
     * return wrapper and js code to load the ad
     *
     * @param obj $ad Advanced_Ads_Ad
     * @param array $args argument passed to the 'get_ad_by_id' function
     * @return string/bool $overridden_ad
     */
    public function get_overridden_ajax_ad( $ad, $args ) {
        $overridden_ad = false;
        $test_id = isset( $args['test_id'] ) ? $args['test_id'] : null;
        $needs_backend = $this->ad_needs_backend_request( $ad );

        if ( 'static' !== $needs_backend || $test_id ) {
            $query = self::build_js_query( $args);
            $overridden_ad = $this->get_override_content( $query );
        }

        return $overridden_ad;
    }

    /**
     * Determine if backend request is needed.
     *
     * @param obj $ad Advanced_Ads_Ad
     * @return string
     *     'static'   Do not use cache-busting. There are no dynamic conditions, all users will see the same.
     *     'off'      Do not use cache-busting (fallback).
     *     'ajax'     Use AJAX request (fallback).
     *     'passive'  Use passive cache-busting.
     */
    public function ad_needs_backend_request( Advanced_Ads_Ad $ad ) {
        $ad_options = $ad->options();
        // code is evaluated as php if setting was never saved or php is allowed
        $allow_php = ( 'plain' === $ad->type && ( ! isset( $ad->output['allow_php'] ) || $ad->output['allow_php'] ) );
        // if there is at least one visitor condition (check old "visitor" and new "visitors" conditions)
        $is_visitor_conditions = ( ( ! empty( $ad_options['visitors'] ) && is_array( $ad_options['visitors'] ) )
            || ( ! empty( $ad_options['visitor'] ) && is_array( $ad_options['visitor'] ) ) );
        $is_group = 'group' === $ad->type;
        $has_shortcode = ! empty( $ad_options['output']['has_shortcode'] )
            // The Rich Content ad type saved long time ago.
            || ( ! isset( $ad_options['output']['has_shortcode'] ) && $ad->type === 'content' );
        $is_lazy_load = $this->lazy_load_module_enabled && isset( $ad_options['lazy_load'] ) && 'enabled' === $ad_options['lazy_load'];
        // Check if there is conditions that need backend request.
        $has_not_js_conditions = false;
        if ( ! empty( $ad_options['visitors'] ) && is_array( $ad_options['visitors'] ) ) {
            $visitors = $ad_options['visitors'];
            // Conditions that can be checked using js.
            $js_visitor_conditions = array(
                'mobile',
                'referrer_url',
                'user_agent',
                'request_uri',
                'browser_lang',
                'cookie',
                'page_impressions',
                'ad_impressions',
                'new_visitor',
                'device_width',
                'tablet',
            );
            $js_visitor_conditions = apply_filters( 'advanced-ads-js-visitor-conditions', $js_visitor_conditions );

            foreach ( $visitors as $visitor ) {
                if ( ! in_array( $visitor['type'], $js_visitor_conditions ) ) {
                    // Use AJAX cache-busting, or disable cache-busting.
                    $has_not_js_conditions = true;
                }
            }
        }

        $has_tracking = false;
        if ( class_exists( 'Advanced_Ads_Tracking', false ) &&
            ( ( isset( $ad_options['tracking']['impression_limit'] ) && $ad_options['tracking']['impression_limit'] ) ||
            ( isset( $ad_options['tracking']['click_limit'] ) && $ad_options['tracking']['click_limit'] ) ) 
        ) {
            // Use AJAX cache-busting, or disable cache-busting.
            $has_tracking = true;
        }

        $has_test = ! empty( $ad_options['test_id'] );

        $need_consent = false;
        if ( empty( $ad_options['privacy']['ignore-consent'] )
            && class_exists( 'Advanced_Ads_Privacy' ) ) {
            $privacy_options = Advanced_Ads_Privacy::get_instance()->options();
            if ( ! empty( $privacy_options['enabled'] ) ) {
                $need_consent = true;
            }
        }
        $specific_days = ! empty( $ad_options['weekdays']['enabled'] );
        $cp_placement = isset( $ad_options['placement_type'] ) && $ad_options['placement_type'] === 'custom_position';

        $fallback = ( ! isset( $this->options['default_fallback_method'] ) || $this->options['default_fallback_method'] === 'ajax' ) ? 'ajax' : 'off';
        if ( $allow_php || $is_group || $has_shortcode || $has_not_js_conditions || $has_tracking ) {
            // Use AJAX cache-busting, or disable cache-busting.
            $return = $fallback;
        } elseif ( $is_visitor_conditions || $is_lazy_load || $need_consent || $specific_days || $cp_placement ) {
            // Passive cache-busting.
            $return = 'passive';
        } else {
            $return = 'static';
        }

        $return = apply_filters( 'advanced-ads-pro-ad-needs-backend-request', $return, $ad, $fallback );
        return $return;
    }

	/**
	 * Prepare query for js handler
	 *
	 * @param array $arguments
	 * @return array query
	 */
	public static function build_js_query( $arguments ) {
		// base query (required keys)
		$query = array(
			'id' => (string) $arguments['id'],
			'method' => (string) $arguments['method'],
		);
		$arguments['global_output'] = true;

		// process further arguments (optional keys)
		$params = array_diff_key( $arguments, array( 'id' => false, 'method' => false ) );

		if ( $params !== array() ) {
			$query['params'] = $params;
		}
		return $query;
	}

    /**
     * Determine override option for query.
     *
     * @param array $query
     *
     * @return boolean
     */
    protected function can_override( $query ) {
        $params = isset( $query['params'] ) ? $query['params'] : array();

        // allow disable cache-busting according to placement settings
        if ( $query['method'] === 'placement' && ! isset( $params['cache-busting'] ) ) {
            $placement_options = Advanced_Ads::get_ad_placements_array();

            if ( isset( $placement_options[ $query['id'] ]['options']['cache-busting'] ) ) {
                $params['cache-busting'] = $placement_options[ $query['id'] ]['options']['cache-busting'];
            }
        }

        return isset( $params['cache-busting'] ) && $params['cache-busting'] === self::OPTION_ON;
    }

    /**
     * Check if passive cache-busting can be used.
     *
     * @param array $args argument passed to ads.
     * @return bool
     */
    private function can_override_passive( $args ) {
        if ( ! empty( $args['wp_the_query']['is_feed'] ) || ! array_key_exists( 'previous_method', $args ) || ! array_key_exists( 'previous_id', $args ) ) {
            return false;
        }

        // Prevent non-header placement from being collected through wp_head.
        if ( doing_action( 'wp_head' ) && isset( $args['placement_type'] ) && 'header' !== $args['placement_type'] ) {
            return false;
        }

        if ( isset( $args['cache-busting'] ) && $args['cache-busting'] === self::OPTION_IGNORE ) {
            return false;
        }

        return true;
    }

    /**
     * Prepare ad for js handler.
     *
     * @param string $query
     * @return string
     */
    protected function get_override_content( $query ) {
        $content = '';

        // Prevent non-header placement from being collected through wp_head.
        if ( doing_action( 'wp_head' ) && isset( $query['params']['placement_type'] ) && 'header' !== $query['params']['placement_type'] ) {
            return $content;
        }

        // <head> scripts require no wrapper
        if ( ! $this->isHead
            || ( isset( $query['params']['placement_type'] ) && $query['params']['placement_type'] !== 'header' )
        ) {
            $query['elementid'] = $this->generate_elementid();
            $content .= $this->create_wrapper( $query['elementid'], $query );
        }

        // Request placement.
        if ( isset( $query['params']['output']['placement_id'] ) ) {
            $query['method'] = 'placement';
            $query['id'] = $query['params']['output']['placement_id'];
        }
        $query['blog_id'] = get_current_blog_id();
	    
	/**
	 * Collect blog data before `restore_current_blog` is called 
	 */
	if ( class_exists( 'Advanced_Ads_Tracking_Util', false ) && method_exists( 'Advanced_Ads_Tracking_Util', 'collect_blog_data' ) ) {
		$tracking_utils = Advanced_Ads_Tracking_Util::get_instance();
		$tracking_utils->collect_blog_data();
	}
	    
        self::$ajax_queries[] = $query;
        return $content;
    }


    /**
     * Create wrapper for cache-busting.
     *
     * @param string $element_id Id of the wrapper.
     * @return array $args.
     */
    private function create_wrapper( $element_id ) {
        return '<div id="' . $element_id . '"></div>';
    }




    /**
     * Generate unique element id
     *
     * @return string
     */
    public function generate_elementid() {
        $prefix = Advanced_Ads_Plugin::get_instance()->get_frontend_prefix();
        return $prefix . md5( 'advanced-ads-pro-ad-' . uniqid( ++self::$adOffset, true ) );
    }

    /**
     * Check if placement can be displayed without passive cache-busting.
     *
     * @param string $id Placement id.
     * @see placement_can_display()
     * @return bool
     */
    private function placement_can_display_not_passive( $id ) {
        // We force this filter to return true when collecting placements for passive cache-busting.
        // For now revoke this behavior
        return apply_filters( 'advanced-ads-can-display-placement', true, $id );
    }

    /**
     * check if placement was closed before
     *
     * @param int $id placement id
     * @return bool whether placement can be displayed or not
     */
    public function placement_can_display( $return, $id = 0 ){
        static $checked_passive = array();

        if ( in_array( $id, $checked_passive ) ) {
            // Ignore current filter when the placement is delivered without passive cache-busting.
            return $return;
        }

        // get all placements
        $placements = Advanced_Ads::get_ad_placements_array();

        $cache_busting_auto = ! isset( $placements[ $id ]['options']['cache-busting'] ) || $placements[ $id ]['options']['cache-busting'] === self::OPTION_AUTO;

        if ( $cache_busting_auto && $this->is_passive_method_used() ) {
            $checked_passive[] = $id;
            return true;
        }

        return $return;
    }

    /**
     * determines, whether the "passive"  method is used or not
     *
     * @return bool true if the "passive" method is used, false otherwise
     */
    public function is_passive_method_used() {
        return isset( $this->options['default_auto_method'] ) && $this->options['default_auto_method'] === 'passive';
    }

    /**
     * determines, whether or not to load tracking scripts
     *
     * @param bool  $need_load_header_scripts
     * @return bool true if tracking scripts should be loaded, $need_load_header_scripts otherwise
     */
    public function load_tracking_scripts( $need_load_header_scripts ) {
        //the script is used by: passive cache-busting, 'group refresh' feature
        return true;
    }

    /**
     * Add ad debug content
     *
     * @param arr $content
     * @param obj $ad Advanced_Ads_Ad
     * @return arr $content
     */
    public function add_debug_content( $content, Advanced_Ads_Ad $ad ) {
        $needs_backend = $this->ad_needs_backend_request( $ad );
        if ( 'off' === $needs_backend || 'ajax' === $needs_backend ) {
            $info = __( 'The ad can not work with passive cache-busting', 'advanced-ads-pro' );
        } else {
            $info = __( 'The ad can work with passive cache-busting', 'advanced-ads-pro' );
        }

        if ( $this->is_ajax ) {
            $name = _x( 'ajax', 'setting label', 'advanced-ads-pro' );
        } elseif ( isset( $ad->args['cache-busting'] ) && $ad->args['cache-busting'] === self::OPTION_AUTO ) {
            $name =  __( 'passive', 'advanced-ads-pro' );
            $info .= '<br />##advanced_ads_passive_cb_debug##'
            . sprintf( '<div class="advads-passive-cb-debug" style="display:none;" data-displayed="%s" data-hidden="%s"></div>',
                __( 'The ad is displayed on the page', 'advanced-ads-pro' ),
                __( 'The ad is not displayed on the page', 'advanced-ads-pro' )
            );
        } else {
            $name = _x( 'off', 'setting label', 'advanced-ads-pro' );
        }

        $content[] = sprintf( '%s <strong>%s</strong><br />%s', _x( 'Cache-busting:', 'placement admin label', 'advanced-ads-pro' ), $name, $info );


        return $content;
    }

    /**
     * Add placement to current ads.
     *
     * @param string $id Placement id.
     */
    private function add_placement_to_current_ads( $id ) {
        $placements = Advanced_Ads::get_ad_placements_array();
        $name = ! empty( $placements[ $id ]['name'] ) ? $placements[ $id ]['name'] : $id;
        Advanced_Ads::get_instance()->current_ads[] = array('type' => 'placement', 'id' => $id, 'title' => $name );
    }

}

