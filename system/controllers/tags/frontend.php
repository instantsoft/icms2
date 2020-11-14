<?php

class tags extends cmsFrontend {

    protected $useOptions = true;
    public $useSeoOptions = true;

    protected $unknown_action_as_index_param = true;

    public function getTagsWidgetParams($options) {

        if(!empty($options['subjects'])){
            $options['subjects'] = array_filter($options['subjects']);
        }

        // WebMan: переделана логика работы.
        // Были проблемы при отборе по типам контента:
        // 1. Отбор производился не по частоте внутри этих типов контента, а по суммарной частоте для всего сайта.
        // 2. Сортировка отобранных тегов также производилась по частоте для всего сайта независимо от выбранных типов контента.
        // 3. В случае сортировки по алфавиту отбирались первые теги независимо от их частоты (и для всего сайта, и для отдельных ТК) - попадалось много непопулярных тегов.
        // 4. Максимальная частота для расчёта размеров шрифта всегда бралась как максимальная частота самого популярного тега по всему сайту без учёта ТК.
        // 5. Минимальный размер шрифта часто вообще не использовался для отображения из-за отсчёта минимальной частоты тегов от 0, а не от реального минимума.
        // Теперь:
        // 1. Сначала производится отбор по частоте тегов внутри выбранных типов контента или всего сайта.
        // 2. Потом отобранные теги сортируются по алфавиту или частоте.
        // 3. Размеры шрифта расчитываются между минимальной и максимальной частотой отобранных тегов с учётом ТК.

        /* Итоговый запрос при выборе ТК в опциях (пример):
            SELECT i.tag_id as id, t.tag, IFNULL(COUNT(i.id), 0) as frequency
            FROM cms_tags_bind i
            INNER JOIN cms_tags as t ON t.id = i.tag_id
            WHERE (i.target_controller = 'content') AND (i.target_subject IN ('pages','articles','news')) AND CHAR_LENGTH(t.tag) >= 3
            GROUP BY i.tag_id
            HAVING (frequency >= 2)
            ORDER BY frequency desc
            LIMIT 20
        */

        /* Итоговый запрос для всего сайта без учёта ТК (пример):
            SELECT id, tag, frequency
            FROM cms_tags
            WHERE (CHAR_LENGTH(tag) >= 3) AND (frequency >= 2)
            ORDER BY frequency desc
            LIMIT 20
        */

        if(!empty($options['min_len'])){
            $this->model->filter("CHAR_LENGTH(tag) >= {$options['min_len']}")->forceIndex('tag');
        }

        if(!empty($options['limit'])){
            $this->model->limit($options['limit']);
        }

        $this->model->order_by = 'frequency desc';

        if(!empty($options['subjects'])){

            // Если заданы типы контента

            $this->model->filterEqual('target_controller', 'content')->
                    filterIn('target_subject', $options['subjects']);

            $this->model->groupBy('i.tag_id' . (!empty($options['min_freq']) ? ' HAVING frequency >= '.$options['min_freq'] : ''));

            $tags = $this->model->
                selectOnly('i.tag_id', 'id')->
                select('t.tag')->
                select('IFNULL(COUNT(i.id), 0)', 'frequency')->
                joinInner('tags', 't', 't.id = i.tag_id')->
                get('tags_bind');

        } else {

            // Если типы контента не заданы

            if(!empty($options['min_freq'])){
                $this->model->filterGtEqual('frequency', $options['min_freq']);
            }

            $tags = $this->model->
                selectOnly('id')->
                select('tag')->
                select('frequency')->
                get('tags');
        }

        if (!$tags) { return false; }

        // Для стиля "Облако" выбираем мин. и макс. частоты отобранных тегов
        // В первом элементе массива - максимальная, в последнем - минимальная
        // array_slice() - самый быстрый способ получения последнего элемента массива
        if ($options['style'] == 'cloud'){
            $min_frequency = array_slice($tags, -1)[0]['frequency'];
            $max_frequency = reset($tags)['frequency'];
        }

        if (empty($options['shuffle']) && $options['ordering'] == 'tag'){
            // Сортировка тегов по алфавиту.
            // Колбэк используется так как нужно сортировать по вложенным элементам массива, а не по его ключам/значениям.
            uasort($tags, function ($a, $b) {
                return strcoll($a['tag'], $b['tag']);
            });
        }

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
            'min_freq' => (isset($min_frequency) ? $min_frequency : 0),
            'max_freq' => (isset($max_frequency) ? $max_frequency : 0)
        );

    }

}
