<div class="edit-form-field" style="display:none;">
    <div id="realteo-field-<?php echo $field_key; ?>">

        <p class="type-container">
            <label for="label">Type</label>
             <select class="field-type-selector" name="type[<?php echo esc_attr( $index ); ?>]">
                <option <?php selected($field['type'], 'text'); ?> value="text">Text</option>
                <option <?php selected($field['type'], 'select'); ?> value="select">Select</option>
               
                <option <?php selected($field['type'], 'input-select'); ?> value="input-select">Input-Select</option>
                <option <?php selected($field['type'], 'double-input'); ?> value="double-input">Min/Max</option>
                <option <?php selected($field['type'], 'slider'); ?> value="slider">Slider</option>
                <option <?php selected($field['type'], 'multi-checkbox'); ?> value="multi-checkbox">Checkboxes in column</option>
                <option <?php selected($field['type'], 'multi-checkbox-row'); ?> value="multi-checkbox-row">Checkboxes in row</option>
                <option <?php selected($field['type'], 'select-taxonomy'); ?> value="select-taxonomy">Select Taxonomy</option>
                <option <?php selected($field['type'], 'location'); ?> value="location">Location</option>
                <option <?php selected($field['type'], 'submit'); ?> value="submit">Submit</option>
                <option <?php selected($field['type'], 'hidden'); ?> value="hidden">Hidden</option>
                <option <?php selected($field['type'], 'header'); ?> value="header">Header</option>
            </select>
        </p>

     
        <p class="placeholder-container">
            <label for="label">Placeholder <span class="dashicons dashicons-editor-help" title="Text that is displayed in the input field before the user enters something" ></span></label>
            <input name="placeholder[<?php echo $index; ?>]" type="text" value="<?php echo esc_attr( $field['name'] ); ?>">
        </p>

        <p class="name-container">
            <label for="label">Name</label>
            <input name="name[<?php echo $index; ?>]"  type="text" value="<?php if(isset( $field['id'])) { echo esc_attr( $field['id'] ); } ?>">
        </p>  
           
        <p class="class-container">
            <label for="label">Field Width <span class="dashicons dashicons-editor-help" title="Field’s width using Bootstrap columns" ></span> </label> 
            <select class="field-edit-class-select" name="class[<?php echo esc_attr( $index ); ?>]" >
                <option  selected="selected" value="col-md-12">12 Columns (Full Width)</option>
                <option  value="col-md-11">11 Columns</option>
                <option  value="col-md-10">10 Columns</option>
                <option  value="col-md-9">9 Columns</option>
                <option  value="col-md-8">8 Columns</option>
                <option  value="col-md-7">7 Columns</option>
                <option  value="col-md-6">6 Columns</option>
                <option  value="col-md-5">5 Columns</option>
                <option  value="col-md-4">4 Columns</option>
                <option  value="col-md-3">3 Columns</option>
                <option  value="col-md-2">2 Columns</option>
                <option  value="col-md-1">1 Column</option>
            </select>
        </p>

        <?php $multi = false ; ?>    
        <p class="multi-container">
            <label for="multi">Enable Multi Select</label>
            <input name="multi[<?php echo $index; ?>]" type="checkbox" <?php checked(  $multi, 1, true ); ?> value="1">
        </p>
      
        <?php $open_row = true ; ?>    
        <p class="open_row-container" style="display: none">
            <label for="label">First In Row<span class="dashicons dashicons-editor-help" title="Prepend the field with container." ></span></label> 
            <input name="open_row[<?php echo esc_attr( $index ); ?>]" type="checkbox" <?php checked(  $open_row, 1, true ); ?> value="<?php echo esc_attr(  $open_row ); ?>">
        </p> 
        <?php $close_row = true ; ?>    
        <p class="close_row-container" style="display: none">
            <label for="label">Last In Row <span class="dashicons dashicons-editor-help" title="Closes the container for fields." ></span></label>
            <input name="close_row[<?php echo esc_attr( $index ); ?>]" type="checkbox" <?php checked(  $close_row, 1, true ); ?> value="<?php echo esc_attr(  $close_row ); ?>">
        </p>

        <p class="priority-container" style="display: none">
            <label for="label">Priority</label>
            <input class="priority_field" name="priority[<?php echo $index; ?>]" type="text" value="<?php if(isset( $field['priority'])) { echo esc_attr( $field['priority'] ); }?>">
        </p>  
        <p class="placement-container">
            <label for="label">Placement <span class="dashicons dashicons-editor-help" title="Defines whether field will be displayed always or will be displayed once ”Advanced Search” is enabled" ></span> </label>
            <select name="place[<?php echo $index; ?>]" ">
                <option <?php selected('main', 'main'); ?> value="main">Main</option>
                <option value="adv">Advanced</option>
            </select>
          
        </p>
        <p class="css-class-container">
            <label for="label">Custom CSS Class</label>
            <input name="css_class[<?php echo esc_attr( $index ); ?>]"  type="text" value="">
        </p>
       
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
        <div class="field-options options-container"  <?php if($field['type'] == 'select') { ?> style="display: block;" <?php } else { ?> style="display: none;" <?php } ?>>
            <label for="options">Options</label>
            
            <select name="options_source[<?php echo esc_attr( $index ); ?>]" class="field-options-data-source-choose">
                <option  value="">--Select Option--</option>
                <option <?php selected($source,'predefined'); ?> value="predefined">Predefined List</option>
                <option <?php selected($source,'custom'); ?> value="custom">Custom Options list</option>
            </select>
            <div class="options " >
                
                <select 
                <?php if($source == 'predefined') { ?> style="display: block;" <?php } else { ?> style="display: none;" <?php } ?>
                class="field-options-predefined" name="options_cb[<?php echo esc_attr( $index ); ?>]" id="">
                    <option value="">--Select Option--</option>
                    <?php foreach ($predefined_options as $key => $value) {?>
                        <option <?php if(isset($field['options_cb'])) { selected($field['options_cb'],$key); } ?> value="<?php echo esc_attr($key) ?>"><?php echo esc_html($value); ?></option>
                    <?php } ?>
                    
                </select>
                <table style="display: none" class="field-options-custom widefat fixed">
                    <thead>
                        <tr>
                            <td>Displayed Name</td>
                            <td>Searched Value</td>
                            <td></td>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <td colspan="3">
                                <a class="add-new-option-table button-primary" href="#">Add row</a>
                            </td>
                        </tr>
                    </tfoot>

                    <tbody  data-field="<?php echo esc_attr('<tr><td><input type="text" class="input-text options" name="options['.esc_attr( $index ).'][-1][name]" /></td><td><input type="text" class="input-text options" name="options['.esc_attr( $index ).'][-1][value]" /></td><td>x</td></tr>'); ?>">
                        <?php if(isset($field['options']) && is_array($field['options'])) { 
                             $i = 0;
                            foreach ($field['options'] as $key => $value) {
                            ?>
                            <tr>
                            <td><input type="text" value="<?php echo esc_attr($key);?>" class="input-text options" name="options[<?php echo esc_attr( $index ); ?>][<?php echo esc_attr( $i); ?>][name]" /></td>
                            <td>
                                <input type="text" value="<?php echo esc_attr($value);?>" class="input-text options" name="options[<?php echo esc_attr( $index ); ?>][<?php echo esc_attr( $i); ?>][value]" />
                            </td>
                            <td class="remove_item">x</td>
                        </tr>
                            <?php 
                            $i++;
                            }
                        }; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <p class="taxonomy-container" style="display:none">
            <label for="label">Taxonomy</label>
            <?php $selected_tax = (isset( $field['taxonomy']) && !empty( $field['taxonomy'])) ? $field['taxonomy'] : false ; ?>
            <select name="field_taxonomy[<?php echo $index; ?>]" id="">
                <option value="">--select--</option>
                <?php 
                    $taxonomy_objects = get_object_taxonomies( 'property', 'objects' );
                    foreach ($taxonomy_objects as $tax) {
                        echo '<option '.selected($selected_tax, $tax->name).' value="'.$tax->name.'">'.$tax->label.'</option>';
                    }
                ?>
            </select>
           
        </p>   
        
        <p class="max-container" <?php if($field['type']!='input-select') { echo ' style="display:none"'; } ?>>
            <label for="label">Max <span class="dashicons dashicons-editor-help" title="Use only for numeric types like area, price, etc." ></span></label>
            <input name="max[<?php echo esc_attr( $index ); ?>]" type="text" value="<?php  if(isset( $field['max'])) { echo esc_attr( $field['max'] ); } ?>">
            <small>Use "auto" to get minimum value from all properties.</small>
        </p>        
        <p class="min-container" <?php if($field['type']!='input-select') { echo ' style="display:none"'; } ?>>
            <label for="label">Min <span class="dashicons dashicons-editor-help" title="Use only for numeric types like area, price, etc." ></span></label>
            <input name="min[<?php echo esc_attr( $index ); ?>]" type="text" value="<?php  if(isset( $field['min'])) { echo esc_attr( $field['min'] ); } ?>">
            <small>Use "auto" to get minimum value from all properties</small>
        </p>        
        <p class="step-container" <?php if($field['type']!='input-select') { echo ' style="display:none"'; } ?>>
            <label for="label">Step</label>
            <input name="step[<?php echo esc_attr( $index ); ?>]" type="number" value="<?php  if(isset( $field['step'])) { echo esc_attr( $field['step'] ); } ?>">
            <small>Set step value high enough  to make less then 40 steps, longer lists are not recomended.</br>.</small>
        </p>       
        <p class="unit-container" <?php if($field['type']!='input-select') { echo ' style="display:none"'; } ?>>
            <label for="label">Unit</label>
            <input name="unit[<?php echo esc_attr( $index ); ?>]" type="text" value="<?php  if(isset( $field['unit'])) { echo esc_attr( $field['unit'] ); } ?>">
            <small>Unit is optional</small>
        </p>
    </div>

</div>
    
      