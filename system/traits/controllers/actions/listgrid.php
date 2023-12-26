<?php

namespace icms\traits\controllers\actions;

use cmsUser, cmsGrid, cmsForm, cmsCore;

/**
 * Трейт для экшена вывода грида
 *
 * @property \cmsTemplate $cms_template
 * @property \cmsUser $cms_user
 * @property \cmsRequest $request
 * @property \cmsModel $model
 * @property \cmsGrid $grid
 *
 */
trait listgrid {

    /**
     * Основная таблица БД
     * @required
     * @var string
     */
    protected $table_name = '';

    /**
     * Имя грида
     * @required
     * @var string
     */
    protected $grid_name = '';

    /**
     * Аргументы, передаваемые в файл описания грида
     * @var array
     */
    protected $grid_args = false;

    /**
     * URL загрузки данных грида
     * @var string
     */
    protected $grid_url = '';

    /**
     * Заголовок страницы
     * @var string
     */
    protected $title = '';

    /**
     * Кнопки тулбара
     * @var array
     */
    protected $tool_buttons = [];

    /**
     * Имя хука для тулбара
     * @var ?string
     */
    protected $toolbar_hook = null;

    /**
     * Коллбэк для модели где получается список данных
     * @var callable
     */
    protected $list_callback = null;

    /**
     * Коллбэк для полученого списка записей
     * @var ?callable
     */
    protected $items_callback = null;

    /**
     * Коллбэк, передаваемый в метод get модели
     * @var ?callable
     */
    protected $item_callback = null;

    /**
     * Коллбэк для фильтра
     * @var callable
     */
    protected $filter_callback = null;

    /**
     * Префикс действия, если надо передать управление другому экшену
     * @var string
     */
    protected $external_action_prefix = '';

    /**
     * Ключ UPS
     * @var string
     */
    protected $ups_key = '';

    /**
     * Грид
     * @var array
     */
    protected $grid;

    /**
     * Кол-во записей на страницу по умолчанию
     * @var integer
     */
    protected $default_perpage = 30;

    /**
     * Запускается перед логикой в run
     */
    public function prepareRun() {}

    /**
     * Основной метод запуска экшена
     *
     * @param mixed $do
     * @param mixed $param_two
     * @return string
     */
    public function run($do = null, $param_two = null){

        // если нужно, передаем управление другому экшену
        if ($do && !is_numeric($do)) {

            $this->runExternalAction($this->external_action_prefix.$do, array_slice($this->params, 1));
            return;
        }

        $this->prepareRun();

        $this->setListGridParams();

        if ($this->request->isAjax()) {

            header('X-Frame-Options: DENY');

            // Если надо сохранить значение из строки
            if ($this->request->has('save_row_field')) {

                return $this->cms_template->renderJSON($this->saveRowField($this->request->getAll()));
            }

            // Вывод всего списка
            return $this->cms_template->renderJSON($this->getListItems());
        }

        return $this->renderListItemsGrid();
    }

    /**
     * Сохраняет значение ячейки строки грида
     *
     * @param array $field_data
     * @return array
     */
    public function saveRowField($field_data) {

        if (empty($field_data['csrf_token']) || !cmsForm::validateCSRFToken($field_data['csrf_token'])) {
            return ['error' => LANG_ERROR . ' #csrf_token'];
        }

        if (!$field_data) {
            return ['error' => LANG_ERROR . ' #empty data'];
        }

        if (empty($field_data['name']) || !is_string($field_data['name'])) {

            return $this->cms_template->renderJSON([
                'error' => LANG_ERROR . ' #empty data name'
            ]);
        }

        if (!array_key_exists('value', $field_data) || is_array($field_data['value'])) {

            return $this->cms_template->renderJSON([
                'error' => LANG_ERROR . ' #empty data value'
            ]);
        }

        if (empty($field_data['id']) || !is_numeric($field_data['id'])) {
            return ['error' => LANG_ERROR . ' #empty id'];
        }

        $i = $this->model->getItemByField($this->table_name, 'id', $field_data['id']);
        if (!$i) {
            return ['error' => LANG_ERROR . ' #404'];
        }

        if (!array_key_exists($field_data['name'], $i)) {
            return ['error' => LANG_ERROR . ' #no field'];
        }

        $error = $this->grid->validateColumnValue($field_data['name'], $field_data['value']);

        if ($error !== true) {
            return ['error' => $error];
        }

        $field_name = $field_data['name'];

        $disable_language_context = $this->grid->getGridValue('columns:' . $field_name . ':editable:language_context');

        if (!$disable_language_context) {

            // Ищем поле на текущем языке
            $field_name = $this->model->getTranslatedFieldName($field_name, $this->table_name);

            // Могло быть не включено в настройках
            if (!array_key_exists($field_name, $i)) {
                return ['error' => LANG_ERROR . ' #no translated field'];
            }
        }

        $this->model->update($this->table_name, $i['id'], [
            $field_name => strip_tags($field_data['value'])
        ]);

        $this->model->limit(1)->filterEqual('id', $field_data['id']);

        $row_data = $this->getListItems(true);

        if (empty($row_data['rows'][0])) {
            return ['error' => LANG_ERROR . ' #no row data'];
        }

        return [
            'error' => false,
            'row'   => $row_data['rows'][0]
        ];
    }

