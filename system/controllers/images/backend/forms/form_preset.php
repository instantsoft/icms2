<?php

class formImagesPreset extends cmsForm {

    public function init($do) {

        return array(

            'basic' => array(
                'type' => 'fieldset',
                'childs' => array(
                    new fieldString('name', array(
                        'title' => LANG_SYSTEM_NAME,
                        'rules' => array(
                            array('required'),
                            array('sysname'),
                            array('max_length', 32),
                            $do == 'add' ? array('unique', 'images_presets', 'name') : false
                        )
                    )),
                    new fieldString('title', array(
                        'title' => LANG_IMAGES_PRESET,
                        'rules' => array(
                            array('required'),
                            array('max_length', 128)
                        )
                    )),
                )
            ),

            'size' => array(
                'type' => 'fieldset',
                'title' => LANG_IMAGES_PRESET_SIZE,
                'childs' => array(
					new fieldNumber('width', array(
						'title' => LANG_IMAGES_PRESET_SIZE_W,
						'hint'  => LANG_IMAGES_PRESET_SIZE_W_HINT,
						'units' => 'px'
					)),
					new fieldNumber('height', array(
						'title' => LANG_IMAGES_PRESET_SIZE_H,
						'hint'  => LANG_IMAGES_PRESET_SIZE_H_HINT,
                        'units' => 'px'
					)),
					new fieldCheckbox('allow_enlarge', array(
						'title' => LANG_IMAGES_PRESET_ALLOW_ENLARGE
					)),
					new fieldCheckbox('gamma_correct', array(
						'title' => LANG_IMAGES_PRESET_GAMMA_CORRECT,
					)),
					new fieldCheckbox('is_square', array(
						'title' => LANG_IMAGES_PRESET_SQUARE,
					)),
					new fieldList('crop_position', array(
						'title' => LANG_IMAGES_PRESET_CROP_POSITION,
                        'default' => cmsImages::CROPCENTER,
						'items' => array(
                            cmsImages::CROPTOP       => LANG_CP_FIELD_LABEL_TOP,
                            cmsImages::CROPCENTER    => LANG_IMAGES_PRESET_CCENTER,
                            cmsImages::CROPTOPCENTER => LANG_IMAGES_PRESET_TOP_CENTER,
                            cmsImages::CROPBOTTOM    => LANG_IMAGES_PRESET_CBOTTOM,
                            cmsImages::CROPLEFT      => LANG_CP_FIELD_LABEL_LEFT,
                            cmsImages::CROPRIGHT     => LANG_IMAGES_PRESET_CRIGHT
                        ),
                        'visible_depend' => array('is_square' => array('show' => array('1')))
					)),
					new fieldNumber('quality', array(
						'title' => LANG_IMAGES_PRESET_QUALITY,
                        'units' => '%',
                        'default' => '90',
						'rules' => array(
							array('digits'),
							array('min', 1),
							array('max', 100)
						)
					))
                )
            ),

            'watermark' => array(
                'type' => 'fieldset',
                'title' => LANG_IMAGES_PRESET_WM,
                'childs' => array(
					new fieldCheckbox('is_watermark', array(
						'title' => LANG_IMAGES_PRESET_WM_ON,
					)),
					new fieldImage('wm_image', array(
						'title' => LANG_IMAGES_PRESET_WM_IMG,
						'options' => array(
							'sizes' => array('small', 'original')
						),
                        'visible_depend' => array('is_watermark' => array('show' => array('1')))
					)),
					new fieldList('wm_origin', array(
						'title' => LANG_IMAGES_PRESET_WM_ORIGIN,
						'items' => array(
							'top-left'     => LANG_IMAGES_PRESET_WM_ORIGIN_TL,
                            'top-center'   => LANG_IMAGES_PRESET_WM_ORIGIN_T,
                            'top-right'    => LANG_IMAGES_PRESET_WM_ORIGIN_TR,
                            'left'         => LANG_IMAGES_PRESET_WM_ORIGIN_L,
                            'center'       => LANG_IMAGES_PRESET_WM_ORIGIN_C,
                            'right'        => LANG_IMAGES_PRESET_WM_ORIGIN_R,
                            'bottom-left'  => LANG_IMAGES_PRESET_WM_ORIGIN_BL,
                            'bottom'       => LANG_IMAGES_PRESET_WM_ORIGIN_B,
                            'bottom-right' => LANG_IMAGES_PRESET_WM_ORIGIN_BR
                        ),
                        'visible_depend' => array('is_watermark' => array('show' => array('1')))
					)),
					new fieldNumber('wm_margin', array(
						'title' => LANG_IMAGES_PRESET_WM_MARGIN,
						'units' => 'px',
						'rules' => array(
							array('digits')
						),
                        'visible_depend' => array('is_watermark' => array('show' => array('1')))
					))
                )
            )

        );

    }

}