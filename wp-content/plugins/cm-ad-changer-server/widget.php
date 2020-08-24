<?php

/**
 * CM Ad Changer
 *
 * @author CreativeMinds (http://ad-changer.cminds.com)
 * @copyright Copyright (c) 2013, CreativeMinds
 */
class CMACWidget extends WP_Widget {

	var $shortcode_name	 = 'cm_ad_changer';
	static $widget_displayed = false;

	/**
	 * The widget constructor. Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary scripts and styles.
	 *
	 * @version 1.0
	 * @since 1.0
	 */
	function __construct() {
		$widget_opts = array(
			'title'				 => 'CM Ad Changer',
			'description'		 => 'Display CM Ad Changer banner',
			'classname'			 => 'CMAC_AdChangerWidget',
			'campaign_id'		 => '0',
			'linked_banner'		 => '1',
			'css_class'			 => '',
			'custom_css'		 => '',
			'wrapper'			 => '0',
			'allow_inject_js'	 => '0',
			'allow_inject_html'	 => '1',
			'height'			 => null,
			'width'				 => null,
			'no_responsive'		 => '1',
		);
		parent::__construct( 'cmac_adchangerwidget', 'CM Ad Changer', $widget_opts );
	}

	function widget( $args, $instance ) {
		extract( $args );

		echo $args[ 'before_widget' ];
		echo '<div id="CMACWidget"';

		if ( isset( $instance[ 'css_class' ] ) && !empty( $instance[ 'css_class' ] ) && (!isset( $instance[ 'wrapper' ] ) || $instance[ 'wrapper' ] == '0') ) {
			echo ' class="' . $instance[ 'css_class' ] . '"';
		}

		echo '>';

		if ( !empty( $instance[ 'title' ] ) ) {
			if ( isset( $instance[ 'title_css_class' ] ) && !empty( $instance[ 'title_css_class' ] ) ) {
				if ( strpos( $before_title, 'class="' ) !== false ) {
					$before_title = str_replace( 'class="', 'class="' . $instance[ 'title_css_class' ] . ' ', $before_title );
				} else {
					$before_title = str_replace( '>', 'class="' . $instance[ 'title_css_class' ] . '">', $before_title );
				}
			}
			echo $before_title . (isset( $instance[ 'title' ] ) && !empty( $instance[ 'title' ] ) ? $instance[ 'title' ] : '') . $after_title;
		}

		if ( isset( $instance[ 'campaign_id' ] ) ) {
			$shortcode = '[cm_ad_changer campaign_id=' . $instance[ 'campaign_id' ];
			if ( isset( $instance[ 'linked_banner' ] ) ) {
				$shortcode .= ' linked_banner=' . $instance[ 'linked_banner' ];
			}

			if ( isset( $instance[ 'css_class' ] ) && !empty( $instance[ 'css_class' ] ) ) {
				$shortcode .= ' class="' . $instance[ 'css_class' ] . '"';
			}

			if ( isset( $instance[ 'wrapper' ] ) ) {
				$shortcode .= ' wrapper=' . $instance[ 'wrapper' ];
			}

			if ( isset( $instance[ 'no_responsive' ] ) ) {
				$shortcode .= ' no_responsive=' . $instance[ 'no_responsive' ];
			}

			if ( isset( $instance[ 'custom_css' ] ) ) {
				$shortcode .= ' custom_css="' . $instance[ 'custom_css' ] . '"';
			}

			if ( isset( $instance[ 'allow_inject_js' ] ) ) {
				$shortcode .= ' allow_inject_js="' . $instance[ 'allow_inject_js' ] . '"';
			}

			if ( isset( $instance[ 'allow_inject_html' ] ) ) {
				$shortcode .= ' allow_inject_html="' . $instance[ 'allow_inject_html' ] . '"';
			}

			if ( isset( $instance[ 'height' ] ) ) {
				$shortcode .= ' height="' . $instance[ 'height' ] . '"';
			}

			if ( isset( $instance[ 'width' ] ) ) {
				$shortcode .= ' width="' . $instance[ 'width' ] . '"';
			}

			$shortcode .=']';

			echo do_shortcode( $shortcode );
		}
		echo '</div>';
		echo $args[ 'after_widget' ];
		self::$widget_displayed = true;
	}

