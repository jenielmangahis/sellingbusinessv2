<?php

class Advanced_Ads_Pro_Module_BuddyPress {
    
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'wp_plugins_loaded_ad_actions' ), 20 );

		add_filter( 'advanced-ads-visitor-conditions', array( $this, 'visitor_conditions' ) );
	}

	public function wp_plugins_loaded_ad_actions(){
		// stop, if main plugin doesn’t exist
		if ( ! class_exists( 'Advanced_Ads', false ) ) {
		    return;
		}

		// stop if BuddyPress isn't activated
		if ( ! class_exists( 'BuddyPress', false ) ){
		    return;
		}

		//dont load new ads on posts added via ajax
		if ( ( defined( 'DOING_AJAX' ) && DOING_AJAX) ){
		    return;
		}

		// load BuddyPress hooks

		// get placements
		$placements = get_option( 'advads-ads-placements', array() );

		if( is_array( $placements ) ){
		    foreach ( $placements as $_placement_id => $_placement ){
			if ( isset($_placement['type']) && 'buddypress' == $_placement['type'] && isset( $_placement['options']['buddypress_hook'] ) ){
			    $hook = str_replace( ' ', '_', 'bp_' . $_placement['options']['buddypress_hook'] );
			    add_action( $hook, array($this, 'execute_hook') );
			}
		    }
		}
	}

	public function execute_hook(){
		// get placements
		$placements = get_option( 'advads-ads-placements', array() );
		// look for the current hook in the placements
		$hook = current_filter();
		if( is_array( $placements ) ){
		    foreach ( $placements as $_placement_id => $_placement ){
			if ( isset($_placement['type'] ) && 'buddypress' == $_placement['type']
			&& isset( $_placement['options']['pro_buddypress_pages_index'] )
			&& isset( $_placement['options']['buddypress_hook'] )
			&& $hook === str_replace( ' ', '_', 'bp_' . $_placement['options']['buddypress_hook'] ) ){
			    if( did_action( $hook ) == $_placement['options']['pro_buddypress_pages_index'] ){
			       the_ad_placement( $_placement_id ); 
			    }
			}
		    }
		}
	}
    
	/**
	 * add visitor condition for BuddyPress profile fields
	 *
	 * @since 2.2.1
	 * @param arr $conditions visitor conditions of the main plugin
	 * @return arr $conditions new global visitor conditions
	 */
	public function visitor_conditions( $conditions ){

		// stop if BuddyPress isn't activated
		if ( ! class_exists( 'BuddyPress', false ) || ! function_exists( 'bp_profile_get_field_groups' ) ){
			return $conditions;
		}
	    
		$conditions['buddypress_profile_field'] = array(
			'label' => __( 'BuddyPress profile field', 'advanced-ads-pro' ),
			'description' => __( 'Display ads based on BuddyPress profile fields', 'advanced-ads-pro' ),
			'metabox' => array( 'Advanced_Ads_Pro_Module_BuddyPress', 'xprofile_metabox' ),
			'check' => array( 'Advanced_Ads_Pro_Module_BuddyPress', 'check_xprofile' ),
		);

		return $conditions;
	}
	
	public static function check_xprofile( $options = array() ) {
		if ( ! isset( $options['operator'] ) || ! isset( $options['value'] ) || ! isset( $options['field'] )  ) {
			    return true;
		}
		$user = wp_get_current_user();
		$operator = $options['operator'];
		$value = trim( $options['value'] );
		$field = trim( $options['field'] );
		if ( !$user ) {
		    return true;
		}

		$args = array(
		    'field'   => $field, // should be field ID
		    'user_id' => $user->ID,
		);
		$profile = bp_get_profile_field_data( $args );

		$trimmed_options = array(
		    'operator' => $operator,
		    'value' => $value,
		);

		$condition = Advanced_Ads_Visitor_Conditions::helper_check_string( $profile, $trimmed_options );
		return $condition;
	}
    
	public static function xprofile_metabox( $options, $index = 0 ) {
		if ( ! isset ( $options['type'] ) || '' === $options['type'] ) { return; }

		$type_options = Advanced_Ads_Visitor_Conditions::get_instance()->conditions;

		if ( ! isset( $type_options[ $options['type'] ] ) ) {
			return;
		}

		$groups     = bp_profile_get_field_groups();

		// form name basis
		$name = Advanced_Ads_Visitor_Conditions::FORM_NAME . '[' . $index . ']';
		$value = isset( $options['value'] ) ? $options['value'] : '';

		// options
		$field = isset( $options['field'] ) ? $options['field'] : '';
		$value = isset( $options['value'] ) ? $options['value'] : '';
		$operator = isset( $options['operator'] ) ? $options['operator'] : 'is_equal';
		?><input type="hidden" name="<?php echo $name; ?>[type]" value="<?php echo $options['type']; ?>"/>

		<?php if( $groups ) :
		    ?><select name=<?php echo $name; ?>[field]"><?php
			foreach( $groups as $_group ) :
			    ?><optgroup label="<?php echo $_group->name; ?>"><?php
				if(  $_group->fields ) foreach( $_group->fields as $_field ) :
				    ?><option value="<?php echo $_field->id; ?>" <?php selected( $field, $_field->id ); ?>><?php echo $_field->name; ?></option><?php
				endforeach;
			    ?></optgroup><?php
			endforeach;
		    ?></select><?php
		else :
		    ?><p class="advads-error-message"><?php 
		    /* translators: "profile fields" relates to BuddyPress profile fields */
		    _e( 'No profile fields found', 'advanced-ads-pro' ); ?></p><?php
		endif;

		if( 0 <= version_compare( ADVADS_VERSION, '1.9.1' ) ) {
			include( ADVADS_BASE_PATH . 'admin/views/ad-conditions-string-operators.php' ); 
		} ?>
		<input type="text" name="<?php echo $name; ?>[value]" value="<?php echo esc_attr( $value ); ?>" />
		<br class="clear" />
		<br />
		    <p class="description"><?php echo $type_options[ $options['type'] ]['description']; ?></p>
		<?php
	}	
}

