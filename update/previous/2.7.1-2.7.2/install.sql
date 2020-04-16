DROP TABLE IF EXISTS `{#}events`;
CREATE TABLE `{#}events` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `event` varchar(64) DEFAULT NULL COMMENT 'Событие',
  `listener` varchar(32) DEFAULT NULL COMMENT 'Слушатель (компонент)',
  `ordering` int(5) unsigned DEFAULT NULL COMMENT 'Порядковый номер ',
  `is_enabled` tinyint(1) unsigned DEFAULT '1' COMMENT 'Активность',
  PRIMARY KEY (`id`),
  KEY `hook` (`event`),
  KEY `listener` (`listener`),
  KEY `is_enabled` (`is_enabled`,`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Привязка хуков к событиям';