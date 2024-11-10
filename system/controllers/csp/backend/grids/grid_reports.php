<?php

function grid_reports($controller) {

    $options = [
        'is_sortable'    => false,
        'is_filter'      => true,
        'is_pagination'  => true,
        'is_draggable'   => false,
        'is_selectable'  => true,
        'order_by'       => 'date_pub',
        'order_to'       => 'desc',
        'show_id'        => false,
        'select_actions' => [
            [
                'title'   => LANG_DELETE,
                'action'  => 'submit',
                'confirm' => LANG_CSP_DELETE_CONFIRM,
                'url'     => $controller->cms_template->href_to('reports_delete')
            ]
        ]
    ];

    $columns = [
        'id' => [
            'title' => 'id'
        ],
        'date_pub' => [
            'title'   => LANG_DATE,
            'handler' => function ($value, $item) {
                return html_date($value, true);
            },
            'filter' => 'date'
        ],
        'blocked_uri' => [
            'title'   => LANG_CSP_BLOCKED_URI,
            'filter'  => 'like'
        ],
        'referrer' => [
            'title'      => 'HTTP referer',
            'disable'    => true,
            'switchable' => true,
            'filter'     => 'like'
        ],
        'document_uri' => [
            'title'      => LANG_CSP_DOCUMENT_URI,
            'disable'    => true,
            'switchable' => true,
            'filter'     => 'like'
        ],
        'line_number' => [
            'title'      => LANG_CSP_LINE_NUMBER,
            'disable'    => true,
            'switchable' => true
        ],
        'status_code' => [
            'title'      => LANG_CSP_STATUS_CODE,
            'switchable' => true,
            'filter'     => 'like'
        ],
        'effective_directive' => [
            'title'      => LANG_CSP_EFFECTIVE_DIRECTIVE,
            'disable'    => true,
            'switchable' => true,
            'filter'     => 'like'
        ],
        'violated_directive' => [
            'title'  => LANG_CSP_VIOLATED_DIRECTIVE,
            'filter' => 'like'
        ],
        'ip' => [
            'title'      => LANG_CSP_IP,
            'width'      => 120,
            'sortable'   => false,
            'filter'     => 'ip',
            'switchable' => true,
            'handler'    => function ($value) {
                if ($value) {
                    $value    = string_bintoip($value);
                    $location = string_ip_to_location($value, true);
                    return $value . (!empty($location['code']) ? '&nbsp;' . $location['code'] : '');
                }
                return '';
            }
        ],
    ];

    $actions = [];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
