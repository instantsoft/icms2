<?php

class backendTypograph extends cmsBackend {

    private $html_tags = [
        'p', 'br', 'span', 'div',
        'a', 'img', 'input', 'label',
        'b', 'i', 'u', 's', 'del', 'em', 'strong', 'sup', 'sub', 'hr', 'font', 'abbr', 'strike',
        'ul', 'ol', 'li', 'dl', 'dt', 'dd',
        'table', 'tbody', 'thead', 'tfoot', 'tr', 'td', 'th', 'caption',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'pre', 'code', 'blockquote', 'picture', 'button',
        'video', 'source', 'audio', 'youtube', 'facebook', 'figure', 'figcaption',
        'iframe', 'spoiler', 'cite', 'footer', 'address'
    ];

    public function getHtmlTags() {
        return cmsEventsManager::hook('typograph_html_tags_list', $this->html_tags);
    }

    public function getTagsForm($tags) {

        $form = new cmsForm();

        foreach ($tags as $tag) {

            $fieldset_id = $form->addFieldset(sprintf(LANG_TYP_ATTR_TAG, $tag), 'tag-attr-'.$tag, ['is_collapsed' => true]);

            $form->addField($fieldset_id, new fieldString('options:callback:'.$tag, [
                'title' => LANG_TYP_TAG_CALLBACK,
                'hint'  => LANG_TYP_TAG_CALLBACK_HINT,
                'rules' => [
                    ['regexp', '#^([a-z]+[a-z0-9_\|]+)$#ui'],
                ]
            ]));

            $form->addField($fieldset_id, new fieldFieldsgroup('options:tags:'.$tag, [
                'add_title' => LANG_TYP_ADD_ATTR,
                'childs' => [
                    new fieldList('type', [
                        'title' => LANG_TYP_ATTR_TYPE,
                        'native_tag' => true,
                        'items' => [
                            '#text'   => LANG_PARSER_TEXT,
                            '#int'    => LANG_PARSER_NUMBER,
                            '#link'   => LANG_PARSER_URL,
                            '#domain' => LANG_TYP_DOMAINS,
                            '#array'  => LANG_TYP_ARRAY,
                            '#image'  => LANG_TYP_PATH
                        ],
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('name', [
                        'title' => LANG_TYP_ATTR_NAME,
                        'rules' => [
                            ['required'],
                            ['regexp', '#^(?!on)([a-z0-9\-]*[a-z]+[a-z0-9\-]*)$#ui']
                        ]
                    ]),
                    new fieldText('params', [
                        'attributes' => ['placeholder' => LANG_TYP_ATTR_PARAMS],
                        'css_class' => 'col-sm-12 mt-2'
                    ])
                ]
            ]));

        }

        return $form;
    }

}
