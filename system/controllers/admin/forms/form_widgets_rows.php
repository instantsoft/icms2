<?php
class formAdminWidgetsRows extends cmsForm {

    public function init($do) {

        return [
            'basic' => [
                'type'  => 'fieldset',
                'title' => LANG_CP_BASIC,
                'childs' => [
                    new fieldHidden('template'),
                    new fieldString('title', [
                        'title' => LANG_TITLE,
                        'rules' => [
                            ['required'],
                            ['max_length', 255]
                        ]
                    ]),
                    new fieldNumber('cols_count', [
                        'title' => LANG_CP_WIDGETS_COL_COUNT,
                        'default' => 2,
                        'options' => [
                            'is_abs'  => true,
                            'is_ceil' => true
                        ],
                        'rules' => [
                            ['min', 1],
                            ['max', 12]
                        ]
                    ]),
                    new fieldList('nested_position', [
                        'title' => LANG_CP_WIDGETS_ROW_NESTED_POSITION,
                        'items' => [
                            'before' => LANG_CP_WIDGETS_ROW_NESTED_POSITION1,
                            'after'  => LANG_CP_WIDGETS_ROW_NESTED_POSITION2
                        ]
                    ]),
                    new fieldList('tag', [
                        'title'   => LANG_CP_WIDGETS_ROW_TAG,
                        'default' => 'div',
                        'items' => [
                            ''        => LANG_NO,
                            'article' => '<article>',
                            'aside'   => '<aside>',
                            'main'    => '<main>',
                            'div'     => '<div>',
                            'footer'  => '<footer>',
                            'header'  => '<header>',
                            'nav'     => '<nav>',
                            'section' => '<section>'
                        ]
                    ]),
                    new fieldString('class', [
                        'title' => LANG_CP_WIDGETS_ROW_CLASS,
                        'rules' => [
                            ['max_length', 100]
                        ],
                        'visible_depend' => ['tag' => ['hide' => ['']]]
                    ])
                ]
            ]
        ];
    }

}
