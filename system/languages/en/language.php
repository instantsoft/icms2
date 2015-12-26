<?php

    define('LANG_LOADING',                   'Loading...');
    define('LANG_SENDING',                   'Sending data...');
    define('LANG_MORE',                      'More');
    define('LANG_VERSION',                   'Version');

    //CONTENT
    define('LANG_CONTENT_TYPE',              'Content Type');
    define('LANG_CONTENT_TYPE_SETTINGS',     'Configure %s');
    define('LANG_CONTENT_ADD_ITEM',          'Add %s');
    define('LANG_CONTENT_EDIT_ITEM',         'Edit %s');
    define('LANG_CONTENT_DELETE_ITEM',       'Delete %s');
    define('LANG_CONTENT_DELETE_ITEM_CONFIRM',  'Are you sure you want to delete %s?');
    define('LANG_CONTENT_NOT_APPROVED',      'Pending');
    define('LANG_CONTENT_NOT_IS_PUB',		 'Not published');

    define('LANG_SHOW_FILTER',               'Show filter');
    define('LANG_FILTER',                    'Filter');
    define('LANG_FILTERS',                   'Filters');
    define('LANG_FILTER_FIELD',              'Filter by field');
    define('LANG_FILTER_ADD',                'Add filter');
    define('LANG_FILTER_LIKE',               'contain');
    define('LANG_FILTER_LIKE_BEGIN',         'starting with');
    define('LANG_FILTER_LIKE_END',           'ends with');
    define('LANG_FILTER_DATE_YOUNGER',       'younger, days');
    define('LANG_FILTER_DATE_OLDER',         'older, дней');
    define('LANG_FILTER_NOT_NULL',           'filled');
    define('LANG_FILTER_IS_NULL',            'not filled');
    define('LANG_FILTER_APPLY',              'Apply');
    define('LANG_FILTER_URL',                'Link');
    define('LANG_SORTING_FIELD',             'Sort by field');
    define('LANG_SORTING',                   'Sorting');
    define('LANG_SORTING_ADD',               'Add rule');
    define('LANG_SORTING_ASC',               'Ascending');
    define('LANG_SORTING_DESC',              'Descending');
    define('LANG_PHOTO',                     'Image');
    define('LANG_PHOTOS',                    'Images');
    define('LANG_COMMENTS',                  'Comments');
    define('LANG_RATING',                    'Rating');
    define('LANG_KARMA',                     'Reputation');
    define('LANG_KARMA_UP',                  'Vote Up');
    define('LANG_KARMA_DOWN',                'Vote Down');
    define('LANG_CSS_CLASS',                 'CSS class');
    define('LANG_CSS_CLASS_WRAP',            'CSS class for container');
    define('LANG_CSS_CLASS_TITLE',           'CSS class for title');
    define('LANG_CSS_CLASS_BODY',            'CSS class for body');
    define('LANG_GROUP',                     'Group');
    define('LANG_GROUPS',                    'Groups');
    define('LANG_WROTE_IN_GROUP',            'in');
    define('LANG_DESIGN',					 'Design');

    //WIDGETS
    define('LANG_WP_SYSTEM',                 'System');
    define('LANG_WP_CUSTOM',                 'Custom');
    define('LANG_WP_ALL_PAGES',              'All pages');
    define('LANG_WP_HOME_PAGE',              'Home page');
    define('LANG_WIDGET_TAB_PREV',           'Group with previous widget');
    define('LANG_WIDGET_TITLE_LINKS',        'Links in the header of the widget');
    define('LANG_WIDGET_TITLE_LINKS_HINT',   'In the format <b><em>Title | URL</em></b>, for example <b><em>Google | http://www.google.com</em></b><br>If the link is wrapped in braces <b>{ }</b> it will be shown only to logged users<br>One link in each row');
    define('LANG_WIDGET_WRAPPER_TPL',		 'Wrapper template');
    define('LANG_WIDGET_WRAPPER_TPL_HINT',	 'File from the <b>widgets</b> folder in your theme, without <b>.tpl.php</b>');
    define('LANG_WIDGET_BODY_TPL',			 'Widget template');
    define('LANG_WIDGET_BODY_TPL_HINT',	     'File from the <b>%s</b> folder in your theme, without <b>.tpl.php</b>');

    //PARSERS
    define('LANG_PARSER_CAPTION',            'Caption');
    define('LANG_PARSER_STRING',             'String');
    define('LANG_PARSER_HIDDEN',             'Hidden field');
    define('LANG_PARSER_NUMBER',             'Number');
    define('LANG_PARSER_NUMBER_FILTER_RANGE','Filter by range');
    define('LANG_PARSER_NUMBER_UNITS',       'Units');

    define('LANG_PARSER_CITY',               'City');
    define('LANG_PARSER_CITY_FILTER_HINT',   'City ID');
    define('LANG_PARSER_CHECKBOX',           'Checkbox');
    define('LANG_PARSER_TEXT',               'Textarea');
    define('LANG_PARSER_TEXT_MAX_LEN',       'Max length');
    define('LANG_PARSER_TEXT_MIN_LEN',       'Min length');
    define('LANG_PARSER_HTML',               'HTML');
    define('LANG_PARSER_HTML_EDITOR',        'WYSIWYG editor');
    define('LANG_PARSER_HTML_FILTERING',     'Sanitize input');
    define('LANG_PARSER_HTML_TEASER_LEN',    'Strip text length in list view');
    define('LANG_PARSER_HTML_TEASER_LEN_HINT','The text will be truncated to the specified length, the formatting will be removed');
    define('LANG_PARSER_BBCODE',             'Textarea with BB-code');
    define('LANG_PARSER_LIST',               'List');
    define('LANG_PARSER_LIST_FILTER_HINT',   'Item index');
    define('LANG_PARSER_LIST_FILTER_MULTI',  'Allow multiple selection in filter');
    define('LANG_PARSER_LIST_GROUPS',        'Users groups list');
    define('LANG_PARSER_LIST_GROUPS_SHOW_GUESTS',     'Show item "Guests"');
    define('LANG_PARSER_LIST_IS_MULTIPLE',   'Allow multiple selection');
    define('LANG_PARSER_LIST_MULTIPLE',      'Multiple list');
    define('LANG_PARSER_LIST_MULTIPLE_SHOW_ALL',      'Show item "All"');
    define('LANG_PARSER_URL',                'Link');
    define('LANG_PARSER_URL_REDIRECT',       'Links through a redirect');
    define('LANG_PARSER_URL_AUTO_HTTP',      'Automatically adds http://');
    define('LANG_PARSER_AGE',                'Age');
    define('LANG_PARSER_AGE_DATE_TITLE',     'Starting date title');
    define('LANG_PARSER_AGE_FILTER_RANGE',   'Filter by range');
    define('LANG_PARSER_DATE',               'Date');
    define('LANG_PARSER_DATE_FILTER_HINT',   'YYYY-MM-DD');
    define('LANG_PARSER_DATE_SHOW_TIME',     'Show time');
    define('LANG_PARSER_USER',               'User');
    define('LANG_PARSER_USER_FILTER_HINT',   'User ID');
    define('LANG_PARSER_USERS',              'Users list');
    define('LANG_PARSER_IMAGE',              'Image');
    define('LANG_PARSER_IMAGES',             'Multiple images');
    define('LANG_PARSER_IMAGE_SIZE_UPLOAD',  'Create thumbnails');
    define('LANG_PARSER_IMAGE_SIZE_TEASER',  'Size on list page');
    define('LANG_PARSER_IMAGE_SIZE_FULL',    'Size on item page');
    define('LANG_PARSER_IMAGE_SIZE_MICRO',   'Micro');
    define('LANG_PARSER_IMAGE_SIZE_SMALL',   'Small');
    define('LANG_PARSER_IMAGE_SIZE_NORMAL',  'Medium');
    define('LANG_PARSER_IMAGE_SIZE_BIG',     'Big');
    define('LANG_PARSER_IMAGE_SIZE_ORIGINAL','Original');
    define('LANG_PARSER_COLOR',              'Colorpicker');
    define('LANG_PARSER_FILE',               'File');
    define('LANG_PARSER_FILE_LABEL',         'File link label');
    define('LANG_PARSER_FILE_LABEL_NAME',    'Filename');
    define('LANG_PARSER_FILE_LABEL_GET',     'Download');
    define('LANG_PARSER_FILE_EXTS',          'Allowed file extensions');
    define('LANG_PARSER_FILE_EXTS_HINT',     'Extensions list separated with commas');
    define('LANG_PARSER_FILE_EXTS_FIELD_HINT',     'Allowed file types: %s');
    define('LANG_PARSER_FILE_SIZE_FIELD_HINT',     'Max file size: %s');
    define('LANG_PARSER_FILE_MAX_SIZE',      'Max file size, Mb');
    define('LANG_PARSER_FILE_MAX_SIZE_PHP',  'Not higher than %d Mb (allowed in PHP settings)');
    define('LANG_PARSER_FILE_SHOW_SIZE',     'Show file size');
    define('LANG_PARSER_CURRENT_TIME',       'Current time');
    define('LANG_PARSER_IN_FULLTEXT_SEARCH', 'Add the field into full-text search');
    define('LANG_PARSER_IN_FULLTEXT_SEARCH_HINT', 'Notice: changing this option will force index to rebuild. That may take long time for large tables.');
    //USERS
    define('LANG_USER',                      'Member');
    define('LANG_USERS',                     'Members');
    define('LANG_AUTHOR',                    'Author');
    define('LANG_REGISTRATION',              'Registration');
    define('LANG_USER_REGISTRATION',         'User Registration');
    define('LANG_CREATE_ACCOUNT',            'Please, sign up');
    define('LANG_LOG_IN',                    'Log in');
    define('LANG_LOG_IN_ACCOUNT',            'Log in using your account');
    define('LANG_LOG_IN_OPENID',             'Log in through social networks');
    define('LANG_LOG_OUT',                   'Log out');
    define('LANG_NO_ACCOUNT',                'Not a member?');
    define('LANG_REG_FIRST_TIME',            'It\'s your first visit?');
    define('LANG_REG_ALREADY',               'Already a member?');
    define('LANG_EMAIL',                     'E-mail');
    define('LANG_PASSWORD',                  'Password');
    define('LANG_RETYPE_PASSWORD',           'Repeat password');
    define('LANG_USER_GROUP',                'Group');
    define('LANG_USER_IS_ADMIN',             'Administrator');
    define('LANG_LOGIN_ERROR',               "The information you provided isn't correct - please try again.");
    define('LANG_LOGIN_REQUIRED',            'You must login to see requested page');
    define('LANG_LOGIN_ADMIN_ONLY',          'Only administrator can login when site is offline');
    define('LANG_NICKNAME',                  'Nickname');
    define('LANG_ADMIN',                     'Administrator');
    define('LANG_EMAIL_FIND',                'Find by e-mail');
    define('LANG_FIND',                      'Find');
    define('LANG_MY_PROFILE',                'My profile');
    define('LANG_PROFILE',                   'Profile');
    define('LANG_CITY',                      'City');
    define('LANG_COMPANY',                   'Company');
    define('LANG_PHONE',                     'Phone');
    define('LANG_NAME',                      'First Name');
    define('LANG_SURNAME',                   'Last Name');
    define('LANG_CHANGE_PASS',               'Change password');
    define('LANG_OLD_PASS',                  'Current password');
    define('LANG_OLD_PASS_INCORRECT',        'The current password is incorrect');
    define('LANG_NEW_PASS',                  'New password');
    define('LANG_RETYPE_NEW_PASS',           'Repeat new password');
    define('LANG_PASS_CHANGED',              'Password succesfully changed');
    define('LANG_REMEMBER_ME',               'Stay signed');
    define('LANG_PLEASE_LOGIN',              'Please, log in');
    define('LANG_LOGIN_ADMIN',               'Administrators login');
    define('LANG_ONLINE',                    'Online');

    //MODERATION
    define('LANG_MODERATION',                'Moderation');
    define('LANG_MODERATOR',                 'Moderator');
    define('LANG_MODERATORS',                'Moderators');
    define('LANG_MODERATION_APPROVE',        'Approve');
    define('LANG_MODERATION_APPROVED',       'Page published');
    define('LANG_MODERATION_APPROVED_BY',    'Approved by moderator');
    define('LANG_MODERATION_PM_AUTHOR',      'Message to author');
    define('LANG_MODERATION_NOTICE',         'Page will be published after moderator approval');
    define('LANG_MODERATION_IDLE',           'Notification sended to moderator %s');
    define('LANG_MODERATION_NO_TASKS',       'There are no pages requiring approval');

    //PERMISSIONS
    define('LANG_PERMISSIONS',               'Permissions');
    define('LANG_PERM_RULE',                 'Permission rule');
    define('LANG_PERM_OPTION_NULL',          'No');
    define('LANG_PERM_OPTION_OWN',           'Only own');
    define('LANG_PERM_OPTION_ALL',           'All');
    define('LANG_SHOW_TO_GROUPS',            'Show to groups');
    define('LANG_HIDE_FOR_GROUPS',           'Hide from groups');

    //AUTHORIZATION
    define('LANG_AUTH_LOGIN',			 'Login');
    define('LANG_AUTH_PASSWORD',		 'Password');

    //PASSWORD RESTORE
    define('LANG_FORGOT_PASS',               'Forgot your password?');

    //SYSTEM ERRORS
    define('LANG_ERROR',					 'Error');
    define('LANG_FORM_ERRORS',				 'The form has errors');
    define('LANG_TRACE_STACK',				 'Recent calls');
    define('ERR_COMPONENT_NOT_FOUND',		 'Component not found');
    define('ERR_MODEL_NOT_FOUND',			 'Model not found');
    define('ERR_TEMPLATE_NOT_FOUND', 		 'Template not found');
    define('ERR_LIBRARY_NOT_FOUND', 		 'Library not found');
    define('ERR_FILE_NOT_FOUND',             'File not found');
    define('ERR_CLASS_NOT_FOUND', 		 	 'Class not found');
    define('ERR_MODULE_NOT_FOUND', 		 	 'Module not found');
    define('ERR_DATABASE_QUERY', 		 	 '<b>Query error</b>: <p>%s</p>');
    define('ERR_DATABASE_CONNECT', 		 	 'Database connection error');
    define('ERR_PAGE_NOT_FOUND', 		 	 'Page not found');
    define('ERR_SITE_OFFLINE',               'Site is offline');
    define('ERR_SITE_OFFLINE_FULL',          'Site is offline. <a href="%s">Enable</a>');

    //UPLOAD ERRORS
    define('LANG_UPLOAD_ERR_OK', 'File uploaded successfully');
    define('LANG_UPLOAD_ERR_INI_SIZE', 'The file size exceeds the allowable: %s');
    define('LANG_UPLOAD_ERR_FORM_SIZE', 'The file size exceeds the allowable');
    define('LANG_UPLOAD_ERR_PARTIAL', 'The file was not fully loaded');
    define('LANG_UPLOAD_ERR_NO_FILE', 'No file was uploaded');
    define('LANG_UPLOAD_ERR_NO_TMP_DIR', 'Folder for temporary files on the server not found');
    define('LANG_UPLOAD_ERR_CANT_WRITE', 'Failed to write file to disk');
    define('LANG_UPLOAD_ERR_EXTENSION', 'File download was interrupted');
    define('LANG_UPLOAD_ERR_MIME', 'The file has the wrong format');

    //MONEY
    define('LANG_CURRENCY',                  '$');

    //VALIDATION ERRORS
    define('ERR_VALIDATE_REQUIRED',          'Field is required');
    define('ERR_VALIDATE_MIN',               'Number is too small (min: %s)');
    define('ERR_VALIDATE_MAX',               'Number is too big (max: %s)');
    define('ERR_VALIDATE_MIN_LENGTH',        'Value is too short (min length: %s)');
    define('ERR_VALIDATE_MAX_LENGTH',        'Value is too long (max length: %s)');
    define('ERR_VALIDATE_EMAIL',             'Invalid e-mail format');
    define('ERR_VALIDATE_REGEXP',            'Invalid format');
    define('ERR_VALIDATE_ALPHANUMERIC',      'Only letters and digits');
    define('ERR_VALIDATE_SYSNAME',           'Only letters (lower case), digits and underscores');
    define('ERR_VALIDATE_SLUG',              'Only letters (lower case), digits, hyphen and forward slash');
    define('ERR_VALIDATE_DIGITS',            'Only digits');
    define('ERR_VALIDATE_NUMBER',            'Enter a number');
    define('ERR_VALIDATE_UNIQUE',            'Value is already used');
    define('ERR_VALIDATE_INVALID',           'Invalid value');

    define('LANG_VALIDATE_REQUIRED',         'Required field');
    define('LANG_VALIDATE_DIGITS',           'Only digits');
    define('LANG_VALIDATE_NUMBER',           'Only numbers');
    define('LANG_VALIDATE_ALPHANUMERIC',     'Only letters and digits');
    define('LANG_VALIDATE_EMAIL',            'E-mail address');
    define('LANG_VALIDATE_UNIQUE',           'Unique value');

    define('ERR_REQ_EMAIL', 		 	 	 'You must specify the e-mail');
    define('ERR_EMPTY_FIELDS', 			 	 'All fields are required');
    define('ERR_NICKNAME_EXISTS', 			 'Nickname &laquo;%s&raquo; is already used');
    define('ERR_WRONG_OLD_PASS', 		 	 'Failed to change password: Old password is incorrect');
    define('ERR_NEW_PASS_MISMATCH', 		 'Failed to change password: Passwords do not match');
    define('ERR_NEW_PASS_REQUIRED', 		 'You must specify a new password twice!');

    //CAPTCHA
    define('LANG_CAPTCHA_CODE', 		 	 'Spam protection');
    define('LANG_CAPTCHA_ERROR', 		 	 'Incorrect spam protection code');

    //LISTS
    define('LANG_NO_ITEMS', 		 		 'There are nothing to show');

    //ACTIONS
    define('LANG_ADD_CATEGORY',              'Add category');
    define('LANG_ADD_CATEGORY_QUICK',        'or create a new category inside selected');
    define('LANG_EDIT_CATEGORY',             'Edit category');
    define('LANG_DELETE_CATEGORY',           'Delete category');
    define('LANG_DELETE_CATEGORY_CONFIRM',   'Are you sure you want to delete the category?\nThe content will also be deleted');
    define('LANG_ADD_FOLDER_QUICK',          'or create a new folder');
    define('LANG_EDIT_FOLDER',               'Edit folder');
    define('LANG_DELETE_FOLDER',             'Delete folder');
    define('LANG_DELETE_FOLDER_CONFIRM',     'Are you sure you want to delete the folder?\nThe content will also be deleted');

    define('LANG_BASIC_OPTIONS',             'Basic');
    define('LANG_YES',                       'Yes');
    define('LANG_NO',                        'No');
    define('LANG_LIST_LIMIT',                'Limit');
    define('LANG_LIST_ALL',					 'Show all');
    define('LANG_LIST_EMPTY',                'There are nothing to show');
    define('LANG_LIST_NONE_SELECTED',        'Nothing selected');
    define('LANG_DOWNLOAD',                  'Download');
    define('LANG_UPLOAD',                    'Upload');
    define('LANG_SELECT_UPLOAD',             'Select and upload');
    define('LANG_DROP_TO_UPLOAD',            'Drag and drop files here to upload');
    define('LANG_CREATE',                    'Create');
    define('LANG_APPLY',                     'Apply');
    define('LANG_ACCEPT',                    'Accept');
    define('LANG_DECLINE',                   'Decline');
    define('LANG_CONFIRM',                   'Confirm');
    define('LANG_INVITE',                    'Invite');
    define('LANG_ADD', 						 'Add');
    define('LANG_ADD_CONTENT',				 'Add page');
    define('LANG_ADD_USER',                  'Add user');
    define('LANG_ADD_NEWS',					 'Add news');
    define('LANG_ADD_MENU',				 	 'Add menu');
    define('LANG_ADD_MENUITEM',				 'Add menu item');
    define('LANG_MENU_MORE',                 'More');
    define('LANG_VIEW', 					 'View');
    define('LANG_EDIT', 					 'Edit');
    define('LANG_EDIT_SELECTED',			 'Edit selected');
    define('LANG_SHOW', 					 'Show');
    define('LANG_SHOW_ALL',                  'Show all');
    define('LANG_SHOW_SELECTED',			 'Show selected');
    define('LANG_HIDE', 					 'Hide');
    define('LANG_HIDE_SELECTED',			 'Hide selected');
    define('LANG_CONFIG', 					 'Settings');
    define('LANG_DELETE', 					 'Delete');
    define('LANG_DELETE_SELECTED',			 'Delete selected');
    define('LANG_DELETE_SELECTED_CONFIRM',   'Delete selected items?');
    define('LANG_MOVE',                      'Move');
    define('LANG_MOVE_TO_CATEGORY',          'Move to category');
    define('LANG_ON',	 					 'On');
    define('LANG_OFF', 						 'Off');
    define('LANG_SAVE',						 'Save');
    define('LANG_SAVE_CHANGES',              'Save changes');
    define('LANG_SAVE_ORDER',                'Save order');
    define('LANG_SAVING',                    'Saving...');
    define('LANG_PREVIEW',                   'Preview');
    define('LANG_SEND',						 'Send');
    define('LANG_INSTALL',					 'Install');
    define('LANG_INSERT',					 'Insert');
    define('LANG_CANCEL',					 'Cancel');
    define('LANG_BACK',					 	 'Back');
    define('LANG_IN_QUEUE',					 'Items in queue');
    define('LANG_SELECT',                    'Select');
    define('LANG_SELECT_ALL',				 'Select all');
    define('LANG_DESELECT_ALL',				 'Deselect all');
    define('LANG_INVERT_ALL',				 'Invert');
    define('LANG_CLOSE',                     'Close');
    define('LANG_CONTINUE',                  'Continue');
    define('LANG_OPTIONS',                   'Options');
    define('LANG_REPLY',                     'Reply');
    define('LANG_REPLY_SPELLCOUNT',          'reply|replies|replies');
    define('LANG_FROM',                      'from');
    define('LANG_TO',                        'to');
    define('LANG_IS_ENABLED',                'Enabled');
    define('LANG_HELP',                      'Help');
    define('LANG_HELP_URL',                  'http://docs.instantcms.ru/en');

    //NAVIGATION
    define('LANG_HOME',                      'Home');
    define('LANG_BACK_TO_HOME',              'Back to homepage');
    define('LANG_PAGE_NEXT', 				 'Next');
    define('LANG_PAGE_PREV',                 'Previous');
    define('LANG_PAGE_FIRST', 				 'First');
    define('LANG_PAGE_LAST',                 'Last');
    define('LANG_PAGES', 					 'Pages');
    define('LANG_PAGE', 					 'Page');
    define('LANG_PAGE_ADD',                  'Add page');
    define('LANG_PAGE_DELETE',               'Delete page');
    define('LANG_PAGE_CURRENT_DELETE',       'Delete current page');
    define('LANG_PAGES_SHOWN',               'Showing %d-%d from %d');
    define('LANG_PAGES_SHOW_PERPAGE',        'Display');

    //FORMS
    define('LANG_SUBMIT', 					 'Submit');

    //LAYOUT
    define('LANG_PAGE_BODY',                 'Content');
    define('LANG_PAGE_MENU',                 'Navigation');
    define('LANG_PAGE_HEADER',               'Header');
    define('LANG_PAGE_FOOTER',               'Footer');
    define('LANG_PAGE_LOGO',                 'Site Logo');
    define('LANG_MENU',                      'Menu');
    define('LANG_TITLE', 					 'Title');
    define('LANG_SHOW_TITLE',                'Show title');
    define('LANG_SYSTEM_NAME',               'System name');
    define('LANG_SYSTEM_EDIT_NOTICE',	     '<b>Warning:</b> if you change the system name field will be re-created and all current data will be lost!');
    define('LANG_DESCRIPTION',               'Description');
    define('LANG_INFORMATION',               'Information');
    define('LANG_CONTENT', 					 'Content');
    define('LANG_CATEGORY',                  'Category');
    define('LANG_CATEGORY_TITLE',            'Category title');
    define('LANG_FOLDER',                    'Folder');
    define('LANG_ROOT_NODE',                 'Root node');
    define('LANG_ROOT_CATEGORY',             'Root category');
    define('LANG_PARENT_CATEGORY',           'Parent category');
    define('LANG_ADDITIONAL_CATEGORIES',	 'Additional categories');
    define('LANG_MESSAGE', 					 'Message text');
    define('LANG_DATE', 					 'Date');
    define('LANG_DATE_PUB',                  'Date of publication');
    define('LANG_PUBLICATION',				 'Publication');
    define('LANG_SLUG',                      'URL');
    define('LANG_PRIVACY',                   'Privacy');
    define('LANG_PRIVACY_PUBLIC',            'For everyone');
    define('LANG_PRIVACY_PRIVATE',           'Only for friends');
    define('LANG_PRIVACY_PRIVATE_HINT',      'This is a private item. Only author friends view it.');
    define('LANG_ON_FRONT',				 	 'On the home page');
    define('LANG_SHOWED',					 'Displaying');
    define('LANG_ORDER',					 'Order');
    define('LANG_ORDER_DOWN',				 'Move down');
    define('LANG_ORDER_UP',					 'Move up');
    define('LANG_HITS',                      'Hits');

    //SEO
    define('LANG_SEO',                       'SEO');
    define('LANG_SEO_TITLE',                 'Page title');
    define('LANG_SEO_KEYS',                  'Keywords');
    define('LANG_SEO_KEYS_HINT',             'Page keywords, separated by commas');
    define('LANG_SEO_DESC',                  'Description');
    define('LANG_SEO_DESC_HINT',             'Brief description of the page for search engines');
    define('LANG_TAGS',                      'Tags');
    define('LANG_TAGS_HINT',                 'Keywords, separated by commas');

    //FILES
    define('LANG_B',               'byte');
    define('LANG_KB',              'Kb');
    define('LANG_MB',              'Mb');
    define('LANG_GB',              'Gb');
    define('LANG_TB',              'Tb');
    define('LANG_PB',              'Pb');

    //UNITS
    define('LANG_UNIT1',                     'unit');
    define('LANG_UNIT2',                     'units');
    define('LANG_UNIT10',                    'units');

    //DATES
    define('LANG_ALL',                       'All');
    define('LANG_JUST_NOW',                  'Just now');
    define('LANG_SECONDS_AGO',               'Less than minute ago');
    define('LANG_YESTERDAY',                 'Yesterday');
    define('LANG_TODAY',                     'Today');
    define('LANG_TOMORROW',                  'Tomorrow');
    define('LANG_WEEK',                      'Week');
    define('LANG_WEEK1',                     'week');
    define('LANG_WEEK2',                     'weeks');
    define('LANG_WEEK10',                    'weeks');
    define('LANG_THIS_WEEK',                 'This week');
    define('LANG_THIS_MONTH',                'This month');
    define('LANG_EVENTS_THIS_WEEK',          'Events this week');
    define('LANG_CALENDAR',                  'Calendar');
    define('LANG_TIME_ZONE',                 'Time zone');
    define('LANG_YEAR',                      'Year');
    define('LANG_YEARS',                     'Years');
    define('LANG_YEAR1',                     'year');
    define('LANG_YEAR2',                     'years');
    define('LANG_YEAR10',                    'years');
    define('LANG_MONTHS',                    'Months');
    define('LANG_MONTH',				     'Month');
    define('LANG_MONTH1',                    'month');
    define('LANG_MONTH2',                    'months');
    define('LANG_MONTH10',                   'months');
    define('LANG_DAYS',                      'Days');
    define('LANG_DAY1',                      'day');
    define('LANG_DAY2',                      'days');
    define('LANG_DAY10',                     'days');
    define('LANG_HOURS',                     'Hours');
    define('LANG_HOUR1',                     'hour');
    define('LANG_HOUR2',                     'hours');
    define('LANG_HOUR10',                    'hours');
    define('LANG_MINUTES',                   'Minutes');
    define('LANG_MINUTE1',                   'minute');
    define('LANG_MINUTE2',                   'minutes');
    define('LANG_MINUTE10',                  'minutes');
    define('LANG_SECONDS',                   'Seconds');
    define('LANG_SECOND1',                   'second');
    define('LANG_SECOND2',                   'seconds');
    define('LANG_SECOND10',                  'seconds');
    define('LANG_DATE_AGO',                  '%s ago');

    //MAIL
    define('LANG_MAIL_DEFAULT_ALT',          'To view a message, you need an email client with HTML support');

    define('LANG_POWERED_BY_INSTANTCMS',     'Powered by <a href="http://instantcms.ru/">InstantCMS</a>');
    define('LANG_ICONS_BY_FATCOW',           'Icons by <a href="http://www.fatcow.com/free-icons">FatCow</a>');
    define('LANG_DEBUG_QUERY_TIME',          'Query time');