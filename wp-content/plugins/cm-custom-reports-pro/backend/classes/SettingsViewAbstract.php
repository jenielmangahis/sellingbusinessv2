<?php

abstract class CMCR_SettingsViewAbstract
{
    protected $categories = array();
    protected $subcategories = array();

    public function display()
    {
        $result = '';
        $categories = $this->getCategories();
        foreach($categories as $category => $title)
        {
            $result .= $this->displayCategory($category);
        }
        return $result;
    }

    public function displayCategory($category)
    {
        $result = '';
        $subcategories = $this->getSubcategories();
        if( !empty($subcategories[$category]) )
        {
            foreach($subcategories[$category] as $subcategory => $title)
            {
                $result .= $this->displaySubcategory($category, $subcategory);
            }
        }
        return $result;
    }

    abstract protected function getCategories();

    abstract protected function getSubcategories();

    public function displaySubcategory($category, $subcategory)
    {
        $result = '';
        $subcategories = $this->getSubcategories();
        if( isset($subcategories[$category]) AND isset($subcategories[$category][$subcategory]) )
        {
            $options = CMCR_Settings::getOptionsConfigByCategory($category, $subcategory);
            foreach($options as $name => $option)
            {
                $result .= $this->displayOption($name, $option);
            }
        }
        return $result;
    }

    public function displayOption($name, array $option = array())
    {
        if( empty($option) )
        {
            $option = CMCR_Settings::getOptionConfig($name);
        }
        return $this->displayOptionTitle($option)
                . $this->displayOptionControls($name, $option)
                . $this->displayOptionDescription($option);
    }

    public function displayOptionTitle($option)
    {
        return $option['title'];
    }

    public function displayOptionControls($name, array $option = array())
    {
        if( empty($option) ) $option = CMCR_Settings::getOptionConfig($name);
        switch($option['type'])
        {
            case CMCR_Settings::TYPE_BOOL:
                return $this->displayBool($name);
            case CMCR_Settings::TYPE_INT:
                return $this->displayInputNumber($name);
            case CMCR_Settings::TYPE_TEXTAREA:
                return $this->displayTextarea($name);
            case CMCR_Settings::TYPE_RADIO:
                return $this->displayRadio($name, $option['options']);
            case CMCR_Settings::TYPE_SELECT:
                return $this->displaySelect($name, $option['options']);
            case CMCR_Settings::TYPE_MULTISELECT:
                return $this->displayMultiSelect($name, $option['options']);
            case CMCR_Settings::TYPE_CSV_LINE:
                return $this->displayCSVLine($name);
            default:
                return $this->displayInputText($name);
        }
    }

    public function displayOptionDescription($option)
    {
        return (isset($option['desc']) ? $option['desc'] : '');
    }

    protected function displayInputText($name, $value = null)
    {
        if( is_null($value) )
        {
            $value = CMCR_Settings::getOption($name);
        }
        return sprintf('<input type="text" name="%s" value="%s" />', esc_attr($name), esc_attr($value));
    }

    protected function displayInputNumber($name)
    {
        return sprintf('<input type="number" name="%s" value="%s" />', esc_attr($name), esc_attr(CMCR_Settings::getOption($name)));
    }

    protected function displayCSVLine($name)
    {
        $value = CMCR_Settings::getOption($name);
        if( is_array($value) ) $value = implode(',', $value);
        return $this->displayInputText($name, $value);
    }

    protected function displayTextarea($name)
    {
        return sprintf('<textarea name="%s" cols="60" rows="5">%s</textarea>', esc_attr($name), esc_html(CMCR_Settings::getOption($name)));
    }

    protected function displayBool($name)
    {
        return $this->displayRadio($name, array(0 => 'No', 1 => 'Yes'), intval(CMCR_Settings::getOption($name)));
    }

    protected function displayRadio($name, $options, $currentValue = null)
    {
        if( is_null($currentValue) )
        {
            $currentValue = CMCR_Settings::getOption($name);
        }
        $result = '';
        $fieldName = esc_attr($name);
        foreach($options as $value => $text)
        {
            $fieldId = esc_attr($name . '_' . $value);
            $result .= sprintf('<div><input type="radio" name="%s" id="%s" value="%s"%s />'
                    . '<label for="%s"> %s</label></div>', $fieldName, $fieldId, esc_attr($value), ( $currentValue == $value ? ' checked="checked"' : ''), $fieldId, esc_html($text)
            );
        }
        return $result;
    }

    protected function displaySelect($name, $options, $currentValue = null)
    {
        return sprintf('<div><select name="%s">%s</select>', esc_attr($name), $this->displaySelectOptions($name, $options, $currentValue));
    }

    protected function displaySelectOptions($name, $options, $currentValue = null)
    {
        if( is_null($currentValue) )
        {
            $currentValue = CMCR_Settings::getOption($name);
        }
        $result = '';
        if( is_callable($options) ) $options = call_user_func($options, $name);
        foreach($options as $value => $text)
        {
            $result .= sprintf('<option value="%s"%s>%s</option>', esc_attr($value), ( $this->isSelected($value, $currentValue) ? ' selected="selected"' : ''), esc_html($text)
            );
        }
        return $result;
    }

    protected function isSelected($option, $value)
    {
        if( is_array($value) )
        {
            return in_array($option, $value);
        }
        else
        {
            return ($option == $value);
        }
    }

    protected function displayMultiSelect($name, $options, $currentValue = null)
    {
        return sprintf('<div><select name="%s[]" multiple="multiple">%s</select>', esc_attr($name), $this->displaySelectOptions($name, $options, $currentValue));
    }

}