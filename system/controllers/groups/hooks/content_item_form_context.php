<?php

class onGroupsContentItemFormContext extends cmsAction {

    public function run($data) {

        list($form, $item, $ctype, $action, $container_data) = $data;

        // Запрещено создавать в группах (сообществах)
        if (!$ctype['is_in_groups'] && !$ctype['is_in_groups_only']){
            return $data;
        }

        $group_id = $this->request->get('group_id', 0);

        // Создание только в группах
        if($ctype['is_in_groups_only']){

            $groups_rules = [['required']];

            $groups_list = [];
        } else {

            $groups_rules = [];

            $groups_list = ['0' => ''];
        }

        if($this->cms_user->is_admin){
            $groups = $this->model->getGroups();
        } else {
            $groups = $this->model->getUserGroups(($action == 'add' ? $this->cms_user->id : $item['user_id']));
        }

        if($groups){
            $groups_list += array_collection_to_list($groups, 'id', 'title');

            // если вне групп добавление записей запрещено, даём выбор только одной группы
            if($group_id && $action == 'add' && !cmsUser::isAllowed($ctype['name'], 'add') && isset($groups_list[$group_id])){

                $groups_list = [$group_id => $groups_list[$group_id]];

                $groups_rules = [['required']];
            }
        }

        // Добавляем поле выбора группы
        if (($action == 'add' || $this->cms_user->is_admin) && $groups){

            $fieldset_id = $form->addFieldsetToBeginning(LANG_GROUP, 'group_wrap', ['is_collapsed' => !empty($ctype['options']['is_collapsed']) && in_array('group_wrap', $ctype['options']['is_collapsed'])]);

            $form->addField($fieldset_id,
                new fieldList('parent_id', [
                        'items' => $groups_list,
                        'default' => $this->request->get('group_id', 0),
                        'rules' => $groups_rules
                    ]
                )
            );
        }

        // Другой глубиномер
        if($group_id && $groups && isset($groups[$group_id])){
            $item['parent_id'] = $group_id;
        }
        if(!empty($item['parent_id']) && isset($groups[$item['parent_id']])){

            $group = $groups[$item['parent_id']];

            $this->cms_template->addBreadcrumb(LANG_GROUPS, href_to('groups'));
            $this->cms_template->addBreadcrumb($group['title'], href_to('groups', $group['slug']));
            if ($ctype['options']['list_on']){
                $this->cms_template->addBreadcrumb((empty($ctype['labels']['profile']) ? $ctype['title'] : $ctype['labels']['profile']), href_to('groups', $group['slug'], array('content', $ctype['name'])));
            }

        }

        return [$form, $item, $ctype, $action, $data];
    }

}
