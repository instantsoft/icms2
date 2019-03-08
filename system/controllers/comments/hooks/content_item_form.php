<?php

class onCommentsContentItemForm extends cmsAction {

    public function run($data) {

        list($form, $item, $ctype) = $data;

        // если разрешено отключать комментарии к записи
        if(cmsUser::isAllowed($ctype['name'], 'disable_comments') && !empty($ctype['is_comments'])){

            $fieldset_id = $form->addFieldset(LANG_RULE_CONTENT_COMMENT, 'is_comment', array(
                'is_collapsed' => !empty($ctype['options']['is_collapsed']) && in_array('is_comment', $ctype['options']['is_collapsed'])
            ));

            $form->addField($fieldset_id, new fieldList('is_comments_on', array(
				'default' => 1,
				'items' => array(
					1 => LANG_YES,
					0 => LANG_NO
				)
			)));

        }

        return array($form, $item, $ctype);

    }

}
