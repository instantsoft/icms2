<?php

class onBootstrap4AdminColSchemeOptionsModern extends cmsAction {

	public function run($data){

        list($do, $row, $col) = $data;

        return [
            new fieldList('options:default_col_class', array(
                'title' => LANG_CP_WIDGETS_COL_WIDTH_D,
                'default' => 'col',
                'items' => array(
                    'col'    => LANG_AUTO,
                    'col-1'  => '8.33%',
                    'col-2'  => '16.67%',
                    'col-3'  => '25%',
                    'col-4'  => '33.33%',
                    'col-5'  => '41.67%',
                    'col-6'  => '50%',
                    'col-7'  => '58.33%',
                    'col-8'  => '66.67%',
                    'col-9'  => '75%',
                    'col-10' => '83.33%',
                    'col-11' => '91.67%',
                    'col-12' => '100%',
                    'col-auto' => LANG_CP_WIDGETS_COL_AUTO
                )
            )),
            new fieldList('options:sm_col_class', array(
                'title' => sprintf(LANG_CP_WIDGETS_COL_WIDTH, '≥576px'),
                'items' => array(
                    ''          => LANG_BY_DEFAULT,
                    'col-sm-1'  => '8.33%',
                    'col-sm-2'  => '16.67%',
                    'col-sm-3'  => '25%',
                    'col-sm-4'  => '33.33%',
                    'col-sm-5'  => '41.67%',
                    'col-sm-6'  => '50%',
                    'col-sm-7'  => '58.33%',
                    'col-sm-8'  => '66.67%',
                    'col-sm-9'  => '75%',
                    'col-sm-10' => '83.33%',
                    'col-sm-11' => '91.67%',
                    'col-sm-12' => '100%',
                    'col-sm-auto' => LANG_CP_WIDGETS_COL_AUTO
                )
            )),
            new fieldList('options:md_col_class', array(
                'title' => sprintf(LANG_CP_WIDGETS_COL_WIDTH, '≥768px'),
                'items' => array(
                    ''          => LANG_BY_DEFAULT,
                    'col-md-1'  => '8.33%',
                    'col-md-2'  => '16.67%',
                    'col-md-3'  => '25%',
                    'col-md-4'  => '33.33%',
                    'col-md-5'  => '41.67%',
                    'col-md-6'  => '50%',
                    'col-md-7'  => '58.33%',
                    'col-md-8'  => '66.67%',
                    'col-md-9'  => '75%',
                    'col-md-10' => '83.33%',
                    'col-md-11' => '91.67%',
                    'col-md-12' => '100%',
                    'col-md-auto' => LANG_CP_WIDGETS_COL_AUTO
                )
            )),
            new fieldList('options:lg_col_class', array(
                'title' => sprintf(LANG_CP_WIDGETS_COL_WIDTH, '≥992px'),
                'items' => array(
                    ''          => LANG_BY_DEFAULT,
                    'col-lg-1'  => '8.33%',
                    'col-lg-2'  => '16.67%',
                    'col-lg-3'  => '25%',
                    'col-lg-4'  => '33.33%',
                    'col-lg-5'  => '41.67%',
                    'col-lg-6'  => '50%',
                    'col-lg-7'  => '58.33%',
                    'col-lg-8'  => '66.67%',
                    'col-lg-9'  => '75%',
                    'col-lg-10' => '83.33%',
                    'col-lg-11' => '91.67%',
                    'col-lg-12' => '100%',
                    'col-lg-auto' => LANG_CP_WIDGETS_COL_AUTO
                )
            )),
            new fieldList('options:xl_col_class', array(
                'title' => sprintf(LANG_CP_WIDGETS_COL_WIDTH, '≥1200px'),
                'items' => array(
                    ''          => LANG_BY_DEFAULT,
                    'col-xl-1'  => '8.33%',
                    'col-xl-2'  => '16.67%',
                    'col-xl-3'  => '25%',
                    'col-xl-4'  => '33.33%',
                    'col-xl-5'  => '41.67%',
                    'col-xl-6'  => '50%',
                    'col-xl-7'  => '58.33%',
                    'col-xl-8'  => '66.67%',
                    'col-xl-9'  => '75%',
                    'col-xl-10' => '83.33%',
                    'col-xl-11' => '91.67%',
                    'col-xl-12' => '100%',
                    'col-xl-auto' => LANG_CP_WIDGETS_COL_AUTO
                )
            )),
            new fieldNumber('options:default_order', array(
                'title' => LANG_CP_WIDGETS_COL_D_ORDER,
                'options' => [
                    'is_abs' => true,
                    'is_ceil' => true
                ],
                'rules' => [
                    array('max', 12),
                ]
            )),
            new fieldNumber('options:sm_order', array(
                'title' => sprintf(LANG_CP_WIDGETS_COL_ORDER, '≥576px'),
                'options' => [
                    'is_abs' => true,
                    'is_ceil' => true
                ],
                'rules' => [
                    array('max', 12),
                ]
            )),
            new fieldNumber('options:md_order', array(
                'title' => sprintf(LANG_CP_WIDGETS_COL_ORDER, '≥768px'),
                'options' => [
                    'is_abs' => true,
                    'is_ceil' => true
                ],
                'rules' => [
                    array('max', 12),
                ]
            )),
            new fieldNumber('options:lg_order', array(
                'title' => sprintf(LANG_CP_WIDGETS_COL_ORDER, '≥992px'),
                'options' => [
                    'is_abs' => true,
                    'is_ceil' => true
                ],
                'rules' => [
                    array('max', 12),
                ]
            )),
            new fieldNumber('options:xl_order', array(
                'title' => sprintf(LANG_CP_WIDGETS_COL_ORDER, '≥1200px'),
                'options' => [
                    'is_abs' => true,
                    'is_ceil' => true
                ],
                'rules' => [
                    array('max', 12),
                ]
            ))
        ];

    }

}
