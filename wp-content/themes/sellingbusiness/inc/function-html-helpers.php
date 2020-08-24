<?php

function getPageTitleByLink($url) {
	$site_url = get_site_url();
	if($url==$site_url.'/')
		return 'Home';
	else
		return get_the_title(url_to_postid($url));
}

function the_breadcrumbs() {
    global $post;
    echo '<ul class="breadcrumbs">';
    if (!is_home() && !is_front_page() || is_paged()) {
        echo '<li><a href="';
        echo get_option('home');
        echo '">';
        echo 'Home';
        echo '</a></li><li class="separator">&raquo;</li>';
        if (is_category() || is_single()) {
            //echo '<li><a href="/blog">Blog</a></li>';
            the_category(' </li><li class="separator">&raquo;</li><li> ');
            if (is_single()) {
                echo '</li><li class="separator">&raquo;</li>';
                echo '</li><li>';
                the_title();
            }
        } 
		elseif (is_day()) {echo"<li><a href='/blog'>Blog</a></li><li class='separator'>&raquo;</li><li>"; the_time('F jS, Y'); echo'</li>';}
		elseif (is_month()) {echo"<li><a href='/blog'>Blog</a></li><li class='separator'>&raquo;</li><li>"; the_time('F, Y'); echo'</li>';}
		elseif (is_year()) {echo"<li><a href='/blog'>Blog</a></li><li class='separator'>&raquo;</li><li>"; the_time('Y'); echo'</li>';}
		elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {echo "<li>Blog Archives"; echo'</li>';}
		
		elseif (is_page()) {
            if($post->post_parent){
                $anc = get_post_ancestors( $post->ID );
                $title = get_the_title();
				$output = '';
				$anc = array_reverse($anc);
                foreach ( $anc as $ancestor ) {
                    $output .= '<li><a href="'.get_permalink($ancestor).'" title="'.get_the_title($ancestor).'">'.get_the_title($ancestor).'</a></li> <li class="separator">&raquo;</li>';
                }
                echo $output;
                echo '<li title="'.$title.'"> '.$title.'</li>';
            } else {
                echo '<li>'.get_the_title().'</li>';
            }
        }
    }
    elseif (is_tag()) {single_tag_title();}
    elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {echo "<li>Blog Archives"; echo'</li>';}
	elseif ($post->post_type=='post') {
		echo '<li><a href="'.get_option('home').'">Home</a></li><li class="separator">></li>';
		echo "<li>Blog"; echo'</li>';
	}
    echo '</ul>';
}

function getPageDepth() {
    // gets the depth of the current page
	global $wp_query;
	$object = $wp_query->get_queried_object();
	$parent_id  = $object->post_parent;
	$depth = 0;
	while ($parent_id > 0) {
		   $page = get_page($parent_id);
		   $parent_id = $page->post_parent;
		   $depth++;
	}
	return $depth;
}


function get_root_parent(){
  global $post;
  if(isset($post->ID)){
	  $page = array($post->ID);
	  $page_ancestors = get_ancestors($post->ID, 'page');
	  $pages = array_merge($page, $page_ancestors);
	  return basename(get_permalink(end($pages)));
  }
}

function trimCharacters($text, $length = false, $after = '...') { 
	if(strlen($text) > $length){
		return trim(substr($text, 0, $length)) . $after;
	}else
		return $text;
}
function custom_strip_all_tags($string, $remove_breaks=true) {
    $string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
    $string = strip_tags($string);
 
//    if ( $remove_breaks )
  //      $string = preg_replace('/[\r\n\t ]+/', ' ', $string);
 
    return trim( $string );
}

