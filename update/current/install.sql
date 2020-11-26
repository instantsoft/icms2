ALTER TABLE `{users}` CHANGE `pass_token` `pass_token` VARCHAR(64) NULL DEFAULT NULL COMMENT 'Ключ для восстановления пароля';
ALTER TABLE `{users}_auth_tokens` CHANGE `auth_token` `auth_token` VARCHAR(128) NULL DEFAULT NULL;

UPDATE `{#}widgets_bind` SET `tpl_wrap`= 'wrapper' WHERE `tpl_wrap` IS NULL;

DELETE FROM `{#}controllers` WHERE `name` = 'forms';
INSERT INTO `{#}controllers` (`title`, `name`, `is_enabled`, `options`, `author`, `url`, `version`, `is_backend`) VALUES
('Конструктор форм', 'forms', 1, '---\nsend_text: >\n  Спасибо! Форма\n  успешно отправлена.\nallow_embed: null\nallow_embed_domain:\ndenied_embed_domain:\nletter: |\n  [subject:Форма: {form_title} - {site}]\r\n  \r\n  Здравствуйте.\r\n  \r\n  С сайта {site} отправлена форма <b>{form_title}</b>.\r\n  \r\n  Данные формы:\r\n  \r\n  {form_data}\r\n  \r\n  --\r\n   C уважением, {site}\r\n   <small>Письмо отправлено автоматически, пожалуйста, не отвечайте на него.</small>\nnotify_text: \'<p>Здравствуйте.</p><p>Отправлена форма <strong>{form_title}</strong>.</p><p><strong>Данные формы:</strong></p><p>{form_data}</p>\'\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1);

ALTER TABLE `{#}controllers` CHANGE `files` `files` MEDIUMTEXT NULL DEFAULT NULL COMMENT 'Список файлов контроллера (для стороних компонентов)';

DROP TABLE IF EXISTS `{#}forms`;
CREATE TABLE `{#}forms` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `options` text,
  `tpl_form` varchar(100) DEFAULT NULL,
  `hash` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hash` (`hash`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Формы конструктора форм';

DROP TABLE IF EXISTS `{#}forms_fields`;
CREATE TABLE `{#}forms_fields` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `form_id` int DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `hint` varchar(200) DEFAULT NULL,
  `ordering` int DEFAULT NULL,
  `is_enabled` tinyint UNSIGNED DEFAULT '1',
  `fieldset` varchar(32) DEFAULT NULL,
  `type` varchar(16) DEFAULT NULL,
  `values` text,
  `options` text,
  PRIMARY KEY (`id`),
  KEY `form_id` (`form_id`,`is_enabled`,`ordering`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Поля конструктора форм';

DROP TABLE IF EXISTS `{#}layout_cols`;
CREATE TABLE `{#}layout_cols` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `row_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID ряда',
  `title` varchar(255) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL COMMENT 'Название позиции',
  `type` enum('typical','custom') DEFAULT 'typical' COMMENT 'Тип колонки',
  `ordering` int(11) UNSIGNED DEFAULT NULL COMMENT 'Порядок колонки в исходном коде',
  `tag` varchar(10) DEFAULT 'div' COMMENT 'Тег колонки',
  `class` varchar(100) DEFAULT NULL COMMENT 'CSS класс колонки',
  `wrapper` text COMMENT 'Шаблон колонки',
  `options` text COMMENT 'Опции колонки',
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `row_id` (`row_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Колонки схемы позиций';

DROP TABLE IF EXISTS `{#}layout_rows`;
CREATE TABLE `{#}layout_rows` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID колонки родителя',
  `title` varchar(255) DEFAULT NULL,
  `tag` varchar(10) DEFAULT NULL COMMENT 'Тег ряда',
  `template` varchar(30) DEFAULT NULL COMMENT 'Привязка к шаблону',
  `ordering` int(11) DEFAULT NULL COMMENT 'Порядок ряда в исходном коде',
  `nested_position` enum('after','before') DEFAULT NULL COMMENT 'Позиция вложенного ряда',
  `class` varchar(100) DEFAULT NULL COMMENT 'CSS класс ряда',
  `options` text COMMENT 'Опции ряда',
  PRIMARY KEY (`id`),
  KEY `template` (`template`,`ordering`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Ряды схемы позиций виджетов';

INSERT INTO `{#}layout_rows` (`id`, `parent_id`, `title`, `tag`, `template`, `ordering`, `nested_position`, `class`, `options`) VALUES
(4, NULL, 'Контент', 'main', 'modern', 7, NULL, NULL, '{\"no_gutters\":null,\"vertical_align\":\"\",\"horizontal_align\":\"\",\"container\":\"container\",\"container_tag\":\"section\",\"container_tag_class\":\"\",\"parrent_tag\":\"\",\"parrent_tag_class\":\"\"}'),
(5, NULL, 'Перед контентом', 'div', 'modern', 6, NULL, NULL, '{\"no_gutters\":1,\"vertical_align\":\"\",\"horizontal_align\":\"\",\"container\":\"container\",\"container_tag\":\"div\",\"container_tag_class\":\"\",\"parrent_tag\":\"\",\"parrent_tag_class\":\"\"}'),
(6, NULL, 'Футер', 'div', 'modern', 10, NULL, 'align-items-center flex-wrap', '{\"no_gutters\":1,\"vertical_align\":\"\",\"horizontal_align\":\"\",\"container\":\"container\",\"container_tag\":\"div\",\"container_tag_class\":\"py-4\",\"parrent_tag\":\"footer\",\"parrent_tag_class\":\"icms-footer__bottom bg-dark text-white\"}'),
(8, 8, 'Вложенный после контента', 'div', 'modern', 8, 'after', NULL, '{\"no_gutters\":null,\"vertical_align\":\"\",\"horizontal_align\":\"\",\"container\":\"\",\"container_tag\":\"div\",\"container_tag_class\":\"\",\"parrent_tag\":\"\",\"parrent_tag_class\":\"\"}'),
(10, NULL, 'Инфо блок', 'div', 'modern', 4, NULL, NULL, '{\"no_gutters\":1,\"vertical_align\":\"\",\"horizontal_align\":\"\",\"container\":\"container\",\"container_tag\":\"div\",\"container_tag_class\":\"\",\"parrent_tag\":\"div\",\"parrent_tag_class\":\"bg-secondary text-warning\"}'),
(13, NULL, 'Хедер', NULL, 'modern', 1, NULL, NULL, '{\"no_gutters\":null,\"vertical_align\":\"\",\"horizontal_align\":\"\",\"container\":\"\",\"container_tag\":\"div\",\"container_tag_class\":\"\",\"parrent_tag\":\"header\",\"parrent_tag_class\":\"bg-primary\"}'),
(14, 26, 'Лого + меню пользователя', NULL, 'modern', 2, 'after', NULL, '{\"no_gutters\":null,\"vertical_align\":\"\",\"horizontal_align\":\"\",\"container\":\"container\",\"container_tag\":\"div\",\"container_tag_class\":\"d-flex justify-content-between align-items-center flex-nowrap py-1\",\"parrent_tag\":\"\",\"parrent_tag_class\":\"\"}'),
(15, 26, 'Меню', NULL, 'modern', 3, 'after', NULL, '{\"no_gutters\":null,\"vertical_align\":\"\",\"horizontal_align\":\"\",\"container\":\"container\",\"container_tag\":\"div\",\"container_tag_class\":\"pb-2\",\"parrent_tag\":\"\",\"parrent_tag_class\":\"\"}'),
(16, NULL, 'Ряд во всю ширину', NULL, 'modern', 5, NULL, NULL, '{\"no_gutters\":1,\"vertical_align\":\"\",\"horizontal_align\":\"\",\"container\":\"\",\"container_tag\":\"div\",\"container_tag_class\":\"\",\"parrent_tag\":\"section\",\"parrent_tag_class\":\"\"}'),
(17, NULL, 'Над футером', 'div', 'modern', 9, NULL, 'py-5 mb-n3', '{\"no_gutters\":null,\"vertical_align\":\"\",\"horizontal_align\":\"\",\"container\":\"container\",\"container_tag\":\"div\",\"container_tag_class\":\"border-bottom\",\"parrent_tag\":\"section\",\"parrent_tag_class\":\"icms-footer__middle  bg-dark text-white-50 mt-auto\"}');

INSERT INTO `{#}layout_cols` (`id`, `row_id`, `title`, `name`, `type`, `ordering`, `tag`, `class`, `wrapper`, `options`) VALUES
(8, 4, 'Тело страницы', 'pos_8', 'typical', 9, 'article', 'mb-3 mb-md-4', NULL, '{\"cut_before\":null,\"default_col_class\":\"\",\"md_col_class\":\"\",\"lg_col_class\":\"col-lg\",\"xl_col_class\":\"\",\"col_class\":\"\",\"default_order\":2,\"sm_order\":0,\"md_order\":0,\"lg_order\":0,\"xl_order\":0}'),
(9, 4, 'Правая колонка', 'pos_9', 'typical', 13, 'aside', 'mb-3 mb-md-4', NULL, '{\"cut_before\":null,\"default_col_class\":\"\",\"md_col_class\":\"\",\"lg_col_class\":\"col-lg-4\",\"xl_col_class\":\"\",\"col_class\":\"\",\"default_order\":3,\"sm_order\":0,\"md_order\":0,\"lg_order\":0,\"xl_order\":0}'),
(10, 5, 'Позиция глубиномера', 'pos_10', 'typical', 7, 'div', 'd-flex justify-content-between align-items-center', NULL, '{\"default_col_class\":\"col-sm-12\",\"md_col_class\":\"\",\"lg_col_class\":\"\",\"xl_col_class\":\"\",\"col_class\":\"\",\"default_order\":0,\"sm_order\":0,\"md_order\":0,\"lg_order\":0,\"xl_order\":0}'),
(11, 6, 'Футер', 'pos_11', 'typical', 17, 'div', 'mt-2 mt-sm-0 mb-1 mb-sm-0', NULL, '{\"cut_before\":null,\"default_col_class\":\"\",\"md_col_class\":\"col-md-6\",\"lg_col_class\":\"\",\"xl_col_class\":\"\",\"col_class\":\"\",\"default_order\":0,\"sm_order\":0,\"md_order\":0,\"lg_order\":0,\"xl_order\":0}'),
(17, 8, 'Левый', 'pos_17', 'typical', 10, 'div', NULL, NULL, '{\"cut_before\":null,\"default_col_class\":\"\",\"md_col_class\":\"col-md\",\"lg_col_class\":\"\",\"xl_col_class\":\"\",\"col_class\":\"\",\"default_order\":0,\"sm_order\":0,\"md_order\":0,\"lg_order\":0,\"xl_order\":0}'),
(18, 8, 'Правый', 'pos_18', 'typical', 11, 'div', NULL, NULL, '{\"cut_before\":null,\"default_col_class\":\"\",\"md_col_class\":\"col-md\",\"lg_col_class\":\"\",\"xl_col_class\":\"\",\"col_class\":\"\",\"default_order\":0,\"sm_order\":0,\"md_order\":0,\"lg_order\":0,\"xl_order\":0}'),
(22, 10, 'Инфо', 'pos_22', 'typical', 5, 'div', NULL, NULL, '{\"default_col_class\":\"col-sm\",\"md_col_class\":\"\",\"lg_col_class\":\"\",\"xl_col_class\":\"\",\"col_class\":\"\",\"default_order\":0,\"sm_order\":0,\"md_order\":0,\"lg_order\":0,\"xl_order\":0}'),
(26, 13, 'Верхний ряд', 'pos_26', 'custom', 1, 'div', NULL, '<div class=\"bg-dark text-white\">\r\n    <div class=\"container d-flex justify-content-between flex-nowrap align-items-center\">\r\n        {position}\r\n    </div>\r\n</div>', '{\"default_col_class\":\"\",\"md_col_class\":\"\",\"lg_col_class\":\"\",\"xl_col_class\":\"\",\"col_class\":\"\",\"default_order\":0,\"sm_order\":0,\"md_order\":0,\"lg_order\":0,\"xl_order\":0}'),
(27, 14, 'Лого, поиск, меню пользователя', 'pos_27', 'custom', 2, 'div', NULL, '{position}', '{\"default_col_class\":\"col\",\"sm_col_class\":\"\",\"md_col_class\":\"\",\"lg_col_class\":\"\",\"xl_col_class\":\"\",\"default_order\":0,\"sm_order\":0,\"md_order\":0,\"lg_order\":0,\"xl_order\":0}'),
(29, 15, 'Позиция меню', 'pos_29', 'custom', 4, 'div', NULL, '{position}', '{\"default_col_class\":\"col\",\"sm_col_class\":\"\",\"md_col_class\":\"\",\"lg_col_class\":\"\",\"xl_col_class\":\"\",\"default_order\":0,\"sm_order\":0,\"md_order\":0,\"lg_order\":0,\"xl_order\":0}'),
(30, 16, 'Позиция во всю ширину', 'con_header', 'custom', 6, 'div', NULL, '{position}', '{\"default_col_class\":\"col-sm\",\"md_col_class\":\"\",\"lg_col_class\":\"\",\"xl_col_class\":\"\",\"col_class\":\"\",\"default_order\":0,\"sm_order\":0,\"md_order\":0,\"lg_order\":0,\"xl_order\":0}'),
(31, 14, 'Меню пользователя', 'pos_31', 'custom', 3, 'div', NULL, '<div class=\"ml-auto d-flex align-items-center\">\r\n    {position}\r\n</div>', '{\"default_col_class\":\"col-sm\",\"md_col_class\":\"\",\"lg_col_class\":\"\",\"xl_col_class\":\"\",\"col_class\":\"\",\"default_order\":0,\"sm_order\":0,\"md_order\":0,\"lg_order\":0,\"xl_order\":0}'),
(32, 6, 'Меню', 'pos_32', 'typical', 18, 'div', NULL, NULL, '{\"default_col_class\":\"\",\"md_col_class\":\"col-md-6\",\"lg_col_class\":\"\",\"xl_col_class\":\"\",\"col_class\":\"\",\"default_order\":0,\"sm_order\":0,\"md_order\":0,\"lg_order\":0,\"xl_order\":0}'),
(33, 5, 'Перед телом страницы', 'pos_33', 'typical', 8, 'div', 'mb-3 mb-md-4', NULL, '{\"default_col_class\":\"col-sm-12\",\"md_col_class\":\"\",\"lg_col_class\":\"\",\"xl_col_class\":\"\",\"col_class\":\"\",\"default_order\":0,\"sm_order\":0,\"md_order\":0,\"lg_order\":0,\"xl_order\":0}'),
(34, 4, 'Левая колонка', 'pos_34', 'typical', 12, 'aside', 'mb-3 mb-md-4', NULL, '{\"cut_before\":null,\"default_col_class\":\"\",\"md_col_class\":\"\",\"lg_col_class\":\"col-lg-3\",\"xl_col_class\":\"\",\"col_class\":\"\",\"default_order\":1,\"sm_order\":0,\"md_order\":0,\"lg_order\":0,\"xl_order\":0}'),
(38, 17, 'Левый', 'pos_38', 'typical', 1, 'div', 'mb-3', NULL, '{\"cut_before\":null,\"default_col_class\":\"\",\"md_col_class\":\"col-md-3\",\"lg_col_class\":\"\",\"xl_col_class\":\"\",\"col_class\":\"\",\"default_order\":0,\"sm_order\":0,\"md_order\":0,\"lg_order\":0,\"xl_order\":0}'),
(39, 17, 'Средний', 'pos_39', 'typical', 2, 'div', 'mb-3', NULL, '{\"cut_before\":null,\"default_col_class\":\"\",\"md_col_class\":\"col-md\",\"lg_col_class\":\"\",\"xl_col_class\":\"\",\"col_class\":\"\",\"default_order\":0,\"sm_order\":0,\"md_order\":0,\"lg_order\":0,\"xl_order\":0}'),
(40, 17, 'Правый', 'pos_40', 'typical', 3, 'div', 'mb-3', NULL, '{\"cut_before\":null,\"default_col_class\":\"\",\"md_col_class\":\"col-md\",\"lg_col_class\":\"\",\"xl_col_class\":\"\",\"col_class\":\"\",\"default_order\":0,\"sm_order\":0,\"md_order\":0,\"lg_order\":0,\"xl_order\":0}');
