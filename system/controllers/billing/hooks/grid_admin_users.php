<?php

class onBillingGridAdminUsers extends cmsAction {

    public function run($grid) {

        $grid['columns']['balance'] = [
            'title'   => LANG_BILLING_BALANCE,
            'width'   => 80,
            'filter'  => 'like',
            'switchable' => true,
            'handler' => function ($value) {
                return $value ? round($value, 2) : 0;
            }
        ];

        return $grid;
    }

}