// From this plugin - https://wordpress.org/plugins/voce-cached-nav/developers/
if ( !class_exists( 'A_Cached_Nav' ) ) {

	class A_Cached_Nav {

		const MENUPREFIX = 'wp_nav_menu-';
		const ITEMSPREFIX = 'wp_nav_items-';
		const MENUIDS = 'wp_nav_menus';

		/**
		 * Set the action hooks to update the cache
		 * @method init
		 * @constructor
		 */
		public static function init() {
			add_action( 'wp_create_nav_menu', array( __CLASS__, 'action_wp_update_nav_menu' ), 100 );
			add_action( 'wp_update_nav_menu', array( __CLASS__, 'action_wp_update_nav_menu' ) );
			add_action( 'wp_delete_nav_menu', array( __CLASS__, 'action_wp_delete_nav_menu' ), 100 );
			add_action( 'save_post', array( __CLASS__, 'action_save_post' ) );
		}

		/**
		 * @method action_wp_update_nav_menu
		 * @param Integer $menu_id
		 */
		public static function action_wp_update_nav_menu( $menu_id ) {
			self::delete_menu_objects_cache( $menu_id );
		}

		/**
		 * @method action_wp_delete_nav_menu
		 * @param Integer $menu_id
		 */
		public static function action_wp_delete_nav_menu( $menu_id ) {
			self::update_menu_ids_cache( $menu_id );
			self::delete_menu_objects_cache( $menu_id );
		}

		/**
		 * Clear the menu caches because the post title/permalink/etc could change.
		 * @method action_save_post
		 */
		public static function action_save_post() {
			// Passing 0 will ensure that all caches are deleted.
			self::get_nav_menus();
			self::delete_menu_objects_cache( 0 );
		}

		public static function get_nav_menus() {
			$menus = get_transient( self::MENUIDS );
			if ( !is_array( $menus ) ) {
				$menus = wp_get_nav_menus();
				foreach ( $menus as $menu ) {
					self::update_menu_ids_cache( $menu->term_id );
				}
			}
			return $menus;
		}

		/**
		 * @method delete_menu_objects_cache
		 * @param Integer $menu_id
		 *
		 * @return type
		 */
		public static function delete_menu_objects_cache( $menu_id ) {
			//if given an existing menu_id delete just that menu
			if ( term_exists( (int) $menu_id, 'nav_menu' ) ) {
				return delete_transient( self::ITEMSPREFIX . $menu_id );
			} else { //delete all cached menus recursively
				$all_cached_menus = get_transient( self::MENUIDS );
				if ( is_array( $all_cached_menus ) ) {
					foreach ( $all_cached_menus as $menu_id ) {
						self::delete_menu_objects_cache( $menu_id );
					}
				}
			}
		}

		/**
		 * @method update_menu_ids_cache
		 * @param Integer $menu_id
		 */
		public static function update_menu_ids_cache( $menu_id ) {
			$cache = get_transient( self::MENUIDS );
			// If there is already a cached array
			if ( is_array( $cache ) ) {
				// If the menu ID is not already in cache and is a valid menu
				if ( !in_array( $menu_id, $cache ) && term_exists( (int) $menu_id, 'nav_menu' ) ) {
					$cache = array_merge( $cache, array( $menu_id ) );
				}
				foreach ( $cache as $key => $cached_id ) {
					// Remove the menu ID if it's invalid
					if ( !term_exists( (int) $cached_id, 'nav_menu' ) ) {
						unset( $cache[ $key ] );
					}
				}
				$data = $cache;
				// If this is executing for the first time
			} else {
				$data = ( term_exists( (int) $menu_id, 'nav_menu' ) ) ? array( $menu_id ) : false;
			}

			if( $data ){
				set_transient( self::MENUIDS, $data );
			} else {
				delete_transient( self::MENUIDS );
			}
		}

		/**
		 * @method get_nav_menu_object
		 * @param Object $args
		 *
		 * @return type
		 */
		public static function get_nav_menu_object( $args ) {
			$menu_lookup = $args->menu;
			$menu = get_transient( self::MENUPREFIX . $menu_lookup );
			if ( empty( $menu ) ) {
				$menu = wp_get_nav_menu_object( $args->menu );
				set_transient( self::MENUPREFIX . $menu_lookup, $menu );
			}

			// Get the nav menu based on the theme_location
			if ( !$menu && $args->theme_location && ( $locations = get_nav_menu_locations() ) && isset( $locations[ $args->theme_location ] ) ) {
				$menu_lookup = $locations[ $args->theme_location ];
				$menu = get_transient( self::MENUPREFIX . $menu_lookup );
				if ( empty( $menu ) ) {
					$menu = wp_get_nav_menu_object( $locations[ $args->theme_location ] );
					set_transient( self::MENUPREFIX . $menu_lookup, $menu );
				}
			}

			// get the first menu that has items if we still can't find a menu
			if ( !$menu && !$args->theme_location ) {
				$menus = self::get_nav_menus();
				foreach ( $menus as $menu_maybe ) {
					if ( $menu_items = self::get_nav_menu_items( $menu_maybe->term_id, array( 'update_post_term_cache' => false ) ) ) {
						$menu = $menu_maybe;
						break;
					}
				}
			}

			return $menu;
		}

		/**
		 * @method get_nav_menu_items
		 * @param Integer $term_id
		 *
		 * @return type
		 */
		public static function get_nav_menu_items( $term_id, $args ) {
			if ( $cache = get_transient( self::ITEMSPREFIX . $term_id ) ) {
				$items = $cache;
			} else {
				$items = wp_get_nav_menu_items( $term_id, $args );
				set_transient( self::ITEMSPREFIX . $term_id, $items );
			}
			return $items;
		}

		/**
		 * @method menu
		 * @staticvar array $menu_id_slugs
		 *
		 * @param     {Array} $args
		 *
		 * @return boolean
		 */
		public static function menu( $args = array() ) {
			static $menu_id_slugs = array();

			$defaults = array(
				'menu'            => '',
				'container'       => 'div',
				'container_class' => '',
				'container_id'    => '',
				'menu_class'      => 'menu',
				'menu_id'         => '',
				'echo'            => true,
				'fallback_cb'     => 'wp_page_menu',
				'before'          => '',
				'after'           => '',
				'link_before'     => '',
				'link_after'      => '',
				'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
				'depth'           => 0,
				'walker'          => '',
				'theme_location'  => ''
			);

			$args = wp_parse_args( $args, $defaults );
			$args = apply_filters( 'wp_nav_menu_args', $args );
			$args = (object) $args;

			// Get the nav menu based on the requested menu
			// move get menu part to self::get_nav_menu_object function
			// to manage cache
			$menu = self::get_nav_menu_object( $args );

			// If the menu exists, get its items.
			if ( $menu && !is_wp_error( $menu ) && !isset( $menu_items ) ) //replace wp_get_nav_menu_items with self::get_nav_menu_items to manage cache
			{
				$menu_items = self::get_nav_menu_items( $menu->term_id, array( 'update_post_term_cache' => false ) );
			}

			/*
			   * If no menu was found:
			   *  - Fall back (if one was specified), or bail.
			   *
			   * If no menu items were found:
			   *  - Fall back, but only if no theme location was specified.
			   *  - Otherwise, bail.
			   */
			if ( ( !$menu || is_wp_error( $menu ) || ( isset( $menu_items ) && empty( $menu_items ) && !$args->theme_location ) ) && $args->fallback_cb && is_callable( $args->fallback_cb ) ) return call_user_func( $args->fallback_cb, (array) $args );

			if ( !$menu || is_wp_error( $menu ) ) return false;

			$nav_menu = $items = '';

			$show_container = false;
			if ( $args->container ) {
				$allowed_tags = apply_filters( 'wp_nav_menu_container_allowedtags', array( 'div', 'nav' ) );
				if ( in_array( $args->container, $allowed_tags ) ) {
					$show_container = true;
					$class = $args->container_class ? ' class="' . esc_attr( $args->container_class ) . '"' : ' class="menu-' . $menu->slug . '-container"';
					$id = $args->container_id ? ' id="' . esc_attr( $args->container_id ) . '"' : '';
					$nav_menu .= '<' . $args->container . $id . $class . '>';
				}
			}

			// Set up the $menu_item variables
			_wp_menu_item_classes_by_context( $menu_items );

			$sorted_menu_items = $menu_items_with_children = array();
			foreach ( (array) $menu_items as $key => $menu_item ) {
				$sorted_menu_items[ $menu_item->menu_order ] = $menu_item;
				if ( $menu_item->menu_item_parent )
					$menu_items_with_children[ $menu_item->menu_item_parent ] = true;
			}

			// Add the menu-item-has-children class where applicable
			if ( !empty( $menu_items_with_children ) ) {
				foreach ( $sorted_menu_items as &$menu_item ) {
					if ( isset( $menu_items_with_children[ $menu_item->ID ] ) )
						$menu_item->classes[] = 'menu-item-has-children';
				}
			}

			unset( $menu_items );

			$sorted_menu_items = apply_filters( 'wp_nav_menu_objects', $sorted_menu_items, $args );

			$items .= walk_nav_menu_tree( $sorted_menu_items, $args->depth, $args );
			unset( $sorted_menu_items );

			// Attributes
			if ( !empty( $args->menu_id ) ) {
				$wrap_id = $args->menu_id;
			} else {
				$wrap_id = 'menu-' . $menu->slug;
				while ( in_array( $wrap_id, $menu_id_slugs ) ) {
					if ( preg_match( '#-(\d+)$#', $wrap_id, $matches ) ) $wrap_id = preg_replace( '#-(\d+)$#', '-' . ++$matches[ 1 ], $wrap_id ); else
						$wrap_id = $wrap_id . '-1';
				}
			}
			$menu_id_slugs[ ] = $wrap_id;

			$wrap_class = $args->menu_class ? $args->menu_class : '';

			// Allow plugins to hook into the menu to add their own <li>'s
			$items = apply_filters( 'wp_nav_menu_items', $items, $args );
			$items = apply_filters( "wp_nav_menu_{$menu->slug}_items", $items, $args );

			// Don't print any markup if there are no items at this point.
			if ( empty( $items ) ) return false;

			$nav_menu .= sprintf( $args->items_wrap, esc_attr( $wrap_id ), esc_attr( $wrap_class ), $items );
			unset( $items );

			if ( $show_container ) $nav_menu .= '</' . $args->container . '>';

			$nav_menu = apply_filters( 'wp_nav_menu', $nav_menu, $args );

			if ( $args->echo ) echo $nav_menu; else
				return $nav_menu;
		}

	}

	A_Cached_Nav::init();

	if ( !function_exists( 'wp_cached_nav_menu' ) ) {
		function wp_cached_nav_menu( $args ) {
			A_Cached_Nav_menu( $args );
		}
	}

	function A_Cached_Nav_menu( $args ) {
		return A_Cached_Nav::menu( $args );

	}

}

