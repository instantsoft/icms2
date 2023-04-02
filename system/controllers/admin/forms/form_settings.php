<?php

class formAdminSettings extends cmsForm {

    public $is_tabbed = true;

    public function init() {

        $is_css_cache = cmsCore::getFilesList('cache/static/css', '*.css');
        $is_js_cache  = cmsCore::getFilesList('cache/static/js', '*.js');
        $ctypes       = cmsCore::getModel('content')->getContentTypes();

        $open_basedir      = @ini_get('open_basedir');
        $open_basedir_hint = '';

        if ($open_basedir) {
            $open_basedirs     = explode(PATH_SEPARATOR, $open_basedir);
            $open_basedir_hint = LANG_CP_SETTINGS_SESSIONS_BASEDIR . implode(' ' . LANG_OR . ' ', $open_basedirs);
        }

        $frontend_templates = [];
        $backend_templates  = [];

        $tpls = cmsCore::getTemplates();

        if ($tpls) {
            foreach ($tpls as $tpl) {

                $template_path = cmsConfig::get('root_path') . cmsTemplate::TEMPLATE_BASE_PATH . $tpl;

                $manifest = cmsTemplate::getTemplateManifest($template_path);

                if ($manifest !== null) {

                    if (!empty($manifest['properties']['is_frontend'])) {
                        $frontend_templates[$tpl] = !empty($manifest['title']) ? $manifest['title'] : $tpl;
                    }
                    if (!empty($manifest['properties']['is_backend'])) {
                        $backend_templates[$tpl] = !empty($manifest['title']) ? $manifest['title'] : $tpl;
                    }

                    continue;
                }
                // Нет манифестов, делаем по старинке
                if (file_exists($template_path . '/main.tpl.php')) {
                    $frontend_templates[$tpl] = $tpl;
                }
                if (file_exists($template_path . '/admin.tpl.php')) {
                    $backend_templates[$tpl] = $tpl;
                }
            }
        }

        return [
            [
                'type'   => 'fieldset',
                'title'  => LANG_CP_SETTINGS_SITE,
                'childs' => [
                    new fieldCheckbox('is_site_on', [
                        'title' => LANG_CP_SETTINGS_SITE_ENABLED
                    ]),
                    new fieldString('off_reason', [
                        'title' => LANG_CP_SETTINGS_SITE_REASON,
                        'can_multilanguage' => true,
                        'visible_depend' => ['is_site_on' => ['show' => ['0']]]
                    ]),
                    new fieldString('sitename', [
                        'title' => LANG_CP_SETTINGS_SITENAME,
                        'can_multilanguage' => true,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldList('frontpage', [
                        'title'     => LANG_CP_SETTINGS_FP_SHOW,
                        'generator' => function ($item) {

                            $items = [
                                'none' => LANG_CP_SETTINGS_FP_SHOW_NONE,
                            ];

                            $frontpage_types = cmsEventsManager::hookAll('frontpage_types');

                            if (is_array($frontpage_types)) {
                                foreach ($frontpage_types as $frontpage_type) {
                                    foreach ($frontpage_type['types'] as $name => $title) {
                                        $items[$name] = $title;
                                    }
                                }
                            }

                            return $items;
                        }
                    ]),
                    new fieldList('ctype_default', [
                        'title' => LANG_CP_SETTINGS_CTYPE_DEF,
                        'is_chosen_multiple' => true,
                        'hint' => LANG_CP_SETTINGS_CTYPE_DEF_HINT,
                        'generator' => function ($item) use ($ctypes) {

                            $items = [];

                            if ($ctypes) {
                                foreach ($ctypes as $ctype) {
                                    $items[$ctype['name']] = $ctype['title'];
                                }
                            }

                            return $items;
                        }
                    ]),
                    new fieldCheckbox('is_check_updates', [
                        'title' => LANG_CP_SETTINGS_CHECK_UPDATES,
                    ]),
                    new fieldString('detect_ip_key', [
                        'title'   => LANG_CP_SETTINGS_DETECT_IP_KEY,
                        'hint'    => LANG_CP_SETTINGS_DETECT_IP_KEY_HINT,
                        'default' => 'REMOTE_ADDR'
                    ])
                ]
            ],
            [
                'type'   => 'fieldset',
                'title'  => 'SEO',
                'childs' => [
                    new fieldString('hometitle', [
                        'title' => LANG_CP_SETTINGS_HOMETITLE,
                        'can_multilanguage' => true,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('metakeys', [
                        'title' => LANG_CP_SETTINGS_METAKEYS,
                        'can_multilanguage' => true,
                        'visible_depend' => ['disable_metakeys' => ['hide' => ['1']]]
                    ]),
                    new fieldText('metadesc', [
                        'title'         => LANG_CP_SETTINGS_METADESC,
                        'can_multilanguage' => true,
                        'is_strip_tags' => true
                    ]),
                    new fieldCheckbox('is_no_meta', [
                        'title' => LANG_CP_SETTINGS_META_NO_DEFAULT,
                        'hint'  => LANG_CP_SETTINGS_META_NO_DEFAULT_HINT
                    ]),
                    new fieldCheckbox('disable_metakeys', [
                        'title' => LANG_CP_SETTINGS_DISABLE_METAKEYS
                    ]),
                    new fieldCheckbox('is_sitename_in_title', [
                        'title'   => LANG_CP_SETTINGS_IS_SITENAME_IN_TITLE,
                        'default' => 1
                    ]),
                    new fieldCheckbox('page_num_in_title', [
                        'title' => LANG_CP_SETTINGS_PAGE_NUM_IN_TITLE
                    ]),
                    new fieldCheckbox('set_head_preload', [
                        'title' => LANG_CP_SETTINGS_SET_HEAD_PRELOAD,
                        'hint'  => '<a href="https://w3c.github.io/preload/" target="_blank">HTTP Preload</a>'
                    ])
                ]
            ],
            [
                'type'   => 'fieldset',
                'title'  => LANG_CP_SETTINGS_GUI,
                'childs' => [
                    new fieldList('template', [
                        'title' => LANG_CP_SETTINGS_TEMPLATE,
                        'hint'  => '<a class="theme_settings theme_settings_options" href="#" data-url="' . href_to('admin', 'settings', 'theme') . '">' . LANG_CP_SETTINGS_TEMPLATE_OPTIONS . '</a><a class="theme_settings inthemer" target="_blank" href="https://addons.instantcms.ru/addons/inthemer.html">' . LANG_CP_SETTINGS_TEMPLATE_INTH . '</a>',
                        'items' => $frontend_templates
                    ]),
                    new fieldList('template_admin', [
                        'title' => LANG_CP_SETTINGS_TEMPLATE_ADMIN,
                        'hint'  => '<a class="theme_settings theme_settings_options" href="#" data-url="' . href_to('admin', 'settings', 'theme') . '">' . LANG_CP_SETTINGS_TEMPLATE_OPTIONS . '</a>',
                        'items' => ['' => LANG_BY_DEFAULT] + $backend_templates
                    ]),
                    new fieldList('template_mobile', [
                        'title' => LANG_CP_SETTINGS_TEMPLATE_MOBILE,
                        'hint'  => '<a class="theme_settings theme_settings_options" href="#" data-url="' . href_to('admin', 'settings', 'theme') . '">' . LANG_CP_SETTINGS_TEMPLATE_OPTIONS . '</a>',
                        'items' => ['' => LANG_BY_DEFAULT] + $frontend_templates
                    ]),
                    new fieldList('template_tablet', [
                        'title' => LANG_CP_SETTINGS_TEMPLATE_TABLET,
                        'hint'  => '<a class="theme_settings theme_settings_options" href="#" data-url="' . href_to('admin', 'settings', 'theme') . '">' . LANG_CP_SETTINGS_TEMPLATE_OPTIONS . '</a>',
                        'items' => ['' => LANG_BY_DEFAULT] + $frontend_templates
                    ]),
                    new fieldList('language', [
                        'title'     => LANG_CP_SETTINGS_LANGUAGE,
                        'generator' => function ($item) {
                            $langs = cmsCore::getLanguages();
                            $items = [];
                            if ($langs) {
                                foreach ($langs as $lang) {
                                    $items[$lang] = mb_strtoupper($lang);
                                }
                            }
                            return $items;
                        }
                    ]),
                    new fieldCheckbox('is_user_change_lang', [
                        'title' => LANG_CP_SETTINGS_USER_CHANGE_LANG
                    ]),
                    new fieldCheckbox('is_browser_auto_lang', [
                        'title'          => LANG_CP_SETTINGS_BROWSER_AUTO_LANG,
                        'visible_depend' => ['is_user_change_lang' => ['show' => ['1']]]
                    ]),
                    new fieldList('default_editor', [
                        'title'     => LANG_CP_SETTINGS_EDITOR,
                        'default'   => 'redactor',
                        'generator' => function ($item) {
                            $items   = [];
                            $editors = cmsCore::getWysiwygs();
                            foreach ($editors as $editor) {
                                $items[$editor] = ucfirst($editor);
                            }
                            $ps = cmsCore::getModel('wysiwygs')->getPresetsList();
                            if ($ps) {
                                foreach ($ps as $key => $value) {
                                    $items[$key] = $value;
                                }
                            }
                            return $items;
                        }
                    ]),
                    new fieldCheckbox('show_breadcrumbs', [
                        'title'   => LANG_CP_SETTINGS_SHOW_BREADCRUMBS,
                        'default' => 1
                    ]),
                    new fieldCheckbox('min_html', [
                        'title' => LANG_CP_SETTINGS_HTML_MINIFY,
                    ]),
                    new fieldCheckbox('merge_css', [
                        'title' => LANG_CP_SETTINGS_MERGE_CSS,
                        'hint'  => $is_css_cache ? sprintf(LANG_CP_SETTINGS_CACHE_CLEAN_MERGED, href_to('admin', 'clear_cache', 'css')) : false
                    ]),
                    new fieldCheckbox('merge_js', [
                        'title' => LANG_CP_SETTINGS_MERGE_JS,
                        'hint'  => $is_js_cache ? sprintf(LANG_CP_SETTINGS_CACHE_CLEAN_MERGED, href_to('admin', 'clear_cache', 'js')) : false
                    ]),
                    new fieldNumber('production_time', [
                        'title'   => LANG_CP_SETTINGS_PRODUCTION_TIME,
                        'hint'    => LANG_CP_SETTINGS_PRODUCTION_TIME_HINT,
                        'default' => time()
                    ])
                ]
            ],
            [
                'type'   => 'fieldset',
                'title'  => LANG_CP_SETTINGS_DATE,
                'childs' => [
                    new fieldList('time_zone', [
                        'title'     => LANG_CP_SETTINGS_TIMEZONE,
                        'generator' => function ($item) {

                            $zones = (new cmsConfigs('timezones.php'))->getAll();

                            return array_combine($zones, $zones);
                        }
                    ]),
                    new fieldCheckbox('allow_users_time_zone', [
                        'title'   => LANG_CP_SETTINGS_ALLOW_USERS_TIMEZONE,
                        'default' => 1
                    ]),
                    new fieldString('date_format', [
                        'title' => LANG_CP_SETTINGS_DATE_FORMAT,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('date_format_js', [
                        'title' => LANG_CP_SETTINGS_DATE_FORMAT_JS,
                        'rules' => [
                            ['required']
                        ]
                    ])
                ]
            ],
            [
                'type'   => 'fieldset',
                'title'  => LANG_CP_SETTINGS_MAIL,
                'childs' => [
                    new fieldString('mail_from', [
                        'title' => LANG_CP_SETTINGS_MAIL_FROM,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('mail_from_name', [
                        'title' => LANG_CP_SETTINGS_MAIL_FROM_NAME,
                        'can_multilanguage' => true
                    ]),
                    new fieldList('mail_transport', [
                        'title' => LANG_CP_SETTINGS_MAIL_TRANSPORT,
                        'items' => [
                            'mail'     => 'PHP mail()',
                            'smtp'     => 'SMTP',
                            'sendmail' => 'Sendmail',
                        ]
                    ]),
                    new fieldString('mail_smtp_server', [
                        'title'          => LANG_CP_SETTINGS_MAIL_SMTP_HOST,
                        'visible_depend' => ['mail_transport' => ['show' => ['smtp']]]
                    ]),
                    new fieldNumber('mail_smtp_port', [
                        'title'          => LANG_CP_SETTINGS_MAIL_SMTP_PORT,
                        'visible_depend' => ['mail_transport' => ['show' => ['smtp']]]
                    ]),
                    new fieldCheckbox('mail_smtp_auth', [
                        'title'          => LANG_CP_SETTINGS_MAIL_SMTP_AUTH,
                        'visible_depend' => ['mail_transport' => ['show' => ['smtp']]]
                    ]),
                    new fieldString('mail_smtp_user', [
                        'title'          => LANG_CP_SETTINGS_MAIL_SMTP_USER,
                        'visible_depend' => ['mail_transport' => ['show' => ['smtp']]]
                    ]),
                    new fieldString('mail_smtp_pass', [
                        'title'          => LANG_CP_SETTINGS_MAIL_SMTP_PASS,
                        'is_password'    => true,
                        'visible_depend' => ['mail_transport' => ['show' => ['smtp']]]
                    ]),
                    new fieldList('mail_smtp_enc', [
                        'title' => LANG_CP_SETTINGS_MAIL_SMTP_ENC,
                        'items' => [
                            0     => LANG_CP_SETTINGS_MAIL_SMTP_ENC_NO,
                            'ssl' => LANG_CP_SETTINGS_MAIL_SMTP_ENC_SSL,
                            'tls' => LANG_CP_SETTINGS_MAIL_SMTP_ENC_TLS,
                        ],
                        'visible_depend' => ['mail_transport' => ['show' => ['smtp']]]
                    ])
                ]
            ],
            [
                'type'   => 'fieldset',
                'title'  => LANG_CP_SETTINGS_CACHE,
                'childs' => [
                    new fieldCheckbox('cache_enabled', [
                        'title' => LANG_CP_SETTINGS_CACHE_ENABLED
                    ]),
                    new fieldNumber('cache_ttl', [
                        'title'          => LANG_CP_SETTINGS_CACHE_TTL,
                        'visible_depend' => ['cache_enabled' => ['show' => ['1']]]
                    ]),
                    new fieldList('cache_method', [
                        'title' => LANG_CP_SETTINGS_CACHE_METHOD,
                        'hint'  => !cmsConfig::get('cache_enabled') ? '' : sprintf(LANG_CP_SETTINGS_CACHE_CLEAN_MERGED, href_to('admin', 'cache_delete', cmsConfig::get('cache_method'))),
                        'items' => [
                            'files'     => 'Files',
                            'memory'    => 'Memcache' . (extension_loaded('memcache') ? '' : ' (' . LANG_CP_SETTINGS_CACHE_METHOD_NO . ')'),
                            'memcached' => 'Memcached' . (extension_loaded('memcached') ? '' : ' (' . LANG_CP_SETTINGS_CACHE_METHOD_NO . ')'),
                        ],
                        'visible_depend' => ['cache_enabled' => ['show' => ['1']]]
                    ]),
                    new fieldString('cache_host', [
                        'title'          => LANG_CP_SETTINGS_CACHE_HOST,
                        'visible_depend' => [
                            'cache_method'  => ['show' => ['memory', 'memcached']],
                            'cache_enabled' => ['hide' => ['0']]
                        ]
                    ]),
                    new fieldNumber('cache_port', [
                        'title'          => LANG_CP_SETTINGS_CACHE_PORT,
                        'visible_depend' => [
                            'cache_method'  => ['show' => ['memory', 'memcached']],
                            'cache_enabled' => ['hide' => ['0']]
                        ]
                    ])
                ]
            ],
            [
                'type'   => 'fieldset',
                'title'  => LANG_CP_SETTINGS_SESSIONS,
                'childs' => [
                    new fieldList('session_save_handler', [
                        'title' => LANG_CP_SETTINGS_SESSIONS_SAVE_HANDLER,
                        'items' => [
                            'files'     => 'Files',
                            'memcache'  => 'Memcache' . (extension_loaded('memcache') ? '' : ' (' . LANG_CP_SETTINGS_CACHE_METHOD_NO . ')'),
                            'memcached' => 'Memcached' . (extension_loaded('memcached') ? '' : ' (' . LANG_CP_SETTINGS_CACHE_METHOD_NO . ')'),
                        ],
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('session_save_path', [
                        'title' => LANG_CP_SETTINGS_SESSIONS_SAVE_PATH,
                        'hint'  => sprintf(LANG_CP_SETTINGS_SESSIONS_SAVE_PATH_HINT, $open_basedir_hint),
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldString('session_name', [
                        'title' => LANG_CP_SETTINGS_SESSION_NAME,
                        'hint'  => LANG_CP_SETTINGS_SESSION_NAME_HINT,
                        'rules' => [
                            ['required']
                        ]
                    ]),
                    new fieldNumber('session_maxlifetime', [
                        'title'   => LANG_CP_SETTINGS_SESSION_MAXLIFETIME,
                        'default' => ini_get('session.gc_maxlifetime') / 60,
                        'units'   => LANG_MINUTES,
                        'rules'   => [
                            ['required'],
                            ['min', 1]
                        ]
                    ]),
                    new fieldString('cookie_domain', [
                        'title'  => LANG_CP_SETTINGS_COOKIE_DOMAIN,
                        'hint'   => LANG_CP_SETTINGS_COOKIE_DOMAIN_HINT,
                        'suffix' => '<span class="auto_copy_value ajaxlink" data-value="' . str_replace('www.', '', $_SERVER['HTTP_HOST']) . '">' . LANG_CP_SETTINGS_CURRENT_DOMAIN . $_SERVER['HTTP_HOST'] . '</span>'
                    ])
                ]
            ],
            [
                'type'   => 'fieldset',
                'title'  => LANG_CP_SETTINGS_DB,
                'childs' => [
                    new fieldList('db_charset', [
                        'title'   => LANG_CP_SETTINGS_DB_CHARSET,
                        'hint'    => LANG_CP_SETTINGS_DB_CHARSET_HINT,
                        'default' => 'utf8',
                        'items'   => [
                            'utf8mb4' => 'UTF8mb4',
                            'utf8'    => 'UTF8'
                        ],
                        'rules'   => [
                            ['required']
                        ]
                    ]),
                    new fieldCheckbox('clear_sql_mode', [
                        'title' => LANG_CP_SETTINGS_DB_CLEAR_SQL_MODE
                    ])
                ]
            ],
            [
                'type'   => 'fieldset',
                'title'  => LANG_CP_SETTINGS_DEBUG,
                'childs' => [
                    new fieldCheckbox('debug', [
                        'title' => LANG_CP_SETTINGS_DEBUG_MODE,
                    ]),
                    new fieldCheckbox('emulate_lag', [
                        'title' => LANG_CP_SETTINGS_EMULATE_LAG,
                    ])
                ]
            ],
            [
                'type'   => 'fieldset',
                'title'  => LANG_CP_SETTINGS_SECURITY,
                'childs' => [
                    new fieldText('allow_ips', [
                        'title'         => LANG_CP_SETTINGS_ALLOW_IPS,
                        'hint'          => sprintf(LANG_CP_SETTINGS_ALLOW_IPS_HINT, cmsUser::getIp()),
                        'is_strip_tags' => true
                    ]),
                    new fieldList('check_spoofing_type', [
                        'title' => LANG_CP_CHECK_SPOOFING_TYPE,
                        'items' => [
                            0 => LANG_NO,
                            1 => LANG_CP_CHECK_SPOOFING_TYPE_OPT1,
                            2 => LANG_CP_CHECK_SPOOFING_TYPE_OPT2
                        ]
                    ])
                ]
            ]
        ];
    }

}
