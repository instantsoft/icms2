<?php

function grid_form_fields($controller, $form_data) {

    $options = [
        'is_sortable'   => false,
        'is_filter'     => true,
        'is_pagination' => false,
        'is_draggable'  => true,
        'drag_save_url' => href_to('admin', 'reorder', 'forms_fields'),
        'order_by'      => 'ordering',
        'order_to'      => 'asc',
        'show_id'       => false
    ];

    $columns = [
        'id' => [
            'title' => 'id'
        ],
        'title' => [
            'title' => LANG_CP_FIELD_TITLE,
            'href'  => href_to($controller->root_url, 'fields_edit', ['{id}']),
            'editable' => [
                'rules' => [
                    ['required'],
                    ['max_length', 100]
                ]
            ]
        ],
        'fieldset' => [
            'title'   => LANG_CP_FIELD_FIELDSET,
            'class'   => 'd-none d-lg-table-cell',
            'handler' => function ($value, $row) {
                return $value ? $value : '&mdash;';
            },
            'filter' => 'exact',
            'filter_select' => [
                'items' => function($name) use ($form_data) {
                    $fieldsets = cmsCore::getModel('forms')->getFormFieldsets($form_data['id']);
                    $items = ['' => LANG_ALL];
                    foreach($fieldsets as $fieldset) { $items[$fieldset] = $fieldset; }
                    return $items;
                }
            ]
        ],
        'is_enabled' => [
            'title'       => LANG_IS_ENABLED,
            'flag'        => true,
            'flag_toggle' => href_to($controller->root_url, 'toggle_item', ['{id}', 'forms_fields', 'is_enabled']),
            'width'       => 80
        ],
        'name' => [
            'title' => LANG_SYSTEM_NAME,
            'class' => 'd-none d-lg-table-cell',
            'width' => 120,
        ],
        'type' => [
            'title' => LANG_CP_FIELD_TYPE,
            'class' => 'd-none d-lg-table-cell',
            'width' => 150,
            'handler' => function ($value, $row) {
                return $row['handler_title'];
            },
            'filter' => 'exact',
            'filter_select' => [
                'items' => function($name) {
                    return ['' => LANG_ALL] + cmsForm::getAvailableFormFields('only_public', 'forms');
                }
            ]
        ]
    ];

    $actions = [
        [
            'title' => LANG_COPY,
            'class' => 'copy',
            'href'  => href_to($controller->root_url, 'fields_add', ['{form_id}', '{id}'])
        ],
        [
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->root_url, 'fields_edit', ['{id}'])
        ],
        [
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->root_url, 'fields_delete', ['{id}']),
            'confirm' => LANG_CP_FIELD_DELETE_CONFIRM
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
