INSERT INTO `{#}widgets_bind` (`template_layouts`, `languages`, `widget_id`, `title`, `links`, `class`, `class_title`, `class_wrap`, `is_title`, `is_tab_prev`, `groups_view`, `groups_hide`, `options`, `tpl_body`, `tpl_wrap`, `tpl_wrap_style`, `device_types`, `is_cacheable`) VALUES
(NULL, NULL, (SELECT id FROM `{#}widgets` WHERE `controller` = 'search' AND `name` = 'search'), '{solid%search} Поиск', NULL, NULL, NULL, NULL, 1, NULL, '---\n', NULL, '---\nshow_input: 1\nshow_btn: null\nshow_search_params: null\n', 'search', 'wrapper', 'icms-widget__transparent', NULL, 1);

INSERT INTO `{#}widgets_bind_pages` (`bind_id`, `template`, `is_enabled`, `page_id`, `position`, `ordering`) VALUES
((SELECT LAST_INSERT_ID()), 'modern', 1, 1, 'pos_9', 1);