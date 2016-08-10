<?php

class actionAdminUsers extends cmsAction {

    public function run($do=false){

        // если нужно, передаем управление другому экшену
        if ($do){
            $this->runAction('users_'.$do, array_slice($this->params, 1));
            return;
        }

        $users_model = cmsCore::getModel('users');

        $groups = $users_model->getGroups();
        $groups = array_pad($groups, (sizeof($groups)+1)*-1, array('id'=>0, 'title'=>LANG_ALL));

        $grid = $this->loadDataGrid('users', false, 'admin.grid_filter.users');

        return cmsTemplate::getInstance()->render('users', array(
            'groups' => $groups,
            'grid' => $grid
        ));

    }

}
