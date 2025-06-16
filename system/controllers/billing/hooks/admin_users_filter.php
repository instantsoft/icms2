<?php

class onBillingAdminUsersFilter extends cmsAction {

    public function run($fields) {

        $fields[] = [
            'title'   => LANG_BILLING_BALANCE,
            'name'    => 'balance',
            'handler' => new fieldNumber('balance')
        ];

        return $fields;
    }

}
