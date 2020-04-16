DROP TABLE IF EXISTS `{#}wysiwygs_presets`;
CREATE TABLE `{#}wysiwygs_presets` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `wysiwyg_name` varchar(40) DEFAULT NULL COMMENT 'Имя редактора',
  `options` text COMMENT 'Опции',
  `title` varchar(100) DEFAULT NULL COMMENT 'Название пресета',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Пресеты для wysiwyg редакторов';

INSERT INTO `{#}wysiwygs_presets` (`id`, `wysiwyg_name`, `options`, `title`) VALUES
(1, 'markitup', '{\"buttons\":[\"0\",\"1\",\"2\",\"3\",\"4\",\"5\",\"7\",\"14\"],\"skin\":\"simple\"}', 'Фотографии');