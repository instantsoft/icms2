<?php

class formGroupsInviteUsers extends cmsForm {

    public function init($group) {

        return array(

            'basic' => array(
                'type' => 'fieldset',
                'childs' => array(
                    new fieldHidden('is_submit'),
                    new fieldList('users_list', array(
                        'title' => LANG_GROUPS_SELECT_USERS_LIST,
                        'is_chosen_multiple' => true,
                        'generator' => function () use ($group) {
                            $model = cmsCore::getModel('groups');
                            $users = $model->getInvitableUsers($group['id']);
                            $items = array('');
                            if($users){ foreach($users as $user) { $items[$user['id']] = $user['nickname'].' ('.$user['email'].')'; } }
                            return $items;
                        }
                    ))
                )
            )

        );

    }

}
