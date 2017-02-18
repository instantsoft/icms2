<?php

class formModerationOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'childs' => array(

					new fieldCheckbox('moderation_log_delete', array(
						'title' => LANG_MODERATION_LOG_DELETE
					)),

					new fieldCheckbox('moderation_log_restore', array(
						'title' => LANG_MODERATION_LOG_RESTORE
					)),

					new fieldCheckbox('clear_log_after_delete', array(
						'title' => LANG_MODERATION_CLEAR_LOG_AFTER_DELETE
					))

                )
            )

        );

    }

}
