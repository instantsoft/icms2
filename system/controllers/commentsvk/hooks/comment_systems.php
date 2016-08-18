<?php

class onCommentsvkCommentSystems extends cmsAction {

	public function run($target_controller=null){

        if(empty($this->options['api_id'])){
            return false;
        }

        return array(
            'name'  => 'vk',
            'title' => LANG_COMMENTSVK_CONTROLLER,
            'html'  => $this->cms_template->renderInternal($this, 'list', array(
                'api_id'    => $this->options['api_id'],
                'page_id'   => $target_controller->target_controller.'_'.$target_controller->target_subject.$target_controller->target_id,
                'vk_params' => json_encode(array(
                    'autoPublish' => (int) $this->options['autoPublish'],
                    'norealtime'  => (int) $this->options['norealtime'],
                    'limit'       => (int) $this->options['limit'],
                    'mini'        => (!$this->options['mini'] ? 0 : $this->options['mini']),
                    'attach'      => (!$this->options['attach'] ? false : implode(',', $this->options['attach'])),
                ))
            ))
        );

    }

}
