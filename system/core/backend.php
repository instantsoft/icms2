<?php
class cmsBackend extends cmsController {

    function __construct($request){

        $this->name = str_replace('backend', '', mb_strtolower(get_called_class()));

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
			cmsTemplate::getInstance()->renderJSON(array(
				'error' => true,
			));
		}

        $i = $this->model->getItemByField($table, 'id', $item_id);

		if (!$i || !array_key_exists($field, $i)){
			cmsTemplate::getInstance()->renderJSON(array(
				'error' => true,
			));
		}

        $active = $i[$field] ? false : true;

		$this->model->update($table, $item_id, array(
			$field => $active
		));

		cmsTemplate::getInstance()->renderJSON(array(
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

        $is_submitted = $this->request->has('submit');

        $options = cmsController::loadOptions($this->name);

        if ($is_submitted){

            $options = $form->parse($this->request, $is_submitted);
            $errors = $form->validate($this, $options);

            if (!$errors){

                cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

                cmsController::saveOptions($this->name, $options);

                $this->redirectToAction('options');

            }

            if ($errors){

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        return cmsTemplate::getInstance()->render('backend/options', array(
            'options' => $options,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

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

        return cmsTemplate::getInstance()->render('backend/perms', array(
            'rules' => $rules,
            'values' => $values,
            'groups' => $groups,
            'subject' => $subject,
        ));

    }

    public function actionPermsSave($subject=''){

        if (empty($this->useDefaultPermissionsAction)){ cmsCore::error404(); }

        $values = $this->request->get('value');

        if (!$values) { cmsCore::error404(); }

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