/**
 * Add custom image sizes attribute to enhance responsive image functionality
 * for post thumbnails
 *
 * @since Twenty Sixteen 1.0
 *
 * @param array $attr Attributes for the image markup.
 * @param int   $attachment Image attachment ID.
 * @param array $size Registered image size or flat array of height and width dimensions.
 * @return string A source size value for use in a post thumbnail 'sizes' attribute.
 */
function post_thumbnail_sizes_attr( $attr, $attachment, $size ) {
	if ( 'post-thumbnail' === $size ) {
		is_active_sidebar( 'sidebar-1' ) && $attr['sizes'] = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 984px) 60vw, (max-width: 1362px) 62vw, 840px';
		! is_active_sidebar( 'sidebar-1' ) && $attr['sizes'] = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 1362px) 88vw, 1200px';
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'post_thumbnail_sizes_attr', 10 , 3 );


/*
*	debug function
*/
function pr($arr) {
	echo '<pre>';
	print_r($arr);
	echo '</pre>';
}

function getArrayValuesByKey($ary, $key){
	if(is_array($ary)){
		$ret = array();
		foreach($ary as $arr){
			if(isset($arr[$key]))
				$ret[] = $arr[$key];
		}
		return $ret;
	}
}

function array_chunk_fixed($input, $num, $preserve_keys = FALSE) {
    $count = count($input) ;
    if($count)
        $input = array_chunk($input, ceil($count/$num), $preserve_keys) ;
    $input = array_pad($input, $num, array()) ;
    return $input ;
}


