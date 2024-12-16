<?php

class onGeoFormAdminWidgetsPage extends cmsAction {

    public function run($_data){

        list($form, $params) = $_data;

        $countries = $this->model->getCountries();

        $form->addField('access', new fieldList('countries:view', [
            'title'              => LANG_SHOW_TO_COUNTRIES,
            'hint'               => LANG_CP_NOT_SET_ALL,
            'is_chosen_multiple' => true,
            'default'            => [],
            'items'              => $countries
        ]));

        $form->addField('access', new fieldList('countries:hide', [
            'title'              => LANG_HIDE_TO_COUNTRIES,
            'default'            => [],
            'is_chosen_multiple' => true,
            'items'              => $countries
        ]));

        return [$form, $params];
    }

}
