<?php
class actionCommentsCommentsList extends cmsAction {

    public function run(){

        $grid = $this->loadDataGrid('comments_list');

        if ($this->request->isAjax()) {

            $this->model->setPerPage(admin::perpage);

            $filter     = array();
            $filter_str = $this->request->get('filter', '');

            if ($filter_str){
                parse_str($filter_str, $filter);
                $this->model->applyGridFilter($grid, $filter);
            }

            $data = $this->model->joinUserLeft()->get('comments');

            $this->cms_template->renderGridRowsJSON($grid, $data);

            $this->halt();

        }

        return $this->cms_template->render('backend/comments_list', array(
            'grid' => $grid
        ));

    }

}
