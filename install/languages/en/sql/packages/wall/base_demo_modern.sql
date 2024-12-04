INSERT INTO `{#}wall_entries` (`date_pub`, `controller`, `profile_type`, `profile_id`, `user_id`, `parent_id`, `status_id`, `content`, `content_html`) VALUES
(CURRENT_TIMESTAMP, 'users', 'user', 1, 1, 0, 1, 'We are all made of stars © Moby', 'We are all made of stars © Moby');

UPDATE `{#}users_statuses` SET `wall_entry_id` = (SELECT LAST_INSERT_ID()) WHERE `id` = 1;

INSERT INTO `{#}wall_entries` (`date_pub`, `controller`, `profile_type`, `profile_id`, `user_id`, `parent_id`, `status_id`, `content`, `content_html`) VALUES
(CURRENT_TIMESTAMP, 'users', 'user', 1, 1, 1, NULL, 'Thank you for viewing my profile page!', 'Thank you for viewing my profile page!');