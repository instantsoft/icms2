DROP TABLE IF EXISTS `{#}billing_actions`;
CREATE TABLE `{#}billing_actions` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `controller` varchar(32) DEFAULT NULL COMMENT 'Controller name',
  `name` varchar(64) DEFAULT NULL COMMENT 'Action name',
  `title` varchar(255) DEFAULT NULL COMMENT 'Title',
  `prices` text DEFAULT NULL COMMENT 'YAML with prices by group',
  PRIMARY KEY (`id`),
  KEY `controller` (`controller`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Paid actions';

INSERT INTO `{#}billing_actions` (`controller`, `name`, `title`, `prices`) VALUES
('content', 'pages_add', 'Pages: adding', '---\n3: 0\n4: 0\n5: 0\n6: 0\n');

DROP TABLE IF EXISTS `{#}billing_holds`;
CREATE TABLE `{#}billing_holds` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `target` varchar(100) DEFAULT NULL COMMENT 'Operation identifier',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT 'User ID',
  `amount` decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT 'Amount',
  `payload` text DEFAULT NULL COMMENT 'JSON with operation parameters',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`target`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Pending balances';

DROP TABLE IF EXISTS `{#}billing_log`;
CREATE TABLE `{#}billing_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Operation type: 1 - income, 0 - payment',
  `action_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'Paid action ID',
  `date_created` timestamp NULL DEFAULT current_timestamp() COMMENT 'Operation creation date',
  `date_done` timestamp NULL DEFAULT NULL COMMENT 'Operation completion date',
  `amount` decimal(11,2) DEFAULT NULL COMMENT 'Amount in internal currency',
  `summ` decimal(11,2) DEFAULT NULL COMMENT 'Amount in real currency',
  `user_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'User ID',
  `sender_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'User ID of the sender (for transfers)',
  `status` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Operation status: 0 - created, 1 - completed',
  `description` varchar(512) DEFAULT NULL COMMENT 'Operation description',
  `url` varchar(255) DEFAULT NULL COMMENT 'URL of the related purchase',
  `ref_link_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'Referral entry ID from billing_refs table',
  `plan_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'Tariff plan ID',
  `plan_period` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Tariff plan duration',
  `system_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'Payment system ID',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `action_id` (`action_id`),
  KEY `date_created` (`date_created`),
  KEY `user_id` (`user_id`),
  KEY `sender_id` (`sender_id`),
  KEY `status` (`status`),
  KEY `ref_link_id` (`ref_link_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='List of all transactions';

DROP TABLE IF EXISTS `{#}billing_outs`;
CREATE TABLE `{#}billing_outs` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `date_created` timestamp NULL DEFAULT current_timestamp() COMMENT 'Creation date',
  `date_done` timestamp NULL DEFAULT NULL COMMENT 'Completion date',
  `user_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID of the user who submitted the request',
  `amount` decimal(11,2) UNSIGNED DEFAULT NULL COMMENT 'Amount in internal currency',
  `summ` decimal(11,2) UNSIGNED DEFAULT NULL COMMENT 'Amount in real currency',
  `system` varchar(64) DEFAULT NULL COMMENT 'Destination of the withdrawal',
  `purse` varchar(32) DEFAULT NULL COMMENT 'Wallet/account number',
  `status` tinyint(1) UNSIGNED DEFAULT 0 COMMENT 'Request status',
  `code` varchar(32) DEFAULT NULL COMMENT 'Withdrawal confirmation code',
  `done_code` varchar(32) DEFAULT NULL COMMENT 'Withdrawal completion code',
  PRIMARY KEY (`id`),
  KEY `date_created` (`date_created`),
  KEY `user_id` (`user_id`),
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Withdrawal requests';

DROP TABLE IF EXISTS `{#}billing_paid_fields`;
CREATE TABLE `{#}billing_paid_fields` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ctype_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'Content type ID',
  `field` varchar(20) DEFAULT NULL COMMENT 'System name of the field',
  `price_field` varchar(20) DEFAULT NULL COMMENT 'Name of the field containing the price',
  `prices` text DEFAULT NULL COMMENT 'YAML with prices by user group',
  `is_to_author` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Payment goes to the author',
  `is_notify_author` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Notify the author about the purchase',
  `notify_email` varchar(100) DEFAULT NULL COMMENT 'Email for field purchase notifications',
  `btn_titles` text DEFAULT NULL COMMENT 'JSON with button titles',
  PRIMARY KEY (`id`),
  KEY `ctype_id` (`ctype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Field sales';

DROP TABLE IF EXISTS `{#}billing_paid_fields_log`;
CREATE TABLE `{#}billing_paid_fields_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `field_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID of the purchasable field from billing_paid_fields',
  `item_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID of the content item',
  `user_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID of the user who purchased the field',
  `date_sold` timestamp NULL DEFAULT current_timestamp() COMMENT 'Date of purchase',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`item_id`,`field_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Field sale transactions';

DROP TABLE IF EXISTS `{#}billing_payouts`;
CREATE TABLE `{#}billing_payouts` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `is_enabled` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Enabled?',
  `title` varchar(128) DEFAULT NULL COMMENT 'Payout title',
  `groups` text DEFAULT NULL COMMENT 'User groups eligible for the payout',
  `user_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'User ID for the payout',
  `is_topup_balance` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Top up the balance up to the specified amount',
  `is_passed` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Restrict by time since registration',
  `is_rating` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Restrict by user rating',
  `is_karma` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Restrict by user reputation',
  `is_field` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Restrict by profile field value',
  `passed_days` int(11) UNSIGNED DEFAULT NULL COMMENT 'Minimum days since registration',
  `rating` int(11) DEFAULT NULL COMMENT 'Minimum rating',
  `karma` int(11) DEFAULT NULL COMMENT 'Minimum reputation',
  `field` varchar(64) DEFAULT NULL COMMENT 'Profile field name',
  `field_value` varchar(128) DEFAULT NULL COMMENT 'Required value of the profile field',
  `amount` decimal(11,2) DEFAULT NULL COMMENT 'Fixed payout amount',
  `field_amount` varchar(64) DEFAULT NULL COMMENT 'Payout amount based on this profile field value',
  `period` int(11) UNSIGNED DEFAULT NULL COMMENT 'Payout frequency (in days)',
  `date_last` timestamp NULL DEFAULT NULL COMMENT 'Date of last payout',
  PRIMARY KEY (`id`),
  KEY `is_enabled` (`is_enabled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Scheduled payouts';

DROP TABLE IF EXISTS `{#}billing_plans`;
CREATE TABLE `{#}billing_plans` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(128) DEFAULT NULL COMMENT 'Plan name',
  `description` text DEFAULT NULL COMMENT 'Plan description',
  `is_enabled` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Enabled?',
  `is_real_price` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Purchase with real currency only',
  `max_out` decimal(11,2) UNSIGNED DEFAULT NULL COMMENT 'Maximum withdrawal amount',
  `groups` text DEFAULT NULL COMMENT 'YAML with target user groups',
  `prices` text DEFAULT NULL COMMENT 'YAML with prices by group',
  `users` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Number of users subscribed to the plan',
  `ordering` int(11) UNSIGNED DEFAULT NULL COMMENT 'Display order in list',
  PRIMARY KEY (`id`),
  KEY `is_enabled` (`is_enabled`),
  KEY `ordering` (`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Subscription plans';

DROP TABLE IF EXISTS `{#}billing_plans_log`;
CREATE TABLE `{#}billing_plans_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID of the user who purchased the subscription',
  `plan_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID of the subscription plan',
  `old_groups` text DEFAULT NULL COMMENT 'YAML of the user’s groups before the subscription',
  `date_until` timestamp NULL DEFAULT NULL COMMENT 'Subscription expiration date',
  `is_paused` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Inactive plan?',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `date_until` (`date_until`),
  KEY `plan_id` (`plan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Active subscriptions';

DROP TABLE IF EXISTS `{#}billing_refs`;
CREATE TABLE `{#}billing_refs` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID of the user who registered',
  `ref_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID of the user whose referral link was used',
  `level` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Referral level',
  `date_reg` timestamp NULL DEFAULT current_timestamp() COMMENT 'Registration date',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `ref_id` (`ref_id`),
  KEY `level` (`level`),
  KEY `date_reg` (`date_reg`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Referrals';

DROP TABLE IF EXISTS `{#}billing_systems`;
CREATE TABLE `{#}billing_systems` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL COMMENT 'Internal system name of the payment system',
  `title` varchar(64) DEFAULT NULL COMMENT 'Display name of the payment system',
  `payment_url` varchar(255) DEFAULT NULL COMMENT 'External URL of the payment form',
  `rate` decimal(8,4) UNSIGNED DEFAULT 1.0000 COMMENT 'Exchange rate of the payment system currency',
  `options` text DEFAULT NULL COMMENT 'YAML with configuration options',
  `is_enabled` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Enabled?',
  `ordering` int(11) UNSIGNED DEFAULT NULL COMMENT 'Sort order in the list',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Payment systems';

INSERT INTO `{#}billing_systems` (`id`, `name`, `title`, `payment_url`, `rate`, `options`, `is_enabled`, `ordering`) VALUES
(2, 'wmz', 'Webmoney WMZ', 'https://merchant.webmoney.ru/lmi/payment.asp', 1.0000, '---\npurse: \"\"\nsecret_key: \"\"\ntest_mode: \"0\"\n', NULL, 2),
(3, 'robokassa', 'E-money, cards, terminals (Robokassa)', 'https://test.robokassa.ru/Index.aspx', 1.0000, '---\nmerchant_login: \"\"\npassword1: \"\"\npassword2: \"\"\nfiscal_on: null\nfiscal_sno: osn\nfiscal_method: full_payment\nfiscal_object: service\nfiscal_name: \"\"\nfiscal_tax: none\n', NULL, 6),
(4, 'interkassa', 'E-money, cards, terminals (Interkassa)', 'https://sci.interkassa.com', 1.0000, '---\nik_co_id: \"\"\nik_secret_key: \"\"\n', NULL, 7),
(5, 'qiwi', 'Qiwi Wallet', 'billing/prepare/qiwi', 1.0000, '---\nshop_id: 0\napi_id: \"\"\napi_password: \"\"\npassword: \"\"\n', 0, 4),
(7, 'onpay', 'E-money, cards, terminals (OnPay)', 'https://secure.onpay.ru/pay/{merchant_login}', 1.0000, '---\nmerchant_login: \"\"\npassword1: \"\"\n', NULL, 9),
(8, 'w1', 'E-money, cards, terminals (W1)', 'https://wl.walletone.com/checkout/checkout/Index', 1.0000, '---\nmerchant_id: \"\"\nkey: \"\"\ncurrency_id: \"\"\n', NULL, 8),
(9, 'test', 'Test (instant top-up)', 'billing/prepare/test', 0.1000, NULL, 1, 1),
(10, 'yandex', 'ЮMoney', 'https://yoomoney.ru/quickpay/confirm.xml', 1.0000, '---\nreceiver: \"\"\nsecret_key: \"\"\n', 0, 12),
(11, 'yakassa', 'ЮKassa', 'https://money.yandex.ru/eshop.xml', 1.0000, '---\nshop_id: \"\"\nscid: \"\"\nkey: \"\"\n', NULL, 13),
(13, 'moneta', 'PayAnyWay', 'https://www.payanyway.ru/assistant.htm', 1.0000, '---\nmnt_id: \"\"\nkey: \"\"\ncurrency: RUB\n', NULL, 15),
(14, 'paypal', 'PayPal', 'https://api.sandbox.paypal.com', 1.0000, '---\naccount: \"\"\ncurrency: USD\nclient_id: \"\"\nsecret: \"\"\n', 0, 10),
(16, 'liqpay', 'LiqPay', 'https://www.liqpay.ua/api/3/checkout', 1.0000, '---\npublic_key: \"\"\nprivate_key: \"\"\ncurrency: UAH\naction: pay\n', NULL, 16);

DROP TABLE IF EXISTS `{#}billing_terms`;
CREATE TABLE `{#}billing_terms` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ctype_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'Content type ID',
  `prices` text DEFAULT NULL COMMENT 'YAML with prices by group',
  PRIMARY KEY (`id`),
  KEY `ctype_id` (`ctype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Prices for one day publication';

DROP TABLE IF EXISTS `{#}billing_transfers`;
CREATE TABLE `{#}billing_transfers` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `from_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'User ID, from whom the transfer is from',
  `to_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'User ID to whom the transfer is to',
  `amount` decimal(11,2) UNSIGNED DEFAULT NULL COMMENT 'Transfer amount',
  `description` varchar(255) DEFAULT NULL COMMENT 'Operation description',
  `code` varchar(32) DEFAULT NULL COMMENT 'Transfer Confirmation Code',
  `status` tinyint(1) DEFAULT 0 COMMENT 'Transfer status',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`) USING BTREE,
  KEY `from_id` (`from_id`),
  KEY `to_id` (`to_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Transfers between users';

DROP TABLE IF EXISTS `{#}billing_vip_fields`;
CREATE TABLE `{#}billing_vip_fields` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ctype_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'Content type ID',
  `field` varchar(40) DEFAULT NULL COMMENT 'System field name',
  `prices` text DEFAULT NULL COMMENT 'YAML with prices by group',
  `description` varchar(255) DEFAULT NULL COMMENT 'Description for transaction history',
  PRIMARY KEY (`id`),
  KEY `ctype_id` (`ctype_id`),
  KEY `field` (`field`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Fields for sale when filling out';

DROP TABLE IF EXISTS `{#}billing_vip_fields_log`;
CREATE TABLE `{#}billing_vip_fields_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `field_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'Field ID from the billing_vip_fields table',
  `item_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'Content type ID',
  `user_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'User ID of the user who purchased the field filling',
  `date_sold` timestamp NULL DEFAULT NULL COMMENT 'The date of sale',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`item_id`,`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Purchases to fill out fields';

ALTER TABLE  `{#}users` ADD  `balance` decimal(12,2) DEFAULT 0.00 AFTER  `email`;
ALTER TABLE  `{#}users` ADD  `plan_id` int(11) UNSIGNED DEFAULT NULL AFTER  `balance`;
ALTER TABLE  `{#}users` ADD INDEX (`balance`);
ALTER TABLE  `{#}users` ADD INDEX (`plan_id`);

INSERT INTO  `{#}users_tabs` (`title` ,`controller` ,`name` ,`is_active` ,`ordering`)
VALUES ('Balance',  'billing',  'balance',  '1',  '100');

INSERT INTO `{#}scheduler_tasks` (`title`, `controller`, `hook`, `period`, `date_last_run`, `is_active`, `is_new`) VALUES
('Tracking the end of subscriptions', 'billing', 'relegation', 60, NULL, 1, 1),
('Scheduled payments', 'billing', 'payouts', 720, NULL, 1, 1);

INSERT INTO `{#}controllers` (`title`, `name`, `slug`, `is_enabled`, `options`, `author`, `url`, `version`, `is_backend`, `is_external`, `files`, `addon_id`) VALUES
('Billing', 'billing', NULL, 1, '---\ncurrency_title: points\ncurrency: point|points|points\ncurrency_real: usd\nmin_pack: 0\nreg_bonus: 0\nin_mode: enabled\nprices:\n  - \n    amount: 1\n    price: 1\nis_plans: 1\nplan_remind_days: 3\nis_transfers: 1\nis_transfers_mail: null\nis_transfers_notify: null\nrtp_groups: [ ]\nis_rtp: null\nrtp_rate: 0.5\nis_ptr: 1\nptr_rate: 1\nis_out: null\nout_groups: [ ]\nis_out_mail: 1\nout_period_days: 0\nout_min: 1\nout_rate: 0.5\nout_systems: |\n  Stripe\r\n  PayPal\nout_email: \"\"\nis_refs: 1\nref_days: 100\nref_url: \"\"\nref_terms: \"\"\nref_bonus: 0\nref_mode: all\nref_type: linear\nref_scale: 2\nref_levels:\n  - \n    percent: 1\ncur_real_symb: $\nis_refs_as_invite: 1\npay_field_html: \'<a class=\"btn btn-primary billing-buy-field\" href=\"{url}\">{solid%coins} {title}</a>\'\nlimit_log: 15\nlimit_out: 15\nlimit_refs: 15\nbtn_titles:\n  guest: \'Buying from {price}\'\n  user: \'Buy for {price}\'\n', 'InstantCMS Team', 'https://instantcms.io', '2.1.0', 1, NULL, NULL, NULL);

INSERT INTO `{#}events` (`event`, `listener`, `ordering`, `is_enabled`) VALUES
('admin_users_filter', 'billing', 244, 1),
('content_add', 'billing', 245, 1),
('content_after_add_approve', 'billing', 246, 1),
('content_after_update_approve', 'billing', 247, 1),
('content_before_item', 'billing', 249, 1),
('content_edit', 'billing', 250, 1),
('content_validate', 'billing', 251, 1),
('cron_payouts', 'billing', 252, 1),
('cron_relegation', 'billing', 253, 1),
('ctype_after_add', 'billing', 254, 1),
('ctype_after_delete', 'billing', 255, 1),
('grid_admin_users', 'billing', 257, 1),
('menu_billing', 'billing', 258, 1),
('user_delete', 'billing', 259, 1),
('user_profile_buttons', 'billing', 260, 1),
('user_registered', 'billing', 261, 1),
('user_tab_info', 'billing', 262, 1),
('user_tab_show', 'billing', 263, 1),
('engine_start', 'billing', 264, 1),
('content_after_add', 'billing', 265, 1),
('content_after_update', 'billing', 266, 1),
('content_after_delete', 'billing', 267, 1),
('moderation_cancel', 'billing', 268, 1),
('moderation_rework', 'billing', 269, 1);