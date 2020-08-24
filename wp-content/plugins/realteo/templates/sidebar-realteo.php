<div class="sidebar sticky right">
		<?php 
		$sidebar = false;
		$sidebar = apply_filters( 'realteo_properties_sidebar', $sidebar );
		
		if( ! $sidebar ) {
			$sidebar = 'sidebar-properties';			
		}
			
		if( is_active_sidebar( $sidebar ) ) {
			dynamic_sidebar( $sidebar );
		} ?>
		
	</div>
</div>