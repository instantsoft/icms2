INSERT INTO `{#}widgets_bind` (`id`, `template_layouts`, `languages`, `widget_id`, `title`, `links`, `class`, `class_title`, `class_wrap`, `is_title`, `is_tab_prev`, `groups_view`, `groups_hide`, `options`, `tpl_body`, `tpl_wrap`, `tpl_wrap_style`, `device_types`, `is_cacheable`, `id`, `bind_id`, `template`, `is_enabled`, `page_id`, `position`, `ordering`) VALUES
(45, NULL, NULL, 3, 'Main menu', NULL, NULL, NULL, NULL, NULL, NULL, '---\n', NULL, '---\nmenu: main\ntemplate: menu\nclass: icms-menu-hovered mx-lg-n2\nis_detect: 1\nmax_items: 0\nmenu_type: navbar\nnavbar_expand: navbar-expand-lg\nnavbar_color_scheme: navbar-dark\nmenu_navbar_style: navbar-nav\nshow_search_form: 2\ntoggler_icon: 1\ntoggler_show_sitename: 1\nmenu_nav_style: nav\nmenu_nav_style_column:\nmenu_is_pills: null\nmenu_is_fill:\n', 'menu', NULL, NULL, NULL, 1, 51, 45, 'modern', 1, 0, 'pos_29', 0),
(50, NULL, NULL, 20, 'Page body', NULL, NULL, NULL, NULL, NULL, NULL, '---\n', NULL, '---\ntype: body\nsession_type: on_position\nbreadcrumbs:\n  template: breadcrumbs\n  strip_last: null\n', 'template', NULL, NULL, NULL, 1, 56, 50, 'modern', 1, 0, 'pos_8', 1),
(51, NULL, NULL, 20, 'Breadcrumbs', NULL, NULL, NULL, NULL, NULL, NULL, '---\n', NULL, '---\ntype: breadcrumbs\nsession_type: on_position\nbreadcrumbs:\n  template: breadcrumbs\n  strip_last: null\n', 'template', NULL, NULL, NULL, 1, 57, 51, 'modern', 1, 0, 'pos_10', 0),
(52, NULL, NULL, 20, 'Session messages', NULL, NULL, NULL, NULL, NULL, NULL, '---\n', NULL, '---\ntype: smessages\nsession_type: toastr\nbreadcrumbs:\n  template: breadcrumbs\n  strip_last: null\n', 'template', NULL, NULL, NULL, 1, 58, 52, 'modern', 1, 0, 'pos_8', 0),
(54, NULL, NULL, 20, 'Copyright / Debug', NULL, NULL, NULL, NULL, NULL, NULL, '---\n', NULL, '---\ntype: copyright\nsession_type: on_position\nbreadcrumbs:\n  template: breadcrumbs\n  strip_last: null\n', 'template', NULL, NULL, NULL, 1, 60, 54, 'modern', 1, 0, 'pos_11', 0),
(57, NULL, NULL, 20, 'Site off message', NULL, NULL, NULL, NULL, NULL, NULL, '---\n', NULL, '---\ntype: site_closed\nsession_type: on_position\nbreadcrumbs:\n  template: breadcrumbs\n  strip_last: null\n', 'template', NULL, NULL, NULL, 1, 63, 57, 'modern', 1, 0, 'pos_22', 0),
(62, NULL, NULL, 20, 'Logo', NULL, NULL, NULL, NULL, NULL, NULL, '---\n', NULL, '---\ntype: logo\nsession_type: on_position\nbreadcrumbs:\n  template: breadcrumbs\n  strip_last: null\n', 'template', NULL, NULL, NULL, 1, 68, 62, 'modern', 1, 0, 'pos_27', 0),
(68, NULL, NULL, 9, 'User menu', NULL, NULL, NULL, NULL, NULL, NULL, '---\n', '---\n- 1\n', '---\nmenu: personal\nis_detect: null\nmax_items: 0\n', 'avatar', NULL, NULL, NULL, 1, 74, 68, 'modern', 1, 0, 'pos_31', 2),
(70, NULL, NULL, 3, 'Actions', NULL, NULL, NULL, NULL, 1, NULL, '---\n', NULL, '---\nmenu: toolbar\ntemplate: menu\nclass:\nis_detect: null\nmax_items: 0\nmenu_type: navbar\nnavbar_expand:\nnavbar_color_scheme: navbar-dark\nshow_search_form: 0\ntoggler_icon: 1\ntoggler_show_sitename: null\nmenu_nav_style: nav\nmenu_nav_style_column:\nmenu_is_pills: null\nmenu_is_fill:\n', 'menu_dropdown', NULL, NULL, NULL, 1, 76, 70, 'modern', 1, 0, 'pos_10', 1),
(76, NULL, NULL, 3, 'Auth menu', NULL, NULL, NULL, NULL, NULL, NULL, '---\n- 1\n', NULL, '---\nmenu: header\ntemplate: menu\nclass:\nis_detect: 1\nmax_items: 0\nmenu_type: navbar\nnavbar_expand:\nnavbar_color_scheme: navbar-dark\nmenu_navbar_style: navbar-nav\nshow_search_form: 0\ntoggler_icon: null\ntoggler_show_sitename: null\nmenu_nav_style: nav\nmenu_nav_style_column:\nmenu_is_pills: null\nmenu_is_fill:\n', 'menu', NULL, NULL, NULL, 1, 82, 76, 'modern', 1, 0, 'pos_31', 0),
(77, NULL, NULL, 3, 'Notification Menu', NULL, NULL, NULL, 'mr-3', NULL, NULL, '---\n', NULL, '---\nmenu: notices\ntemplate: menu\nclass:\nis_detect: 1\nmax_items: 0\nmenu_type: navbar\nnavbar_expand:\nnavbar_color_scheme: navbar-dark\nmenu_navbar_style: navbar-nav\nshow_search_form: 0\ntoggler_icon: null\ntoggler_show_sitename: null\nmenu_nav_style: nav\nmenu_nav_style_column:\nmenu_is_pills: null\nmenu_is_fill:\n', 'menu', NULL, NULL, NULL, 1, 83, 77, 'modern', 1, 0, 'pos_31', 1);

INSERT INTO `{#}widgets_bind_pages` (`id`, `bind_id`, `template`, `is_enabled`, `page_id`, `position`, `ordering`) VALUES
(51, 45, 'modern', 1, 0, 'pos_29', 0),
(56, 50, 'modern', 1, 0, 'pos_8', 1),
(57, 51, 'modern', 1, 0, 'pos_10', 0),
(58, 52, 'modern', 1, 0, 'pos_8', 0),
(60, 54, 'modern', 1, 0, 'pos_11', 0),
(63, 57, 'modern', 1, 0, 'pos_22', 0),
(68, 62, 'modern', 1, 0, 'pos_27', 0),
(74, 68, 'modern', 1, 0, 'pos_31', 2),
(76, 70, 'modern', 1, 0, 'pos_10', 1),
(82, 76, 'modern', 1, 0, 'pos_31', 0),
(83, 77, 'modern', 1, 0, 'pos_31', 1);
