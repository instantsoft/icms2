<?php

class formSearchOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_SEARCH_IN_CTYPES,
                'childs' => array(

                    new fieldList('types', array(
                        'is_multiple' => true,
                        'generator' => function(){

                            $search_controllers = cmsEventsManager::hookAll('fulltext_search', cmsCore::getController('search'), []);

                            $items = array();

                            foreach ($search_controllers as $controller) {

                                $items = array_merge($items, $controller['sources']);

                            }

                            return $items;

                        },
                        'multiple_select_deselect' => true
                    )),

                    new fieldCheckbox('is_hash_tag', array(
                        'title' => LANG_SEARCH_IS_HASH_TAG
                    ))

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
                    ))

                )
            )

        );

    }

}
