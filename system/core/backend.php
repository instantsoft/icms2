<?php

class cmsBackend extends cmsController {

    private $h1 = '';

    public $maintained_ctype = false;

    public function __construct($request){

        $this->name = str_replace('backend', '', strtolower(get_called_class()));

        parent::__construct($request);

        $this->root_path = $this->root_path . 'backend/';

    }

    public function setH1($title) {

        if (is_array($title)){ $title = implode(' -> ', $title); }

        $this->h1 = ' -> '.$title;

    }

    public function getH1() {
        return $this->h1;
    }

//============================================================================//
//============================================================================//

    public function getBackendMenu(){
        return array();
    }

    public function getOptionsToolbar(){
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

        $i[$field] = $i[$field] ? false : true;

		$this->model->update($table, $item_id, array(
			$field => $i[$field]
		));

        $this->processCallback('actiontoggle_'.$table.'_'.$field, array($i));

		$this->cms_template->renderJSON(array(
			'error' => false,
			'is_on' => $i[$field]
		));

    }

//============================================================================//
//=========                  ОПЦИИ КОМПОНЕНТА                        =========//
//============================================================================//

    public function addControllerSeoOptions($form) {

        if($this->useSeoOptions){
            $form->addFieldset(LANG_ROOT_SEO, 'seo_basic', array(
                'childs' => array(
                    new fieldString('seo_keys', array(
                        'title' => LANG_SEO_KEYS,
                        'hint' => LANG_SEO_KEYS_HINT,
                        'options'=>array(
                            'max_length'=> 256,
                            'show_symbol_count'=>true
                        )
                    )),
                    new fieldText('seo_desc', array(
                        'title' => LANG_SEO_DESC,
                        'hint' => LANG_SEO_DESC_HINT,
                        'options'=>array(
                            'max_length'=> 256,
                            'show_symbol_count'=>true
                        )
                    ))
                )
            ));
        }

        if($this->useItemSeoOptions){
            $form->addFieldset(LANG_CP_SEOMETA, 'seo_items', array(
                'childs' => array(
                    new fieldString('tag_title', array(
                        'title' => LANG_CP_SEOMETA_ITEM_TITLE,
                        'hint' => LANG_CP_SEOMETA_ITEM_HINT
                    )),
                    new fieldString('tag_desc', array(
                        'title' => LANG_CP_SEOMETA_ITEM_DESC,
                        'hint'  => LANG_CP_SEOMETA_ITEM_HINT
                    )),
                    new fieldString('tag_h1', array(
                        'title' => LANG_CP_SEOMETA_ITEM_H1,
                        'hint'  => LANG_CP_SEOMETA_ITEM_HINT
                    ))
                )
            ));
        }

        return $form;

    }

    public function actionOptions(){

        if (empty($this->useDefaultOptionsAction)){ cmsCore::error404(); }

        $form = $this->getForm('options');
        if (!$form) { cmsCore::error404(); }

        $form = $this->addControllerSeoOptions($form);

        $options = cmsController::loadOptions($this->name);

        if ($this->request->has('submit')){

            $options = array_merge( $options, $form->parse($this->request, true) );
            $errors  = $form->validate($this, $options);

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
            'toolbar' => $this->getOptionsToolbar(),
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

        // добавляем правила доступа от типа контента, если контроллер на его основе
		$ctype = cmsCore::getModel('content')->getContentTypeByName($this->name);
        if ($ctype) {
            $rules = array_merge(cmsPermissions::getRulesList('content'), $rules);
        }

        list($rules, $values) = cmsEventsManager::hook("controller_{$this->name}_perms", array($rules, $values));

        $groups = cmsCore::getModel('users')->getGroups(false);

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
        $rules  = cmsPermissions::getRulesList($this->name);

        // добавляем правила доступа от типа контента, если контроллер на его основе
		$ctype = cmsCore::getModel('content')->getContentTypeByName($this->name);
        if ($ctype) {
            $rules = array_merge(cmsPermissions::getRulesList('content'), $rules);
        }

        list($rules, $values) = cmsEventsManager::hook("controller_{$this->name}_perms", array($rules, $values));

        $groups = cmsCore::getModel('users')->getGroups(false);

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

        cmsUser::addSessionMessage(LANG_CP_PERMISSIONS_SUCCESS, 'success');

        cmsPermissions::savePermissions($subject, $values);

        $this->redirectBack();

    }

//============================================================================//
//============================================================================//

}
