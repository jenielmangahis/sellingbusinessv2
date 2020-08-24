<tr>
    <td class="sort-column">&nbsp;</td>
    <td>
        <?php echo esc_attr( $field['label'] ); ?>
    </td>
    <td class="field-type">
       <?php echo esc_html($field['type']); ?>
    </td>  
    <td class="field-options">
        <?php switch ($field['type']) {
            case 'text':
                echo "Text input field";
                break;
            case 'double-input':
                echo "Two Text input field";
                break;

            case 'multi-checkbox-row':
                echo "Text input field";
                break;
            case 'multi-checkbox':
                echo "Text input field";
                break;
            case 'select-taxonomy':
                echo "Text input field";
                break;
            case 'select':
                echo "Text input field";
                break;

            default:
                # code...
                break;
        } ?>
    </td> 
    <td class="field-name">
       <?php echo esc_attr( $field['name'] ); ?>
    </td>
    <td class="field-class">
        <?php echo esc_attr( $field['class'] ); ?>
    </td>
    <td class="field-rows">
        <?php echo $field['open_row']; ?>
        <?php echo $field['close_row']; ?>
    </td> 
    <td class="field-place">
        <?php echo esc_attr( $field['place'] ); ?>
    </td>
    
    
    <td class="field-actions">
        <a class="delete-field" href='#'>X</a>
        <a class="edit-field" data-id="<?php echo esc_attr($index); ?>" href='#'>Edit</a>
        
    </td>
</tr>