<?php
class widgetTagsCloud extends cmsWidget {

    public function run(){

        $ordering = $this->getOption('ordering', 'tag');
        $style    = $this->getOption('style', 'cloud');
        $max_fs   = $this->getOption('max_fs');
        $min_fs   = $this->getOption('min_fs');
        $limit    = $this->getOption('limit');
        $shuffle  = $this->getOption('shuffle');
        $subjects = $this->getOption('subjects');
        $min_len  = $this->getOption('min_len');
        $min_freq = $this->getOption('min_freq');
        $colors   = $this->getOption('colors');

        $model = cmsCore::getModel('tags');

        if(!empty($subjects) && $subjects !== array(0)){

            $tag_ids = $model->selectOnly('tag_id')->filterEqual('target_controller', 'content')->
                    filterIn('target_subject', $subjects)->groupBy('tag_id')->
                    get('tags_bind', function($item, $model){
                        return $item['tag_id'];
                    }, false);

            if($tag_ids){
                $model->filterIn('id', $tag_ids);
            }

        }

        if($min_freq){
            $model->filterGtEqual('frequency', $min_freq);
        }

        if($min_len){
            $model->filter("CHAR_LENGTH(i.tag) >= {$min_len}")->forceIndex('tag');
        }

        switch($ordering){
            case 'tag': $model->orderBy('tag', 'asc'); break;
            case 'frequency': $model->orderBy('frequency', 'desc'); break;
        }

        if($limit){
            $model->limit($limit);
        }

        $tags = $model->getTags();

        if ($style=='cloud'){
            $max_frequency = $model->getMaxTagFrequency();
        }

        if (!$tags) { return false; }

        if($shuffle){
            shuffle($tags);
        }

        return array(
            'style'    => $style,
            'max_fs'   => $max_fs,
            'min_fs'   => $min_fs,
            'colors'   => ($colors ? explode(',', $colors) : array()),
            'tags'     => $tags,
            'max_freq' => (isset($max_frequency) ? $max_frequency : 0)
        );

    }

}