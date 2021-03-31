<?php

class onGroupsContentBeforeItem extends cmsAction {

    public function run($data){

        list($ctype, $item, $fields) = $data;

        if (!empty($item['parent_id']) && !$ctype['is_in_groups_only']){

            $group = $this->model->getGroup($item['parent_id']);

            if ($group){

                $group['access'] = $this->getGroupAccess($group);

                // администраторы групп могут отвязывать контент
                if ($group['access']['member_role'] == groups::ROLE_STAFF) {

                    $this->cms_template->addToolButton(array(
                        'class' => 'newspaper_delete ajax-modal',
                        'icon'  => 'unlink',
                        'title' => LANG_GROUPS_UNBIND,
                        'href'  => href_to($this->name, $group['slug'], array('unbind', $ctype['name'], $item['id']))
                    ));

                }

            }

        }

        return array($ctype, $item, $fields);

    }

}
