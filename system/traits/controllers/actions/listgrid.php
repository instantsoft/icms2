<?php

namespace icms\traits\controllers\actions;

use cmsUser;

/**
 * Трейт для экшена вывода грида
 *
 * @property \cmsTemplate $cms_template
 * @property \cmsUser $cms_user
 * @property \cmsRequest $request
 * @property \cmsModel $model
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
     * Коллбэк для модели где получается список данных
     * @var callable
     */
    protected $list_callback = null;

    /**
     * Коллбэк для полученого списка записей
     * @var callable
     */
    protected $items_callback = null;

    /**
     * Префикс действия, если надо передать управление другому экшену
     * @var string
     */
    protected $external_action_prefix = '';

    /**
     * Ключ UPS
     * @var string
     */
    protected $ups_key;

    /**
     * Префикс ключа UPS для уникализации
     * @var string
     */
    protected $ups_key_prefix = '';

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

    public function run($do = false){

        // если нужно, передаем управление другому экшену
        if ($do && !is_numeric($do)) {

            $this->runExternalAction($this->external_action_prefix.$do, array_slice($this->params, 1));
            return;
        }

        $this->setListGridParams();

        if ($this->request->isAjax()) {

            return $this->getListItems();
        }

        return $this->renderListItemsGrid();
    }

    public function setListGridParams() {

        $this->ups_key = 'grid_filter.' . $this->ups_key_prefix . $this->name .'_'. $this->grid_name;

        $this->grid = $this->loadDataGrid($this->grid_name, $this->grid_args, $this->ups_key);

    }

    public function getListItemsGridHtml(){

        $this->cms_template->addToolButtons($this->tool_buttons);

        return $this->cms_template->getRenderedAsset('ui/grid', [
            'grid'       => $this->grid,
            'page_title' => $this->title,
            'source_url' => $this->grid_url ? $this->grid_url : $this->cms_template->href_to($this->current_action)
        ]);
    }

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

    public function getListItems(){

        $this->model->setPerPage($this->default_perpage);

        $filter     = [];
        $filter_str = cmsUser::getUPSActual($this->ups_key, $this->request->get('filter', ''));

        if ($filter_str){
            parse_str($filter_str, $filter);
            $this->model->applyGridFilter($this->grid, $filter);
        }

        if($this->list_callback){
            $this->model = call_user_func_array($this->list_callback, [$this->model]);
        }

        $total   = $this->model->getCount($this->table_name);
        $perpage = isset($filter['perpage']) ? $filter['perpage'] : $this->default_perpage;
        $pages   = ceil($total / $perpage);

        $data = $this->model->get($this->table_name);

        if($this->items_callback){
            $data = call_user_func_array($this->items_callback, [$data]);
        }

        $this->cms_template->renderGridRowsJSON($this->grid, $data, $total, $pages);

        return $this->halt();
    }

}
