<?php

    define('LANG_USERS_CONTROLLER',         'Members profiles');

    define('LANG_USERS_LIST',               'Members list');
    define('LANG_USERS_PROFILE',            'Profile');
    define('LANG_USERS_SOCIALITY',          'Sociality');
    define('LANG_USERS_EDIT_PROFILE',       'Edit profile');
    define('LANG_USERS_EDIT_USER',          'Edit user');

    define('LANG_USERS_EDIT_PROFILE_MAIN',  'Info');
    define('LANG_USERS_EDIT_PROFILE_THEME', 'Theme');
    define('LANG_USERS_EDIT_PROFILE_NOTICES', 'Notifications');
    define('LANG_USERS_EDIT_PROFILE_PRIVACY', 'Privacy');

    define('LANG_USERS_CFG_FIELDS',         'Fields');
    define('LANG_USERS_CFG_TABS',           'Tabs');
    define('LANG_USERS_CFG_OPTIONS',        'Options');
    define('LANG_USERS_CFG_MIGRATION',      'Migrations');

    define('LANG_USERS_OPT_FRIENDSHIP',     'Enable friends');
    define('LANG_USERS_OPT_THEME',          'Enable profile theming');
    define('LANG_USERS_OPT_THEME_HINT',     'Must be supported by site theme');
    define('LANG_USERS_OPT_MAX_TABS',       'Max number of tabs');
    define('LANG_USERS_OPT_MAX_TABS_HINT',  'The other tabs will be hidden under the «More...»<br>0 — unlimited');
    define('LANG_USERS_OPT_AUTH_ONLY',      'Show profiles only to logged users');
    define('LANG_USERS_OPT_WALL_ENABLED',   'Enable profiles walls');
    define('LANG_USERS_OPT_STATUSES_ENABLED',   'Enable profiles statuses');
    define('LANG_USERS_OPT_KARMA_COMMENTS', 'Ask for clarification when user changes someone else\'s reputation');
    define('LANG_USERS_OPT_KARMA_TIME',     'The voting period for the reputation, days');
    define('LANG_USERS_OPT_KARMA_TIME_HINT','The user can change the reputation of another person only once during the period');

    define('LANG_USERS_MIG_TITLE',              'Rule title');
    define('LANG_USERS_MIG_IS_ACTIVE',          'Rule is active');
    define('LANG_USERS_MIG_ADD',                'Add migration rule');
    define('LANG_USERS_MIG_DELETE_CONFIRM',     'Delete rule "{title}"?');
    define('LANG_USERS_MIG_ACTION',             'Action type');
    define('LANG_USERS_MIG_ACTION_CHANGE',      'Change group');
    define('LANG_USERS_MIG_ACTION_ADD',         'Add to group');
    define('LANG_USERS_MIG_FROM',               'From');
    define('LANG_USERS_MIG_TO',                 'To');
    define('LANG_USERS_MIG_COND_DATE',          'Date conditions');
    define('LANG_USERS_MIG_PASSED_DAYS',        'Days');
    define('LANG_USERS_MIG_PASSED',             'Passed more than, days');
    define('LANG_USERS_MIG_PASSED_FROM',        'From');
    define('LANG_USERS_MIG_PASSED_REG',         'registration');
    define('LANG_USERS_MIG_PASSED_MIG',         'last migration');
    define('LANG_USERS_MIG_COND_RATING',        'Rating conditions');
    define('LANG_USERS_MIG_COND_KARMA',         'Reputation conditions');
    define('LANG_USERS_MIG_RATING',             'Rating higher than');
    define('LANG_USERS_MIG_KARMA',              'Reputation higher than');
    define('LANG_USERS_MIG_NOTIFY',             'Send notification to user after migration');
    define('LANG_USERS_MIG_NOTIFY_TEXT',        'Notification message');

    define('LANG_USERS_KARMA_COMMENT',      'Please explain your vote');

    define('LANG_USERS_OPT_DS_SHOW',        'Show tab "%s"');
    define('LANG_USERS_DS_LATEST',          'New');
    define('LANG_USERS_DS_POPULAR',         'Popular');
    define('LANG_USERS_DS_ONLINE',          'Online');
    define('LANG_USERS_DS_RATED',           'Rating');
    define('LANG_USERS_DS_DATE_LOG',        'Date of last visit');

    define('LANG_USERS_OPT_FILTER_SHOW',    'Show filter');

    define('LANG_USERS_FIELD_PRIVATE',      'Show field only to profile owner');

    define('LANG_USERS_PROFILE_INDEX',      'Profile');
    define('LANG_USERS_PROFILE_CONTENT',    'Content');
    define('LANG_USERS_PROFILE_FRIENDS',    'Friends');
    define('LANG_USERS_PROFILE_ACTIVITY',   'Activity');
    define('LANG_USERS_PROFILE_FEED',       'Activity');

    define('LANG_USERS_PROFILE_WALL',       'Profile Wall');

    define('LANG_USERS_PROFILE_REGDATE',    'Registered');
    define('LANG_USERS_PROFILE_LOGDATE',    'Last visit');
    define('LANG_USERS_PROFILE_INVITED_BY', 'Invited by');
    define('LANG_USERS_PROFILE_LAST_IP',    'Last IP');

    define('LANG_USERS_PROFILE_IS_HIDDEN',  'Personal information is hidden by privacy settings');

    define('LANG_USERS_FRIENDS',            'Friends');
    define('LANG_USERS_FRIENDS_ADD',        'Add to friends');
    define('LANG_USERS_FRIENDS_DELETE',     'Remove from friends');
    define('LANG_USERS_FRIENDS_CONFIRM',    'Send friendship request to %s?');
    define('LANG_USERS_FRIENDS_DELETE_CONFIRM', 'Remove <b>%s</b> from your friends list?');
    define('LANG_USERS_FRIENDS_SENT',       'Friendship request sent');
    define('LANG_USERS_FRIENDS_DELETED',    '%s removed from your friends list');
    define('LANG_USERS_FRIENDS_DECLINED',   '%s declined your friendship request');
    define('LANG_USERS_FRIENDS_NOTICE',     '%s invites you to become friends');
    define('LANG_USERS_FRIENDS_DONE',       'You and %s are friends now');
    define('LANG_USERS_FRIENDS_UNDONE',     '%s stopped friendship with you');

    define('LANG_USERS_NOTIFY_VIA_NONE',    'No');
    define('LANG_USERS_NOTIFY_VIA_EMAIL',   'By e-mail');
    define('LANG_USERS_NOTIFY_VIA_PM',      'By private message');
    define('LANG_USERS_NOTIFY_VIA_BOTH',    'By e-mail and private message');

    define('LANG_USERS_PRIVACY_FOR_ANYONE',  'Anyone');
    define('LANG_USERS_PRIVACY_FOR_FRIENDS', 'Only friends');
    define('LANG_USERS_PRIVACY_FOR_NOBODY',  'Nobody');

    define('LANG_USERS_NOTIFY_FRIEND_ADD',     'Notify about friendship requests');
    define('LANG_USERS_NOTIFY_FRIEND_ACCEPT',  'Notify about accepted friendship requests');
    define('LANG_USERS_NOTIFY_FRIEND_DELETE',  'Notify about canceled friendships');

    define('LANG_USERS_PRIVACY_PROFILE_VIEW',  'Who can view your profile?');
    define('LANG_USERS_PRIVACY_PROFILE_WALL',  'Who can write on your wall?');

    define('LANG_USERS_FRIENDS_SPELLCOUNT', 'friend|friends|friends');

    define('LANG_USERS_ACTIVITY_FRIENDS',   'and %s become friends');

    define('LANG_USERS_LOCKED_NOTICE',          'You profile is blocked.');
    define('LANG_USERS_LOCKED_NOTICE_PUBLIC',   'Blocked');
    define('LANG_USERS_LOCKED_NOTICE_UNTIL',    'Lock expires: %s');
    define('LANG_USERS_LOCKED_NOTICE_REASON',   'The reason for blocking: %s');

    define('LANG_USERS_WHAT_HAPPENED',   'What\'s happened, %s?');
    define('LANG_USERS_DELETE_STATUS_CONFIRM',   'Delete current status?');

    define('LANG_RULE_USERS_VOTE_KARMA',   'Vote for reputation');

    define('LANG_USERS_KARMA_LOG',          'Reputation Log');
	define('LANG_USERS_KARMA_LOG_EMPTY',    'Nobody voted for this user yet');

    define('LANG_USERS_MY_INVITES',         'My invitations');
    define('LANG_USERS_INVITES_COUNT',      'You can send %s');
    define('LANG_USERS_INVITES_SPELLCOUNT', 'invitation|invitations|invitations');
    define('LANG_USERS_INVITES_EMAIL',      'E-mail to send an invitation');
    define('LANG_USERS_INVITES_EMAILS',     'E-mails to send invitations');
    define('LANG_USERS_INVITES_EMAILS_HINT','One address in a row');
    define('LANG_USERS_INVITES_SENT_TO',    'Invitations was successfully sent to');
    define('LANG_USERS_INVITES_FAILED_TO',  'Failed to sent invitations to');