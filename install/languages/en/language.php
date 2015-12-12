<?php

    define('LANG_PAGE_TITLE',               'InstantCMS Installation');
    define('LANG_INSTALLATION_WIZARD',      'Installation wizard');
    define('LANG_NEXT',                     'Continue &rarr;');

    define('LANG_MANUAL',                   '<a href="http://docs.instantcms.ru/en/manual/install" target="_blank">Installation Manual</a>');

    define('LANG_BETA_WARNING',             'This is a release candidate distributed only for testing purposes');

    define('LANG_STEP_LANGUAGE',            'Language');
    define('LANG_STEP_START',               'Introduction');
    define('LANG_STEP_LICENSE',             'License');
    define('LANG_STEP_PHP_CHECK',           'PHP');
    define('LANG_STEP_PATHS',               'Paths');
    define('LANG_STEP_DATABASE',            'Database');
    define('LANG_STEP_SITE',                'Site');
    define('LANG_STEP_ADMIN',               'Administrator');
    define('LANG_STEP_CONFIG',              'Configuration');
    define('LANG_STEP_CRON',                'Scheduler');
    define('LANG_STEP_FINISH',              'Finish');

    define('LANG_STEP_START_1',             'The installation wizard will check whether your server meets the system requirements.');
    define('LANG_STEP_START_2',             'The wizard will ask you some questions required for the correct installation and configuration.');
    define('LANG_STEP_START_3',             'Before you start you have to create a clean MySQL database encoded in <b>utf8_general_ci</b>');

    define('LANG_LICENSE_AGREE',            'I agree with the terms of the license');
    define('LANG_LICENSE_ERROR',            'You must agree to the license terms');
    define('LANG_LICENSE_NOTE',             'InstantCMS is licensed under <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GNU/GPL</a> v2.');
    define('LANG_LICENSE_ORIGINAL',         'English');
    define('LANG_LICENSE_TRANSLATION',      'Russian');

    define('LANG_PHP_VERSION',              'PHP version');
    define('LANG_PHP_VERSION_REQ',          'PHP 5.3 or higher is required');
    define('LANG_PHP_VERSION_DESC',         'Installed version');
	define('LANG_PHP_VARIABLES',            'PHP configuration');
	define('LANG_PHP_VARIABLES_HINT',       'Required values shown in gray');
	define('LANG_PHP_VARIABLES_ON',			'On');
	define('LANG_PHP_VARIABLES_OFF',		'Off');
    define('LANG_PHP_EXTENSIONS',           'Required extensions');
    define('LANG_PHP_EXTENSIONS_REQ',       'These extensions are required for InstantCMS to operate');
    define('LANG_PHP_EXTENSIONS_EXTRA',     'Recommended extensions');
    define('LANG_PHP_EXTENSIONS_EXTRA_REQ', 'These extensions are optional, but without them some functionality may be inaccessible');
    define('LANG_PHP_EXT_INSTALLED',        'Installed');
    define('LANG_PHP_EXT_NOT_INSTALLED',    'Not found');
    define('LANG_PHP_CHECK_ERROR',          "You can't continue until all red values will be fixed.");
    define('LANG_PHP_CHECK_ERROR_HINT',     'Contact technical support of your hosting provider with the request to provide the required conditions');

    define('LANG_PATHS_ROOT_INFO',          'All paths are relative to: <br/><span class="root-path">%s</span>');
    define('LANG_PATHS_ROOT_CHANGE',        'edit');
    define('LANG_PATHS_CHANGE_INFO',        'You can change these paths later in configuration file');
    define('LANG_PATHS_HTACCESS_INFO',      'You are installing InstantCMS not in a site\'s root folder. So you must additionally edit .htaccess file. <a href="http://docs.instantcms.ru/manual/install?&#указание-путей" target="_blank">How to do it</a> (in russian)');
    define('LANG_PATHS_MUST_WRITABLE',      'Must be writable');
    define('LANG_PATHS_NOT_WRITABLE',       'is not writable!');
    define('LANG_PATHS_WRITABLE_HINT',      'Set the correct permissions on this folder');

    define('LANG_PATHS_ROOT',               'Site root');
    define('LANG_PATHS_ROOT_PATH',          'Root path');
    define('LANG_PATHS_ROOT_HOST',          'Root URL');
    define('LANG_PATHS_UPLOAD',             'Uploads');
    define('LANG_PATHS_UPLOAD_PATH',        'Uploads path');
    define('LANG_PATHS_UPLOAD_HOST',        'Uploads URL');
    define('LANG_PATHS_CACHE',              'Cache');
    define('LANG_PATHS_CACHE_PATH',         'Cache path');

    define('LANG_DATABASE_INFO',            'Enter the details to connect to the MySQL database');
    define('LANG_DATABASE_CHARSET_INFO',    'The database must be encoded in <b>utf8_general_ci</b>');
    define('LANG_DATABASE_HOST',            'MySQL Server');
    define('LANG_DATABASE_USER',            'Username');
    define('LANG_DATABASE_PASS',            'Password');
    define('LANG_DATABASE_BASE',            'Database');
    define('LANG_DATABASE_ENGINE',          'Database engine');
    define('LANG_DATABASE_ENGINE_HINT',     'Do not know what to choose? Choose MyISAM.');
    define('LANG_DATABASE_PREFIX',          'Table prefix');
    define('LANG_DATABASE_USERS_TABLE',     'Users table');
    define('LANG_DATABASE_USERS_TABLE_NEW', 'Create new');
    define('LANG_DATABASE_USERS_TABLE_OLD', 'Use an existing table');
    define('LANG_DATABASE_INSTALL_DEMO',    'To set a demo content');

    define('LANG_DATABASE_CONNECT_ERROR',   "MySQL Connection Error:\n\n%s");
    define('LANG_DATABASE_BASE_ERROR',      "Error importing database\nCheck the provided settings");
    define('LANG_DATABASE_ENGINE_NO',       'The engine is not supported');
    define('LANG_DATABASE_ENGINE_DISABLED', 'The engine is supported but has been disabled');
    define('LANG_DATABASE_ENGINE_ERROR',    'The engine is not supported by server');

    define('LANG_SITE_SITENAME',            "Site name");
    define('LANG_SITE_HOMETITLE',           "Frontpage title");
    define('LANG_SITE_METAKEYS',            "Site keywords");
    define('LANG_SITE_METADESC',            "Site description");

    define('LANG_SITE_SITENAME_ERROR',      "Site name is required");

    define('LANG_ADMIN_EXTERNAL',           "Administrator's account will be taken from the table <b>%s</b>");
    define('LANG_ADMIN_INFO',               "Please enter administrator's name, e-mail and password");
    define('LANG_ADMIN_NAME',               'Name');
    define('LANG_ADMIN_EMAIL',              'E-mail');
    define('LANG_ADMIN_PASS',               'Password');
    define('LANG_ADMIN_PASS2',              'Repeat password');

    define('LANG_ADMIN_ERROR',              'Please fill in all fields');
    define('LANG_ADMIN_EMAIL_ERROR',        'Incorrect e-mail address');
    define('LANG_ADMIN_PASS_ERROR',         'Passwords do not match');

    define('LANG_CONFIG_INFO',              'Now the configuration file will be created.');
    define('LANG_CONFIG_PATH',              'File location:');
    define('LANG_CONFIG_MUST_WRITABLE',     'Specified folder must be writable.');
    define('LANG_CONFIG_AFTER',             'When configuration file will be created you should make that folder not writable.');
    define('LANG_CONFIG_NOT_WRITABLE',      'Folder is not writable');

    define('LANG_CRON_1',                   "It's time to add job to the server's CRON scheduler.");
    define('LANG_CRON_2',                   'This will allow the system to perform periodic service tasks in the background.');
    define('LANG_CRON_FILE',                'File to run: <b>%s</b>');
    define('LANG_CRON_INT',                 'Interval: <b>5 minutes</b>');
    define('LANG_CRON_EXAMPLE',             'Usually, the command you should add to the scheduler looks like:');
    define('LANG_CRON_SUPPORT_1',           "For more information about CRON settings please visit the FAQ section of your hosting provider's website.");
    define('LANG_CRON_SUPPORT_2',           'If you have troubles, please contact the technical support of your hosting provider.');

    define('LANG_FINISH_1',                 'Installation completed.');
    define('LANG_FINISH_2',                 'Before proceeding, remove the <b>install</b> folder in the site root.');

    define('LANG_FINISH_TO_SITE',           'Go to the website');

    define('LANG_CFG_OFF_REASON',           'The site is closed for maintenance');
    define('LANG_CFG_SITENAME',             'InstantCMS 2.0');
    define('LANG_CFG_HOMETITLE',            'InstantCMS 2.0');
    define('LANG_CFG_DATE_FORMAT',          'm/d/Y');
    define('LANG_CFG_DATE_FORMAT_JS',       'mm/dd/yy');
    define('LANG_CFG_TIME_ZONE',            'Europe/London');
    define('LANG_CFG_METAKEYS',             'site, blog, community');
    define('LANG_CFG_METADESC',             'My social site');
