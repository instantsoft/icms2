<?php

class formAdminWidgetsCols extends cmsForm {

    public function init($do, $col_id, $row) {

        return [
            'basic' => [
                'type'   => 'fieldset',
                'childs' => [
                    new fieldHidden('row_id'),
                    new fieldString('title', [
                        'title' => LANG_TITLE,
                        'rules' => [
                            ['required'],
                            ['max_length', 255]
                        ]
                    ]),
                    new fieldString('name', [
                        'title' => LANG_CP_WIDGETS_COL_NAME,
                        'hint'  => LANG_CP_WIDGETS_COL_NAME_HINT,
                        'rules' => [
                            ['max_length', 50],
                            ['sysname'],
                            [function ($controller, $data, $value) use ($do, $col_id, $row) {

                                if (empty($value)) {
                                    return true;
                                }
                                if (!in_array(gettype($value), ['integer', 'string', 'double'])) {
                                    return ERR_VALIDATE_INVALID;
                                }

                                $model = new cmsModel();

                                $model->filterEqual('name', $value);
                                $model->filterEqual('r.template', $row['template']);

                                if ($col_id) {
                                    $model->filterNotEqual('id', $col_id);
                                }
                                $model->joinInner('layout_rows', 'r', 'r.id = i.row_id');
                                $count = $model->getCount('layout_cols');

                                if ($count) {
                                    return ERR_VALIDATE_UNIQUE;
                                }

                                return true;
                            }]
                        ]
                    ]),
                    new fieldList('type', [
                        'title'   => LANG_CP_WIDGETS_COL_TYPE,
                        'default' => 'typical',
                        'items'   => [
                            'typical' => LANG_CP_WIDGETS_COL_TYPE1,
                            'custom'  => LANG_CP_WIDGETS_COL_TYPE2
                        ]
                    ]),
                    new fieldHtml('wrapper', [
                        'title'   => LANG_CP_WIDGETS_COL_WRAPPER,
                        'hint'    => LANG_CP_WIDGETS_COL_WRAPPER_H,
                        'options' => [
                            'editor' => 'ace'
                        ],
                        'visible_depend' => ['type' => ['hide' => ['typical']]]
                    ]),
                    new fieldList('tag', [
                        'title'   => LANG_CP_WIDGETS_COL_TAG,
                        'default' => 'div',
                        'items'   => [
                            'div'     => '<div>',
                            'article' => '<article>',
                            'aside'   => '<aside>',
                            'main'    => '<main>',
                            'footer'  => '<footer>',
                            'header'  => '<header>',
                            'nav'     => '<nav>',
                            'section' => '<section>'
                        ],
                        'visible_depend' => ['type' => ['hide' => ['custom']]]
                    ]),
                    new fieldString('class', [
                        'title' => LANG_CP_WIDGETS_COL_CLASS,
                        'rules' => [
                            ['max_length', 100]
                        ],
                        'visible_depend' => ['type' => ['hide' => ['custom']]]
                    ]),
                    new fieldList('options:add_js_files', [
                        'title' => LANG_CP_WIDGETS_COL_ADD_JS,
                        'hint'  => sprintf(LANG_PARSER_TEMPLATE_HINT, 'js/', 'layout-col-'),
                        'is_chosen_multiple' => true,
                        'generator' => function () use($row) {
                            return cmsTemplate::getInstance()->getAvailableTemplatesFiles('js', 'layout-col-*.js', $row['template']);
                        }
                    ])
                ]
            ]
        ];
    }

}
