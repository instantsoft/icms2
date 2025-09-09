<?php

class formWidgetFormsFormOptions extends cmsForm {

    public function init() {

        cmsCore::loadControllerLanguage('forms');

        return [
            [
                'type'   => 'fieldset',
                'title'  => LANG_OPTIONS,
                'childs' => [
                    new fieldList('options:form_id', [
                        'title'   => LANG_PARSER_FORMS,
                        'generator' => function() {

                            $model = new cmsModel();

                            return array_collection_to_list($model->get('forms'), 'id', 'title');
                        }
                    ]),
                    new fieldCheckbox('options:show_title', [
                        'title' => LANG_SHOW_TITLE
                    ])
                ]
            ]
        ];
    }

}
