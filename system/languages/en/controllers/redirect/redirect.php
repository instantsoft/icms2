<?php

define('LANG_REDIRECT_CONTROLLER', 'Redirects');
define('LANG_REDIRECT_H1', 'External link disclaimer');
define('LANG_REDIRECT_HINT1', 'You are leaving the website "<a href="%s">%s</a>" via the external link <a rel="nofollow" href="%s">%s</a>.');
define('LANG_REDIRECT_HINT2', 'We are not responsible for the content of the site <b>%s</b> and strongly recommend that you <b>do not specify</b> any personal data on third-party sites.');
define('LANG_REDIRECT_HINT3', 'If you do not want to risk the safety of your computer, click <a rel="nofollow" href="javascript:window.close()">Cancel</a>, otherwise, you will be redirected in');
define('LANG_REDIRECT_HINT4', 'Do not warn me');
define('LANG_REDIRECT_YOUR_SAFETY', 'Your safety is %s\'s primary concern.');
define('LANG_REDIRECT_SUSPICIOUS_LINK', 'Link to a Fraudulent Site');
define('LANG_REDIRECT_SUSPICIOUS_LINK_1', 'The link you are trying to open may lead you to a site that was created for the purpose of deceiving users with the intention of gaining profit. <a rel="nofollow" href="javascript:window.close()">Close Tab</a>');
define('LANG_REDIRECT_SUSPICIOUS_LINK_2', 'I understand the risk, but I still want to <a rel="nofollow" href="%s">Visit the Website</a>.');

define('LANG_REDIRECT_NO_REDIRECT_LIST', 'List of directly forwarded domains');
define('LANG_REDIRECT_ADMIN_HINT', 'In the domain.com format, each on a new line. ');
define('LANG_REDIRECT_NO_REDIRECT_LIST_HINT', 'The intermediate page will be skipped when clicking on links.');
define('LANG_REDIRECT_BLACK_LIST', 'Domain blacklist');
define('LANG_REDIRECT_BLACK_LIST_HINT', 'Links from these domains will be always blocked.');
define('LANG_REDIRECT_IS_CHECK_REFER', 'Check HTTP referer');
define('LANG_REDIRECT_IS_CHECK_LINK', 'Check links');
define('LANG_REDIRECT_IS_CHECK_LINK_HINT', 'Links are checked through the <a href="https://vk.com/dev/utils.checkLink" target="_blank" rel="noopener noreferrer">Vkontakte Open Method</a>');
define('LANG_REDIRECT_WHITE_LIST', 'Domain whitelist');
define('LANG_REDIRECT_WHITE_LIST_HINT', 'These domains will not be checked.');
define('LANG_REDIRECT_REDIRECT_TIME', 'Link timer');
define('LANG_REDIRECT_REWRITE_JSON', 'Rules for redirect and address switching');
define('LANG_REDIRECT_REWRITE_JSON_HINT', 'In JSON format. An array of the form:
{
"source": "regular expression, to compare with the current URI",
"target": "URI for redirection, if source matches",
"action": "action when the source is matched: rewrite, redirect, redirect-301"
}');
