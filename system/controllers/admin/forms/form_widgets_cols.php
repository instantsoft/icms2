<?php
class formAdminWidgetsCols extends cmsForm {

    public function init($do, $col_id) {

        return array(
            'basic' => array(
                'type' => 'fieldset',
                'childs' => array(
                    new fieldHidden('row_id'),
                    new fieldString('title', array(
                        'title' => LANG_TITLE,
                        'rules' => array(
                            array('required'),
                            array('max_length', 255)
                        )
                    )),
                    new fieldString('name', array(
                        'title' => LANG_CP_WIDGETS_COL_NAME,
                        'hint' => LANG_CP_WIDGETS_COL_NAME_HINT,
                        'rules' => array(
                            array('max_length', 50),
                            $do == 'add' ? ['unique', 'layout_cols', 'name'] : ['unique_exclude', 'layout_cols', 'name', $col_id]
                        )
                    )),
                    new fieldCheckbox('is_body', array(
                        'title' => LANG_CP_WIDGETS_COL_IS_BODY
                    )),
                    new fieldCheckbox('is_breadcrumb', array(
                        'title' => LANG_CP_WIDGETS_COL_IS_BREADCRUMB
                    )),
                    new fieldString('class', array(
                        'title' => LANG_CP_WIDGETS_COL_CLASS,
                        'rules' => array(
                            array('max_length', 100)
                        )
                    ))
                )
            )
        );

    }

}
