<?php

class formImagesPreset extends cmsForm {

    public function init($do) {

        $fields = [
            'basic' => [
                'type'   => 'fieldset',
                'title'  => LANG_CP_BASIC,
                'childs' => [
                    new fieldString('name', [
                        'title' => LANG_SYSTEM_NAME,
                        'rules' => [
                            ['required'],
                            ['sysname'],
                            ['max_length', 32],
                            $do == 'add' ? ['unique', 'images_presets', 'name'] : false
                        ]
                    ]),
                    new fieldString('title', [
                        'title' => LANG_IMAGES_PRESET,
                        'rules' => [
                            ['required'],
                            ['max_length', 128]
                        ]
                    ]),
                ]
            ],
            'size' => [
                'type'   => 'fieldset',
                'title'  => LANG_OPTIONS,
                'childs' => [
                    new fieldNumber('width', [
                        'title' => LANG_IMAGES_PRESET_SIZE_W,
                        'hint'  => LANG_IMAGES_PRESET_SIZE_W_HINT,
                        'units' => 'px'
                    ]),
                    new fieldNumber('height', [
                        'title' => LANG_IMAGES_PRESET_SIZE_H,
                        'hint'  => LANG_IMAGES_PRESET_SIZE_H_HINT,
                        'units' => 'px'
                    ]),
                    new fieldCheckbox('allow_enlarge', [
                        'title' => LANG_IMAGES_PRESET_ALLOW_ENLARGE
                    ]),
                    new fieldCheckbox('is_square', [
                        'title' => LANG_IMAGES_PRESET_SQUARE
                    ]),
                    new fieldList('crop_position', [
                        'title'   => LANG_IMAGES_PRESET_CROP_POSITION,
                        'default' => cmsImages::CROPCENTER,
                        'items'   => [
                            cmsImages::CROPTOP       => LANG_CP_FIELD_LABEL_TOP,
                            cmsImages::CROPCENTER    => LANG_IMAGES_PRESET_CCENTER,
                            cmsImages::CROPTOPCENTER => LANG_IMAGES_PRESET_TOP_CENTER,
                            cmsImages::CROPBOTTOM    => LANG_IMAGES_PRESET_CBOTTOM,
                            cmsImages::CROPLEFT      => LANG_CP_FIELD_LABEL_LEFT,
                            cmsImages::CROPRIGHT     => LANG_IMAGES_PRESET_CRIGHT
                        ],
                        'visible_depend' => ['is_square' => ['show' => ['1']]]
                    ]),
                    new fieldCheckbox('gamma_correct', [
                        'title' => LANG_IMAGES_PRESET_GAMMA_CORRECT
                    ]),
                    new fieldList('convert_format', [
                        'title'   => LANG_IMAGES_PRESET_OUT_FORMAT,
                        'default' => '',
                        'items'   => [
                            ''     => LANG_IMAGES_PRESET_OUT_ASIS,
                            'jpg'  => 'JPG',
                            'png'  => 'PNG',
                            'webp' => 'WEBP'
                        ]
                    ]),
                    'gif_to_gif' => new fieldCheckbox('gif_to_gif', [
                        'title' => LANG_IMAGES_PRESET_GIF_TO_GIF,
                        'default' => 1,
                        'visible_depend' => ['convert_format' => ['hide' => ['']]]
                    ]),
                    new fieldNumber('quality', [
                        'title'   => LANG_IMAGES_PRESET_QUALITY,
                        'units'   => '%',
                        'default' => '87',
                        'rules'   => [
                            ['required'],
                            ['digits'],
                            ['min', 1],
                            ['max', 100]
                        ]
                    ])
                ]
            ],
            'watermark' => [
                'type'   => 'fieldset',
                'title'  => LANG_IMAGES_PRESET_WM,
                'childs' => [
                    new fieldCheckbox('is_watermark', [
                        'title' => LANG_IMAGES_PRESET_WM_ON
                    ]),
                    new fieldImage('wm_image', [
                        'title'   => LANG_IMAGES_PRESET_WM_IMG,
                        'options' => [
                            'sizes' => ['small', 'original']
                        ],
                        'visible_depend' => ['is_watermark' => ['show' => ['1']]]
                    ]),
                    new fieldList('wm_origin', [
                        'title' => LANG_IMAGES_PRESET_WM_ORIGIN,
                        'items' => [
                            'top-left'     => LANG_IMAGES_PRESET_WM_ORIGIN_TL,
                            'top-center'   => LANG_IMAGES_PRESET_WM_ORIGIN_T,
                            'top-right'    => LANG_IMAGES_PRESET_WM_ORIGIN_TR,
                            'left'         => LANG_IMAGES_PRESET_WM_ORIGIN_L,
                            'center'       => LANG_IMAGES_PRESET_WM_ORIGIN_C,
                            'right'        => LANG_IMAGES_PRESET_WM_ORIGIN_R,
                            'bottom-left'  => LANG_IMAGES_PRESET_WM_ORIGIN_BL,
                            'bottom'       => LANG_IMAGES_PRESET_WM_ORIGIN_B,
                            'bottom-right' => LANG_IMAGES_PRESET_WM_ORIGIN_BR
                        ],
                        'visible_depend' => ['is_watermark' => ['show' => ['1']]]
                    ]),
                    new fieldNumber('wm_margin', [
                        'title' => LANG_IMAGES_PRESET_WM_MARGIN,
                        'units' => 'px',
                        'rules' => [
                            ['digits']
                        ],
                        'visible_depend' => ['is_watermark' => ['show' => ['1']]]
                    ])
                ]
            ]
        ];

        if (!extension_loaded('imagick')) {
            unset($fields['size']['childs']['gif_to_gif']);
        }

        return $fields;
    }

}
