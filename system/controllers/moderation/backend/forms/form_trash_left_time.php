<?php

class formModerationTrashLeftTime extends cmsForm {

    public function init() {

        return array(

            'basic' => array(
                'type' => 'fieldset',
                'childs' => array(

                    new fieldNumber('trash_left_time', array(
                        'hint' => LANG_MODERATION_TRASH_LEFT_TIME_HINT,
                        'units' => LANG_HOURS
                    ))

                )
            )

        );

    }

}
