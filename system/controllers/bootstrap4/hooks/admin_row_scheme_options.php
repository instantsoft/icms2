<?php

class onBootstrap4AdminRowSchemeOptions extends cmsAction {

	public function run($data){

        list($do, $row, $col) = $data;

        $template = new cmsTemplate($row['template']);

        $manifest = $template->getManifest();

        if(empty($manifest['properties']['vendor'])){
            return false;
        }

        // Нам нужны только шаблоны на bootstrap4
        if($manifest['properties']['vendor'] !== 'bootstrap4'){
            return false;
        }

        $fields = [
            new fieldCheckbox('options:no_gutters', array(
                'title' => LANG_CP_WIDGETS_ROW_NO_GUTTERS,
                'visible_depend' => array('tag' => array('hide' => array('')))
            )),
            new fieldList('options:vertical_align', array(
                'title' => LANG_CP_WIDGETS_COL_VA,
                'items' => array(
                    '' => LANG_BY_DEFAULT,
                    'align-items-start'   => LANG_CP_WIDGETS_COL_VA1,
                    'align-items-center'  => LANG_CP_WIDGETS_COL_VA2,
                    'align-items-end'     => LANG_CP_WIDGETS_COL_VA3
                ),
                'visible_depend' => array('tag' => array('hide' => array('')))
            )),
            new fieldList('options:horizontal_align', array(
                'title' => LANG_CP_WIDGETS_COL_HA,
                'items' => array(
                    '' => LANG_BY_DEFAULT,
                    'justify-content-start'   => LANG_CP_WIDGETS_COL_HA1,
                    'justify-content-center'  => LANG_CP_WIDGETS_COL_VA2,
                    'justify-content-end'     => LANG_CP_WIDGETS_COL_HA3,
                    'justify-content-around'  => LANG_CP_WIDGETS_COL_HA4,
                    'justify-content-between' => LANG_CP_WIDGETS_COL_HA5
                ),
                'visible_depend' => array('tag' => array('hide' => array('')))
            )),
            new fieldList('options:container', array(
                'title' => LANG_CP_WIDGETS_ROW_CONT,
                'default' => 'container',
                'items' => array(
                    'container'       => '100% <576px',
                    'container-md'    => '100% <768px',
                    'container-lg'    => '100% <992px',
                    'container-xl'    => '100% <1200px',
                    'container-fluid' => '100%',
                    ''                => LANG_CP_WIDGETS_ROW_CONT_NO
                )
            )),
            new fieldList('options:container_tag', array(
                'title' => LANG_CP_WIDGETS_ROW_CONT_TAG,
                'default' => 'div',
                'items' => array(
                    'article' => '<article>',
                    'aside'   => '<aside>',
                    'main'    => '<main>',
                    'div'     => '<div>',
                    'footer'  => '<footer>',
                    'header'  => '<header>',
                    'nav'     => '<nav>',
                    'section' => '<section>'
                ),
                'visible_depend' => array('options:container' => array('hide' => array('')))
            )),
            new fieldString('options:container_tag_class', array(
                'title' => LANG_CP_WIDGETS_ROW_CONT_CSS,
                'rules' => array(
                    array('max_length', 255)
                ),
                'visible_depend' => array('options:container' => array('hide' => array('')))
            )),
            new fieldList('options:parrent_tag', array(
                'title' => LANG_CP_WIDGETS_ROW_PARRENT_TAG,
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
            new fieldString('options:parrent_tag_class', array(
                'title' => LANG_CP_WIDGETS_ROW_PARRENT_TAG_C,
                'rules' => array(
                    array('max_length', 255)
                ),
                'visible_depend' => array('options:parrent_tag' => array('hide' => array('')))
            ))
        ];

        return $fields;

    }

}
