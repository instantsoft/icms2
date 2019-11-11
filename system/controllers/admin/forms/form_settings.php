<?php
class formAdminSettings extends cmsForm {

    public $is_tabbed = true;

    public function init() {

        $is_css_cache = cmsCore::getFilesList('cache/static/css', '*.css');
        $is_js_cache = cmsCore::getFilesList('cache/static/js', '*.js');
        $ctypes = cmsCore::getModel('content')->getContentTypes();

        $open_basedir = @ini_get('open_basedir'); $open_basedir_hint = '';

        if($open_basedir){
            $open_basedirs = explode(PATH_SEPARATOR, $open_basedir);
            $open_basedir_hint = LANG_CP_SETTINGS_SESSIONS_BASEDIR.implode(' '.LANG_OR.' ', $open_basedirs);
        }

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_CP_SETTINGS_SITE,
                'childs' => array(

                    new fieldCheckbox('is_site_on', array(
                        'title' => LANG_CP_SETTINGS_SITE_ENABLED,
                    )),

                    new fieldString('off_reason', array(
                        'title' => LANG_CP_SETTINGS_SITE_REASON,
                        'visible_depend' => array('is_site_on' => array('show' => array('0')))
                    )),

                    new fieldString('sitename', array(
                        'title' => LANG_CP_SETTINGS_SITENAME,
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
                        'is_chosen_multiple' => true,
						'hint' => LANG_CP_SETTINGS_CTYPE_DEF_HINT,
                        'generator' => function($item) use($ctypes){

                            $items = [];

                            if ($ctypes) {
                                foreach ($ctypes as $ctype) {
                                    $items[$ctype['name']] = $ctype['title'];
                                }
                            }

                            return $items;

                        }
                    )),

                    new fieldCheckbox('is_check_updates', array(
                        'title' => LANG_CP_SETTINGS_CHECK_UPDATES,
                    )),

                    new fieldString('cookie_domain', array(
                        'title' => LANG_CP_SETTINGS_COOKIE_DOMAIN,
                        'hint'  => LANG_CP_SETTINGS_COOKIE_DOMAIN_HINT,
                        'suffix' => '<span class="auto_copy_value ajaxlink" data-value="'.str_replace('www.', '', $_SERVER['HTTP_HOST']).'">'.LANG_CP_SETTINGS_CURRENT_DOMAIN.$_SERVER['HTTP_HOST'].'</span>'
                    )),

                    new fieldString('detect_ip_key', array(
                        'title'   => LANG_CP_SETTINGS_DETECT_IP_KEY,
                        'hint'    => LANG_CP_SETTINGS_DETECT_IP_KEY_HINT,
                        'default' => 'REMOTE_ADDR'
                    ))

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => 'SEO',
                'childs' => array(

                    new fieldString('hometitle', array(
                        'title' => LANG_CP_SETTINGS_HOMETITLE,
                        'rules' => array(
                            array('required'),
                        )
                    )),

                    new fieldString('metakeys', array(
                        'title' => LANG_CP_SETTINGS_METAKEYS,
			'visible_depend' => array('disable_metakeys' => array('hide' => array('1')))
                    )),

                    new fieldText('metadesc', array(
                        'title' => LANG_CP_SETTINGS_METADESC,
                        'is_strip_tags' => true
                    )),

                    new fieldCheckbox('is_no_meta', array(
                        'title' => LANG_CP_SETTINGS_META_NO_DEFAULT,
                        'hint' => LANG_CP_SETTINGS_META_NO_DEFAULT_HINT
                    )),

                    new fieldCheckbox('disable_metakeys', array(
                        'title' => LANG_CP_SETTINGS_DISABLE_METAKEYS
                    )),

                    new fieldCheckbox('is_sitename_in_title', array(
                        'title'   => LANG_CP_SETTINGS_IS_SITENAME_IN_TITLE,
                        'default' => 1
                    )),

                    new fieldCheckbox('page_num_in_title', array(
                        'title'   => LANG_CP_SETTINGS_PAGE_NUM_IN_TITLE
                    )),

                    new fieldCheckbox('set_head_preload', array(
                        'title'   => LANG_CP_SETTINGS_SET_HEAD_PRELOAD,
                        'hint' => '<a href="https://w3c.github.io/preload/" target="_blank">HTTP Preload</a>'
                    ))

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_CP_SETTINGS_GUI,
                'childs' => array(

                    new fieldList('template', array(
                        'title' => LANG_CP_SETTINGS_TEMPLATE,
                        'hint' => '<a class="theme_settings theme_settings_options" href="#" data-url="'.href_to('admin', 'settings', 'theme').'">'.LANG_CP_SETTINGS_TEMPLATE_OPTIONS.'</a><a class="theme_settings inthemer" target="_blank" href="https://addons.instantcms.ru/addons/inthemer.html">'.LANG_CP_SETTINGS_TEMPLATE_INTH.'</a>',
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
                        'hint' => '<a class="theme_settings theme_settings_options" href="#" data-url="'.href_to('admin', 'settings', 'theme').'">'.LANG_CP_SETTINGS_TEMPLATE_OPTIONS.'</a>',
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
                        'hint' => '<a class="theme_settings theme_settings_options" href="#" data-url="'.href_to('admin', 'settings', 'theme').'">'.LANG_CP_SETTINGS_TEMPLATE_OPTIONS.'</a>',
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
                        'hint' => '<a class="theme_settings theme_settings_options" href="#" data-url="'.href_to('admin', 'settings', 'theme').'">'.LANG_CP_SETTINGS_TEMPLATE_OPTIONS.'</a>',
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
                            $items = [];
                            $editors = cmsCore::getWysiwygs();
                            foreach($editors as $editor){
                                $items[$editor] = ucfirst($editor);
                            }
                            $ps = cmsCore::getModel('wysiwygs')->getPresetsList();
                            if($ps){
                                foreach ($ps as $key => $value) {
                                    $items[$key] = $value;
                                }
                            }
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

                    new fieldNumber('production_time', array(
                        'title'   => LANG_CP_SETTINGS_PRODUCTION_TIME,
                        'hint'    => LANG_CP_SETTINGS_PRODUCTION_TIME_HINT,
                        'default' => time()
                    ))

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

                    new fieldString('mail_from', array(
                        'title' => LANG_CP_SETTINGS_MAIL_FROM,
                        'rules' => array(
                            array('required'),
                        )
                    )),

                    new fieldString('mail_from_name', array(
                        'title' => LANG_CP_SETTINGS_MAIL_FROM_NAME
                    )),

                    new fieldList('mail_transport', array(
                        'title' => LANG_CP_SETTINGS_MAIL_TRANSPORT,
                        'items' => array(
                            'mail' => 'PHP mail()',
                            'smtp' => 'SMTP',
                            'sendmail' => 'Sendmail',
                        )
                    )),

                    new fieldString('mail_smtp_server', array(
                        'title' => LANG_CP_SETTINGS_MAIL_SMTP_HOST,
                        'visible_depend' => array('mail_transport' => array('show' => array('smtp')))
                    )),

                    new fieldNumber('mail_smtp_port', array(
                        'title' => LANG_CP_SETTINGS_MAIL_SMTP_PORT,
                        'visible_depend' => array('mail_transport' => array('show' => array('smtp')))
                    )),

                    new fieldCheckbox('mail_smtp_auth', array(
                        'title' => LANG_CP_SETTINGS_MAIL_SMTP_AUTH,
                        'visible_depend' => array('mail_transport' => array('show' => array('smtp')))
                    )),

                    new fieldString('mail_smtp_user', array(
                        'title' => LANG_CP_SETTINGS_MAIL_SMTP_USER,
                        'visible_depend' => array('mail_transport' => array('show' => array('smtp')))
                    )),

                    new fieldString('mail_smtp_pass', array(
                        'title' => LANG_CP_SETTINGS_MAIL_SMTP_PASS,
                        'is_password' => true,
                        'visible_depend' => array('mail_transport' => array('show' => array('smtp')))
                    )),

                    new fieldList('mail_smtp_enc', array(
                        'title' => LANG_CP_SETTINGS_MAIL_SMTP_ENC,
                        'items' => array(
							0 => LANG_CP_SETTINGS_MAIL_SMTP_ENC_NO,
							'ssl' => LANG_CP_SETTINGS_MAIL_SMTP_ENC_SSL,
							'tls' => LANG_CP_SETTINGS_MAIL_SMTP_ENC_TLS,
						),
                        'visible_depend' => array('mail_transport' => array('show' => array('smtp')))
                    ))

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
                        'visible_depend' => array('cache_enabled' => array('show' => array('1')))
                    )),

                    new fieldList('cache_method', array(
                        'title' => LANG_CP_SETTINGS_CACHE_METHOD,
                        'hint'  => !cmsConfig::get('cache_enabled') ? '' : sprintf(LANG_CP_SETTINGS_CACHE_CLEAN_MERGED, href_to('admin', 'cache_delete', cmsConfig::get('cache_method'))),
                        'items' => array(
                            'files' => 'Files',
                            'memory' => 'Memcache' . (extension_loaded('memcache') ? '' : ' ('.LANG_CP_SETTINGS_CACHE_METHOD_NO.')'),
                            'memcached' => 'Memcached' . (extension_loaded('memcached') ? '' : ' ('.LANG_CP_SETTINGS_CACHE_METHOD_NO.')'),
                        ),
                        'visible_depend' => array('cache_enabled' => array('show' => array('1')))
                    )),

                    new fieldString('cache_host', array(
                        'title' => LANG_CP_SETTINGS_CACHE_HOST,
                        'visible_depend' => array(
                            'cache_method' => array('show' => array('memory', 'memcached')),
                            'cache_enabled' => array('hide' => array('0'))
                        )
                    )),

                    new fieldNumber('cache_port', array(
                        'title' => LANG_CP_SETTINGS_CACHE_PORT,
                        'visible_depend' => array(
                            'cache_method' => array('show' => array('memory', 'memcached')),
                            'cache_enabled' => array('hide' => array('0'))
                        )
                    ))

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_CP_SETTINGS_SESSIONS,
                'childs' => array(

                    new fieldList('session_save_handler', array(
                        'title' => LANG_CP_SETTINGS_SESSIONS_SAVE_HANDLER,
                        'items' => array(
                            'files' => 'Files',
                            'memcache' => 'Memcache' . (extension_loaded('memcache') ? '' : ' ('.LANG_CP_SETTINGS_CACHE_METHOD_NO.')'),
                            'memcached' => 'Memcached' . (extension_loaded('memcached') ? '' : ' ('.LANG_CP_SETTINGS_CACHE_METHOD_NO.')'),
                        ),
                        'rules' => array(
                            array('required')
                        )
                    )),

                    new fieldString('session_save_path', array(
                        'title' => LANG_CP_SETTINGS_SESSIONS_SAVE_PATH,
                        'hint'  => sprintf(LANG_CP_SETTINGS_SESSIONS_SAVE_PATH_HINT, $open_basedir_hint),
                        'rules' => array(
                            array('required'),
                        )
                    )),

                    new fieldNumber('session_maxlifetime', array(
                        'title' => LANG_CP_SETTINGS_SESSION_MAXLIFETIME,
                        'default' => ini_get('session.gc_maxlifetime')/60,
                        'units' => LANG_MINUTES,
                        'rules' => array(
                            array('required'),
                        )
                    ))

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_CP_SETTINGS_DB,
                'childs' => array(

                    new fieldList('db_charset', array(
                        'title' => LANG_CP_SETTINGS_DB_CHARSET,
                        'hint' => LANG_CP_SETTINGS_DB_CHARSET_HINT,
                        'default' => 'utf8',
                        'items' => array(
                            'utf8mb4' => 'UTF8mb4',
                            'utf8' => 'UTF8'
                        ),
                        'rules' => array(
                            array('required')
                        )
                    )),

                    new fieldCheckbox('clear_sql_mode', array(
                        'title' => LANG_CP_SETTINGS_DB_CLEAR_SQL_MODE
                    ))

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
                    ))

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_CP_SETTINGS_SECURITY,
                'childs' => array(

                    new fieldText('allow_ips', array(
                        'title' => LANG_CP_SETTINGS_ALLOW_IPS,
                        'hint'  => sprintf(LANG_CP_SETTINGS_ALLOW_IPS_HINT, cmsUser::getIp()),
                        'is_strip_tags' => true
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