	function update( $new_instance, $old_instance ) {
		$instance = array();

		if ( !is_null( $new_instance[ 'title' ] ) ) {
			$instance[ 'title' ] = $new_instance[ 'title' ];
		}
		if ( !is_null( $new_instance[ 'campaign_id' ] ) ) {
			$instance[ 'campaign_id' ] = $new_instance[ 'campaign_id' ];
		}


		if ( !is_null( $new_instance[ 'css_class' ] ) ) {
			$instance[ 'css_class' ] = $new_instance[ 'css_class' ];
		}

		if ( !is_null( $new_instance[ 'title_css_class' ] ) ) {
			$instance[ 'title_css_class' ] = $new_instance[ 'title_css_class' ];
		}

		if ( !is_null( $new_instance[ 'custom_css' ] ) ) {
			$instance[ 'custom_css' ] = $new_instance[ 'custom_css' ];
		}

		$instance[ 'allow_inject_js' ] = isset( $new_instance[ 'allow_inject_js' ] ) ? '1' : '0';

		$instance[ 'allow_inject_html' ] = isset( $new_instance[ 'allow_inject_html' ] ) ? '1' : '0';

		$instance[ 'height' ] = $new_instance[ 'height' ];

		$instance[ 'width' ] = $new_instance[ 'width' ];

		$instance[ 'wrapper' ] = isset( $new_instance[ 'wrapper' ] ) ? '1' : '0';

		$instance[ 'linked_banner' ] = isset( $new_instance[ 'linked_banner' ] ) ? '1' : '0';

		$instance[ 'no_responsive' ] = isset( $new_instance[ 'no_responsive' ] ) ? '1' : '0';

		return $instance;
	}

