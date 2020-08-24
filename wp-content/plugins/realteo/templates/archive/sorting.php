<div class="col-md-6">
	<!-- Sort by -->
	<div class="sort-by">
		<label><?php esc_html_e('Sort by:','realteo'); ?></label>
		<form class="realteo-ordering" method="get" action="">
		<div class="sort-by-select">
			<?php $default = isset( $_GET['realteo_order'] ) ? (string) $_GET['realteo_order']  : realteo_get_option('realteo_sort_by');
			?>
			<select name="realteo_order" data-placeholder="Default order" class="chosen-select-no-single orderby" >
				<option <?php selected($default,'default'); ?> value="default"><?php esc_html_e( 'Default Order' , 'realteo' ); ?></option>	
				<option <?php selected($default,'price-asc'); ?> value="price-asc"><?php esc_html_e( 'Price Low to High' , 'realteo' ); ?></option>
				<option <?php selected($default,'price-desc'); ?> value="price-desc"><?php esc_html_e( 'Price High to Low' , 'realteo' ); ?></option>
				<option <?php selected($default,'date-desc'); ?> value="date-desc"><?php esc_html_e( 'Newest Properties' , 'realteo' ); ?></option>
				<option <?php selected($default,'date-asc'); ?> value="date-asc"><?php esc_html_e( 'Oldest Properties' , 'realteo' ); ?></option>
				<option <?php selected($default,'featured'); ?> value="featured"><?php esc_html_e( 'Featured' , 'realteo' ); ?></option>
				<option <?php selected($default,'rand'); ?> value="rand"><?php esc_html_e( 'Random' , 'realteo' ); ?></option>
			</select>
		</div>
		</form>
	</div>
</div>