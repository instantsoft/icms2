<?php

class onContentAdminDashboardChart extends cmsAction {

    public function run() {

        $ctypes = $this->model->getContentTypesFiltered();

        $data = [
            'id'       => 'content',
            'title'    => LANG_CONTENT,
            'sections' => [],
            'footer'   => []
        ];

        foreach ($ctypes as $ctype) {

            $ctype_table_name = $this->model->getContentTypeTableName($ctype['name']);

            $data['sections'][$ctype['name']] = [
                'title' => $ctype['title'],
                'table' => $ctype_table_name,
                'key'   => 'date_pub'
            ];

            $data['footer'][$ctype['name']][] = [
                'title'    => LANG_CP_TOTAL . ' ' . $ctype['labels']['many'],
                'table'    => $ctype_table_name,
                'progress' => 'success'
            ];

            $data['footer'][$ctype['name']][] = [
                'title'    => LANG_CP_ONMODERATE,
                'table'    => 'moderators_tasks',
                'progress' => 'warning',
                'filters'  => [
                    [
                        'condition' => 'eq',
                        'value'     => $ctype['name'],
                        'field'     => 'ctype_name'
                    ]
                ]
            ];

            $data['footer'][$ctype['name']][] = [
                'title'    => LANG_CONTENT_CONTEXT_LT_TRASH,
                'table'    => $ctype_table_name,
                'progress' => 'secondary',
                'filters'  => [
                    [
                        'condition' => 'eq',
                        'value'     => 1,
                        'field'     => 'is_deleted'
                    ]
                ]
            ];

            $data['footer'][$ctype['name']][] = [
                'title'    => LANG_CP_NOTPUB . ' ' . $ctype['labels']['many'],
                'table'    => $ctype_table_name,
                'progress' => 'danger',
                'filters'  => [
                    [
                        'condition' => 'lt',
                        'value'     => 1,
                        'field'     => 'is_pub'
                    ]
                ]
            ];
        }

        return $data;
    }

}