/************************************/

/************************************/
// GET THE CURRENT URL OF THE PAGE / START
/************************************/

function curPageURL() {
	
	$pageURL = 'http';
		if ($_SERVER["HTTPS"] == "on") {
				$pageURL .= "s";
		}
	$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		}else{
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
	return $pageURL;
		
}

/************************************/
// GET THE CURRENT URL OF THE PAGE / END
/************************************/

/* custom paginator */
function pagination($pages = '', $range = 2) {
	$showitems = ($range * 2)+1;

	global $paged;

	if(empty($paged)) $paged = 1;
	if($pages == '') {
		global $wp_query;
		$pages = $wp_query->max_num_pages;

		if(!$pages) {
			$pages = 1;
		}
	}

	if(1 != $pages) {
		echo "<nav class='pagination'>";
		//if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'>&laquo;</a>";
		//if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."' class='prev'>prev</a>";
		if($paged > 1) echo "<a href='".get_pagenum_link($paged - 1)."' class='ctrl prev'>&laquo; Prev</a>";
		
		for ($i=1; $i <= $pages; $i++) {
			if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems )) {
				echo ($paged == $i)? "<span class='num current'>".$i."</span>":"<a href='".get_pagenum_link($i)."' class='num' >".$i."</a>";
				//echo ($i>=$pages)?'':'|';
			}
		}
		
		if ($paged < $pages) echo "<a href='".get_pagenum_link($paged + 1)."' class='ctrl next'>Next &raquo;</a>";  
		//if ($paged < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($paged + 1)."' class='next'>next</a>";  
		//if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."'>&raquo;</a>";
		echo "</nav>\n";
	}
}



