START TRANSACTION;

INSERT INTO `{#}activity` (`type_id`, `user_id`, `group_id`, `subject_title`, `subject_id`, `subject_url`, `reply_url`, `images`, `images_count`, `date_pub`, `is_private`, `is_parent_hidden`) VALUES
(12, 1, NULL, 'We are all made of stars © Moby', NULL, NULL, 'users/1?wid=1&reply=1', NULL, NULL, CURRENT_TIMESTAMP, 0, NULL),
(1, 1, NULL, 'About', 1, 'pages/about.html', NULL, NULL, NULL, '2013-08-08 15:07:27', 0, NULL),
(1, 1, NULL, 'Site Rules', 2, 'pages/rules.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 10 DAY), 0, NULL);

INSERT INTO `{#}activity_types` (`is_enabled`, `controller`, `name`, `title`, `description`) VALUES
(1, 'content', 'add.articles', 'Adding articles', 'added article %s');
SET @activity_type_1 = LAST_INSERT_ID();

INSERT INTO `{#}activity` (`type_id`, `user_id`, `group_id`, `subject_title`, `subject_id`, `subject_url`, `reply_url`, `images`, `images_count`, `date_pub`, `is_private`, `is_parent_hidden`) VALUES
(@activity_type_1, 1, NULL, 'Elliptical perigee in the XXI century', 1, 'articles/1-elliptical-perigee-in-the-xxi-century.html', NULL, NULL, NULL, '2013-07-24 10:49:30', 0, NULL),
(@activity_type_1, 1, NULL, 'Undersaturated diamond: preconditions and development', 4, 'articles/4-undersaturated-diamond-preconditions-and-development.html', NULL, NULL, NULL, '2013-07-24 11:22:39', 0, NULL),
(@activity_type_1, 1, NULL, 'Mythological recipient', 10, 'articles/10-mythological-recipient.html', NULL, NULL, NULL, '2013-10-09 14:48:19', 0, NULL),
(@activity_type_1, 1, NULL, 'Public review of international experience', 11, 'articles/11-public-review-of-international-experience.html', NULL, NULL, NULL, '2013-10-09 14:54:36', 0, NULL);

INSERT INTO `{#}activity_types` (`is_enabled`, `controller`, `name`, `title`, `description`) VALUES
(1, 'content', 'add.posts', 'Adding posts', 'added post %s');

INSERT INTO `{#}activity` (`type_id`, `user_id`, `group_id`, `subject_title`, `subject_id`, `subject_url`, `reply_url`, `images`, `images_count`, `date_pub`, `is_private`, `is_parent_hidden`) VALUES
((SELECT LAST_INSERT_ID()), 1, 1, 'My first post in the Community', 5, 'posts/5-moi-pervyi-post-v-soobschestve.html', NULL, NULL, NULL, '2013-11-13 16:43:07', 0, NULL);

INSERT INTO `{#}activity_types` (`is_enabled`, `controller`, `name`, `title`, `description`) VALUES
(1, 'content', 'add.news', 'Adding news', 'added news %s');
SET @activity_type_2 = LAST_INSERT_ID();

INSERT INTO `{#}activity` (`type_id`, `user_id`, `group_id`, `subject_title`, `subject_id`, `subject_url`, `reply_url`, `images`, `images_count`, `date_pub`, `is_private`, `is_parent_hidden`) VALUES
(@activity_type_2, 1, NULL, 'Toys are becoming more expensive', 2, 'news/2-igrushki-stanovjatsja-dorozhe.html', NULL, NULL, NULL, '2013-09-09 16:02:07', 0, NULL),
(@activity_type_2, 1, NULL, 'Сar service for vintage cars', 3, 'news/3-v-gorode-otkryt-servis-dlja-retro-avtomobilei.html', NULL, NULL, NULL, '2013-09-09 16:03:25', 0, NULL),
(@activity_type_2, 1, NULL, 'Summer season has officially begun', 4, 'news/4-dachnyi-sezon-otkryt.html', NULL, NULL, NULL, '2013-09-09 16:04:25', 0, NULL),
(@activity_type_2, 1, NULL, 'Business expects to reduce taxes', 5, 'news/5-snizhenie-nalogov-dlja-biznesa.html', NULL, NULL, NULL, '2013-09-09 16:05:26', 0, NULL),
(@activity_type_2, 1, NULL, 'More and more people are buying homes abroad', 6, 'news/6-vse-bolshe-rossijan-pokupayut-nedvizhimost-za-granicei.html', NULL, NULL, NULL, '2013-09-12 12:09:25', 0, NULL),
(@activity_type_2, 1, NULL, 'Reduced the number of crimes', 7, 'news/7-kolichestvo-prestuplenii-v-rossii-sokraschaetsja.html', NULL, NULL, NULL, '2013-09-12 12:10:55', 0, NULL),
(@activity_type_2, 1, NULL, 'We have won the World Championship!', 9, 'news/9-rossijane-stali-pervymi-na-chempionate-mira.html', NULL, NULL, NULL, '2013-09-12 12:14:13', 0, NULL);

COMMIT;