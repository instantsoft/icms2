<?php

class onRatingContentBeforeList extends cmsAction {

    public function run($data){

        list($ctype, $items) = $data;

        if ($ctype['is_rating'] && $items){

            if(!empty($ctype['options']['rating_template'])){
                $this->setOption('template', $ctype['options']['rating_template']);
            }

            $this->setContext('content', $ctype['name']);

            // вызывать после установки контекста
            $this->loadCurrentUserVoted(array_keys($items));

            $is_rating_allowed = cmsUser::isAllowed($ctype['name'], 'rate', true, true);

            if(!empty($ctype['options']['rating_list_label'])){
                $this->setLabel($ctype['options']['rating_list_label']);
            }

            foreach($items as $id => $item){

                $is_rating_enabled = $is_rating_allowed && ($item['user_id'] != $this->cms_user->id);

                // запоминаем в этой ячейке для совместимости
                $items[$id]['rating_widget'] = $this->getWidget($item['id'], $item['rating'], $is_rating_enabled);

                // с версий выше 2.12 инфобар в отдельном массиве
                if(!isset($item['info_bar'])){ $items[$id]['info_bar'] = []; }

                // добавляем блок рейтинга в самое начало
                array_unshift($items[$id]['info_bar'], [
                    'css'   => 'bi_rating',
                    'html'  => $items[$id]['rating_widget']
                ]);

            }

        }

        return array($ctype, $items);

    }

}
