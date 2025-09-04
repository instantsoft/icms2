<?php
class formAdminContentCategory extends cmsForm {

    public function init($ctype) {

        return [
            'basic' => [
                'type' => 'fieldset',
                'childs' => [
                    new fieldList('parent_id', [
                        'title' => LANG_PARENT_CATEGORY,
                        'generator' => function($cat) use ($ctype) {

                            $content_model = cmsCore::getModel('content');
                            $tree = $content_model->limit(0)->getCategoriesTree($ctype['name']);

                            if ($tree){
                                foreach($tree as $item){
                                    $items[$item['id']] = str_repeat('- ', $item['ns_level']).' '.$item['title'];
                                }
                            }

                            return $items;
                        }
                    ]),
                    new fieldText('title', [
                        'title' => LANG_CP_CONTENT_CATS_TITLES,
                        'hint'  => LANG_CP_CONTENT_CATS_TITLES_HINT,
                        'is_strip_tags' => true,
                        'options' => [
                            'max_length' => 12288
                        ],
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldCheckbox('is_inherit_binds', [
                        'title' => LANG_CP_CONTENT_CATS_BIND
                    ])
                ]
            ]
        ];
    }

}