	/**
	 * Generates the administration form for the widget.
	 * @param  Array $instance The array of keys and values for the widget.
	 *
	 * @version 1.0
	 * @since 1.0
	 */
	function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = 'CM Ad Changer';
		}
		?>
		<table cellpadding=2>
			<?php
			echo '<tr><td width="45%"><label for="' . $this->get_field_id( 'title' ) . '">Title</label></td>';
			echo '<td><input type="text" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" value="' . (isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : '') . '" size=13  /></td></tr>';

			echo '<tr><td><label for="' . $this->get_field_id( 'campaign_id' ) . '">Campaign ID</label></td>';
			echo '<td><input type="text" id="' . $this->get_field_id( 'campaign_id' ) . '" name="' . $this->get_field_name( 'campaign_id' ) . '" value="' . (isset( $instance[ 'campaign_id' ] ) ? $instance[ 'campaign_id' ] : '') . '" size=4  /></td></tr>';

			echo '<tr><td width="80px"><label for="' . $this->get_field_id( 'linked_banner' ) . '">Linked Banner</label></td>';
			echo '<td><input type="checkbox" id="' . $this->get_field_id( 'linked_banner' ) . '" name="' . $this->get_field_name( 'linked_banner' ) . '" ' . (isset( $instance[ 'linked_banner' ] ) && $instance[ 'linked_banner' ] == '0' ? '' : 'checked=checked') . ' value="1"  /></td></tr>';

			echo '<tr><td><label for="' . $this->get_field_id( 'css_class' ) . '">Class</label></td>';
			echo '<td><input type="text" id="' . $this->get_field_id( 'css_class' ) . '" name="' . $this->get_field_name( 'css_class' ) . '" value="' . (isset( $instance[ 'css_class' ] ) ? $instance[ 'css_class' ] : '') . '" size=13  /></td></tr>';

			echo '<tr><td><label for="' . $this->get_field_id( 'title_css_class' ) . '">Title Class</label></td>';
			echo '<td><input type="text" id="' . $this->get_field_id( 'title_css_class' ) . '" name="' . $this->get_field_name( 'title_css_class' ) . '" value="' . (isset( $instance[ 'title_css_class' ] ) ? $instance[ 'title_css_class' ] : '') . '" size=13  /></td></tr>';

			echo '<tr><td><label for="' . $this->get_field_id( 'custom_css' ) . '">Custom CSS</label></td>';
			echo '<td><textarea id="' . $this->get_field_id( 'custom_css' ) . '" name="' . $this->get_field_name( 'custom_css' ) . '" rows="3" cols="26" >' . (isset( $instance[ 'custom_css' ] ) ? $instance[ 'custom_css' ] : '') . '</textarea></td></tr>';

			echo '<tr><td width="80px"><label for="' . $this->get_field_id( 'allow_inject_js' ) . '">Allow Inject JS</label></td>';
			echo '<td><input type="checkbox" id="' . $this->get_field_id( 'allow_inject_js' ) . '" name="' . $this->get_field_name( 'allow_inject_js' ) . '" ' . (isset( $instance[ 'allow_inject_js' ] ) && $instance[ 'allow_inject_js' ] == '0' ? '' : 'checked=checked') . ' value="1"  /></td></tr>';

			echo '<tr><td width="80px"><label for="' . $this->get_field_id( 'allow_inject_html' ) . '">Allow Inject HTML</label></td>';
			echo '<td><input type="checkbox" id="' . $this->get_field_id( 'allow_inject_html' ) . '" name="' . $this->get_field_name( 'allow_inject_html' ) . '" ' . (isset( $instance[ 'allow_inject_html' ] ) && $instance[ 'allow_inject_html' ] == '0' ? '' : 'checked=checked') . ' value="1"  /></td></tr>';

			echo '<tr><td><label for="' . $this->get_field_id( 'width' ) . '">Banner Width</label></td>';
			echo '<td><input type="text" id="' . $this->get_field_id( 'width' ) . '" name="' . $this->get_field_name( 'width' ) . '" value="' . (isset( $instance[ 'width' ] ) ? $instance[ 'width' ] : '') . '" size=13  /></td></tr>';

			echo '<tr><td><label for="' . $this->get_field_id( 'height' ) . '">Banner Height</label></td>';
			echo '<td><input type="text" id="' . $this->get_field_id( 'height' ) . '" name="' . $this->get_field_name( 'height' ) . '" value="' . (isset( $instance[ 'height' ] ) ? $instance[ 'height' ] : '') . '" size=13  /></td></tr>';

			echo '<tr><td><label for="' . $this->get_field_id( 'wrapper' ) . '">Wrapper</label></td>';
			echo '<td><input type="checkbox" id="' . $this->get_field_id( 'wrapper' ) . '" name="' . $this->get_field_name( 'wrapper' ) . '" ' . (isset( $instance[ 'wrapper' ] ) && $instance[ 'wrapper' ] == '1' ? 'checked=checked' : '') . ' value="1"  /></td></tr>';

			echo '<tr><td><label for="' . $this->get_field_id( 'no_responsive' ) . '">Not responsive</label></td>';
			echo '<td><input type="checkbox" id="' . $this->get_field_id( 'no_responsive' ) . '" name="' . $this->get_field_name( 'no_responsive' ) . '" ' . (isset( $instance[ 'no_responsive' ] ) && $instance[ 'no_responsive' ] == '0' ? '' : 'checked=checked') . ' value="1"  /></td></tr>';
			?>
		</table>
		<?php
	}

}

// registering widget
function cmac_register_widget() {
	register_widget( 'CMACWidget' );
}

add_action( 'widgets_init', 'cmac_register_widget' );
