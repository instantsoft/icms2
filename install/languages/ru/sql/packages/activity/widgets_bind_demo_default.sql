INSERT INTO `{#}widgets_bind` (`template_layouts`, `languages`, `widget_id`, `title`, `links`, `class`, `class_title`, `class_wrap`, `is_title`, `is_tab_prev`, `groups_view`, `groups_hide`, `options`, `tpl_body`, `tpl_wrap`, `device_types`) VALUES
(NULL, NULL, (SELECT id FROM `{#}widgets` WHERE `controller` = 'activity' AND `name` = 'list'), 'Активность', 'Вся | activity\r\n{Моих друзей | activity/index/friends}\r\n{Моя | activity/index/my}', NULL, NULL, NULL, 1, NULL, '---\n- 0\n', NULL, '---\ndataset: all\nshow_avatars: 1\ndate_group: null\nlimit: 5\n', NULL, 'wrapper', NULL);

INSERT INTO `{#}widgets_bind_pages` (`bind_id`, `template`, `is_enabled`, `page_id`, `position`, `ordering`) VALUES
((SELECT LAST_INSERT_ID()), 'default', 1, 1, 'left-bottom', 3);