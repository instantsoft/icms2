<?php

class formSearchOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_SEARCH_IN_CTYPES,
                'childs' => array(

                    new fieldList('ctypes', array(
                        'is_multiple' => true,
                        'generator' => function(){
                            $content_model = cmsCore::getModel('content');
                            $ctypes = $content_model->getContentTypes();
                            $items = array_collection_to_list($ctypes, 'name', 'title');
                            return $items;
                        }
                    )),

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_SEARCH_PERPAGE,
                'childs' => array(

                    new fieldNumber('perpage', array(
                        'default' => 15,
                        'rules' => array(
                            array('required')
                        )
                    )),

                )
            ),

        );

    }

}
