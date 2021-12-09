<?php

class onActivityAdminDashboardBlock extends cmsAction {

    public function run($options) {

        if (!empty($options['only_titles'])) {
            return [
                'activity' => LANG_ACTIVITY
            ];
        }

        $dashboard_blocks = [];

        if (!array_key_exists('activity', $options['dashboard_enabled']) || !empty($options['dashboard_enabled']['activity'])) {

            $this->model->limit(50);

            $items = $this->model->getEntries();

            // запрещаем автоматически подключать файл css стилей контроллера
            $this->template_disable_auto_insert_css = true;

            $dashboard_blocks[] = [
                'title'   => LANG_ACTIVITY,
                'name'    => 'activity',
                'actions' => [
                    [
                        'url'  => href_to('admin', 'controllers', ['edit', $this->name]),
                        'hint' => LANG_OPTIONS,
                        'icon' => 'cog'
                    ]
                ],
                'html'    => $this->cms_template->renderInternal($this, 'backend/admin_dashboard_block_activity', [
                    'items' => $items
                ])
            ];
        }

        return $dashboard_blocks;
    }

}
