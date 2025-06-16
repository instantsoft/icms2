<?php

function grid_outs($controller) {

    $options = [
        'is_sortable'   => true,
        'is_filter'     => true,
        'is_pagination' => true,
        'is_draggable'  => false,
        'is_selectable' => false,
        'order_by'      => 'date_created',
        'order_to'      => 'desc',
        'show_id'       => true
    ];

    $columns = [
        'id' => [
            'title'  => 'id',
            'width'  => 30,
            'filter' => 'exact',
            'handler' => function ($value) {
                return '# ' . $value;
            }
        ],
        'date_created' => [
            'title'   => LANG_BILLING_LOG_DATE,
            'width'   => 150,
            'filter'  => 'date',
            'handler' => function ($value) {
                return html_date_time($value);
            }
        ],
        'user_nickname' => [
            'title'     => LANG_USER,
            'href'      => href_to('users', '{user_id}'),
            'order_by'  => 'u.nickname',
            'filter'    => 'like',
            'filter_by' => 'u.nickname'
        ],
        'system' => [
            'title'  => LANG_BILLING_OUT_SYSTEM,
            'filter' => 'like'
        ],
        'purse' => [
            'title'  => LANG_BILLING_OUT_PURSE,
            'filter' => 'like'
        ],
        'amount' => [
            'title'   => LANG_BILLING_OUT_AMOUNT,
            'filter'  => 'like',
            'handler' => function ($value) {
                $value *= -1;
                return $value ? '<span class="' . html_signed_class($value) . '">' . html_signed_num(nf($value, 2, '')) . '</span>' : '&mdash;';
            }
        ],
        'summ' => [
            'title'   => LANG_BILLING_OUT_SUMM,
            'filter'  => 'like',
            'handler' => function ($value) use($controller) {
                return $value ? nf($value, 2, '').' '.$controller->options['cur_real_symb'] : '&mdash;';
            }
        ],
        'status' => [
            'title'   => LANG_BILLING_OUT_CLOSED,
            'flag'    => true,
            'flag_on' => 2
        ]
    ];

    $actions = [
        [
            'title'   => LANG_CLOSE,
            'class'   => 'accept',
            'confirm' => LANG_BILLING_OUT_CLOSE_CONFIRM,
            'href'    => href_to($controller->root_url, 'outs', ['done', '{id}']),
            'handler' => function ($out) {
                return $out['status'] < modelBilling::OUT_STATUS_DONE;
            }
        ],
        [
            'title'   => LANG_CANCEL,
            'class'   => 'delete',
            'confirm' => LANG_BILLING_OUT_CANCEL_CONFIRM,
            'href'    => href_to($controller->root_url, 'outs', ['cancel', '{id}']),
            'handler' => function ($out) {
                return $out['status'] < modelBilling::OUT_STATUS_DONE;
            }
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
