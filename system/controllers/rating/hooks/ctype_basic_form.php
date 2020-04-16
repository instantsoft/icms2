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

        return $form;

    }

}
