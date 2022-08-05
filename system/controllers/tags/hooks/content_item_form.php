<?php

class onTagsContentItemForm extends cmsAction {

    public function run($data) {

        list($form, $item, $ctype) = $data;

        if ($ctype['is_tags']) {

            $fieldset_id = $form->addFieldset(LANG_TAGS, 'tags_wrap', [
                'is_collapsed' => !empty($ctype['options']['is_collapsed']) && in_array('tags_wrap', $ctype['options']['is_collapsed'])
            ]);

            $form->addField($fieldset_id, new fieldString('tags', [
                'hint' => LANG_TAGS_HINT,
                'options'      => [
                    'max_length'        => 1000,
                    'show_symbol_count' => true
                ],
                'autocomplete' => [
                    'multiple' => true,
                    'url'      => href_to('tags', 'autocomplete')
                ],
                'rules' => [
                    [function ($controller, $data, $value) {

                        if (!$value) {
                            return true;
                        }

                        if (strpos($value, '?') !== false) {
                            return ERR_VALIDATE_INVALID;
                        }

                        $tags = explode(',', $value);

                        foreach ($tags as $tag) {

                            $tag = trim($tag);

                            if (mb_strlen($tag) > modelTags::TAG_LEN) {
                                return sprintf(LANG_TAGS_LEN_ERROR, $tag, modelTags::TAG_LEN);
                            }
                        }

                        return true;
                    }]
                ]
            ]));
        }

        return [$form, $item, $ctype];
    }

}
