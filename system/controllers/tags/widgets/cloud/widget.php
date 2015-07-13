<?php
class widgetTagsCloud extends cmsWidget {

    public function run(){

        $ordering = $this->getOption('ordering', 'tag');
        $style = $this->getOption('style', 'cloud');
        $max_fs = $this->getOption('max_fs');
        $min_fs = $this->getOption('min_fs');
        $limit = $this->getOption('limit');

        $model = cmsCore::getModel('tags');

        switch($ordering){
            case 'tag': $model->orderBy('tag', 'asc'); break;
            case 'frequency': $model->orderBy('frequency', 'desc'); break;
        }

        $model->limit($limit);

        $tags = $model->getTags();

        if ($style=='cloud'){
            $max_frequency = $model->getMaxTagFrequency();
        }

        if (!$tags) { return false; }

        return array(
            'style' => $style,
            'max_fs' => $max_fs,
            'min_fs' => $min_fs,
            'tags' => $tags,
            'max_freq' => isset($max_frequency) ? $max_frequency : 0,
        );

    }

}
