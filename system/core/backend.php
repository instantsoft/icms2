<?php

class cmsBackend extends cmsController {

    use icms\traits\eventDispatcher;

    public $maintained_ctype = false;

    protected $backend_menu = [];
    protected $backend_sub_menu = [];

    protected $useDefaultModerationAction = false;

    protected $useModerationTrash = false;

    public function __construct(cmsRequest $request) {

        $this->name = str_replace('backend', '', strtolower(get_called_class()));

        parent::__construct($request);

        $this->root_path .= 'backend/';

        // Устанавливаем корень для URL внутри бэкенда
        $admin_controller_url = 'admin';
        $controller_alias     = cmsCore::getControllerAliasByName($admin_controller_url);
        if ($controller_alias) {
            $admin_controller_url = $controller_alias;
        }
        $this->setRootURL($admin_controller_url . '/controllers/edit/' . $this->name);

        if (!empty($this->useDefaultModerationAction)) {
            $this->backend_menu[] = [
                'title' => LANG_MODERATORS,
                'url'   => href_to($this->root_url, 'moderators'),
                'options' => [
                    'icon' => 'user-shield'
                ]
            ];
        }
    }

    public function setCurrentAction($action_name) {

        $this->current_action = $action_name;
        $this->current_template_name = 'backend/' . $action_name;

        return $this;
    }

//============================================================================//
//============================================================================//

    public function getBackendSubMenu() {
        return $this->backend_sub_menu;
    }

    public function getBackendMenu() {
        return $this->backend_menu;
    }

    public function getOptionsToolbar() {
        return [];
    }

//============================================================================//
//============================================================================//
//=========              ШАБЛОНЫ ОСНОВНЫХ ДЕЙСТВИЙ                   =========//
//============================================================================//
//============================================================================//

    /**
     * Экшен скрытия/показа записей
     *
     * @param integer $item_id ID записи в таблице
     * @param string $table Название таблицы
     * @param string $field Название поля
     * @param boolean $zero_as_null Нулевое значение сохранять как null
     * @return void
     */
    public function actionToggleItem($item_id = 0, $table = '', $field = 'is_pub', $zero_as_null = false) {

        if (!$item_id || !$table || !is_numeric($item_id) || $this->validate_regexp("/^([a-z0-9\_{}]*)$/", urldecode($table)) !== true) {
            return $this->cms_template->renderJSON([
                'error' => true
            ]);
        }

        if (!$this->model->db->isTableExists($table)) {
            return $this->cms_template->renderJSON([
                'error' => true
            ]);
        }

        $i = $this->model->getItemByField($table, 'id', $item_id);

        if (!$i || !array_key_exists($field, $i)) {
            return $this->cms_template->renderJSON([
                'error' => true
            ]);
        }

        $i[$field] = $i[$field] ? ($zero_as_null ? null : 0) : 1;

        $this->model->update($table, $item_id, [
            $field => $i[$field]
        ]);

        // Уведомляем слушателей
        $this->dispatchEvent('actiontoggle_' . $table . '_' . $field, [$i]);

        return $this->cms_template->renderJSON([
            'error' => false,
            'is_on' => intval($i[$field])
        ]);
    }

//============================================================================//
//=========                  ОПЦИИ КОМПОНЕНТА                        =========//
//============================================================================//

    public function addControllerSeoOptions($form) {

        if ($this->useSeoOptions) {

            $form->addFieldset(LANG_ROOT_SEO, 'seo_basic', [
                'childs' => [
                    new fieldText('seo_desc', [
                        'title' => LANG_SEO_DESC,
                        'hint'  => LANG_SEO_DESC_HINT,
                        'multilanguage' => true,
                        'is_strip_tags' => true,
                        'options' => [
                            'max_length' => 256,
                            'show_symbol_count' => true
                        ]
                    ])
                ]
            ]);

            if (!$this->cms_config->disable_metakeys) {
                $form->addFieldToBeginning('seo_basic',
                    new fieldString('seo_keys', [
                        'title'   => LANG_SEO_KEYS,
                        'hint'    => LANG_SEO_KEYS_HINT,
                        'multilanguage' => true,
                        'options' => [
                            'max_length' => 256,
                            'show_symbol_count' => true
                        ]
                    ])
                );
            }
        }

        if ($this->useItemSeoOptions) {

            $meta_item_fields = [];
            if (method_exists($this, 'getMetaItemFields')) {
                $meta_item_fields = $this->getMetaItemFields();
            }

            $form->addFieldset(LANG_CP_SEOMETA, 'seo_items', [
                'childs' => [
                    new fieldString('tag_title', [
                        'title' => LANG_CP_SEOMETA_ITEM_TITLE,
                        'multilanguage' => true,
                        'patterns_hint' => ($meta_item_fields ? ['patterns' => $meta_item_fields] : '')
                    ]),
                    new fieldString('tag_desc', [
                        'title' => LANG_CP_SEOMETA_ITEM_DESC,
                        'multilanguage' => true,
                        'patterns_hint' => ($meta_item_fields ? ['patterns' => $meta_item_fields] : '')
                    ]),
                    new fieldString('tag_h1', [
                        'title' => LANG_CP_SEOMETA_ITEM_H1,
                        'multilanguage' => true,
                        'patterns_hint' => ($meta_item_fields ? ['patterns' => $meta_item_fields] : '')
                    ])
                ]
            ]);
        }

        return $form;
    }