    /**
     * Загружает грид
     *
     * @return void
     */
    public function setListGridParams() {

        $this->ups_key = 'grid_filter.' . $this->ups_key . $this->name . '_' . $this->grid_name;

        $this->grid = new cmsGrid($this->controller, $this->grid_name, $this->grid_args);

        if (!$this->grid->isLoaded()) {

            return cmsCore::error($this->grid->getError());
        }

        $this->grid->source_url = $this->grid_url ? $this->grid_url : $this->cms_template->href_to($this->current_action, $this->params);
    }

    /**
     * Возвращает HTML грида
     *
     * @return string
     */
    public function getListItemsGridHtml(){

        $this->cms_template->addToolButtons($this->tool_buttons);

        if ($this->toolbar_hook) {

            $this->cms_template->applyToolbarHook($this->toolbar_hook);
        }

        return $this->cms_template->getRenderedAsset('ui/grid-data', [
            'grid'       => $this->grid,
            'rows'       => $this->getListItems(),
            'page_title' => $this->title
        ]);
    }

    /**
     * Печатает грид и подключает при необходимости
     * CSS контроллера контекста вызова
     *
     * @return string
     */
    public function renderListItemsGrid(){

        $html = $this->getListItemsGridHtml();

        if ($this->request->isStandard()) {
            $this->cms_template->addOutput($html);
        }

        $css_file = $this->cms_template->getStylesFileName();

        if ($css_file) {
            $this->cms_template->addCSSFromContext($css_file, $this->request);
        }

        return $html;
    }

    /**
     * Возвращает подготовленные данные записей для грида
     *
     * @param boolean $ignore_field Игнорировать фильтр
     * @return type
     */
    public function getListItems($ignore_field = false){

        $visible_columns = cmsUser::getUPSActual($this->ups_key.'.visible_columns', $this->request->get('visible_columns', []));

        if ($visible_columns) {

            $switchable_columns = $this->grid->getSwitchableColumns();

            if ($switchable_columns) {
                foreach ($switchable_columns as $name => $column) {
                    if (!in_array($name, $visible_columns)) {
                        $this->grid->disableColumn($name);
                    } else {
                        $this->grid->enableColumn($name);
                    }
                }
            }
        }

        if (!$ignore_field) {

            $this->model->setPerPage($this->default_perpage);

            $filter     = $this->grid->filter;
            $pre_filter = cmsUser::getUPSActual($this->ups_key, $this->request->get('filter', ''));

            if ($pre_filter) {
                parse_str($pre_filter, $filter);
            }

            if ($filter) {

                if ($this->filter_callback) {
                    $filter = call_user_func_array($this->filter_callback, [$filter]);
                }

                $this->grid->applyGridFilter($this->model, $filter, $this->table_name);
            }
        }

        if($this->list_callback){
            $this->model = call_user_func_array($this->list_callback, [$this->model]);
        }

        $total = $this->model->getCount($this->table_name);

        $data = $this->model->get($this->table_name, $this->item_callback) ?: [];

        if($this->items_callback){
            $data = call_user_func_array($this->items_callback, [$data]);
        }

        return $this->grid->makeGridRows($data, $total);
    }

}
