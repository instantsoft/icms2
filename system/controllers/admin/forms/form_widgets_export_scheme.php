<?php

class formAdminWidgetsExportScheme extends cmsForm {

    public function init($widgets) {

        return [
            'basic' => [
                'type'   => 'fieldset',
                'childs' => [
                    new fieldHidden('submit'),
                    new fieldCheckbox('save_widgets', array(
                        'title' => LANG_CP_WIDGETS_SAVE_WIDGETS
                    )),
                    new fieldList('save_widgets_list', array(
                        'is_multiple' => true,
                        'multiple_select_deselect' => true,
                        'items' => $widgets,
                        'default' => array_keys($widgets),
                        'visible_depend' => array('save_widgets' => array('hide' => array('0')))
                    ))
                ]
            ]
        ];
    }

}
