<?php
class formAdminWidgetsRows extends cmsForm {

    public function init($do) {

        return array(
            'basic' => array(
                'type' => 'fieldset',
                'title' => LANG_CP_BASIC,
                'childs' => array(
                    new fieldHidden('template'),
                    new fieldString('title', array(
                        'title' => LANG_TITLE,
                        'rules' => array(
                            array('required'),
                            array('max_length', 255)
                        )
                    )),
                    new fieldNumber('cols_count', array(
                        'title' => LANG_CP_WIDGETS_COL_COUNT,
                        'default' => 2,
                        'options' => [
                            'is_abs' => true,
                            'is_ceil' => true
                        ],
                        'rules' => [
                            array('min', 1),
                            array('max', 12),
                        ]
                    )),
                    new fieldList('nested_position', array(
                        'title' => LANG_CP_WIDGETS_ROW_NESTED_POSITION,
                        'items' => array(
                            'before'  => LANG_CP_WIDGETS_ROW_NESTED_POSITION1,
                            'after' => LANG_CP_WIDGETS_ROW_NESTED_POSITION2
                        )
                    )),
                    new fieldList('tag', array(
                        'title' => LANG_CP_WIDGETS_ROW_TAG,
                        'default' => 'div',
                        'items' => array(
                            ''        => LANG_NO,
                            'article' => '<article>',
                            'aside'   => '<aside>',
                            'main'    => '<main>',
                            'div'     => '<div>',
                            'footer'  => '<footer>',
                            'header'  => '<header>',
                            'nav'     => '<nav>',
                            'section' => '<section>'
                        )
                    )),
                    new fieldString('class', array(
                        'title' => LANG_CP_WIDGETS_ROW_CLASS,
                        'rules' => array(
                            array('max_length', 100)
                        ),
                        'visible_depend' => array('tag' => array('hide' => array('')))
                    ))
                )
            )
        );

    }

}
