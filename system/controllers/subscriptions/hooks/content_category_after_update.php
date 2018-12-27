<?php

class onSubscriptionsContentCategoryAfterUpdate extends cmsAction {

    public function run($data){

        list($ctype, $old_category, $new_category) = $data;

        if($old_category['slug'] != $new_category['slug']){

            $old_url = href_to_rel($ctype['name'], $old_category['slug']);
            $new_url = href_to_rel($ctype['name'], $new_category['slug']);

            $this->model->replaceFieldString('subscriptions', $old_url, $new_url, 'subject_url');

        }

        return $data;

    }

}
