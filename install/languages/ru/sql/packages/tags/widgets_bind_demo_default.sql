INSERT INTO `{#}widgets_bind` (`template_layouts`, `languages`, `widget_id`, `title`, `links`, `class`, `class_title`, `class_wrap`, `is_title`, `is_tab_prev`, `groups_view`, `groups_hide`, `options`, `tpl_body`, `tpl_wrap`, `device_types`) VALUES
(NULL, NULL, (SELECT id FROM `{#}widgets` WHERE `controller` = 'tags' AND `name` = 'cloud'), 'Облако тегов', NULL, NULL, NULL, NULL, 1, NULL, '---\n- 0\n', NULL, '---\nordering: tag\nstyle: cloud\nmax_fs: 22\nmin_fs: 12\nlimit: 10\n', NULL, 'wrapper', NULL);

INSERT INTO `{#}widgets_bind_pages` (`bind_id`, `template`, `is_enabled`, `page_id`, `position`, `ordering`) VALUES
((SELECT LAST_INSERT_ID()), 'default', 1, 1, 'right-bottom', 3);