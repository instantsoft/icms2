<?php
class actionCommentsvkCommentsList extends cmsAction {

    public function run(){

        return $this->cms_template->render('backend/comments_list', array(
            'api_id'    => $this->options['api_id'],
            'vk_params' => json_encode(array(
                'norealtime' => (int)$this->options['norealtime'],
                'limit'      => 100,
                'mini'       => (empty($this->options['mini']) ? 0 : $this->options['mini'])
            ))
        ));

    }

}
