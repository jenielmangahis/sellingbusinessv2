<div class="edit-form-field " style="display:none">
    <div id="realteo-field-<?php echo $field_key; ?>">

        <p class="type-container">
            <label for="label">Type</label>
            <select class="field-type-selector" name="type[<?php echo esc_attr( $index ); ?>]">
                <?php 
                $types = array(
                    'header' => 'Header',
                    'text' => 'Text Input',
                    'select' => 'Select Input',
                    'checkbox' => 'Checkbox',
                    'files' => 'Files Upload (Gallery)',
                    'file' => 'Single File Upload',
                    'hidden' => 'Hidden Field',
                    'term-select' => 'Taxonomies Dropdown',
                    'term-checkboxes' => 'Taxonomies Checkboxes',
                    'wp-editor' => 'Textarea With Editor',
                    'map' => 'Map',
                    'floorplans'=> 'Floorplans Field'
                );
                foreach ($types as $type => $name) { ?>
                    <option <?php selected($field['type'], $type); ?> value="<?php echo $type; ?>"><?php echo $name; ?></option>
                <?php  } ?>
            </select>
        </p>
       
        <?php $multi = (isset($field['multi'])) ? $field['multi'] : false ; ?>    
        <p class="multi-container">
            <label for="multi">Switch to Multi Select</label>
            <input name="multi[<?php echo esc_attr( $index ); ?>]" type="checkbox" <?php checked(  $multi, 1, true ); ?> value="1">
        </p>

        <p class="label-container">
            <label for="label">Label <span class="dashicons dashicons-editor-help" title="Field title" ></span></label>
            <input name="label[<?php echo $index; ?>]" type="text" value="<?php  if(isset( $field['label'])) { echo esc_attr( $field['label'] ); } ?>">
        </p>


        <p class="name-container">
            <label for="label">Name</label>
            <input name="name[<?php echo $index; ?>]" type="text" value="<?php if(isset( $field['name'])) { echo esc_attr( $field['name'] ); } ?>">
        </p>  

        <p class="placeholder-container">
            <label for="label">Placeholder <span class="dashicons dashicons-editor-help" title="Text that is displayed in the input field before the user enters something" ></span></label>
            <input name="placeholder[<?php echo $index; ?>]" type="text" value="<?php  if(isset( $field['placeholder'])) { echo esc_attr( $field['placeholder'] ); } ?>">
        </p>

        <p class="tooltip-container">
            <label for="label">Tooltip <span class="dashicons dashicons-editor-help" title="Text that is displayed in the tooltip" ></span></label>
            <input name="tooltip[<?php echo $index; ?>]" type="text" value="<?php  if(isset( $field['tooltip'])) { echo esc_attr( $field['tooltip'] ); } ?>">
        </p> 

        <p class="description-container">
            <label for="label">Description <span class="dashicons dashicons-editor-help" title="Text that is displayed in the tooltip" ></span></label>
            <input name="description[<?php echo $index; ?>]" type="text" value="<?php  if(isset( $field['description'])) { echo esc_attr( $field['description'] ); } ?>">
        </p>
    
        <?php $required = (isset($field['required'])) ? $field['required'] : false ; ?>    
        <p class="required-container">
            <label for="label">Required?<span class="dashicons dashicons-editor-help" title="Check it if field has to be required" ></span></label> 
            <input name="required[<?php echo $index; ?>]" type="checkbox" <?php checked(  $required, 1, true ); ?> value="1">
        </p> 
  
           
        <p class="class-container">
            <?php $row_col = (isset($field['render_row_col'])) ? $field['render_row_col'] : '' ; ?>
            <label for="label">Field Width <span class="dashicons dashicons-editor-help" title="Field’s width using Bootstrap columns" ></span> </label> 
            <select class="field-edit-class-select" name="render_row_col[<?php echo $index; ?>]" >
                <option <?php selected($row_col, ''); ?> value="">Full Width</option>
                <option <?php selected($row_col, '11'); ?> value="11">11 Columns</option>
                <option <?php selected($row_col, '10'); ?> value="10">10 Columns</option>
                <option <?php selected($row_col, '9'); ?> value="9">9 Columns</option>
                <option <?php selected($row_col, '8'); ?> value="8">8 Columns</option>
                <option <?php selected($row_col, '7'); ?> value="7">7 Columns</option>
                <option <?php selected($row_col, '6'); ?> value="6">6 Columns</option>
                <option <?php selected($row_col, '5'); ?> value="5">5 Columns</option>
                <option <?php selected($row_col, '4'); ?> value="4">4 Columns</option>
                <option <?php selected($row_col, '3'); ?> value="3">3 Columns</option>
                <option <?php selected($row_col, '2'); ?> value="2">2 Columns</option>
                <option <?php selected($row_col, '1'); ?> value="1">1 Column</option>
            </select>
           
        </p>
      
        <?php $before_row = (isset($field['before_row']) && !empty($field['before_row'])) ? true : false ; ?>    
        <p class="open_row-container">
            <label for="label">First In Row<span class="dashicons dashicons-editor-help" title="Prepend the field with container." ></span></label> 
            <input name="before_row[<?php echo $index; ?>]" type="checkbox" <?php checked(  $before_row, 1, true ); ?> value="1">
        </p> 
        <?php $after_row = (isset($field['after_row']) && !empty($field['after_row'])) ? true : false ; ?>    
        <p class="close_row-container">
            <label for="label">Last In Row <span class="dashicons dashicons-editor-help" title="Closes the container for fields." ></span></label>
            <input name="after_row[<?php echo $index; ?>]" type="checkbox" <?php checked(  $after_row, 1, true ); ?> value="1">
        </p>

        <p class="unit-container">
            <label for="label">Unit</label>
            <input class="unit_field" name="unit[<?php echo $index; ?>]" type="text" value="<?php if(isset( $field['unit'])) { echo esc_attr( $field['unit'] ); }?>">
        </p>  
        <p class="priority-container" style="display: none">
            <label for="label">Priority</label>
            <input class="priority_field" name="priority[<?php echo $index; ?>]" type="text" value="<?php if(isset( $field['priority'])) { echo esc_attr( $field['priority'] ); }?>">
        </p>  
       
        <p class="css-class-container">
            <label for="label">Custom CSS Class</label>
            <input name="class[<?php echo $index; ?>]"  type="text" value="<?php if(isset( $field['class'])) {  echo esc_attr( $field['class'] ); } ?>">
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
         
        <p class="default-container" style="display:none">
            <label for="label">Default value</label>
            <input name="default[<?php echo $index; ?>]" type="text" value="<?php  if(isset( $field['default'])) { echo esc_attr( $field['default'] ); } ?>">
        </p>       
      
    </div>

</div>
    
      