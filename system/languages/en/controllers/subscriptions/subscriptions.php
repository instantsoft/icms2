<?php

define('LANG_SUBSCRIPTIONS_CONTROLLER', 'Subscriptions');
define('LANG_SBSCR_ISSUBSCRIBE', 'You are subscribed');
define('LANG_SBSCR_GUEST_FORM_TITLE', 'Enter the data for the subscription');
define('LANG_SBSCR_GUEST_EMAIL_CONFIRMATION', 'Request guest confirmation via e-mail');
define('LANG_SBSCR_GUEST_EMAIL_CONFIRM_SEND', 'You received an email to confirm your subscription to E-mail');
define('LANG_SBSCR_GUEST_IS_EXISTS', 'Subscription to this E-mail has already been done before');
define('LANG_SBSCR_NEED_AUTH', 'Subscriptions are only available to registered users');
define('LANG_SBSCR_SHOW_BTN_TITLE', 'Show subscribe/unsubscribe on the subscribe button');
define('LANG_SBSCR_SHOW_BTN_TITLE_HINT', 'If off, one icon will be shown');
define('LANG_SBSCR_VERIFY_EXP', 'Link confirmation lifetime');
define('LANG_SBSCR_VERIFY_SUCCESS', 'E-mail address and subscription confirmed');
define('LANG_SBSCR_ADMIN_EMAIL', 'E-mail addresses for notifications (you can specify several separated by commas) about new lists without names');
define('LANG_SBSCR_QUEUE_NAME', 'mailings');
define('LANG_SBSCR_LIST', 'Subscription Lists');
define('LANG_SBSCR_SUBSCRIBERS', 'Subscribers');
define('LANG_SBSCR_LIMIT', 'Number of subscriptions per page in the tab');
define('LANG_SBSCR_NOTIFY_NEW', 'Notify about new entries in subscriptions');
define('LANG_SBSCR_DELETE_CONFIRM', 'Delete this list?\nAll subscribers will be unsubscribed.');
define('LANG_SBSCR_PM_NOTIFY', 'Update in your subscription list «<a href="%s">%s</a>»<br>%s');
define('LANG_SBSCR_UNSUBSCRIBE_SUCCESS', 'You successfully unsubscribed from the subscription');
define('LANG_SBSCR_CTYPE_ON', 'Enable Subscriptions');
define('LANG_SBSCR_CTYPE_RECURSIVE_CATEGORIES', 'Include nested categories');
define('LANG_SBSCR_CTYPE_SHOW_IN_LIST', 'Show subscribe button in list');
define('LANG_SBSCR_CTYPE_IN_LIST_POS', 'Button display position name');
define('LANG_SBSCR_CTYPE_IN_LIST_POS_HINT', 'The position should be defined in your template by the construct <b>&lt;?php $this->block(\'before_body\'); ?&gt;</b>, where before_body is the position name');
define('LANG_SBSCR_CTYPE_SHOW_IN_FILTER', 'Show subscribe button in filter');
define('LANG_SBSCR_NOTIFY_TEXT', 'Notification template on the site');
define('LANG_SBSCR_NOTIFY_TEXT_HINT', 'If not specified, generic will be used. Example text: <b class="text-danger">'. htmlspecialchars(LANG_SBSCR_PM_NOTIFY).'</b>. %s will be replaced by notification values.');
define('LANG_SBSCR_LETTER_TPL', 'Email notification template');
define('LANG_SBSCR_LETTER_TPL_HINT', 'If not specified, generic will be used. Example of template text system/languages/en/letters/subscribe_new_item.txt');
define('LANG_SBSCR_LIST_TITLE', 'Subscription list title');
define('LANG_SBSCR_LIST_URL', 'Subscription list URL');
define('LANG_SBSCR_UNSUBSCRIBE_URL', 'Unsubscribe URL');
define('LANG_SBSCR_SUBJECTS_URLS', 'List of links to new subscription entries');
