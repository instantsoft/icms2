UPDATE `{#}perms_rules` SET `type` = 'list', `options` = 'premod,yes' WHERE `controller` = 'groups' AND `name` = 'add';
UPDATE `{#}perms_users` SET `value`= 'yes' WHERE `rule_id` = (SELECT `id` FROM `{#}perms_rules` WHERE `controller` = 'groups' AND `name` = 'add');
UPDATE `{#}perms_rules` SET `type` = 'list', `options` = 'premod,yes' WHERE `controller` = 'content' AND `name` = 'add';
UPDATE `{#}perms_users` SET `value`= 'yes' WHERE `rule_id` = (SELECT `id` FROM `{#}perms_rules` WHERE `controller` = 'content' AND `name` = 'add');
UPDATE `{#}perms_rules` SET `type` = 'list', `options` = 'premod_own,own,premod_all,all' WHERE `controller` = 'content' AND `name` = 'edit';
ALTER TABLE `{#}content_datasets` CHANGE `filters` `filters` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Массив фильтров набора';
ALTER TABLE `{#}content_datasets` CHANGE `sorting` `sorting` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Массив правил сортировки';
DROP TABLE IF EXISTS `{#}jobs`;
CREATE TABLE `{#}jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(100) DEFAULT NULL COMMENT 'Название очереди',
  `payload` text COMMENT 'Данные задания',
  `last_error` varchar(200) DEFAULT NULL COMMENT 'Последняя ошибка',
  `priority` tinyint(1) UNSIGNED DEFAULT '1' COMMENT 'Приоритет',
  `attempts` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Попытки выполнения',
  `is_locked` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Блокировка одновременного запуска',
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата постановки в очередь',
  `date_started` timestamp NULL DEFAULT NULL COMMENT 'Дата последней попытки выполнения задания',
  PRIMARY KEY (`id`),
  KEY `queue` (`queue`),
  KEY `attempts` (`attempts`,`is_locked`,`date_started`,`priority`,`date_created`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Очередь';
DELETE FROM `{#}perms_rules` WHERE `controller` = 'comments' AND `name` = 'is_moderator';