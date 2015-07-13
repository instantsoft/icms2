<?php
class formContentCategory extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'childs' => array(

                    new fieldString('title', array(
                        'title' => LANG_CATEGORY_TITLE,
                        'rules' => array(
                            array('required'),
                            array('max_length', 64)
                        )
                    )),

                    new fieldList('parent_id', array(
                        'title' => LANG_PARENT_CATEGORY,
                        'generator' => function($cat){

                            $content_model = cmsCore::getModel('content');
                            $tree = $content_model->getCategoriesTree($cat['ctype_name']);

                            if ($tree){
                                foreach($tree as $item){

                                    // при редактировании исключаем себя и вложенные
                                    // подкатегории из списка выбора родителя
                                    if (isset($cat['ns_left'])){
                                        if ($item['ns_left'] >= $cat['ns_left'] && $item['ns_right'] <= $cat['ns_right']){
                                            continue;
                                        }
                                    }

                                    $items[$item['id']] = str_repeat('- ', $item['ns_level']).' '.$item['title'];

                                }
                            }

                            return $items;

                        }
                    )),

                )
            )


        );

    }

}
