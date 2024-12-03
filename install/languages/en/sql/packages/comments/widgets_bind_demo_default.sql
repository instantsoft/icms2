INSERT INTO `{#}widgets_bind` (`template_layouts`, `languages`, `widget_id`, `title`, `links`, `class`, `class_title`, `class_wrap`, `is_title`, `is_tab_prev`, `groups_view`, `groups_hide`, `options`, `tpl_body`, `tpl_wrap`, `device_types`) VALUES
(NULL, NULL, (SELECT id FROM `{#}widgets` WHERE `controller` = 'comments' AND `name` = 'list'), 'Latest comments', 'All | comments\r\n{My friends | comments/index/friends}\r\n{My | comments/index/my}', NULL, NULL, NULL, 1, 1, '---\n- 0\n', NULL, '---\nshow_avatars: 1\nshow_text: 1\nlimit: 10\n', NULL, 'wrapper', NULL);

INSERT INTO `{#}widgets_bind_pages` (`bind_id`, `template`, `is_enabled`, `page_id`, `position`, `ordering`) VALUES
((SELECT LAST_INSERT_ID()), 'default', 1, 1, 'left-bottom', 4);