/**
 * Displays the term list of the current post
 */
function set_resource_category_tags(){
	global $post;
	$term_list = wp_get_post_terms($post->ID, 'resources_categories', array("fields" => "all"));
	if(!empty($term_list)){
		$tmp = array();
		foreach($term_list as $term) {
			$tmp[] = '<a href="'. get_term_link($term) .'"><em>'. $term->name .'</em></a>';
		}
		echo '<p><strong>RESOURCE CATEGORY TAGS:</strong><br />'. implode(', ', $tmp) .'</p>';
	}
}
/**
 * Displays the assigned term(level & knowledge area) of the current post
 */
function resource_term_headings(){
	global $post;	
	$levels = wp_get_post_terms($post->ID, 'level', array("fields" => "names"));	
	if(!empty($levels)){
		echo '<div class="catTable"><span class="tKey">Year Level:</span><span class="tVal">';
		echo implode('<br />', $levels);
		echo '</span></div><span class="clearfix"></span>';
	}
	
	//$kwdge = get_term_by('slug', 'knowledge_areas', 'resources_categories');
	//$areas = wp_get_post_terms($post->ID, 'resources_categories', array("fields" => "names",'child', 'child_of' => $kwdge->term_id));
	$areas = wp_get_post_terms($post->ID, 'knowledge_area', array("fields" => "names"));	
	if(!empty($areas)){
		echo '<div class="catTable"><span class="tKey">Knowledge Area:</span><span class="tVal">';
		echo implode('<br />', $areas);
		echo '</span></div><span class="clearfix"></span>';
	}
}

/**
 * Displays videos of the current post
 */
function resource_video_headings(){
	$vids = get_field('videos');
	if( has_term('ebooks','resources_categories') && $vids) {
		foreach($vids as $vid){
			echo '<section class="videos">'. $vid['video'] .'</section>';
		}
	}
}

