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

INSERT INTO `{#}controllers` (`title`, `name`, `is_enabled`, `options`, `author`, `url`, `version`, `is_backend`) VALUES
('Конструктор форм', 'forms', 1, '---\nsend_text: >\n  Спасибо! Форма\n  успешно отправлена.\nallow_embed: null\nallow_embed_domain:\ndenied_embed_domain:\nletter: |\n  [subject:Форма: {form_title} - {site}]\r\n  \r\n  Здравствуйте.\r\n  \r\n  С сайта {site} отправлена форма <b>{form_title}</b>.\r\n  \r\n  Данные формы:\r\n  \r\n  {form_data}\r\n  \r\n  --\r\n   C уважением, {site}\r\n   <small>Письмо отправлено автоматически, пожалуйста, не отвечайте на него.</small>\nnotify_text: \'<p>Здравствуйте.</p><p>Отправлена форма <strong>{form_title}</strong>.</p><p><strong>Данные формы:</strong></p><p>{form_data}</p>\'\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1);

INSERT INTO `{#}events` (`event`, `listener`, `ordering`, `is_enabled`) VALUES
('content_before_item', 'forms', 192, 1),
('languages_forms', 'forms', 233, 1);

INSERT INTO `{#}widgets` (`controller`, `name`, `title`, `author`, `url`, `version`, `is_external`) VALUES
('forms', 'form', 'Форма', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL);