<?php
/**
 * Класс для работы с гридам (таблицами данных)
 */
class cmsGrid {

    /**
     * Параметры грида
     *
     * @var array
     */
    private $grid = [
        // URL, откуда загружать данные
        'source_url' => '',
        // Опции списка
        'options' => [
            'order_by'        => 'id',  // Поле сортировки
            'order_to'        => 'asc', // Направление сортировки
            'show_id'         => true,  // Показывать id записи
            'is_sortable'     => true,  // Можно сортировать
            'is_filter'       => true,  // Поля грида можно фильтровать
            'advanced_filter' => false, // URL для дополнительного фильтра записей
            'is_pagination'   => true,  // показывать пагинацию
            'perpage'         => 30,    // записей на странице по умолчанию
            'is_toolbar'      => true,  // выводить тулбар
            'is_draggable'    => false, // строки могут меняться местами мышью
            'drag_save_url'   => '',    // URL для сохранения при драг-эн-дроп
            'is_selectable'   => false, // Строки могут выделяться
            /**
             * select_actions - это массив действий над выделенными строками
             * Пример в system/controllers/admin/grids/grid_content_items.php
                'title'   => Название,
                'action'  => submit || open,
                'confirm' => Фраза подтверждения действия,
                'url'     => URL на который отправлять выделенные записи
             */
            'select_actions'  => false
        ],
        // Колонки параметров
        'columns' => [],
        // Действия над записью
        'actions' => [],
        // Фильтр записей. Не заполняйте его из функции описания грида
        'filter'  => []
    ];

    /**
     * Все значения колонки по умолчанию
     * @var array
     */
    private $default_column = [
        'tooltip'           => '',  // Текст всплывающей подсказки
        'tooltip_handler'   => null,  // Функция обработчик, возвращающая текст всплывающей подсказки
        'switchable'        => false, // Может включаться/выключаться
        'disable'           => false, // Если может включаться/выключаться выключение по умолчанию
        'sortable'          => true,  // Можено сортировать по ней
        'title'             => '',    // Заголовок
        'class'             => '',    // CSS класс
        'key_alias'         => '',    // Псевдоним поля
        'handler'           => null,  // Функция обработчик значения колонки
        'flag'              => false, // Колонка - флаг (включена/выключена)
        'flag_on'           => null,  // Значение, с которым сравнивается запись, чтобы флажок был включен
        'flag_handler'      => null,  // Функция обработчик значения для флага
        'flag_toggle'       => '',    // URL переключения состояния флага
        'href'              => '',    // Ссылка колонки
        'href_handler'      => null,  // Функция обработчик, если возвращает true, то ссылка показывается
        'order_by'          => '',    // Имя поля, по которому нужно сортировать
        'filter_by'         => '',    // Имя поля, по которому нужно фильтровать
        'filter'            => '',    // Тип фильтра, см. applyGridFilter
        'filter_select'     => ['items' => []], // Массив значений для select тега фильтра
        'filter_attributes' => [],    // Атрибуты тега фильтрации
        'editable' => [               // Может редактироваться из списка
            'rules'            => [],      // Массив правил валидации при сохранении
            'renderer'         => null,    // Компонент vue поля редактирования, по умолчанию form-input
            'items'            => null,    // Массив списка для селекта, если renderer form-select
            'language_context' => false,   // Если выключено, будет искать языковое поле для текущей локали
            'save_action'      => '',      // URL для сохранения
            'attributes'       => []       // Атрибуты тега быстрого редактирования
        ]
    ];

    /**
     * Колонки, которые нужно показывать
     *
     * @var ?array
     */
    private $visible_columns = null;

    /**
     * Имена колонок ключи, включена/выключена значение
     *
     * @var array
     */
    private $visible_columns_names = [];

    /**
     * Колонки, которые можно отключать
     *
     * @var ?array|boolean
     */
    private $switchable_columns = null;

    /**
     * Маппинг имя фильтра => компонент (vuejs) отрисовки
     * @var array
     */
    private $filter_component_map = [
        'range_date' => 'form-date-range',
        'range'      => 'form-range',
        'zero'       => 'form-checkbox',
        'nn'         => 'form-checkbox',
        'ni'         => 'form-checkbox',
        'in'         => 'form-multiselect',
        'filled'     => 'form-select',
        'exact'      => 'form-select',
        'like'       => 'form-input',
        'ip'         => 'form-input',
        'date'       => 'form-date'
    ];

    /**
     * Объект контроллера
     * @var cmsController
     */
    private $controller;

    /**
     * Имя грида
     * @var string
     */
    private $grid_name = '';

    /**
     * Параметры, передающиеся в функцию грида
     * @var array
     */
    private $grid_params = [];

    /**
     * Грид успешно загружен?
     * @var boolean
     */
    private $is_loaded = false;

    /**
     * Текст ошибки при инициализации
     * @var ?string
     */
    private $error = null;

    /**
     *
     * @param cmsController|cmsAction $controller Объект контроллера (cmsAction с магией тоже), для которого строим грид
     * @param string $grid_name Имя грида
     * @param ?array $grid_params Параметры инициализации
     */
    public function __construct($controller, $grid_name, $grid_params = null) {

        $this->controller = $controller;

        $this->grid_name = $grid_name;

        if($grid_params) {

            $this->grid_params = !is_array($grid_params) ? [$grid_params] : $grid_params;
        }

        $this->is_loaded = $this->load();
    }

    public function &__get($name) {
        return $this->grid[$name];
    }

    public function __set($name, $value) {
        $this->grid[$name] = $value;
    }

    public function __isset($name) {
        return isset($this->grid[$name]);
    }

    public function __unset($name) {
        unset($this->grid[$name]);
    }

    /**
     * Грид успешно загружен?
     *
     * @return boolean
     */
    public function isLoaded() {
        return $this->is_loaded;
    }

    /**
     * Возвращает последнюю ошибку
     *
     * @return ?string
     */
    public function getError() {
        return $this->error;
    }

    /**
     * Загружает и подготовливает параметры грида
     *
     * @return bool
     */
    private function load() {

        $grid_file = $this->controller->root_path . 'grids/grid_' . $this->grid_name . '.php';

        if (!is_readable($grid_file)) {

            $this->error = ERR_FILE_NOT_FOUND . ': '. str_replace(PATH, '', $grid_file);

            return false;
        }

        include_once $grid_file;

        $grid_func_name = 'grid_' . $this->grid_name;

        if(!function_exists($grid_func_name)){

            $this->error = $grid_func_name.' function not found in '. str_replace(PATH, '', $grid_file);

            return false;
        }

        $args = [$this->controller];

        foreach ($this->grid_params as $p) {
            $args[] = $p;
        }

        $grid = call_user_func_array($grid_func_name, $args);

        foreach ($grid as $key => $data) {

            $this->grid[$key] = is_array($data) ? array_merge(($this->grid[$key] ?? []), $data) : $data;
        }

        // Фильтр по умолчанию
        $this->setDefaultFilter();

        $this->grid = cmsEventsManager::hook('grid_' . $this->controller->name . '_' . $this->grid_name, $this->grid);

        if($this->grid['options']['select_actions']){
            array_unshift($this->grid['options']['select_actions'], [
                'title' => LANG_SELECTED_ACTIONS,
                'url'   => ''
            ]);
        }

        list($this->grid, $args) = cmsEventsManager::hook(
            'grid_' . $this->controller->name . '_' . $this->grid_name . '_args',
            [$this->grid, $args]
        );

        return true;
    }

    /**
     * Возвращает массив грида целиком
     * @return array
     */
    public function getGrid() {
        return $this->grid;
    }

    /**
     * Возвращает значение описания грида
     *
     * @param string $path Путь до ключа, например columns:name:title
     * @return mixed
     */
    public function getGridValue($path) {
        return array_value_recursive($path, $this->grid);
    }

    /**
     * Валидация значения колонки
     * аналогично как в формах
     *
     * @param string $field_name
     * @param mixed $value
     * @return boolean
     */
    public function validateColumnValue($field_name, $value) {

        $rules = $this->grid['columns'][$field_name]['editable']['rules'] ?? [];

        if(!$rules){
            return true;
        }

        foreach ($rules as $rule) {

            if (!$rule) { continue; }

            $validate_function = "validate_{$rule[0]}";

            $rule[] = $value;

            unset($rule[0]);

            $result = call_user_func_array([$this->controller, $validate_function], $rule);

            // ошибка уже найдена
            if ($result !== true) {
                return $result;
            }
        }

        return true;
    }

