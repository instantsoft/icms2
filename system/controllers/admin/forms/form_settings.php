<?php
class formAdminSettings extends cmsForm {

    public $is_tabbed = true;

    public function init() {

        $is_css_cache = cmsCore::getFilesList('cache/static/css', '*.css');
        $is_js_cache = cmsCore::getFilesList('cache/static/js', '*.js');
        $ctypes = cmsCore::getModel('content')->getContentTypes();

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_CP_SETTINGS_SITE,
                'childs' => array(

                    new fieldCheckbox('is_site_on', array(
                        'title' => LANG_CP_SETTINGS_SITE_ENABLED,
                    )),

                    new fieldCheckbox('is_site_only_auth_users', array(
                        'title' => LANG_CP_SETTINGS_SITE_ONLY_TO_USERS,
                    )),

                    new fieldString('off_reason', array(
                        'title' => LANG_CP_SETTINGS_SITE_REASON,
                    )),

                    new fieldString('sitename', array(
                        'title' => LANG_CP_SETTINGS_SITENAME,
                        'rules' => array(
                            array('required'),
                        )
                    )),

                    new fieldString('hometitle', array(
                        'title' => LANG_CP_SETTINGS_HOMETITLE,
                        'rules' => array(
                            array('required'),
                        )
                    )),

                    new fieldList('frontpage', array(
                        'title' => LANG_CP_SETTINGS_FP_SHOW,
                        'generator' => function($item) {

                            $items = array(
                                'none' => LANG_CP_SETTINGS_FP_SHOW_NONE,
                            );

                            $frontpage_types = cmsEventsManager::hookAll('frontpage_types');

                            if (is_array($frontpage_types)){
                                foreach($frontpage_types as $frontpage_type){
                                    foreach($frontpage_type['types'] as $name => $title){
                                        $items[$name] = $title;
                                    }
                                }
                            }

                            return $items;

                        }
                    )),

                    new fieldList('ctype_default', array(
                        'title' => LANG_CP_SETTINGS_CTYPE_DEF,
						'hint' => LANG_CP_SETTINGS_CTYPE_DEF_HINT,
                        'generator' => function($item) use($ctypes){

							$items[''] = LANG_NO;

                            if ($ctypes) {
                                foreach ($ctypes as $ctype) {
                                    $items[$ctype['name']] = $ctype['title'];
                                }
                            }

                            return $items;

                        }
                    )),

                    new fieldString('metakeys', array(
                        'title' => LANG_CP_SETTINGS_METAKEYS,
                    )),

                    new fieldText('metadesc', array(
                        'title' => LANG_CP_SETTINGS_METADESC,
                    )),

                    new fieldCheckbox('is_no_meta', array(
                        'title' => LANG_CP_SETTINGS_META_NO_DEFAULT,
                    )),

                    new fieldCheckbox('is_sitename_in_title', array(
                        'title'   => LANG_CP_SETTINGS_IS_SITENAME_IN_TITLE,
                        'default' => 1
                    )),

                    new fieldCheckbox('is_check_updates', array(
                        'title' => LANG_CP_SETTINGS_CHECK_UPDATES,
                    )),

                    new fieldString('detect_ip_key', array(
                        'title'   => LANG_CP_SETTINGS_DETECT_IP_KEY,
                        'hint'    => LANG_CP_SETTINGS_DETECT_IP_KEY_HINT,
                        'default' => 'REMOTE_ADDR'
                    )),

                    new fieldString('cookie_domain', array(
                        'title' => LANG_CP_SETTINGS_COOKIE_DOMAIN,
                        'hint'  => LANG_CP_SETTINGS_COOKIE_DOMAIN_HINT,
                        'suffix' => '<span class="auto_copy_value ajaxlink" data-value="'.str_replace('www.', '', $_SERVER['HTTP_HOST']).'">'.LANG_CP_SETTINGS_CURRENT_DOMAIN.$_SERVER['HTTP_HOST'].'</span>'
                    ))

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_CP_SETTINGS_GUI,
                'childs' => array(

                    new fieldList('template', array(
                        'title' => LANG_CP_SETTINGS_TEMPLATE,
                        'hint' => '<a href="#" data-url="'.href_to('admin', 'settings', 'theme').'">'.LANG_CP_SETTINGS_TEMPLATE_OPTIONS.'</a>',
                        'generator' => function($item) {
                            $tpls = cmsCore::getTemplates();
                            $items = array();
                            if ($tpls) {
                                foreach ($tpls as $tpl) {
                                    $items[$tpl] = $tpl;
                                }
                            }
                            return $items;
                        }
                    )),

                    new fieldList('template_admin', array(
                        'title' => LANG_CP_SETTINGS_TEMPLATE_ADMIN,
                        'hint' => '<a href="#" data-url="'.href_to('admin', 'settings', 'theme').'">'.LANG_CP_SETTINGS_TEMPLATE_OPTIONS.'</a>',
                        'generator' => function($item) {
                            $tpls = cmsCore::getTemplates();
                            $items = array(''=>LANG_BY_DEFAULT);
                            if ($tpls) {
                                foreach ($tpls as $tpl) {
                                    $items[$tpl] = $tpl;
                                }
                            }
                            return $items;
                        }
                    )),

                    new fieldList('template_mobile', array(
                        'title' => LANG_CP_SETTINGS_TEMPLATE_MOBILE,
                        'hint' => '<a href="#" data-url="'.href_to('admin', 'settings', 'theme').'">'.LANG_CP_SETTINGS_TEMPLATE_OPTIONS.'</a>',
                        'generator' => function($item) {
                            $tpls = cmsCore::getTemplates();
                            $items = array(''=>LANG_BY_DEFAULT);
                            if ($tpls) {
                                foreach ($tpls as $tpl) {
                                    $items[$tpl] = $tpl;
                                }
                            }
                            return $items;
                        }
                    )),

                    new fieldList('template_tablet', array(
                        'title' => LANG_CP_SETTINGS_TEMPLATE_TABLET,
                        'hint' => '<a href="#" data-url="'.href_to('admin', 'settings', 'theme').'">'.LANG_CP_SETTINGS_TEMPLATE_OPTIONS.'</a>',
                        'generator' => function($item) {
                            $tpls = cmsCore::getTemplates();
                            $items = array(''=>LANG_BY_DEFAULT);
                            if ($tpls) {
                                foreach ($tpls as $tpl) {
                                    $items[$tpl] = $tpl;
                                }
                            }
                            return $items;
                        }
                    )),

                    new fieldList('language', array(
                        'title' => LANG_CP_SETTINGS_LANGUAGE,
                        'generator' => function($item) {
                            $langs = cmsCore::getLanguages();
                            $items = array();
                            if ($langs) {
                                foreach ($langs as $lang) {
                                    $items[$lang] = mb_strtoupper($lang);
                                }
                            }
                            return $items;
                        }
                    )),

                    new fieldCheckbox('is_user_change_lang', array(
                        'title' => LANG_CP_SETTINGS_USER_CHANGE_LANG
                    )),

                    new fieldList('default_editor', array(
                        'title' => LANG_CP_SETTINGS_EDITOR,
                        'default' => 'redactor',
                        'generator' => function($item){
                            $items = array();
                            $editors = cmsCore::getWysiwygs();
                            foreach($editors as $editor){ $items[$editor] = $editor; }
                            return $items;
                        }
                    )),

                    new fieldCheckbox('show_breadcrumbs', array(
                        'title'   => LANG_CP_SETTINGS_SHOW_BREADCRUMBS,
                        'default' => 1
                    )),

                    new fieldCheckbox('min_html', array(
                        'title' => LANG_CP_SETTINGS_HTML_MINIFY,
                    )),

                    new fieldCheckbox('merge_css', array(
                        'title' => LANG_CP_SETTINGS_MERGE_CSS,
                        'hint' => $is_css_cache ? sprintf(LANG_CP_SETTINGS_CACHE_CLEAN_MERGED, href_to('admin', 'clear_cache', 'css')) : false
                    )),

                    new fieldCheckbox('merge_js', array(
                        'title' => LANG_CP_SETTINGS_MERGE_JS,
                        'hint' => $is_js_cache ? sprintf(LANG_CP_SETTINGS_CACHE_CLEAN_MERGED, href_to('admin', 'clear_cache', 'js')) : false
                    )),

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_CP_SETTINGS_DATE,
                'childs' => array(

                    new fieldList('time_zone', array(
                        'title' => LANG_CP_SETTINGS_TIMEZONE,
                        'generator' => function($item){
                            return cmsCore::getTimeZones();
                        }
                    )),

                    new fieldString('date_format', array(
                        'title' => LANG_CP_SETTINGS_DATE_FORMAT,
                        'rules' => array(
                            array('required'),
                        )
                    )),

                    new fieldString('date_format_js', array(
                        'title' => LANG_CP_SETTINGS_DATE_FORMAT_JS,
                        'rules' => array(
                            array('required'),
                        )
                    )),

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_CP_SETTINGS_MAIL,
                'childs' => array(

                    new fieldList('mail_transport', array(
                        'title' => LANG_CP_SETTINGS_MAIL_TRANSPORT,
                        'items' => array(
                            'mail' => 'PHP mail()',
                            'smtp' => 'SMTP',
                            'sendmail' => 'Sendmail',
                        )
                    )),

                    new fieldString('mail_from', array(
                        'title' => LANG_CP_SETTINGS_MAIL_FROM,
                        'rules' => array(
                            array('required'),
                        )
                    )),

                    new fieldString('mail_from_name', array(
                        'title' => LANG_CP_SETTINGS_MAIL_FROM_NAME
                    )),

                    new fieldString('mail_smtp_server', array(
                        'title' => LANG_CP_SETTINGS_MAIL_SMTP_HOST,
                    )),

                    new fieldNumber('mail_smtp_port', array(
                        'title' => LANG_CP_SETTINGS_MAIL_SMTP_PORT,
                    )),

                    new fieldCheckbox('mail_smtp_auth', array(
                        'title' => LANG_CP_SETTINGS_MAIL_SMTP_AUTH,
                    )),

                    new fieldString('mail_smtp_user', array(
                        'title' => LANG_CP_SETTINGS_MAIL_SMTP_USER,
                    )),

                    new fieldString('mail_smtp_pass', array(
                        'title' => LANG_CP_SETTINGS_MAIL_SMTP_PASS,
                        'is_password' => true
                    )),

                    new fieldList('mail_smtp_enc', array(
                        'title' => LANG_CP_SETTINGS_MAIL_SMTP_ENC,
                        'items' => array(
							0 => LANG_CP_SETTINGS_MAIL_SMTP_ENC_NO,
							'ssl' => LANG_CP_SETTINGS_MAIL_SMTP_ENC_SSL,
							'tls' => LANG_CP_SETTINGS_MAIL_SMTP_ENC_TLS,
						)
                    )),

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_CP_SETTINGS_CACHE,
                'childs' => array(

                    new fieldCheckbox('cache_enabled', array(
                        'title' => LANG_CP_SETTINGS_CACHE_ENABLED,
                    )),

                    new fieldNumber('cache_ttl', array(
                        'title' => LANG_CP_SETTINGS_CACHE_TTL,
                    )),

                    new fieldList('cache_method', array(
                        'title' => LANG_CP_SETTINGS_CACHE_METHOD,
                        'hint'  => !cmsConfig::get('cache_enabled') ? '' : sprintf(LANG_CP_SETTINGS_CACHE_CLEAN_MERGED, href_to('admin', 'cache_delete', cmsConfig::get('cache_method'))),
                        'items' => array(
                            'files' => 'Files',
                            'memory' => 'Memcached' . (extension_loaded('memcache') ? '' : ' ('.LANG_CP_SETTINGS_CACHE_METHOD_NO.')'),
                        )
                    )),

                    new fieldString('cache_host', array(
                        'title' => LANG_CP_SETTINGS_CACHE_HOST,
                    )),

                    new fieldNumber('cache_port', array(
                        'title' => LANG_CP_SETTINGS_CACHE_PORT,
                    )),

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_CP_SETTINGS_DEBUG,
                'childs' => array(

                    new fieldCheckbox('debug', array(
                        'title' => LANG_CP_SETTINGS_DEBUG_MODE,
                    )),

                    new fieldCheckbox('manifest_from_files', array(
                        'title' => LANG_CP_SETTINGS_MANIFEST_FROM_FILES,
                    )),

                    new fieldCheckbox('emulate_lag', array(
                        'title' => LANG_CP_SETTINGS_EMULATE_LAG,
                    )),

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_CP_SETTINGS_SECURITY,
                'childs' => array(

                    new fieldText('allow_ips', array(
                        'title' => LANG_CP_SETTINGS_ALLOW_IPS,
                        'hint'  => sprintf(LANG_CP_SETTINGS_ALLOW_IPS_HINT, cmsUser::getIp())
                    )),

                    new fieldList('check_spoofing_type', array(
                        'title' => LANG_CP_CHECK_SPOOFING_TYPE,
                        'items' => array(
                            0 => LANG_NO,
                            1 => LANG_CP_CHECK_SPOOFING_TYPE_OPT1,
                            2 => LANG_CP_CHECK_SPOOFING_TYPE_OPT2
                        )
                    ))

                )
            )

        );

    }


}
