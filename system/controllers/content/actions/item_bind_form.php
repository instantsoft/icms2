<?php

class actionContentItemBindForm extends cmsAction {

    public function run(){

        cmsUser::getInstance();

        $ctype_name       = $this->request->get('ctype_name', '');
        $child_ctype_name = $this->request->get('child_ctype_name', '');
        $item_id          = $this->request->get('id', 0);
        $mode             = $this->request->get('mode', 'childs');
        $input_action     = $this->request->get('input_action', 'bind');
        $selected_ids     = $this->request->get('selected', '');

        if (!$ctype_name || !$child_ctype_name){
            cmsCore::error404();
        }

        if ($this->validate_sysname($ctype_name) !== true || $this->validate_sysname($child_ctype_name) !== true){
            cmsCore::error404();
        }

        $is_allowed_to_add = cmsUser::isAllowed($child_ctype_name, 'add_to_parent');
        $is_allowed_to_bind = cmsUser::isAllowed($child_ctype_name, 'bind_to_parent');
        $is_allowed_to_unbind = cmsUser::isAllowed($child_ctype_name, 'bind_off_parent');

        if ($mode != 'unbind' && (!$is_allowed_to_add && !$is_allowed_to_bind)) {
            cmsCore::error404();
        }

        if ($mode == 'unbind' && !$is_allowed_to_unbind) {
            cmsCore::error404();
        }

        // родительский тип контента
        $ctype = $this->model->getContentTypeByName($ctype_name);
        if (!$ctype) { cmsCore::error404(); }

        // дочерний контроллер
        $target_controller = 'content';

        // дочерний тип контента
        $child_ctype = $this->model->getContentTypeByName($child_ctype_name);
        // если нет, проверяем по контроллеру
        if (!$child_ctype) {
            if (cmsCore::isControllerExists($child_ctype) && cmsController::enabled($child_ctype)){

                if ($mode != 'parents') {
                    cmsCore::error404();
                }

                $child_ctype = array(
                    'name' => $child_ctype_name,
                    'id'   => null
                );

                $target_controller = $child_ctype_name;

            } else {
                cmsCore::error404();
            }
        }

        // связь
		$relation = $this->model->getContentRelationByTypes($ctype['id'], $child_ctype['id'], $target_controller);
		if (!$relation) { cmsCore::error404(); }

        $perm_bind_to_parent = cmsUser::getPermissionValue($child_ctype_name, 'bind_to_parent');

        // сама запись
		$item = array('id' => 0);

		if ($item_id){
			if ($mode == 'childs' || $mode == 'unbind'){
				$item = $this->model->getContentItem($ctype_name, $item_id);
			} else {

                if($relation['target_controller'] != 'content'){
                    $item = cmsCore::getModel($relation['target_controller'])->getContentItem($item_id);
                } else {
                    $item = $this->model->getContentItem($child_ctype_name, $item_id);
                }

			}
			if (!$item) { cmsCore::error404(); }
		}

		if ($mode == 'childs' || $mode == 'unbind'){

            if($relation['target_controller'] != 'content'){
                $this->model->setTablePrefix('');
            }

			$fields = $this->model->getContentFields($child_ctype_name);

		}

		if ($mode == 'parents'){

            $this->model->setTablePrefix(cmsModel::DEFAULT_TABLE_PREFIX);

			$fields = $this->model->getContentFields($ctype_name);

		}

        $fields = cmsEventsManager::hook('ctype_content_fields', $fields);

		$filter_fields = array();

		foreach($fields as $field){
			if ($field['handler']->filter_type == 'str'){
				$filter_fields[$field['name']] = $field['title'];
			}
		}

		$filter_fields['id'] = 'ID';

        $show_all_tab = $show_my_tab = $is_allowed_to_bind;

        // показываем родительские записи
        if ($mode == 'parents'){
            $show_all_tab = !in_array($perm_bind_to_parent, array('all_to_own', 'own_to_own', 'other_to_own'));
            $show_my_tab  = !in_array($perm_bind_to_parent, array('own_to_other', 'other_to_other', 'all_to_other'));
        }

        // показываем дочерние записи
        if ($mode == 'childs'){
            $show_all_tab = !in_array($perm_bind_to_parent, array('own_to_all', 'own_to_own', 'own_to_other'));
            $show_my_tab  = !in_array($perm_bind_to_parent, array('other_to_own', 'other_to_other', 'other_to_all'));
        }

        if($this->cms_user->is_admin){
            $show_all_tab = true;
        }

        return $this->cms_template->render('item_bind_form', array(
			'show_all_tab'  => $show_all_tab,
			'show_my_tab'   => $show_my_tab,
			'mode'          => $mode,
            'ctype'         => $ctype,
            'child_ctype'   => $child_ctype,
            'item'          => $item,
            'input_action'  => $input_action,
            'selected_ids'  => $selected_ids,
            'filter_fields' => $filter_fields
        ));

    }

}
