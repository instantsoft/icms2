<?php

function grid_queue($controller, $contex_controller) {

    $options = [
        'is_sortable' => false,
        'show_id'     => false,
        'is_filter'   => false
    ];

    $columns = [
        'id' => [
            'title' => 'id',
            'width' => 30
        ],
        'date_created' => [
            'title'   => LANG_CP_QUEUE_DATE_CREATED,
            'width'   => 130,
            'handler' => function ($value, $item) {
                $is_expired = time() - strtotime($value) > 36400;
                $is_expired_start = $item['date_started'] && !$item['attempts'] && time() - strtotime($item['date_started']) > 36400;
                return html_bool_span(html_date($value, true), (!$is_expired || !$is_expired_start));
            }
        ],
        'date_started' => [
            'title'   => LANG_CP_QUEUE_DATE_STARTED,
            'class'   => 'd-none d-lg-table-cell',
            'width'   => 130,
            'handler' => function ($value, $item) {
                if (!$value) {
                    return '–';
                }
                if (!$item['attempts']) {
                    return html_date($value, true);
                }
                $is_expired = (time() - strtotime($value) > 36400);
                return html_bool_span(html_date($value, true), !$is_expired);
            }
        ],
        'last_error' => [
            'title'   => LANG_ERROR,
            'class'   => 'd-none d-lg-table-cell',
            'handler' => function ($value, $item) {
                return html_bool_span($value, false);
            }
        ],
        'is_locked' => [
            'title'   => LANG_CP_QUEUE_STATUS,
            'handler' => function ($value, $item) {
                if ($value && !$item['last_error']) {
                    return LANG_CP_QUEUE_STATUS1 . ' <div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>';
                }
                if ($value && $item['last_error']) {
                    return LANG_CP_QUEUE_STATUS2;
                }
                if (!$item['date_started'] || ($item['date_started'] && !$item['attempts'])) {
                    return LANG_CP_QUEUE_STATUS3;
                }
                if ($item['attempts']) {
                    return sprintf(LANG_CP_QUEUE_STATUS3, ($item['attempts'] + 1));
                }
                return '–';
            }
        ]
    ];

    if ((count($contex_controller->queue['queues']) > 1)) {
        $columns['queue'] = [
            'title' => LANG_CP_QUEUE,
            'width' => 90
        ];
    }

    $actions = [
        [
            'title'   => LANG_CP_QUEUE_QUEUE_RESTART,
            'class'   => 'play',
            'href'    => href_to($contex_controller->root_url, 'queue_restart', ['{id}']),
            'handler' => function ($row) {
                return $row['is_locked'] && $row['last_error'];
            }
        ],
        [
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($contex_controller->root_url, 'queue_delete', ['{id}']),
            'confirm' => LANG_CP_SCHEDULER_TASK_DELETE_CONFIRM,
            'handler' => function ($row) {
                return !$row['is_locked'] || ($row['is_locked'] && $row['last_error']);
            }
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];

}