/* Pull apart OEmbed video link to get thumbnails out*/
	function get_video_thumbnail_uri( $video_uri ) {
	
		$thumbnail_uri = '';
		
		// determine the type of video and the video id
		$video = parse_video_uri( $video_uri );		
		
		// get youtube thumbnail
		if ( $video['type'] == 'youtube' )
			$thumbnail_uri = 'http://img.youtube.com/vi/' . $video['id'] . '/hqdefault.jpg';
		
		// get vimeo thumbnail
		if( $video['type'] == 'vimeo' )
			$thumbnail_uri = get_vimeo_thumbnail_uri( $video['id'] );
		// get wistia thumbnail
		if( $video['type'] == 'wistia' )
			$thumbnail_uri = get_wistia_thumbnail_uri( $video_uri );
		// get default/placeholder thumbnail
		if( empty( $thumbnail_uri ) || is_wp_error( $thumbnail_uri ) )
			$thumbnail_uri = ''; 
		
		//return thumbnail uri
		return $thumbnail_uri;
		
	}
	
	
	/* Parse the video uri/url to determine the video type/source and the video id */
	function parse_video_uri( $url ) {
		
		// Parse the url 
		$parse = parse_url( $url );
		
		// Set blank variables
		$video_type = '';
		$video_id = '';
		
		// Url is http://youtu.be/xxxx
		if ( $parse['host'] == 'youtu.be' ) {
		
			$video_type = 'youtube';
			
			$video_id = ltrim( $parse['path'],'/' );
			
		}
		
		// Url is http://www.youtube.com/watch?v=xxxx 
		// or http://www.youtube.com/watch?feature=player_embedded&v=xxx
		// or http://www.youtube.com/embed/xxxx
		if ( ( $parse['host'] == 'youtube.com' ) || ( $parse['host'] == 'www.youtube.com' ) ) {
		
			$video_type = 'youtube';
			
			parse_str( $parse['query'] );
			
			$video_id = $v;	
			
			if ( !empty( $feature ) )
				$video_id = end( explode( 'v=', $parse['query'] ) );
				
			if ( strpos( $parse['path'], 'embed' ) == 1 )
				$video_id = end( explode( '/', $parse['path'] ) );
			
		}
		
		// Url is http://www.vimeo.com
		if ( ( $parse['host'] == 'vimeo.com' ) || ( $parse['host'] == 'www.vimeo.com' ) ) {
		
			$video_type = 'vimeo';
			
			$video_id = ltrim( $parse['path'],'/' );	
						
		}
		$host_names = explode(".", $parse['host'] );
		$rebuild = ( ! empty( $host_names[1] ) ? $host_names[1] : '') . '.' . ( ! empty($host_names[2] ) ? $host_names[2] : '');
		// Url is an oembed url wistia.com
		if ( ( $rebuild == 'wistia.com' ) || ( $rebuild == 'wi.st.com' ) ) {
		
			$video_type = 'wistia';
				
			if ( strpos( $parse['path'], 'medias' ) == 1 )
					$video_id = end( explode( '/', $parse['path'] ) );
		
		}
		
		// If recognised type return video array
		if ( !empty( $video_type ) ) {
		
			$video_array = array(
				'type' => $video_type,
				'id' => $video_id
			);
		
			return $video_array;
			
		} else {
		
			return false;
			
		}
		
	}
	
	
	/* Takes a Vimeo video/clip ID and calls the Vimeo API v2 to get the large thumbnail URL.*/
	function get_vimeo_thumbnail_uri( $clip_id ) {
		$vimeo_api_uri = 'http://vimeo.com/api/v2/video/' . $clip_id . '.php';
		$vimeo_response = wp_remote_get( $vimeo_api_uri );
		if( is_wp_error( $vimeo_response ) ) {
			return $vimeo_response;
		} else {
			$vimeo_response = unserialize( $vimeo_response['body'] );
			return $vimeo_response[0]['thumbnail_large'];
		}
		
	}
	
	/* Takes a wistia oembed url and gets the video thumbnail url. */
	function get_wistia_thumbnail_uri( $video_uri ) {
		if ( empty($video_uri) )
			return false;
		$wistia_api_uri = 'http://fast.wistia.com/oembed?url=' . $video_uri;
		$wistia_response = wp_remote_get( $wistia_api_uri );
		if( is_wp_error( $wistia_response ) ) {
			return $wistia_response;
		} else {
			$wistia_response = json_decode( $wistia_response['body'], true );
			return $wistia_response['thumbnail_url'];
		}
	}
	
	function setYoutubeVideo($acffield){
		$ret = '';
		if($acffield){
			$vid = parse_video_uri($acffield);
			$thumb = get_video_thumbnail_uri($acffield);
			$ret = array('url'=>$vid['id'], 'thumb'=>$thumb);
		}
		return $ret;
	}


add_shortcode( 'hidethis', 'hide_func' );
function hide_func( $atts, $content = null ) {
	$a = shortcode_atts( array(
		'readmore_show' => 'read more',
		'readmore_hide' => 'read less',
	), $atts );

	return '<div class="hidetext">' . $content . '</div><p><a class="readmore" href="javascript:void(0);"><span class="shw">' . esc_attr($a['readmore_show']) . '</span><span class="hdn">' . esc_attr($a['readmore_hide']) . '</span></a></p>';
}

function fjarrett_get_attachment_id_by_url( $url ) {

    // Split the $url into two parts with the wp-content directory as the separator
    $parsed_url  = explode( parse_url( WP_CONTENT_URL, PHP_URL_PATH ), $url );

    // Get the host of the current site and the host of the $url, ignoring www
    $this_host = str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
    $file_host = str_ireplace( 'www.', '', parse_url( $url, PHP_URL_HOST ) );

    // Return nothing if there aren't any $url parts or if the current host and $url host do not match
    if ( ! isset( $parsed_url[1] ) || empty( $parsed_url[1] ) || ( $this_host != $file_host ) ) {
        return;
    }

    // Now we're going to quickly search the DB for any attachment GUID with a partial path match

    // Example: /uploads/2013/05/test-image.jpg
    global $wpdb;
    $attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}posts WHERE guid RLIKE %s;", $parsed_url[1] ) );

    // Returns null if no attachment is found
    return $attachment[0];
}

