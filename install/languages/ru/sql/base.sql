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
(6, NULL, 'Футер', 'div', 'modern', 10, NULL, 'align-items-center flex-wrap', '{\"no_gutters\":1,\"vertical_align\":\"\",\"horizontal_align\":\"\",\"container\":\"container\",\"container_tag\":\"div\",\"container_tag_class\":\"py-2\",\"parrent_tag\":\"footer\",\"parrent_tag_class\":\"icms-footer__bottom\"}'),
(8, 8, 'Вложенный после контента', 'div', 'modern', 8, 'after', 'mt-3 mt-md-4', '{\"no_gutters\":null,\"vertical_align\":\"\",\"horizontal_align\":\"\",\"container\":\"\",\"container_tag\":\"div\",\"container_tag_class\":\"\",\"parrent_tag\":\"\",\"parrent_tag_class\":\"\"}'),
(10, NULL, 'Инфо блок', 'div', 'modern', 4, NULL, NULL, '{\"no_gutters\":1,\"vertical_align\":\"\",\"horizontal_align\":\"\",\"container\":\"container\",\"container_tag\":\"div\",\"container_tag_class\":\"\",\"parrent_tag\":\"div\",\"parrent_tag_class\":\"bg-secondary text-warning\"}'),
(13, NULL, 'Хедер', NULL, 'modern', 1, NULL, NULL, '{\"no_gutters\":null,\"vertical_align\":\"\",\"horizontal_align\":\"\",\"container\":\"\",\"container_tag\":\"div\",\"container_tag_class\":\"\",\"parrent_tag\":\"header\",\"parrent_tag_class\":\"\"}'),
(14, 26, 'Лого + меню пользователя', NULL, 'modern', 2, 'after', NULL, '{\"no_gutters\":null,\"vertical_align\":\"\",\"horizontal_align\":\"\",\"container\":\"container\",\"container_tag\":\"div\",\"container_tag_class\":\"d-flex justify-content-between align-items-center flex-nowrap\",\"parrent_tag\":\"div\",\"parrent_tag_class\":\"icms-header__middle\"}'),
(15, 26, 'Меню', NULL, 'modern', 3, 'after', NULL, '{\"no_gutters\":null,\"vertical_align\":\"\",\"horizontal_align\":\"\",\"container\":\"container\",\"container_tag\":\"div\",\"container_tag_class\":\"\",\"parrent_tag\":\"div\",\"parrent_tag_class\":\"icms-header__bottom border-bottom icms-navbar\"}'),
(16, NULL, 'Ряд во всю ширину', NULL, 'modern', 5, NULL, NULL, '{\"no_gutters\":1,\"vertical_align\":\"\",\"horizontal_align\":\"\",\"container\":\"\",\"container_tag\":\"div\",\"container_tag_class\":\"\",\"parrent_tag\":\"section\",\"parrent_tag_class\":\"\"}'),
(17, NULL, 'Над футером', 'div', 'modern', 9, NULL, 'py-5 mb-n3', '{\"no_gutters\":null,\"vertical_align\":\"\",\"horizontal_align\":\"\",\"container\":\"container\",\"container_tag\":\"div\",\"container_tag_class\":\"border-bottom\",\"parrent_tag\":\"section\",\"parrent_tag_class\":\"icms-footer__middle mt-auto\"}');

INSERT INTO `{#}layout_cols` (`id`, `row_id`, `title`, `name`, `type`, `ordering`, `tag`, `class`, `wrapper`, `options`) VALUES
(8, 4, 'Тело страницы', 'pos_8', 'typical', 9, 'article', 'mb-3 mb-md-4', NULL, '{\"cut_before\":null,\"default_col_class\":\"\",\"md_col_class\":\"\",\"lg_col_class\":\"col-lg\",\"xl_col_class\":\"\",\"col_class\":\"\",\"default_order\":2,\"sm_order\":0,\"md_order\":0,\"lg_order\":0,\"xl_order\":0}'),
(9, 4, 'Правая колонка', 'pos_9', 'typical', 13, 'aside', 'mb-3 mb-md-4', NULL, '{\"cut_before\":null,\"default_col_class\":\"\",\"md_col_class\":\"\",\"lg_col_class\":\"col-lg-4\",\"xl_col_class\":\"\",\"col_class\":\"\",\"default_order\":3,\"sm_order\":0,\"md_order\":0,\"lg_order\":0,\"xl_order\":0}'),
(10, 5, 'Позиция глубиномера', 'pos_10', 'typical', 7, 'div', 'd-flex justify-content-between align-items-center', NULL, '{\"default_col_class\":\"col-sm-12\",\"md_col_class\":\"\",\"lg_col_class\":\"\",\"xl_col_class\":\"\",\"col_class\":\"\",\"default_order\":0,\"sm_order\":0,\"md_order\":0,\"lg_order\":0,\"xl_order\":0}'),
(11, 6, 'Футер', 'pos_11', 'typical', 17, 'div', 'mt-2 mt-sm-0 mb-1 mb-sm-0', NULL, '{\"cut_before\":null,\"default_col_class\":\"\",\"md_col_class\":\"col-md-6\",\"lg_col_class\":\"\",\"xl_col_class\":\"\",\"col_class\":\"\",\"default_order\":0,\"sm_order\":0,\"md_order\":0,\"lg_order\":0,\"xl_order\":0}'),
(17, 8, 'Левый', 'pos_17', 'typical', 10, 'div', NULL, NULL, '{\"cut_before\":null,\"default_col_class\":\"\",\"md_col_class\":\"col-md\",\"lg_col_class\":\"\",\"xl_col_class\":\"\",\"col_class\":\"\",\"default_order\":0,\"sm_order\":0,\"md_order\":0,\"lg_order\":0,\"xl_order\":0}'),
(18, 8, 'Правый', 'pos_18', 'typical', 11, 'div', NULL, NULL, '{\"cut_before\":null,\"default_col_class\":\"\",\"md_col_class\":\"col-md\",\"lg_col_class\":\"\",\"xl_col_class\":\"\",\"col_class\":\"\",\"default_order\":0,\"sm_order\":0,\"md_order\":0,\"lg_order\":0,\"xl_order\":0}'),
(22, 10, 'Инфо', 'pos_22', 'typical', 5, 'div', NULL, NULL, '{\"default_col_class\":\"col-sm\",\"md_col_class\":\"\",\"lg_col_class\":\"\",\"xl_col_class\":\"\",\"col_class\":\"\",\"default_order\":0,\"sm_order\":0,\"md_order\":0,\"lg_order\":0,\"xl_order\":0}'),
(26, 13, 'Верхний ряд', 'pos_26', 'custom', 1, 'div', NULL, '<div class=\"icms-header__top\">\r\n    <div class=\"container d-flex justify-content-end flex-nowrap align-items-center\">\r\n        {position}\r\n    </div>\r\n</div>', '{\"cut_before\":null,\"default_col_class\":\"\",\"md_col_class\":\"\",\"lg_col_class\":\"\",\"xl_col_class\":\"\",\"col_class\":\"\",\"default_order\":0,\"sm_order\":0,\"md_order\":0,\"lg_order\":0,\"xl_order\":0}'),
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

DROP TABLE IF EXISTS `{#}typograph_presets`;
CREATE TABLE `{#}typograph_presets` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `options` text DEFAULT NULL COMMENT 'Опции',
  `title` varchar(100) DEFAULT NULL COMMENT 'Название пресета',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Пресеты для типографа';

INSERT INTO `{#}typograph_presets` (`id`, `options`, `title`) VALUES
(1, '---\nis_auto_br: null\nis_auto_link_mode: null\nbuild_redirect_link: 1\nis_process_callback: 1\nautoreplace:\n  - \n    search: +/-\n    replace: ±\n  - \n    search: (c)\n    replace: ©\n  - \n    search: (с)\n    replace: ©\n  - \n    search: (r)\n    replace: ®\n  - \n    search: (C)\n    replace: ©\n  - \n    search: (С)\n    replace: ©\n  - \n    search: (R)\n    replace: ®\nallowed_tags:\n  - p\n  - br\n  - span\n  - div\n  - a\n  - img\n  - input\n  - label\n  - b\n  - i\n  - u\n  - s\n  - del\n  - em\n  - strong\n  - sup\n  - sub\n  - hr\n  - font\n  - ul\n  - ol\n  - li\n  - table\n  - tbody\n  - thead\n  - tfoot\n  - tr\n  - td\n  - th\n  - h2\n  - h3\n  - h4\n  - h5\n  - pre\n  - code\n  - blockquote\n  - picture\n  - video\n  - source\n  - audio\n  - youtube\n  - facebook\n  - figure\n  - figcaption\n  - iframe\n  - spoiler\n  - cite\n  - footer\n  - address\ncallback:\n  p: \"\"\n  br: \"\"\n  span: \"\"\n  div: \"\"\n  a: typograph|linkRedirectPrefix\n  img: typograph|parseImg\n  input: \"\"\n  label: \"\"\n  b: \"\"\n  i: \"\"\n  u: \"\"\n  s: \"\"\n  del: \"\"\n  em: \"\"\n  strong: \"\"\n  sup: \"\"\n  sub: \"\"\n  hr: \"\"\n  font: \"\"\n  ul: \"\"\n  ol: \"\"\n  li: \"\"\n  table: \"\"\n  tbody: \"\"\n  thead: \"\"\n  tfoot: \"\"\n  tr: \"\"\n  td: \"\"\n  th: \"\"\n  h2: \"\"\n  h3: \"\"\n  h4: \"\"\n  h5: \"\"\n  pre: typograph|parsePre\n  code: typograph|parseCode\n  blockquote: \"\"\n  picture: \"\"\n  video: \"\"\n  source: \"\"\n  audio: \"\"\n  youtube: typograph|parseYouTubeVideo\n  facebook: typograph|parseFacebookVideo\n  figure: \"\"\n  figcaption: \"\"\n  iframe: typograph|parseIframe\n  spoiler: typograph|parseSpoiler\n  cite: \"\"\n  footer: \"\"\n  address: \"\"\ntags:\n  p:\n    - \n      type: \'#text\'\n      name: style\n      params: \"\"\n    - \n      type: \'#text\'\n      name: class\n      params: \"\"\n  br: [ ]\n  span:\n    - \n      type: \'#text\'\n      name: style\n      params: \"\"\n    - \n      type: \'#text\'\n      name: class\n      params: \"\"\n  div:\n    - \n      type: \'#text\'\n      name: style\n      params: \"\"\n    - \n      type: \'#text\'\n      name: class\n      params: \"\"\n  a:\n    - \n      type: \'#link\'\n      name: href\n      params: \"\"\n    - \n      type: \'#text\'\n      name: name\n      params: \"\"\n    - \n      type: \'#text\'\n      name: target\n      params: \"\"\n    - \n      type: \'#text\'\n      name: class\n      params: \"\"\n  img:\n    - \n      type: \'#image\'\n      name: src\n      params: \"\"\n    - \n      type: \'#text\'\n      name: style\n      params: \"\"\n    - \n      type: \'#text\'\n      name: alt\n      params: \"\"\n    - \n      type: \'#text\'\n      name: title\n      params: \"\"\n    - \n      type: \'#array\'\n      name: align\n      params: |\n        right\n        left\n        center\n    - \n      type: \'#int\'\n      name: width\n      params: \"\"\n    - \n      type: \'#int\'\n      name: height\n      params: \"\"\n    - \n      type: \'#int\'\n      name: hspace\n      params: \"\"\n    - \n      type: \'#int\'\n      name: vspace\n      params: \"\"\n    - \n      type: \'#text\'\n      name: class\n      params: \"\"\n  input:\n    - \n      type: \'#text\'\n      name: tabindex\n      params: \"\"\n    - \n      type: \'#text\'\n      name: type\n      params: \"\"\n    - \n      type: \'#text\'\n      name: id\n      params: \"\"\n  label:\n    - \n      type: \'#text\'\n      name: class\n      params: \"\"\n    - \n      type: \'#text\'\n      name: for\n      params: \"\"\n  b: [ ]\n  i: [ ]\n  u: [ ]\n  s: [ ]\n  del: [ ]\n  em:\n    - \n      type: \'#text\'\n      name: class\n      params: \"\"\n  strong: [ ]\n  sup: [ ]\n  sub: [ ]\n  hr: [ ]\n  font: [ ]\n  ul: [ ]\n  ol: [ ]\n  li: [ ]\n  table:\n    - \n      type: \'#int\'\n      name: width\n      params: \"\"\n    - \n      type: \'#int\'\n      name: height\n      params: \"\"\n    - \n      type: \'#int\'\n      name: cellpadding\n      params: \"\"\n    - \n      type: \'#int\'\n      name: cellspacing\n      params: \"\"\n    - \n      type: \'#int\'\n      name: border\n      params: \"\"\n    - \n      type: \'#text\'\n      name: style\n      params: \"\"\n    - \n      type: \'#text\'\n      name: align\n      params: \"\"\n    - \n      type: \'#text\'\n      name: valign\n      params: \"\"\n  tbody: [ ]\n  thead: [ ]\n  tfoot: [ ]\n  tr: [ ]\n  td:\n    - \n      type: \'#int\'\n      name: width\n      params: \"\"\n    - \n      type: \'#int\'\n      name: height\n      params: \"\"\n    - \n      type: \'#text\'\n      name: style\n      params: \"\"\n    - \n      type: \'#text\'\n      name: align\n      params: \"\"\n    - \n      type: \'#text\'\n      name: valign\n      params: \"\"\n    - \n      type: \'#int\'\n      name: colspan\n      params: \"\"\n    - \n      type: \'#int\'\n      name: rowspan\n      params: \"\"\n  th:\n    - \n      type: \'#int\'\n      name: width\n      params: \"\"\n    - \n      type: \'#int\'\n      name: height\n      params: \"\"\n    - \n      type: \'#text\'\n      name: style\n      params: \"\"\n    - \n      type: \'#text\'\n      name: align\n      params: \"\"\n    - \n      type: \'#text\'\n      name: valign\n      params: \"\"\n    - \n      type: \'#int\'\n      name: colspan\n      params: \"\"\n    - \n      type: \'#int\'\n      name: rowspan\n      params: \"\"\n  h2:\n    - \n      type: \'#text\'\n      name: id\n      params: \"\"\n    - \n      type: \'#text\'\n      name: class\n      params: \"\"\n  h3:\n    - \n      type: \'#text\'\n      name: id\n      params: \"\"\n    - \n      type: \'#text\'\n      name: class\n      params: \"\"\n  h4:\n    - \n      type: \'#text\'\n      name: id\n      params: \"\"\n    - \n      type: \'#text\'\n      name: class\n      params: \"\"\n  h5:\n    - \n      type: \'#text\'\n      name: id\n      params: \"\"\n    - \n      type: \'#text\'\n      name: class\n      params: \"\"\n  pre:\n    - \n      type: \'#text\'\n      name: class\n      params: \"\"\n  code:\n    - \n      type: \'#text\'\n      name: type\n      params: \"\"\n  blockquote: [ ]\n  picture: [ ]\n  video:\n    - \n      type: \'#text\'\n      name: controls\n      params: \"\"\n    - \n      type: \'#text\'\n      name: class\n      params: \"\"\n    - \n      type: \'#int\'\n      name: width\n      params: \"\"\n    - \n      type: \'#int\'\n      name: height\n      params: \"\"\n  source:\n    - \n      type: \'#image\'\n      name: src\n      params: \"\"\n    - \n      type: \'#text\'\n      name: srcset\n      params: \"\"\n    - \n      type: \'#text\'\n      name: type\n      params: \"\"\n    - \n      type: \'#text\'\n      name: media\n      params: \"\"\n  audio:\n    - \n      type: \'#image\'\n      name: src\n      params: \"\"\n    - \n      type: \'#text\'\n      name: srcset\n      params: \"\"\n    - \n      type: \'#text\'\n      name: type\n      params: \"\"\n    - \n      type: \'#text\'\n      name: media\n      params: \"\"\n  youtube: [ ]\n  facebook: [ ]\n  figure:\n    - \n      type: \'#text\'\n      name: style\n      params: \"\"\n    - \n      type: \'#text\'\n      name: class\n      params: \"\"\n  figcaption:\n    - \n      type: \'#text\'\n      name: style\n      params: \"\"\n    - \n      type: \'#text\'\n      name: class\n      params: \"\"\n  iframe:\n    - \n      type: \'#int\'\n      name: width\n      params: \"\"\n    - \n      type: \'#int\'\n      name: height\n      params: \"\"\n    - \n      type: \'#text\'\n      name: style\n      params: \"\"\n    - \n      type: \'#int\'\n      name: frameborder\n      params: \"\"\n    - \n      type: \'#text\'\n      name: allowfullscreen\n      params: \"\"\n    - \n      type: \'#domain\'\n      name: src\n      params: |\n        youtube.com\n        yandex.ru\n        rutube.ru\n        vimeo.com\n        vk.com\n        my.mail.ru\n        facebook.com\n  spoiler:\n    - \n      type: \'#text\'\n      name: title\n      params: \"\"\n  cite: [ ]\n  footer:\n    - \n      type: \'#text\'\n      name: class\n      params: \"\"\n  address:\n    - \n      type: \'#text\'\n      name: class\n      params: \"\"\n', 'По умолчанию'),
(2, '---\nis_auto_br: null\nis_auto_link_mode: null\nbuild_redirect_link: 1\nis_process_callback: 1\nautoreplace: [ ]\nallowed_tags:\n  - p\n  - br\n  - a\n  - img\n  - b\n  - i\n  - u\n  - s\n  - strong\n  - strike\n  - ul\n  - ol\n  - li\n  - blockquote\n  - iframe\ncallback:\n  p: \"\"\n  br: \"\"\n  a: typograph|linkRedirectPrefix\n  img: typograph|parseImg\n  b: \"\"\n  i: \"\"\n  u: \"\"\n  s: \"\"\n  strong: \"\"\n  strike: \"\"\n  ul: \"\"\n  ol: \"\"\n  li: \"\"\n  blockquote: \"\"\n  iframe: typograph|parseIframe\ntags:\n  p: [ ]\n  br: [ ]\n  a:\n    - \n      type: \'#link\'\n      name: href\n      params: \"\"\n    - \n      type: \'#text\'\n      name: target\n      params: \"\"\n  img:\n    - \n      type: \'#image\'\n      name: src\n      params: \"\"\n    - \n      type: \'#text\'\n      name: alt\n      params: \"\"\n    - \n      type: \'#text\'\n      name: title\n      params: \"\"\n    - \n      type: \'#array\'\n      name: align\n      params: |\n        right\n        left\n        center\n    - \n      type: \'#int\'\n      name: width\n      params: \"\"\n    - \n      type: \'#int\'\n      name: height\n      params: \"\"\n    - \n      type: \'#text\'\n      name: class\n      params: \"\"\n  b: [ ]\n  i: [ ]\n  u: [ ]\n  s: [ ]\n  strong: [ ]\n  strike: [ ]\n  ul: [ ]\n  ol: [ ]\n  li: [ ]\n  blockquote: [ ]\n  iframe:\n    - \n      type: \'#int\'\n      name: width\n      params: \"\"\n    - \n      type: \'#int\'\n      name: height\n      params: \"\"\n    - \n      type: \'#text\'\n      name: style\n      params: \"\"\n    - \n      type: \'#int\'\n      name: frameborder\n      params: \"\"\n    - \n      type: \'#text\'\n      name: allowfullscreen\n      params: \"\"\n    - \n      type: \'#domain\'\n      name: src\n      params: |\n        youtube.com\n        yandex.ru\n        rutube.ru\n        vimeo.com\n        vk.com\n        my.mail.ru\n        facebook.com\n', 'Для личных сообщений'),
(3, '---\nis_auto_br: 1\nis_auto_link_mode: null\nbuild_redirect_link: 1\nbuild_smiles: 1\nis_process_callback: 1\nautoreplace: [ ]\nallowed_tags:\n  - p\n  - br\n  - a\n  - b\n  - i\n  - u\n  - s\n  - strong\n  - strike\n  - ul\n  - ol\n  - li\ncallback:\n  p: \"\"\n  br: \"\"\n  a: typograph|linkRedirectPrefix\n  b: \"\"\n  i: \"\"\n  u: \"\"\n  s: \"\"\n  strong: \"\"\n  strike: \"\"\n  ul: \"\"\n  ol: \"\"\n  li: \"\"\ntags:\n  p: [ ]\n  br: [ ]\n  a:\n    - \n      type: \'#link\'\n      name: href\n      params: \"\"\n    - \n      type: \'#text\'\n      name: target\n      params: \"\"\n  b: [ ]\n  i: [ ]\n  u: [ ]\n  s: [ ]\n  strong: [ ]\n  strike: [ ]\n  ul: [ ]\n  ol: [ ]\n  li: [ ]\n', 'Для Markitup редактора');

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

