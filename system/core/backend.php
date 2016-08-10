<?php

class cmsBackend extends cmsController {

    function __construct($request){

        $this->name = str_replace('backend', '', strtolower(get_called_class()));

        parent::__construct($request);

        $this->root_path = $this->root_path . 'backend/';

    }

//============================================================================//
//============================================================================//

    public function getBackendMenu(){
        return array();
    }

//============================================================================//
//============================================================================//
//=========              ШАБЛОНЫ ОСНОВНЫХ ДЕЙСТВИЙ                   =========//
//============================================================================//
//============================================================================//

//============================================================================//
//=========                Скрытие/показ записей                     =========//
//============================================================================//

    public function actionToggleItem($item_id=false, $table=false, $field='is_pub'){

		if (!$item_id || !$table || !is_numeric($item_id) || $this->validate_regexp("/^([a-z0-9\_{}]*)$/", urldecode($table)) !== true){
			$this->cms_template->renderJSON(array(
				'error' => true,
			));
		}

        $i = $this->model->getItemByField($table, 'id', $item_id);

		if (!$i || !array_key_exists($field, $i)){
			$this->cms_template->renderJSON(array(
				'error' => true,
			));
		}

        $active = $i[$field] ? false : true;

		$this->model->update($table, $item_id, array(
			$field => $active
		));

		$this->cms_template->renderJSON(array(
			'error' => false,
			'is_on' => $active
		));

    }

//============================================================================//
//=========                  ОПЦИИ КОМПОНЕНТА                        =========//
//============================================================================//

    public function actionOptions(){

        if (empty($this->useDefaultOptionsAction)){ cmsCore::error404(); }

        $form = $this->getForm('options');
        if (!$form) { cmsCore::error404(); }

        $form = cmsEventsManager::hook("form_options_{$this->name}", $form);

        $options = cmsController::loadOptions($this->name);

        if ($this->request->has('submit')){

            $options = $form->parse($this->request, true);
            $errors = $form->validate($this, $options);

            if (!$errors){

                cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

                cmsController::saveOptions($this->name, $options);

                $this->processCallback(__FUNCTION__, array($options));

                $this->redirectToAction('options');

            }

            if ($errors){

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        $template_params = array(
            'options' => $options,
            'form'    => $form,
            'errors'  => isset($errors) ? $errors : false
        );

        // если задан шаблон опций в контроллере
        if($this->cms_template->getTemplateFileName('controllers/'.$this->name.'/backend/options', true)){

            return $this->cms_template->render('backend/options', $template_params);

        } else {

            $default_admin_tpl = $this->cms_template->getTemplateFileName('controllers/admin/controllers_options');

            return $this->cms_template->processRender($default_admin_tpl, $template_params);

        }

    }

//============================================================================//
//=========                  УПРАВЛЕНИЕ ДОСТУПОМ                     =========//
//============================================================================//

    public function actionPerms($subject=''){

        if (empty($this->useDefaultPermissionsAction)){ cmsCore::error404(); }

        $rules  = cmsPermissions::getRulesList($this->name);
        $values = cmsPermissions::getPermissions($subject);

        $users_model = cmsCore::getModel('users');
        $groups = $users_model->getGroups(false);

        $template_params = array(
            'rules'   => $rules,
            'values'  => $values,
            'groups'  => $groups,
            'subject' => $subject
        );

        // если задан шаблон опций в контроллере
        if($this->cms_template->getTemplateFileName('controllers/'.$this->name.'/backend/perms', true)){

            return $this->cms_template->render('backend/perms', $template_params);

        } else {

            $default_admin_tpl = $this->cms_template->getTemplateFileName('controllers/admin/controllers_perms');

            return $this->cms_template->processRender($default_admin_tpl, $template_params);

        }

    }

    public function actionPermsSave($subject=''){

        if (empty($this->useDefaultPermissionsAction)){ cmsCore::error404(); }

        $values = $this->request->get('value', array());

        $rules = cmsPermissions::getRulesList($this->name);

        $users_model = cmsCore::getModel('users');
        $groups = $users_model->getGroups(false);

        // перебираем правила
        foreach($rules as $rule){

            // если для этого правила вообще ничего нет,
            // то присваиваем null
            if (!isset($values[$rule['id']])) {
                $values[$rule['id']] = null; continue;
            }

            // перебираем группы, заменяем на нуллы
            // значения отсутствующих правил
            foreach($groups as $group){
                if (!isset($values[$rule['id']][$group['id']])) {
                    $values[$rule['id']][$group['id']] = null;
                }
            }

        }

        cmsPermissions::savePermissions($subject, $values);

        $this->redirectBack();

    }

//============================================================================//
//============================================================================//

}