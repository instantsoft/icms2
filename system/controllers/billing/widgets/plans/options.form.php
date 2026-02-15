<?php

class formWidgetBillingPlansOptions extends cmsForm {

    public function init() {

        $plans = cmsCore::getController('billing')->model->getPlans();

        $plans = array_column($plans, 'title', 'id');

        return [
            [
                'type'   => 'fieldset',
                'title'  => LANG_OPTIONS,
                'childs' => [
                    new fieldListMultiple('options:show_list', [
                        'title'    => LANG_BILLING_WD_PLANS,
                        'default'  => 0,
                        'show_all' => true,
                        'items'    => $plans
                    ]),
                    new fieldList('options:default_plan_id', [
                        'title' => LANG_BILLING_PLAN_DEFAULT,
                        'hint'  => LANG_BILLING_WD_DEFAULT_HINT,
                        'items' => ['' => ''] + $plans
                    ]),
                    new fieldString('options:default_plan_badge', [
                        'title' => LANG_BILLING_WD_PLAN_BADGE
                    ]),
                    new fieldHtml('options:plans_desc', [
                        'title' => LANG_BILLING_WD_PLAN_DESC,
                        'options' => ['editor' => 'ace']
                    ])
                ]
            ]
        ];
    }

}
