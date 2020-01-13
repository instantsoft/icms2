<?php
class formAdminFtp extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_CP_FTP_ACCOUNT,
                'childs' => array(

                    new fieldString('host', array(
                        'title' => LANG_CP_FTP_HOST,
                        'rules' => array(
                            array('required'),
                        )
                    )),

                    new fieldString('port', array(
                        'title'   => LANG_CP_FTP_PORT,
                        'default' => 21,
                        'rules' => array(
                            array('required'),
                        )
                    )),

                    new fieldString('user', array(
                        'title' => LANG_CP_FTP_USER,
                        'rules' => array(
                            array('required'),
                        )
                    )),

                    new fieldString('pass', array(
                        'title' => LANG_CP_FTP_PASS,
                        'is_password' => true,
                        'is_clean_disable' => true
                    )),

                    new fieldString('path', array(
                        'title' => LANG_CP_FTP_PATH,
						'hint' => LANG_CP_FTP_PATH_HINT,
                        'default' => '/',
                        'suffix' => '<a id="check_ftp" href="'.href_to('admin', 'check_ftp').'" class="ajaxlink">'.LANG_CP_CHECK.'</a>',
                        'rules' => array(
                            array('required'),
                        )
                    )),

					new fieldCheckbox('is_pasv', array(
                        'title' => LANG_CP_FTP_IS_PASV,
                        'default' => true,
                    ))

                )
            ),

            array(
                'type' => 'fieldset',
                'childs' => array(
					new fieldCheckbox('save_to_session', array(
                        'title'   => LANG_CP_FTP_SAVE_TO_SESSION,
                        'hint'    => LANG_CP_FTP_SAVE_TO_SESSION_HINT,
                        'default' => false
                    ))
				)
			),

            array(
                'type' => 'fieldset',
                'childs' => array(
					new fieldCheckbox('is_skip', array(
                        'title' => LANG_CP_FTP_SKIP,
                        'hint' => LANG_CP_FTP_SKIP_HINT,
                        'default' => false
                    ))
				)
			)

        );

    }

}
