<?php

class onSubscriptionsCtypeBasicForm extends cmsAction {

    public function run($form){

        $fieldset = $form->addFieldsetAfter('rss', LANG_SUBSCRIPTIONS_CONTROLLER, 'subscriptions', array('is_collapsed' => true));

        $form->addField($fieldset, new fieldCheckbox('options:enable_subscriptions', array(
            'title'   => LANG_SBSCR_CTYPE_ON,
            'default' => true
        )));

        $form->addField($fieldset, new fieldCheckbox('options:subscriptions_recursive_categories', array(
            'title'   => LANG_SBSCR_CTYPE_RECURSIVE_CATEGORIES,
            'default' => true
        )));

        return $form;

    }

}
