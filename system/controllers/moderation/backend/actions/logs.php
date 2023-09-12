<?php

class actionModerationLogs extends cmsAction {

    public function run($target_controller = null, $target_subject = null, $target_id = null, $moderator_id = null) {

        cmsCore::loadAllControllersLanguages();

        $grid = $this->loadDataGrid('logs');

        $url             = href_to($this->root_url, 'logs');
        $sub_url         = [];
        $url_query       = [];
        $additional_h1   = [];
        $subj_controller = null;

        $action         = $this->request->get('action', -1);
        $only_to_delete = $this->request->get('only_to_delete', 0);

        if ($action > -1) {

            $this->model->filterEqual('action', $action);

            if ($only_to_delete) {

                $this->model->filterNotNull('date_expired');

                $url_query['only_to_delete'] = $only_to_delete;
            }

            $additional_h1[] = string_lang('LANG_MODERATION_ACTION_' . $action);

            $url_query['action'] = $action;
        }

        if (!empty($target_controller)) {

            $subj_controller = cmsCore::getController($target_controller);

            $this->model->filterEqual('target_controller', $target_controller);

            $sub_url[] = $target_controller;

            if (!empty($target_subject)) {

                $ctype = $subj_controller->getContentTypeForModeration($target_subject);

                if ($ctype) {
                    $target_subject = $ctype['name'];
                } else {
                    return cmsCore::error404();
                }

                $this->model->filterEqual('target_subject', $target_subject);

                $sub_url[] = $target_subject;

                if ($ctype) {
                    $additional_h1[] = $ctype['title'];
                }

                if (!empty($target_id)) {

                    $this->model->filterEqual('target_id', $target_id);

                    $sub_url[] = $target_id;

                    $this->model->lockFilters();

                    $item = $this->model->getItem('moderators_logs', function ($item, $model) {
                        $item['data'] = cmsModel::yamlToArray($item['data']);
                        return $item;
                    });

                    if ($item) {
                        $additional_h1[] = $item['data']['title'];
                    }

                    $this->model->unlockFilters();
                }
            }
        }

        if (!empty($moderator_id)) {

            $this->model->filterEqual('moderator_id', $moderator_id);

            if (count($sub_url) == 3) {
                $sub_url[] = $moderator_id;
            } elseif (count($sub_url) == 2) {
                $sub_url[] = 0;
                $sub_url[] = $moderator_id;
            } elseif (count($sub_url) == 1) {
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

            if ($user) {
                $additional_h1[] = $user['nickname'];
            }
        }

        if ($this->request->isAjax()) {

            $filter     = [];
            $filter_str = $this->request->get('filter', '');

            if ($filter_str) {
                parse_str($filter_str, $filter);
                $grid->applyGridFilter($this->model, $filter, 'moderators_logs');
            }

            $total = $this->model->getCount('moderators_logs');

            $this->model->joinUserLeft('moderator_id');

            $data = $this->model->get('moderators_logs', function ($item, $model) use ($subj_controller) {

                $item['data'] = cmsModel::yamlToArray($item['data']);

                $item['controller_title'] = string_lang($item['target_controller'] . '_CONTROLLER');

                $item['subject_title'] = $item['controller_title'];

                if ($subj_controller !== null) {

                    $ctype = $subj_controller->getContentTypeForModeration($item['target_subject']);

                    $item['subject_title'] = $ctype['title'];
                }

                return $item;
            }) ?: [];

            return $this->cms_template->renderJSON($grid->makeGridRows($data, $total));
        }

        if ($additional_h1) {
            $this->cms_template->setPageH1($additional_h1);
        }

        $this->model->resetFilters();

        return $this->cms_template->render('backend/logs', [
            'grid'      => $grid,
            'sub_url'   => $sub_url,
            'url_query' => $url_query,
            'url'       => $url . ($sub_url ? '/' . implode('/', $sub_url) : '') . (($action > -1) ? '?' . http_build_query($url_query) : '')
        ]);
    }
}
