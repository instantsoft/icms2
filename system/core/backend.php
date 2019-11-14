<?php

class cmsBackend extends cmsController {

    public $maintained_ctype = false;

    protected $backend_menu = array();
    protected $backend_sub_menu = array();

    public $queue = array(
        'queues'           => array(),
        'queue_name'       => '',
        'use_queue_action' => false
    );

    protected $useDefaultModerationAction = false;
    protected $useModerationTrash = false;

    public function __construct( cmsRequest $request){

        $this->name = str_replace('backend', '', strtolower(get_called_class()));

        parent::__construct($request);

        $this->root_path = $this->root_path . 'backend/';

        // Устанавливаем корень для URL внутри бэкенда
        $admin_controller_url = 'admin';
        $controller_alias = cmsCore::getControllerAliasByName($admin_controller_url);
        if ($controller_alias) { $admin_controller_url = $controller_alias; }
        $this->setRootURL($admin_controller_url.'/controllers/edit/'.$this->name);

        if(!empty($this->queue['use_queue_action'])){
            $this->backend_menu[] = array(
                'title' => sprintf(LANG_CP_QUEUE_TITLE, $this->queue['queue_name']),
                'url'   => href_to($this->root_url, 'queue')
            );
        }

        if(!empty($this->useDefaultModerationAction)){
            $this->backend_menu[] = array(
                'title' => LANG_MODERATORS,
                'url'   => href_to($this->root_url, 'moderators')
            );
        }

    }

    public function setCurrentAction($action_name) {

        $this->current_action = $action_name;
        $this->current_template_name = 'backend/'.$action_name;

        return $this;
    }

//============================================================================//
//============================================================================//

    public function getBackendSubMenu(){
        return $this->backend_sub_menu;
    }

