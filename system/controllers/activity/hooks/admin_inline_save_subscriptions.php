<?php

class onActivityAdminInlineSaveSubscriptions extends cmsAction {

    public function run($input_data){

        list($data, $_data, $item) = $input_data;

        if(empty($data['title'])){
            return $input_data;
        }

        $this->updateEntry('subscriptions', 'subscribe', $item['id'], array(
            'subject_title' => $data['title']
        ));

        return $input_data;

    }

}
