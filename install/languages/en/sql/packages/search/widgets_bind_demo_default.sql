INSERT INTO `{#}widgets_bind` (`template_layouts`, `languages`, `widget_id`, `title`, `links`, `class`, `class_title`, `class_wrap`, `is_title`, `is_tab_prev`, `groups_view`, `groups_hide`, `options`, `tpl_body`, `tpl_wrap`, `device_types`) VALUES
(NULL, NULL, (SELECT id FROM `{#}widgets` WHERE `controller` = 'search' AND `name` = 'search'), 'Search', NULL, NULL, NULL, NULL, NULL, NULL, '---\n- 0\n', NULL, '', NULL, 'wrapper', NULL);

INSERT INTO `{#}widgets_bind_pages` (`bind_id`, `template`, `is_enabled`, `page_id`, `position`, `ordering`) VALUES
((SELECT LAST_INSERT_ID()), 'default', 1, 1, 'right-top', 0);