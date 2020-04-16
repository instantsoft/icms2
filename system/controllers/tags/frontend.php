<?php

class tags extends cmsFrontend {

    protected $useOptions = true;
    public $useSeoOptions = true;

    protected $unknown_action_as_index_param = true;

    public function getTagsWidgetParams($options) {

        if(!empty($options['subjects'])){
            $options['subjects'] = array_filter($options['subjects']);
        }

        if(!empty($options['subjects'])){

            $tag_ids = $this->model->selectOnly('tag_id')->
                    filterEqual('target_controller', 'content')->
                    filterIn('target_subject', $options['subjects'])->groupBy('tag_id')->
                    get('tags_bind', function($item, $model){
                        return $item['tag_id'];
                    }, false);

            if($tag_ids){
                $this->model->filterIn('id', $tag_ids);
            }

        }

        if(!empty($options['min_freq'])){
            $this->model->filterGtEqual('frequency', $options['min_freq']);
        }

        if(!empty($options['min_len'])){
            $this->model->filter("CHAR_LENGTH(i.tag) >= {$options['min_len']}")->forceIndex('tag');
        }

        if(empty($options['ordering'])){
            $options['ordering'] = 'tag';
        }

        switch($options['ordering']){
            case 'tag': $this->model->orderBy('tag', 'asc'); break;
            case 'frequency': $this->model->orderBy('frequency', 'desc'); break;
        }

        if(!empty($options['limit'])){
            $this->model->limit($options['limit']);
        }

        $tags = $this->model->getTags();

        if ($options['style'] == 'cloud'){
            $max_frequency = $this->model->getMaxTagFrequency();
        }

        if (!$tags) { return false; }

        if(!empty($options['shuffle'])){
            shuffle($tags);
        }

        return array(
            'subjects' => ((!empty($options['subjects']) && $options['subjects'] !== array('0')) ? $options['subjects'] : array()),
            'style'    => $options['style'],
            'max_fs'   => $options['max_fs'],
            'min_fs'   => $options['min_fs'],
            'colors'   => (!empty($options['colors']) ? explode(',', $options['colors']) : array()),
            'tags'     => $tags,
            'max_freq' => (isset($max_frequency) ? $max_frequency : 0)
        );

    }

}
