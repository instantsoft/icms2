<?php

define('LANG_REDIRECT_CONTROLLER', 'Редиректы');
define('LANG_REDIRECT_H1', 'Переход по внешней ссылке');
define('LANG_REDIRECT_HINT1', 'Вы покидаете сайт «<a href="%s">%s</a>» по внешней ссылке <a rel="nofollow" href="%s">%s</a>.');
define('LANG_REDIRECT_HINT2', 'Мы не несем ответственности за содержимое сайта <b>%s</b> и настоятельно рекомендуем <b>не указывать</b> никаких своих личных данных на сторонних сайтах.');
define('LANG_REDIRECT_HINT3', 'Если Вы не хотите рисковать безопасностью компьютера, нажмите <a rel="nofollow" href="javascript:window.close()">отмена</a>, иначе вы будете перемещены через');
define('LANG_REDIRECT_HINT4', 'Не предупреждать меня больше');
define('LANG_REDIRECT_YOUR_SAFETY', '«%s» всегда заботится о вашей безопасности.');
define('LANG_REDIRECT_SUSPICIOUS_LINK', 'Ссылка на подозрительный сайт');
define('LANG_REDIRECT_SUSPICIOUS_LINK_1', 'Ссылка, по которой Вы попытались перейти, может вести на сайт, который был создан с целью обмана пользователей и получения за счет этого прибыли. <a rel="nofollow" href="javascript:window.close()">Закрыть вкладку</a>');
define('LANG_REDIRECT_SUSPICIOUS_LINK_2', 'Я понял риск, но все равно хочу <a rel="nofollow" href="%s">перейти на сайт</a>.');

define('LANG_REDIRECT_NO_REDIRECT_LIST', 'Список доменов для прямого редиректа');
define('LANG_REDIRECT_ADMIN_HINT', 'В формате domain.com, каждый с новой строки. ');
define('LANG_REDIRECT_NO_REDIRECT_LIST_HINT', 'Переход по ссылкам будет осуществляться без промежуточной страницы.');
define('LANG_REDIRECT_BLACK_LIST', 'Черный список доменов');
define('LANG_REDIRECT_BLACK_LIST_HINT', 'Переход по ссылкам с указанных доменов будет блокироваться всегда.');
define('LANG_REDIRECT_IS_CHECK_REFER', 'Проверять HTTP referer');
define('LANG_REDIRECT_IS_CHECK_LINK', 'Проверять ссылки');
define('LANG_REDIRECT_IS_CHECK_LINK_HINT', 'Ссылки проверяются посредством <a href="https://vk.com/dev/utils.checkLink" target="_blank" rel="noopener noreferrer">открытого метода Вконтакте</a>');
define('LANG_REDIRECT_WHITE_LIST', 'Белый список доменов');
define('LANG_REDIRECT_WHITE_LIST_HINT', 'Эти домены не будут проверяться.');
define('LANG_REDIRECT_REDIRECT_TIME', 'Таймер перехода для ссылок');
define('LANG_REDIRECT_REWRITE_JSON', 'Правила для редиректа и подмены адресов');
define('LANG_REDIRECT_REWRITE_JSON_HINT', 'В формате JSON. Массив вида:
{
"source": "регулярное выражение, для сравнения с текущим URI",
"target": "URI для перенаправления, при совпадении source",
"action": "действие при совпадении source: rewrite, redirect, redirect-301"
}');
