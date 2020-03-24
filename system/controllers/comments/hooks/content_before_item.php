<?php

class onCommentsContentBeforeItem extends cmsAction {

    public function run($data){

        list($ctype, $item, $fields) = $data;

        if ($ctype['is_comments'] && $item['is_approved'] && !empty($item['is_comments_on'])){

            $this->target_controller = 'content';
            $this->target_subject    = $ctype['name'];
            $this->target_id         = $item['id'];
            $this->target_user_id    = $item['user_id'];

            if(!empty($ctype['options']['comments_labels'])){

                $this->setLabels($ctype['options']['comments_labels']);

            }

            if (!empty($ctype['options']['comments_title_pattern'])){
                $this->comments_title = string_replace_keys_values_extended($ctype['options']['comments_title_pattern'], $this->getItemSeo($item, $fields));
            }

            if (!empty($ctype['options']['comments_template'])){
                $this->comment_template = $ctype['options']['comments_template'];
            }

            $item['comments_widget'] = $this->getWidget();

        }

        return array($ctype, $item, $fields);

    }

    private function getItemSeo($item, $fields) {

        $_item = $item;

        foreach ($fields as $field) {

            if (!isset($item[$field['name']])) { $_item[$field['name']] = '';  continue; }

            if (empty($item[$field['name']]) && $item[$field['name']] !== '0') {
                $_item[$field['name']] = null; continue;
            }

            $_item[$field['name']] = $field['string_value'];

        }

        if(!empty($item['category']['title'])){
            $_item['category'] = $item['category']['title'];
        } else {
            $_item['category'] = null;
        }

        return $_item;

    }

}
