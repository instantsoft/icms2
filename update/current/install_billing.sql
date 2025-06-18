CREATE TABLE IF NOT EXISTS `{#}billing_actions` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `controller` varchar(32) DEFAULT NULL COMMENT 'Имя контроллера',
  `name` varchar(64) DEFAULT NULL COMMENT 'Название действия',
  `title` varchar(255) DEFAULT NULL COMMENT 'Заголовок',
  `prices` text DEFAULT NULL COMMENT 'YAML с ценами по группам',
  PRIMARY KEY (`id`),
  KEY `controller` (`controller`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Платные действия';

CREATE TABLE IF NOT EXISTS `{#}billing_holds` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `target` varchar(100) DEFAULT NULL COMMENT 'Идентификатор операции',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT 'ID пользователя',
  `amount` decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT 'Сумма',
  `payload` text DEFAULT NULL COMMENT 'JSON с параметрами операции',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`target`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Балансы в ожидании';

CREATE TABLE IF NOT EXISTS `{#}billing_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Тип операции: 1 прибыль, 0 - оплата',
  `action_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID платного действия',
  `date_created` timestamp NULL DEFAULT current_timestamp() COMMENT 'Дата создания операции',
  `date_done` timestamp NULL DEFAULT NULL COMMENT 'Дата выполнения операции',
  `amount` decimal(11,2) DEFAULT NULL COMMENT 'Сумма во внутренней валюте',
  `summ` decimal(11,2) DEFAULT NULL COMMENT 'Сумма в реальной валюте',
  `user_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID пользователя',
  `sender_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID пользователя, который отправил перевод',
  `status` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Статус операции: 0 - создана, 1 - выполнена',
  `description` varchar(512) DEFAULT NULL COMMENT 'Описание операции',
  `url` varchar(255) DEFAULT NULL COMMENT 'URL связанной покупки',
  `ref_link_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID записи реферала из таблицы billing_refs',
  `plan_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID тарифного плана',
  `plan_period` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Период действия тарифного плана',
  `system_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID платёжной системы',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `action_id` (`action_id`),
  KEY `date_created` (`date_created`),
  KEY `user_id` (`user_id`),
  KEY `sender_id` (`sender_id`),
  KEY `status` (`status`),
  KEY `ref_link_id` (`ref_link_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Список всех операций';

CREATE TABLE IF NOT EXISTS `{#}billing_outs` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `date_created` timestamp NULL DEFAULT current_timestamp() COMMENT 'Дата создания',
  `date_done` timestamp NULL DEFAULT NULL COMMENT 'Дата выполнения',
  `user_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID пользователя, создавшего заявку',
  `amount` decimal(11,2) UNSIGNED DEFAULT NULL COMMENT 'Сумма во внутренней валюте',
  `summ` decimal(11,2) UNSIGNED DEFAULT NULL COMMENT 'Сумма в реальной валюте',
  `system` varchar(64) DEFAULT NULL COMMENT 'Куда выводить деньги',
  `purse` varchar(32) DEFAULT NULL COMMENT 'Кошелёк/номер счёта',
  `status` tinyint(1) UNSIGNED DEFAULT 0 COMMENT 'Статус заявки',
  `code` varchar(32) DEFAULT NULL COMMENT 'Код подтверждения заявки',
  `done_code` varchar(32) DEFAULT NULL COMMENT 'Код выполнения заявки',
  PRIMARY KEY (`id`),
  KEY `date_created` (`date_created`),
  KEY `user_id` (`user_id`),
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Заявки на вывод';

CREATE TABLE IF NOT EXISTS `{#}billing_paid_fields` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ctype_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID типа контента',
  `field` varchar(20) DEFAULT NULL COMMENT 'Системное имя поля',
  `price_field` varchar(20) DEFAULT NULL COMMENT 'Имя поля с ценой',
  `prices` text DEFAULT NULL COMMENT 'YAML с ценами по группам',
  `is_to_author` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Оплата в пользу автора',
  `is_notify_author` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Уведомлять автора о покупке',
  `notify_email` varchar(100) DEFAULT NULL COMMENT 'Email для уведоплений о покупке поля',
  `btn_titles` text DEFAULT NULL COMMENT 'JSON с названиями кнопок',
  PRIMARY KEY (`id`),
  KEY `ctype_id` (`ctype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Продажа полей';

CREATE TABLE IF NOT EXISTS `{#}billing_paid_fields_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `field_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID поля для покупки из billing_paid_fields',
  `item_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID записи типа контента',
  `user_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID пользователя, который купил поле',
  `date_sold` timestamp NULL DEFAULT current_timestamp() COMMENT 'Дата продажи',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`item_id`,`field_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Операции продажи полей';

CREATE TABLE IF NOT EXISTS `{#}billing_payouts` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `is_enabled` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Включена?',
  `title` varchar(128) DEFAULT NULL COMMENT 'Название выплаты',
  `groups` text DEFAULT NULL COMMENT 'Группы пользователей для выплаты',
  `user_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID пользователя для выплаты',
  `is_topup_balance` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Пополнить баланс до указанной суммы',
  `is_passed` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Ограничение по времени с момента регистрации',
  `is_rating` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Ограничение по рейтингу',
  `is_karma` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Ограничение по репутации',
  `is_field` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Ограничение по полю профиля',
  `passed_days` int(11) UNSIGNED DEFAULT NULL COMMENT 'Не менее дней с момента регистрации',
  `rating` int(11) DEFAULT NULL COMMENT 'Рейтинг не менее',
  `karma` int(11) DEFAULT NULL COMMENT 'Репутация не менее',
  `field` varchar(64) DEFAULT NULL COMMENT 'Имя поля',
  `field_value` varchar(128) DEFAULT NULL COMMENT 'Значение поля',
  `amount` decimal(11,2) DEFAULT NULL COMMENT 'Сумма выплаты',
  `field_amount` varchar(64) DEFAULT NULL COMMENT 'Сумма выплаты из значения этого поля профиля',
  `period` int(11) UNSIGNED DEFAULT NULL COMMENT 'Периодичность выплаты',
  `date_last` timestamp NULL DEFAULT NULL COMMENT 'Дата последней выплаты',
  PRIMARY KEY (`id`),
  KEY `is_enabled` (`is_enabled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Выплаты по расписанию';

CREATE TABLE IF NOT EXISTS `{#}billing_plans` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(128) DEFAULT NULL COMMENT 'Название плана',
  `description` text DEFAULT NULL COMMENT 'Описание плана',
  `is_enabled` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Включено?',
  `is_real_price` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Покупка только за реальную валюту',
  `max_out` decimal(11,2) UNSIGNED DEFAULT NULL COMMENT 'Максимальная сумма вывода денег',
  `groups` text DEFAULT NULL COMMENT 'YAML с группами для перевода',
  `prices` text DEFAULT NULL COMMENT 'YAML с ценами по группам',
  `users` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Кол-во пользователей, подписанных на план',
  `ordering` int(11) UNSIGNED DEFAULT NULL COMMENT 'Порядковый номер в списке',
  PRIMARY KEY (`id`),
  KEY `is_enabled` (`is_enabled`),
  KEY `ordering` (`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Тарифные планы подписок';

CREATE TABLE IF NOT EXISTS `{#}billing_plans_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID пользователя, который купил подписку',
  `plan_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID тарифного плана',
  `old_groups` text DEFAULT NULL COMMENT 'YAML групп, которые были до покупки подписки',
  `date_until` timestamp NULL DEFAULT NULL COMMENT 'Дата окончания подписки',
  `is_paused` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Неактивный план?',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `date_until` (`date_until`),
  KEY `plan_id` (`plan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Оформленные подписки';

CREATE TABLE IF NOT EXISTS `{#}billing_refs` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID пользователя, который зарегистрировался',
  `ref_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID пользователя, по чьей ссылке зарегистрировался',
  `level` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Уровень',
  `date_reg` timestamp NULL DEFAULT current_timestamp() COMMENT 'Дата регистрации',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `ref_id` (`ref_id`),
  KEY `level` (`level`),
  KEY `date_reg` (`date_reg`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Рефералы';

CREATE TABLE IF NOT EXISTS `{#}billing_systems` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL COMMENT 'Внутреннее системное имя платёжной системы',
  `title` varchar(64) DEFAULT NULL COMMENT 'Название платёжной системы',
  `payment_url` varchar(255) DEFAULT NULL COMMENT 'Внешний URL формы оплаты',
  `rate` decimal(8,4) UNSIGNED DEFAULT 1.0000 COMMENT 'Курс валюты платёжной системы',
  `options` text DEFAULT NULL COMMENT 'YAML опций',
  `is_enabled` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Включена?',
  `ordering` int(11) UNSIGNED DEFAULT NULL COMMENT 'Порядоковый номер при сортировке в списке',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Системы оплаты';

INSERT INTO `{#}billing_systems` (`id`, `name`, `title`, `payment_url`, `rate`, `options`, `is_enabled`, `ordering`) VALUES
(2, 'wmz', 'Webmoney WMZ', 'https://merchant.webmoney.ru/lmi/payment.asp', 1.0000, '---\npurse: \"\"\nsecret_key: \"\"\ntest_mode: \"0\"\n', NULL, 2),
(3, 'robokassa', 'Электронные деньги, карты, терминалы (Робокасса)', 'https://test.robokassa.ru/Index.aspx', 1.0000, '---\nmerchant_login: \"\"\npassword1: \"\"\npassword2: \"\"\nfiscal_on: null\nfiscal_sno: osn\nfiscal_method: full_payment\nfiscal_object: service\nfiscal_name: \"\"\nfiscal_tax: none\n', NULL, 6),
(4, 'interkassa', 'Электронные деньги, карты, терминалы (Интеркасса)', 'https://sci.interkassa.com', 1.0000, '---\nik_co_id: \"\"\nik_secret_key: \"\"\n', NULL, 7),
(5, 'qiwi', 'Qiwi кошелек', 'billing/prepare/qiwi', 1.0000, '---\nshop_id: 0\napi_id: \"\"\napi_password: \"\"\npassword: \"\"\n', 0, 4),
(7, 'onpay', 'Электронные деньги, карты, терминалы (OnPay)', 'https://secure.onpay.ru/pay/{merchant_login}', 1.0000, '---\nmerchant_login: \"\"\npassword1: \"\"\n', NULL, 9),
(8, 'w1', 'Электронные деньги, карты, терминалы (W1)', 'https://wl.walletone.com/checkout/checkout/Index', 1.0000, '---\nmerchant_id: \"\"\nkey: \"\"\ncurrency_id: \"\"\n', NULL, 8),
(9, 'test', 'Тест (мгновенное пополнение)', 'billing/prepare/test', 0.1000, NULL, 1, 1),
(10, 'yandex', 'ЮMoney (Яндекс.Деньги)', 'https://yoomoney.ru/quickpay/confirm.xml', 1.0000, '---\nreceiver: \"\"\nsecret_key: \"\"\n', 0, 12),
(11, 'yakassa', 'ЮKassa (Яндекс.Касса)', 'https://money.yandex.ru/eshop.xml', 1.0000, '---\nshop_id: \"\"\nscid: \"\"\nkey: \"\"\n', NULL, 13),
(13, 'moneta', 'Монета.Ру', 'https://www.payanyway.ru/assistant.htm', 1.0000, '---\nmnt_id: \"\"\nkey: \"\"\ncurrency: RUB\n', NULL, 15),
(14, 'paypal', 'PayPal', 'https://api.sandbox.paypal.com', 1.0000, '---\naccount: \"\"\ncurrency: RUB\nclient_id: \"\"\nsecret: \"\"\n', 0, 10),
(16, 'liqpay', 'LiqPay', 'https://www.liqpay.ua/api/3/checkout', 1.0000, '---\npublic_key: \"\"\nprivate_key: \"\"\ncurrency: UAH\naction: pay\n', NULL, 16);

CREATE TABLE IF NOT EXISTS `{#}billing_terms` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ctype_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID типа контента',
  `prices` text DEFAULT NULL COMMENT 'YAML с ценами по группам',
  PRIMARY KEY (`id`),
  KEY `ctype_id` (`ctype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Цены за публикацию одного дня';

CREATE TABLE IF NOT EXISTS `{#}billing_transfers` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `from_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID пользователя, от кого перевод',
  `to_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID пользователя, кому перевод',
  `amount` decimal(11,2) UNSIGNED DEFAULT NULL COMMENT 'Сколько перевели',
  `description` varchar(255) DEFAULT NULL COMMENT 'Описание операции',
  `code` varchar(32) DEFAULT NULL COMMENT 'Код подтверждения перевода',
  `status` tinyint(1) DEFAULT 0 COMMENT 'Статус перевода',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`) USING BTREE,
  KEY `from_id` (`from_id`),
  KEY `to_id` (`to_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Переводы между пользователями';

CREATE TABLE IF NOT EXISTS `{#}billing_vip_fields` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ctype_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID типа контента',
  `field` varchar(40) DEFAULT NULL COMMENT 'Системное имя поля',
  `prices` text DEFAULT NULL COMMENT 'YAML с ценами по группам',
  `description` varchar(255) DEFAULT NULL COMMENT 'Описание для истории операций',
  PRIMARY KEY (`id`),
  KEY `ctype_id` (`ctype_id`),
  KEY `field` (`field`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Поля для продажи при заполнении';

CREATE TABLE IF NOT EXISTS `{#}billing_vip_fields_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `field_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID поля из таблицы billing_vip_fields',
  `item_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID записи ТК',
  `user_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID пользователя, купившего заполнение поля',
  `date_sold` timestamp NULL DEFAULT NULL COMMENT 'Дата продажи',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`item_id`,`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Покупки заполнения полей';

INSERT INTO  `{#}users_tabs` (`title` ,`controller` ,`name` ,`is_active` ,`ordering`)
VALUES ('Баланс',  'billing',  'balance',  '1',  '100');

INSERT INTO `{#}scheduler_tasks` (`title`, `controller`, `hook`, `period`, `date_last_run`, `is_active`, `is_new`) VALUES
('Отслеживание окончания подписок', 'billing', 'relegation', 60, NULL, 1, 1),
('Выплаты по расписанию', 'billing', 'payouts', 720, NULL, 1, 1);

INSERT INTO `{#}controllers` (`title`, `name`, `slug`, `is_enabled`, `options`, `author`, `url`, `version`, `is_backend`, `is_external`, `files`, `addon_id`) VALUES
('Биллинг', 'billing', NULL, 1, '---\ncurrency_title: баллы\ncurrency: балл|балла|баллов\ncurrency_real: руб.\nmin_pack: 0\nreg_bonus: 0\nin_mode: enabled\nprices:\n  - \n    amount: 1\n    price: 1\nis_plans: 1\nplan_remind_days: 3\nis_transfers: 1\nis_transfers_mail: null\nis_transfers_notify: null\nrtp_groups: [ ]\nis_rtp: null\nrtp_rate: 0.5\nis_ptr: 1\nptr_rate: 1\nis_out: null\nout_groups: [ ]\nis_out_mail: 1\nout_period_days: 0\nout_min: 1\nout_rate: 0.5\nout_systems: |\n  Webmoney WMZ\r\n  ЮMoney\r\n  QIWI\r\n  PayPal\nout_email: \"\"\nis_refs: 1\nref_days: 100\nref_url: \"\"\nref_terms: \"\"\nref_bonus: 0\nref_mode: all\nref_type: linear\nref_scale: 2\nref_levels:\n  - \n    percent: 1\ncur_real_symb: ₽\nis_refs_as_invite: 1\npay_field_html: \'<a class=\"btn btn-primary billing-buy-field\" href=\"{url}\">{solid%coins} {title}</a>\'\nlimit_log: 15\nlimit_out: 15\nlimit_refs: 15\nbtn_titles:\n  guest: \'Покупка от {price}\'\n  user: \'Купить за {price}\'\n', 'InstantCMS Team', 'https://instantcms.ru', '2.1.0', 1, NULL, NULL, NULL);