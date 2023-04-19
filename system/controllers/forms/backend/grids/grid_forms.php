<?php

function grid_forms($controller) {

    $options = [];

    $columns = [
        'id' => [
            'title'  => 'id',
            'filter' => 'exact'
        ],
        'title' => [
            'title'  => LANG_TITLE,
            'href'   => href_to($controller->root_url, 'edit', '{id}'),
            'filter' => 'like'
        ],
        'name' => [
            'title'  => LANG_SYSTEM_NAME,
            'filter' => 'like'
        ]
    ];

    if (!empty($controller->options['allow_embed'])) {

        $columns['hash'] = [
            'title'   => LANG_FORMS_CP_FORMS_EMBED,
            'handler' => function ($v, $row) {
                return html_input('text', '', '<script src="' . href_to_abs('forms', 'framejs', $v) . '"></script>', ['onclick' => '$(this).select();']);
            }
        ];
    }

    if (!empty($controller->options['allow_shortcode'])) {

        $columns['tpl_form'] = [
            'title'   => LANG_FORMS_CP_FORMS_SHORTCODE,
            'handler' => function ($v, $row) {
                return html_input('text', '', '{forms:' . $row['name'] . '}', ['onclick' => '$(this).select();']);
            }
        ];
    }

    $actions = [
        [
            'title' => LANG_VIEW,
            'class' => 'view ajax-modal',
            'href'  => href_to($controller->root_url, 'view', '{id}')
        ],
        [
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->root_url, 'edit', '{id}'),
        ],
        [
            'title' => LANG_CP_CTYPE_FIELDS,
            'class' => 'fields',
            'href'  => href_to($controller->root_url, 'form_fields', '{id}')
        ],
        [
            'title' => LANG_FORMS_CP_FORMS_COPY,
            'class' => 'copy',
            'href'  => href_to($controller->root_url, 'copy', '{id}'),
        ],
        [
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->root_url, 'delete', '{id}'),
            'confirm' => LANG_FORMS_CP_FORM_DELETE_CONFIRM
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
