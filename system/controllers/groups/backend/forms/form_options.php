<?php

class formGroupsOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_GROUPS_LIST,
                'childs' => array(

                    new fieldCheckbox('is_ds_rating', array(
                        'title' => sprintf(LANG_GROUPS_OPT_DS_SHOW, LANG_GROUPS_DS_RATED),
                    )),
                    new fieldCheckbox('is_ds_popular', array(
                        'title' => sprintf(LANG_GROUPS_OPT_DS_SHOW, LANG_GROUPS_DS_POPULAR),
                    )),

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_GROUPS_VIEW,
                'childs' => array(

                    new fieldCheckbox('is_wall', array(
                        'title' => LANG_GROUPS_OPT_WALL_ENABLED,
                    )),

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_LIST_LIMIT,
                'childs' => array(

                    new fieldNumber('limit', array(
                        'default' => 15,
                        'rules' => array(
                            array('required')
                        )
                    ))

                )
            )

        );

    }

}
