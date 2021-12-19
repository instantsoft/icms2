<?php

class formActivityOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_ACTIVITY_OPT_TYPES,
                'childs' => array(

                    new fieldList('types', array(
                        'is_multiple' => true,
                        'multiple_select_deselect' => true,
                        'generator' => function(){
                            $types = cmsCore::getModel('activity')->getTypes();
                            return array_collection_to_list($types, 'id', 'title');
                        }
                    )),

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_LIST_LIMIT,
                'childs' => array(

                    new fieldNumber('limit', array(
                        'default' => 15,
                        'rules' => [
                            ['required'],
                            ['min', 1]
                        ]
                    ))

                )
            )

        );

    }

}
