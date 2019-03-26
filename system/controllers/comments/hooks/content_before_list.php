<?php

class onCommentsContentBeforeList extends cmsAction {

    public function run($data){

        list($ctype, $items) = $data;

        if ($ctype['is_comments'] && $items){

            foreach($items as $id => $item){

                if (!$item['is_comments_on']){
                    continue;
                }

                // с версий выше 2.12 инфобар в отдельном массиве
                if(!isset($item['info_bar'])){ $items[$id]['info_bar'] = []; }

                $info_bar = [
                    'css'   => 'bi_comments',
                    'title' => LANG_COMMENTS,
                    'html'  => intval($item['comments'])
                ];

                if (empty($item['is_private_item'])) {
                    $info_bar['href'] = href_to($ctype['name'], $item['slug'].'.html').'#comments';
                }

                $items[$id]['info_bar'][] = $info_bar;

            }

        }

        return array($ctype, $items);

    }

}
