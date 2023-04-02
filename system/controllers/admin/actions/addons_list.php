<?php

class actionAdminAddonsList extends cmsAction {

    public function run() {

        if (!function_exists('curl_init')) {

            return $this->cms_template->render('addons_list', [
                'error_text' => LANG_CP_ADDONS_CURL_ERROR
            ]);
        }

        $dataset_id = $this->request->get('dataset_id', 0);
        $cat_id     = $this->request->get('cat_id', 0);
        $page       = $this->request->get('page', 0);
        $is_paid    = $this->request->get('is_paid', 0);
        $title      = $this->request->get('title', '');

        if ($this->request->isAjax()) {

            $params = ['branch' => 2];

            if ($dataset_id > 0) {
                $params['dataset_id'] = $dataset_id;
            }

            if ($dataset_id < 0) {

                $installed_ids = $this->model->getInstalledAddonsIds();

                if (!$installed_ids) {

                    return $this->cms_template->renderPlain('addons_list_data', [
                        'count'    => 0,
                        'has_next' => 0,
                        'items'    => false
                    ]);
                }

                $params['ids'] = implode(',', $installed_ids);
            }

            if ($cat_id) {
                $params['cat_id'] = $cat_id;
            }

            if ($page) {
                $params['page'] = $page;
            }

            if ($title) {
                $params['title'] = $title;
            }

            if ($is_paid) {
                $params['is_paid'][] = $is_paid - 1;
            }

            $items = $this->getAddonsMethod('content.get.addons', $params);

            if (empty($items['response']['items'])) {

                return $this->cms_template->renderPlain('addons_list_data', [
                    'count'    => 0,
                    'has_next' => 0,
                    'items'    => false
                ]);
            }

            $items['response']['items'] = $this->checkInstalledPackages($items['response']['items']);

            return $this->cms_template->renderPlain('addons_list_data', [
                'count'        => $items['response']['count'],
                'has_next'     => (int) $items['response']['paging']['has_next'],
                'items'        => (empty($items['response']['items']) ? [] : $items['response']['items']),
                'core_version' => cmsCore::getVersion()
            ]);
        }

        $datasets = $this->getAddonsMethod('content.get_datasets.addons', [], true);

        if (!empty($datasets['response']['items']) && $dataset_id == 0) {
            $first_dataset = reset($datasets['response']['items']);
            $dataset_id    = $first_dataset['id'];
        }

        $cats = $this->getAddonsMethod('content.get_categories.addons', [], true);

        if (!$datasets || !$cats) {

            return $this->cms_template->render('addons_list', [
                'error_text' => LANG_CP_ADDONS_DATA_ERROR
            ]);
        }

        $datasets = (isset($datasets['response']['items']) ? $datasets['response']['items'] : []);

        $datasets['update'] = [
            'id'    => -1,
            'name'  => 'installed',
            'title' => LANG_CP_ADDDONS_DS_INSTALLED
        ];

        return $this->cms_template->render('addons_list', [
            'error_text' => false,
            'dataset_id' => $dataset_id,
            'cat_id'     => $cat_id,
            'datasets'   => $datasets,
            'cats'       => (isset($cats['response']['items']) ? $cats['response']['items'] : [])
        ]);
    }

    private function checkInstalledPackages($items) {

        $ids = array();

        foreach ($items as $item) {
            $ids[$item['type_raw']][] = $item['id'];
        }

        if (!empty($ids[1])) {

            $controllers = $this->model->selectOnly('name')->select('id')->
                    select('addon_id')->select('version')->filterIn('addon_id', $ids[1])->
                    get('controllers', false, 'addon_id');
        }

        if (!empty($ids[2])) {

            $widgets = $this->model->selectOnly('id')->select('name')->
                    select('addon_id')->select('version')->filterIn('addon_id', $ids[2])->
                    get('widgets', false, 'addon_id');
        }

        $result_items = [];

        foreach ($items as $key => $item) {

            $latest_version = reset($item['versions']);

            $item['install'] = [
                'need_update'   => false,
                'need_install'  => true,
                'install_url'   => ($item['versions'] ? $latest_version['download_url'] : ''),
                'installed_url' => '',
                'install_title' => LANG_CP_DO_INSTALL
            ];

            $installed = false;

            if (isset($controllers[$item['id']])) {

                $installed = $controllers[$item['id']];
            } else if (isset($widgets[$item['id']])) {

                $installed = $widgets[$item['id']];
            }

            if ($installed) {

                $version_compare = version_compare($latest_version['name'], $installed['version']);

                if ($version_compare == 0) {

                    $item['install']['need_install']  = false;
                    $item['install']['install_url']   = '';
                    $item['install']['installed_url'] = $item['type_raw'] == 2 ? href_to('admin', 'widgets') : href_to('admin', 'controllers', ['edit', $installed['name']]);
                }

                if ($version_compare > 0) {

                    $versions = array_keys($item['versions']);

                    $installed_key = array_search($installed['version'], $versions);

                    // версию нашли
                    if ($installed_key !== false) {
                        $prev_key = $installed_key - 1;
                    } else {
                        // версия есть, но её нет :)
                        // значит у автора дополнения версия в манифесте и версия в каталоге не совпадают
                        $prev_key = 0;
                    }

                    $next_version = $item['versions'][$versions[$prev_key]];

                    $item['install']['need_install']  = false;
                    $item['install']['need_update']   = true;
                    $item['install']['install_title'] = LANG_CP_DO_UPDATE;
                    $item['install']['install_url']   = $next_version['update_url'];
                }
            }

            $result_items[$key] = $item;
        }

        return $result_items;
    }

}
