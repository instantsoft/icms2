<?php

class formWidgetContentFilterOptions extends cmsForm {

    public function init() {

        return [
            [
                'type'   => 'fieldset',
                'title'  => LANG_CONTENT_TYPE,
                'childs' => [
                    new fieldList('options:ctype_name', [
                        'generator' => function ($ctype) {

                            $model = cmsCore::getModel('content');
                            $tree  = $model->getContentTypes();

                            $items = [0 => LANG_WD_CONTENT_FILTER_DETECT];

                            if ($tree) {
                                foreach ($tree as $item) {
                                    $items[$item['name']] = $item['title'];
                                }
                            }

                            return $items;
                        }
                    ])
                ]
            ]
        ];
    }

}
