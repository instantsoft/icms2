<?php
class formAdminContentCategory extends cmsForm {

    public function init($ctype) {

        return array(

            'basic' => array(
                'type' => 'fieldset',
                'childs' => array(

                    new fieldList('parent_id', array(
                        'title' => LANG_PARENT_CATEGORY,
                        'generator' => function($cat) use ($ctype){

                            $content_model = cmsCore::getModel('content');
                            $tree = $content_model->limit(0)->getCategoriesTree($ctype['name']);

                            if ($tree){
                                foreach($tree as $item){
                                    $items[$item['id']] = str_repeat('- ', $item['ns_level']).' '.$item['title'];
                                }
                            }

                            return $items;

                        }
                    )),

                    new fieldText('title', array(
                        'title' => LANG_CP_CONTENT_CATS_TITLES,
                        'hint' => LANG_CP_CONTENT_CATS_TITLES_HINT,
                        'is_strip_tags' => true,
                        'rules' => array(
                            array('required'),
                        )
                    )),

                    new fieldCheckbox('is_inherit_binds', array(
                        'title' => LANG_CP_CONTENT_CATS_BIND,
                    )),

                )
            )


        );

    }

}
