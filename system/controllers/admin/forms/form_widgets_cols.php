<?php
class formAdminWidgetsCols extends cmsForm {

    public function init($do, $col_id, $row) {

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
                            array('sysname'),
                            array(function($controller, $data, $value) use($do, $col_id, $row){

                                if (empty($value)) { return true; }
                                if (!in_array(gettype($value), array('integer','string','double'))) { return ERR_VALIDATE_INVALID; }

                                $model = new cmsModel();

                                $model->filterEqual('name', $value);
                                $model->filterEqual('r.template', $row['template']);

                                if($col_id){
                                    $model->filterNotEqual('id', $col_id);
                                }
                                $model->joinInner('layout_rows', 'r', 'r.id = i.row_id');
                                $count = $model->getCount('layout_cols');

                                if ($count) { return ERR_VALIDATE_UNIQUE; }
                                return true;
                            })
                        )
                    )),
                    new fieldList('type', array(
                        'title' => LANG_CP_WIDGETS_COL_TYPE,
                        'default' => 'typical',
                        'items' => array(
                            'typical' => LANG_CP_WIDGETS_COL_TYPE1,
                            'custom'  => LANG_CP_WIDGETS_COL_TYPE2
                        )
                    )),
                    new fieldHtml('wrapper', array(
                        'title' => LANG_CP_WIDGETS_COL_WRAPPER,
                        'hint'  => LANG_CP_WIDGETS_COL_WRAPPER_H,
                        'options' => array(
                            'editor' => 'ace'
                        ),
                        'visible_depend' => array('type' => array('hide' => array('typical')))
                    )),
                    new fieldList('tag', array(
                        'title' => LANG_CP_WIDGETS_COL_TAG,
                        'default' => 'div',
                        'items' => array(
                            'div'     => '<div>',
                            'article' => '<article>',
                            'aside'   => '<aside>',
                            'main'    => '<main>',
                            'footer'  => '<footer>',
                            'header'  => '<header>',
                            'nav'     => '<nav>',
                            'section' => '<section>'
                        ),
                        'visible_depend' => array('type' => array('hide' => array('custom')))
                    )),
                    new fieldString('class', array(
                        'title' => LANG_CP_WIDGETS_COL_CLASS,
                        'rules' => array(
                            array('max_length', 100)
                        ),
                        'visible_depend' => array('type' => array('hide' => array('custom')))
                    ))
                )
            )
        );

    }

}
