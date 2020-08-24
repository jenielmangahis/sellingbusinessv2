<div class="edit-form-field" style="display: none;">
    <div id="realteo-field-<?php echo $field_key; ?>">

    	 <p class="name-container">
            <label for="label">Name</label>
            <input type="text" class="input-text" name="name[<?php echo esc_attr( $index ); ?>]" value="<?php echo esc_attr( $field['name'] ); ?>" />
        </p>  
        <?php 
        $blocked_fileds = array('_price','_price_per','_offer_type','_property_type','_rental_period','_area','_friendly_address','_address','_geolocation_lat','_geolocation_long'); 

        ?>
		
		<p class="field-id" 
		<?php if( isset($field['id']) && in_array($field['id'],$blocked_fileds)) { echo 'style="display:none"'; } ?>>
			<label for="label">ID</label>
			<input type="text" class="input-text" name="id[<?php echo esc_attr( $index ); ?>]" value="<?php echo esc_attr( isset( $field['id'] ) ? $field['id'] : '' ); ?>"  />
		</p>
		<p class="field-type">
			<label for="type">Type</label>
			<select name="type[<?php echo esc_attr( $index ); ?>]">
				<?php
				foreach ( $field_types as $key => $type ) {
					echo '<option value="' . esc_attr( $key ) . '" ' . selected( $field['type'], $key, false ) . '>' . esc_html( $type ) . '</option>';
				}
				?>
			</select>
		</p>
		<?php if( in_array($tab,array('main_details_tab','details_tab')) ) : ?>
		<p class="invert-container">
            <label for="invert">Show value before label</label>
            <input name="invert[<?php echo esc_attr( $index ); ?>]" type="checkbox" <?php checked(  $field['invert'], 1, true ); ?> value="1">
        </p>
    	<?php endif; ?>
		<p>
			<label for="desc">Decription</label>
			<textarea  rows="4" cols="30" class="input-text" name="desc[<?php echo esc_attr( $index ); ?>]"><?php if(isset( $field['desc'] )) { echo esc_attr( $field['desc'] ); } ?></textarea>
		</p>
		<div class="field-options">
			<label for="options">Options</label>
			<?php 
			$source = '';
			if(!isset($field['options_source'])) {
				if( isset($field['options_cb']) && !empty($field['options_cb']) ) {
				 	$source = 'predefined';
				}; 
			} else {
				$source = '';
			};

			if(isset($field['options_source']) && empty($field['options_source'])) {
				if( isset($field['options_cb']) && !empty($field['options_cb'])) {
				 	$source = 'predefined';
				}; 
			} 
			if(isset($field['options_source']) && !empty($field['options_source'])) {
				$source = $field['options_source'];
			} ?>
			<select name="options_source[<?php echo esc_attr( $index ); ?>]" class="field-options-data-source-choose">
				<option  value="">--Select Option--</option>
				<option <?php selected($source,'predefined'); ?> value="predefined">Predefined List</option>
				<option <?php selected($source,'custom'); ?> value="custom">Custom Options list</option>
			</select>
			<div class="options" >
				<select style="display: none" class="field-options-predefined" name="options_cb[<?php echo esc_attr( $index ); ?>]" id="">
					<option value="">--Select Option--</option>
					<?php foreach ($predefined_options as $key => $value) {?>
						<option <?php if(isset($field['options_cb'])) { selected($field['options_cb'],$key); } ?> value="<?php echo esc_attr($key) ?>"><?php echo esc_html($value); ?></option>
					<?php } ?>
				</select>
				<table style="display: none" class="field-options-custom">
					<thead>
						<tr>
							<td>Value</td>
							<td>Name</td>
							<td></td>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="3">
								<a class="add-new-option-table" href="#">Add</a>
							</td>
						</tr>
					</tfoot>
					<tbody data-field="<?php echo esc_attr("
					<tr>
						<td>
							<input type='text' class='input-text options' name='options[{$index}][-1][name]' />
						</td>
						<td>
							<input type='text' class='input-text options' name='options[{$index}][-1][value]' />
						</td>
						<td>
							x
						</td>
					</tr>"); ?>">
						<?php if(isset($field['options']) && is_array($field['options'])) { 
							 $i = 0;
							foreach ($field['options'] as $key => $value) {
							?>
						<tr>
							<td>
	<input type="text" value="<?php echo esc_attr($key);?>" class="input-text options" name="options[<?php echo esc_attr( $index ); ?>][<?php echo esc_attr($i); ?>][name]" />
							</td>
							<td>
	<input type="text" value="<?php echo esc_attr($value);?>" class="input-text options" name="options[<?php echo esc_attr( $index ); ?>][<?php echo esc_attr($i); ?>][value]" />
							</td>
							<td class="remove_row">x</td>
						</tr>
							<?php 
							$i++;
							}
						}; ?>
					</tbody>
				</table>
			</div>
		</div>
		<p>
			<label for="">Default</label>
			<input type="text" class="input-text" name="default[<?php echo esc_attr( $index ); ?>]" value="<?php if(isset( $field['default'] )) { echo esc_attr( $field['default'] ); } ?>" />
		</p>

    </div>
</div>
