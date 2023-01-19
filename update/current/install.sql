ALTER TABLE `{#}content_types` CHANGE `title` `title` VARCHAR(100) NULL DEFAULT NULL;
ALTER TABLE `{#}content_datasets` CHANGE `title` `title` VARCHAR(100) NULL DEFAULT NULL;

DELETE FROM `{#}controllers` WHERE `name` = 'languages';
INSERT INTO `{#}controllers` (`id`, `title`, `name`, `slug`, `is_enabled`, `options`, `author`, `url`, `version`, `is_backend`) VALUES
(25, 'Мультиязычность', 'languages', NULL, 1, '---\r\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1);