<?php

class formWidgetFormsFormOptions extends cmsForm {

    public function init() {

        cmsCore::loadControllerLanguage('forms');

        return array(
            array(
                'type'   => 'fieldset',
                'title'  => LANG_OPTIONS,
                'childs' => array(
                    new fieldList('options:form_id', array(
                        'title'   => LANG_PARSER_FORMS,
                        'generator' => function() {

                            $model = new cmsModel();

                            return array_collection_to_list($model->get('forms'), 'id', 'title');
                        }
                    )),
                    new fieldCheckbox('options:show_title', array(
                        'title' => LANG_SHOW_TITLE
                    ))
                )
            )
        );

    }

}
