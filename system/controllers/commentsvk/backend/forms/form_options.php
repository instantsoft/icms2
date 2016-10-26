<?php

class formCommentsvkOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'childs' => array(

                    new fieldString('api_id', array(
                        'title' => LANG_COM_VK_API_ID,
                        'hint'  => LANG_COM_VK_API_ID_HINT
                    )),

                    new fieldCheckbox('autoPublish', array(
                       'title'   => LANG_COM_VK_AUTOPUBLISH,
                       'default' => 1
                    )),

                    new fieldCheckbox('norealtime', array(
                       'title'   => LANG_COM_VK_NOREALTIME,
                       'default' => 0
                    )),

                    new fieldList('mini', array(
                        'title'   => LANG_COM_VK_MINI,
                        'default' => 0,
                        'items'   => array(
                            0      => LANG_NO,
                            1      => LANG_YES,
                            'auto' => LANG_AUTO
                        )
                    )),

                    new fieldListMultiple('attach', array(
                        'title'   => LANG_COM_VK_ATTACH,
                        'default' => 0,
                        'items'   => array(
                            'graffiti' => LANG_COM_VK_GRAFFITI,
                            'photo'    => LANG_PHOTOS,
                            'video'    => LANG_COM_VK_VIDEO,
                            'link'     => LANG_COM_VK_LINK,
                            'audio'    => LANG_COM_VK_AUDIO
                        )
                    )),

                    new fieldNumber('limit', array(
                        'title'   => LANG_COM_VK_LIMIT,
                        'default' => 50,
                        'rules' => array(
                            array('required'),
                            array('min', 5),
                            array('max', 100)
                        )
                    ))

                )
            )

        );

    }

}