    /**
     * Подготоваливает видимые колонки
     */
    private function getVisibleColumns() {

        if ($this->visible_columns === null) {

            foreach ($this->grid['columns'] as $name => $column) {

                $is_disabled = false;

                if (!empty($column['disable'])) {
                    $is_disabled = true;
                }
                if (array_key_exists($name, $this->visible_columns_names)) {
                    $is_disabled = !$this->visible_columns_names[$name];
                }
                if(empty($column['switchable'])){
                    $is_disabled = false;
                }
                if ($is_disabled) {
                    continue;
                }

                $this->visible_columns[$name] = $column;
            }
        }

        return $this->visible_columns;
    }

    /**
     * Отключаемые колонки
     *
     * @return array
     */
    public function getSwitchableColumns() {

        if ($this->switchable_columns === null) {

            $columns = [];

            foreach ($this->grid['columns'] as $key => $item) {
                if (!empty($item['switchable'])) {
                    $columns[$key] = $item['title'];
                }
            }

            $this->switchable_columns = $columns ?: false;
        }

        return $this->switchable_columns;
    }

    /**
     * Отключает колонку для вывода
     *
     * @param string $name Имя колонки
     * @return void
     */
    public function disableColumn($name) {
        $this->visible_columns_names[$name] = false;
    }

    /**
     * Включает колонку для вывода
     *
     * @param string $name Имя колонки
     * @return void
     */
    public function enableColumn($name) {
        $this->visible_columns_names[$name] = true;
    }

    /**
     * Устанавливает параметры выборки из БД по умолчанию
     *
     * @return void
     */
    private function setDefaultFilter() {

        $this->grid['filter'] = [
            'page' => 1,
            'columns' => [],
            'advanced_filter' => '',
            'perpage' => $this->grid['options']['perpage']
        ];

        if ($this->grid['options']['order_by']) {
            $this->grid['filter']['order_by'] = $this->grid['options']['order_by'];
        }

        if ($this->grid['options']['order_to']) {
            $this->grid['filter']['order_to'] = $this->grid['options']['order_to'];
        }
    }

    /**
     * Добавляет данные в фильтр
     *
     * @param array $filter
     * @return void
     */
    public function addToFilter($filter) {
        $this->grid['filter'] = array_replace_recursive($this->grid['filter'], $filter);
    }

