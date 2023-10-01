<?php

class formAdminWidgetsImportScheme extends cmsForm {

    public function init($to_template_name) {

        $templates_dynamic_scheme = [];

        $tpls = cmsCore::getTemplates();

        if ($tpls) {
            foreach ($tpls as $tpl) {

                if($to_template_name == $tpl){
                    continue;
                }

                $template_path = cmsConfig::get('root_path') . cmsTemplate::TEMPLATE_BASE_PATH. $tpl;

                $manifest = cmsTemplate::getTemplateManifest($template_path);

                if($manifest === null){
                    continue;
                }

                if (!empty($manifest['properties']['is_dynamic_layout'])) {
                    $templates_dynamic_scheme[$tpl] = !empty($manifest['title']) ? $manifest['title'] : $tpl;
                }
            }
        }

        if(!$templates_dynamic_scheme){
            return [
                'basic' => [
                    'type'   => 'fieldset',
                    'childs' => [
                        new fieldFile('yaml_file', [
                            'title' => LANG_CP_WIDGETS_LFILE,
                            'hint'  => LANG_CP_WIDGETS_LFILE_HINT,
                            'options' => [
                                'extensions' => 'txt'
                            ]
                        ])
                    ]
                ]
            ];
        }

        return [
            'basic' => [
                'type'   => 'fieldset',
                'childs' => [
                    new fieldList('import_type', [
                        'title' => LANG_CP_WIDGETS_IMPORT_TYPE,
                        'items' => [
                            'file' => LANG_PARSER_FILE,
                            'template' => LANG_CP_WIDGETS_IMPORT_TYPE_EX
                        ]
                    ]),
                    new fieldList('from_template', [
                        'title' => LANG_CP_WIDGETS_FROM_TEMPLATE,
                        'items' => $templates_dynamic_scheme,
                        'visible_depend' => ['import_type' => ['show' => ['template']]]
                    ]),
                    new fieldCheckbox('copy_widgets', array(
                        'title' => LANG_CP_WIDGETS_COPY_WIDGETS,
                        'visible_depend' => ['import_type' => ['show' => ['template']]]
                    )),
                    new fieldFile('yaml_file', [
                        'title' => LANG_CP_WIDGETS_LFILE,
                        'hint'  => LANG_CP_WIDGETS_LFILE_HINT,
                        'options' => [
                            'extensions' => 'txt'
                        ],
                        'visible_depend' => ['import_type' => ['show' => ['file']]]
                    ])
                ]
            ]
        ];
    }

}
