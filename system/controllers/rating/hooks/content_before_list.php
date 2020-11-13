<?php

class onRatingContentBeforeList extends cmsAction {

    public function run($data){

        list($ctype, $items) = $data;

        if ($ctype['is_rating'] && $items){

            if(array_key_exists('rating_is_in_list', $ctype['options']) && empty($ctype['options']['rating_is_in_list'])){
                return $data;
            }

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
                $items[$id]['info_bar'] = ['rating' => [
                    'css'   => 'bi_rating',
                    'html'  => $items[$id]['rating_widget']
                ]] + $items[$id]['info_bar'];

            }

        }

        return array($ctype, $items);

    }

}
