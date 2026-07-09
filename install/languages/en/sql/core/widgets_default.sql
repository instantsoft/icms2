INSERT INTO `{#}widgets_bind` (`id`, `template_layouts`, `languages`, `widget_id`, `title`, `links`, `class`, `class_title`, `class_wrap`, `is_title`, `is_tab_prev`, `groups_view`, `groups_hide`, `options`, `tpl_body`, `tpl_wrap`, `device_types`) VALUES
(1, NULL, NULL, 3, 'Main menu', NULL, NULL, NULL, NULL, NULL, NULL, '---\n- 0\n', NULL, '---\nmenu: main\nis_detect: 1\nmax_items: 8\n', NULL, NULL, NULL),
(2, NULL, NULL, 3, 'Auth menu', NULL, NULL, NULL, NULL, NULL, NULL, '---\n- 1\n', NULL, '---\nmenu: header\nis_detect: 1\nmax_items: 0\n', NULL, NULL, NULL),
(5, NULL, NULL, 3, 'Actions menu', NULL, NULL, NULL, 'fixed_actions_menu', NULL, NULL, '---\n- 0\n', NULL, '---\nmenu: toolbar\ntemplate: menu\nis_detect: null\nmax_items: 0\n', 'menu', 'wrapper', NULL),
(20, NULL, NULL, 12, 'Log in', NULL, NULL, NULL, NULL, 1, NULL, '---\n- 0\n', NULL, '', NULL, 'wrapper', NULL),
(22, NULL, NULL, 9, 'User menu', NULL, NULL, NULL, NULL, NULL, NULL, '---\n- 0\n', '---\n- 1\n', '---\nmenu: personal\nis_detect: 1\nmax_items: 0\n', 'avatar', NULL, NULL),
(23, NULL, NULL, 3, 'Notifications', NULL, NULL, NULL, NULL, NULL, NULL, '---\n- 0\n', '---\n- 1\n', '---\nmenu: notices\ntemplate: menu\nis_detect: null\nmax_items: 0\n', 'menu', NULL, NULL);

INSERT INTO `{#}widgets_bind_pages` (`id`, `bind_id`, `template`, `is_enabled`, `page_id`, `position`, `ordering`) VALUES
(1, 1, 'default', 1, 0, 'top', 0),
(2, 2, 'default', 1, 0, 'header', 0),
(3, 5, 'default', 1, 0, 'left-top', 0),
(4, 20, 'default', 1, 0, 'right-center', 0),
(5, 22, 'default', 1, 0, 'header', 1),
(6, 23, 'default', 1, 0, 'header', 2);