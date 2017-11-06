<?php

class actionModerationLogs extends cmsAction {

    public function run($target_controller = null, $target_subject = null, $target_id = null, $moderator_id = null){

        cmsCore::loadAllControllersLanguages();

        $grid = $this->loadDataGrid('logs');

        $model = cmsCore::getModel('content');

        $url           = href_to($this->root_url, 'logs');
        $sub_url       = array();
        $url_query     = array();
        $additional_h1 = array();

        $action = $this->request->get('action', -1);
        $only_to_delete = $this->request->get('only_to_delete', 0);
        if($action > -1){

            $model->filterEqual('action', $action);

            if($only_to_delete){

                $model->filterNotNull('date_expired');

                $url_query['only_to_delete'] = $only_to_delete;

            }

            $additional_h1[] = string_lang('LANG_MODERATION_ACTION_'.$action);

            $url_query['action'] = $action;

        }

        if(!empty($target_controller)){

            $model->filterEqual('target_controller', $target_controller);

            $sub_url[] = $target_controller;

            if(!empty($target_subject)){

                if(is_numeric($target_subject) && $target_controller == 'content'){
                    $ctype = $model->getContentType($target_subject);
                    if($ctype){
                        $target_subject = $ctype['name'];
                    }
                } else {
                    $ctype = $model->getContentTypeByName($target_subject);
                }

                $model->filterEqual('target_subject', $target_subject);

                $sub_url[] = $target_subject;

                if($ctype){
                    $additional_h1[] = $ctype['title'];
                }

                if(!empty($target_id)){

                    $model->filterEqual('target_id', $target_id);

                    $sub_url[] = $target_id;

                    $model->lockFilters();

                        $item = $model->getItem('moderators_logs', function ($item, $model){
                            $item['data'] = cmsModel::yamlToArray($item['data']);
                            return $item;
                        });

                        if($item){
                            $additional_h1[] = $item['data']['title'];
                        }

                    $model->unlockFilters();

                }

            }

        }

        if(!empty($moderator_id)){

            $model->filterEqual('moderator_id', $moderator_id);

            if(count($sub_url) == 3){
                $sub_url[] = $moderator_id;
            } elseif(count($sub_url) == 2){
                $sub_url[] = 0;
                $sub_url[] = $moderator_id;
            } elseif(count($sub_url) == 1){
                $sub_url[] = 0;
                $sub_url[] = 0;
                $sub_url[] = $moderator_id;
            } else {
                $sub_url[] = 0;
                $sub_url[] = 0;
                $sub_url[] = 0;
                $sub_url[] = $moderator_id;
            }

            $user = cmsCore::getModel('users')->getuser($moderator_id);

            if($user){
                $additional_h1[] = $user['nickname'];
            }

        }

        if ($this->request->isAjax()) {

            $filter     = array();
            $filter_str = $this->request->get('filter', '');

            if ($filter_str){
                parse_str($filter_str, $filter);
                $model->applyGridFilter($grid, $filter);
            }

            $total = $model->getCount('moderators_logs');
            $perpage = isset($filter['perpage']) ? $filter['perpage'] : admin::perpage;
            $pages = ceil($total / $perpage);

            $model->joinUserLeft('moderator_id');

            $data = $model->get('moderators_logs', function ($item, $model){
                $item['data'] = cmsModel::yamlToArray($item['data']);
                $item['controller_title'] = string_lang($item['target_controller'].'_CONTROLLER');
                if($item['target_controller'] == 'content'){
                    $ctype = $model->getContentTypeByName($item['target_subject']);
                    $item['subject_title'] = $ctype['title'];
                }
                return $item;
            });

            $this->cms_template->renderGridRowsJSON($grid, $data, $total, $pages);

            $this->halt();

        }

        if($additional_h1){
            $this->setH1($additional_h1);
        }

        $model->resetFilters();

		return $this->cms_template->render('backend/logs', array(
            'grid'      => $grid,
            'sub_url'   => $sub_url,
            'url_query' => $url_query,
            'url'       => $url.($sub_url ? '/'.implode('/', $sub_url) : '').(($action > -1) ? '?'.http_build_query($url_query) : '')
        ));

    }

}
