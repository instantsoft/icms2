<?php

class onPhotosContentAlbumsBeforeList extends cmsAction {

    public function run($data) {

        list($ctype, $items) = $data;

        $ctype['photos_options'] = $this->options;

        $context = $this->cms_core->getUriData();

        if ($context['controller'] === 'groups' && $context['action']) {

            $group_controller = cmsCore::getController('groups');

            if (is_numeric($context['action'])) {
                $group = $group_controller->model->getGroup($context['action']);
            } else {
                $group = $group_controller->model->getGroupBySlug($context['action']);
            }

            if (!$group) {
                return [$ctype, $items];
            }

            $group['access'] = $group_controller->getGroupAccess($group);

            $can_add = $group_controller->isContentAddAllowed($ctype['name'], $group);

            if ($can_add) {

                $this->cms_template->addMenuItem('controller_actions_menu', [
                    'options' => ['class' => 'images', 'icon' => 'upload'],
                    'title'   => LANG_PHOTOS_UPLOAD,
                    'url'     => href_to($this->name, 'upload') . '?group_id=' . $group['id']
                ]);
            }
        } else if($context['controller'] !== 'frontpage') {

            if (cmsUser::isAllowed($ctype['name'], 'add')) {

                $this->cms_template->addToolButton([
                    'class' => 'images',
                    'icon'  => 'upload',
                    'title' => LANG_PHOTOS_UPLOAD,
                    'href'  => href_to($this->name, 'upload')
                ]);
            }
        }

        return [$ctype, $items];
    }

}
