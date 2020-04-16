<?php

class actionGroupsDatasets extends cmsAction {

    public function run($do = false, $id = 0){

        $admin = cmsCore::getController('admin', $this->request);

        // для добавления/редактирования вызываем экшены админки
        if ($do){

            $this->cms_template->setContext($admin);

            $html = $admin->runExternalAction('ctypes_datasets_'.$do, ($do === 'add' ? [$this->name] : [$id]));

            $this->cms_template->restoreContext();

            return $html;
        }

        $grid = $admin->loadDataGrid('ctype_datasets', [href_to('admin', 'ctypes', array('datasets_reorder', 'groups')), $this->cms_template->href_to('datasets', ['edit', '{id}'])]);

        if ($this->request->isAjax()) {

            $content_model = cmsCore::getModel('content');

            $content_model->orderBy('ordering', 'asc');

            $datasets = $content_model->getContentDatasets('groups');

            $this->cms_template->renderGridRowsJSON($grid, $datasets);

            $this->halt();

        }

        return $this->cms_template->render('backend/datasets', array(
            'grid' => $grid
        ));

    }

}