add_action("wp_ajax_more_post", "more_post");
add_action( 'wp_ajax_nopriv_more_post', 'more_post' );
function more_post() {
   if ( !wp_verify_nonce( $_REQUEST['nonce'], "more_post_nonce")) {
      exit();
   }
   $page = ($_REQUEST['page'])?$_REQUEST['page']:2;
   
   $args = array('post_type' => 'post',
   				 'posts_per_page' => 9,
				 'orderby' => 'post_date',
				 'order' => 'DESC',
				 'post_status' => 'publish',
				 'paged' => $page );
				 
   $children = new WP_Query( $args );
	
	if($children->posts){
		$result['type'] = "success";
		//$category = get_category($catId);
		$result['post_total'] = wp_count_posts('post')->publish;
		$result['post_html'] =	setHmltPost($children->posts);
		$result['page'] = $page;
	}else{
		$result['type'] = "error";
	}
	
	
   if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
      $result = json_encode($result);
      echo $result;
   }
   else {
      header("Location: ".$_SERVER["HTTP_REFERER"]);
   }

   die();

}

function setHmltPost($posts, $limit=9){
	if(!$posts)
		return false;
	$res = '';
	$cnt = 1;
	foreach($posts as $child):
		if($cnt>$limit) break;
		$custom_fields = get_post_meta($child->ID);
		wp_reset_query();
		
		$thumbImg =  '';		
		$custom_img = get_field('image_banner', $child->ID);
		if($custom_img){
			$image_link = wp_get_attachment_image_src( fjarrett_get_attachment_id_by_url($custom_img), 'news_thumb');
				$thumbImg = $image_link[0];
			}else{
				if(has_post_thumbnail( $child->ID ))
					$thumbImg = get_the_post_thumbnail_url($child->ID, 'news_thumb');
				else
					$thumbImg = get_stylesheet_directory_uri().'/assets/img/news-thumb.gif';
		}
		
		$res .= '<a href="'.get_permalink($child->ID).'" class="col-lg-4 col-md-6 p-2 thumb-blocks"><div class="grid-item h-100">';
		
		$pDate = strtotime($child->post_date);
		$res .= '<span class="pDate">'.date('M',$pDate);
		$res .= ' <b>'.date('j',$pDate).'</b> ';
		$res .= date('Y',$pDate).'</span>';
		
      //  $res .= '<span class="postFormat '.get_post_format($child->ID).'"></span>';
        $res .= '<img src="'.$thumbImg.'" alt="'.$child->post_title.'" class="lazyload" />';
        $res .= '<div class="dsc"><h5>'.$child->post_title.'</h5>';
               $excerpt = $child->post_excerpt;									
				if (!$excerpt){
					$excerpt = $child->post_content;
					$excerpt = trimCharacters(esc_attr( strip_tags( stripslashes( $excerpt ) ) ),200);
				}
		$res .= $excerpt;
        $res .= '</div></div></a>';
		
	$cnt++; endforeach;
	return $res;
}

/**
* Returns the state for a postcode.
* eg. NSW
* 
* @author http://waww.com.au/ramblings/determine-state-from-postcode-in-australia
* @link http://en.wikipedia.org/wiki/Postcodes_in_Australia#States_and_territories
*/
function findState($postcode) {
  $ranges = array(
    'NSW' => array(
      1000, 1999,
      2000, 2599,
      2619, 2898,
      2921, 2999
    ),
    'ACT' => array(
      200, 299,
      2600, 2618,
      2900, 2920
    ),
    'VIC' => array(
      3000, 3999,
      8000, 8999
    ),
    'QLD' => array(
      4000, 4999,
      9000, 9999
    ),
    'SA' => array(
      5000, 5999
    ),
    'WA' => array(
      6000, 6797,
      6800, 6999
    ),
    'TAS' => array(
      7000, 7999
    ),
    'NT' => array(
      800, 999
    )
  );
  $exceptions = array(
    872 => 'NT',
    2540 => 'NSW',
    2611 => 'ACT',
    2620 => 'NSW',
    3500 => 'VIC',
    3585 => 'VIC',
    3586 => 'VIC',
    3644 => 'VIC',
    3707 => 'VIC',
    2899 => 'NSW',
    6798 => 'WA',
    6799 => 'WA',
    7151 => 'TAS'
  );

  $postcode = intval($postcode);
  if ( array_key_exists($postcode, $exceptions) ) {
    return $exceptions[$postcode];
  }

  foreach ($ranges as $state => $range)
  {
    $c = count($range);
    for ($i = 0; $i < $c; $i+=2) {
      $min = $range[$i];
      $max = $range[$i+1];
      if ( $postcode >= $min && $postcode <= $max ) {
        return $state;
      }
    }
  }

  return null;
}
