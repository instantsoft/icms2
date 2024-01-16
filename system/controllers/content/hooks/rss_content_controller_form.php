<?php

class onContentRssContentControllerForm extends cmsAction {

    public function run($data) {

        list($form, $feed) = $data;

        $fields = $this->model->getContentFields($feed['ctype_name']);

        $fields = cmsEventsManager::hook('ctype_content_fields', $fields);

        $fields = ['' => ''] + array_collection_to_list($fields, 'name', 'title');

        $form->addFieldset(LANG_RSS_FEED_MAPPING, 'mapping', [
            'childs' => [
                new fieldList('mapping:title', [
                    'title' => LANG_RSS_FEED_MAP_TITLE,
                    'items' => $fields
                ]),
                new fieldList('mapping:description', [
                    'title' => LANG_RSS_FEED_MAP_DESC,
                    'items' => $fields
                ]),
                new fieldList('mapping:pubDate', [
                    'title' => LANG_RSS_FEED_MAP_DATE,
                    'items' => $fields
                ]),
                new fieldList('mapping:image', [
                    'title' => LANG_RSS_FEED_MAP_IMAGE,
                    'items' => $fields
                ]),
                new fieldList('mapping:image_size', [
                    'title'     => LANG_RSS_FEED_MAP_IMAGE_SIZE,
                    'generator' => function ($item) {
                        return ['original' => LANG_PARSER_IMAGE_SIZE_ORIGINAL] + cmsCore::getModel('images')->getPresetsList(true);
                    }
                ])
            ]
        ]);

        return [$form, $feed];
    }

}
