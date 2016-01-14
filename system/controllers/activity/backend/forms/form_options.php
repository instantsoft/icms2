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
                        'generator' => function(){
                            $types = cmsCore::getModel('activity')->getTypes();
                            return array_collection_to_list($types, 'id', 'title');
                        }
                    )),

                )
            ),

        );

    }

}
