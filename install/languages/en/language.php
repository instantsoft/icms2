<?php

    define('LANG_PAGE_TITLE',               'InstantCMS Installation');
    define('LANG_INSTALLATION_WIZARD',      'Installation wizard');
    define('LANG_NEXT',                     'Next &rarr;');
    define('LANG_ERROR',                    'Error');

    define('LANG_MANUAL',                   '<a href="http://docs.instantcms.ru/en/manual/install" target="_blank" rel="noopener noreferrer">Installation Manual</a>');

    define('LANG_LANGUAGE_SELECT_RU',       'Пожалуйста, выберите язык');
    define('LANG_LANGUAGE_SELECT_EN',       'Please select a language');

    define('LANG_STEP_LANGUAGE',            'Language');
    define('LANG_STEP_START',               'Introduction');
    define('LANG_STEP_LICENSE',             'License');
    define('LANG_STEP_PHP_CHECK',           'PHP Check');
    define('LANG_STEP_PATHS',               'Paths');
    define('LANG_STEP_DATABASE',            'Database');
    define('LANG_STEP_SITE',                'Site');
    define('LANG_STEP_ADMIN',               'Administration');
    define('LANG_STEP_CONFIG',              'Configuration');
    define('LANG_STEP_CRON',                'Scheduler');
    define('LANG_STEP_FINISH',              'Finish');

    define('LANG_STEP_START_1',             'The InstantCMS installation wizard will check whether your server meets the system requirements.');
    define('LANG_STEP_START_2',             'The wizard will ask you a series of questions that are required for the correct installation and configuration.');

    define('LANG_LICENSE_AGREE',            'I agree to the license terms');
    define('LANG_LICENSE_ERROR',            'You need to agree to the license terms');
    define('LANG_LICENSE_NOTE',             'InstantCMS is licensed under <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank" rel="noopener noreferrer">GNU/GPL</a> v2.');
    define('LANG_LICENSE_ORIGINAL',         'English');
    define('LANG_LICENSE_TRANSLATION',      'Russian');

    define('LANG_PHP_VERSION',              'PHP version');
    define('LANG_PHP_VERSION_REQ',          'PHP 7.0 or higher is required');
    define('LANG_PHP_VERSION_DESC',         'Installed version');
	define('LANG_PHP_VARIABLES',            'PHP configuration');
	define('LANG_PHP_VARIABLES_HINT',       'Required conditions are marked gray');
	define('LANG_PHP_VARIABLES_ON',			'On');
	define('LANG_PHP_VARIABLES_OFF',		'Off');
    define('LANG_PHP_EXTENSIONS',           'Required extensions');
    define('LANG_PHP_EXTENSIONS_REQ',       'These extensions are essential for InstantCMS');
    define('LANG_PHP_EXTENSIONS_EXTRA',     'Recommended extensions');
    define('LANG_PHP_EXTENSIONS_EXTRA_REQ', 'These extensions are optional, however, <br>update and cache functions may not be available.');
    define('LANG_PHP_EXT_INSTALLED',        'Installed');
    define('LANG_PHP_EXT_NOT_INSTALLED',    'Not found');
    define('LANG_PHP_CHECK_ERROR',          'You will not be able to proceed the installation unless all conditions that are marked red are fixed.');
    define('LANG_PHP_CHECK_ERROR_HINT',     'Request your hosting support to provide the required conditions and restart the installation.');

    define('LANG_PATHS_ROOT_INFO',          'All paths are relative to:<br/><span class="root-path">%s</span>');
    define('LANG_PATHS_ROOT_CHANGE',        'edit');
    define('LANG_PATHS_CHANGE_INFO',        'You will be able to edit the paths in the configuration file later.<br/>Do not forget to do it after migration from local server to hosting!');
    define('LANG_PATHS_MUST_WRITABLE',      'Must be writable');
    define('LANG_PATHS_NOT_WRITABLE',       'not writable!');
    define('LANG_PATHS_WRITABLE_HINT',      'Set correct permissions for the folder');

    define('LANG_PATHS_ROOT',               'Site root');
    define('LANG_PATHS_ROOT_PATH',          'Root path');
    define('LANG_PATHS_ROOT_HOST',          'Root URL');
    define('LANG_PATHS_UPLOAD',             'Uploads');
    define('LANG_PATHS_UPLOAD_PATH',        'Upload path');
    define('LANG_PATHS_UPLOAD_HOST',        'Upload URL');
    define('LANG_PATHS_CACHE',              'Cache');
    define('LANG_PATHS_CACHE_PATH',         'Cache path');
    define('LANG_PATHS_SESSION',            'Sessions');
    define('LANG_PATHS_SESSION_PATH',       'Session save path');
    define('LANG_PATHS_SESSIONS_BASEDIR',    'The full path to the file system, which must be in one of the paths ');

    define('LANG_DATABASE_INFO',            'Provide your MySQL database connection details');
    define('LANG_DATABASE_HOST',            'MySQL Server');
    define('LANG_DATABASE_USER',            'Username');
    define('LANG_DATABASE_PASS',            'Password');
    define('LANG_DATABASE_BASE',            'Database');
    define('LANG_DATABASE_BASE_HINT',       'Create if not exists');
    define('LANG_DATABASE_ENGINE',          'Database engine');
    define('LANG_DATABASE_ENGINE_HINT',     'If you don\'t know what to choose, select InnoDB.');
    define('LANG_DATABASE_CHARSET',         'Database collation');
    define('LANG_DATABASE_PREFIX',          'Table prefix');
    define('LANG_DATABASE_USERS_TABLE',     'User table');
    define('LANG_DATABASE_USERS_TABLE_NEW', 'Create new');
    define('LANG_DATABASE_USERS_TABLE_OLD', 'Use an existing table');
    define('LANG_DATABASE_INSTALL_DEMO',    'Install demo content');

    define('LANG_DATABASE_PREFIX_ERROR',    'Database prefix can contain only letters, numbers, and underscores');
    define('LANG_DATABASE_SELECT_ERROR',    'Unable to select the "%s" database');
    define('LANG_DATABASE_CONNECT_ERROR',   "MySQL Connection Error:\n\n%s");
    define('LANG_DATABASE_BASE_ERROR',      "Database import error\nCheck provided details");
    define('LANG_DATABASE_ENGINE_NO',       'Database engine is not supported');
    define('LANG_DATABASE_ENGINE_DISABLED', 'Database engine is supported but has been disabled in MySQL settings');
    define('LANG_DATABASE_ENGINE_ERROR',    'Database engine is not supported by the server');
    define('LANG_DATABASE_CH_ERROR',        'Table collation is not supported by the server');

    define('LANG_SITE_SITENAME',            "Site name");
    define('LANG_SITE_HOMETITLE',           "Main page title");
    define('LANG_SITE_METAKEYS',            "Keywords");
    define('LANG_SITE_METADESC',            "Description");
    define('LANG_SITE_CHECK_UPDATE',        "Automatically check for InstantCMS updates");
    define('LANG_SITE_TEMPLATE',            'Site template');
    define('LANG_SITE_TEMPLATE_ADMIN',      'Admin panel template');

    define('LANG_SITE_SITENAME_ERROR',      "Site name is required");
    define('LANG_SITE_HOMETITLE_ERROR',     "Home title is required");

    define('LANG_ADMIN_EXTERNAL',           'Administrator details will be taken from the <b>%s</b> table');
    define('LANG_ADMIN_INFO',               'Main administrator details');
    define('LANG_ADMIN_NAME',               'Name');
    define('LANG_ADMIN_EMAIL',              'E-mail');
    define('LANG_ADMIN_PASS',               'Password');
    define('LANG_ADMIN_PASS2',              'Repeat password');

    define('LANG_ADMIN_ERROR',              'All fields are required');
    define('LANG_ADMIN_EMAIL_ERROR',        'Incorrect e-mail address');
    define('LANG_ADMIN_PASS_ERROR',         'Passwords do not match');
    define('LANG_ADMIN_PASS_HASH_ERROR',    'Error creating password hash, try again');
    define('LANG_VALIDATE_MIN_LENGTH',      'Too short %s field value (min. length: %s)');
    define('LANG_VALIDATE_MAX_LENGTH',      'Too long %s field value (max. length: %s)');

    define('LANG_CONFIG_INFO',              'The configuration file will be created now.');
    define('LANG_CONFIG_PATH',              'File location:');
    define('LANG_CONFIG_MUST_WRITABLE',     'The folder must be writable.');
    define('LANG_CONFIG_AFTER',             'Once the configuration file is created, make the folder (and files in it) not writable.');
    define('LANG_CONFIG_NOT_WRITABLE',      'Folder not writable');

    define('LANG_CRON_1',                   'You need to schedule a CRON job on your web server to make InstantCMS function properly.');
    define('LANG_CRON_2',                   'This will allow the system to execute periodic tasks in the background.');
    define('LANG_CRON_FILE',                'File to run: <b>%s</b>');
    define('LANG_CRON_INT',                 'Interval: <b>5 minutes</b>');
    define('LANG_CRON_EXAMPLE',             'Usually, the command for the scheduler looks like:');
    define('LANG_CRON_SUPPORT_1',           'For more information about CRON settings please see the FAQ section on the site of your hosting provider.');
    define('LANG_CRON_SUPPORT_2',           'If you experience difficulties, copy the text above and request your hosting support.');

    define('LANG_FINISH_1',                 'InstantCMS installation is complete.');
    define('LANG_FINISH_2',                 'Delete the <b>install</b> folder from the root directory before you proceed.');

    define('LANG_FINISH_TO_SITE',           'Visit site');

    define('LANG_CFG_OFF_REASON',           'The site is closed for maintenance');
    define('LANG_CFG_SITENAME',             'InstantCMS 2');
    define('LANG_CFG_HOMETITLE',            'InstantCMS 2');
    define('LANG_CFG_DATE_FORMAT',          'd.m.Y');
    define('LANG_CFG_DATE_FORMAT_JS',       'dd.mm.yy');
    define('LANG_CFG_TIME_ZONE',            'Europe/London');
    define('LANG_CFG_METAKEYS',             'site, blog, community');
    define('LANG_CFG_METADESC',             'My social site');
