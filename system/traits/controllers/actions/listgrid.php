<?php

namespace icms\traits\controllers\actions;

use cmsUser, cmsGrid, cmsForm, cmsCore, cmsEventsManager;

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

    use \icms\traits\oneable;

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
     * Заголовок <h1>
     * @var string
     */
    protected $h1_title = '';

    /**
     * Описание
     * @var string
     */
    protected $description = '';

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

            $this->executeAction($this->external_action_prefix.$do, array_slice($this->params, 1));
            return;
        }

        $this->prepareRun();

        $this->setListGridParams();

        if ($this->request->isAjax()) {

            header('X-Frame-Options: DENY');

            // Если надо сохранить одно значение из строки
            if ($this->request->has('save_row_field')) {
                return $this->cms_template->renderJSON($this->saveRowField($this->request->getAll()));
            }

            // Массовое сохранение
            if ($this->request->has('save_rows_fields')) {
                return $this->cms_template->renderJSON($this->saveRowsFields($this->request->getAll()));
            }

            // Вывод всего списка
            return $this->cms_template->renderJSON($this->getListItems());
        }

        return $this->renderListItemsGrid();
    }

    /**
     * Массовое сохранение строк грида
     *
     * @param array $field_data
     * @return array
     */
    public function saveRowsFields($field_data) {

        if (!$field_data) {
            return ['error' => LANG_ERROR . ' #empty data'];
        }

        if (empty($field_data['csrf_token']) || !cmsForm::validateCSRFToken($field_data['csrf_token'])) {
            return ['error' => LANG_ERROR . ' #csrf_token'];
        }

        if (empty($field_data['rows']) || !is_array($field_data['rows'])) {
            return ['error' => LANG_ERROR . ' #empty rows'];
        }

        foreach ($field_data['rows'] as $row_id => $row) {

            if (!is_numeric($row_id) || !is_array($row)) {
                return ['error' => LANG_ERROR . ' #empty id or row'];
            }

            $saved_data = [];
            $items = [];

            foreach ($row as $name => $value) {

                if (!is_string($name) || is_array($value)) {
                    return ['error' => LANG_ERROR . ' #validate name=>value error'];
                }

                // Получаем таблицу, запись и имя поля
                $result = $this->getTableAndItemAndFielName(['name' => $name, 'value' => $value, 'id' => $row_id]);

                if (is_string($result)) {
                    return ['error' => ['id' => $row_id, 'name' => $name, 'value' => $result]];
                }

                list($save_table, $item, $field_name) = $result;

                $saved_data[$save_table][$field_name] = strip_tags($value);

                $items[$save_table][$row_id] = $item;
            }

            foreach ($saved_data as $save_table => $data) {

                $this->model->update($save_table, $row_id, $data);

                $item = $items[$save_table][$row_id];

                list($save_table, $row_id, $data, $item) = cmsEventsManager::hook(
                    ['grid_inline_save_list_after', 'grid_'.$this->grid->getGridFullName().'_inline_save_list_after'],
                    [$save_table, $row_id, $data, $item]
                );
            }
        }

        return [
            'error' => false
        ];
    }

    /**
     * Сохраняет значение ячейки строки грида
     *
     * @param array $field_data
     * @return array
     */
    public function saveRowField($field_data) {

        if (!$field_data) {
            return ['error' => LANG_ERROR . ' #empty data'];
        }

        if (empty($field_data['csrf_token']) || !cmsForm::validateCSRFToken($field_data['csrf_token'])) {
            return ['error' => LANG_ERROR . ' #csrf_token'];
        }

        if (empty($field_data['name']) || !is_string($field_data['name'])) {
            return ['error' => LANG_ERROR . ' #empty data name'];
        }

        if (!array_key_exists('value', $field_data) || is_array($field_data['value'])) {
            return ['error' => LANG_ERROR . ' #empty data value'];
        }

        if (empty($field_data['id']) || !is_numeric($field_data['id'])) {
            return ['error' => LANG_ERROR . ' #empty id'];
        }

        // Получаем таблицу, запись и имя поля
        $result = $this->getTableAndItemAndFielName($field_data);

        if (is_string($result)) {
            return ['error' => $result];
        }

        list($save_table, $i, $field_name) = $result;

        list($save_table, $field_data, $i) = cmsEventsManager::hook(
            ['grid_inline_save_before', 'grid_'.$this->grid->getGridFullName().'_inline_save_before'],
            [$save_table, $field_data, $i]
        );

        $this->model->update($save_table, $i['id'], [
            $field_name => strip_tags($field_data['value'])
        ]);

        $this->model->limit(1)->filterEqual('id', $field_data['id']);

        $row_data = $this->getListItems(true);

        if (empty($row_data['rows'][0])) {
            return ['error' => LANG_ERROR . ' #no row data'];
        }

        $row_data = $row_data['rows'][0];

        list($save_table, $field_data, $i, $row_data) = cmsEventsManager::hook(
            ['grid_inline_save_after', 'grid_'.$this->grid->getGridFullName().'_inline_save_after'],
            [$save_table, $field_data, $i, $row_data]
        );

        return [
            'error' => false,
            'row'   => $row_data
        ];
    }

    /**
     * Получает имя таблицы, запись и имя поля для сохранения
     *
     * @param array $field_data [name, id, value]
     * @return array|string Массив данных или строку с ошибкой
     */
    protected function getTableAndItemAndFielName($field_data) {

        // Включено ли редактирование
        if ($this->grid->getGridValue('columns:' . $field_data['name'] . ':editable') === null) {
            return LANG_ERROR . ' #non-editable field';
        }

        // Таблица для сохранения может быть указана в гриде
        $save_table = $this->grid->getGridValue('columns:' . $field_data['name'] . ':editable:table');
        if (!$save_table) {
            $save_table = $this->table_name;
        }

        // Получаем запись
        $i = $this->getOnce($this->model)->getItemByField($save_table, 'id', $field_data['id']);
        if (!$i) {
            return LANG_ERROR . ' #404';
        }

        // Поле должно быть
        if (!array_key_exists($field_data['name'], $i)) {
            return LANG_ERROR . ' #no field';
        }

        // Проверяем на ошибки
        $error = $this->grid->validateColumnValue($field_data['name'], $field_data['value']);

        if ($error !== true) {
            return $error;
        }

        $field_name = $field_data['name'];

        $disable_language_context = $this->grid->getGridValue('columns:' . $field_name . ':editable:language_context');

        if (!$disable_language_context) {

            // Ищем поле на текущем языке
            $field_name = $this->model->getTranslatedFieldName($field_name, $save_table);

            // Могло быть не включено в настройках
            if (!array_key_exists($field_name, $i)) {
                return LANG_ERROR . ' #no translated field';
            }
        }

        return [$save_table, $i, $field_name];
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
            'grid'        => $this->grid,
            'rows'        => $this->getListItems(),
            'description' => $this->description,
            'h1_title'    => $this->h1_title,
            'page_title'  => $this->title
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
