INSERT INTO `{#}con_albums` (`id`, `title`, `content`, `slug`, `seo_keys`, `seo_desc`, `seo_title`, `tags`, `date_pub`, `date_last_modified`, `date_pub_end`, `is_pub`, `hits_count`, `user_id`, `parent_id`, `parent_type`, `parent_title`, `parent_url`, `is_parent_hidden`, `category_id`, `folder_id`, `is_comments_on`, `comments`, `rating`, `is_approved`, `approved_by`, `date_approved`, `is_private`, `cover_image`, `photos_count`, `is_public`) VALUES
(16, 'Красота окружающей природы', 'Фотографии из коллекции сайта deviantart.com', '16-krasota-okruzhayuschei-prirody', NULL, NULL, NULL, 'пример, фото', DATE_SUB(NOW(),INTERVAL 4 DAY), DATE_SUB(NOW(),INTERVAL 3 DAY), NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, 0, 0, 1, NULL, NULL, 0, NULL, 0, NULL);

INSERT INTO `{#}con_albums_cats_bind` (`item_id`, `category_id`) VALUES
(16, 1);

INSERT INTO `{#}menu_items` (`menu_id`, `parent_id`, `title`, `url`, `ordering`, `options`, `groups_view`, `groups_hide`) VALUES
(1, 0, 'Фото', 'albums', 4, '---\ntarget: _self\nclass:', '---\n- 0\n', NULL);