<?php

class actionContentItemUnbind extends cmsAction {

    public function run(){

        $user = cmsUser::getInstance();

        $ctype_name       = $this->request->get('ctype_name', '');
        $child_ctype_name = $this->request->get('child_ctype_name', '');
        $item_id          = $this->request->get('id', 0);
        $selected_ids     = explode(',', $this->request->get('selected_ids', ''));

        if (!$ctype_name || !$child_ctype_name || !$item_id || !$selected_ids){
            cmsCore::error404();
        }

        if (!cmsUser::isAllowed($child_ctype_name, 'bind_off_parent')) {
            cmsCore::error404();
        }

        $ctype = $this->model->getContentTypeByName($ctype_name);
        if (!$ctype) { cmsCore::error404(); }

        $parent_item = $this->model->getContentItem($ctype_name, $item_id);
        if (!$parent_item) { cmsCore::error404(); }

        $child_ctype = $this->model->getContentTypeByName($child_ctype_name);
        if (!$child_ctype) { cmsCore::error404(); }

		$relation = $this->model->getContentRelationByTypes($ctype['id'], $child_ctype['id']);
		if (!$relation) { cmsCore::error404(); }

		$perm = cmsUser::getPermissionValue($child_ctype_name, 'bind_off_parent');

		foreach($selected_ids as $child_item_id){

			$child_item_id = intval(trim($child_item_id));
			if (!$child_item_id){ continue;}

			$child_item = $this->model->getContentItem($child_ctype_name, $child_item_id);
			if (!$child_item) { continue; }

			$is_allowed_to_unbind = $perm && (
									($perm == 'all') ||
									($perm == 'own' && $child_item['user_id'] == $user->id)
								) || $user->is_admin;

			if (!$is_allowed_to_unbind) { continue; }

			$this->model->unbindContentItemRelation(array(
                'parent_ctype_name' => $ctype['name'],
                'parent_ctype_id'   => $ctype['id'],
                'parent_item_id'    => $parent_item['id'],
                'child_ctype_name'  => $child_ctype['name'],
                'child_ctype_id'    => $child_ctype['id'],
                'child_item_id'     => $child_item['id']
            ));

		}

		$this->redirectBack();

    }

}
