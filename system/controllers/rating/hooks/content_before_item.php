<?php

class onRatingContentBeforeItem extends cmsAction {

    public function run($data){

        list($ctype, $item, $fields) = $data;

        if ($ctype['is_rating']){

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

            // с версий выше 2.11 инфобар в отдельном массиве
            if(!isset($item['info_bar'])){ $item['info_bar'] = []; }

            // добавляем блок рейтинга в самое начало
            array_unshift($item['info_bar'], [
                'css'   => 'bi_rating',
                'html'  => $item['rating_widget']
            ]);

        }

        return array($ctype, $item, $fields);

    }

}
