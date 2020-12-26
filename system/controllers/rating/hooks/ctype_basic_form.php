<?php

class onRatingCtypeBasicForm extends cmsAction {

    public function run($form){

        $fieldset = $form->addFieldsetAfter('folders', LANG_CP_RATING, 'ratings', array('is_collapsed' => true));

        $form->addField($fieldset,new fieldCheckbox('is_rating', array(
            'title' => LANG_CP_RATING_ON
        )));

        $form->addField($fieldset, new fieldList('options:rating_template', array(
            'title' => LANG_RATING_TEMPLATE,
            'hint'  => sprintf(LANG_WIDGET_BODY_TPL_HINT, 'controllers/rating/widget*'),
            'generator' => function($item) {
                return $this->cms_template->getAvailableTemplatesFiles('controllers/rating', 'widget*.tpl.php');
            },
            'visible_depend' => array('is_rating' => array('show' => array('1')))
        )));

        $form->addField($fieldset, new fieldString('options:rating_item_label', array(
            'title' => LANG_RATING_ITEM_LABEL,
            'multilanguage' => true,
            'visible_depend' => array('is_rating' => array('show' => array('1')))
        )));

        $form->addField($fieldset, new fieldString('options:rating_list_label', array(
            'title' => LANG_RATING_LIST_LABEL,
            'multilanguage' => true,
            'visible_depend' => array('is_rating' => array('show' => array('1')))
        )));

        $form->addField($fieldset, new fieldCheckbox('options:rating_is_in_item', array(
            'title' => LANG_CP_FIELD_IN_ITEM,
            'default' => true,
            'visible_depend' => array('is_rating' => array('show' => array('1')))
        )));

        $form->addField($fieldset, new fieldCheckbox('options:rating_is_in_list', array(
            'title' => LANG_CP_FIELD_IN_LIST,
            'default' => true,
            'visible_depend' => array('is_rating' => array('show' => array('1')))
        )));

        $form->addField($fieldset, new fieldCheckbox('options:rating_is_average', array(
            'title' => LANG_RATING_IS_AVERAGE,
            'hint' => LANG_RATING_IS_AVERAGE_HINT,
            'default' => true,
            'visible_depend' => array('is_rating' => array('show' => array('1')), 'options:rating_template' => array('hide' => array('widget')))
        )));

        return $form;
    }

}
