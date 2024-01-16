<?php

class onCommentsContentItemForm extends cmsAction {

    public function run($data) {

        list($form, $item, $ctype) = $data;

        // если разрешено отключать комментарии к записи
        if (cmsUser::isAllowed($ctype['name'], 'disable_comments') && !empty($ctype['is_comments'])) {

            $labels = get_localized_value('comments_labels', $ctype['options']);

            if ($labels) {

                $this->setLabels($labels);
            }

            $fieldset_id = $form->addFieldset($this->labels->commenting, 'is_comment', [
                'is_collapsed' => !empty($ctype['options']['is_collapsed']) && in_array('is_comment', $ctype['options']['is_collapsed'])
            ]);

            $form->addField($fieldset_id, new fieldList('is_comments_on', [
                'default' => 1,
                'items'   => [
                    1 => LANG_YES,
                    0 => LANG_NO
                ]
            ]));
        }

        return [$form, $item, $ctype];
    }

}
