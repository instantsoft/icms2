ALTER TABLE `{#}content_types` CHANGE `title` `title` VARCHAR(100) NULL DEFAULT NULL;
ALTER TABLE `{#}content_datasets` CHANGE `title` `title` VARCHAR(100) NULL DEFAULT NULL;
ALTER TABLE `{#}widgets_bind` CHANGE `title` `title` VARCHAR(128) NULL DEFAULT NULL;
ALTER TABLE `{#}activity_types` CHANGE `description` `description` VARCHAR(200) NULL DEFAULT NULL;

DELETE FROM `{#}controllers` WHERE `name` = 'languages';
INSERT INTO `{#}controllers` (`title`, `name`, `slug`, `is_enabled`, `options`, `author`, `url`, `version`, `is_backend`) VALUES
('Мультиязычность', 'languages', NULL, 1, '---\nservice: google\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1);