<?php

class formWidgetGroupsListOptions extends cmsForm {

    public function init($options = false) {

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_OPTIONS,
                'childs' => array(

                    new fieldList('options:widget_type', array(
                        'title' => LANG_WD_GROUPS_WIDGET_TYPE,
                        'default' => 'list',
                        'items' => array(
                            'list'    => LANG_WD_GROUPS_WIDGET_TYPE1,
                            'related' => LANG_WD_GROUPS_WIDGET_TYPE2
                        )
                    )),

                    new fieldList('options:dataset_id', array(
                        'title' => LANG_WD_GROUPS_LIST_DATASET,
                        'generator' => function (){

                            $datasets_list = array('0' => '');

                            $content_model = cmsCore::getModel('content');

                            $datasets = $content_model->getContentDatasets('groups');
                            if ($datasets){ $datasets_list += array_collection_to_list($datasets, 'id', 'title'); }

                            return $datasets_list;

                        }
                    )),

                    new fieldList('options:fields_is_in_list', array(
                        'title' => LANG_WD_GROUPS_FIELDS,
                        'is_chosen_multiple' => true,
                        'default' => array(1,2,3),
                        'generator' => function (){

                            $fields = cmsCore::getModel('content')->setTablePrefix('')->orderBy('ordering')->getContentFields('groups');

                            return array_collection_to_list($fields, 'id', 'title');

                        },
                        'rules' => array(
                            array('required')
                        )
                    )),

                    new fieldCheckbox('options:show_members_count', array(
                        'title' => LANG_WD_GROUPS_SHOW_MEMBERS_COUNT,
                        'default' => 1
                    )),

                    new fieldNumber('options:limit', array(
                        'title' => LANG_LIST_LIMIT,
                        'default' => 10,
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
