<?php

class onRatingContentBeforeList extends cmsAction {

    public function run($data){

        list($ctype, $items) = $data;

        if ($ctype['is_rating'] && $items){

            $this->setContext('content', $ctype['name']);

            $is_rating_allowed = cmsUser::isAllowed($ctype['name'], 'rate');

            foreach($items as $id => $item){
                $is_rating_enabled = $is_rating_allowed && ($item['user_id'] != $this->cms_user->id);
                $items[$id]['rating_widget'] = $this->getWidget($item['id'], $item['rating'], $is_rating_enabled);
            }

        }

        return array($ctype, $items);

    }

}
