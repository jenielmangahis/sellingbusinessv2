<?php

class Advanced_Ads_Pro_Module_BuddyPress_Admin {
    
    public function __construct() {
        // stop, if main plugin doesn’t exist
	if ( ! class_exists( 'Advanced_Ads', false ) ) {
            return;
	}
        
        // stop if BuddyPress isn't activated
        if ( ! class_exists( 'BuddyPress', false ) ){
            return;
        }
        
        
        
        // add sticky placement
	add_action( 'advanced-ads-placement-types', array( $this, 'add_placement' ) );
        // content of sticky placement
	add_action( 'advanced-ads-placement-options-after', array( $this, 'placement_options' ), 10, 2 );
    }
    
    public function add_placement($types){
        //ad injection on a buddypress activity-stream
        $types['buddypress'] = array(
            'title' => __( 'BuddyPress Content', 'advanced-ads-pro' ),
            'description' => __( 'Display ads on BuddyPress related pages.', 'advanced-ads-pro' ),
            'image' => AAP_BASE_URL . 'modules/buddypress/assets/img/buddypress-icon.png'
        );
        return $types;
    }
    
    public function placement_options( $placement_slug = '', $placement = array() ){
	if( 'buddypress' === $placement['type'] ){
            $buddypress_positions = $this->get_buddypress_hooks();
            $current = isset($placement['options']['buddypress_hook']) ? $placement['options']['buddypress_hook'] : '';
            ?><label><?php _e( 'position', 'advanced-ads-pro' ); ?></label>
            <select name="advads[placements][<?php echo $placement_slug; ?>][options][buddypress_hook]">
                <option>---</option>
                <?php foreach( $buddypress_positions as $_group => $_positions ) : ?>
                    <optgroup label="<?php echo $_group; ?>">
                <?php foreach( $_positions as $_position ) : ?>
                    <option <?php selected( $_position, $current ); ?>><?php echo $_position; ?></option>
                <?php endforeach; ?>
                    </optgroup>
                <?php endforeach; ?>
            </select>
            <?php
                $index = (isset($placement['options']['pro_buddypress_pages_index'])) ? $placement['options']['pro_buddypress_pages_index'] : 1;
                ?><br><?php 
                $index_option = '<input type="number" name="advads[placements][' . $placement_slug . '][options][pro_buddypress_pages_index]" value="'
                . $index . '"/>';
		printf( __( 'Inject at %s. entry', 'advanced-ads-pro' ), $index_option );
	}
    }
    
    public function get_buddypress_hooks(){
        return array(
            __( 'Activity Entry', 'advanced-ads-pro' ) => array(
                'before activity entry',
                'activity entry content',
                'after activity entry',
                'before activity entry comments',
                'activity entry comments',
                'after activity entry comments'
            ),
            __( 'Group List', 'advanced-ads-pro' ) => array(
                'directory groups item'
            ),
            __( 'Member List', 'advanced-ads-pro') => array(
                'directory members item'
            )
        );
    }
}

