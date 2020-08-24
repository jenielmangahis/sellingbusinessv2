<?php

add_action( 'widgets_init', 'pt_social_icons_widget' );

function pt_social_icons_widget() {
	register_widget( 'pt_social_icons_widget' );
}


class pt_social_icons_widget extends WP_Widget {

	function __construct() {

    add_action( 'load-widgets.php', array(&$this, 'pt_widget_adds') );

	parent::__construct(
			'pt-social-icons',
			__('Web Font Social Icons Widget', 'wfsi'),
			array(  'classname' => 'pt-social-icons',
                    'description' => __('This widget displays retina ready social icons', 'wfsi'),
                    'idbase' => 'pt-social-icons'
            ),
			array( 'width' => 320, 'idbase' => 'pt-social-icons'));
	}

    function pt_widget_adds() {
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
    }

	function widget( $args, $instance ) {
		extract( $args );
    	$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
		$icons = $instance['icons'];

        echo $before_widget;
        if($title){
            echo $before_title . $title . $after_title;    
        }
        

        $bgcolor = $instance['bg_color'];

        $iconcolor = $instance['icons_color'];
        $size = $instance['size'];
        if(!empty($instance['target'])) {
            $target = $instance['target'];
        } else {
            $target = '';
        }
        $randomid = rand(3, 15); //get random id for each widget
        $custom_css = "
                .ptwsi_social-icons.ptwsi".$randomid." li a:visited,
                .ptwsi_social-icons.ptwsi".$randomid." li a{
                        background: ".$bgcolor.";
                        color:  ".$iconcolor.";
                }";
        wp_add_inline_style( 'wfsi-social-widget', $custom_css );

        do_action( 'before-wfsi-widget' );
        ?>
        
        <ul class="ptwsi_social-icons ptwsi<?php echo $randomid; ?>">
            <?php foreach ($icons as $icon => $url) { ?>
               <li><a target="<?php echo $target; ?>" class="<?php echo $icon; ?> <?php echo $size; ?>  ptwsi-social-icon" href="<?php echo $url; ?>"><i class="ptwsi-icon-<?php echo $icon; ?>"></i></a></li>
            <?php } ?>
        </ul>
		<?php
        do_action( 'after-wfsi-widget' );
        echo $after_widget;
	}

	//Update the widget
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		//Strip tags from title and name to remove HTML
		$instance['title'] = strip_tags( $new_instance['title'] );
        $instance['icons'] = $new_instance['icons'];
		$instance['size'] = $new_instance['size'];
        $instance['bg_color'] = $new_instance['bg_color'];
        $instance['icons_color'] = $new_instance['icons_color'];
        $instance['target'] = $new_instance['target'];

		return $instance;
	}

	function form( $instance ) {

		$defaults = array(
            'title' => null
        );
        if(!empty($instance['icons'])) { $icons = $instance['icons']; } else { $icons = ''; }

        $class = new PureThemes_SocialIcons;
        $iconspack = $class->getIconsArray();

		//Set up some default widget settings.
		$instance = wp_parse_args( (array) $instance, $defaults); ?>
		<div class="widget-content ptwsi">
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Widget Title:', 'wfsi'); ?></label>
                <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
            </p>
            <p id="selector">
                <label for="<?php echo $this->get_field_id('id'); ?>"><?php _e('Choose service:', 'wfsi'); ?></label>
                <select class="widefat icontype" id="<?php echo $this->get_field_id( 'icontype' ); ?>" name="<?php echo $this->get_field_name( 'icontype' ); ?>">
                    <option value="">-</option>
                <?php foreach ($iconspack as $icon => $service) { ?>
                    <option value="<?php echo $icon; ?>"><?php echo $service; ?></option>
                <?php } ?>
                </select>
            </p>

            <input type="hidden" name="temp" id="fieldname" value="<?php echo 'widget-' . $this->id_base . '[' . $this->number . '][icons][replace]'; ?>" />
            <div id="socialicons">
            <?php
                if(!empty($icons)){
                    foreach ($icons as $icon => $service) { ?>
                       <p>
                            <label><?php echo $iconspack[$icon]; ?></label>
                            <input type="text" class="" value="<?php echo $service; ?>" name="<?php echo 'widget-' . $this->id_base . '[' . $this->number . '][icons][' . $icon . ']'; ?>" />
                        </p>
                    <?php }
                } ?>

            </div>
            <p>
                <small><strong>Hint</strong> You can sort icons by drag&drop, and delete them by dragging element outside the widget!</small>
            </p>
            <p>
                <label><?php _e('Icons background' , 'wfsi') ?></label><br>
                <input type="text" data-default-color="#F2F2F2" id="<?php echo $this->get_field_id( 'bg_color' ); ?>" name="<?php echo $this->get_field_name( 'bg_color' ); ?>" value="<?php if(!empty($instance['bg_color'])) { echo esc_attr($instance['bg_color']); } else { echo "#F2F2F2";  } ?>" class="wp-color-picker" />
            </p>
            <p>
                <label><?php _e('Icons color' , 'wfsi') ?></label><br>
                <input class="wp-color-picker" data-default-color="#A0A0A0" type="text" id="<?php echo $this->get_field_id( 'icons_color' ); ?>" name="<?php echo $this->get_field_name( 'icons_color' ); ?>" value="<?php if(!empty($instance['icons_color'])) { echo esc_attr($instance['icons_color']); } else { echo "#A0A0A0"; } ?>" />
            </p>
             <p>
                <label for="<?php echo $this->get_field_id('target'); ?>"><?php _e('Links target:', 'wfsi'); ?></label>
                <select class="widefat" id="<?php echo $this->get_field_id( 'target' ); ?>" name="<?php echo $this->get_field_name( 'target' ); ?>">
                    <option value="_self" <?php selected( $instance['target'], '_self' ); ?>>_self</option>
                    <option value="_blank" <?php selected( $instance['target'], '_blank' ); ?>>_blank</option>
                    <option value="_parent" <?php selected( $instance['target'], '_parent' ); ?>>_parent</option>
                    <option value="_top" <?php selected( $instance['target'], '_top' ); ?>>_top</option>
                </select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('size'); ?>"><?php _e('Icons size:', 'wfsi'); ?></label>
                <select class="widefat" id="<?php echo $this->get_field_id( 'size' ); ?>" name="<?php echo $this->get_field_name( 'size' ); ?>">
                    <option value="" <?php selected( $instance['size'], '' ); ?>>Standard</option>
                    <option value="small" <?php selected( $instance['size'], 'small' ); ?>>Small</option>
                </select>
            </p>
		</div>
	<?php
	}
}


?>