<?php
namespace icms\controllers\admin\traits;
/**
 * @property \cmsModel $model
 */
trait listgrid {

    protected $trait_params = [
        'table_name'    => '',
        'grid_name'     => '',
        'grid_url'      => '',
        'title'         => '',
        'tool_buttons'  => [],
        'list_callback' => null
    ];

    protected $ups_key, $grid;

    protected function setProperty($key, $value) {
        $this->trait_params[$key] = $value;
    }

    public function run(){

        $this->setListGridParams();

        if ($this->request->isAjax()) {

            return $this->getListItems();
        }

        return $this->renderListItemsGrid();
    }

    public function setListGridParams() {

        $this->ups_key = 'admin.grid_filter.' . $this->name .'_'. $this->trait_params['grid_name'];

        $this->grid = $this->loadDataGrid($this->trait_params['grid_name'], false, $this->ups_key);

    }

    public function renderListItemsGrid(){

        if($this->trait_params['tool_buttons']){
            $this->cms_template->addToolButtons($this->trait_params['tool_buttons']);
        }

        return $this->cms_template->getRenderedAsset('ui/grid', [
            'grid'       => $this->grid,
            'page_title' => $this->trait_params['title'],
            'source_url' => $this->trait_params['grid_url']
        ]);
    }

    public function getListItems(){

        $this->model->setPerPage(\admin::perpage);

        $filter     = [];
        $filter_str = \cmsUser::getUPSActual($this->ups_key, $this->request->get('filter', ''));

        if ($filter_str){
            \parse_str($filter_str, $filter);
            $this->model->applyGridFilter($this->grid, $filter);
        }

        if($this->trait_params['list_callback']){
            $this->model = \call_user_func($this->trait_params['list_callback'], $this->model);
        }

        $total   = $this->model->getCount($this->trait_params['table_name']);
        $perpage = isset($filter['perpage']) ? $filter['perpage'] : \admin::perpage;
        $pages   = \ceil($total / $perpage);

        $data = $this->model->get($this->trait_params['table_name']);

        $this->cms_template->renderGridRowsJSON($this->grid, $data, $total, $pages);

        return $this->halt();
    }

}
