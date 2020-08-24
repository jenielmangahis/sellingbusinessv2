<?php

class CMCR_Settings
{
    const TYPE_BOOL = 'bool';
    const TYPE_INT = 'int';
    const TYPE_STRING = 'string';
    const TYPE_COLOR = 'color';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_RADIO = 'radio';
    const TYPE_SELECT = 'select';
    const TYPE_MULTISELECT = 'multiselect';
    const TYPE_CSV_LINE = 'csv_line';

    /*
     * OPTIONS
     */
    // General
    //General
    const OPTION_DATE_FORMAT = 'cmcr_date_format';
    const OPTION_DATA_RANGE_SPAN = 'cmcr_data_range_span';
    const OPTION_FAVORITED_REPORTS = 'cmcr_favorited_reports';


    /*
     * OPTIONS - END
     */
    const ACCESS_EVERYONE = 0;
    const ACCESS_USERS = 1;
    const ACCESS_ROLE = 2;
    const EDIT_MODE_DISALLOWED = 0;
    const EDIT_MODE_WITHIN_HOUR = 1;
    const EDIT_MODE_WITHIN_DAY = 2;
    const EDIT_MODE_ANYTIME = 3;

    public static $categories = array(
        'general'    => 'General',
        'appearance' => 'Appearance',
        'custom_css' => 'Custom CSS',
        'labels'     => 'Labels',
    );
    public static $subcategories = array(
        'general' => array(
            'general' => 'General Options',
        ),
    );

    public static function getOptionsConfig()
    {

        return apply_filters('cmcr_options_config', array(
            // General
            self::OPTION_DATE_FORMAT => array(
                'type'        => self::TYPE_SELECT,
                'default'     => 'us',
                'category'    => 'general',
                'subcategory' => 'general',
                'title'       => 'Date format',
                'desc'        => 'Select the date format',
                'options'     => array('us' => 'US', 'eur' => 'EUR'),
            ),
            self::OPTION_DATA_RANGE_SPAN => array(
                'type'        => self::TYPE_BOOL,
                'default'     => TRUE,
                'category'    => 'general',
                'subcategory' => 'general',
                'title'       => 'Span the report to selected dates',
                'desc'        => 'Select if you want to span the report to the dates selected with the filter. With this option disabled the span will be limited to first and last day with data.',
            ),
            self::OPTION_FAVORITED_REPORTS => array(
                'type'        => self::TYPE_SELECT,
                'default'     => '',
                'category'    => 'hidden',
                'subcategory' => 'hidden',
                'title'       => 'Favorited reports',
                'desc'        => 'Select the favorited reports',
                'options'     => array(),
            ),
                )
        );
    }

    public static function getOptionsConfigByCategory($category, $subcategory = null)
    {
        $options = self::getOptionsConfig();
        return array_filter($options, function($val) use ($category, $subcategory)
        {
            if( $val['category'] == $category )
            {
                return (is_null($subcategory) OR $val['subcategory'] == $subcategory);
            }
        });
    }

    public static function getOptionConfig($name)
    {
        $options = self::getOptionsConfig();
        if( isset($options[$name]) )
        {
            return $options[$name];
        }
    }

    public static function setOption($name, $value)
    {
        $options = self::getOptionsConfig();
        if( isset($options[$name]) )
        {
            $field = $options[$name];
            $old = get_option($name);
            if( is_array($old) OR is_object($old) OR strlen((string) $old) > 0 )
            {
                update_option($name, self::cast($value, $field['type']));
            }
            else
            {
                $result = update_option($name, self::cast($value, $field['type']));
            }
        }
    }

    public static function deleteAllOptions()
    {
        $params = array();
        $options = self::getOptionsConfig();
        foreach($options as $name => $optionConfig)
        {
            self::deleteOption($name);
        }

        return $params;
    }

    public static function deleteOption($name)
    {
        $options = self::getOptionsConfig();
        if( isset($options[$name]) )
        {
            delete_option($name);
        }
    }

