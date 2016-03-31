<?php

    define('LANG_AUTH_CONTROLLER',          'Authorization & registration');

    define('LANG_AUTHORIZATION',            'Authorization');

    define('LANG_AUTH_RESTRICTIONS',            'Restrictions');
    define('LANG_AUTH_RESTRICTED_EMAILS',       "Restricted e-mails");
    define('LANG_AUTH_RESTRICTED_EMAILS_HINT',  'One address in a row. You can use * as a wildcard');
    define('LANG_AUTH_RESTRICTED_EMAIL',        'E-mail <b>%s</b> is forbidden');

    define('LANG_AUTH_RESTRICTED_NAMES',        'Restricted nicknames');
    define('LANG_AUTH_RESTRICTED_NAMES_HINT',   'One nickname in a row. You can use * as a wildcard');
    define('LANG_AUTH_RESTRICTED_NAME',         'Nickname <b>%s</b> is forbidden');

    define('LANG_AUTH_RESTRICTED_IPS',          'Restricted IP\'s for registration');
    define('LANG_AUTH_RESTRICTED_IPS_HINT',     'One address in a row. You can use * as a wildcard');
    define('LANG_AUTH_RESTRICTED_IP',           'Registration from IP <b>%s</b> is forbidden');

    define('LANG_AUTH_INVITES',                 'Invitations');
    define('LANG_AUTH_INVITES_AUTO',            'Issue invitations to registered users');
    define('LANG_AUTH_INVITES_AUTO_HINT',       'Users will be able to send invitations to their friends');
    define('LANG_AUTH_INVITES_STRICT',          'Bind invitations to e-mail');
    define('LANG_AUTH_INVITES_STRICT_HINT',     'If enabled, it will be possible to register only with the e-mail invitation code was sent to');
    define('LANG_AUTH_INVITES_PERIOD',          'Issue invitations once in the period of');
    define('LANG_AUTH_INVITES_QTY',             'How many invitations to give');
    define('LANG_AUTH_INVITES_KARMA',           'Issue invitations to users with reputation higher than');
    define('LANG_AUTH_INVITES_RATING',          'Issue invitations to users with rating higher than');
    define('LANG_AUTH_INVITES_DATE',            'Issue invitations to users registered on the site of at least');

    define('LANG_REG_INVITED_ONLY',             'Registration is allowed by invitation only');
    define('LANG_REG_INVITE_CODE',              'Invitation code');
    define('LANG_REG_WRONG_INVITE_CODE',        'Wrong invitation code');
    define('LANG_REG_WRONG_INVITE_CODE_EMAIL',  'Invitation code assigned to another e-mail');

    define('LANG_REG_CFG_IS_ENABLED',           'Registration is enabled');
    define('LANG_REG_CFG_DISABLED_NOTICE',      'Reason why registration is disabled');
    define('LANG_REG_CFG_IS_INVITES',           'Registration is by invitation only');

    define('LANG_REG_CFG_REG_CAPTCHA',          'Show CAPTCHA to protect from spam registrations');
    define('LANG_REG_CFG_AUTH_CAPTCHA',         'Show CAPTCHA after failed log in');
    define('LANG_REG_CFG_FIRST_AUTH_REDIRECT',  'After the first login to the site');
    define('LANG_REG_CFG_AUTH_REDIRECT',        'After following authorizations on the website');
    define('LANG_REG_CFG_AUTH_REDIRECT_NONE',        'Stay on page, at which the logged');
    define('LANG_REG_CFG_AUTH_REDIRECT_INDEX',       'Open main page');
    define('LANG_REG_CFG_AUTH_REDIRECT_PROFILE',     'Open profile');
    define('LANG_REG_CFG_AUTH_REDIRECT_PROFILEEDIT', 'Open profile settings');

    define('LANG_REG_CFG_VERIFY_EMAIL',         'Send verification e-mail after registration');
    define('LANG_REG_CFG_VERIFY_EMAIL_HINT',    'New users will be blocked until they open URL from verification e-mail');
	define('LANG_REG_CFG_REG_AUTO_AUTH',        'Authorize user after registration');
    define('LANG_REG_CFG_VERIFY_EXPIRATION',   'Delete unverified accounts after, hours');
    define('LANG_REG_CFG_VERIFY_LOCK_REASON',  'E-mail verification required');
	define('LANG_REG_CFG_DEF_GROUP_ID',		   'Put new users in groups');

    define('LANG_REG_INCORRECT_EMAIL',       'Invalid e-mail address');
    define('LANG_REG_EMAIL_EXISTS',          'The email address is already registered');
    define('LANG_REG_PASS_NOT_EQUAL',        'Passwords do not match');
    define('LANG_REG_PASS_EMPTY',            'You must specify a password');
    define('LANG_REG_SUCCESS',               'Registration was successful');
    define('LANG_REG_SUCCESS_NEED_VERIFY',   'Verification e-mail sent to <b>%s</b>. Click on the link in the message to activate your account');
    define('LANG_REG_SUCCESS_VERIFIED',      'E-mail address is successfully validated. You can login now.');
	define('LANG_REG_SUCCESS_VERIFIED_AND_AUTH', 'E-mail address is successfully validated. Welcome!');

    define('LANG_PASS_RESTORE',              'Password recovery');
    define('LANG_EMAIL_NOT_FOUND',           'This e-mail was not found in our database');
    define('LANG_TOKEN_SENDED',              'Instructions sent on specified e-mail');
    define('LANG_RESTORE_NOTICE',            'Please enter the E-mail address you entered during registration.<br/>To the specified address will be sent instructions to reset your password.');
