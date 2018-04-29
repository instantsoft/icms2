UPDATE `{#}comments` SET `author_name` = 'Гость' WHERE `author_name` IS NOT NULL;
UPDATE `{#}widgets` SET `controller` = 'auth', `title` = 'Форма авторизации' WHERE `controller` IS NULL AND `name` = 'auth';

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Списки подписок';

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Подписки';