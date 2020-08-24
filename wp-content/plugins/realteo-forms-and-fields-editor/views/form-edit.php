<div class="edit-form-field " style="display:none">
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
            <input name="placeholder[<?php echo $index; ?>]" type="text" value="<?php  if(isset( $field['placeholder'])) { echo esc_attr( $field['placeholder'] ); } ?>">
        </p>

        <p class="name-container">
            <label for="label">Name</label>
            <input name="name[<?php echo $index; ?>]" type="text" value="<?php if(isset( $field['name'])) { echo esc_attr( $field['name'] ); } ?>">
        </p>  
           
        <p class="class-container">
            <label for="label">Field Width <span class="dashicons dashicons-editor-help" title="Field’s width using Bootstrap columns" ></span> </label> 
            <select class="field-edit-class-select" name="class[<?php echo $index; ?>]" >
                <option <?php selected($field['class'], 'col-md-12'); ?> value="col-md-12">12 Columns (Full Width)</option>
                <option <?php selected($field['class'], 'col-md-11'); ?> value="col-md-11">11 Columns</option>
                <option <?php selected($field['class'], 'col-md-10'); ?> value="col-md-10">10 Columns</option>
                <option <?php selected($field['class'], 'col-md-9'); ?> value="col-md-9">9 Columns</option>
                <option <?php selected($field['class'], 'col-md-8'); ?> value="col-md-8">8 Columns</option>
                <option <?php selected($field['class'], 'col-md-7'); ?> value="col-md-7">7 Columns</option>
                <option <?php selected($field['class'], 'col-md-6'); ?> value="col-md-6">6 Columns</option>
                <option <?php selected($field['class'], 'col-md-5'); ?> value="col-md-5">5 Columns</option>
                <option <?php selected($field['class'], 'col-md-4'); ?> value="col-md-4">4 Columns</option>
                <option <?php selected($field['class'], 'col-md-3'); ?> value="col-md-3">3 Columns</option>
                <option <?php selected($field['class'], 'col-md-2'); ?> value="col-md-2">2 Columns</option>
                <option <?php selected($field['class'], 'col-md-1'); ?> value="col-md-1">1 Column</option>
            </select>
           
        </p>

        <?php $multi = (isset($field['multi'])) ? $field['multi'] : false ; ?>    
        <p class="multi-container">
            <label for="multi">Enable Multi Select</label>
            <input name="multi[<?php echo $index; ?>]" type="checkbox" <?php checked(  $multi, 1, true ); ?> value="1">
        </p>
      
        <?php $open_row = (isset($field['open_row'])) ? $field['open_row'] : false ; ?>    
        <p class="open_row-container">
            <label for="label">First In Row<span class="dashicons dashicons-editor-help" title="Prepend the field with container." ></span></label> 
            <input name="open_row[<?php echo $index; ?>]" type="checkbox" <?php checked(  $open_row, 1, true ); ?> value="1">
        </p> 

        <?php $close_row = (isset($field['close_row'])) ? $field['close_row'] : false ; ?>    
        <p class="close_row-container">
            <label for="label">Last In Row <span class="dashicons dashicons-editor-help" title="Closes the container for fields." ></span></label>
            <input name="close_row[<?php echo $index; ?>]" type="checkbox" <?php checked(  $close_row, 1, true ); ?> value="1">
        </p>

        <p class="priority-container" style="display: none">
            <label for="label">Priority</label>
            <input class="priority_field" name="priority[<?php echo $index; ?>]" type="text" value="<?php if(isset( $field['priority'])) { echo esc_attr( $field['priority'] ); }?>">
        </p>  

        <p class="placement-container">
            <label for="label">Placement <span class="dashicons dashicons-editor-help" title="Defines whether field will be displayed always or will be displayed once ”Advanced Search” is enabled" ></span> </label>
            <select name="place[<?php echo $index; ?>]" ">
                <option <?php selected($field['place'], 'main'); ?> value="main">Main</option>
                <option <?php selected($field['place'], 'adv'); ?> value="adv">Advanced</option>
            </select>
        </p>
        <p class="css-class-container">
            <label for="label">Custom CSS Class</label>
            <input name="css_class[<?php echo $index; ?>]"  type="text" value="<?php if(isset( $field['css_class'])) {  echo esc_attr( $field['css_class'] ); } ?>">
        </p>
        
        <div class="field-options options-container">
            <label for="options">Options <span class="dashicons dashicons-editor-help" title="Choose preset list or create your own list of options" ></span></label>
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
            <div class="options " >
                
                <select style="display: none" class="field-options-predefined" name="options_cb[<?php echo esc_attr( $index ); ?>]" id="">
                    <option value="">--Select Option--</option>
                    <?php foreach ($predefined_options as $key => $value) {?>
                        <option <?php if(isset($field['options_cb'])) { selected($field['options_cb'],$key); } ?> value="<?php echo esc_attr($key) ?>"><?php echo esc_html($value); ?></option>
                    <?php } ?>
                    
                </select>
                <table style="display: none" class="field-options-custom widefat fixed">
                    <thead>
                        <tr>
                            
                            <td>Searched Value</td>
                            <td>Displayed Name</td>
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

                    <tbody  data-field="<?php echo esc_attr('<tr><td><input type="text" class="input-text options" name="options['.esc_attr( $index ).'][-1][name]" /></td><td><input type="text" class="input-text options" name="options['.esc_attr( $index ).'][-1][value]" /></td><td><a class="remove-row button" href="#">Remove</a></td></tr>'); ?>">
                        <?php if(isset($field['options']) && is_array($field['options'])) { 
                             $i = 0;
                            foreach ($field['options'] as $key => $value) {
                            ?>
                            <tr>
                            <td><input type="text" value="<?php echo esc_attr($key);?>" class="input-text options" name="options[<?php echo esc_attr( $index ); ?>][<?php echo esc_attr( $i); ?>][name]" /></td>
                            <td>
                                <input type="text" value="<?php echo esc_attr($value);?>" class="input-text options" name="options[<?php echo esc_attr( $index ); ?>][<?php echo esc_attr( $i); ?>][value]" />
                            </td>
                            <td><a class="remove-row button" href="#">Remove</a></td>
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

        <p class="max-container" style="display:none">
            <label for="label">Max <span class="dashicons dashicons-editor-help" title="Use only for numeric types like area, price, etc." ></span></label>
            <input name="max[<?php echo $index; ?>]" type="text" value="<?php  if(isset( $field['max'])) { echo esc_attr( $field['max'] ); } ?>">
            <small>Use "auto" to get minimum value from all properties </br>.</small>
        </p>        
        <p class="min-container" style="display:none">
            <label for="label">Min <span class="dashicons dashicons-editor-help" title="Use only for numeric types like area, price, etc." ></span></label>
            <input name="min[<?php echo $index; ?>]" type="text" value="<?php  if(isset( $field['min'])) { echo esc_attr( $field['min'] ); } ?>">
            <small>Use "auto" to get minimum value from all properties</small>
        </p>        
        <p class="step-container" style="display:none">
            <label for="label">Step</label>
            <input name="step[<?php echo $index; ?>]" type="number" value="<?php  if(isset( $field['step'])) { echo esc_attr( $field['step'] ); } ?>">
            <small>Set step value high enough  to make less then 30 steps, longer lists are not recomended.</br>.</small>
            <p class="step-error-exceed">Error: Step exceeds the specified range</p>
            <p class="step-error-too-many">Error: Too many steps</p>
        </p>       
        <p class="unit-container" style="display:none">
            <label for="label">Unit</label>
            <input name="unit[<?php echo $index; ?>]" type="text" value="<?php  if(isset( $field['unit'])) { echo esc_attr( $field['unit'] ); } ?>">
            <small>Unit is optional</small>
        </p>
    </div>

</div>
    
      