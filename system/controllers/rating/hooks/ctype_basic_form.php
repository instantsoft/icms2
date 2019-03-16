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

                $tpls = cmsCore::getFilesList('templates/'.cmsConfig::get('template').'/controllers/rating/', 'widget*.tpl.php');
                $default_tpls = cmsCore::getFilesList('templates/default/controllers/rating/', 'widget*.tpl.php');
                $tpls = array_unique(array_merge($tpls, $default_tpls));

                $items = array('' => LANG_BY_DEFAULT);

                if ($tpls) {
                    foreach ($tpls as $tpl) {
                        $items[str_replace('.tpl.php', '', $tpl)] = $tpl;
                    }
                }

                return $items;

            },
            'visible_depend' => array('is_rating' => array('show' => array('1')))
        )));

        return $form;

    }

}