    public function getBackendMenu(){
        return $this->backend_menu;
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

    public function actionToggleItem($item_id = false, $table = false, $field = 'is_pub', $zero_as_null = false) {

        if (!$item_id || !$table || !is_numeric($item_id) || $this->validate_regexp("/^([a-z0-9\_{}]*)$/", urldecode($table)) !== true){
			return $this->cms_template->renderJSON(array(
				'error' => true
			));
		}

		if (!$this->model->db->isTableExists($table)){
			return $this->cms_template->renderJSON(array(
				'error' => true
			));
		}

        $i = $this->model->getItemByField($table, 'id', $item_id);

		if (!$i || !array_key_exists($field, $i)){
			return $this->cms_template->renderJSON(array(
				'error' => true
			));
		}

        $i[$field] = $i[$field] ? ($zero_as_null ? null : 0) : 1;

		$this->model->update($table, $item_id, array(
			$field => $i[$field]
		));

        $this->processCallback('actiontoggle_'.$table.'_'.$field, array($i));

		return $this->cms_template->renderJSON(array(
			'error' => false,
			'is_on' => intval($i[$field])
		));

    }

//============================================================================//
//=========                  ОПЦИИ КОМПОНЕНТА                        =========//
//============================================================================//

    public function addControllerSeoOptions($form) {

        if($this->useSeoOptions){
            $form->addFieldset(LANG_ROOT_SEO, 'seo_basic', array(
                'childs' => array(
                    new fieldText('seo_desc', array(
                        'title' => LANG_SEO_DESC,
                        'hint'  => LANG_SEO_DESC_HINT,
                        'is_strip_tags' => true,
                        'options'=>array(
                            'max_length'        => 256,
                            'show_symbol_count' => true
                        )
                    ))
                )
            ));

            if (!$this->cms_config->disable_metakeys) {
                $form->addFieldToBeginning('seo_basic',
                    new fieldString('seo_keys', array(
                        'title'   => LANG_SEO_KEYS,
                        'hint'    => LANG_SEO_KEYS_HINT,
                        'options' => array(
                            'max_length'        => 256,
                            'show_symbol_count' => true
                        )
                    ))
                );
            }
        }

        if($this->useItemSeoOptions){

            $meta_item_fields = [];
            if(method_exists($this, 'getMetaItemFields')){
                $meta_item_fields = $this->getMetaItemFields();
            }

            $form->addFieldset(LANG_CP_SEOMETA, 'seo_items', array(
                'childs' => array(
                    new fieldString('tag_title', array(
                        'title' => LANG_CP_SEOMETA_ITEM_TITLE,
                        'patterns_hint' => ($meta_item_fields ? [ 'patterns' =>  $meta_item_fields ] : '')
                    )),
                    new fieldString('tag_desc', array(
                        'title' => LANG_CP_SEOMETA_ITEM_DESC,
                        'patterns_hint' => ($meta_item_fields ? [ 'patterns' =>  $meta_item_fields ] : '')
                    )),
                    new fieldString('tag_h1', array(
                        'title' => LANG_CP_SEOMETA_ITEM_H1,
                        'patterns_hint' => ($meta_item_fields ? [ 'patterns' =>  $meta_item_fields ] : '')
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

                $options = cmsEventsManager::hook("controller_{$this->name}_before_save_options", $options);

                cmsController::saveOptions($this->name, $options);

                $this->processCallback(__FUNCTION__, array($options));

                cmsEventsManager::hook("controller_{$this->name}_after_save_options", $options);

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
        if ($ctype && $subject == $this->name) {
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
//=========                           Очереди                        =========//
//============================================================================//

    public function actionQueue(){

        if (empty($this->queue['use_queue_action'])){ cmsCore::error404(); }

        $grid = $this->controller_admin->loadDataGrid('queue', array('contex_controller' => $this));

        if ($this->request->isAjax()) {

            $filter     = array();
            $filter_str = $this->request->get('filter', '');

            if($filter_str){
                parse_str($filter_str, $filter);
            }

            $this->controller_admin->model->filterIn('queue', $this->queue['queues']);

            $total = $this->controller_admin->model->getCount(cmsQueue::getTableName());

            $perpage = isset($filter['perpage']) ? $filter['perpage'] : admin::perpage;
            $page    = isset($filter['page']) ? intval($filter['page']) : 1;

            $pages = ceil($total / $perpage);

            $this->controller_admin->model->limitPage($page, $perpage);

            $this->controller_admin->model->orderByList(array(
                array('by' => 'date_started', 'to' => 'asc'),
                array('by' => 'priority', 'to' => 'desc'),
                array('by' => 'date_created', 'to' => 'asc')
            ));

            $jobs = $this->controller_admin->model->get(cmsQueue::getTableName());

            $this->cms_template->renderGridRowsJSON($grid, $jobs, $total, $pages);

            $this->halt();

        }

        $template_params = array(
            'grid'       => $grid,
            'page_title' => sprintf(LANG_CP_QUEUE_TITLE, $this->queue['queue_name']),
            'source_url' => href_to($this->root_url, 'queue'),
        );

        return $this->cms_template->processRender($this->cms_template->getTemplateFileName('assets/ui/grid'), $template_params);

    }

    public function actionQueueRestart($job_id){

        if (empty($this->queue['use_queue_action'])){ cmsCore::error404(); }

        cmsQueue::restartJob(array('id' => $job_id));

        $this->redirectBack();

    }

    public function actionQueueDelete($job_id){

        if (empty($this->queue['use_queue_action'])){ cmsCore::error404(); }

        $csrf_token = $this->request->get('csrf_token', '');
        if (!cmsForm::validateCSRFToken( $csrf_token )){
            cmsCore::error404();
        }

        cmsQueue::deleteJob(array('id' => $job_id));

        $this->redirectBack();

    }

    //============================================================================//
    //=========                         Модераторы                       =========//
    //============================================================================//

    public function actionModerators(){

        if (empty($this->useDefaultModerationAction)){ cmsCore::error404(); }

        $moderators = $this->model_moderation->getContentTypeModerators($this->name);

        $template_params = array(
            'title'         => $this->title,
            'not_use_trash' => !$this->useModerationTrash,
            'moderators'    => $moderators
        );

        $this->cms_template->addToolButton(array(
            'class'  => 'settings',
            'title'  => LANG_MODERATORATION_OPTIONS,
            'href'   => href_to('admin', 'controllers', array('edit', 'moderation', 'options'))
        ));

        $this->cms_template->addToolButton(array(
            'class'  => 'help',
            'title'  => LANG_HELP,
            'target' => '_blank',
            'href'   => LANG_HELP_URL_CTYPES_MODERATORS
        ));

        // если задан шаблон в контроллере
        if($this->cms_template->getTemplateFileName('controllers/'.$this->name.'/backend/moderators', true)){

            return $this->cms_template->render('backend/moderators', $template_params);

        } else {

            $default_admin_tpl = $this->cms_template->getTemplateFileName('controllers/admin/controllers_moderators');

            return $this->cms_template->processRender($default_admin_tpl, $template_params);

        }

    }

    public function actionModeratorsAdd(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $name = $this->request->get('name', '');
        if (!$name) { cmsCore::error404(); }

        $user = cmsCore::getModel('users')->filterEqual('email', $name)->getUser();

        if ($user === false){
            return $this->cms_template->renderJSON(array(
                'error'   => true,
                'message' => sprintf(LANG_CP_USER_NOT_FOUND, $name)
            ));
        }

        $moderators = $this->model_moderation->getContentTypeModerators($this->name);

        if (isset($moderators[$user['id']])){
            return $this->cms_template->renderJSON(array(
                'error'   => true,
                'message' => sprintf(LANG_MODERATOR_ALREADY, $user['nickname'])
            ));
        }

        $moderator = $this->model_moderation->addContentTypeModerator($this->name, $user['id']);

        if (!$moderator){
            return $this->cms_template->renderJSON(array(
                'error'   => true,
                'message' => LANG_ERROR
            ));
        }

        $ctypes_moderator_tpl = $this->cms_template->getTemplateFileName('controllers/admin/ctypes_moderator');

        return $this->cms_template->renderJSON(array(
            'error' => false,
            'name'  => $user['nickname'],
            'html'  => $this->cms_template->processRender($ctypes_moderator_tpl, array(
                'moderator' => $moderator,
                'not_use_trash' => !$this->useModerationTrash,
                'ctype'     => array('name' => $this->name, 'controller' => $this->name)
            ), new cmsRequest(array(), cmsRequest::CTX_INTERNAL)),
            'id'    => $user['id']
        ));

    }

    public function actionModeratorsDelete(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $id = $this->request->get('id', 0);
        if (!$id) { cmsCore::error404(); }

        $moderators = $this->model_moderation->getContentTypeModerators($this->name);

        if (!isset($moderators[$id])){
            return $this->cms_template->renderJSON(array(
                'error' => true
            ));
        }

        $this->model_moderation->deleteContentTypeModerator($this->name, $id);

        return $this->cms_template->renderJSON(array(
            'error' => false
        ));

    }

}
