INSERT INTO `{#}widgets_bind` (`template_layouts`, `languages`, `widget_id`, `title`, `links`, `class`, `class_title`, `class_wrap`, `is_title`, `is_tab_prev`, `groups_view`, `groups_hide`, `options`, `tpl_body`, `tpl_wrap`, `device_types`) VALUES
(NULL, NULL, (SELECT id FROM `{#}widgets` WHERE `controller` = 'content' AND `name` = 'list'), 'Фотоальбомы', 'Все альбомы | albums\r\n{Загрузить фото | photos/upload}', NULL, NULL, NULL, 1, 1, '---\n- 0\n', NULL, '---\nctype_id: 7\ndataset:\nimage_field: cover_image\nteaser_field:\nshow_details: null\nlimit: 5\n', 'list_tiles_big', 'wrapper', NULL);

INSERT INTO `{#}widgets_bind_pages` (`bind_id`, `template`, `is_enabled`, `page_id`, `position`, `ordering`) VALUES
((SELECT LAST_INSERT_ID()), 'default', 1, 1, 'left-bottom', 2);