    /**
     * Экшен опций компонента
     *
     * @return string
     */
    public function actionOptions() {

        if (empty($this->useDefaultOptionsAction)) {
            return cmsCore::error404();
        }

        $options = cmsController::loadOptions($this->name);

        $form = $this->getForm('options', [$options]);
        if (!$form) {
            return cmsCore::error404();
        }

        $form = $this->addControllerSeoOptions($form);

        if ($this->request->has('submit')) {

            $options = array_merge($options, $form->parse($this->request, true, $options));
            $errors  = $form->validate($this, $options);

            if (!$errors) {

                cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

                $options = cmsEventsManager::hook("controller_{$this->name}_before_save_options", $options);

                cmsController::saveOptions($this->name, $options);

                $this->dispatchEvent('controller_save_options', [$options]);

                cmsEventsManager::hook("controller_{$this->name}_after_save_options", $options);

                if (!$this->isActionExists('index')) {
                    return $this->redirectToAction();
                }

                return $this->redirectToAction('options');
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        $template_params = [
            'toolbar' => $this->getOptionsToolbar(),
            'options' => $options,
            'form'    => $form,
            'errors'  => isset($errors) ? $errors : false
        ];

        // если задан шаблон опций в контроллере
        if ($this->cms_template->getTemplateFileName('controllers/' . $this->name . '/backend/options', true)) {
            return $this->cms_template->render('backend/options', $template_params);
        } else {

            $default_admin_tpl = $this->cms_template->getTemplateFileName('controllers/admin/controllers_options');

            return $this->cms_template->processRender($default_admin_tpl, $template_params);
        }
    }

//============================================================================//
//=========                  УПРАВЛЕНИЕ ДОСТУПОМ                     =========//
//============================================================================//

    /**
     * Экшен правил доступа
     *
     * @param string $subject
     * @return string
     */
    public function actionPerms($subject = '') {

        if (empty($this->useDefaultPermissionsAction)) {
            return cmsCore::error404();
        }

        $rules  = cmsPermissions::getRulesList($this->name);
        $values = cmsPermissions::getPermissions($subject);

        // добавляем правила доступа от типа контента, если контроллер на его основе
        $ctype = cmsCore::getModel('content')->getContentTypeByName($this->name);
        if ($ctype && $subject == $this->name) {
            $rules = array_merge(cmsPermissions::getRulesList('content'), $rules);
        }

        list($rules, $values) = cmsEventsManager::hook("controller_{$this->name}_perms", [$rules, $values]);

        $groups = cmsCore::getModel('users')->getGroups(false);

        $template_params = [
            'rules'   => $rules,
            'values'  => $values,
            'groups'  => $groups,
            'subject' => $subject
        ];

        // если задан шаблон опций в контроллере
        if ($this->cms_template->getTemplateFileName('controllers/' . $this->name . '/backend/perms', true)) {

            return $this->cms_template->render('backend/perms', $template_params);

        } else {

            $default_admin_tpl = $this->cms_template->getTemplateFileName('controllers/admin/controllers_perms');

            return $this->cms_template->processRender($default_admin_tpl, $template_params);
        }
    }

    /**
     * Экшен сохранения правил доступа
     *
     * @param string $subject
     * @return redirect
     */
    public function actionPermsSave($subject = '') {

        if (empty($this->useDefaultPermissionsAction)) {
            return cmsCore::error404();
        }

        $values = $this->request->get('value', []);
        $rules  = cmsPermissions::getRulesList($this->name);

        // добавляем правила доступа от типа контента, если контроллер на его основе
        $ctype = cmsCore::getModel('content')->getContentTypeByName($this->name);
        if ($ctype) {
            $rules = array_merge(cmsPermissions::getRulesList('content'), $rules);
        }

        list($rules, $values) = cmsEventsManager::hook("controller_{$this->name}_perms", [$rules, $values]);

        $groups = cmsCore::getModel('users')->getGroups(false);

        // перебираем правила
        foreach ($rules as $rule) {

            // если для этого правила вообще ничего нет,
            // то присваиваем null
            if (!isset($values[$rule['id']])) {
                $values[$rule['id']] = null;
                continue;
            }

            // перебираем группы, заменяем на нуллы
            // значения отсутствующих правил
            foreach ($groups as $group) {
                if (!isset($values[$rule['id']][$group['id']])) {
                    $values[$rule['id']][$group['id']] = null;
                }
            }
        }

        cmsUser::addSessionMessage(LANG_CP_PERMISSIONS_SUCCESS, 'success');

        cmsPermissions::savePermissions($subject, $values);

        return $this->redirectBack();
    }

    //============================================================================//
    //=========                         Модераторы                       =========//
    //============================================================================//

    /**
     * Экшен списка модераторов
     *
     * @return string
     */
    public function actionModerators() {

        if (empty($this->useDefaultModerationAction)) {
            return cmsCore::error404();
        }

        $moderators = $this->model_moderation->getContentTypeModerators($this->name);

        $template_params = [
            'title'         => $this->title,
            'not_use_trash' => !$this->useModerationTrash,
            'moderators'    => $moderators
        ];

        $this->cms_template->addToolButton([
            'class' => 'settings',
            'title' => LANG_MODERATORATION_OPTIONS,
            'href'  => href_to('admin', 'controllers', ['edit', 'moderation', 'options'])
        ]);

        $this->cms_template->addToolButton([
            'class'  => 'help',
            'title'  => LANG_HELP,
            'target' => '_blank',
            'href'   => LANG_HELP_URL_CTYPES_MODERATORS
        ]);

        // если задан шаблон в контроллере
        if ($this->cms_template->getTemplateFileName('controllers/' . $this->name . '/backend/moderators', true)) {

            return $this->cms_template->render('backend/moderators', $template_params);

        } else {

            $default_admin_tpl = $this->cms_template->getTemplateFileName('controllers/admin/controllers_moderators');

            return $this->cms_template->processRender($default_admin_tpl, $template_params);
        }
    }

    /**
     * Экшен добавления модератора
     *
     * @return json Выводит JSON и завершает работу
     */
    public function actionModeratorsAdd() {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        $name = $this->request->get('name', '');
        if (!$name) {
            return cmsCore::error404();
        }

        $user = cmsCore::getModel('users')->filterEqual('email', $name)->getUser();

        if ($user === false) {
            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => sprintf(LANG_CP_USER_NOT_FOUND, $name)
            ]);
        }

        $moderators = $this->model_moderation->getContentTypeModerators($this->name);

        if (isset($moderators[$user['id']])) {
            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => sprintf(LANG_MODERATOR_ALREADY, $user['nickname'])
            ]);
        }

        $moderator = $this->model_moderation->addContentTypeModerator($this->name, $user['id']);

        if (!$moderator) {
            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => LANG_ERROR
            ]);
        }

        $ctypes_moderator_tpl = $this->cms_template->getTemplateFileName('controllers/admin/ctypes_moderator');

        return $this->cms_template->renderJSON([
            'error' => false,
            'name'  => $user['nickname'],
            'html'  => $this->cms_template->processRender($ctypes_moderator_tpl, [
                'moderator'     => $moderator,
                'not_use_trash' => !$this->useModerationTrash,
                'ctype'         => ['name' => $this->name, 'controller' => $this->name]
            ], new cmsRequest([], cmsRequest::CTX_INTERNAL)),
            'id'    => $user['id']
        ]);
    }

    /**
     * Экшен удаления модератора
     *
     * @return json Выводит JSON и завершает работу
     */
    public function actionModeratorsDelete() {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        $id = $this->request->get('id', 0);
        if (!$id) {
            return cmsCore::error404();
        }

        $moderators = $this->model_moderation->getContentTypeModerators($this->name);

        if (!isset($moderators[$id])) {
            return $this->cms_template->renderJSON([
                'error' => true
            ]);
        }

        $this->model_moderation->deleteContentTypeModerator($this->name, $id);

        return $this->cms_template->renderJSON([
            'error' => false
        ]);
    }

    public function getForm($form_name, $params = false, $path_prefix = '') {
        return parent::getForm($form_name, $params, 'backend/'.$path_prefix);
    }

}