    public static function getOption($name)
    {
        $options = self::getOptionsConfig();
        if( isset($options[$name]) )
        {
            $field = $options[$name];
            $defaultValue = (isset($field['default']) ? $field['default'] : null);
            return self::cast(get_option($name, $defaultValue), $field['type']);
        }
    }

    public static function getCategories()
    {
        $categories = array();
        $options = self::getOptionsConfig();
        foreach($options as $option)
        {
            $categories[] = $option['category'];
        }
        return $categories;
    }

    public static function getSubcategories($category)
    {
        $subcategories = array();
        $options = self::getOptionsConfig();
        foreach($options as $option)
        {
            if( $option['category'] == $category )
            {
                $subcategories[] = $option['subcategory'];
            }
        }
        return $subcategories;
    }

    protected static function boolval($val)
    {
        return (boolean) $val;
    }

    protected static function arrayval($val)
    {
        if( is_array($val) ) return $val;
        else if( is_object($val) ) return (array) $val;
        else return array();
    }

    protected static function cast($val, $type)
    {
        if( $type == self::TYPE_BOOL )
        {
            return (intval($val) ? 1 : 0);
        }
        else
        {
            $castFunction = $type . 'val';
            if( function_exists($castFunction) )
            {
                return call_user_func($castFunction, $val);
            }
            else if( method_exists(__CLASS__, $castFunction) )
            {
                return call_user_func(array(__CLASS__, $castFunction), $val);
            }
            else
            {
                return $val;
            }
        }
    }

    protected static function csv_lineval($value)
    {
        if( !is_array($value) ) $value = explode(',', $value);
        return $value;
    }

    public static function processPostRequest()
    {
        $params = array();
        $options = self::getOptionsConfig();
        foreach($options as $name => $optionConfig)
        {
            if( isset($_POST[$name]) )
            {
                $params[$name] = $_POST[$name];
                self::setOption($name, $_POST[$name]);
            }
        }

        return $params;
    }

    public static function userId($userId = null)
    {
        if( empty($userId) ) $userId = get_current_user_id();
        return $userId;
    }

    public static function isLoggedIn($userId = null)
    {
        $userId = self::userId($userId);
        return !empty($userId);
    }

    public static function getRolesOptions()
    {
        global $wp_roles;
        $result = array();
        if( !empty($wp_roles) AND is_array($wp_roles->roles) ) foreach($wp_roles->roles as $name => $role)
            {
                $result[$name] = $role['name'];
            }
        return $result;
    }

    public static function canReportspam($userId = null)
    {
        return (self::getOption(self::OPTION_SPAM_REPORTING_ENABLED) AND ( self::getOption(self::OPTION_SPAM_REPORTING_GUESTS) OR self::isLoggedIn($userId)));
    }

    public static function getPagesOptions()
    {
        $pages = get_pages(array('number' => 100));
        $result = array(null => '--');
        foreach($pages as $page)
        {
            $result[$page->ID] = $page->post_title;
        }
        return $result;
    }

    public static function areAttachmentsAllowed()
    {
        $ext = self::getOption(self::OPTION_ATTACHMENTS_FILE_EXTENSIONS);
        return (!empty($ext) AND ( self::getOption(self::OPTION_ATTACHMENTS_ANSWERS_ALLOW) OR self::getOption(self::OPTION_ATTACHMENTS_QUESTIONS_ALLOW)));
    }

    public static function getLoginPageURL($returnURL = null)
    {
        if( empty($returnURL) )
        {
            $returnURL = get_permalink();
        }
        if( $customURL = CMCR_Settings::getOption(CMCR_Settings::OPTION_LOGIN_PAGE_LINK_URL) )
        {
            return esc_url(add_query_arg(array('redirect_to' => urlencode($returnURL)), $customURL));
        }
        else
        {
            return wp_login_url($returnURL);
        }
    }

}