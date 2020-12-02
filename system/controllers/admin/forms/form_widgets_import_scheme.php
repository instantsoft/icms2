<?php

class formAdminWidgetsImportScheme extends cmsForm {

    public function init($to_template_name) {

        return [
            'basic' => [
                'type'   => 'fieldset',
                'childs' => [
                    new fieldList('from_template', [
                        'title' => LANG_CP_WIDGETS_FROM_TEMPLATE,
                        'generator' => function($item) use($to_template_name) {
                            $items = [];

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
                                        $items[$tpl] = !empty($manifest['title']) ? $manifest['title'] : $tpl;
                                    }
                                }
                            }

                            return $items;
                        }
                    ]),
                    new fieldCheckbox('copy_widgets', array(
                        'title' => LANG_CP_WIDGETS_COPY_WIDGETS
                    ))
                ]
            ]
        ];
    }

}
