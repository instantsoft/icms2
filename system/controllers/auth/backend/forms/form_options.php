<?php

class formAuthOptions extends cmsForm {

    public $is_tabbed = true;

    public function init() {

        $auth_redirect_items = array(
            'none'        => LANG_REG_CFG_AUTH_REDIRECT_NONE,
            'index'       => LANG_REG_CFG_AUTH_REDIRECT_INDEX,
            'profile'     => LANG_REG_CFG_AUTH_REDIRECT_PROFILE,
            'profileedit' => LANG_REG_CFG_AUTH_REDIRECT_PROFILEEDIT
        );

        $model = new cmsModel();

        $show_notify_old_auth_options = $model->filterIsNull('password_hash')->getCount('{users}');

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_REGISTRATION,
                'childs' => array(

                    new fieldCheckbox('is_reg_enabled', array(
                        'title' => LANG_REG_CFG_IS_ENABLED,
                    )),

                    new fieldString('reg_reason', array(
                        'title' => LANG_REG_CFG_DISABLED_NOTICE,
                        'visible_depend' => array('is_reg_enabled' => array('show' => array('0')))
                    )),

                    new fieldCheckbox('is_reg_invites', array(
                        'title' => LANG_REG_CFG_IS_INVITES,
                    )),

                    new fieldCheckbox('reg_captcha', array(
                        'title' => LANG_REG_CFG_REG_CAPTCHA,
                    )),

                    new fieldCheckbox('reg_auto_auth', array(
                        'title'   => LANG_REG_CFG_REG_AUTO_AUTH,
                        'default' => 1
                    )),

                    new fieldListGroups('def_groups', array(
                        'title' => LANG_REG_CFG_DEF_GROUP_ID,
                        'show_all' => false,
						'default' => array(3),
                        'rules' => array(
                            array(function($controller, $data, $value) {
                                if($value){
                                    return true;
                                }
                                $public_groups = cmsCore::getModel('users')->getPublicGroups();
                                if(!$public_groups){
                                    return ERR_VALIDATE_REQUIRED;
                                }
                                return true;
                            })
                        )
                    )),

                    new fieldCheckbox('verify_email', array(
                        'title' => LANG_REG_CFG_VERIFY_EMAIL,
                        'hint' => LANG_REG_CFG_VERIFY_EMAIL_HINT,
                    )),

                    new fieldNumber('verify_exp', array(
                        'title'   => LANG_REG_CFG_VERIFY_EXPIRATION,
                        'default' => 48,
                        'rules' => array(
                            array('required'),
                            array('min', 1)
                        )
                    )),

                    new fieldCheckbox('send_greetmsg', array(
                        'title' => LANG_REG_CFG_SEND_GREETMSG
                    )),

                    new fieldHtml('greetmsg', array(
                        'title' => LANG_REG_CFG_GREETMSG,
                        'visible_depend' => array('send_greetmsg' => array('show' => array('1')))
                    ))
                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_AUTHORIZATION,
                'childs' => array(

                    new fieldCheckbox('notify_old_auth', array(
                        'title' => LANG_REG_CFG_NOTIFY_OLD_AUTH,
                        'hint' => LANG_REG_CFG_NOTIFY_OLD_AUTH_HINT,
                        'is_visible' => $show_notify_old_auth_options
                    )),

                    new fieldCheckbox('auth_captcha', array(
                        'title' => LANG_REG_CFG_AUTH_CAPTCHA,
                    )),

                    new fieldList('first_auth_redirect', array(
                        'title'   => LANG_REG_CFG_FIRST_AUTH_REDIRECT,
                        'default' => 'profileedit',
                        'items'   => $auth_redirect_items
                    )),

                    new fieldList('auth_redirect', array(
                        'title'   => LANG_REG_CFG_AUTH_REDIRECT,
                        'default' => 'none',
                        'items'   => $auth_redirect_items
                    )),

                    new fieldList('2fa', array(
                        'title' => LANG_REG_CFG_AUTH_2FA,
                        'is_chosen_multiple' => true,
                        'generator' => function(){

                            $providers = cmsEventsManager::hookAll('auth_twofactor_list');

                            $items = [];

                            if (is_array($providers)){
                                foreach($providers as $provider){
                                    foreach($provider['types'] as $name => $title){
                                        $items[$name] = $title;
                                    }
                                }
                            }

                            return $items;

                        }
                    ))

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_AUTH_RESTRICTIONS,
                'childs' => array(

                    new fieldCheckbox('is_site_only_auth_users', array(
                        'title' => LANG_CP_SETTINGS_SITE_ONLY_TO_USERS,
                    )),

                    new fieldList('guests_allow_controllers', array(
                        'title'     => LANG_REG_CFG_GUESTS_ALLOW_CONTROLLERS,
                        'default'   => array('auth', 'geo'),
                        'is_chosen_multiple' => true,
                        'generator' => function ($item){
                            $admin_model = cmsCore::getModel('admin');
                            $controllers = $admin_model->getInstalledControllers();
                            $items = array('' => '');
                            foreach($controllers as $controller){
                                $items[$controller['name']] = $controller['title'];
                            }
                            return $items;
                        },
                        'visible_depend' => array('is_site_only_auth_users' => array('show' => array('1')))
                    )),

                    new fieldText('restricted_emails', array(
                        'title' => LANG_AUTH_RESTRICTED_EMAILS,
                        'hint' => LANG_AUTH_RESTRICTED_EMAILS_HINT,
                    )),

                    new fieldText('restricted_names', array(
                        'title' => LANG_AUTH_RESTRICTED_NAMES,
                        'hint' => LANG_AUTH_RESTRICTED_NAMES_HINT,
                    )),

                    new fieldText('restricted_ips', array(
                        'title' => LANG_AUTH_RESTRICTED_IPS,
                        'hint' => LANG_AUTH_RESTRICTED_IPS_HINT,
                    )),

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_AUTH_INVITES,
                'childs' => array(

                    new fieldCheckbox('is_invites', array(
                        'title' => LANG_AUTH_INVITES_AUTO,
                        'hint' => LANG_AUTH_INVITES_AUTO_HINT

                    )),

                    new fieldCheckbox('is_invites_strict', array(
                        'title' => LANG_AUTH_INVITES_STRICT,
                        'hint' => LANG_AUTH_INVITES_STRICT_HINT

                    )),

                    new fieldNumber('invites_period', array(
                        'title' => LANG_AUTH_INVITES_PERIOD,
                        'units' => LANG_DAY10,
                        'default' => 7,
                        'rules' => array(
                            array('min', 1)
                        )
                    )),

                    new fieldNumber('invites_qty', array(
                        'title' => LANG_AUTH_INVITES_QTY,
                        'rules' => array(
                            array('min', 1)
                        )
                    )),

                    new fieldNumber('invites_min_karma', array(
                        'title' => LANG_AUTH_INVITES_KARMA,
                    )),

                    new fieldNumber('invites_min_rating', array(
                        'title' => LANG_AUTH_INVITES_RATING,
                    )),

                    new fieldNumber('invites_min_days', array(
                        'title' => LANG_AUTH_INVITES_DATE,
                        'units' => LANG_DAY10
                    ))

                )
            )

        );

    }

}
