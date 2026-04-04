INSERT INTO `{#}con_albums` (`id`, `title`, `content`, `slug`, `seo_keys`, `seo_desc`, `seo_title`, `tags`, `date_pub`, `date_last_modified`, `date_pub_end`, `is_pub`, `hits_count`, `user_id`, `parent_id`, `parent_type`, `parent_title`, `parent_url`, `is_parent_hidden`, `category_id`, `folder_id`, `is_comments_on`, `comments`, `rating`, `is_approved`, `approved_by`, `date_approved`, `is_private`, `cover_image`, `photos_count`, `is_public`) VALUES
(16, 'The beauty of the surrounding nature', 'Photos from the deviantart.com', '16-the-beauty-of-the-surrounding-nature', 'photos, deviantart.com', 'Photos from the deviantart.com', NULL, 'example, photo', '2013-11-13 16:48:18', '2013-11-22 16:32:38', NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, 0, 0, 1, NULL, '2013-11-22 16:32:38', 0, '---\nbig: u1/004/4f11cd73.jpg\nnormal: u1/004/5b0ff517.jpg\nsmall: u1/004/5edb4681.jpg', 0, NULL),
(14, 'Other photos', 'Photos taken of me at leisure', '14-other-photos', 'photos', 'leisure', NULL, '0', '2013-10-09 16:46:43', '2013-10-09 16:46:43', NULL, 1, 0, 6, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, 0, 0, 0, NULL, NULL, 0, NULL, 0, NULL);

INSERT INTO `{#}con_albums_cats_bind` (`item_id`, `category_id`) VALUES
(16, 1),
(14, 1);

INSERT INTO `{#}menu_items` (`menu_id`, `parent_id`, `title`, `url`, `ordering`, `options`, `groups_view`, `groups_hide`) VALUES
(1, 0, 'Photos', 'albums', 4, '---\ntarget: _self\nclass:', '---\n- 0\n', NULL);