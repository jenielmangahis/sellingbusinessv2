<?php

N2Loader::import('libraries.form.elements.text');

class N2ElementTwitterToken extends N2ElementText {

    protected function fetchElement() {

        N2JS::addInline('new N2Classes.FormElementTwitterToken("' . $this->fieldID . '", "' . N2Base::getApplication('smartslider')->router->createAjaxUrl(array(
                "generator/getAuthUrl",
                array(
                    'group' => N2Request::getVar('group'),
                    'type'  => N2Request::getVar('type')
                )
            )) . '", "' . admin_url('admin.php') . '");');

        return parent::fetchElement();
    }

    protected function post() {
        return '<a id="' . $this->fieldID . '_button" class="n2-form-element-button n2-h5 n2-uc" href="#">' . n2_('Request token') . '</a>';
    }
}


