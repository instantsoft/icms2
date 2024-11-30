START TRANSACTION;

INSERT INTO `{#}activity` (`type_id`, `user_id`, `group_id`, `subject_title`, `subject_id`, `subject_url`, `reply_url`, `images`, `images_count`, `date_pub`, `is_private`, `is_parent_hidden`) VALUES
(1, 1, NULL, 'О проекте', 1, 'pages/about.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 11 DAY), 0, NULL),
(1, 1, NULL, 'Правила сайта', 2, 'pages/rules.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 10 DAY), 0, NULL),
(12, 1, NULL, 'We are all made of stars © Moby', NULL, NULL, 'users/1?wid=1&reply=1', NULL, NULL, CURRENT_TIMESTAMP, 0, NULL);

INSERT INTO `{#}activity_types` (`is_enabled`, `controller`, `name`, `title`, `description`) VALUES
(1, 'content', 'add.articles', 'Добавление статей', 'добавляет статью %s');
SET @activity_type_1 = LAST_INSERT_ID();

INSERT INTO `{#}activity` (`type_id`, `user_id`, `group_id`, `subject_title`, `subject_id`, `subject_url`, `reply_url`, `images`, `images_count`, `date_pub`, `is_private`, `is_parent_hidden`) VALUES
(@activity_type_1, 1, NULL, 'Эллиптический перигей в XXI веке', 1, 'articles/1-ellipticheskii-perigei-v-xxi-veke.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 13 DAY), 0, NULL),
(@activity_type_1, 1, NULL, 'Недонасыщенный алмаз: предпосылки и развитие', 4, 'articles/4-nedonasyschennyi-almaz-predposylki-i-razvitie.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 12 DAY), 0, NULL),
(@activity_type_1, 1, NULL, 'Мифологический реципиент', 10, 'articles/10-mifologicheskii-recipient.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 1 DAY), 0, NULL),
(@activity_type_1, 1, NULL, 'Общественный анализ зарубежного опыта', 11, 'articles/11-obschestvennyi-analiz-zarubezhnogo-opyta.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 7 MINUTE), 0, NULL);

INSERT INTO `{#}activity_types` (`is_enabled`, `controller`, `name`, `title`, `description`) VALUES
(1, 'content', 'add.posts', 'Добавление постов', 'добавляет пост %s');

INSERT INTO `{#}activity` (`type_id`, `user_id`, `group_id`, `subject_title`, `subject_id`, `subject_url`, `reply_url`, `images`, `images_count`, `date_pub`, `is_private`, `is_parent_hidden`) VALUES
((SELECT LAST_INSERT_ID()), 1, @insert_group_id, 'Мой первый пост в сообществе', 5, 'posts/5-moi-pervyi-post-v-soobschestve.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 6 MINUTE), 0, NULL);

INSERT INTO `{#}activity_types` (`is_enabled`, `controller`, `name`, `title`, `description`) VALUES
(1, 'content', 'add.board', 'Добавление объявлений', 'добавляет объявление %s');

INSERT INTO `{#}activity` (`type_id`, `user_id`, `group_id`, `subject_title`, `subject_id`, `subject_url`, `reply_url`, `images`, `images_count`, `date_pub`, `is_private`, `is_parent_hidden`) VALUES
((SELECT LAST_INSERT_ID()), 1, NULL, 'Продам квартиру в новостройке', 7, 'board/7-prodam-kvartiru-v-novostroike.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 5 MINUTE), 0, NULL);

INSERT INTO `{#}activity_types` (`is_enabled`, `controller`, `name`, `title`, `description`) VALUES
(1, 'content', 'add.news', 'Добавление новостей', 'добавляет новость %s');
SET @activity_type_2 = LAST_INSERT_ID();

INSERT INTO `{#}activity` (`type_id`, `user_id`, `group_id`, `subject_title`, `subject_id`, `subject_url`, `reply_url`, `images`, `images_count`, `date_pub`, `is_private`, `is_parent_hidden`) VALUES
(@activity_type_2, 1, NULL, 'На улице 22 Партсъезда прорвало трубы с водой', 1, 'news/1-na-ulice-prorvalo-truby.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 9 DAY), 0, NULL),
(@activity_type_2, 1, NULL, 'Игрушки становятся дороже', 2, 'news/2-igrushki-stanovjatsja-dorozhe.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 8 DAY), 0, NULL),
(@activity_type_2, 1, NULL, 'В городе открыт сервис для ретро-автомобилей', 3, 'news/3-v-gorode-otkryt-servis-dlja-retro-avtomobilei.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 7 DAY), 0, NULL),
(@activity_type_2, 1, NULL, 'Дачный сезон на Урале официально начался', 4, 'news/4-dachnyi-sezon-otkryt.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 6 DAY), 0, NULL),
(@activity_type_2, 1, NULL, 'Бизнес ожидает снижения налогов', 5, 'news/5-snizhenie-nalogov-dlja-biznesa.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 5 DAY), 0, NULL),
(@activity_type_2, 1, NULL, 'Все больше россиян покупают дома за границей', 6, 'news/6-vse-bolshe-rossijan-pokupayut-nedvizhimost-za-granicei.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 4 DAY), 0, NULL),
(@activity_type_2, 1, NULL, 'Количество преступлений в России сокращается', 7, 'news/7-kolichestvo-prestuplenii-v-rossii-sokraschaetsja.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 3 DAY), 0, NULL),
(@activity_type_2, 1, NULL, 'Россияне стали первыми на Чемпионате Мира', 9, 'news/9-rossijane-stali-pervymi-na-chempionate-mira.html', NULL, NULL, NULL, DATE_SUB(NOW(),INTERVAL 2 DAY), 0, NULL);

COMMIT;