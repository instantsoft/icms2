<?php

class onCommentsContentBeforeList extends cmsAction {

    public function run($data) {

        list($ctype, $items) = $data;

        if ($ctype['is_comments'] && $items) {

            if (!empty($ctype['options']['comments_labels'])) {

                $this->setLabels($ctype['options']['comments_labels']);
            }

            foreach ($items as &$item) {

                if (!$item['is_comments_on']) {
                    continue;
                }

                // с версий выше 2.12 инфобар в отдельном массиве
                if (!isset($item['info_bar'])) {
                    $item['info_bar'] = [];
                }

                $info_bar = [
                    'css'   => 'bi_comments',
                    'icon'  => 'comments',
                    'title' => $this->labels->comments,
                    'html'  => intval($item['comments'])
                ];

                if (empty($item['is_private_item']) && $ctype['options']['item_on']) {
                    $info_bar['href'] = href_to($ctype['name'], $item['slug'] . '.html') . '#comments';
                }

                $item['info_bar']['comments'] = $info_bar;
            }
        }

        return [$ctype, $items];
    }

}
