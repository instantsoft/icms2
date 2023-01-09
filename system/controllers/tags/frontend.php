<?php
/**
 * @property \modelTags $model
 */
class tags extends cmsFrontend {

    protected $useOptions = true;
    public $useSeoOptions = true;
    protected $unknown_action_as_index_param = true;

    public function getTagsWidgetParams($options) {

        if (!empty($options['subjects'])) {
            $options['subjects'] = array_filter($options['subjects']);
        }

        if (!empty($options['min_len'])) {
            $this->model->filter("CHAR_LENGTH(tag) >= {$options['min_len']}")->forceIndex('tag');
        }

        if (!empty($options['limit'])) {
            $this->model->limit($options['limit']);
        }

        $this->model->orderByList([[
            'by' => 'frequency',
            'to' => 'desc',
            'strict' => true
        ]]);

        if (!empty($options['subjects'])) {

            // Если заданы типы контента

            $this->model->filterEqual('target_controller', 'content')->
                    filterIn('target_subject', $options['subjects']);

            $this->model->groupBy('i.tag_id' . (!empty($options['min_freq']) ? ' HAVING frequency >= ' . $options['min_freq'] : ''));

            $this->model->useCache('tags.tags');

            $tags = $this->model->
                    selectOnly('i.tag_id', 'id')->
                    select('t.tag')->
                    select('IFNULL(COUNT(i.id), 0)', 'frequency')->
                    joinInner('tags', 't', 't.id = i.tag_id')->
                    get('tags_bind');

        } else {

            // Если типы контента не заданы

            if (!empty($options['min_freq'])) {
                $this->model->filterGtEqual('frequency', $options['min_freq']);
            }

            $tags = $this->model->
                    selectOnly('id')->
                    select('tag')->
                    select('frequency')->
                    getTags();
        }

        if (!$tags) {
            return false;
        }

        // Для стиля "Облако" выбираем мин. и макс. частоты отобранных тегов
        if ($options['style'] == 'cloud') {
            $min_frequency = end($tags)['frequency'];
            $max_frequency = reset($tags)['frequency'];
        }

        if (empty($options['shuffle']) && $options['ordering'] == 'tag') {
            // Сортировка тегов по алфавиту.
            array_order_by($tags, 'tag');
        }

        if (!empty($options['shuffle'])) {
            shuffle($tags);
        }

        return [
            'subjects' => ((!empty($options['subjects']) && $options['subjects'] !== ['0']) ? $options['subjects'] : []),
            'style'    => $options['style'],
            'max_fs'   => $options['max_fs'],
            'min_fs'   => $options['min_fs'],
            'colors'   => (!empty($options['colors']) ? explode(',', $options['colors']) : []),
            'tags'     => $tags,
            'min_freq' => (isset($min_frequency) ? $min_frequency : 0),
            'max_freq' => (isset($max_frequency) ? $max_frequency : 0)
        ];
    }

}
