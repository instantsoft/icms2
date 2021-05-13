INSERT INTO `{#}widgets_bind` (`id`, `template_layouts`, `languages`, `widget_id`, `title`, `links`, `class`, `class_title`, `class_wrap`, `is_title`, `is_tab_prev`, `groups_view`, `groups_hide`, `options`, `tpl_body`, `tpl_wrap`, `device_types`) VALUES
(6, NULL, NULL, 8, 'Now Online', NULL, NULL, NULL, NULL, 1, NULL, '---\n- 0\n', NULL, '---\nis_avatars: 1\ngroups: null\n', NULL, 'wrapper', NULL),
(8, NULL, NULL, 10, 'Tag cloud', NULL, NULL, NULL, NULL, 1, NULL, '---\n- 0\n', NULL, '---\nordering: tag\nstyle: cloud\nmax_fs: 22\nmin_fs: 12\nlimit: 10\n', NULL, 'wrapper', NULL),
(9, NULL, NULL, 6, 'Activity feed', 'All | activity\r\n{My friends | activity/index/friends}\r\n{My | activity/index/my}', NULL, NULL, NULL, 1, NULL, '---\n- 0\n', NULL, '---\ndataset: all\nshow_avatars: 1\ndate_group: null\nlimit: 5\n', NULL, 'wrapper', NULL),
(10, NULL, NULL, 4, 'Articles', 'All | articles\r\n{Add article | articles/add}', 'columns-2', NULL, NULL, 1, 1, '---\n- 0\n', NULL, '---\nctype_id: 5\ndataset:\nimage_field:\nteaser_field:\nshow_details: 1\nlimit: 5\n', NULL, 'wrapper', NULL),
(11, NULL, NULL, 7, 'Latest comments', 'All | comments\r\n{My friends | comments/index/friends}\r\n{My | comments/index/my}', NULL, NULL, NULL, 1, 1, '---\n- 0\n', NULL, '---\nshow_avatars: 1\nshow_text: 1\nlimit: 10\n', NULL, 'wrapper', NULL),
(12, NULL, NULL, 5, 'Content cats', NULL, NULL, NULL, NULL, 1, NULL, '---\n- 0\n', NULL, '---\nctype_name: 0\nis_root: null\n', NULL, 'wrapper', NULL),
(13, NULL, NULL, 4, 'Photo albums', 'All albums | albums\r\n{Upload photos | photos/upload}', NULL, NULL, NULL, 1, 1, '---\n- 0\n', NULL, '---\nctype_id: 7\ndataset:\nimage_field: cover_image\nteaser_field:\nshow_details: null\nlimit: 5\n', 'list_tiles_big', 'wrapper', NULL),
(14, NULL, NULL, 2, 'New users', 'All | users', NULL, NULL, NULL, 1, NULL, '---\n- 0\n', NULL, '---\nshow: all\ndataset: latest\nstyle: tiles\ngroups: null\nlimit: 10\n', NULL, 'wrapper', NULL),
(15, NULL, NULL, 3, 'Bottom menu', NULL, NULL, NULL, NULL, NULL, NULL, '---\n- 0\n', NULL, '---\nmenu: footer\nis_detect: 1\nmax_items: 0\n', NULL, NULL, NULL),
(16, NULL, NULL, 4, 'News', 'All | news\r\nDiscussed | news-discussed\r\n{Private | news/from_friends}', NULL, NULL, NULL, 1, NULL, '---\n- 0\n', NULL, '---\nctype_id: 10\ncategory_id: 1\ndataset: 0\nimage_field: photo\nteaser_field:\nshow_details: 1\nteaser_len:\nlimit: 5\n', 'list_featured', 'wrapper', NULL),
(17, NULL, NULL, 11, 'Content slider', NULL, NULL, NULL, NULL, NULL, NULL, '---\n- 0\n', NULL, '---\nctype_id: 10\ncategory_id: 1\ndataset: 0\nimage_field: photo\nbig_image_field:\nbig_image_preset: big\nteaser_field: teaser\ndelay: 5\nlimit: 5\n', NULL, 'wrapper', NULL),
(21, NULL, NULL, 13, 'Search', NULL, NULL, NULL, NULL, NULL, NULL, '---\n- 0\n', NULL, '', NULL, 'wrapper', NULL);

INSERT INTO `{#}widgets_bind_pages` (`id`, `bind_id`, `template`, `is_enabled`, `page_id`, `position`, `ordering`) VALUES
(7, 6, 'default', 1, 1, 'right-bottom', 0),
(8, 8, 'default', 1, 1, 'right-bottom', 2),
(9, 9, 'default', 1, 1, 'left-bottom', 3),
(10, 10, 'default', 1, 1, 'left-bottom', 1),
(11, 11, 'default', 1, 1, 'left-bottom', 4),
(12, 12, 'default', 1, 147, 'right-bottom', 3),
(13, 13, 'default', 1, 1, 'left-bottom', 2),
(14, 14, 'default', 1, 1, 'right-bottom', 1),
(15, 15, 'default', 1, 0, 'footer', 0),
(16, 16, 'default', 1, 1, 'left-bottom', 0),
(17, 17, 'default', 1, 1, 'left-top', 1),
(19, 21, 'default', 1, 1, 'right-top', 0);