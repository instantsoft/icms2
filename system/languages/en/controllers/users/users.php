<?php

    define('LANG_USERS_CONTROLLER',         'User Profiles');

    define('LANG_USERS_LIST',               'User List');
    define('LANG_USERS_PROFILE',            'User Profile');
    define('LANG_USERS_RESTRICTIONS',       'Restrictions');
    define('LANG_USERS_SOCIALITY',          'Sociality');
    define('LANG_USERS_EDIT_PROFILE',       'Edit Profile');
    define('LANG_USERS_EDIT_USER',          'Edit User');

    define('LANG_RULE_USERS_CHANGE_SLUG',   'Change profile page URL');
    define('LANG_USERS_RESTORE_PROFILE',    'Restore profile');
    define('LANG_USERS_DELETE_PROFILE',     'Delete profile');
    define('LANG_RULE_USERS_DELETE',        'Delete profile');
    define('LANG_RULE_USERS_BIND_TO_PARENT', 'Bind content type items');
    define('LANG_RULE_USERS_BIND_OFF_PARENT', 'Unbind content type items');
    define('LANG_RULE_USERS_BAN',           'Blocking users');
    define('LANG_PERM_OPTION_MY',           'Only own');
    define('LANG_PERM_OPTION_ANYUSER',      'Any');
    define('LANG_USERS_DELETE_CONFIRM',     'Delete the %s\'profile?');
    define('LANG_USERS_DELETE_SUCCESS',     'Profile successfully deleted');
    define('LANG_USERS_RESTORE_SUCCESS',    'Profile successfully restored');
    define('LANG_USERS_DELETE_ADMIN_ERROR', 'You can not delete administrator profiles');
    define('LANG_USERS_IS_DELETED',         'Profile is deleted');

    define('LANG_USERS_EDIT_PROFILE_MAIN',  'Info');
    define('LANG_USERS_EDIT_PROFILE_THEME', 'Theme');
    define('LANG_USERS_EDIT_PROFILE_NOTICES', 'Notifications');
    define('LANG_USERS_EDIT_PROFILE_PRIVACY', 'Privacy');

    define('LANG_USERS_CFG_FIELDS',         'Profile Fields');
    define('LANG_USERS_CFG_TABS',           'Profile Tabs');
    define('LANG_USERS_CFG_OPTIONS',        'Options');
    define('LANG_USERS_CFG_MIGRATION',      'Migration');

    define('LANG_USERS_OPT_FRIENDSHIP',     'Allow to add friends');
    define('LANG_USERS_OPT_THEME',          'Custom profile design');
    define('LANG_USERS_OPT_THEME_HINT',     'only if the theme supports this function');
    define('LANG_USERS_OPT_MAX_TABS',       'Maximum number of tabs');
    define('LANG_USERS_OPT_MAX_TABS_HINT',  'Other tabs will be hidden under the «More...» item<br>0 — unlimited number');
    define('LANG_USERS_OPT_AUTH_ONLY',      'Allow to view profiles only to authorized users');
    define('LANG_USERS_OPT_SHOW_USER_GROUPS', 'Show groups the user belongs to');
    define('LANG_USERS_OPT_SHOW_REG_DATA',    'Show user registration date');
    define('LANG_USERS_OPT_SHOW_LAST_VISIT',  'Show the user\'s last session');
    define('LANG_USERS_OPT_RESTRICTED_SLUGS', 'Restricted profile adresses');
    define('LANG_USERS_OPT_RESTRICTED_SLUGS_HINT', 'One address per line, you can use * as a wildcard');
    define('LANG_USERS_OPT_RESTRICTED_SLUG', 'Profile adress <b>%s</b> is forbidden');
    define('LANG_RULE_USERS_CHANGE_EMAIL',   'Allow email change');
    define('LANG_RULE_USERS_CHANGE_EMAIL_PERIOD', 'Email Change Period, days');
    define('LANG_RULE_USERS_CHANGE_EMAIL_PERIOD_HINT', 'Not specified, can always be changed');
    define('LANG_USERS_EMAIL_VERIFY', 'An email has been sent to <b>%s</b>. Follow the link from the letter to activate the mail change');
    define('LANG_USERS_OPT_WALL_ENABLED',   'Enable profile wall');
    define('LANG_USERS_OPT_STATUSES_ENABLED',   'Enable profile status');
    define('LANG_USERS_OPT_KARMA_ENABLED',   'Enable a reputation assessment');
    define('LANG_USERS_OPT_KARMA_COMMENTS', 'Explain reputation vote');
    define('LANG_USERS_OPT_KARMA_TIME',     'Reputation voting period, days');
    define('LANG_USERS_OPT_KARMA_TIME_HINT','A user will be able to vote for another user\'s reputation only once in a specified period');
    define('LANG_USERS_OPT_MAX_FRIENDS_COUNT', 'Maximum number of friends on the main profile page');

    define('LANG_USERS_MIG_TITLE',              'Rule title');
    define('LANG_USERS_MIG_IS_ACTIVE',          'Rule is active');
    define('LANG_USERS_MIG_ADD',                'New Migration Rule');
    define('LANG_USERS_MIG_DELETE_CONFIRM',     'Delete the "{title}" rule?');
    define('LANG_USERS_MIG_ACTION',             'Action type');
    define('LANG_USERS_MIG_ACTION_CHANGE',      'Change Group');
    define('LANG_USERS_MIG_ACTION_ADD',         'Add to group');
    define('LANG_USERS_MIG_FROM',               'Initial group');
    define('LANG_USERS_MIG_TO',                 'Target group');
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
    define('LANG_USERS_MIG_NOTIFY',             'Send migration notification');
    define('LANG_USERS_MIG_NOTIFY_TEXT',        'Notification text');

    define('LANG_USERS_KARMA_COMMENT',      'Please explain your vote');

    define('LANG_USERS_OPT_DS_SHOW',        'Show the "%s" tab');
    define('LANG_USERS_DS_LATEST',          'New');
    define('LANG_USERS_DS_POPULAR',         'Popular');
    define('LANG_USERS_DS_SUBSCRIBERS',     'Popular by subscribers');
    define('LANG_USERS_DS_ONLINE',          'Online');
    define('LANG_USERS_DS_RATED',           'Rating');
    define('LANG_USERS_DS_DATE_LOG',        'Last visit');
    define('LANG_USERS_OPT_LIST_ALLOWED',   'Users list is available for');

    define('LANG_USERS_OPT_FILTER_SHOW',    'Show filter');

    define('LANG_USERS_FIELD_PRIVATE',      'Show field only to the profile owner');

    define('LANG_USERS_PROFILE_INDEX',      'Profile');
    define('LANG_USERS_PROFILE_CONTENT',    'Content');
    define('LANG_USERS_PROFILE_FRIENDS',    'Friends');
    define('LANG_USERS_PROFILE_ACTIVITY',   'Activity');
    define('LANG_USERS_PROFILE_FEED',       'Feed');

    define('LANG_USERS_PROFILE_WALL',       'User Wall');

    define('LANG_USERS_PROFILE_REGDATE',    'Registered');
    define('LANG_USERS_PROFILE_INVITED_BY', 'Invited by');

    define('LANG_USERS_PROFILE_IS_HIDDEN',  'Private information is hidden in privacy settings');
    define('LANG_USERS_CONTENT_IS_HIDDEN',  '%s does not allow you to view a list of his %s');

    define('LANG_USERS_FRIENDS',            'Friends');
    define('LANG_USERS_FRIENDS_ADD',        'Add to Friends');
    define('LANG_USERS_FRIENDS_DELETE',     'Remove from Friends');
    define('LANG_USERS_KEEP_IN_SUBSCRIBERS', 'Keep in subscribers');
    define('LANG_USERS_FRIENDS_CONFIRM',    'Send friendship request to user %s?');
    define('LANG_USERS_SUBSCRIBE_CONFIRM',    'Subscribe to %s\'s news?');
    define('LANG_USERS_UNSUBSCRIBE_CONFIRM',    'Unsubscribe from user %s?');
    define('LANG_USERS_FRIENDS_DELETE_CONFIRM', 'Delete user <b>%s</b> from your friend list?');
    define('LANG_USERS_FRIENDS_SUBSCRIBE_CONFIRM', 'Delete user <b>%s</b> from your friend list, keeping in subscribers?');
    define('LANG_USERS_FRIENDS_SENT',       'Friendship request was sent');
    define('LANG_USERS_SUBSCRIBE_SUCCESS',  'You have successfully subscribed');
    define('LANG_USERS_UNSUBSCRIBE_SUCCESS',  'You have successfully unsubscribed');
    define('LANG_USERS_FRIENDS_DELETED',    '%s was removed from your friend list');
    define('LANG_USERS_FRIENDS_DECLINED',   '%s declined your friendship request');
    define('LANG_USERS_KEEP_IN_SUBSCRIBERS_NOTICE',   '%s keep you in subscribers');
    define('LANG_USERS_FRIENDS_NOTICE',     '%s invites you to become friends');
    define('LANG_USERS_FRIENDS_DONE',       '%s became your friend');
    define('LANG_USERS_SUBSCRIBE_DONE',     '%s subscribed to you');
    define('LANG_USERS_UNSUBSCRIBE_DONE',   '%s unsubscribed from you');
    define('LANG_USERS_FRIENDS_UNDONE',     '%s stopped being your friend');

    define('LANG_USERS_NOTIFY_VIA_NONE',    'Do not notify');
    define('LANG_USERS_NOTIFY_VIA_EMAIL',   'By e-mail');
    define('LANG_USERS_NOTIFY_VIA_PM',      'By PM');
    define('LANG_USERS_NOTIFY_VIA_BOTH',    'By e-mail and PM');

    define('LANG_USERS_PRIVACY_FOR_ANYONE',  'Anyone');
    define('LANG_USERS_PRIVACY_FOR_FRIENDS', 'Only friends');
    define('LANG_USERS_PRIVACY_FOR_NOBODY',  'Nobody');

    define('LANG_USERS_NOTIFY_FRIEND_ADD',     'Notify on friendship requests');
    define('LANG_USERS_NOTIFY_FRIEND_ACCEPT',  'Notify on friendship request acceptance');
    define('LANG_USERS_NOTIFY_FRIEND_DELETE',  'Notify on cancelled friendship');

    define('LANG_USERS_PRIVACY_FRIENDSHIP',    'Who can send you requests for friendship?');
    define('LANG_USERS_PRIVACY_SHOW_REG_DATA', 'Who can see your registration date?');
    define('LANG_USERS_PRIVACY_SHOW_LAST_VISIT', 'Who can see your last visit?');
    define('LANG_USERS_PRIVACY_SHOW_USER_GROUPS', 'Who can see your groups?');
    define('LANG_USERS_PRIVACY_PROFILE_VIEW',  'Who can view your profile?');
    define('LANG_USERS_PRIVACY_PROFILE_WALL',  'Who can write on your wall?');
    define('LANG_USERS_PRIVACY_PROFILE_WALL_REPLY', 'Who can comment on my wall posts?');
    define('LANG_USERS_PRIVACY_PROFILE_CTYPE',  'Who can view your %s list?');

    define('LANG_USERS_FRIENDS_SPELLCOUNT', 'friend|friends|friends');

    define('LANG_USERS_ACTIVITY_FRIENDS',   'and %s become friends');

    define('LANG_USERS_LOCK_USER',              'Blocking profile');
    define('LANG_USERS_LOCKED_NOTICE',          'Your profile was blocked.');
    define('LANG_USERS_LOCKED_NOTICE_PUBLIC',   'Blocked');
    define('LANG_USERS_LOCKED_NOTICE_UNTIL',    'Blocking expires: %s');
    define('LANG_USERS_LOCKED_NOTICE_REASON',   'Blocking reason: %s');

    define('LANG_USERS_WHAT_HAPPENED',          'What’s new, %s?');
    define('LANG_USERS_DELETE_STATUS_CONFIRM',   'Delete current status?');

    define('LANG_RULE_USERS_VOTE_KARMA',        'Voting for reputation');
    define('LANG_RULE_USERS_WALL_ADD',         'Adding wall entries');
    define('LANG_RULE_USERS_WALL_DELETE',      'Removing wall entries');

    define('LANG_USERS_KARMA_LOG',          'Reputation history');
    define('LANG_USERS_KARMA_LOG_EMPTY',    'No votes yet');

    define('LANG_USERS_MY_INVITES',         'My invites');
    define('LANG_USERS_INVITES_LINKS',      'Or distribute links for an invitation');
    define('LANG_USERS_INVITES_COUNT',      'You can send %s');
    define('LANG_USERS_INVITES_SPELLCOUNT', 'invite|invites|invites');
    define('LANG_USERS_INVITES_EMAIL',      'E-mail that is used to send an invite');
    define('LANG_USERS_INVITES_EMAILS',     'E-mails that are used to send invites');
    define('LANG_USERS_INVITES_EMAILS_HINT','One e-mail address per line');
    define('LANG_USERS_INVITES_SENT_TO',    'Invites were successfully sent');
    define('LANG_USERS_INVITES_FAILED_TO',  'Failed to send invites');
    define('LANG_USERS_SESSIONS',  'Sessions');
    define('LANG_USERS_SESSIONS_DELETE',  'Session successfully closed');
    define('LANG_SESS_DESKTOP',  'Desktop PC');
    define('LANG_SESS_TABLET',  'The tablet');
    define('LANG_SESS_MOBILE',  'Phone');
    define('LANG_SESS_APP',  'Mobile app');
    define('LANG_SESS_NOT_FOUND',  'There are no active sessions saved.');
    define('LANG_SESS_DROP',  'Drop');
    define('LANG_SESS_DROP_CONFIRM',  'End this session?');
    define('LANG_SESS_IP',  'IP-address');
    define('LANG_SESS_LAST_DATE',  'Last activity');
    define('LANG_SESS_TYPE',  'Access type');
    define('LANG_SESSIONS_HINT',  'It shows the sessions with active access authorization when you set the checkbox "Remember me" or sessions with a mobile application. You can at any time to terminate any of the sessions.');
    define('LANG_USERS_SLUG',  'Your page URL');
