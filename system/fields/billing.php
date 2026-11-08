<?php

class fieldBilling extends cmsFormField {

    public $title                = LANG_PARSER_BILLING_PLAN;
    public $is_public            = true;
    public $sql                  = '';
    public $is_virtual           = true;
    public $allow_index          = false;
    public $excluded_controllers = ['forms', 'content', 'groups'];

    public function getStringValue($value) {
        return '';
    }

    public function getInput($value) {
        return '';
    }

    public function parseTeaser($value) {
        return '';
    }

    public function parse($value) {

        if (empty($this->item['id'])) {
            return '';
        }

        $plan = cmsCore::getModel('billing')->
                setControllerOptions(cmsController::loadOptions('billing'))->
                getUserCurrentPlan($this->item['id']);
        if (!$plan) {
            return '';
        }

        $is_owner = cmsUser::get('id') == $this->item['id'];

        return cmsTemplate::getInstance()->renderFormField($this->class . '_view', [
            'field'    => $this,
            'is_owner' => $is_owner,
            'plan'     => $plan
        ]);
    }

}
