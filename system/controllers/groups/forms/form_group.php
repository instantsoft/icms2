<?php
class formGroupsGroup extends cmsForm {

    public function init() {

        return array(
            'basic' => array(
                'type' => 'fieldset',
                'childs' => array(

                    'title' => new fieldString('title', array(
                        'title' => LANG_GROUPS_GROUP_TITLE,
                        'rules' => array(
                            array('required'),
                            array('max_length', 128)
                        )
                    )),

                    'description' => new fieldHtml('description', array(
                        'title' => LANG_GROUPS_GROUP_DESC,
                        'options' => array(
                            'editor' => cmsConfig::get('default_editor')
                        )
                    )),

                    'logo' => new fieldImage('logo', array(
                        'title' => LANG_GROUPS_GROUP_LOGO,
                    )),

                    'join_policy' => new fieldList('join_policy', array(
                        'title' => LANG_GROUPS_GROUP_JOIN_POLICY,
                        'items' => array(
                            groups::JOIN_POLICY_FREE => LANG_GROUPS_GROUP_PUBLIC,
                            groups::JOIN_POLICY_PUBLIC => LANG_GROUPS_GROUP_PRIVATE_SOFT,
                            groups::JOIN_POLICY_PRIVATE => LANG_GROUPS_GROUP_PRIVATE_HARD,
                        )
                    )),

                    'is_closed' => new fieldList('is_closed', array(
                        'title' => LANG_GROUPS_GROUP_IS_CLOSED,
                        'items' => array(
                            0 => LANG_GROUPS_GROUP_OPENED,
                            1 => LANG_GROUPS_GROUP_CLOSED,
                        )
                    )),

                    'edit_policy' => new fieldList('edit_policy', array(
                        'title' => LANG_GROUPS_GROUP_EDIT_POLICY,
                        'items' => array(
                            groups::EDIT_POLICY_OWNER => LANG_GROUPS_GROUP_EDIT_OWNER,
                            groups::EDIT_POLICY_STAFF => LANG_GROUPS_GROUP_EDIT_STAFF,
                        )
                    )),

                    'wall_policy' => new fieldList('wall_policy', array(
                        'title' => LANG_GROUPS_GROUP_WALL_POLICY,
                        'items' => array(
                            groups::WALL_POLICY_MEMBERS => LANG_GROUPS_GROUP_WALL_MEMBERS,
                            groups::WALL_POLICY_STAFF => LANG_GROUPS_GROUP_WALL_STAFF,
                            groups::WALL_POLICY_OWNER => LANG_GROUPS_GROUP_WALL_OWNER,
                        )
                    )),

                )
            ),
        );

    }

}
