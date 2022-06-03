<?php

class onRatingContentBeforeItem extends cmsAction {

    public function run($data){

        list($ctype, $item, $fields) = $data;

        if ($ctype['is_rating']){

            if(array_key_exists('rating_is_in_item', $ctype['options']) && empty($ctype['options']['rating_is_in_item'])){
                return $data;
            }

            $this->setContext('content', $ctype['name']);

            $this->loadCurrentUserVoted([$item['id']]);

            // мы можем формировать рейтинг как-то иначе
            if(isset($item['rating_total_voted'])){
                $this->setTotalVoted($item['rating_total_voted']);
            } else {
                $this->loadCurrentTotalVoted($item['id']);
            }

            $is_rating_allowed = cmsUser::isAllowed($ctype['name'], 'rate', true, true) && ($item['user_id'] != $this->cms_user->id);

            if(!empty($ctype['options']['rating_template'])){
                $this->setOption('template', $ctype['options']['rating_template']);
            }

            if(!empty($ctype['options']['rating_item_label'])){
                $this->setLabel($ctype['options']['rating_item_label']);
            }

            // запоминаем в этой ячейке для совместимости
            $item['rating_widget'] = $this->getWidget($item['id'], $item['rating'], $is_rating_allowed);

            // Запишем кол-во голосов
            $item['rating_data'] = [
                'value' => $item['rating'],
                'count' => $this->getTotalVoted(),
            ];

            // с версий выше 2.11 инфобар в отдельном массиве
            if(!isset($item['info_bar'])){ $item['info_bar'] = []; }

            // добавляем блок рейтинга в самое начало
            $item['info_bar'] = ['rating' => [
                'css'   => 'bi_rating',
                'html'  => $item['rating_widget']
            ]] + $item['info_bar'];

        }

        return [$ctype, $item, $fields];
    }

}
