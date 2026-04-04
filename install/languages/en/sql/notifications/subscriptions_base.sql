DROP TABLE IF EXISTS `{#}subscriptions`;
CREATE TABLE `{#}subscriptions` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `controller` varchar(32) DEFAULT NULL,
  `subject` varchar(50) DEFAULT NULL,
  `subject_url` varchar(255) DEFAULT NULL,
  `params` text,
  `subscribers_count` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `hash` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`),
  KEY `target_controller` (`controller`,`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Subscription Lists';

DROP TABLE IF EXISTS `{#}subscriptions_bind`;
CREATE TABLE `{#}subscriptions_bind` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `subscription_id` int(11) UNSIGNED DEFAULT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `guest_email` varchar(100) DEFAULT NULL,
  `guest_name` varchar(50) DEFAULT NULL,
  `is_confirmed` tinyint(1) UNSIGNED DEFAULT '1',
  `confirm_token` varchar(32) DEFAULT NULL,
  `date_pub` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`subscription_id`) USING BTREE,
  KEY `guest_email` (`guest_email`,`subscription_id`) USING BTREE,
  KEY `confirm_token` (`confirm_token`),
  KEY `subscription_id` (`subscription_id`,`is_confirmed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Subscriptions';

INSERT INTO `{#}controllers` (`title`, `name`, `is_enabled`, `options`, `author`, `url`, `version`, `is_backend`) VALUES
('Subscriptions', 'subscriptions', 1, '---\nguest_email_confirmation: 1\nneed_auth: null\nverify_exp: 24\nupdate_user_rating: 1\nrating_value: 1\nadmin_email:\nlimit: 20\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1);

INSERT INTO `{#}events` (`event`, `listener`, `ordering`, `is_enabled`) VALUES
('user_delete', 'subscriptions', 129, 1),
('content_toolbar_html', 'subscriptions', 130, 1),
('photos_toolbar_html', 'subscriptions', 131, 1),
('content_filter_buttons_html', 'subscriptions', 132, 1),
('user_tab_info', 'subscriptions', 133, 1),
('content_photos_after_add', 'subscriptions', 134, 1),
('user_notify_types', 'subscriptions', 135, 1),
('user_tab_show', 'subscriptions', 136, 1),
('content_after_add_approve', 'subscriptions', 137, 1),
('publish_delayed_content', 'subscriptions', 138, 1),
('ctype_basic_form', 'subscriptions', 139, 1),
('content_category_after_update', 'subscriptions', 153, 1);

INSERT INTO `{#}scheduler_tasks` (`title`, `controller`, `hook`, `period`, `is_strict_period`, `date_last_run`, `is_active`, `is_new`) VALUES
('Removes expired unconfirmed guest subscriptions', 'subscriptions', 'delete_expired_unconfirmed', 1440, 1, DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:05'), 1, 1);

INSERT INTO `{#}users_tabs` (`title`, `controller`, `name`, `is_active`, `ordering`) VALUES
('Subscriptions', 'subscriptions', 'subscriptions', 1, 3);

INSERT INTO `{#}widgets` (`controller`, `name`, `title`, `author`, `url`, `version`, `is_external`) VALUES
('subscriptions', 'button', 'Subscription buttons', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL);