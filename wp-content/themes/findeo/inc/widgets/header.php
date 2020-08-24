<?php

/**
 * Custom widgets for findeo theme
 *
 *
 * @package findeo
 * @since findeo 1.0
 */


add_action('widgets_init', 'findeo_load_header_widget'); // Loads widgets here
function findeo_load_header_widget() {
    register_widget('Findeo_Header');
}


class Findeo_Header extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
            'findeo_header_widget', // Base ID
            __( 'Findeo Header Widget', 'findeo' ), // Name
            array( 'description' => __( 'Use in Header Section', 'findeo' ) ) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
     
         /**
         * Filter the content of the Text widget.
         *
         * @since 2.3.0
         * @since 4.4.0 Added the `$this` parameter.
         *
         * @param string         $widget_text The widget content.
         * @param array          $instance    Array of settings for the current widget.
         * @param WP_Widget_Text $this        Current Text widget instance.
         */
   
        $first_box_title    =  $instance['first_box_title'];
        $first_box_subtitle =  $instance['first_box_subtitle'];
        $first_box_icon     =  $instance['first_box_icon'];

        $second_box_title    =  $instance['second_box_title'];
        $second_box_subtitle = $instance['second_box_subtitle'];
        $second_box_icon     = $instance['second_box_icon'];
      
        $button_submit        = $instance['button_submit'];

     
        
        ?>
        <ul class="header-widget">
            <?php
            if ( ! empty( $first_box_title ) ) { ?>
            <li>
                <i class="sl sl-icon-<?php echo esc_attr($first_box_icon); ?>"></i>
                <div class="widget-content"> 
                    <span class="title"><?php echo $first_box_title; ?></span>
                    <span class="data"><?php echo $first_box_subtitle; ?></span>
                </div>
            </li>
            <?php } 
            if ( ! empty( $second_box_title ) ) {  ?>
            <li>
                <i class="sl sl-icon-<?php echo esc_attr($second_box_icon); ?>"></i>
                <div class="widget-content">
                    <span class="title"><?php echo $second_box_title; ?></span>
                    <span class="data"><?php echo $second_box_subtitle; ?></span>
                </div>
            </li>
            <?php } 

            if ( ! empty( $button_submit )) {   ?>
                <li class="with-btn"><a href="<?php echo get_permalink( realteo_get_option( 'submit_property_page' ) ); ?>" class="button border medium"><?php esc_html_e('Submit Property', 'findeo'); ?></a></li>
            <?php   }  ?>
        </ul>
      
           
           <?php
       
    }

   /**
     * Handles updating settings for the current Text widget instance.
     *
     * @since 2.8.0
     * @access public
     *
     * @param array $new_instance New settings for this instance as input by the user via
     *                            WP_Widget::form().
     * @param array $old_instance Old settings for this instance.
     * @return array Settings to save or bool false to cancel saving.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

/*        $instance['first_box_title']    = sanitize_text_field( $new_instance['first_box_title'] );
        $instance['first_box_subtitle'] =  $new_instance['first_box_subtitle '] ;
        $instance['first_box_icon']     = sanitize_text_field( $new_instance['first_box_icon '] );        

        $instance['second_box_title']   = sanitize_text_field( $new_instance['second_box_title'] );
        $instance['second_box_subtitle'] = sanitize_text_field( $new_instance['second_box_subtitle '] );
        $instance['second_box_icon']    = sanitize_text_field( $new_instance['second_box_icon '] );

        $instance['button_text']        = sanitize_text_field( $new_instance['button_text '] );
        $instance['button_link']        = sanitize_text_field( $new_instance['button_link '] );
        */
        $instance = array();
        $instance['first_box_title']  = ( ! empty( $new_instance['first_box_title'] ) ) ? ( $new_instance['first_box_title'] ) : '';
        $instance['first_box_subtitle'] = ( ! empty( $new_instance['first_box_subtitle'] ) ) ? ( $new_instance['first_box_subtitle'] ) : '';
        $instance['first_box_icon']   = ( ! empty( $new_instance['first_box_icon'] ) ) ? ( $new_instance['first_box_icon'] ) : '';

        $instance['second_box_title']  = ( ! empty( $new_instance['second_box_title'] ) ) ? ( $new_instance['second_box_title'] ) : '';
        $instance['second_box_subtitle'] = ( ! empty( $new_instance['second_box_subtitle'] ) ) ? ( $new_instance['second_box_subtitle'] ) : '';
        $instance['second_box_icon']   = ( ! empty( $new_instance['second_box_icon'] ) ) ? ( $new_instance['second_box_icon'] ) : '';
        
        $instance['button_submit'] = ( ! empty( $new_instance['button_submit'] ) ) ? strip_tags( $new_instance['button_submit'] ) : '';
 

        return $instance;
    }

    /**
     * Outputs the Text widget settings form.
     *
     * @since 2.8.0
     * @access public
     *
     * @param array $instance Current settings.
     */
    public function form( $instance ) {

        $first_box_title = ! empty( $instance['first_box_title'] ) ? $instance['first_box_title'] : '';
        $first_box_subtitle = ! empty( $instance['first_box_subtitle'] ) ? $instance['first_box_subtitle'] : '';
        $first_box_icon = ! empty( $instance['first_box_icon'] ) ? $instance['first_box_icon'] : '';

        $second_box_title = ( $instance['second_box_title'] );
        $second_box_subtitle = ( $instance['second_box_subtitle'] );
        $second_box_icon = sanitize_text_field( $instance['second_box_icon'] );

        $button_submit = sanitize_text_field( $instance['button_submit'] );


        $icons = purethemes_get_simple_line_icons();
        ?>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('first_box_title')); ?>"><?php esc_html_e('First box title:', 'findeo' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('first_box_title')); ?>" name="<?php echo esc_attr($this->get_field_name('first_box_title')); ?>" type="text" value="<?php echo esc_attr($first_box_title); ?>" />
        </p>       
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('first_box_subtitle')); ?>"><?php esc_html_e('First box subtitle:', 'findeo' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('first_box_subtitle')); ?>" name="<?php echo esc_attr($this->get_field_name('first_box_subtitle')); ?>" type="text" value="<?php echo esc_attr($first_box_subtitle); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('first_box_icon')); ?>"><?php esc_html_e('First box icon:', 'findeo' ); ?></label>
            <select name="<?php echo esc_attr($this->get_field_name('first_box_icon')); ?>" >
                <?php foreach ($icons as $icon) {?>
                    <option <?php if ($icon == $first_box_icon) echo 'selected'; ?> value="<?php echo esc_attr($icon); ?>"><?php echo esc_html($icon); ?></option>
                <?php }?>
            </select>
        </p>
        <hr />

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('second_box_title')); ?>"><?php esc_html_e('Second box title:', 'findeo' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('second_box_title')); ?>" name="<?php echo esc_attr($this->get_field_name('second_box_title')); ?>" type="text" value="<?php echo esc_attr($second_box_title); ?>" />
        </p>       
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('second_box_subtitle')); ?>"><?php esc_html_e('Second box subtitle:', 'findeo' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('second_box_subtitle')); ?>" name="<?php echo esc_attr($this->get_field_name('second_box_subtitle')); ?>" type="text" value="<?php echo esc_attr($second_box_subtitle); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('second_box_icon')); ?>"><?php esc_html_e('Second box icon:', 'findeo' ); ?></label>
            <select name="<?php echo esc_attr($this->get_field_name('second_box_icon')); ?>" >
                <?php foreach ($icons as $icon) {?>
                    <option <?php if ($icon == $second_box_icon) echo 'selected'; ?> value="<?php echo esc_attr($icon); ?>"><?php echo esc_html($icon); ?></option>
                <?php }?>
            </select>
        </p>
        <hr />

         <p>
            <label for="<?php echo esc_attr($this->get_field_id('button_submit')); ?>"><?php esc_html_e('Submit property button:', 'findeo' ); ?></label>
            <input id="<?php echo esc_attr($this->get_field_id('button_submit')); ?>" name="<?php echo esc_attr($this->get_field_name('button_submit')); ?>" type="checkbox" value="1" <?php checked( '1', $button_submit ); ?>/> <br/>
            <span class="description"><?php _e( 'Enable this option to show a button "Submit Property"','findeo'); ?></span>
        </p>  
       

        <?php
    }
}