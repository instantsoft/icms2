<?php

class onRatingCtypeBasicForm extends cmsAction {

    public function run($form) {

        $fieldset = $form->addFieldsetAfter('folders', LANG_CP_RATING, 'ratings', ['is_collapsed' => true]);

        $form->addField($fieldset,new fieldCheckbox('is_rating', [
            'title' => LANG_CP_RATING_ON
        ]));

        $form->addField($fieldset, new fieldList('options:rating_template', [
            'title' => LANG_RATING_TEMPLATE,
            'hint'  => sprintf(LANG_WIDGET_BODY_TPL_HINT, 'controllers/rating/widget*'),
            'generator' => function($item) {
                return $this->cms_template->getAvailableTemplatesFiles('controllers/rating', 'widget*.tpl.php');
            },
            'visible_depend' => ['is_rating' => ['show' => ['1']]]
        ]));

        $form->addField($fieldset, new fieldString('options:rating_item_label', [
            'title' => LANG_RATING_ITEM_LABEL,
            'multilanguage' => true,
            'visible_depend' => ['is_rating' => ['show' => ['1']]]
        ]));

        $form->addField($fieldset, new fieldString('options:rating_list_label', [
            'title' => LANG_RATING_LIST_LABEL,
            'multilanguage' => true,
            'visible_depend' => ['is_rating' => ['show' => ['1']]]
        ]));

        $form->addField($fieldset, new fieldCheckbox('options:rating_is_in_item', [
            'title' => LANG_CP_FIELD_IN_ITEM,
            'default' => true,
            'visible_depend' => ['is_rating' => ['show' => ['1']]]
        ]));

        $form->addField($fieldset, new fieldCheckbox('options:rating_is_in_list', [
            'title' => LANG_CP_FIELD_IN_LIST,
            'default' => true,
            'visible_depend' => ['is_rating' => ['show' => ['1']]]
        ]));

        $form->addField($fieldset, new fieldCheckbox('options:rating_is_average', [
            'title' => LANG_RATING_IS_AVERAGE,
            'hint' => LANG_RATING_IS_AVERAGE_HINT,
            'default' => true,
            'visible_depend' => ['is_rating' => ['show' => ['1']], 'options:rating_template' => ['hide' => ['widget']]]
        ]));

        return $form;
    }

}