DROP TABLE IF EXISTS `{#}activity`;
CREATE TABLE `{#}activity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `group_id` int(11) unsigned DEFAULT NULL,
  `subject_title` varchar(140) DEFAULT NULL,
  `subject_id` int(11) unsigned DEFAULT NULL,
  `subject_url` varchar(250) DEFAULT NULL,
  `reply_url` varchar(250) DEFAULT NULL,
  `images` text,
  `images_count` int(11) unsigned DEFAULT NULL,
  `date_pub` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_private` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_parent_hidden` tinyint(1) unsigned DEFAULT NULL,
  `is_pub` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `type_id` (`type_id`),
  KEY `user_id` (`user_id`),
  KEY `date_pub` (`date_pub`),
  KEY `is_private` (`is_private`),
  KEY `group_id` (`group_id`),
  KEY `is_parent_hidden` (`is_parent_hidden`),
  KEY `is_pub` (`is_pub`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Лента активности';

DROP TABLE IF EXISTS `{#}activity_types`;
CREATE TABLE `{#}activity_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `is_enabled` tinyint(1) unsigned DEFAULT '1',
  `controller` varchar(32) NOT NULL,
  `name` varchar(32) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(200) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `is_enabled` (`is_enabled`),
  KEY `controller` (`controller`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Типы записей в ленте активности';

INSERT INTO `{#}activity_types` (`id`, `is_enabled`, `controller`, `name`, `title`, `description`) VALUES
(1, 1, 'content', 'add.pages', 'Добавление страниц', 'добавляет страницу %s'),
(2, 1, 'comments', 'vote.comment', 'Оценка комментария', 'оценил комментарий на странице %s'),
(7, 1, 'users', 'friendship', 'Дружба', 'и %s становятся друзьями'),
(8, 1, 'users', 'signup', 'Регистрация', 'регистрируется. Приветствуем!'),
(10, 1, 'groups', 'join', 'Вступление в группу', 'вступает в группу %s'),
(11, 1, 'groups', 'leave', 'Выход из группы', 'выходит из группы %s'),
(12, 1, 'users', 'status', 'Изменение статуса', '&rarr; %s'),
(18, 1, 'photos', 'add.photos', 'Добавление фотографий', 'загружает фото в альбом %s'),
(19, 1, 'users', 'avatar', 'Изменение аватара', 'изменяет аватар'),
(20, 1, 'subscriptions', 'subscribe', 'Подписка на контент', 'подписывается на список %s');

DROP TABLE IF EXISTS `{#}comments`;
CREATE TABLE `{#}comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned DEFAULT NULL COMMENT 'ID родительского комментария',
  `level` tinyint(4) unsigned DEFAULT NULL COMMENT 'Уровень вложенности',
  `ordering` int(11) unsigned DEFAULT NULL COMMENT 'Порядковый номер в дереве',
  `user_id` int(11) unsigned DEFAULT NULL COMMENT 'ID автора',
  `date_pub` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата публикации',
  `date_last_modified` timestamp NULL DEFAULT NULL COMMENT 'Дата изменения',
  `target_controller` varchar(32) DEFAULT NULL COMMENT 'Контроллер комментируемого контента',
  `target_subject` varchar(32) DEFAULT NULL COMMENT 'Объект комментирования',
  `target_id` int(11) unsigned DEFAULT NULL COMMENT 'ID объекта комментирования',
  `target_url` varchar(250) DEFAULT NULL COMMENT 'URL объекта комментирования',
  `target_title` varchar(100) DEFAULT NULL COMMENT 'Заголовок объекта комментирования',
  `author_name` varchar(100) DEFAULT NULL COMMENT 'Имя автора (гостя)',
  `author_email` varchar(100) DEFAULT NULL COMMENT 'E-mail автора (гостя)',
  `author_ip` varbinary(16) DEFAULT NULL COMMENT 'ip адрес',
  `content` text COMMENT 'Текст комментария',
  `content_html` text COMMENT 'Текст после типографа',
  `is_deleted` tinyint(1) unsigned DEFAULT NULL COMMENT 'Комментарий удален?',
  `is_private` tinyint(1) unsigned DEFAULT '0' COMMENT 'Только для друзей?',
  `rating` int(11) NOT NULL DEFAULT '0',
  `is_approved` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `target_id` (`target_id`,`target_controller`,`target_subject`,`ordering`),
  KEY `author_ip` (`author_ip`),
  KEY `is_approved` (`is_approved`,`is_deleted`,`date_pub`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Комментарии пользователей';

DROP TABLE IF EXISTS `{#}comments_rating`;
CREATE TABLE `{#}comments_rating` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `score` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comment_id` (`comment_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}comments_tracks`;
CREATE TABLE `{#}comments_tracks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `target_controller` varchar(32) DEFAULT NULL,
  `target_subject` varchar(32) DEFAULT NULL,
  `target_id` int(11) unsigned DEFAULT NULL,
  `target_url` varchar(250) DEFAULT NULL,
  `target_title` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `target_id` (`target_id`,`target_controller`,`target_subject`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Подписки пользователей на новые комментарии';

DROP TABLE IF EXISTS `{#}content_datasets`;
CREATE TABLE `{#}content_datasets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ctype_id` int(11) unsigned DEFAULT NULL COMMENT 'ID типа контента',
  `name` varchar(32) NOT NULL COMMENT 'Название набора',
  `title` varchar(100) DEFAULT NULL COMMENT 'Заголовок набора',
  `description` text COMMENT 'Описание',
  `ordering` int(11) unsigned DEFAULT NULL COMMENT 'Порядковый номер',
  `is_visible` tinyint(1) unsigned DEFAULT NULL COMMENT 'Отображать набор на сайте?',
  `filters` text COMMENT 'Массив фильтров набора',
  `sorting` text COMMENT 'Массив правил сортировки',
  `index` varchar(40) DEFAULT NULL COMMENT 'Название используемого индекса',
  `groups_view` text COMMENT 'Показывать группам',
  `groups_hide` text COMMENT 'Скрывать от групп',
  `seo_keys` varchar(256) DEFAULT NULL,
  `seo_desc` varchar(256) DEFAULT NULL,
  `seo_title` varchar(256) DEFAULT NULL,
  `seo_h1` varchar(256) DEFAULT NULL,
  `cats_view` text COMMENT 'Показывать в категориях',
  `cats_hide` text COMMENT 'Не показывать в категориях',
  `max_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `target_controller` varchar(32) DEFAULT NULL,
  `list` text,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `ctype_id` (`ctype_id`,`ordering`),
  KEY `index` (`index`),
  KEY `target_controller` (`target_controller`,`ordering`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Наборы для типов контента';

DROP TABLE IF EXISTS `{#}content_folders`;
CREATE TABLE `{#}content_folders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ctype_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `title` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`ctype_id`,`title`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Папки для записей типов контента';

DROP TABLE IF EXISTS `{#}content_relations`;
CREATE TABLE `{#}content_relations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(256) DEFAULT NULL,
  `target_controller` varchar(32) NOT NULL DEFAULT 'content',
  `ctype_id` int(11) unsigned DEFAULT NULL,
  `child_ctype_id` int(11) unsigned DEFAULT NULL,
  `layout` varchar(32) DEFAULT NULL,
  `options` text,
  `seo_keys` varchar(256) DEFAULT NULL,
  `seo_desc` varchar(256) DEFAULT NULL,
  `seo_title` varchar(256) DEFAULT NULL,
  `ordering` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ctype_id` (`ctype_id`,`ordering`),
  KEY `child_ctype_id` (`child_ctype_id`,`target_controller`,`ordering`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Свзяи типов контента';

DROP TABLE IF EXISTS `{#}content_relations_bind`;
CREATE TABLE `{#}content_relations_bind` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_ctype_id` int(11) unsigned DEFAULT NULL,
  `parent_item_id` int(11) unsigned DEFAULT NULL,
  `child_ctype_id` int(11) unsigned DEFAULT NULL,
  `child_item_id` int(11) unsigned DEFAULT NULL,
  `target_controller` varchar(32) NOT NULL DEFAULT 'content',
  PRIMARY KEY (`id`),
  KEY `parent_ctype_id` (`parent_ctype_id`),
  KEY `child_ctype_id` (`child_ctype_id`),
  KEY `parent_item_id` (`parent_item_id`,`target_controller`),
  KEY `child_item_id` (`child_item_id`,`target_controller`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}content_types`;
CREATE TABLE `{#}content_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL COMMENT 'Название',
  `name` varchar(32) NOT NULL COMMENT 'Системное имя',
  `description` text COMMENT 'Описание',
  `ordering` int(11) DEFAULT NULL,
  `is_enabled` tinyint(1) UNSIGNED DEFAULT '1',
  `is_date_range` tinyint(1) unsigned DEFAULT NULL COMMENT 'Разрешить управление сроком публикации?',
  `is_cats` tinyint(1) unsigned DEFAULT NULL COMMENT 'Категории включены?',
  `is_cats_recursive` tinyint(1) unsigned DEFAULT NULL COMMENT 'Сквозной просмотр категорий?',
  `is_folders` tinyint(1) unsigned DEFAULT NULL COMMENT 'Включены личные папки?',
  `is_in_groups` tinyint(1) unsigned DEFAULT NULL COMMENT 'Создание в группах',
  `is_in_groups_only` tinyint(1) unsigned DEFAULT NULL COMMENT 'Создание только в группах',
  `is_comments` tinyint(1) unsigned DEFAULT NULL COMMENT 'Комментарии включены?',
  `is_rating` tinyint(1) unsigned DEFAULT NULL COMMENT 'Разрешить рейтинг?',
  `is_tags` tinyint(1) unsigned DEFAULT NULL COMMENT 'Разрешить теги?',
  `is_auto_keys` tinyint(1) unsigned DEFAULT NULL COMMENT 'Автоматическая генерация ключевых слов?',
  `is_auto_desc` tinyint(1) unsigned DEFAULT NULL COMMENT 'Автоматическая генерация описания?',
  `is_auto_url` tinyint(1) unsigned DEFAULT NULL COMMENT 'Генерировать URL из заголовка?',
  `is_fixed_url` tinyint(1) unsigned DEFAULT NULL COMMENT 'Не изменять URL при изменении записи?',
  `url_pattern` varchar(255) DEFAULT '{id}-{title}',
  `options` text COMMENT 'Массив опций',
  `labels` text COMMENT 'Массив заголовков',
  `seo_keys` varchar(256) DEFAULT NULL COMMENT 'Ключевые слова',
  `seo_desc` varchar(256) DEFAULT NULL COMMENT 'Описание',
  `seo_title` varchar(256) DEFAULT NULL,
  `item_append_html` text,
  `is_fixed` tinyint(1) unsigned DEFAULT NULL COMMENT 'Нельзя удалить из админки',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `ordering` (`ordering`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Типы контента';

INSERT INTO `{#}content_types` (`id`, `title`, `name`, `description`, `is_date_range`, `is_cats`, `is_cats_recursive`, `is_folders`, `is_in_groups`, `is_in_groups_only`, `is_comments`, `is_rating`, `is_tags`, `is_auto_keys`, `is_auto_desc`, `is_auto_url`, `is_fixed_url`, `url_pattern`, `options`, `labels`, `seo_keys`, `seo_desc`, `seo_title`, `item_append_html`, `is_fixed`) VALUES
(1, 'Страницы', 'pages', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '{id}-{title}', '---\nis_cats_change: null\nis_cats_open_root: null\nis_cats_only_last: null\nis_tags_in_list: null\nis_tags_in_item: null\nis_rss: null\nlist_on: null\nprofile_on: null\nlist_show_filter: null\nlist_expand_filter: null\nitem_on: 1\nseo_title_pattern:\nseo_keys_pattern: \'{content|string_get_meta_keywords}\'\nseo_desc_pattern: \'{content|string_get_meta_description}\'\n', '---\none: страница\ntwo: страницы\nmany: страниц\ncreate: страницу\n', NULL, NULL, NULL, NULL, 1),
(7, 'Фотоальбомы', 'albums', '<p>Альбомы с фотографиями пользователей</p>', NULL, NULL, NULL, NULL, 1, NULL, 1, 1, 1, 1, 1, 1, 1, '{id}-{title}', '---\nis_cats_change: null\nis_cats_open_root: null\nis_cats_only_last: null\nis_show_cats: null\nis_tags_in_list: null\nis_tags_in_item: 1\nis_rss: 1\nlist_on: 1\nprofile_on: 1\nlist_show_filter: null\nlist_expand_filter: null\nitem_on: 1\nis_cats_keys: null\nis_cats_desc: null\nis_cats_auto_url: 1\nseo_title_pattern:\nseo_keys_pattern: \'{content|string_get_meta_keywords}\'\nseo_desc_pattern: \'{content|string_get_meta_description}\'\n', '---\none: альбом\ntwo: альбома\nmany: альбомов\ncreate: фотоальбом\n', NULL, NULL, NULL, NULL, 1);

DROP TABLE IF EXISTS `{#}controllers`;
CREATE TABLE `{#}controllers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `name` varchar(32) NOT NULL COMMENT 'Системное имя',
  `slug` varchar(64) DEFAULT NULL,
  `is_enabled` tinyint(1) unsigned DEFAULT '1' COMMENT 'Включен?',
  `options` text COMMENT 'Массив настроек',
  `author` varchar(128) NOT NULL COMMENT 'Имя автора',
  `url` varchar(250) DEFAULT NULL COMMENT 'Сайт автора',
  `version` varchar(8) NOT NULL COMMENT 'Версия',
  `is_backend` tinyint(1) unsigned DEFAULT NULL COMMENT 'Есть админка?',
  `is_external` tinyint(1) unsigned DEFAULT NULL COMMENT 'Сторонний компонент',
  `files` text COMMENT 'Список файлов контроллера (для стороних компонентов)',
  `addon_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID дополнения в официальном каталоге',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Компоненты';

INSERT INTO `{#}controllers` (`id`, `title`, `name`, `is_enabled`, `options`, `author`, `url`, `version`, `is_backend`) VALUES
(1, 'Панель управления', 'admin', 1, '---\ndashboard_order:\n  stat: 0\n  activity: 1\n  news: 2\n  sysinfo: 3\n  resources: 4\n  users_online: 5\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 0),
(2, 'Контент', 'content', 1, NULL, 'InstantCMS Team', 'https://instantcms.ru', '2.0', 0),
(3, 'Профили пользователей', 'users', 1, '---\nis_ds_online: 1\nis_ds_rating: 1\nis_ds_popular: 1\nis_filter: 1\nis_auth_only: null\nis_status: 1\nis_wall: 1\nis_themes_on: 1\nmax_tabs: 6\nis_friends_on: 1\nis_karma: 1\nis_karma_comments: 1\nkarma_time: 30\nrestricted_slugs: |\n  *admin*\r\n  *moder*\nlimit: 15\nlist_allowed: [ ]\nshow_user_groups: 1\nshow_reg_data: 1\nshow_last_visit: 1\nprofile_max_friends_count: 10\nseo_keys:\nseo_desc:\ntag_title:\ntag_desc:\ntag_h1:\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(4, 'Комментарии', 'comments', 1, '---\ndisable_icms_comments: null\nis_guests: 1\nguest_ip_delay: 1\nrestricted_ips: \"\"\ndim_negative: 1\nupdate_user_rating: 1\nlimit: 20\nseo_keys: \"\"\nseo_desc: \"\"\nis_guests_moderate: 1\nrestricted_emails: \"\"\nrestricted_names: \"\"\nlimit_nesting: 5\nshow_author_email: 1\neditor: \"4\"\neditor_presets: null\nshow_list:\n  - \"0\"\ntypograph_id: \"1\"\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(5, 'Личные сообщения', 'messages', 1, '---\nlimit: 10\ngroups_allowed: [ ]\neditor: \"2\"\neditor_presets: null\ntime_delete_old: 0\nrealtime_mode: ajax\nrefresh_time: 15\nsocket_host: \"\"\nsocket_port: 3000\nuse_queue: null\nis_enable_pm: 1\nis_contact_first_select: null\ntypograph_id: \"2\"\nemail_template: \"\"\n', 'InstantCMS Team', 'https://instantcms.ru/', '2.0', 1),
(6, 'Авторизация и регистрация', 'auth', 1, '---\nis_reg_enabled: 1\nreg_reason: >\n  К сожалению, нам пока\n  не нужны новые\n  пользователи\nis_reg_invites: null\nreg_captcha: null\nverify_email: null\nverify_exp: 48\nauth_captcha: null\nrestricted_emails: |\n  *@shitmail.me\r\n  *@mailspeed.ru\r\n  *@temp-mail.ru\r\n  *@guerrillamail.com\r\n  *@12minutemail.com\r\n  *@mytempemail.com\r\n  *@spamobox.com\r\n  *@disposableinbox.com\r\n  *@filzmail.com\r\n  *@freemail.ms\r\n  *@anonymbox.com\r\n  *@lroid.com\r\n  *@yopmail.com\r\n  *@TempEmail.net\r\n  *@spambog.com\r\n  *@mailforspam.com\r\n  *@spam.su\r\n  *@no-spam.ws\r\n  *@mailinator.com\r\n  *@spamavert.com\r\n  *@trashcanmail.com\nrestricted_names: |\n  admin*\r\n  админ*\r\n  модератор\r\n  moderator\nrestricted_ips:\nis_invites: 1\nis_invites_strict: 1\ninvites_period: 7\ninvites_qty: 3\ninvites_min_karma: 0\ninvites_min_rating: 0\ninvites_min_days: 0\nreg_auto_auth: 1\nfirst_auth_redirect: profileedit\nauth_redirect: none\ndef_groups:\n  - 3\nis_site_only_auth_users: null\nguests_allow_controllers:\n  - auth\n  - geo\nseo_keys:\nseo_desc:\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(7, 'Лента активности', 'activity', 1, '---\ntypes:\n  - 10\n  - 11\n  - 17\n  - 16\n  - 14\n  - 13\n  - 18\n  - 7\n  - 19\n  - 12\n  - 8\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(8, 'Группы', 'groups', 1, '---\nis_ds_rating: 1\nis_ds_popular: 1\nis_wall: 1\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(10, 'Рейтинг', 'rating', 1, '---\nis_hidden: 1\nis_show: 1\nallow_guest_vote: null\ntemplate: widget\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(11, 'Стена', 'wall', 1, '---\nlimit: 15\norder_by: date_last_reply\nshow_entries: 5\neditor: \"4\"\neditor_presets: null\ntypograph_id: \"1\"\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(12, 'Капча reCAPTCHA', 'recaptcha', 1, '---\npublic_key:\nprivate_key:\ntheme: light\nlang: ru\nsize: normal\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(13, 'Модерация', 'moderation', 1, NULL, 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(14, 'Теги', 'tags', 1, '---\nordering: frequency\nstyle: cloud\nmax_fs: 22\nmin_fs: 12\nmin_freq: 0\nmin_len: 0\nlimit: 10\ncolors:\nshuffle: 1\nseo_keys:\nseo_desc:\nseo_title_pattern:\nseo_desc_pattern:\nseo_h1_pattern:\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(15, 'Генератор RSS', 'rss', 1, NULL, 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(16, 'Генератор карты сайта и robots.txt', 'sitemap', 1, '---\nsources:\n  content|pages: 1\n  content|albums: 1\n  content|articles: 1\n  content|posts: 1\n  content|board: 1\n  content|news: 1\n  frontpage|root: 1\n  groups|profiles: 1\n  users|profiles: 1\nshow_lastmod: 1\nshow_changefreq: 1\ndefault_changefreq: daily\nshow_priority: 1\nrobots: |\n  User-agent: *\r\n  Disallow:\ngenerate_html_sitemap: null\nchangefreq:\n  content:\n    pages:\n    albums:\n    articles:\n    posts:\n    board:\n    news:\n  frontpage:\n    root:\n  groups:\n    profiles:\n  users:\n    profiles:\npriority:\n  content:\n    pages:\n    albums:\n    articles:\n    posts:\n    board:\n    news:\n  frontpage:\n    root: 1.0\n  groups:\n    profiles: 0.8\n  users:\n    profiles: 0.8\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(17, 'Поиск', 'search', 1, '---\nctypes:\n  - articles\n  - posts\n  - albums\n  - board\n  - news\nperpage: 15\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(18, 'Фотоальбомы', 'photos', 1, '---\nsizes:\n  - normal\n  - small\n  - big\nis_origs: 1\npreset: big\npreset_small: normal\ntypes: |\n  1 | Фото\n  2 | Векторы\n  3 | Иллюстрации\nordering: date_pub\norderto: desc\nlimit: 20\ndownload_view:\n  normal: [ ]\n  micro: [ ]\n  small: [ ]\n  content_list_small: [ ]\n  content_list: [ ]\n  big: [ ]\n  content_item: [ ]\n  original: [ ]\ndownload_hide:\n  normal: null\n  micro: null\n  small: null\n  content_list_small: null\n  content_list: null\n  big: null\n  content_item: null\n  original:\n    - \"1\"\n    - \"3\"\n    - \"4\"\nurl_pattern: \'{id}-{title}\'\npreset_related: normal\nrelated_limit: 0\neditor: \"1\"\neditor_presets: null\nseo_keys: \"\"\nseo_desc: \"\"\nallow_add_public_albums: null\nallow_download: 1\nhide_photo_item_info: null\ntypograph_id: \"3\"\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(19, 'Загрузка изображений', 'images', 1, NULL, 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(20, 'Редиректы', 'redirect', 1, '---\nno_redirect_list:\nblack_list:\nis_check_link: null\nwhite_list:\nredirect_time: 10\nis_check_refer: null\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(21, 'География', 'geo', 1, '---\nauto_detect: 1\nauto_detect_provider: geoiplookup\ndefault_country_id: null\ndefault_country_id_cache: null\ndefault_region_id: null\ndefault_region_id_cache: null\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(22, 'Подписки', 'subscriptions', 1, '---\nguest_email_confirmation: 1\nneed_auth: null\nverify_exp: 24\nupdate_user_rating: 1\nrating_value: 1\nadmin_email:\nlimit: 20\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(23, 'Wysiwyg редакторы', 'wysiwygs', 1, NULL, 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(24, 'Конструктор форм', 'forms', 1, '---\nsend_text: >\n  Спасибо! Форма\n  успешно отправлена.\nallow_embed: null\nallow_embed_domain:\ndenied_embed_domain:\nletter: |\n  [subject:Форма: {form_title} - {site}]\r\n  \r\n  Здравствуйте.\r\n  \r\n  С сайта {site} отправлена форма <b>{form_title}</b>.\r\n  \r\n  Данные формы:\r\n  \r\n  {form_data}\r\n  \r\n  --\r\n   C уважением, {site}\r\n   <small>Письмо отправлено автоматически, пожалуйста, не отвечайте на него.</small>\nnotify_text: \'<p>Здравствуйте.</p><p>Отправлена форма <strong>{form_title}</strong>.</p><p><strong>Данные формы:</strong></p><p>{form_data}</p>\'\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(25, 'Мультиязычность', 'languages', 1, '---\nservice: google\n', 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1),
(26, 'Типограф', 'typograph', 1, NULL, 'InstantCMS Team', 'https://instantcms.ru', '2.0', 1);

DROP TABLE IF EXISTS `{#}con_albums`;
CREATE TABLE `{#}con_albums` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `content` text,
  `slug` varchar(100) DEFAULT NULL,
  `seo_keys` varchar(256) DEFAULT NULL,
  `seo_desc` varchar(256) DEFAULT NULL,
  `seo_title` varchar(256) DEFAULT NULL,
  `tags` varchar(1000) DEFAULT NULL,
  `template` varchar(150) DEFAULT NULL,
  `date_pub` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` timestamp NULL DEFAULT NULL,
  `date_pub_end` timestamp NULL DEFAULT NULL,
  `is_pub` tinyint(1) NOT NULL DEFAULT '1',
  `hits_count` int(11) DEFAULT '0',
  `user_id` int(11) unsigned DEFAULT NULL,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `parent_type` varchar(32) DEFAULT NULL,
  `parent_title` varchar(100) DEFAULT NULL,
  `parent_url` varchar(255) DEFAULT NULL,
  `is_parent_hidden` tinyint(1) DEFAULT NULL,
  `category_id` int(11) unsigned NOT NULL DEFAULT '1',
  `folder_id` int(11) unsigned DEFAULT NULL,
  `is_comments_on` tinyint(1) unsigned DEFAULT '1',
  `comments` int(11) NOT NULL DEFAULT '0',
  `rating` int(11) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) unsigned DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT '1',
  `approved_by` int(11) DEFAULT NULL,
  `date_approved` timestamp NULL DEFAULT NULL,
  `is_private` tinyint(1) NOT NULL DEFAULT '0',
  `cover_image` text,
  `photos_count` int(11) NOT NULL DEFAULT '0',
  `is_public` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `folder_id` (`folder_id`),
  KEY `slug` (`slug`),
  KEY `date_pub` (`is_pub`,`is_parent_hidden`,`is_deleted`,`is_approved`,`date_pub`),
  KEY `parent_id` (`parent_id`,`parent_type`,`date_pub`),
  KEY `user_id` (`user_id`,`date_pub`),
  KEY `date_pub_end` (`date_pub_end`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}con_albums_cats`;
CREATE TABLE `{#}con_albums_cats` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `description` text NULL DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `slug_key` varchar(255) DEFAULT NULL,
  `seo_keys` varchar(256) DEFAULT NULL,
  `seo_desc` varchar(256) DEFAULT NULL,
  `seo_title` varchar(256) DEFAULT NULL,
  `seo_h1` varchar(256) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  `ns_left` int(11) DEFAULT NULL,
  `ns_right` int(11) DEFAULT NULL,
  `ns_level` int(11) DEFAULT NULL,
  `ns_differ` varchar(32) NOT NULL DEFAULT '',
  `ns_ignore` tinyint(4) NOT NULL DEFAULT '0',
  `allow_add` text,
  `is_hidden` tinyint(1) UNSIGNED DEFAULT NULL,
  `cover` text,
  PRIMARY KEY (`id`),
  KEY `ordering` (`ordering`),
  KEY `slug` (`slug`),
  KEY `ns_left` (`ns_level`,`ns_right`,`ns_left`),
  KEY `parent_id` (`parent_id`,`ns_left`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `{#}con_albums_cats` (`id`, `parent_id`, `title`, `slug`, `slug_key`, `seo_keys`, `seo_desc`, `seo_title`, `ordering`, `ns_left`, `ns_right`, `ns_level`, `ns_differ`, `ns_ignore`) VALUES
(1, 0, '---', NULL, NULL, NULL, NULL, NULL, 1, 1, 2, 0, '', 0);

DROP TABLE IF EXISTS `{#}con_albums_cats_bind`;
CREATE TABLE `{#}con_albums_cats_bind` (
  `item_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  KEY `item_id` (`item_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}con_albums_fields`;
CREATE TABLE `{#}con_albums_fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ctype_id` int(11) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `hint` varchar(200) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  `is_enabled` tinyint(1) UNSIGNED DEFAULT '1',
  `fieldset` varchar(32) DEFAULT NULL,
  `type` varchar(16) DEFAULT NULL,
  `is_in_list` tinyint(1) DEFAULT NULL,
  `is_in_item` tinyint(1) DEFAULT NULL,
  `is_in_filter` tinyint(1) DEFAULT NULL,
  `is_private` tinyint(1) DEFAULT NULL,
  `is_fixed` tinyint(1) DEFAULT NULL,
  `is_fixed_type` tinyint(1) DEFAULT NULL,
  `is_system` tinyint(1) DEFAULT NULL,
  `values` text,
  `options` text,
  `groups_read` text,
  `groups_add` text,
  `groups_edit` text,
  `filter_view` text,
  PRIMARY KEY (`id`),
  KEY `is_enabled` (`is_enabled`,`ordering`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `{#}con_albums_fields` (`id`, `ctype_id`, `name`, `title`, `hint`, `ordering`, `fieldset`, `type`, `is_in_list`, `is_in_item`, `is_in_filter`, `is_private`, `is_fixed`, `is_fixed_type`, `is_system`, `values`, `options`, `groups_read`, `groups_edit`) VALUES
(1, 7, 'title', 'Название альбома', NULL, 1, NULL, 'caption', 1, 1, 1, NULL, 1, 1, 0, NULL, '---\nlabel_in_list: none\nlabel_in_item: none\nis_required: 1\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\n', '---\n- 0\n', '---\n- 0\n'),
(2, 7, 'date_pub', 'Дата публикации', NULL, 2, NULL, 'date', 1, 1, 1, NULL, 1, 1, 1, NULL, '---\nlabel_in_list: none\nlabel_in_item: left\nshow_time: false\n', NULL, NULL),
(3, 7, 'user', 'Автор', NULL, 3, NULL, 'user', 1, 1, 0, NULL, 1, 1, 1, NULL, '---\nlabel_in_list: none\nlabel_in_item: left\n', NULL, NULL),
(4, 7, 'content', 'Описание альбома', NULL, 4, NULL, 'text', 1, 1, NULL, NULL, 1, NULL, NULL, NULL, '---\nmin_length: 0\nmax_length: 2048\nlabel_in_list: none\nlabel_in_item: none\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\n', '---\n- 0\n', '---\n- 0\n'),
(5, 7, 'cover_image', 'Обложка альбома', NULL, 5, NULL, 'image', 1, NULL, NULL, NULL, 1, 1, 1, NULL, '---\nlabel_in_list: left\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\n', '---\n- 0\n', '---\n- 0\n'),
(6, 7, 'is_public', 'Общий фотоальбом', 'Другие пользователи тоже смогут добавлять фото в этот альбом', 6, NULL, 'checkbox', 0, 0, NULL, NULL, 1, NULL, NULL, NULL, '---\nlabel_in_list: none\nlabel_in_item: none\n', NULL, NULL);

DROP TABLE IF EXISTS `{#}con_albums_props`;
CREATE TABLE `{#}con_albums_props` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ctype_id` int(11) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `fieldset` varchar(32) DEFAULT NULL,
  `type` varchar(16) DEFAULT NULL,
  `is_in_filter` tinyint(1) DEFAULT NULL,
  `values` text,
  `options` text,
  PRIMARY KEY (`id`),
  KEY `is_active` (`is_in_filter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}con_albums_props_bind`;
CREATE TABLE `{#}con_albums_props_bind` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `prop_id` int(11) DEFAULT NULL,
  `cat_id` int(11) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `prop_id` (`prop_id`),
  KEY `ordering` (`cat_id`,`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}con_albums_props_values`;
CREATE TABLE `{#}con_albums_props_values` (
  `prop_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  KEY `prop_id` (`prop_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}con_pages`;
CREATE TABLE `{#}con_pages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `content` text,
  `slug` varchar(100) DEFAULT NULL,
  `seo_keys` varchar(256) DEFAULT NULL,
  `seo_desc` varchar(256) DEFAULT NULL,
  `seo_title` varchar(256) DEFAULT NULL,
  `tags` varchar(1000) DEFAULT NULL,
  `template` varchar(150) DEFAULT NULL,
  `date_pub` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` timestamp NULL DEFAULT NULL,
  `date_pub_end` timestamp NULL DEFAULT NULL,
  `is_pub` tinyint(1) NOT NULL DEFAULT '1',
  `hits_count` int(11) DEFAULT '0',
  `user_id` int(11) unsigned DEFAULT NULL,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `parent_type` varchar(32) DEFAULT NULL,
  `parent_title` varchar(100) DEFAULT NULL,
  `parent_url` varchar(255) DEFAULT NULL,
  `is_parent_hidden` tinyint(1) DEFAULT NULL,
  `category_id` int(11) unsigned NOT NULL DEFAULT '1',
  `folder_id` int(11) unsigned DEFAULT NULL,
  `is_comments_on` tinyint(1) unsigned DEFAULT '1',
  `comments` int(11) NOT NULL DEFAULT '0',
  `rating` int(11) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) unsigned DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT '1',
  `approved_by` int(11) DEFAULT NULL,
  `date_approved` timestamp NULL DEFAULT NULL,
  `is_private` tinyint(1) NOT NULL DEFAULT '0',
  `attach` text,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `folder_id` (`folder_id`),
  KEY `slug` (`slug`),
  KEY `date_pub` (`is_pub`,`is_parent_hidden`,`is_deleted`,`is_approved`,`date_pub`),
  KEY `parent_id` (`parent_id`,`parent_type`,`date_pub`),
  KEY `user_id` (`user_id`,`date_pub`),
  KEY `date_pub_end` (`date_pub_end`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}con_pages_cats`;
CREATE TABLE `{#}con_pages_cats` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `description` text NULL DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `slug_key` varchar(255) DEFAULT NULL,
  `seo_keys` varchar(256) DEFAULT NULL,
  `seo_desc` varchar(256) DEFAULT NULL,
  `seo_title` varchar(256) DEFAULT NULL,
  `seo_h1` varchar(256) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  `ns_left` int(11) DEFAULT NULL,
  `ns_right` int(11) DEFAULT NULL,
  `ns_level` int(11) DEFAULT NULL,
  `ns_differ` varchar(32) NOT NULL DEFAULT '',
  `ns_ignore` tinyint(4) NOT NULL DEFAULT '0',
  `allow_add` text,
  `is_hidden` tinyint(1) UNSIGNED DEFAULT NULL,
  `cover` text,
  PRIMARY KEY (`id`),
  KEY `ordering` (`ordering`),
  KEY `slug` (`slug`),
  KEY `ns_left` (`ns_level`,`ns_right`,`ns_left`),
  KEY `parent_id` (`parent_id`,`ns_left`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `{#}con_pages_cats` (`id`, `parent_id`, `title`, `slug`, `slug_key`, `seo_keys`, `seo_desc`, `seo_title`, `ordering`, `ns_left`, `ns_right`, `ns_level`, `ns_differ`, `ns_ignore`) VALUES
(1, 0, '---', NULL, NULL, NULL, NULL, NULL, 1, 1, 2, 0, '', 0);

DROP TABLE IF EXISTS `{#}con_pages_cats_bind`;
CREATE TABLE `{#}con_pages_cats_bind` (
  `item_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  KEY `item_id` (`item_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}con_pages_fields`;
CREATE TABLE `{#}con_pages_fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ctype_id` int(11) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `hint` varchar(200) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  `is_enabled` tinyint(1) UNSIGNED DEFAULT '1',
  `fieldset` varchar(32) DEFAULT NULL,
  `type` varchar(16) DEFAULT NULL,
  `is_in_list` tinyint(1) DEFAULT NULL,
  `is_in_item` tinyint(1) DEFAULT NULL,
  `is_in_filter` tinyint(1) DEFAULT NULL,
  `is_private` tinyint(1) DEFAULT NULL,
  `is_fixed` tinyint(1) DEFAULT NULL,
  `is_fixed_type` tinyint(1) DEFAULT NULL,
  `is_system` tinyint(1) DEFAULT NULL,
  `values` text,
  `options` text,
  `groups_read` text,
  `groups_add` text,
  `groups_edit` text,
  `filter_view` text,
  PRIMARY KEY (`id`),
  KEY `is_enabled` (`is_enabled`,`ordering`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `{#}con_pages_fields` (`id`, `ctype_id`, `name`, `title`, `hint`, `ordering`, `fieldset`, `type`, `is_in_list`, `is_in_item`, `is_in_filter`, `is_private`, `is_fixed`, `is_fixed_type`, `is_system`, `values`, `options`, `groups_read`, `groups_edit`) VALUES
(1, 1, 'title', 'Заголовок', NULL, 1, NULL, 'caption', 1, 1, 1, NULL, 1, 1, NULL, NULL, '---\nlabel_in_list: none\nlabel_in_item: none\nmin_length: 3\nmax_length: 100\nis_required: true\n', NULL, NULL),
(2, 1, 'date_pub', 'Дата публикации', NULL, 2, NULL, 'date', NULL, NULL, NULL, NULL, 1, NULL, 1, NULL, '---\nlabel_in_list: none\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\n', '---\n- 0\n', '---\n- 0\n'),
(3, 1, 'user', 'Автор', NULL, 3, NULL, 'user', NULL, NULL, NULL, NULL, 1, NULL, 1, NULL, '---\nlabel_in_list: none\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\n', '---\n- 0\n', '---\n- 0\n'),
(4, 1, 'content', 'Текст страницы', NULL, 4, NULL, 'html', NULL, 1, NULL, NULL, 1, NULL, NULL, NULL, '---\neditor: 3\nis_html_filter: null\nlabel_in_list: none\nlabel_in_item: none\nis_required: 1\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\n', '---\n- 0\n', '---\n- 0\n'),
(5, 1, 'attach', 'Скачать', 'Приложите файл к странице', 5, NULL, 'file', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '---\nshow_name: 0\nextensions: jpg, gif, png\nmax_size_mb: 2\nshow_size: 1\nlabel_in_list: none\nlabel_in_item: none\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\n', '---\n- 0\n', '---\n- 0\n');

DROP TABLE IF EXISTS `{#}con_pages_props`;
CREATE TABLE `{#}con_pages_props` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ctype_id` int(11) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `fieldset` varchar(32) DEFAULT NULL,
  `type` varchar(16) DEFAULT NULL,
  `is_in_filter` tinyint(1) DEFAULT NULL,
  `values` text,
  `options` text,
  PRIMARY KEY (`id`),
  KEY `is_active` (`is_in_filter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}con_pages_props_bind`;
CREATE TABLE `{#}con_pages_props_bind` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `prop_id` int(11) DEFAULT NULL,
  `cat_id` int(11) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `prop_id` (`prop_id`),
  KEY `ordering` (`cat_id`,`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{#}con_pages_props_values`;
CREATE TABLE `{#}con_pages_props_values` (
  `prop_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  KEY `prop_id` (`prop_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

INSERT INTO `{#}events` (`id`, `event`, `listener`, `ordering`, `is_enabled`) VALUES
(1, 'content_after_add_approve', 'activity', 1, 1),
(2, 'content_after_update_approve', 'activity', 2, 1),
(3, 'publish_delayed_content', 'activity', 3, 1),
(4, 'user_delete', 'activity', 4, 1),
(5, 'user_tab_info', 'activity', 5, 1),
(6, 'user_tab_show', 'activity', 6, 1),
(7, 'menu_admin', 'admin', 7, 1),
(8, 'user_login', 'admin', 8, 1),
(9, 'admin_confirm_login', 'admin', 9, 1),
(10, 'user_profile_update', 'auth', 10, 1),
(11, 'frontpage', 'auth', 11, 1),
(12, 'page_is_allowed', 'auth', 12, 1),
(13, 'frontpage_types', 'auth', 13, 1),
(14, 'content_after_update', 'comments', 14, 1),
(16, 'admin_dashboard_chart', 'comments', 16, 1),
(17, 'user_privacy_types', 'comments', 17, 1),
(18, 'user_login', 'comments', 18, 1),
(19, 'user_notify_types', 'comments', 19, 1),
(20, 'user_delete', 'comments', 20, 1),
(21, 'user_tab_info', 'comments', 21, 1),
(22, 'user_tab_show', 'comments', 22, 1),
(23, 'fulltext_search', 'content', 23, 1),
(24, 'admin_dashboard_chart', 'content', 24, 1),
(25, 'menu_content', 'content', 25, 1),
(26, 'user_delete', 'content', 26, 1),
(27, 'user_privacy_types', 'content', 27, 1),
(28, 'sitemap_sources', 'content', 28, 1),
(30, 'rss_content_controller_form', 'content', 30, 1),
(31, 'rss_content_controller_after_update', 'content', 31, 1),
(32, 'frontpage', 'content', 32, 1),
(33, 'frontpage_types', 'content', 33, 1),
(34, 'ctype_relation_childs', 'content', 34, 1),
(35, 'admin_content_dataset_fields_list', 'content', 35, 1),
(36, 'moderation_list', 'content', 36, 1),
(37, 'ctype_lists_context', 'content', 37, 1),
(38, 'ctype_after_update', 'frontpage', 38, 1),
(39, 'ctype_after_delete', 'frontpage', 39, 1),
(40, 'admin_dashboard_chart', 'groups', 40, 1),
(41, 'content_view_hidden', 'groups', 41, 1),
(42, 'content_before_list', 'groups', 42, 1),
(43, 'rating_vote', 'groups', 43, 1),
(44, 'user_privacy_types', 'groups', 44, 1),
(45, 'user_profile_buttons', 'groups', 45, 1),
(46, 'user_notify_types', 'groups', 46, 1),
(47, 'user_delete', 'groups', 47, 1),
(48, 'user_tab_info', 'groups', 48, 1),
(49, 'user_tab_show', 'groups', 49, 1),
(50, 'menu_groups', 'groups', 50, 1),
(51, 'sitemap_sources', 'groups', 51, 1),
(52, 'sitemap_urls', 'groups', 52, 1),
(53, 'content_privacy_types', 'groups', 53, 1),
(54, 'content_add_permissions', 'groups', 54, 1),
(55, 'fulltext_search', 'groups', 55, 1),
(56, 'content_before_childs', 'groups', 56, 1),
(57, 'ctype_relation_childs', 'groups', 57, 1),
(58, 'admin_groups_dataset_fields_list', 'groups', 58, 1),
(59, 'content_validate', 'groups', 59, 1),
(60, 'moderation_list', 'groups', 60, 1),
(61, 'content_before_item', 'groups', 61, 1),
(62, 'user_delete', 'images', 62, 1),
(63, 'admin_dashboard_chart', 'messages', 63, 1),
(64, 'menu_messages', 'messages', 64, 1),
(65, 'users_profile_view', 'messages', 65, 1),
(66, 'user_privacy_types', 'messages', 66, 1),
(67, 'user_delete', 'messages', 67, 1),
(68, 'user_notify_types', 'messages', 68, 1),
(69, 'admin_dashboard_block', 'moderation', 69, 1),
(70, 'content_after_trash_put', 'moderation', 70, 1),
(71, 'content_after_restore', 'moderation', 71, 1),
(72, 'content_before_delete', 'moderation', 72, 1),
(73, 'menu_moderation', 'moderation', 73, 1),
(74, 'content_albums_items_html', 'photos', 74, 1),
(75, 'fulltext_search', 'photos', 75, 1),
(76, 'admin_albums_ctype_menu', 'photos', 76, 1),
(77, 'content_albums_after_add', 'photos', 77, 1),
(78, 'content_albums_after_delete', 'photos', 78, 1),
(79, 'content_albums_item_html', 'photos', 79, 1),
(80, 'content_albums_before_item', 'photos', 80, 1),
(81, 'content_albums_before_list', 'photos', 81, 1),
(82, 'user_delete', 'photos', 82, 1),
(83, 'user_delete', 'rating', 83, 1),
(84, 'content_before_list', 'rating', 84, 1),
(85, 'captcha_html', 'recaptcha', 85, 1),
(86, 'captcha_validate', 'recaptcha', 86, 1),
(87, 'ctype_basic_form', 'rss', 87, 1),
(88, 'ctype_before_add', 'rss', 88, 1),
(89, 'ctype_after_add', 'rss', 89, 1),
(90, 'ctype_before_edit', 'rss', 90, 1),
(91, 'ctype_before_update', 'rss', 91, 1),
(92, 'ctype_after_delete', 'rss', 92, 1),
(93, 'content_before_category', 'rss', 93, 1),
(94, 'content_before_profile', 'rss', 94, 1),
(95, 'photos_before_item', 'search', 95, 1),
(96, 'content_before_list', 'search', 96, 1),
(97, 'content_before_item', 'search', 97, 1),
(98, 'before_print_head', 'search', 98, 1),
(99, 'html_filter', 'typograph', 99, 1),
(100, 'admin_dashboard_chart', 'users', 100, 1),
(101, 'menu_users', 'users', 101, 1),
(102, 'rating_vote', 'users', 102, 1),
(103, 'user_notify_types', 'users', 103, 1),
(104, 'user_privacy_types', 'users', 104, 1),
(105, 'user_tab_info', 'users', 105, 1),
(106, 'auth_login', 'users', 106, 1),
(107, 'user_preloaded', 'users', 107, 1),
(108, 'wall_permissions', 'users', 108, 1),
(109, 'wall_after_add', 'users', 109, 1),
(110, 'wall_after_delete', 'users', 110, 1),
(111, 'content_privacy_types', 'users', 111, 1),
(112, 'content_view_hidden', 'users', 112, 1),
(113, 'sitemap_sources', 'users', 113, 1),
(114, 'content_before_childs', 'users', 114, 1),
(115, 'ctype_relation_childs', 'users', 115, 1),
(116, 'admin_dashboard_chart', 'wall', 116, 1),
(117, 'user_notify_types', 'wall', 117, 1),
(118, 'user_delete', 'wall', 118, 1),
(119, 'page_is_allowed', 'widgets', 119, 1),
(120, 'ctype_lists_context', 'groups', 120, 1),
(121, 'ctype_lists_context', 'tags', 121, 1),
(122, 'moderation_list', 'comments', 122, 1),
(123, 'content_groups_before_delete', 'moderation', 123, 1),
(124, 'comments_after_refuse', 'moderation', 124, 1),
(125, 'subscribe', 'activity', 125, 1),
(126, 'unsubscribe', 'activity', 126, 1),
(127, 'admin_subscriptions_list', 'content', 127, 1),
(128, 'admin_subscriptions_list', 'photos', 128, 1),
(129, 'user_delete', 'subscriptions', 129, 1),
(130, 'content_toolbar_html', 'subscriptions', 130, 1),
(131, 'photos_toolbar_html', 'subscriptions', 131, 1),
(132, 'content_filter_buttons_html', 'subscriptions', 132, 1),
(133, 'user_tab_info', 'subscriptions', 133, 1),
(134, 'content_photos_after_add', 'subscriptions', 134, 1),
(135, 'user_notify_types', 'subscriptions', 135, 1),
(136, 'user_tab_show', 'subscriptions', 136, 1),
(137, 'content_after_add_approve', 'subscriptions', 137, 1),
(138, 'publish_delayed_content', 'subscriptions', 138, 1),
(139, 'ctype_basic_form', 'subscriptions', 139, 1),
(140, 'admin_dashboard_block', 'users', 140, 1),
(141, 'engine_start', 'sitemap', 141, 1),
(142, 'sitemap_sources', 'frontpage', 142, 1),
(143, 'sitemap_sources', 'photos', 143, 1),
(144, 'ctype_basic_form', 'tags', 144, 1),
(145, 'content_after_add', 'tags', 145, 1),
(146, 'content_before_update', 'tags', 146, 1),
(147, 'content_item_form', 'tags', 147, 1),
(148, 'content_before_item', 'tags', 148, 1),
(149, 'content_before_list', 'tags', 149, 1),
(150, 'tags_search_subjects', 'content', 150, 1),
(151, 'images_before_upload', 'typograph', 151, 1),
(152, 'engine_start', 'content', 152, 1),
(153, 'content_category_after_update', 'subscriptions', 153, 1),
(155, 'user_notify_types', 'rating', 155, 1),
(156, 'content_before_item', 'comments', 156, 1),
(157, 'content_before_item', 'rating', 157, 1),
(158, 'content_item_form', 'comments', 158, 1),
(159, 'ctype_basic_form', 'comments', 159, 1),
(160, 'ctype_basic_form', 'rating', 160, 1),
(161, 'ctype_basic_form', 'groups', 161, 1),
(162, 'photos_before_item', 'rating', 162, 1),
(163, 'photos_before_item', 'comments', 163, 1),
(164, 'comments_targets', 'content', 164, 1),
(165, 'comments_targets', 'photos', 165, 1),
(166, 'content_before_list', 'comments', 166, 1),
(167, 'admin_dashboard_block', 'admin', 167, 1),
(168, 'admin_dashboard_block', 'activity', 168, 1),
(169, 'user_notify_types', 'content', 169, 1),
(170, 'form_users_password_2fa', 'authga', 170, 1),
(171, 'controller_auth_after_save_options', 'authga', 171, 1),
(172, 'form_users_password', 'auth', 172, 1),
(173, 'auth_twofactor_list', 'authga', 173, 1),
(174, 'users_before_edit_password', 'authga', 174, 1),
(175, 'admin_inline_save_subscriptions', 'activity', 175, 1),
(176, 'admin_col_scheme_options', 'bootstrap4', 176, 1),
(178, 'admin_row_scheme_options', 'bootstrap4', 178, 1),
(179, 'process_render_users_profile_view', 'wall', 179, 1),
(180, 'process_render_groups_group_view', 'wall', 180, 1),
(181, 'user_add_status_after', 'activity', 181, 1),
(182, 'user_add_status', 'wall', 182, 1),
(183, 'form_groups_options', 'wall', 183, 1),
(184, 'form_users_options', 'wall', 184, 1),
(185, 'user_privacy_types', 'wall', 185, 1),
(186, 'ctype_field_users_after_update', 'bootstrap4', 186, 1),
(187, 'widget_menu_form', 'bootstrap4', 187, 1),
(188, 'users_add_friendship_mutual', 'activity', 188, 1),
(189, 'user_registered', 'activity', 189, 1),
(190, 'db_nested_tables', 'content', 190, 1),
(191, 'widget_content_list_form', 'content', 191, 1),
(192, 'content_before_item', 'forms', 192, 1),
(193, 'users_after_update', 'activity', 193, 1),
(194, 'content_item_form_context', 'groups', 194, 1),
(195, 'ctype_labels_after_update', 'activity', 195, 1),
(196, 'ctype_after_delete', 'activity', 196, 1),
(197, 'comments_rate_after', 'activity', 197, 1),
(198, 'content_albums_after_delete', 'activity', 198, 1),
(199, 'content_photos_after_add', 'activity', 199, 1),
(200, 'comments_after_delete_list', 'activity', 200, 1),
(201, 'content_after_delete', 'activity', 201, 1),
(202, 'content_after_delete', 'comments', 202, 1),
(203, 'content_after_delete', 'rating', 203, 1),
(204, 'content_after_delete', 'tags', 204, 1),
(205, 'content_after_restore', 'activity', 205, 1),
(206, 'content_after_trash_put', 'activity', 206, 1),
(207, 'content_after_restore', 'comments', 207, 1),
(208, 'content_after_trash_put', 'comments', 208, 1),
(209, 'content_groups_after_delete', 'activity', 209, 1),
(210, 'group_after_join', 'activity', 210, 1),
(211, 'group_after_leave', 'activity', 211, 1),
(212, 'groups_after_accept_request', 'activity', 212, 1),
(213, 'groups_after_update', 'activity', 213, 1),
(214, 'render_widget_menu_menu', 'bootstrap4', 214, 1),
(215, 'engine_start', 'redirect', 215, 1),
(216, 'restore_user', 'comments', 216, 1),
(217, 'set_user_is_deleted', 'comments', 217, 1),
(218, 'comments_after_delete_list', 'moderation', 218, 1),
(219, 'form_get', 'languages', 219, 1),
(220, 'widget_options_full_form', 'languages', 220, 1),
(221, 'languages_forms', 'admin', 221, 1),
(222, 'languages_forms', 'widgets', 222, 1),
(223, 'languages_forms', 'content', 223, 1),
(224, 'form_make', 'languages', 224, 1),
(225, 'languages_forms', 'users', 225, 1),
(226, 'languages_forms', 'groups', 226, 1),
(227, 'languages_forms', 'activity', 227, 1),
(228, 'grid_activity_types', 'languages', 228, 1),
(229, 'content_form_field', 'languages', 229, 1),
(230, 'ctype_field_after_add', 'languages', 230, 1),
(231, 'ctype_field_after_update', 'languages', 231, 1),
(232, 'engine_start', 'languages', 232, 1),
(233, 'languages_forms', 'forms', 233, 1),
(234, 'ctype_basic_form', 'languages', 234, 1),
(235, 'frontpage_action_index', 'languages', 235, 1),
(236, 'content_before_item', 'languages', 236, 1),
(237, 'content_before_list', 'languages', 237, 1),
(238, 'content_item_form', 'languages', 238, 1);

DROP TABLE IF EXISTS `{#}groups`;
CREATE TABLE `{#}groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) unsigned DEFAULT NULL COMMENT 'Создатель',
  `date_pub` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата создания',
  `title` varchar(128) NOT NULL COMMENT 'Название',
  `description` text COMMENT 'Описание',
  `logo` text COMMENT 'Логотип группы',
  `rating` int(11) NOT NULL DEFAULT '0' COMMENT 'Рейтинг',
  `members_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Кол-во членов',
  `join_policy` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Политика вступления',
  `edit_policy` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Политика редактирования',
  `wall_policy` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Политика стены',
  `wall_reply_policy` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Политика комментирования стены',
  `is_closed` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Закрытая?',
  `cover` text COMMENT 'Обложка группы',
  `slug` varchar(100) DEFAULT NULL,
  `content_policy` varchar(500) DEFAULT NULL COMMENT 'Политика контента',
  `content_groups` varchar(1000) DEFAULT NULL COMMENT 'Группы, которым разрешено добавление контента',
  `roles` varchar(2000) DEFAULT NULL,
  `content_roles` varchar(1000) DEFAULT NULL,
  `join_roles` varchar(1000) DEFAULT NULL COMMENT 'Роли при вступлении в группу',
  `is_approved` tinyint(1) NOT NULL DEFAULT '1',
  `approved_by` int(11) DEFAULT NULL,
  `date_approved` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `members_count` (`members_count`),
  KEY `date_pub` (`date_pub`),
  KEY `rating` (`rating`),
  KEY `owner_id` (`owner_id`,`members_count`),
  KEY `slug` (`slug`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Группы (сообщества)';

DROP TABLE IF EXISTS `{#}groups_fields`;
CREATE TABLE `{#}groups_fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ctype_id` int(11) unsigned DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `hint` varchar(200) DEFAULT NULL,
  `ordering` int(11) unsigned DEFAULT NULL,
  `is_enabled` tinyint(1) UNSIGNED DEFAULT '1',
  `fieldset` varchar(32) DEFAULT NULL,
  `type` varchar(16) DEFAULT NULL,
  `is_in_list` tinyint(1) unsigned DEFAULT NULL,
  `is_in_item` tinyint(1) unsigned DEFAULT NULL,
  `is_in_filter` tinyint(1) unsigned DEFAULT NULL,
  `is_in_closed` tinyint(3) unsigned DEFAULT NULL,
  `is_private` tinyint(1) unsigned DEFAULT NULL,
  `is_fixed` tinyint(1) unsigned DEFAULT NULL,
  `is_fixed_type` tinyint(1) unsigned DEFAULT NULL,
  `is_system` tinyint(1) unsigned DEFAULT NULL,
  `values` text,
  `options` text,
  `groups_read` text,
  `groups_add` text,
  `groups_edit` text,
  `filter_view` text,
  PRIMARY KEY (`id`),
  KEY `is_enabled` (`is_enabled`,`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Поля групп';

INSERT INTO `{#}groups_fields` (`id`, `ctype_id`, `name`, `title`, `hint`, `ordering`, `fieldset`, `type`, `is_in_list`, `is_in_item`, `is_in_filter`, `is_in_closed`, `is_private`, `is_fixed`, `is_fixed_type`, `is_system`, `values`, `options`, `groups_read`, `groups_edit`, `filter_view`) VALUES
(1, NULL, 'title', 'Заголовок', NULL, 1, 'Основная информация', 'caption', 1, 1, 1, 1, NULL, 1, 1, 1, NULL, '---\nmin_length: 1\nmax_length: 128\nin_fulltext_search: 1\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n', '---\n- 0\n'),
(2, NULL, 'description', 'Описание группы', NULL, 2, 'Основная информация', 'html', 1, 1, NULL, 1, NULL, 1, 1, NULL, NULL, '---\neditor: 3\nis_html_filter: 1\nbuild_redirect_link: 1\nteaser_len: 200\nin_fulltext_search: null\nlabel_in_list: none\nlabel_in_item: none\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n', '---\n- 0\n'),
(3, NULL, 'logo', 'Логотип группы', NULL, 3, 'Основная информация', 'image', 1, 1, NULL, 1, NULL, 1, 1, 1, NULL, '---\nsize_teaser: small\nsize_full: micro\nsize_modal:\nsizes:\n  - micro\n  - small\nallow_import_link: 1\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n', '---\n- 0\n'),
(5, NULL, 'cover', 'Обложка группы', NULL, 4, 'Основная информация', 'image', NULL, 1, NULL, 1, NULL, 1, 1, 1, NULL, '---\nsize_teaser: small\nsize_full: original\nsize_modal:\nsizes:\n  - small\n  - original\nallow_import_link: 1\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n', '---\n- 0\n');

DROP TABLE IF EXISTS `{#}groups_invites`;
CREATE TABLE `{#}groups_invites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned DEFAULT NULL COMMENT 'ID группы',
  `user_id` int(11) unsigned DEFAULT NULL COMMENT 'ID пригласившего',
  `invited_id` int(11) unsigned DEFAULT NULL COMMENT 'ID приглашенного',
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`),
  KEY `invited_id` (`invited_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Приглашения в группы';

DROP TABLE IF EXISTS `{#}groups_members`;
CREATE TABLE `{#}groups_members` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `role` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT 'Роль пользователя в группе',
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата обновления роли',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `group_id` (`group_id`,`date_updated`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Члены групп (сообществ)';

DROP TABLE IF EXISTS `{#}groups_member_roles`;
CREATE TABLE `{#}groups_member_roles` (
  `user_id` int(11) unsigned DEFAULT NULL,
  `group_id` int(11) unsigned DEFAULT NULL,
  `role_id` tinyint(1) unsigned NOT NULL DEFAULT '0',
  KEY `user_id` (`user_id`),
  KEY `group_id` (`group_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Роли участников групп';

DROP TABLE IF EXISTS `{#}images_presets`;
CREATE TABLE `{#}images_presets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL COMMENT 'Системное имя пресета',
  `title` varchar(128) DEFAULT NULL COMMENT 'Название пресета',
  `width` int(11) unsigned DEFAULT NULL COMMENT 'Ширина конвертированного изображения',
  `height` int(11) unsigned DEFAULT NULL COMMENT 'Высота конвертированного изображения',
  `is_square` tinyint(1) unsigned DEFAULT NULL COMMENT 'Обрезать строго по размеру',
  `is_watermark` tinyint(1) unsigned DEFAULT NULL COMMENT 'Накладывать ватермарк',
  `wm_image` text COMMENT 'Путь к изображению ватермарка',
  `wm_origin` varchar(16) DEFAULT NULL COMMENT 'Позиция ватермарка',
  `wm_margin` int(11) unsigned DEFAULT NULL COMMENT 'Отступы от краёв для ватермарка',
  `is_internal` tinyint(1) unsigned DEFAULT NULL COMMENT 'Системный пресет?',
  `quality` tinyint(1) unsigned DEFAULT '90' COMMENT 'Качество изображения',
  `gamma_correct` tinyint(1) unsigned DEFAULT NULL COMMENT 'Гамма-коррекция',
  `crop_position` tinyint(1) unsigned DEFAULT '2' COMMENT 'Позиция при обрезке строго по размеру',
  `allow_enlarge` tinyint(1) unsigned DEFAULT NULL COMMENT 'Увеличивать до размера пресета',
  `gif_to_gif` tinyint(1) unsigned DEFAULT '1' COMMENT 'Конвертировать GIF сохраняя анимацию',
  `convert_format` char(4) DEFAULT NULL COMMENT 'Итоговый формат изображения после конвертации',
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Пресеты для конвертации изображений';

INSERT INTO `{#}images_presets` (`id`, `name`, `title`, `width`, `height`, `is_square`, `is_watermark`, `wm_image`, `wm_origin`, `wm_margin`, `is_internal`, `quality`) VALUES
(1, 'micro', 'Микро', 32, 32, 1, NULL, NULL, NULL, NULL, NULL, 75),
(2, 'small', 'Маленький', 64, 64, 1, NULL, NULL, NULL, NULL, NULL, 80),
(3, 'normal', 'Средний', NULL, 256, NULL, NULL, NULL, NULL, NULL, NULL, 85),
(4, 'big', 'Большой', 690, 690, NULL, NULL, NULL, 'bottom-right', NULL, NULL, 90),
(5, 'wysiwyg_markitup', 'Редактор: markItUp!', 400, 400, NULL, NULL, NULL, 'top-left', NULL, 1, 85),
(6, 'wysiwyg_redactor', 'Редактор: Redactor', 800, 800, NULL, NULL, NULL, 'top-left', NULL, 1, 90);

DROP TABLE IF EXISTS `{#}menu`;
CREATE TABLE `{#}menu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL COMMENT 'Системное имя',
  `title` varchar(64) DEFAULT NULL COMMENT 'Название меню',
  `is_fixed` tinyint(1) unsigned DEFAULT NULL COMMENT 'Запрещено удалять?',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Меню сайта';

INSERT INTO `{#}menu` (`id`, `name`, `title`, `is_fixed`) VALUES
(1, 'main', 'Главное меню', 1),
(2, 'personal', 'Персональное меню', 1),
(4, 'toolbar', 'Меню действий', 1),
(5, 'header', 'Верхнее меню', NULL),
(6, 'notices', 'Уведомления', NULL);

DROP TABLE IF EXISTS `{#}menu_items`;
CREATE TABLE `{#}menu_items` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) unsigned DEFAULT NULL COMMENT 'ID меню',
  `parent_id` int(11) unsigned DEFAULT '0' COMMENT 'ID родительского пункта',
  `is_enabled` tinyint(1) UNSIGNED DEFAULT '1' COMMENT 'Включен?',
  `title` varchar(64) DEFAULT NULL COMMENT 'Заголовок пункта',
  `url` varchar(255) DEFAULT NULL COMMENT 'Ссылка',
  `ordering` int(11) unsigned DEFAULT NULL COMMENT 'Порядковый номер',
  `options` text COMMENT 'Массив опций',
  `groups_view` text COMMENT 'Массив разрешенных групп пользователей',
  `groups_hide` text COMMENT 'Массив запрещенных групп пользователей',
  PRIMARY KEY (`id`),
  KEY `menu_id` (`menu_id`),
  KEY `parent_id` (`parent_id`),
  KEY `ordering` (`ordering`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Пункты меню';

INSERT INTO `{#}menu_items` (`id`, `menu_id`, `parent_id`, `title`, `url`, `ordering`, `options`, `groups_view`, `groups_hide`) VALUES
(13, 2, 0, 'Мой профиль', 'users/{user.id}', 1, '---\ntarget: _self\nclass: profile\nicon: user\n', '---\n- 0\n', NULL),
(14, 2, 0, 'Мои сообщения', '{messages:view}', 2, '---\ntarget: _self\nclass: messages messages-counter ajax-modal\nicon: envelope\n', '---\n- 0\n', NULL),
(24, 2, 0, 'Создать', '{content:add}', 6, '---\nclass: add\n', NULL, NULL),
(25, 2, 0, 'Панель управления', '{admin:menu}', 7, '---\nclass: cpanel\n', '---\n- 6\n', NULL),
(29, 1, 0, 'Люди', 'users', 9, '---\nclass: \n', '---\n- 0\n', NULL),
(30, 6, 0, 'Уведомления', '{messages:notices}', 1, '---\ntarget: _self\nclass: bell ajax-modal notices-counter\nicon: bell\n', '---\n- 0\n', '---\n- 1\n'),
(31, 1, 0, 'Активность', 'activity', 7, '---\nclass:', '---\n- 0\n', NULL),
(32, 1, 0, 'Группы', 'groups', 6, '---\nclass:', '---\n- 0\n', NULL),
(33, 2, 0, 'Мои группы', '{groups:my}', 5, '---\nclass: group', '---\n- 0\n', NULL),
(34, 5, 0, 'Войти', 'auth/login', 9, '---\ntarget: _self\nclass: ajax-modal key\nicon: sign-in-alt\n', '---\n- 1\n', NULL),
(35, 5, 0, 'Регистрация', 'auth/register', 10, '---\ntarget: _self\nclass: user_add\nicon: user-plus\n', '---\n- 1\n', NULL),
(36, 2, 0, 'Черновики', '{moderation:draft}', 4, '---\ntarget: _self\nclass: draft\nicon: cloud\n', '---\n- 0\n', NULL),
(37, 2, 0, 'Модерация', '{moderation:panel}', 4, '---\ntarget: _self\nclass: checklist\nicon: user-graduate\n', '---\n- 5\n- 6\n', NULL),
(41, 2, 0, 'На модерации', '{moderation:user_panel}', 4, '---\ntarget: _self\nclass: onchecklist\nicon: clipboard-check\n', '---\n- 0\n', NULL),
(38, 1, 0, 'Комментарии', 'comments', 8, '---\nclass:', '---\n- 0\n', NULL),
(43, 2, 0, 'Выйти', 'auth/logout?csrf_token={csrf_token}', 12, '---\ntarget: _self\nclass: logout\nicon: sign-out-alt\n', '---\n- 0\n', NULL);

DROP TABLE IF EXISTS `{#}moderators`;
CREATE TABLE `{#}moderators` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `date_assigned` timestamp NULL DEFAULT NULL,
  `ctype_name` varchar(32) DEFAULT NULL,
  `count_approved` int(11) unsigned NOT NULL DEFAULT '0',
  `count_deleted` int(11) unsigned NOT NULL DEFAULT '0',
  `count_idle` int(11) unsigned NOT NULL DEFAULT '0',
  `trash_left_time` int(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `ctype_name` (`ctype_name`),
  KEY `count_idle` (`count_idle`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Модераторы';

DROP TABLE IF EXISTS `{#}moderators_tasks`;
CREATE TABLE `{#}moderators_tasks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `moderator_id` int(11) unsigned DEFAULT NULL,
  `author_id` int(11) unsigned DEFAULT NULL,
  `item_id` int(11) unsigned DEFAULT NULL,
  `ctype_name` varchar(32) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `url` varchar(256) DEFAULT NULL,
  `date_pub` timestamp NULL DEFAULT NULL,
  `is_new_item` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `moderator_id` (`moderator_id`),
  KEY `author_id` (`author_id`),
  KEY `ctype_name` (`ctype_name`),
  KEY `date_pub` (`date_pub`),
  KEY `item_id` (`item_id`),
  KEY `is_new` (`is_new_item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Задачи модераторов';

DROP TABLE IF EXISTS `{#}moderators_logs`;
CREATE TABLE `{#}moderators_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `moderator_id` int(11) unsigned DEFAULT NULL,
  `author_id` int(11) unsigned DEFAULT NULL,
  `action` tinyint(1) unsigned DEFAULT NULL,
  `date_pub` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_expired` timestamp NULL DEFAULT NULL,
  `target_id` int(11) unsigned DEFAULT NULL,
  `target_controller` varchar(32) DEFAULT NULL,
  `target_subject` varchar(32) DEFAULT NULL,
  `data` text,
  PRIMARY KEY (`id`),
  KEY `moderator_id` (`moderator_id`),
  KEY `target_id` (`target_id`,`target_subject`,`target_controller`),
  KEY `author_id` (`author_id`),
  KEY `date_expired` (`date_expired`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Логи модерации';

DROP TABLE IF EXISTS `{#}perms_rules`;
CREATE TABLE `{#}perms_rules` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `controller` varchar(32) DEFAULT NULL COMMENT 'Компонент (владелец)',
  `name` varchar(32) NOT NULL COMMENT 'Название правила',
  `type` enum('flag','list','number') NOT NULL DEFAULT 'flag' COMMENT 'Тип выбора (flag,list...)',
  `options` varchar(128) DEFAULT NULL COMMENT 'Массив возможных значений',
  `show_for_guest_group` tinyint(1) DEFAULT NULL COMMENT 'Показывать правило для группы гости',
  PRIMARY KEY (`id`),
  KEY `controller` (`controller`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Перечь всех возможных правил доступа';

INSERT INTO `{#}perms_rules` (`id`, `controller`, `name`, `type`, `options`) VALUES
(1, 'content', 'add', 'list', 'premod,yes'),
(2, 'content', 'edit', 'list', 'premod_own,own,premod_all,all'),
(3, 'content', 'delete', 'list', 'own,all'),
(4, 'content', 'add_cat', 'flag', NULL),
(5, 'content', 'edit_cat', 'flag', NULL),
(6, 'content', 'delete_cat', 'flag', NULL),
(8, 'content', 'rate', 'flag', NULL),
(9, 'content', 'privacy', 'flag', NULL),
(10, 'comments', 'add', 'flag', NULL),
(11, 'comments', 'edit', 'list', 'own,all'),
(12, 'comments', 'delete', 'list', 'own,all,full_delete'),
(13, 'content', 'view_all', 'flag', NULL),
(14, 'comments', 'view_all', 'flag', NULL),
(15, 'groups', 'add', 'list', 'premod,yes'),
(16, 'groups', 'edit', 'list', 'own,all'),
(17, 'groups', 'delete', 'list', 'own,all'),
(18, 'content', 'limit', 'number', NULL),
(19, 'users', 'vote_karma', 'flag', NULL),
(20, 'comments', 'rate', 'flag', NULL),
(21, 'comments', 'karma', 'number', NULL),
(22, 'content', 'karma', 'number', NULL),
(23, 'activity', 'delete', 'flag', NULL),
(24, 'content', 'pub_late', 'flag', NULL),
(25, 'content', 'pub_long', 'list', 'days,any'),
(26, 'content', 'pub_max_days', 'number', NULL),
(27, 'content', 'pub_max_ext', 'flag', NULL),
(28, 'content', 'pub_on', 'flag', NULL),
(29, 'content', 'disable_comments', 'flag', NULL),
(30, 'comments', 'add_approved', 'flag', NULL),
(32, 'content', 'add_to_parent', 'list', 'to_own,to_other,to_all'),
(33,  'content',  'bind_to_parent',  'list',  'own_to_own,own_to_other,own_to_all,other_to_own,other_to_other,other_to_all,all_to_own,all_to_other,all_to_all'),
(34, 'content',  'bind_off_parent',  'list',  'own,all'),
(35, 'content', 'move_to_trash', 'list', 'own,all'),
(36, 'content', 'restore', 'list', 'own,all'),
(37, 'content', 'trash_left_time', 'number', NULL),
(38, 'users', 'delete', 'list', 'my,anyuser'),
(39, 'groups', 'invite_users', 'flag', NULL),
(40, 'groups', 'bind_to_parent', 'list', 'own_to_own,own_to_other,own_to_all,other_to_own,other_to_other,other_to_all,all_to_own,all_to_other,all_to_all'),
(41, 'users', 'bind_to_parent', 'list', 'own_to_own,own_to_other,own_to_all,other_to_own,other_to_other,other_to_all,all_to_own,all_to_other,all_to_all'),
(42, 'groups', 'bind_off_parent', 'list', 'own,all'),
(43, 'users', 'bind_off_parent', 'list', 'own,all'),
(44, 'groups', 'content_access', 'flag', NULL),
(45, 'auth', 'view_closed', 'flag', NULL),
(46, 'content', 'view_list', 'list', 'all,other,allow'),
(47, 'content', 'limit24', 'number', NULL),
(48, 'users', 'change_email', 'flag', NULL),
(49, 'users', 'change_email_period', 'number', NULL),
(50, 'users', 'change_slug', 'flag', NULL),
(51, 'comments', 'times', 'number', NULL),
(52, 'content', 'edit_times', 'number', NULL),
(53, 'content', 'delete_times', 'number', NULL),
(54, 'users', 'wall_add', 'flag', NULL),
(55, 'users', 'wall_delete', 'list', 'own,all'),
(56, 'users', 'ban', 'flag', NULL);

DROP TABLE IF EXISTS `{#}perms_users`;
CREATE TABLE `{#}perms_users` (
  `rule_id` int(11) unsigned DEFAULT NULL COMMENT 'ID правила',
  `group_id` int(11) unsigned DEFAULT NULL COMMENT 'ID группы',
  `subject` varchar(32) DEFAULT NULL COMMENT 'Субъект действия правила',
  `value` varchar(16) NOT NULL COMMENT 'Значение правила',
  KEY `rule_id` (`rule_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Привязка правил доступа к группам пользователей';

INSERT INTO `{#}perms_users` (`rule_id`, `group_id`, `subject`, `value`) VALUES
(10, 4, 'comments', '1'),
(11, 4, 'comments', 'own'),
(15, 4, 'groups', 'yes'),
(17, 4, 'groups', 'own'),
(16, 4, 'groups', 'own'),
(19, 4, 'users', '1'),
(10, 5, 'comments', '1'),
(12, 5, 'comments', 'all'),
(11, 5, 'comments', 'all'),
(14, 5, 'comments', '1'),
(15, 5, 'groups', 'yes'),
(17, 5, 'groups', 'all'),
(16, 5, 'groups', 'all'),
(19, 5, 'users', '1'),
(10, 3, 'comments', '1'),
(12, 3, 'comments', 'own'),
(11, 3, 'comments', 'own'),
(1, 4, 'albums', 'yes'),
(1, 5, 'albums', 'yes'),
(1, 6, 'albums', 'yes'),
(3, 4, 'albums', 'own'),
(3, 5, 'albums', 'all'),
(3, 6, 'albums', 'all'),
(2, 4, 'albums', 'own'),
(2, 5, 'albums', 'all'),
(2, 6, 'albums', 'all'),
(9, 4, 'albums', '1'),
(9, 5, 'albums', '1'),
(9, 6, 'albums', '1'),
(8, 4, 'albums', '1'),
(8, 5, 'albums', '1'),
(8, 6, 'albums', '1'),
(13, 5, 'albums', '1'),
(13, 6, 'albums', '1'),
(10, 6, 'comments', '1'),
(12, 6, 'comments', 'all'),
(11, 6, 'comments', 'all'),
(20, 4, 'comments', '1'),
(20, 5, 'comments', '1'),
(20, 6, 'comments', '1'),
(14, 6, 'comments', '1'),
(23, 5, 'activity', '1'),
(23, 6, 'activity', '1'),
(1, 3, 'albums', 'yes'),
(3, 3, 'albums', 'own'),
(2, 3, 'albums', 'own');

DROP TABLE IF EXISTS `{#}photos`;
CREATE TABLE `{#}photos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `album_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `date_pub` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_photo` timestamp NULL DEFAULT NULL,
  `title` varchar(128) DEFAULT NULL,
  `content_source` text,
  `content` text,
  `image` text NOT NULL,
  `exif` varchar(250) DEFAULT NULL,
  `height` smallint(5) unsigned NOT NULL DEFAULT '0',
  `width` smallint(5) unsigned NOT NULL DEFAULT '0',
  `sizes` varchar(250) DEFAULT NULL,
  `rating` int(11) NOT NULL DEFAULT '0',
  `comments` int(11) unsigned DEFAULT '0',
  `hits_count` int(11) unsigned NOT NULL DEFAULT '0',
  `orientation` enum('square','landscape','portrait','') DEFAULT NULL,
  `type` tinyint(3) unsigned DEFAULT NULL,
  `camera` varchar(50) DEFAULT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `is_private` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) unsigned NOT NULL DEFAULT '0',
  `downloads_count` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`date_pub`),
  KEY `album_id` (`album_id`,`date_pub`,`id`),
  KEY `slug` (`slug`),
  KEY `camera` (`camera`),
  KEY `ordering` (`ordering`),
  FULLTEXT KEY `title` (`title`,`content`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Фотографии фотоальбомов';

DROP TABLE IF EXISTS `{#}rating_log`;
CREATE TABLE `{#}rating_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL COMMENT 'ID пользователя',
  `target_controller` varchar(32) DEFAULT NULL COMMENT 'Компонент (владелец оцениваемого контента)',
  `target_subject` varchar(32) DEFAULT NULL COMMENT 'Субъект (тип оцениваемого контента)',
  `target_id` int(11) unsigned DEFAULT NULL COMMENT 'ID субъекта (записи оцениваемого контента)',
  `score` tinyint(1) DEFAULT NULL COMMENT 'Значение оценки',
  `ip` varbinary(16) DEFAULT NULL COMMENT 'ip-адрес проголосовавшего',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `target_id` (`target_id`,`target_controller`,`target_subject`),
  KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Оценки рейтинга';

DROP TABLE IF EXISTS `{#}rss_feeds`;
CREATE TABLE `{#}rss_feeds` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ctype_id` int(11) unsigned DEFAULT NULL,
  `ctype_name` varchar(32) DEFAULT NULL,
  `title` varchar(128) DEFAULT NULL,
  `description` text,
  `image` text,
  `mapping` text,
  `limit` int(11) unsigned NOT NULL DEFAULT '15',
  `is_enabled` tinyint(1) unsigned DEFAULT NULL,
  `is_cache` tinyint(1) unsigned DEFAULT NULL,
  `cache_interval` int(11) unsigned DEFAULT '60',
  `date_cached` timestamp NULL DEFAULT NULL,
  `template` varchar(30) NOT NULL DEFAULT 'feed' COMMENT 'Шаблон ленты',
  PRIMARY KEY (`id`),
  KEY `ctype_id` (`ctype_id`),
  KEY `ctype_name` (`ctype_name`),
  KEY `is_enabled` (`is_enabled`),
  KEY `is_cache` (`is_cache`),
  KEY `cache_interval` (`cache_interval`),
  KEY `date_cached` (`date_cached`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='RSS ленты';

INSERT INTO `{#}rss_feeds` (`id`, `ctype_id`, `ctype_name`, `title`, `description`, `image`, `mapping`, `limit`, `is_enabled`, `is_cache`, `cache_interval`, `date_cached`) VALUES
(1, NULL, 'comments', 'Комментарии', NULL, NULL, '---\r\ntitle: target_title\r\ndescription: content_html\r\npubDate: date_pub\r\n', 15, 1, NULL, 60, NULL),
(4, 7, 'albums', 'Фотоальбомы', NULL, NULL, '---\ntitle: title\ndescription: content\npubDate: date_pub\nimage: cover_image\nimage_size: normal\n', 15, 1, NULL, 60, NULL);

DROP TABLE IF EXISTS `{#}scheduler_tasks`;
CREATE TABLE `{#}scheduler_tasks` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(250) DEFAULT NULL,
  `controller` varchar(32) DEFAULT NULL,
  `hook` varchar(32) DEFAULT NULL,
  `period` int(11) UNSIGNED DEFAULT NULL,
  `is_strict_period` tinyint(1) UNSIGNED DEFAULT NULL,
  `date_last_run` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) UNSIGNED DEFAULT NULL,
  `is_new` tinyint(1) UNSIGNED DEFAULT '1',
  `consistent_run` tinyint(1) UNSIGNED DEFAULT NULL,
  `ordering` int(11) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `period` (`period`),
  KEY `date_last_run` (`date_last_run`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Задачи планировщика';

INSERT INTO `{#}scheduler_tasks` (`id`, `title`, `controller`, `hook`, `period`, `is_strict_period`, `date_last_run`, `is_active`, `is_new`) VALUES
(1, 'Перевод пользователей между группами', 'users', 'migration', 1440, NULL, NULL, 1, 0),
(2, 'Создание карты сайта', 'sitemap', 'generate', 1440, NULL, NULL, 1, 0),
(3, 'Выдача приглашений пользователям', 'auth', 'send_invites', 1440, NULL, NULL, 1, 0),
(4, 'Публикация контента по расписанию', 'content', 'publication', 1440, NULL, NULL, 1, 1),
(5, 'Очистка удалённых личных сообщений', 'messages', 'clean', 1440, NULL, NULL, 1, 1),
(6, 'Удаление пользователей, не прошедших верификацию', 'auth', 'delete_expired_unverified', 60, NULL, NULL, 1, 1),
(7, 'Удаление просроченных записей из корзины', 'moderation', 'trash', 30, NULL, NULL, 1, 1),
(8, 'Выполняет задачи системной очереди', 'queue', 'run_queue', 1, NULL, NULL, 1, 1),
(9, 'Удаляет просроченные неподтвержденные подписки гостей', 'subscriptions', 'delete_expired_unconfirmed', 1440, 1, DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:05'), 1, 1),
(10, 'Удаляет устаревшие сессии', 'users', 'sessionclean', 10, NULL, NULL, 1, 1),
(11, 'Рассылает уведомления об окончании публикации', 'content', 'publication_notify', 1440, 1, DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:05'), 1, 1);

DROP TABLE IF EXISTS `{#}sessions_online`;
CREATE TABLE `{#}sessions_online` (
  `user_id` int(11) unsigned DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `user_id` (`user_id`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Онлайн сессии';

DROP TABLE IF EXISTS `{#}tags`;
CREATE TABLE `{#}tags` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(32) NOT NULL,
  `frequency` int(11) unsigned NOT NULL DEFAULT '1',
  `tag_title` varchar(300) DEFAULT NULL,
  `tag_desc` varchar(300) DEFAULT NULL,
  `tag_h1` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag` (`tag`),
  UNIQUE KEY `frequency` (`frequency`,`tag`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Список тегов';

DROP TABLE IF EXISTS `{#}tags_bind`;
CREATE TABLE `{#}tags_bind` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) unsigned DEFAULT NULL,
  `target_controller` varchar(32) DEFAULT NULL,
  `target_subject` varchar(32) DEFAULT NULL,
  `target_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `target_id` (`target_id`,`target_controller`,`target_subject`),
  KEY `tag_id` (`tag_id`),
  KEY `target_controller` (`target_controller`,`target_subject`,`tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Привязка тегов к материалам';

DROP TABLE IF EXISTS `{#}uploaded_files`;
CREATE TABLE `{#}uploaded_files` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(170) DEFAULT NULL COMMENT 'Путь к файлу',
  `name` varchar(100) DEFAULT NULL COMMENT 'Имя файла',
  `size` int(11) unsigned DEFAULT NULL COMMENT 'Размер файла',
  `counter` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Счетчик скачиваний',
  `type` varchar(32) DEFAULT 'file' COMMENT  'Тип файла',
  `target_controller` varchar(32) DEFAULT NULL COMMENT 'Контроллер привязки',
  `target_subject` varchar(32) DEFAULT NULL COMMENT 'Субъект привязки',
  `target_id` int(11) unsigned DEFAULT NULL COMMENT 'ID субъекта',
  `user_id` int(11) unsigned DEFAULT NULL COMMENT 'ID владельца',
  `date_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата добавления',
  PRIMARY KEY (`id`),
  UNIQUE KEY `path` (`path`),
  KEY `user_id` (`user_id`),
  KEY `target_controller` (`target_controller`,`target_subject`,`target_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Загруженные файлы';

DROP TABLE IF EXISTS `{#}users`;
CREATE TABLE `{#}users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `groups` text COMMENT 'Массив групп пользователя',
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL COMMENT 'Хеш пароля',
  `password` varchar(32) DEFAULT NULL COMMENT 'Хэш пароля (устаревшее поле)',
  `password_salt` varchar(16) DEFAULT NULL COMMENT 'Соль пароля (устаревшее поле)',
  `is_admin` tinyint(1) unsigned DEFAULT NULL COMMENT 'Администратор?',
  `nickname` varchar(100) NOT NULL COMMENT 'Имя',
  `slug` varchar(100) DEFAULT NULL,
  `date_reg` timestamp NULL DEFAULT NULL COMMENT 'Дата регистрации',
  `date_log` timestamp NULL DEFAULT NULL COMMENT 'Дата последней авторизации',
  `date_group` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Время последней смены группы',
  `ip` varchar(45) DEFAULT NULL,
  `2fa` varchar(32) DEFAULT NULL,
  `is_deleted` tinyint(1) unsigned DEFAULT NULL COMMENT 'Удалён',
  `is_locked` tinyint(1) unsigned DEFAULT NULL COMMENT 'Заблокирован',
  `lock_until` timestamp NULL DEFAULT NULL COMMENT 'Блокировка до',
  `lock_reason` varchar(250) DEFAULT NULL COMMENT 'Причина блокировки',
  `pass_token` varchar(64) DEFAULT NULL COMMENT 'Ключ для восстановления пароля',
  `date_token` timestamp NULL DEFAULT NULL COMMENT 'Дата создания ключа восстановления пароля',
  `friends_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Кол-во друзей',
  `subscribers_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Кол-во подписчиков',
  `time_zone` varchar(32) DEFAULT NULL COMMENT 'Часовой пояс',
  `karma` int(11) NOT NULL DEFAULT '0' COMMENT 'Репутация',
  `rating` int(11) NOT NULL DEFAULT '0' COMMENT 'Рейтинг',
  `theme` text COMMENT 'Настройки темы профиля',
  `notify_options` text COMMENT 'Настройки уведомлений',
  `privacy_options` text COMMENT 'Настройки приватности',
  `status_id` int(11) unsigned DEFAULT NULL COMMENT 'Текстовый статус',
  `status_text` varchar(140) DEFAULT NULL COMMENT 'Текст статуса',
  `inviter_id` int(11) unsigned DEFAULT NULL,
  `invites_count` int(11) unsigned NOT NULL DEFAULT '0',
  `date_invites` timestamp NULL DEFAULT NULL,
  `birth_date` datetime DEFAULT NULL,
  `city` int(11) unsigned DEFAULT NULL,
  `city_cache` varchar(128) DEFAULT NULL,
  `hobby` text,
  `avatar` text,
  `phone` varchar(255) DEFAULT NULL,
  `music` varchar(255) DEFAULT NULL,
  `movies` varchar(255) DEFAULT NULL,
  `site` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `pass_token` (`pass_token`),
  KEY `birth_date` (`birth_date`),
  KEY `city` (`city`),
  KEY `is_admin` (`is_admin`),
  KEY `friends_count` (`friends_count`),
  KEY `karma` (`karma`),
  KEY `rating` (`rating`),
  KEY `is_locked` (`is_locked`),
  KEY `date_reg` (`date_reg`),
  KEY `date_log` (`date_log`),
  KEY `date_group` (`date_group`),
  KEY `inviter_id` (`inviter_id`),
  KEY `date_invites` (`date_invites`),
  KEY `ip` (`ip`),
  KEY `slug` (`slug`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Пользователи';

INSERT INTO `{#}users` (`id`, `groups`, `email`, `password_hash`, `is_admin`, `nickname`, `date_reg`, `date_log`, `date_group`, `ip`, `is_locked`, `lock_until`, `lock_reason`, `pass_token`, `date_token`, `friends_count`, `subscribers_count`, `time_zone`, `karma`, `rating`, `theme`, `notify_options`, `privacy_options`, `status_id`, `status_text`, `inviter_id`, `invites_count`, `date_invites`, `birth_date`, `city`, `city_cache`, `hobby`, `avatar`, `phone`, `music`, `movies`, `site`) VALUES
(1, '---\n- 6\n', 'admin@example.com', NULL, 1, 'admin', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '127.0.0.1', NULL, NULL, NULL, NULL, NULL, 0, 0, 'Europe/Moscow', 0, 0, '---\nbg_img: null\nbg_color: ''#ffffff''\nbg_repeat: no-repeat\nbg_pos_x: left\nbg_pos_y: top\nmargin_top: 0\n', '---\nusers_friend_add: both\nusers_friend_delete: both\ncomments_new: both\ncomments_reply: email\nusers_friend_accept: pm\ngroups_invite: email\nusers_wall_write: email\n', '---\nusers_profile_view: anyone\nmessages_pm: anyone\n', NULL, NULL, NULL, 0, NULL, '1985-10-15 00:00:00', 4400, 'Москва', 'Ротор векторного поля, очевидно, неоднозначен. По сути, уравнение в частных производных масштабирует нормальный лист Мёбиуса, при этом, вместо 13 можно взять любую другую константу.', NULL, '100-20-30', 'Disco House, Minimal techno', 'разные интересные', 'instantcms.ru');

DROP TABLE IF EXISTS `{#}users_contacts`;
CREATE TABLE `{#}users_contacts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL COMMENT 'ID пользователя',
  `contact_id` int(11) unsigned DEFAULT NULL COMMENT 'ID контакта (другого пользователя)',
  `date_last_msg` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Дата последнего сообщения',
  `new_messages` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Кол-во новых сообщений',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`date_last_msg`),
  KEY `contact_id` (`contact_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Контакты пользователей';

DROP TABLE IF EXISTS `{#}users_fields`;
CREATE TABLE `{#}users_fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ctype_id` int(11) unsigned DEFAULT NULL,
  `name` varchar(20) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `hint` varchar(200) DEFAULT NULL,
  `ordering` int(11) unsigned DEFAULT NULL,
  `is_enabled` tinyint(1) UNSIGNED DEFAULT '1',
  `fieldset` varchar(32) DEFAULT NULL,
  `type` varchar(16) DEFAULT NULL,
  `is_in_list` tinyint(1) unsigned DEFAULT NULL,
  `is_in_item` tinyint(1) unsigned DEFAULT NULL,
  `is_in_filter` tinyint(1) unsigned DEFAULT NULL,
  `is_private` tinyint(1) unsigned DEFAULT NULL,
  `is_fixed` tinyint(1) unsigned DEFAULT NULL,
  `is_fixed_type` tinyint(1) unsigned DEFAULT NULL,
  `is_system` tinyint(1) unsigned DEFAULT NULL,
  `values` text,
  `options` text,
  `groups_read` text,
  `groups_add` text,
  `groups_edit` text,
  `filter_view` text,
  PRIMARY KEY (`id`),
  KEY `is_enabled` (`is_enabled`,`ordering`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Поля профилей пользователей';

INSERT INTO `{#}users_fields` (`id`, `ctype_id`, `name`, `title`, `hint`, `ordering`, `fieldset`, `type`, `is_in_list`, `is_in_item`, `is_in_filter`, `is_private`, `is_fixed`, `is_fixed_type`, `is_system`, `values`, `options`, `groups_read`, `groups_edit`) VALUES
(1, NULL, 'birth_date', 'Возраст', NULL, 4, 'Анкета', 'age', NULL, 1, 1, NULL, NULL, NULL, NULL, NULL, '---\ndate_title: Дата рождения\nshow_y: 1\nshow_m: \nshow_d: \nshow_h: \nshow_i: \nrange: YEAR\nlabel_in_item: left\nis_required: \nis_digits: \nis_alphanumeric: \nis_email: \nis_unique: \n', '---\n- 0\n', '---\n- 0\n'),
(2, NULL, 'city', 'Город', 'Укажите город, в котором вы живете', 3, 'Анкета', 'city', NULL, 1, 1, NULL, NULL, NULL, NULL, NULL, '---\nlabel_in_item: left\nis_required: 1\nis_digits: null\nis_alphanumeric: null\nis_email: null\n', '---\n- 0\n', '---\n- 0\n'),
(3, NULL, 'hobby', 'Расскажите о себе', 'Расскажите о ваших интересах и увлечениях', 11, 'О себе', 'text', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '---\nmin_length: 0\nmax_length: 255\nlabel_in_item: none\nis_required: \nis_digits: \nis_alphanumeric: \nis_email: \nis_unique: \n', '---\n- 0\n', '---\n- 0\n'),
(5, NULL, 'nickname', 'Никнейм', 'Ваше имя для отображения на сайте', 1, 'Анкета', 'string', 1, 1, 1, NULL, 1, NULL, 1, NULL, '---\r\nlabel_in_list: left\r\nlabel_in_item: left\r\nis_required: 1\r\nis_digits: \r\nis_number: \r\nis_alphanumeric: \r\nis_email: \r\nis_unique: \r\nshow_symbol_count: 1\r\nmin_length: 2\r\nmax_length: 100\r\n', '---\n- 0\n', '---\n- 0\n'),
(6, NULL, 'avatar', 'Аватар', 'Ваша основная фотография', 2, 'Анкета', 'image', 1, 1, NULL, NULL, 1, NULL, 1, NULL, '---\nvisible_depend: null\nlabel_in_list: left\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_url: null\nis_regexp: null\nrules_regexp_str: \"\"\nis_unique: null\nauthor_access: null\nsize_teaser: small\nsize_full: normal\nsize_modal: \"\"\nsizes:\n  - normal\n  - micro\n  - small\nallow_import_link: null\nallow_image_cropper: 1\nimage_cropper_rounded: null\nimage_cropper_ratio: 1\ndefault_image: null\nshow_to_item_link: 1\n', '---\n- 0\n', '---\n- 0\n'),
(9, NULL, 'phone', 'Телефон', NULL, 7, 'Контакты', 'string', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '---\nmin_length: 0\nmax_length: 255\nlabel_in_item: left\nis_required: \nis_digits: \nis_alphanumeric: \nis_email: \nis_unique: \n', '---\n- 0\n', '---\n- 0\n'),
(10, NULL, 'music', 'Любимая музыка', NULL, 6, 'Предпочтения', 'string', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '---\nmin_length: 0\nmax_length: 255\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\n', '---\n- 0\n', '---\n- 0\n'),
(11, NULL, 'movies', 'Любимые фильмы', NULL, 5, 'Предпочтения', 'string', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '---\nmin_length: 0\nmax_length: 255\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\n', '---\n- 0\n', '---\n- 0\n'),
(12, NULL, 'site', 'Сайт', 'Ваш персональный веб-сайт', 10, 'Контакты', 'url', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '---\nredirect: 1\nauto_http: 1\nlabel_in_item: left\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\n', '---\n- 0\n', '---\n- 0\n');

DROP TABLE IF EXISTS `{#}users_friends`;
CREATE TABLE `{#}users_friends` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL COMMENT 'ID пользователя',
  `friend_id` int(11) unsigned DEFAULT NULL COMMENT 'ID друга',
  `is_mutual` tinyint(1) unsigned DEFAULT NULL COMMENT 'Дружба взаимна?',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`is_mutual`),
  KEY `friend_id` (`friend_id`,`is_mutual`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Дружба пользователей';

DROP TABLE IF EXISTS `{#}users_groups`;
CREATE TABLE `{#}users_groups` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL COMMENT 'Системное имя',
  `title` varchar(32) DEFAULT NULL COMMENT 'Название группы',
  `is_fixed` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Системная?',
  `is_public` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Группу можно выбрать при регистрации?',
  `is_filter` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Выводить группу в фильтре пользователей?',
  `ordering` int(11) UNSIGNED DEFAULT '1' COMMENT 'Порядок',
  PRIMARY KEY (`id`),
  KEY `ordering` (`ordering`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Группы пользователей';

INSERT INTO `{#}users_groups` (`id`, `name`, `title`, `is_fixed`, `is_public`, `is_filter`, `ordering`) VALUES
(1, 'guests', 'Гости', 1, NULL, NULL, 1),
(3, 'newbies', 'Новые', NULL, NULL, NULL, 2),
(4, 'members', 'Пользователи', NULL, NULL, NULL, 3),
(5, 'moderators', 'Модераторы', NULL, NULL, NULL, 4),
(6, 'admins', 'Администраторы', NULL, NULL, 1, 5);

DROP TABLE IF EXISTS `{#}users_groups_members`;
CREATE TABLE `{#}users_groups_members` (
  `user_id` int(11) unsigned NOT NULL,
  `group_id` int(11) unsigned NOT NULL,
  KEY `user_id` (`user_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Привязка пользователей к группам';

INSERT INTO `{#}users_groups_members` (`user_id`, `group_id`) VALUES
(1, 6);

DROP TABLE IF EXISTS `{#}users_groups_migration`;
CREATE TABLE `{#}users_groups_migration` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `is_active` tinyint(1) unsigned DEFAULT NULL,
  `title` varchar(256) DEFAULT NULL,
  `group_from_id` int(11) unsigned DEFAULT NULL,
  `group_to_id` int(11) unsigned DEFAULT NULL,
  `is_keep_group` tinyint(1) unsigned DEFAULT NULL,
  `is_passed` tinyint(1) unsigned DEFAULT NULL,
  `is_rating` tinyint(1) unsigned DEFAULT NULL,
  `is_karma` tinyint(1) unsigned DEFAULT NULL,
  `passed_days` int(11) unsigned DEFAULT NULL,
  `passed_from` tinyint(1) unsigned DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `karma` int(11) DEFAULT NULL,
  `is_notify` tinyint(1) unsigned DEFAULT NULL,
  `notify_text` text,
  PRIMARY KEY (`id`),
  KEY `group_from_id` (`group_from_id`),
  KEY `group_to_id` (`group_to_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Правила перевода между группами';

INSERT INTO `{#}users_groups_migration` (`id`, `is_active`, `title`, `group_from_id`, `group_to_id`, `is_keep_group`, `is_passed`, `is_rating`, `is_karma`, `passed_days`, `passed_from`, `rating`, `karma`, `is_notify`, `notify_text`) VALUES
(1, 1, 'Проверка временем', 3, 4, 0, 1, NULL, NULL, 3, 0, NULL, NULL, 1, 'С момента вашей регистрации прошло 3 дня.\r\nТеперь вам доступны все функции сайта.');

DROP TABLE IF EXISTS `{#}users_ignors`;
CREATE TABLE `{#}users_ignors` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL COMMENT 'ID пользователя',
  `ignored_user_id` int(11) unsigned NOT NULL COMMENT 'ID игнорируемого пользователя',
  PRIMARY KEY (`id`),
  KEY `ignored_user_id` (`ignored_user_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Списки игнорирования';

DROP TABLE IF EXISTS `{#}users_invites`;
CREATE TABLE `{#}users_invites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `code` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `email` (`email`),
  KEY `key` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Выданные инвайты';

DROP TABLE IF EXISTS `{#}users_karma`;
CREATE TABLE `{#}users_karma` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL COMMENT 'Кто поставил',
  `profile_id` int(11) unsigned DEFAULT NULL COMMENT 'Кому поставил',
  `date_pub` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата оценки',
  `points` tinyint(2) DEFAULT NULL COMMENT 'Оценка',
  `comment` varchar(256) DEFAULT NULL COMMENT 'Пояснение',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `profile_id` (`profile_id`),
  KEY `date_pub` (`date_pub`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Оценки репутации пользователей';

DROP TABLE IF EXISTS `{#}users_messages`;
CREATE TABLE `{#}users_messages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `from_id` int(11) unsigned NOT NULL COMMENT 'ID отправителя',
  `to_id` int(11) unsigned NOT NULL COMMENT 'ID получателя',
  `date_pub` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата создания',
  `date_delete` timestamp NULL DEFAULT NULL COMMENT 'Дата удаления',
  `is_new` tinyint(1) unsigned DEFAULT '1' COMMENT 'Не прочитано?',
  `content` text NOT NULL COMMENT 'Текст сообщения',
  `is_deleted` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `to_id` (`to_id`,`from_id`,`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Личные сообщения пользователей';

DROP TABLE IF EXISTS `{#}users_notices`;
CREATE TABLE `{#}users_notices` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `date_pub` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `content` text,
  `options` text,
  `actions` text,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`date_pub`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Уведомления пользователей';

DROP TABLE IF EXISTS `{#}users_statuses`;
CREATE TABLE `{#}users_statuses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL COMMENT 'Пользователь',
  `date_pub` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата публикации',
  `content` varchar(140) DEFAULT NULL COMMENT 'Текст статуса',
  `replies_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Количество ответов',
  `wall_entry_id` int(11) unsigned DEFAULT NULL COMMENT 'ID записи на стене',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `date_pub` (`date_pub`),
  KEY `replies_count` (`replies_count`),
  KEY `wall_entry_id` (`wall_entry_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Текстовые статусы пользователей';

DROP TABLE IF EXISTS `{#}users_tabs`;
CREATE TABLE `{#}users_tabs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(32) DEFAULT NULL,
  `controller` varchar(32) DEFAULT NULL,
  `name` varchar(32) DEFAULT NULL,
  `is_active` tinyint(1) unsigned DEFAULT NULL,
  `ordering` int(11) unsigned DEFAULT NULL,
  `groups_view` text,
  `groups_hide` text,
  `show_only_owner` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `is_active` (`is_active`,`ordering`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Табы профилей';

INSERT INTO `{#}users_tabs` (`id`, `title`, `controller`, `name`, `is_active`, `ordering`) VALUES
(1, 'Лента', 'activity', 'activity', 1, 1),
(3, 'Друзья', 'users', 'friends', 1, 2),
(4, 'Комментарии', 'comments', 'comments', 1, 10),
(5, 'Группы', 'groups', 'groups', 1, 3),
(6, 'Репутация', 'users', 'karma', 1, 11),
(7, 'Подписчики', 'users', 'subscribers', 1, 3),
(8, 'Подписки', 'subscriptions', 'subscriptions', 1, 3);

DROP TABLE IF EXISTS `{#}users_personal_settings`;
CREATE TABLE `{#}users_personal_settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `skey` varchar(150) DEFAULT NULL,
  `settings` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`skey`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Универсальные персональные настройки пользователей';

DROP TABLE IF EXISTS `{#}users_auth_tokens`;
CREATE TABLE `{#}users_auth_tokens` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `auth_token` varchar(128) DEFAULT NULL,
  `date_auth` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_log` timestamp NULL DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `access_type` varchar(100) DEFAULT NULL,
  `ip` varbinary(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `auth_token` (`auth_token`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Токены авторизации';

DROP TABLE IF EXISTS `{#}wall_entries`;
CREATE TABLE `{#}wall_entries` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date_pub` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата публикации',
  `date_last_reply` timestamp NULL DEFAULT NULL COMMENT 'Дата последнего ответа',
  `date_last_modified` timestamp NULL DEFAULT NULL COMMENT 'Дата изменения',
  `controller` varchar(32) DEFAULT NULL COMMENT 'Компонент владелец профиля',
  `profile_type` varchar(32) DEFAULT NULL COMMENT 'Тип профиля (пользователь/группа)',
  `profile_id` int(11) unsigned DEFAULT NULL COMMENT 'ID профиля',
  `user_id` int(11) unsigned DEFAULT NULL COMMENT 'ID автора',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'ID родительской записи',
  `status_id` int(11) unsigned DEFAULT NULL COMMENT 'Связь со статусом пользователя',
  `content` text COMMENT 'Текст записи',
  `content_html` text COMMENT 'Текст после типографа',
  PRIMARY KEY (`id`),
  KEY `date_pub` (`date_pub`),
  KEY `user_id` (`user_id`),
  KEY `parent_id` (`parent_id`),
  KEY `profile_id` (`profile_id`,`profile_type`),
  KEY `status_id` (`status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Записи на стенах профилей';

DROP TABLE IF EXISTS `{#}widgets`;
CREATE TABLE `{#}widgets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `controller` varchar(32) DEFAULT NULL COMMENT 'Контроллер',
  `name` varchar(32) NOT NULL COMMENT 'Системное имя',
  `title` varchar(64) DEFAULT NULL COMMENT 'Название',
  `author` varchar(128) DEFAULT NULL COMMENT 'Имя автора',
  `url` varchar(250) DEFAULT NULL COMMENT 'Сайт автора',
  `version` varchar(8) DEFAULT NULL COMMENT 'Версия',
  `is_external` tinyint(1) DEFAULT '1',
  `files` text COMMENT 'Список файлов виджета (для стороних виджетов)',
  `addon_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID дополнения в официальном каталоге',
  `image_hint_path` varchar(100) DEFAULT NULL COMMENT 'Поясняющее изображение',
  PRIMARY KEY (`id`),
  KEY `version` (`version`),
  KEY `name` (`name`),
  KEY `controller` (`controller`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Доступные виджеты CMS';

INSERT INTO `{#}widgets` (`id`, `controller`, `name`, `title`, `author`, `url`, `version`, `is_external`) VALUES
(1, NULL, 'text', 'Текстовый блок', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(2, 'users', 'list', 'Список пользователей', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(3, NULL, 'menu', 'Меню', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(4, 'content', 'list', 'Список контента', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(5, 'content', 'categories', 'Категории', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(6, 'activity', 'list', 'Лента активности', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(7, 'comments', 'list', 'Новые комментарии', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(8, 'users', 'online', 'Кто онлайн', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(9, 'users', 'avatar', 'Аватар пользователя', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(10, 'tags', 'cloud', 'Облако тегов', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(11, 'content', 'slider', 'Слайдер контента', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(12, 'auth', 'auth', 'Форма авторизации', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(13, 'search', 'search', 'Поиск', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(14, NULL, 'html', 'HTML блок', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(15, 'content', 'filter', 'Фильтр контента', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(16, 'photos', 'list', 'Список фотографий', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(17, 'groups', 'list', 'Список групп', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(18, 'subscriptions', 'button', 'Кнопки подписки', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(19, 'auth', 'register', 'Форма регистрации', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(20, NULL, 'template', 'Элементы шаблона', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(21, 'content', 'fields', 'Поля контента', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(22, 'forms', 'form', 'Форма', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL),
(23, 'content', 'author', 'Автор записи', 'InstantCMS Team', 'https://instantcms.ru', '2.0', NULL);

DROP TABLE IF EXISTS `{#}widgets_bind`;
CREATE TABLE `{#}widgets_bind` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `template_layouts` varchar(500) DEFAULT NULL,
  `languages` varchar(100) DEFAULT NULL,
  `widget_id` int(11) UNSIGNED NOT NULL,
  `title` varchar(128) DEFAULT NULL COMMENT 'Заголовок',
  `links` text,
  `class` varchar(64) DEFAULT NULL COMMENT 'CSS класс',
  `class_title` varchar(64) DEFAULT NULL,
  `class_wrap` varchar(64) DEFAULT NULL,
  `is_title` tinyint(1) UNSIGNED DEFAULT '1' COMMENT 'Показывать заголовок',
  `is_tab_prev` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Объединять с предыдущим?',
  `groups_view` text COMMENT 'Показывать группам',
  `groups_hide` text COMMENT 'Не показывать группам',
  `options` text COMMENT 'Опции',
  `tpl_body` varchar(128) DEFAULT NULL,
  `tpl_wrap` varchar(128) DEFAULT NULL,
  `tpl_wrap_custom` text,
  `tpl_wrap_style` varchar(50) DEFAULT NULL,
  `device_types` varchar(50) DEFAULT NULL,
  `is_cacheable` tinyint(1) UNSIGNED DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `widget_id` (`widget_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Виджеты сайта';

DROP TABLE IF EXISTS `{#}widgets_bind_pages`;
CREATE TABLE `{#}widgets_bind_pages` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `bind_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID параметров виджета',
  `template` varchar(30) DEFAULT NULL COMMENT 'Привязка к шаблону',
  `is_enabled` tinyint(1) UNSIGNED DEFAULT NULL COMMENT 'Включен?',
  `page_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID страницы для вывода',
  `position` varchar(32) DEFAULT NULL COMMENT 'Имя позиции',
  `ordering` int(11) UNSIGNED DEFAULT NULL COMMENT 'Порядковый номер',
  PRIMARY KEY (`id`),
  KEY `position` (`position`),
  KEY `page_id` (`page_id`,`position`,`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Привязка виджетов к страницам';

DROP TABLE IF EXISTS `{#}widgets_pages`;
CREATE TABLE `{#}widgets_pages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `controller` varchar(32) DEFAULT NULL COMMENT 'Компонент',
  `name` varchar(64) DEFAULT NULL COMMENT 'Системное имя',
  `title_const` varchar(64) DEFAULT NULL COMMENT 'Название страницы (языковая константа)',
  `title_subject` varchar(64) DEFAULT NULL COMMENT 'Название субъекта (передается в языковую константу)',
  `title` varchar(64) DEFAULT NULL,
  `url_mask` text COMMENT 'Маска URL',
  `url_mask_not` text COMMENT 'Отрицательная маска',
  `groups` text COMMENT 'Группы доступа',
  `countries` text COMMENT 'Страны доступа',
  `body_css` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `controller` (`controller`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `{#}widgets_pages` (`id`, `controller`, `name`, `title_const`, `title_subject`, `title`, `url_mask`, `url_mask_not`) VALUES
(100, 'users', 'list', 'LANG_USERS_LIST', NULL, NULL, 'users\r\nusers/index\r\nusers/index/*', NULL),
(101, 'users', 'profile', 'LANG_USERS_PROFILE', NULL, NULL, 'users/%*', 'users/%/edit'),
(102, 'users', 'edit', 'LANG_USERS_EDIT_PROFILE', NULL, NULL, 'users/%/edit', NULL),
(155, 'content', 'albums.all', 'LANG_WP_CONTENT_ALL_PAGES', NULL, NULL, 'albums\nalbums-*\nalbums/*', NULL),
(156, 'content', 'albums.list', 'LANG_WP_CONTENT_LIST', NULL, NULL, 'albums\nalbums-*\nalbums/*', 'albums/*/view-*\nalbums/*.html\nalbums/add\nalbums/add?*\nalbums/add/%\nalbums/addcat\nalbums/addcat/%\nalbums/editcat/%\nalbums/edit/*'),
(157, 'content', 'albums.item', 'LANG_WP_CONTENT_ITEM', NULL, NULL, 'albums/*.html', NULL),
(158, 'content', 'albums.edit', 'LANG_WP_CONTENT_ITEM_EDIT', NULL, NULL, 'albums/add\nalbums/add/%\nalbums/edit/*', NULL),
(167, 'photos', 'item', 'LANG_PHOTOS_WP_ITEM', NULL, NULL, 'photos/*.html', NULL),
(168, 'photos', 'upload', 'LANG_PHOTOS_WP_UPLOAD', NULL, NULL, 'photos/upload/%\r\nphotos/upload', NULL),
(169, 'groups', 'list', 'LANG_GROUPS_LIST', NULL, NULL, 'groups', NULL),
(200, NULL, 'all', 'LANG_WP_ALL_PAGES', NULL, NULL, NULL, NULL);

UPDATE `{#}widgets_pages` SET `id` = 0 WHERE `id` = 200;

DROP TABLE IF EXISTS `{#}wysiwygs_presets`;
CREATE TABLE `{#}wysiwygs_presets` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `wysiwyg_name` varchar(40) DEFAULT NULL COMMENT 'Имя редактора',
  `options` text COMMENT 'Опции',
  `title` varchar(100) DEFAULT NULL COMMENT 'Название пресета',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Пресеты для wysiwyg редакторов';

INSERT INTO `{#}wysiwygs_presets` (`id`, `wysiwyg_name`, `options`, `title`) VALUES
(1, 'markitup', '{\"buttons\":[\"0\",\"1\",\"2\",\"3\",\"4\",\"5\",\"7\",\"14\"],\"skin\":\"simple\"}', 'Фотографии'),
(2, 'redactor', '{\"plugins\":[\"smiles\"],\"buttons\":[\"bold\",\"italic\",\"deleted\",\"unorderedlist\",\"image\",\"video\",\"link\"],\"convertVideoLinks\":1,\"convertDivs\":null,\"toolbarFixedBox\":null,\"autoresize\":null,\"pastePlainText\":1,\"removeEmptyTags\":1,\"linkNofollow\":1,\"minHeight\":\"58\",\"placeholder\":\"\\u0412\\u0432\\u0435\\u0434\\u0438\\u0442\\u0435 \\u0441\\u043e\\u043e\\u0431\\u0449\\u0435\\u043d\\u0438\\u0435\"}', 'Редактор для личных сообщений'),
(3, 'tinymce', '{\"toolbar\":\"formatselect codesample blockquote | bold italic underline strikethrough numlist bullist | image link unlink media table hr  emoticons spoiler-add | fullscreen\",\"quickbars_selection_toolbar\":\"bold italic underline | quicklink h2 h3 blockquote\",\"quickbars_insert_toolbar\":\"quickimage quicktable\",\"plugins\":[\"autoresize\",\"paste\"],\"skin\":\"icms\",\"forced_root_block\":\"p\",\"block_formats\":[\"p\",\"h2\",\"h3\",\"h4\",\"h5\"],\"toolbar_drawer\":\"\",\"image_caption\":null,\"image_title\":1,\"image_description\":null,\"image_dimensions\":null,\"image_advtab\":null,\"statusbar\":null,\"paste_as_text\":1,\"min_height\":350,\"max_height\":900,\"images_preset\":\"big\",\"allow_mime_types\":{\"3\":null,\"4\":null,\"5\":null,\"6\":null}}', 'По умолчанию'),
(4, 'tinymce', '{\"toolbar\":\"bold italic underline strikethrough | numlist bullist blockquote | link image media spoiler-add | emoticons\",\"quickbars_selection_toolbar\":\"bold italic underline | quicklink blockquote\",\"quickbars_insert_toolbar\":\"quickimage\",\"plugins\":[\"autoresize\"],\"skin\":\"icms\",\"forced_root_block\":\"p\",\"block_formats\":[\"p\"],\"toolbar_drawer\":\"\",\"image_caption\":null,\"image_title\":null,\"image_description\":null,\"image_dimensions\":null,\"image_advtab\":null,\"statusbar\":null,\"paste_as_text\":1,\"min_height\":350,\"max_height\":700,\"images_preset\":\"big\",\"allow_mime_types\":{\"3\":null,\"4\":null,\"5\":null,\"6\":null}}', 'Для комментариев');