    /**
     * Применяет фильтр к модели выборки
     *
     * @param cmsModel $model Объект модели, где выбираем записи
     * @param array $filter Массив фильтрации
     * @param array $table_name Таблица, где ищем поля фильтра
     * @return cmsModel
     */
    public function applyGridFilter(cmsModel $model, $filter, $table_name) {

        // применяем сортировку
        if (!empty($filter['order_by']) && !empty($filter['order_to'])) {

            // Есть ли вообще такой столбец
            if(empty($this->grid['columns'][$filter['order_by']])){
                $filter['order_by'] = $this->grid['options']['order_by'];
            }

            $order_by = $filter['order_by'];

            // Есть отдельный столбец для сортировки
            if (!empty($this->grid['columns'][$order_by]['order_by'])) {
                $order_by = $this->grid['columns'][$order_by]['order_by'];
            }

            $model->orderBy($order_by, $filter['order_to']);
        }

        // устанавливаем страницу
        if (!empty($filter['page'])) {

            $filter['perpage'] = !empty($filter['perpage']) ? (int) $filter['perpage'] : 30;
            $filter['page']    = (int) ($filter['page'] <= 0 ? 1 : $filter['page']);

            $model->limitPage($filter['page'], $filter['perpage']);
        }

        // Пагинация отключена
        if(!$this->grid['options']['is_pagination']){
            $model->limit(false);
        }

        //
        // проходим по каждой колонке таблицы
        // и проверяем не передан ли фильтр для нее
        //
        foreach ($this->getVisibleColumns() as $field => $column) {

            if (empty($column['filter']) ||
                    $column['filter'] === 'none' ||
                    !array_key_exists($field, $filter) ||
                    is_empty_value($filter[$field])) {
                continue;
            }

            if (!empty($column['filter_by'])) {
                $filter_field = $column['filter_by'];
            } else {
                $filter_field = $field;
            }

            switch ($column['filter']) {
                case 'range_date':
                    if (isset($filter[$field]['from']) && !is_empty_value($filter[$field]['from'])) {
                        $date_from = date('Y-m-d', strtotime($filter[$field]['from']));
                        $model->filterGtEqual($filter_field, $date_from);
                    }
                    if (isset($filter[$field]['to']) && !is_empty_value($filter[$field]['to'])) {
                        $date_to = date('Y-m-d', strtotime($filter[$field]['to']));
                        $model->filterLtEqual($filter_field, $date_to);
                    }
                case 'range':
                    if (isset($filter[$field]['from']) && !is_empty_value($filter[$field]['from'])) {
                        $model->filterGtEqual($filter_field, $filter[$field]['from']);
                    }
                    if (isset($filter[$field]['to']) && !is_empty_value($filter[$field]['to'])) {
                        $model->filterLtEqual($filter_field, $filter[$field]['to']);
                    }
                    break;
                case 'zero':
                    if($filter[$field]) {
                        $model->filterEqual($filter_field, 0);
                    }
                case 'nn':
                    if($filter[$field]) {
                        $model->filterNotNull($filter_field);
                    }
                    break;
                case 'ni':
                    if($filter[$field]) {
                        $model->filterIsNull($filter_field);
                    }
                    break;
                case 'in': $model->filterIn($filter_field, !is_array($filter[$field]) ? explode(',', $filter[$field]) : $filter[$field]);
                    break;
                case 'filled': ($filter[$field] ? $model->filterNotNull($filter_field) : $model->filterIsNull($filter_field));
                    break;
                case 'exact': $model->filterEqual($filter_field, $filter[$field]);
                    break;
                case 'ip': $model->filterEqual($filter_field, string_iptobin($filter[$field]), true);
                    break;
                case 'like': $model->filterLike($filter_field, "%{$filter[$field]}%");
                    break;
                case 'date':
                    $date = date('Y-m-d', strtotime($filter[$field]));
                    $model->filterLike($filter_field, "%{$date}%");
                    break;
            }
        }

        // Запоминаем
        $this->grid['filter'] = array_merge($this->grid['filter'], $filter);

        // Дополнительный фильтр
        if (!empty($filter['advanced_filter']) && is_string($filter['advanced_filter'])) {

            $dataset_filters = [];

            parse_str($filter['advanced_filter'], $dataset_filters);

            if (!$model->applyDatasetFilters($dataset_filters, true, [], $table_name)) {

                $this->grid['filter']['advanced_filter'] = '';
            }
        }

        return $model;
    }

    /**
     * Собирает все данные таблицы
     *
     * @param array|false $dataset Данные из базы
     * @param integer $total Сколько всего записей
     * @return array
     */
    public function makeGridRows($dataset = false, $total = 0) {

        $rows = [];

        if(is_array($dataset)){
            foreach($dataset as $row){
                $rows[] = [
                    'columns'  => $this->makeRowColumns($row),
                    'id'       => $row['id'],
                    'selected' => false,
                    'edited'   => false
                ];
            }
        }

        return [
            'dragging'   => -1,
            'is_loading' => false,
            'need_load'  => $dataset === false,
            'source_url' => $this->grid['source_url'] !== '' ? $this->grid['source_url'] : cmsCore::getInstance()->uri_absolute,
            'options'    => $this->grid['options'],
            'filter'     => $this->grid['filter'],
            'rows'       => $rows,
            'total'      => $total ? $total : count(($dataset ?: [])),
            'switchable' => [
                'title'   => LANG_GRID_COLYMNS_SETTINGS,
                'columns' => $this->getSwitchableColumns()
            ],
            'columns'    => $this->makeColumns()
        ];
    }

    /**
     * Собирает строку таблицы
     *
     * @param array $row
     * @return array
     */
    private function makeRowColumns($row) {

        $columns = [];

        $is_set_dragged_class = false;

        foreach ($this->getVisibleColumns() as $field => $column) {

            if (isset($column['key_alias'])){
                $field = $column['key_alias'];
            }

            $class = [];

            if (!empty($column['class'])) {
                $class[] = $column['class'];
            }

            if ($field === 'id') {
                if (!$this->grid['options']['show_id']) {

                    $class[] = 'd-none';
                } else {

                    $class[] = 'dragged_handle';

                    $is_set_dragged_class = true;
                }
            } else if (!$is_set_dragged_class) {

                $class[] = 'dragged_handle';

                $is_set_dragged_class = true;
            }

            $row_column = [
                'row_id'   => $row['id'],
                'tooltip'  => $column['tooltip'] ?? '',
                'name'     => $field,
                'value'    => '',
                'class'    => implode(' ', $class),
                'editable' => $this->getEditableParams($row, $column, $field),
                'href'     => false,
                'renderer' => $column['renderer'] ?? 'basic' // basic, flag, html, actions
            ];

            // Это скорее чтобы не было ошибки при рендере
            if (!array_key_exists($field, $row)) {

                $columns[] = $row_column;

                continue;
            }

            // тултип колонки
            if (isset($column['tooltip_handler'])) {
                $row_column['tooltip'] = $column['tooltip_handler']($row);
            }

            // null - значит будет пустая строка
            $row_column['value'] = $row[$field] ?? '';

            if (isset($column['href_handler'])) {
                $is_active_href = $column['href_handler']($row);
            } else {
                $is_active_href = true;
            }

            // если из значения нужно сделать ссылку, то парсим шаблон
            // адреса, заменяя значения полей
            if (isset($column['href'])) {
                if ($is_active_href) {
                    $row_column['href'] = string_replace_keys_values_extended($column['href'], $row);
                }
            }

            // Тип флаг
            if (!empty($column['flag'])) {

                if (isset($column['flag_handler'])) {

                    $row_column['value'] = $column['flag_handler']($row_column['value'], $row);
                }

                // Есть предустановленное значение для "включенности" флага
                if (isset($column['flag_on'])) {

                    $row_column['value'] = $row_column['value'] == $column['flag_on'] ? 1 : 0;
                } else {

                    $row_column['value'] = (int) $row_column['value'];
                }

                // URL для смены состояния флага
                $flag_toggle_url = $column['flag_toggle'] ?? false;
                if ($flag_toggle_url) {
                    $flag_toggle_url = string_replace_keys_values($flag_toggle_url, $row);
                }

                $row_column['flag_class'] = $column['flag'] === true ? 'flag' : $column['flag'];
                $row_column['href']       = $is_active_href ? $flag_toggle_url : false;
                $row_column['confirm']    = $column['flag_confirm'] ?? false;
                $row_column['renderer']   = 'flag';

                $columns[] = $row_column;

                continue;
            }

            // Есть функция обработчик, предполагаем, что вернёт готовый HTML
            if (isset($column['handler'])) {

                $row_column['renderer'] = 'html';
                $row_column['value']    = $column['handler']($row_column['value'], $row);

                $columns[] = $row_column;

                continue;
            }

            // Массивы отдаём на откуп разработчикам
            if (is_array($row_column['value'])) {

                if (isset($column['renderer'])) {

                    // Можно в колонке указать компонент отрисовки (vue) колонки
                    $row_column['renderer'] = $column['renderer'];

                } else {
                    $row_column['value'] = '!error grid value!';
                }
            }

            $columns[] = $row_column;
        }

        // если есть колонка действий, то формируем набор ссылок
        if ($this->grid['actions']) {

            $row_column = [
                'row_id'   => $row['id'],
                'value'    => [],
                'editable' => false,
                'class'    => false,
                'href'     => false,
                'renderer' => 'actions'
            ];

            foreach($this->grid['actions'] as $action){

                if (isset($action['handler'])) {

                    $is_active = $action['handler']($row);

                    unset($action['handler']);
                } else {
                    $is_active = true;
                }

                if (!$is_active) {
                    continue;
                }

                // парсим шаблон адреса, заменяя значения полей
                if (isset($action['href'])){
                    $action['href'] = string_replace_keys_values_extended($action['href'], $row);
                }

                // парсим шаблон запроса подтверждения, заменяя значения полей
                if (isset($action['confirm'])) {

                    $action['confirm'] = string_replace_keys_values_extended($action['confirm'], $row);
                }

                // все действия с подтверждением снабжаем csrf_token
                if (isset($action['confirm']) && !empty($action['href'])) {

                    $action['href'] .= (strpos($action['href'], '?') !== false ? '&' : '?') . 'csrf_token=' . cmsForm::getCSRFToken();
                }

                if (!empty($action['icon'])) {

                    $icon_params = explode(':', $action['icon']);

                    if (!isset($icon_params[1])) {
                        array_unshift($icon_params, 'solid');
                    }

                    $action['icon'] = html_svg_icon($icon_params[0], $icon_params[1], 16, false);
                }

                $row_column['value'][] = $action;
            }

            $columns[] = $row_column;
        }

        return $columns;
    }

