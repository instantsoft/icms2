INSERT INTO `{#}widgets_bind` (`template_layouts`, `languages`, `widget_id`, `title`, `links`, `class`, `class_title`, `class_wrap`, `is_title`, `is_tab_prev`, `groups_view`, `groups_hide`, `options`, `tpl_body`, `tpl_wrap`, `tpl_wrap_style`, `device_types`, `is_cacheable`) VALUES
(NULL, NULL, (SELECT id FROM `{#}widgets` WHERE `controller` = 'tags' AND `name` = 'cloud'), 'Облако тегов', NULL, NULL, NULL, NULL, 1, NULL, '---\n', NULL, '---\nsubjects:\n  - 0\nordering: tag\nstyle: cloud\nmax_fs: 22\nmin_fs: 12\nmin_freq: 0\nmin_len: 0\nlimit: 10\ncolors: \'#008cba,#6610f2,#e83e8c,#f04124,#e99002,#43ac6a,#5bc0de\'\nshuffle: null\n', 'cloud', 'wrapper', 'icms-widget__compact', NULL, 1);

INSERT INTO `{#}widgets_bind_pages` (`bind_id`, `template`, `is_enabled`, `page_id`, `position`, `ordering`) VALUES
((SELECT LAST_INSERT_ID()), 'modern', 1, 1, 'pos_40', 0);