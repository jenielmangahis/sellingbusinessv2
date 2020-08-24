<?php
include_once CMCR_PLUGIN_DIR . 'backend/classes/SettingsViewAbstract.php';

class CMCR_SettingsView extends CMCR_SettingsViewAbstract
{

    public function displaySubcategory($category, $subcategory)
    {
        return sprintf('<table><caption>%s</caption>%s</table>', esc_html($this->getSubcategoryTitle($category, $subcategory)), parent::displaySubcategory($category, $subcategory)
        );
    }

    public function displayOption($name, array $option = array())
    {
        return sprintf('<tr>%s</tr>', parent::displayOption($name, $option));
    }

    public function displayOptionPlain($name, array $option = array())
    {
        return sprintf('<div>%s</div>', parent::displayOption($name, $option));
    }

    public function displayOptionTitle($option)
    {
        return sprintf('<th scope="row">%s:</th>', parent::displayOptionTitle($option));
    }

    public function displayOptionControls($name, array $option = array())
    {
        return sprintf('<td>%s</td>', parent::displayOptionControls($name, $option));
    }

    public function displayOptionDescription($option)
    {
        return sprintf('<td>%s</td>', parent::displayOptionDescription($option));
    }

    protected function getSubcategoryTitle($category, $subcategory)
    {
        $subcategories = $this->getSubcategories();
        if( isset($subcategories[$category]) AND isset($subcategories[$category][$subcategory]) )
        {
            return CM_Custom_Reports::__($subcategories[$category][$subcategory]);
        }
        else
        {
            return $subcategory;
        }
    }

    protected function getCategories()
    {
        return apply_filters('cmcr_settings_pages', CMCR_Settings::$categories);
    }

    protected function getSubcategories()
    {
        return apply_filters('cmcr_settings_pages_groups', CMCR_Settings::$subcategories);
    }

}