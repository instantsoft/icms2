DELETE {#}widgets_bind, {#}widgets_bind_pages FROM {#}widgets_bind INNER JOIN {#}widgets_bind_pages ON {#}widgets_bind.id = {#}widgets_bind_pages.bind_id WHERE {#}widgets_bind_pages.template = 'modern';

INSERT INTO `{#}widgets_bind` (`template_layouts`, `languages`, `widget_id`, `title`, `links`, `class`, `class_title`, `class_wrap`, `is_title`, `is_tab_prev`, `groups_view`, `groups_hide`, `options`, `tpl_body`, `tpl_wrap`, `tpl_wrap_style`, `device_types`, `is_cacheable`) VALUES
(NULL, NULL, (SELECT id FROM `{#}widgets` WHERE `controller` IS NULL AND `name` = 'menu'), 'Главное меню', NULL, NULL, NULL, NULL, NULL, NULL, '---\n', NULL, '---\nmenu: main\ntemplate: menu\nclass: icms-menu-hovered mx-lg-n2\nis_detect: 1\nmax_items: 0\nmenu_type: navbar\nnavbar_expand: navbar-expand-lg\nnavbar_color_scheme: navbar-dark\nmenu_navbar_style: navbar-nav\nshow_search_form: 2\ntoggler_icon: 1\ntoggler_show_sitename: 1\nmenu_nav_style: nav\nmenu_nav_style_column:\nmenu_is_pills: null\nmenu_is_fill:\n', 'menu', NULL, NULL, NULL, 1);

INSERT INTO `{#}widgets_bind_pages` (`bind_id`, `template`, `is_enabled`, `page_id`, `position`, `ordering`) VALUES
((SELECT LAST_INSERT_ID()), 'modern', 1, 0, 'pos_29', 0);

INSERT INTO `{#}widgets_bind` (`template_layouts`, `languages`, `widget_id`, `title`, `links`, `class`, `class_title`, `class_wrap`, `is_title`, `is_tab_prev`, `groups_view`, `groups_hide`, `options`, `tpl_body`, `tpl_wrap`, `tpl_wrap_style`, `device_types`, `is_cacheable`) VALUES
(NULL, NULL, (SELECT id FROM `{#}widgets` WHERE `controller` IS NULL AND `name` = 'template'), 'Тело страницы', NULL, NULL, NULL, NULL, NULL, NULL, '---\n', NULL, '---\ntype: body\nsession_type: on_position\nbreadcrumbs:\n  template: breadcrumbs\n  strip_last: null\n', 'template', NULL, NULL, NULL, 1);

INSERT INTO `{#}widgets_bind_pages` (`bind_id`, `template`, `is_enabled`, `page_id`, `position`, `ordering`) VALUES
((SELECT LAST_INSERT_ID()), 'modern', 1, 0, 'pos_8', 1);

INSERT INTO `{#}widgets_bind` (`template_layouts`, `languages`, `widget_id`, `title`, `links`, `class`, `class_title`, `class_wrap`, `is_title`, `is_tab_prev`, `groups_view`, `groups_hide`, `options`, `tpl_body`, `tpl_wrap`, `tpl_wrap_style`, `device_types`, `is_cacheable`) VALUES
(NULL, NULL, (SELECT id FROM `{#}widgets` WHERE `controller` IS NULL AND `name` = 'template'), 'Глубиномер', NULL, NULL, NULL, NULL, NULL, NULL, '---\n', NULL, '---\ntype: breadcrumbs\nsession_type: on_position\nbreadcrumbs:\n  template: breadcrumbs\n  strip_last: null\n', 'template', NULL, NULL, NULL, 1);

INSERT INTO `{#}widgets_bind_pages` (`bind_id`, `template`, `is_enabled`, `page_id`, `position`, `ordering`) VALUES
((SELECT LAST_INSERT_ID()), 'modern', 1, 0, 'pos_10', 0);

INSERT INTO `{#}widgets_bind` (`template_layouts`, `languages`, `widget_id`, `title`, `links`, `class`, `class_title`, `class_wrap`, `is_title`, `is_tab_prev`, `groups_view`, `groups_hide`, `options`, `tpl_body`, `tpl_wrap`, `tpl_wrap_style`, `device_types`, `is_cacheable`) VALUES
(NULL, NULL, (SELECT id FROM `{#}widgets` WHERE `controller` IS NULL AND `name` = 'template'), 'Сообщения сессии', NULL, NULL, NULL, NULL, NULL, NULL, '---\n', NULL, '---\ntype: smessages\nsession_type: toastr\nbreadcrumbs:\n  template: breadcrumbs\n  strip_last: null\n', 'template', NULL, NULL, NULL, 1);

INSERT INTO `{#}widgets_bind_pages` (`bind_id`, `template`, `is_enabled`, `page_id`, `position`, `ordering`) VALUES
((SELECT LAST_INSERT_ID()), 'modern', 1, 0, 'pos_8', 0);

INSERT INTO `{#}widgets_bind` (`template_layouts`, `languages`, `widget_id`, `title`, `links`, `class`, `class_title`, `class_wrap`, `is_title`, `is_tab_prev`, `groups_view`, `groups_hide`, `options`, `tpl_body`, `tpl_wrap`, `tpl_wrap_style`, `device_types`, `is_cacheable`) VALUES
(NULL, NULL, (SELECT id FROM `{#}widgets` WHERE `controller` IS NULL AND `name` = 'template'), 'Копирайт/отладка', NULL, NULL, NULL, NULL, NULL, NULL, '---\n', NULL, '---\ntype: copyright\nsession_type: on_position\nbreadcrumbs:\n  template: breadcrumbs\n  strip_last: null\n', 'template', NULL, NULL, NULL, 1);

INSERT INTO `{#}widgets_bind_pages` (`bind_id`, `template`, `is_enabled`, `page_id`, `position`, `ordering`) VALUES
((SELECT LAST_INSERT_ID()), 'modern', 1, 0, 'pos_11', 0);

INSERT INTO `{#}widgets_bind` (`template_layouts`, `languages`, `widget_id`, `title`, `links`, `class`, `class_title`, `class_wrap`, `is_title`, `is_tab_prev`, `groups_view`, `groups_hide`, `options`, `tpl_body`, `tpl_wrap`, `tpl_wrap_style`, `device_types`, `is_cacheable`) VALUES
(NULL, NULL, (SELECT id FROM `{#}widgets` WHERE `controller` IS NULL AND `name` = 'template'), 'Сообщение об отключении сайта', NULL, NULL, NULL, NULL, NULL, NULL, '---\n', NULL, '---\ntype: site_closed\nsession_type: on_position\nbreadcrumbs:\n  template: breadcrumbs\n  strip_last: null\n', 'template', NULL, NULL, NULL, 1);

INSERT INTO `{#}widgets_bind_pages` (`bind_id`, `template`, `is_enabled`, `page_id`, `position`, `ordering`) VALUES
((SELECT LAST_INSERT_ID()), 'modern', 1, 0, 'pos_22', 0);

INSERT INTO `{#}widgets_bind` (`template_layouts`, `languages`, `widget_id`, `title`, `links`, `class`, `class_title`, `class_wrap`, `is_title`, `is_tab_prev`, `groups_view`, `groups_hide`, `options`, `tpl_body`, `tpl_wrap`, `tpl_wrap_style`, `device_types`, `is_cacheable`) VALUES
(NULL, NULL, (SELECT id FROM `{#}widgets` WHERE `controller` IS NULL AND `name` = 'template'), 'Лого', NULL, NULL, NULL, NULL, NULL, NULL, '---\n', NULL, '---\ntype: logo\nsession_type: on_position\nbreadcrumbs:\n  template: breadcrumbs\n  strip_last: null\n', 'template', NULL, NULL, NULL, 1);

INSERT INTO `{#}widgets_bind_pages` (`bind_id`, `template`, `is_enabled`, `page_id`, `position`, `ordering`) VALUES
((SELECT LAST_INSERT_ID()), 'modern', 1, 0, 'pos_27', 0);

INSERT INTO `{#}widgets_bind` (`template_layouts`, `languages`, `widget_id`, `title`, `links`, `class`, `class_title`, `class_wrap`, `is_title`, `is_tab_prev`, `groups_view`, `groups_hide`, `options`, `tpl_body`, `tpl_wrap`, `tpl_wrap_style`, `device_types`, `is_cacheable`) VALUES
(NULL, NULL, (SELECT id FROM `{#}widgets` WHERE `controller` = 'users' AND `name` = 'avatar'), 'Меню пользователя', NULL, NULL, NULL, NULL, NULL, NULL, '---\n', '---\n- 1\n', '---\nmenu: personal\nis_detect: null\nmax_items: 0\n', 'avatar', NULL, NULL, NULL, 1);

INSERT INTO `{#}widgets_bind_pages` (`bind_id`, `template`, `is_enabled`, `page_id`, `position`, `ordering`) VALUES
((SELECT LAST_INSERT_ID()), 'modern', 1, 0, 'pos_31', 2);

INSERT INTO `{#}widgets_bind` (`template_layouts`, `languages`, `widget_id`, `title`, `links`, `class`, `class_title`, `class_wrap`, `is_title`, `is_tab_prev`, `groups_view`, `groups_hide`, `options`, `tpl_body`, `tpl_wrap`, `tpl_wrap_style`, `device_types`, `is_cacheable`) VALUES
(NULL, NULL, (SELECT id FROM `{#}widgets` WHERE `controller` IS NULL AND `name` = 'menu'), 'Действия', NULL, NULL, NULL, NULL, 1, NULL, '---\n', NULL, '---\nmenu: toolbar\ntemplate: menu\nclass:\nis_detect: null\nmax_items: 0\nmenu_type: navbar\nnavbar_expand:\nnavbar_color_scheme: navbar-dark\nshow_search_form: 0\ntoggler_icon: 1\ntoggler_show_sitename: null\nmenu_nav_style: nav\nmenu_nav_style_column:\nmenu_is_pills: null\nmenu_is_fill:\n', 'menu_dropdown', NULL, NULL, NULL, 1);

INSERT INTO `{#}widgets_bind_pages` (`bind_id`, `template`, `is_enabled`, `page_id`, `position`, `ordering`) VALUES
((SELECT LAST_INSERT_ID()), 'modern', 1, 0, 'pos_10', 1);

INSERT INTO `{#}widgets_bind` (`template_layouts`, `languages`, `widget_id`, `title`, `links`, `class`, `class_title`, `class_wrap`, `is_title`, `is_tab_prev`, `groups_view`, `groups_hide`, `options`, `tpl_body`, `tpl_wrap`, `tpl_wrap_style`, `device_types`, `is_cacheable`) VALUES
(NULL, NULL, (SELECT id FROM `{#}widgets` WHERE `controller` IS NULL AND `name` = 'menu'), 'Меню авторизации', NULL, NULL, NULL, NULL, NULL, NULL, '---\n- 1\n', NULL, '---\nmenu: header\ntemplate: menu\nclass:\nis_detect: 1\nmax_items: 0\nmenu_type: navbar\nnavbar_expand:\nnavbar_color_scheme: navbar-dark\nmenu_navbar_style: navbar-nav\nshow_search_form: 0\ntoggler_icon: null\ntoggler_show_sitename: null\nmenu_nav_style: nav\nmenu_nav_style_column:\nmenu_is_pills: null\nmenu_is_fill:\n', 'menu', NULL, NULL, NULL, 1);

INSERT INTO `{#}widgets_bind_pages` (`bind_id`, `template`, `is_enabled`, `page_id`, `position`, `ordering`) VALUES
((SELECT LAST_INSERT_ID()), 'modern', 1, 0, 'pos_31', 0);

INSERT INTO `{#}widgets_bind` (`template_layouts`, `languages`, `widget_id`, `title`, `links`, `class`, `class_title`, `class_wrap`, `is_title`, `is_tab_prev`, `groups_view`, `groups_hide`, `options`, `tpl_body`, `tpl_wrap`, `tpl_wrap_style`, `device_types`, `is_cacheable`) VALUES
(NULL, NULL, (SELECT id FROM `{#}widgets` WHERE `controller` IS NULL AND `name` = 'menu'), 'Меню уведомлений', NULL, NULL, NULL, 'mr-3', NULL, NULL, '---\n', NULL, '---\nmenu: notices\ntemplate: menu\nclass:\nis_detect: 1\nmax_items: 0\nmenu_type: navbar\nnavbar_expand:\nnavbar_color_scheme: navbar-dark\nmenu_navbar_style: navbar-nav\nshow_search_form: 0\ntoggler_icon: null\ntoggler_show_sitename: null\nmenu_nav_style: nav\nmenu_nav_style_column:\nmenu_is_pills: null\nmenu_is_fill:\n', 'menu', NULL, NULL, NULL, 1);

INSERT INTO `{#}widgets_bind_pages` (`bind_id`, `template`, `is_enabled`, `page_id`, `position`, `ordering`) VALUES
((SELECT LAST_INSERT_ID()), 'modern', 1, 0, 'pos_31', 1);