    /**
     * Собирает заголовки таблицы
     *
     * @return array
     */
    private function makeColumns() {

        $columns = [];

        foreach ($this->getVisibleColumns() as $name => $column) {

            $column = array_merge($this->default_column, $column);

            $class = [];

            if (!empty($column['class'])) {
                $class[] = $column['class'];
            }

            if ($name === 'id' && !$this->grid['options']['show_id']) {
                $class[] = 'd-none';
            }

            $filter = false;

            if($column['filter'] && $column['filter'] !== 'none'){

                $filter = [
                    'component' => $this->filter_component_map[$column['filter']],
                    'params'    => [
                        'attributes' => array_merge($column['filter_attributes'], [
                            'name' => $name
                        ])
                    ]
                ];

                switch ($filter['component']) {
                    case 'form-date-range':
                    case 'form-range':
                        $filter['params']['lang_from'] = LANG_FROM;
                        $filter['params']['lang_to'] = LANG_TO;
                        break;
                    case 'form-input':
                        $filter['params']['attributes']['type'] = 'search';
                        break;
                    case 'form-checkbox':
                        $filter['params']['title'] = $column['filter_checkbox'] ?? false;
                        break;
                    case 'form-multiselect':
                    case 'form-select':
                        $filter['params']['items'] = is_array($column['filter_select']['items']) ? $column['filter_select']['items'] : $column['filter_select']['items']($name);
                        if(!$filter['params']['items']){
                            $filter['component'] = 'form-input';
                            $filter['params']['attributes']['type'] = 'search';
                        }
                        break;
                }
            }

            $columns[] = [
                'width'    => $column['width'] ?? '',
                'title'    => $column['title'] ?? '',
                'name'     => $name,
                'filter'   => $filter,
                'class'    => implode(' ', $class),
                'sortable' => $this->grid['options']['is_sortable'] && $column['sortable'],
            ];
        }

        if ($this->grid['actions']) {

            $filter = [
                'component' => 'form-filter',
                'params' => [
                    'href'        => $this->grid['options']['advanced_filter'],
                    'lang_filter' => LANG_FILTER,
                    'lang_cancel' => LANG_CANCEL,
                    'icon_filter' => html_svg_icon('solid', 'search-plus', 16, false),
                    'icon_cancel' => html_svg_icon('solid', 'search-minus', 16, false)
                ]
            ];

            $columns[] = [
                'sortable' => false,
                'width'    => count($this->grid['actions']) * 30,
                'title'    => LANG_CP_ACTIONS,
                'class'    => 'text-right align-middle',
                'name'     => 'advanced_filter',
                'filter'   => $this->grid['options']['advanced_filter'] ? $filter : false
            ];
        }

        return $columns;
    }

    private function getEditableParams($row, $column, $field) {

        if (!array_key_exists('editable', $column) || empty($row['id'])) {
            return false;
        }

        $save_action_query = [
            'csrf_token' => cmsForm::getCSRFToken(),
            'name' => $field,
            'id' => $row['id']
        ];

        // Экшен списка записей должен реализовывать и сохранение поля столбца
        $save_action = $this->grid['source_url'];

        // @deprecated Передача таблицы устарела
        if (!empty($column['editable']['table'])) {
            $save_action = href_to('admin', 'inline_save', [urlencode($column['editable']['table']), $row['id']]);
        }

        if (!empty($column['editable']['save_action'])) {
            $save_action = string_replace_keys_values_extended($column['editable']['save_action'], $row);
        }

        if(!isset($save_action)){
            return false;
        }

        $attributes = ['autocomplete' => 'off'];

        if (!empty($column['editable']['attributes'])) {
            foreach ($column['editable']['attributes'] as $akey => $avalue) {
                if (is_string($avalue)) {
                    $attributes[$akey] = string_replace_keys_values_extended($avalue, $row);
                } else {
                    $attributes[$akey] = $avalue;
                }
            }
        }

        return [
            'component'   => $column['editable']['renderer'] ?? 'form-input',
            'items'       => $column['editable']['items'] ?? [],
            'edit_icon'   => html_svg_icon('solid', 'pen', 16, false),
            'value'       => $row[$field] ?? '',
            'attributes'  => $attributes,
            'lang_edit'   => LANG_EDIT,
            'lang_save'   => LANG_SAVE,
            'save_action' => $save_action . '?' . http_build_query($save_action_query)
        ];
    }

}
