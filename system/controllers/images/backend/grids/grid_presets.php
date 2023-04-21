<?php

function grid_presets($controller) {

    $options = [
        'is_sortable'   => false,
        'is_filter'     => false,
        'is_pagination' => false,
        'is_draggable'  => false,
        'show_id'       => false
    ];

    $columns = [
        'id' => [
            'title' => 'id'
        ],
        'title' => [
            'title'    => LANG_IMAGES_PRESET,
            'href'     => href_to($controller->root_url, 'presets_edit', ['{id}']),
            'editable' => []
        ],
        'name' => [
            'title'      => LANG_SYSTEM_NAME,
            'switchable' => true
        ],
        'width' => [
            'title'      => LANG_IMAGES_PRESET_SIZE,
            'width'      => 100,
            'switchable' => true,
            'handler'    => function ($val, $row) {
                return ($val ? $val : LANG_AUTO) . ' x ' . ($row['height'] ? $row['height'] : LANG_AUTO);
            }
        ],
        'convert_format' => [
            'title'      => LANG_CP_FORMAT,
            'switchable' => true,
            'handler'    => function ($val, $row) {
                return $val ?: LANG_IMAGES_PRESET_OUT_ASIS;
            },
            'editable' => [
                'renderer' => 'form-select',
                'items' => [
                    ''     => LANG_IMAGES_PRESET_OUT_ASIS,
                    'jpg'  => 'JPG',
                    'png'  => 'PNG',
                    'webp' => 'WEBP'
                ]
            ]
        ],
        'quality' => [
            'title'      => LANG_IMAGES_PRESET_QUALITY,
            'switchable' => true,
            'handler'    => function ($val, $row) {
                return $val . ' %';
            },
            'editable' => [],
            'width'    => 70
        ],
        'is_square' => [
            'title'      => LANG_IMAGES_PRESET_CROP,
            'switchable' => true,
            'flag'       => true,
            'width'      => 120
        ],
        'is_watermark' => [
            'title'      => LANG_IMAGES_PRESET_WM,
            'switchable' => true,
            'flag'       => true,
            'width'      => 100
        ]
    ];

    $actions = [
        [
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href'  => href_to($controller->root_url, 'presets_edit', ['{id}']),
        ],
        [
            'title'   => LANG_DELETE,
            'class'   => 'delete',
            'href'    => href_to($controller->root_url, 'presets_delete', ['{id}']),
            'confirm' => LANG_IMAGES_PRESET_DELETE_CONFIRM,
            'handler' => function ($row) {
                if ($row['is_internal']) {
                    return false;
                }
                return true;
            }
        ]
    ];

    return [
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    ];
}
