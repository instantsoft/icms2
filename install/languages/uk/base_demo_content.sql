INSERT INTO `{#}content_datasets` (`id`, `ctype_id`, `name`, `title`, `ordering`, `is_visible`, `filters`, `sorting`, `index`, `groups_view`, `groups_hide`)
VALUES (10, 10, 'latest', 'Последние', 1, 1, NULL, '---\n- \n  by: date_pub\n  to: desc\n', 'date_pub', '---\n- 0\n', NULL);

INSERT INTO `{#}content_types` (`id`, `title`, `name`, `description`, `is_date_range`, `is_cats`, `is_cats_recursive`, `is_folders`, `is_in_groups`, `is_in_groups_only`, `is_comments`,
                                `is_comments_tree`, `is_rating`, `is_rating_pos`, `is_tags`, `is_auto_keys`, `is_auto_desc`, `is_auto_url`, `is_fixed_url`, `url_pattern`, `options`, `labels`,
                                `seo_keys`, `seo_desc`, `seo_title`, `item_append_html`, `is_fixed`)
VALUES (10, 'Новости', 'news', '<p>Информационные сообщения</p>', NULL, 1, 1, NULL, NULL, NULL, 1, NULL, 1, NULL, 1, 1, 1, 1, 1, '{id}-{title}',
        '---\nis_cats_change: 1\nis_cats_open_root: null\nis_cats_only_last: null\nis_show_cats: null\nis_tags_in_list: null\nis_tags_in_item: 1\nis_rss: 1\nlist_on: 1\nprofile_on: 1\nlist_show_filter: null\nlist_expand_filter: null\nlist_style: featured\nitem_on: 1\nis_cats_keys: null\nis_cats_desc: null\nis_cats_auto_url: 1\n',
        '---\none: новость\ntwo: новости\nmany: новостей\ncreate: новость\nlist:\nprofile:\n', NULL, NULL, NULL, NULL, NULL);

DROP TABLE IF EXISTS `{#}con_news`;
CREATE TABLE `{#}con_news`
(
  `id`                 int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title`              varchar(100)              DEFAULT NULL,
  `content`            text,
  `slug`               varchar(100)              DEFAULT NULL,
  `seo_keys`           varchar(256)              DEFAULT NULL,
  `seo_desc`           varchar(256)              DEFAULT NULL,
  `seo_title`          varchar(256)              DEFAULT NULL,
  `tags`               varchar(1000)             DEFAULT NULL,
  `template`           varchar(150)              DEFAULT NULL,
  `date_pub`           timestamp        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_last_modified` timestamp        NULL     DEFAULT NULL,
  `date_pub_end`       timestamp        NULL     DEFAULT NULL,
  `is_pub`             tinyint(1)                DEFAULT '1',
  `hits_count`         int(11)                   DEFAULT '0',
  `user_id`            int(11) unsigned          DEFAULT NULL,
  `parent_id`          int(11) unsigned          DEFAULT NULL,
  `parent_type`        varchar(32)               DEFAULT NULL,
  `parent_title`       varchar(100)              DEFAULT NULL,
  `parent_url`         varchar(255)              DEFAULT NULL,
  `is_parent_hidden`   tinyint(1)                DEFAULT NULL,
  `category_id`        int(11) unsigned NOT NULL DEFAULT '1',
  `folder_id`          int(11) unsigned          DEFAULT NULL,
  `is_comments_on`     tinyint(1) unsigned       DEFAULT '1',
  `comments`           int(11)          NOT NULL DEFAULT '0',
  `rating`             int(11)          NOT NULL DEFAULT '0',
  `is_deleted`         tinyint(1) unsigned       DEFAULT NULL,
  `is_approved`        tinyint(1)       NOT NULL DEFAULT '1',
  `approved_by`        int(11)                   DEFAULT NULL,
  `date_approved`      timestamp        NULL     DEFAULT NULL,
  `is_private`         tinyint(1)       NOT NULL DEFAULT '0',
  `teaser`             varchar(255)              DEFAULT NULL,
  `photo`              text,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `approved_by` (`approved_by`),
  KEY `folder_id` (`folder_id`),
  KEY `slug` (`slug`),
  KEY `date_pub` (`is_pub`, `is_parent_hidden`, `is_deleted`, `is_approved`, `date_pub`),
  KEY `parent_id` (`parent_id`, `parent_type`, `date_pub`),
  KEY `user_id` (`user_id`, `date_pub`),
  KEY `date_pub_end` (`date_pub_end`),
  KEY `dataset_discussed` (`is_pub`, `is_parent_hidden`, `is_deleted`, `is_approved`, `comments`),
  KEY `dataset_popular` (`is_pub`, `is_parent_hidden`, `is_deleted`, `is_approved`, `rating`),
  FULLTEXT KEY `title` (`title`)
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

INSERT INTO `{#}con_news` (`id`, `title`, `content`, `slug`, `seo_keys`, `seo_desc`, `seo_title`, `tags`, `date_pub`, `date_last_modified`, `date_pub_end`, `is_pub`, `hits_count`, `user_id`,
                           `parent_id`, `parent_type`, `parent_title`, `parent_url`, `is_parent_hidden`, `category_id`, `folder_id`, `is_comments_on`, `comments`, `rating`, `is_approved`,
                           `approved_by`, `date_approved`, `is_private`, `teaser`, `photo`)
VALUES (1, 'На улице 22 Партсъезда прорвало трубы с водой',
        '<p>\r\n	Если основание движется с постоянным ускорением, проекция на подвижные оси трудна в описании. Маховик мал. Погрешность преобразует угол крена, механически интерпретируя полученные выражения. Как уже указывалось, кожух безусловно не входит своими составляющими, что очевидно, в силы нормальных реакций связей, так же как и момент силы трения, что обусловлено малыми углами карданового подвеса. Абсолютно твёрдое тело переворачивает гирогоризонт, перейдя к исследованию устойчивости линейных гироскопических систем с искусственными силами. Тангаж определяет астатический объект, что видно из уравнения кинетической энергии ротора.\r\n</p>\r\n<p>\r\n	Механическая природа, в силу третьего закона Ньютона, опасна. Векторная форма, как можно показать с помощью не совсем тривиальных вычислений, заставляет иначе взглянуть на то, что такое гирокомпас, что нельзя рассматривать без изменения системы координат. Объект учитывает угол крена, что обусловлено существованием циклического интеграла у второго уравнения системы уравнений малых колебаний. Успокоитель качки, в соответствии с модифицированным уравнением Эйлера, участвует в погрешности определения курса меньше, чем поплавковый период, основываясь на предыдущих вычислениях.\r\n</p>',
        '1-na-ulice-prorvalo-truby', 'крена, уравнения, обусловлено, объект, системы, проекция, твёрдое, переворачивает, гирогоризонт, абсолютно',
        'Если основание движется с постоянным ускорением, проекция на подвижные оси трудна в описании. Маховик мал. Погрешность преобразует угол крена, механически интерпретируя полученные выражения',
        NULL, 'новости, проишествия', DATE_SUB(NOW(), INTERVAL 9 DAY), DATE_SUB(NOW(), INTERVAL 9 DAY), NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 5, NULL, 1, 0, 0, 1, NULL,
        DATE_SUB(NOW(), INTERVAL 9 DAY), 0, 'Радостные дети бегают по лужам', NULL),
       (2, 'Игрушки становятся дороже',
        'Будем, как и раньше, предполагать, что волчок устойчив. Если основание движется с постоянным ускорением, ПИГ не входит своими составляющими, что очевидно, в силы нормальных реакций связей, так же как и прецизионный гироскопический стабилизатоор, изменяя направление движения. Уравнение возмущенного движения, согласно уравнениям Лагранжа, принципиально связывает устойчивый систематический уход, что неправильно при большой интенсивности диссипативных сил. Направление вращает математический маятник, рассматривая уравнения движения тела в проекции на касательную к его траектории. Устойчивость, как следует из системы уравнений, интегрирует гравитационный суммарный поворот, что при любом переменном вращении в горизонтальной плоскости будет направлено вдоль оси. Гировертикаль косвенно требует большего внимания к анализу ошибок, которые даёт курс, составляя уравнения Эйлера для этой системы координат.',
        '2-igrushki-stanovjatsja-dorozhe', 'движения, системы, уравнения, направление, согласно, уравнение, возмущенного, изменяя, уравнениям, интенсивности',
        'Будем, как и раньше, предполагать, что волчок устойчив', NULL, 'новости', DATE_SUB(NOW(), INTERVAL 8 DAY), DATE_SUB(NOW(), INTERVAL 8 DAY), NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 6,
        NULL, 1, 0, 0, 1, NULL, NULL, 0, 'Эксперты прогнозируют дальнейший рост цен на детские товары',
        '---\noriginal: u1/003/25838c0f.jpg\nbig: u1/003/2e2bf124.jpg\nnormal: u1/003/f6f14e82.jpg\nsmall: u1/003/236d41e4.jpg\nmicro: u1/003/74809cbe.jpg\n'),
       (3, 'В городе открыт сервис для ретро-автомобилей',
        'Силовой трёхосный гироскопический стабилизатор, в силу третьего закона Ньютона, неустойчив. Установившийся режим требует перейти к поступательно перемещающейся системе координат, чем и характеризуется дифференциальный угол тангажа, составляя уравнения Эйлера для этой системы координат. Максимальное отклонение мгновенно. Отсюда следует, что ось собственного вращения даёт большую проекцию на оси, чем подвес, учитывая смещения центра масс системы по оси ротора.',
        '3-v-gorode-otkryt-servis-dlja-retro-avtomobilei', 'координат, системы, перейти, поступательно, перемещающейся, дифференциальный, характеризуется, системе, требует, режим',
        'Силовой трёхосный гироскопический стабилизатор, в силу третьего закона Ньютона, неустойчив', NULL, 'новости, пример', DATE_SUB(NOW(), INTERVAL 7 DAY), DATE_SUB(NOW(), INTERVAL 7 DAY), NULL,
        1, 0, 1, NULL, NULL, NULL, NULL, NULL, 3, NULL, 1, 0, 0, 1, NULL, NULL, 0, 'Каждый желающий может обратиться с просьбой о ремонте',
        '---\noriginal: u1/003/5a771d4e.jpg\nbig: u1/003/4878547b.jpg\nnormal: u1/003/ad753a86.jpg\nsmall: u1/003/9f03ca75.jpg\nmicro: u1/003/5edc315b.jpg\n'),
       (4, 'Дачный сезон на Урале официально начался',
        'BTL, не меняя концепции, изложенной выше, консолидирует стиль менеджмента, используя опыт предыдущих кампаний. VIP-мероприятие, как следует из вышесказанного, консолидирует SWOT-анализ, используя опыт предыдущих кампаний. Медиа, отбрасывая подробности, изящно раскручивает анализ рыночных цен, невзирая на действия конкурентов. Стоит отметить, что promotion-кампания вырождена. Показ баннера, анализируя результаты рекламной кампании, концентрирует обществвенный анализ зарубежного опыта, используя опыт предыдущих кампаний. А вот по мнению аналитиков партисипативное планирование экономит эмпирический рекламоноситель, работая над проектом.',
        '4-dachnyi-sezon-otkryt', 'используя, кампаний, предыдущих, консолидирует, анализ, раскручивает, изящно, подробности, действия, отбрасывая',
        'BTL, не меняя концепции, изложенной выше, консолидирует стиль менеджмента, используя опыт предыдущих кампаний. VIP-мероприятие, как следует из вышесказанного, консолидирует SWOT-анализ, используя опыт предыдущих кампаний',
        NULL, 'пример', DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_SUB(NOW(), INTERVAL 6 DAY), NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 2, NULL, 1, 0, 0, 1, NULL, DATE_SUB(NOW(), INTERVAL 6 DAY), 0,
        'Горожане массово переезжают за город', '---\noriginal: u1/003/01153b4d.jpg\nbig: u1/003/b9767257.jpg\nnormal: u1/003/53497165.jpg\nsmall: u1/003/b1e550ce.jpg\nmicro: u1/003/f1476363.jpg\n'),
       (5, 'Бизнес ожидает снижения налогов',
        'Продвижение проекта, пренебрегая деталями, поразительно. Стратегический рыночный план решительно нейтрализует инструмент маркетинга, полагаясь на инсайдерскую информацию. Можно предположить, что VIP-мероприятие настроено позитивно. Баннерная реклама, в рамках сегодняшних воззрений, охватывает сублимированный BTL, отвоевывая рыночный сегмент.\r\n\r\nУзнавание бренда, как следует из вышесказанного, слабо притягивает ролевой медиавес, оптимизируя бюджеты. Продукт, анализируя результаты рекламной кампании, концентрирует культурный продуктовый ассортимент, повышая конкуренцию. Позиционирование на рынке конструктивно. Личность топ менеджера, безусловно, создает жизненный цикл продукции, учитывая современные тенденции.',
        '5-snizhenie-nalogov-dlja-biznesa', 'рыночный, рамках, vip-мероприятие, реклама, баннерная, настроено, позитивно, сегодняшних, воззрений, бренда',
        'Продвижение проекта, пренебрегая деталями, поразительно. Стратегический рыночный план решительно нейтрализует инструмент маркетинга, полагаясь на инсайдерскую информацию. Можно предположить, что VIP-мероприятие настроено позитивно',
        NULL, '0', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY), NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 3, NULL, 1, 0, 0, 1, NULL, DATE_SUB(NOW(), INTERVAL 5 DAY), 0,
        'Юридические лица будут платить еще меньше',
        '---\noriginal: u1/003/0ff19ffb.jpg\nbig: u1/003/3c2e4a35.jpg\nnormal: u1/003/fa562059.jpg\nsmall: u1/003/cae0bdfb.jpg\nmicro: u1/003/852fb216.jpg\n'),
       (6, 'Все больше россиян покупают дома за границей',
        'Наш современник стал особенно чутко относиться к слову, однако дольник жизненно приводит мелодический зачин, туда же попадает и еще недавно вызывавший безусловную симпатию гетевский Вертер. В заключении добавлю, полисемия отталкивает парафраз – это уже пятая стадия понимания по М.Бахтину. Однако Л.В.Щерба утверждал, что расположение эпизодов существенно отражает сюжетный абстракционизм, но не рифмами. Женское окончание начинает конструктивный скрытый смысл, об этом свидетельствуют краткость и завершенность формы, бессюжетность, своеобразие тематического развертывания. Расположение эпизодов начинает подтекст, что нельзя сказать о нередко манерных эпитетах. Если выстроить в ряд случаи инверсий у Державина, то расположение эпизодов диссонирует словесный речевой акт, но языковая игра не приводит к активно-диалогическому пониманию.',
        '6-vse-bolshe-rossijan-pokupayut-nedvizhimost-za-granicei', 'эпизодов, расположение, приводит, однако, начинает, полисемия, парафраз, добавлю, отталкивает, пятая',
        'Наш современник стал особенно чутко относиться к слову, однако дольник жизненно приводит мелодический зачин, туда же попадает и еще недавно вызывавший безусловную симпатию гетевский Вертер',
        NULL, 'пример, новости', DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY), NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 2, NULL, 1, 0, 0, 1, NULL,
        DATE_SUB(NOW(), INTERVAL 4 DAY), 0, 'За последний год их количество заметно выросло',
        '---\noriginal: u1/003/2fea4487.jpg\nbig: u1/003/a05ad20e.jpg\nnormal: u1/003/41646570.jpg\nsmall: u1/003/eb2bac70.jpg\nmicro: u1/003/1c88035a.jpg\n'),
       (7, 'Количество преступлений в России сокращается',
        'Ю.Лотман, не дав ответа, тут же запутывается в проблеме превращения не-текста в текст, поэтому нет смысла утверждать, что первое полустишие начинает механизм сочленений, так как в данном случае роль наблюдателя опосредована ролью рассказчика. Брахикаталектический стих приводит палимпсест, первым образцом которого принято считать книгу А.Бертрана "Гаспар из тьмы". Наш современник стал особенно чутко относиться к слову, однако впечатление существенно дает ямб, так как в данном случае роль наблюдателя опосредована ролью рассказчика. Цитата как бы придвигает к нам прошлое, при этом звукопись дает мелодический дактиль, также необходимо сказать о сочетании метода апроприации художественных стилей прошлого с авангардистскими стратегиями. Слово кумулятивно. Контрапункт, несмотря на внешние воздействия, существенно иллюстрирует диалогический не-текст, туда же попадает и еще недавно вызывавший безусловную симпатию гетевский Вертер.',
        '7-kolichestvo-prestuplenii-v-rossii-sokraschaetsja', 'данном, случае, наблюдателя, опосредована, существенно, рассказчика, ролью, поэтому, принято, которого',
        'Ю.Лотман, не дав ответа, тут же запутывается в проблеме превращения не-текста в текст, поэтому нет смысла утверждать, что первое полустишие начинает механизм сочленений, так как в данном случае роль наблюдателя опосредована ролью рассказчика',
        NULL, '0', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY), NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 5, NULL, 1, 0, 0, 1, NULL, DATE_SUB(NOW(), INTERVAL 3 DAY), 0,
        'В последних отчетах МВД видна положительная тенденция',
        '---\noriginal: u1/003/4d953a88.jpg\nbig: u1/003/e3c52c3e.jpg\nnormal: u1/003/9e9ef526.jpg\nsmall: u1/003/3f768733.jpg\nmicro: u1/003/ddaa0bd4.jpg\n'),
       (8, 'Инвестиции для чайников: куда вкладывать?',
        'Из приведенных текстуальных фрагментов видно, как матрица абсурдно просветляет диалогический контекст, где автор является полновластным хозяином своих персонажей, а они - его марионетками. Эстетическое воздействие, на первый взгляд, осознаёт сюжетный генезис свободного стиха, туда же попадает и еще недавно вызывавший безусловную симпатию гетевский Вертер. Басня, как бы это ни казалось парадоксальным, доступна. Стих текстологически отталкивает поэтический амфибрахий, однако дальнейшее развитие приемов декодирования мы находим в работах академика В.Виноградова. Зачин редуцирует конструктивный анапест, об этом свидетельствуют краткость и завершенность формы, бессюжетность, своеобразие тематического развертывания.',
        '8-investicii-dlja-chainikov-kuda-vkladyvat', 'приведенных, сюжетный, осознаёт, взгляд, воздействие, первый, генезис, свободного, безусловную, симпатию',
        'Из приведенных текстуальных фрагментов видно, как матрица абсурдно просветляет диалогический контекст, где автор является полновластным хозяином своих персонажей, а они - его марионетками',
        NULL, '0', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY), NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 3, NULL, 1, 0, 0, 1, NULL, NULL, 0,
        'Читайте в нашем обзоре самых популярных способов инвестиций',
        '---\noriginal: u1/003/ff539643.jpg\nbig: u1/003/77fbbb95.jpg\nnormal: u1/003/89e8e681.jpg\nsmall: u1/003/3400aa78.jpg\nmicro: u1/003/f95ca1a2.jpg\n'),
       (9, 'Россияне стали первыми на Чемпионате Мира',
        'Ударение, соприкоснувшись в чем-то со своим главным антагонистом в постструктурной поэтике, диссонирует коммунальный модернизм, туда же попадает и еще недавно вызывавший безусловную симпатию гетевский Вертер. Скрытый смысл вызывает глубокий контрапункт, но не рифмами. Олицетворение, если уловить хореический ритм или аллитерацию на "р", аннигилирует симулякр, при этом нельзя говорить, что это явления собственно фоники, звукописи. Матрица параллельна.',
        '9-rossijane-stali-pervymi-na-chempionate-mira', 'ударение, недавно, попадает, вызывавший, безусловную, вертер, гетевский, симпатию, модернизм, коммунальный',
        'Ударение, соприкоснувшись в чем-то со своим главным антагонистом в постструктурной поэтике, диссонирует коммунальный модернизм, туда же попадает и еще недавно вызывавший безусловную симпатию гетевский Вертер',
        NULL, '0', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY), NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 7, NULL, 1, 0, 0, 1, NULL, NULL, 0,
        'Наша команда не оставила шансов конкурентам',
        '---\noriginal: u1/003/59b08272.jpg\nbig: u1/003/d0ed7732.jpg\nnormal: u1/003/44b68dc8.jpg\nsmall: u1/003/93e51e49.jpg\nmicro: u1/003/0599295b.jpg\n');

DROP TABLE IF EXISTS `{#}con_news_cats`;
CREATE TABLE `{#}con_news_cats`
(
  `id`          int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id`   int(11) unsigned          DEFAULT NULL,
  `title`       varchar(200)              DEFAULT NULL,
  `description` text             NULL     DEFAULT NULL,
  `slug`        varchar(255)              DEFAULT NULL,
  `slug_key`    varchar(255)              DEFAULT NULL,
  `seo_keys`    varchar(256)              DEFAULT NULL,
  `seo_desc`    varchar(256)              DEFAULT NULL,
  `seo_title`   varchar(256)              DEFAULT NULL,
  `ordering`    int(11)                   DEFAULT NULL,
  `ns_left`     int(11)                   DEFAULT NULL,
  `ns_right`    int(11)                   DEFAULT NULL,
  `ns_level`    int(11)                   DEFAULT NULL,
  `ns_differ`   varchar(32)      NOT NULL DEFAULT '',
  `ns_ignore`   tinyint(4)       NOT NULL DEFAULT '0',
  `allow_add`   text,
  PRIMARY KEY (`id`),
  KEY `ordering` (`ordering`),
  KEY `slug` (`slug`),
  KEY `ns_left` (`ns_level`, `ns_right`, `ns_left`),
  KEY `parent_id` (`parent_id`, `ns_left`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

INSERT INTO `{#}con_news_cats` (`id`, `parent_id`, `title`, `slug`, `slug_key`, `seo_keys`, `seo_desc`, `seo_title`, `ordering`, `ns_left`, `ns_right`, `ns_level`, `ns_differ`, `ns_ignore`)
VALUES (1, 0, '---', NULL, NULL, NULL, NULL, NULL, 1, 1, 14, 0, '', 0),
       (2, 1, 'Общество', 'obschestvo', NULL, NULL, NULL, NULL, 1, 2, 3, 1, '', 0),
       (3, 1, 'Бизнес', 'biznes', NULL, NULL, NULL, NULL, 2, 4, 5, 1, '', 0),
       (4, 1, 'Политика', 'politika', NULL, NULL, NULL, NULL, 3, 6, 7, 1, '', 0),
       (5, 1, 'Происшествия', 'proisshestvija', NULL, NULL, NULL, NULL, 4, 8, 9, 1, '', 0),
       (6, 1, 'В мире', 'v-mire', NULL, NULL, NULL, NULL, 5, 10, 11, 1, '', 0),
       (7, 1, 'Спорт', 'sport', NULL, NULL, NULL, NULL, 6, 12, 13, 1, '', 0);

DROP TABLE IF EXISTS `{#}con_news_cats_bind`;
CREATE TABLE `{#}con_news_cats_bind`
(
  `item_id`     int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  KEY `item_id` (`item_id`),
  KEY `category_id` (`category_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

INSERT INTO `{#}con_news_cats_bind` (`item_id`, `category_id`)
VALUES (1, 5),
       (2, 6),
       (3, 3),
       (4, 2),
       (5, 3),
       (6, 2),
       (7, 5),
       (8, 3),
       (9, 7);

DROP TABLE IF EXISTS `{#}con_news_fields`;
CREATE TABLE `{#}con_news_fields`
(
  `id`            int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ctype_id`      int(11)      DEFAULT NULL,
  `name`          varchar(40)  DEFAULT NULL,
  `title`         varchar(100) DEFAULT NULL,
  `hint`          varchar(200) DEFAULT NULL,
  `ordering`      int(11)      DEFAULT NULL,
  `fieldset`      varchar(32)  DEFAULT NULL,
  `type`          varchar(16)  DEFAULT NULL,
  `is_in_list`    tinyint(1)   DEFAULT NULL,
  `is_in_item`    tinyint(1)   DEFAULT NULL,
  `is_in_filter`  tinyint(1)   DEFAULT NULL,
  `is_private`    tinyint(1)   DEFAULT NULL,
  `is_fixed`      tinyint(1)   DEFAULT NULL,
  `is_fixed_type` tinyint(1)   DEFAULT NULL,
  `is_system`     tinyint(1)   DEFAULT NULL,
  `values`        text,
  `options`       text,
  `groups_read`   text,
  `groups_edit`   text,
  `filter_view`   text,
  PRIMARY KEY (`id`),
  KEY `ordering` (`ordering`),
  KEY `is_in_list` (`is_in_list`),
  KEY `is_in_item` (`is_in_item`),
  KEY `is_in_filter` (`is_in_filter`),
  KEY `is_private` (`is_private`),
  KEY `is_fixed` (`is_fixed`),
  KEY `is_fixed_type` (`is_fixed_type`),
  KEY `is_system` (`is_system`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

INSERT INTO `{#}con_news_fields` (`id`, `ctype_id`, `name`, `title`, `hint`, `ordering`, `fieldset`, `type`, `is_in_list`, `is_in_item`, `is_in_filter`, `is_private`, `is_fixed`, `is_fixed_type`,
                                  `is_system`, `values`, `options`, `groups_read`, `groups_edit`)
VALUES (1, 10, 'title', 'Заголовок новости', NULL, 1, NULL, 'caption', 1, 1, 1, NULL, 1, 1, 0, NULL,
        '---\nlabel_in_list: none\nlabel_in_item: none\nis_required: 1\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\n', '---\n- 0\n', '---\n- 0\n'),
       (2, 10, 'date_pub', 'Дата публикации', NULL, 6, NULL, 'date', 1, 1, 1, NULL, 1, 1, 1, NULL, '---\nlabel_in_list: none\nlabel_in_item: left\nshow_time: true\n', NULL, NULL),
       (3, 10, 'user', 'Автор', NULL, 5, NULL, 'user', 1, 1, 0, NULL, 1, 1, 1, NULL, '---\nlabel_in_list: none\nlabel_in_item: left\n', NULL, NULL),
       (4, 10, 'content', 'Текст новости', NULL, 4, NULL, 'html', NULL, 1, NULL, NULL, 1, NULL, NULL, NULL,
        '---\neditor: redactor\nis_html_filter: 1\nteaser_len:\nlabel_in_list: none\nlabel_in_item: none\nis_required: 1\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n',
        '---\n- 0\n', '---\n- 0\n'),
       (5, 10, 'teaser', 'Краткое описание новости', 'Выводится в списке новостей', 3, NULL, 'string', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
        '---\nmin_length: 0\nmax_length: 255\nlabel_in_list: none\nlabel_in_item: none\nis_required: 1\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n',
        '---\n- 0\n', '---\n- 0\n'),
       (6, 10, 'photo', 'Фотография', NULL, 2, NULL, 'image', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL,
        '---\nsize_teaser: small\nsize_full: normal\nsize_modal: big\nsizes:\n  - normal\n  - micro\n  - small\n  - big\nallow_import_link: null\nlabel_in_list: none\nlabel_in_item: none\nwrap_type: left\nwrap_width:\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n',
        '---\n- 0\n', '---\n- 0\n');

DROP TABLE IF EXISTS `{#}con_news_props`;
CREATE TABLE `{#}con_news_props`
(
  `id`           int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ctype_id`     int(11)      DEFAULT NULL,
  `title`        varchar(100) DEFAULT NULL,
  `fieldset`     varchar(32)  DEFAULT NULL,
  `type`         varchar(16)  DEFAULT NULL,
  `is_in_filter` tinyint(1)   DEFAULT NULL,
  `values`       text,
  `options`      text,
  PRIMARY KEY (`id`),
  KEY `is_in_filter` (`is_in_filter`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `{#}con_news_props_bind`;
CREATE TABLE `{#}con_news_props_bind`
(
  `id`       int(11) unsigned NOT NULL AUTO_INCREMENT,
  `prop_id`  int(11) DEFAULT NULL,
  `cat_id`   int(11) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `prop_id` (`prop_id`),
  KEY `ordering` (`cat_id`, `ordering`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

DROP TABLE IF EXISTS `{#}con_news_props_values`;
CREATE TABLE `{#}con_news_props_values`
(
  `prop_id` int(11)      DEFAULT NULL,
  `item_id` int(11)      DEFAULT NULL,
  `value`   varchar(255) DEFAULT NULL,
  KEY `prop_id` (`prop_id`),
  KEY `item_id` (`item_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

INSERT INTO `{#}con_pages` (`id`, `title`, `content`, `slug`, `seo_keys`, `seo_desc`, `seo_title`, `tags`, `date_pub`, `date_last_modified`, `date_pub_end`, `is_pub`, `hits_count`, `user_id`,
                            `parent_id`, `parent_type`, `parent_title`, `parent_url`, `is_parent_hidden`, `category_id`, `folder_id`, `is_comments_on`, `comments`, `rating`, `is_approved`,
                            `approved_by`, `date_approved`, `is_private`, `attach`)
VALUES (1, 'О проекте',
        '<p>В пределах аккумулятивных равнин вулканическое стекло занимает коллювий, за счет чего увеличивается мощность коры под многими хребтами. Палинологическое изучение осадков онежской трансгрессии, имеющей отчетливое межморенное залегание, показало, что притеррасная низменность горизонально обогащает апофиз, поскольку непосредственно мантийные струи не наблюдаются. Межледниковье опускает гидротермальный лакколит, делая этот типологический таксон районирования носителем важнейших инженерно-геологических характеристик природных условий. Фумарола определяет шток, что в конце концов приведет к полному разрушению хребта под действием собственного веса. Минеральное сырье имеет тенденцию биокосный грунт, делая этот типологический таксон районирования носителем важнейших инженерно-геологических характеристик природных условий. Поэтому многие геологи считают, что ядро опускает межпластовый надвиг, что в общем свидетельствует о преобладании тектонических опусканий в это время.</p>\r\n\r\n<p>Алмаз, с учетом региональных факторов, наклонно сменяет лавовый купол, в тоже время поднимаясь в пределах горстов до абсолютных высот 250 м. Как видно из самых общих закономерности распределения криолитозоны, извержение варьирует эвапорит, что обусловлено не только первичными неровностями эрозионно-тектонического рельефа поверхности кристаллических пород, но и проявлениями долее поздней блоковой тектоники. Фумарола, особенно в речных долинах, кавернозна. Ледниковое озеро затруднено.</p>\r\n\r\n<p>Питание прогиба исходным материалом смещает слабоминерализованный сталагмит, причем, вероятно, быстрее, чем прочность мантийного вещества. Инфлюация, скажем, за 100 тысяч лет, несет в себе палеокриогенный замок складки, что в общем свидетельствует о преобладании тектонических опусканий в это время. Но, пожалуй, еще более убедителен ортоклаз покрывает фитолитный криптархей, что свидетельствует о проникновении днепровских льдов в бассейн Дона. Амфибол отчетливо и полно пододвигается под пегматитовый бентос, где на поверхность выведены кристаллические структуры фундамента. Изостазия имеет тенденцию молого-шекснинский криптархей, но приводит к загрязнению окружающей среды.</p>\r\n\r\n<p><a href="http://referats.yandex.ru/">Источник</a> </p>',
        'about', 'свидетельствует, криптархей, имеет, фумарола, условий, тенденцию, общем, опусканий, тектонических, преобладании',
        'В пределах аккумулятивных равнин вулканическое стекло занимает коллювий, за счет чего увеличивается мощность коры под многими хребтами', NULL, NULL, DATE_SUB(NOW(), INTERVAL 11 DAY),
        DATE_SUB(NOW(), INTERVAL 11 DAY), NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, 0, 0, 1, NULL, NULL, 0, ''),
       (2, 'Правила сайта',
        '<p>1.&nbsp;Запрещены&nbsp;любые формы оскорблений участников сообщества или администрации, в том числе нецензурные логины и никнеймы.</p>\r\n\r\n<p>2.&nbsp;Запрещен мат, в том числе завуалированный.</p>\r\n\r\n<p>3.&nbsp;Запрещено&nbsp;публичное обсуждение действий администрации и ее представителей.</p>\r\n\r\n<p>4. Администрация проекта оставляет за собой право изменять и дополнять данные правила в любой момент времени.</p>\r\n\r\n<p>5. В общении на сайте придерживайтесь норм грамматики русского языка и общепринятой вежливости. Запрещено осознанное коверканье слов, жаргон. Избегайте необоснованного перехода на "ты".</p>\r\n\r\n<p><a name="forum"></a></p>',
        'rules', 'администрации, числе, право, администрация, представителей.\r\r4, проекта, действий, собой, оставляет, изменять',
        '1.&nbsp;Запрещены&nbsp;любые формы оскорблений участников сообщества или администрации, в том числе нецензурные логины и никнеймы.\r\r2.&nbsp;Запрещен мат, в том числе завуалированный.\r\r3',
        NULL, NULL, DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY), NULL, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, 0, 0, 1, NULL, DATE_SUB(NOW(), INTERVAL 10 DAY), 0,
        '');

INSERT INTO `{#}con_pages_cats_bind` (`item_id`, `category_id`)
VALUES (1, 1),
       (2, 1);

INSERT INTO `{#}menu_items` (`id`, `menu_id`, `parent_id`, `title`, `url`, `ordering`, `options`, `groups_view`, `groups_hide`)
VALUES (18, 1, 0, 'О проекте', 'pages/about.html', 1, '---\nclass: \n', '---\n- 0\n', NULL),
       (19, 1, 0, 'Правила сайта', 'pages/rules.html', 2, '---\nclass: \n', '---\n- 0\n', NULL),
       (40, 1, 0, 'Новости', '{content:news}', 1, '---\ntarget: _self\nclass:', '---\n- 0\n', NULL);


INSERT INTO `{#}perms_users` (`rule_id`, `group_id`, `subject`, `value`)
VALUES (1, 4, 'news', 'yes'),
       (1, 5, 'news', 'yes'),
       (1, 6, 'news', 'yes'),
       (4, 6, 'news', '1'),
       (3, 4, 'news', 'own'),
       (3, 5, 'news', 'all'),
       (3, 6, 'news', 'all'),
       (6, 6, 'news', '1'),
       (2, 4, 'news', 'own'),
       (2, 5, 'news', 'all'),
       (2, 6, 'news', 'all'),
       (5, 6, 'news', '1'),
       (9, 5, 'news', '1'),
       (9, 6, 'news', '1'),
       (8, 4, 'news', '1'),
       (8, 5, 'news', '1'),
       (8, 6, 'news', '1'),
       (13, 6, 'news', '1');


INSERT INTO `{#}widgets_bind`
VALUES (1, 'default', NULL, NULL, 3, 'Главное меню', NULL, NULL, NULL, NULL, NULL, 1, NULL, '---\n- 0\n', NULL, '---\nmenu: main\nis_detect: 1\nmax_items: 8\n', 0, 'top', 1, NULL, NULL, NULL),
       (2, 'default', NULL, NULL, 3, 'Меню авторизации', NULL, NULL, NULL, NULL, NULL, 1, NULL, '---\n- 1\n', NULL, '---\nmenu: header\nis_detect: 1\nmax_items: 0\n', 0, 'header', 1, NULL, NULL,
        NULL),
       (5, 'default', NULL, NULL, 3, 'Меню действий', NULL, NULL, NULL, 'fixed_actions_menu', NULL, 1, NULL, '---\n- 0\n', NULL, '---\nmenu: toolbar\ntemplate: menu\nis_detect: null\nmax_items: 0\n',
        0, 'left-top', 1, 'menu', 'wrapper', NULL),
       (16, 'default', NULL, NULL, 4, 'Новости', 'Все новости | news\r\nОбсуждаемые | news-discussed\r\n{Приватные | news/from_friends}', NULL, NULL, NULL, 1, 1, NULL, '---\n- 0\n', NULL,
        '---\nctype_id: 10\ncategory_id: 1\ndataset: 0\nimage_field: photo\nteaser_field:\nstyle: featured\nshow_details: 1\nteaser_len:\nlimit: 5\n', 1, 'left-bottom', 1, NULL, NULL, NULL),
       (17, 'default', NULL, NULL, 11, 'Слайдер контента', NULL, NULL, NULL, NULL, NULL, 1, NULL, '---\n- 0\n', NULL,
        '---\nctype_id: 10\ncategory_id: 1\ndataset: 0\nimage_field: photo\nbig_image_field:\nbig_image_preset: big\nteaser_field: teaser\ndelay: 5\nlimit: 5\n', 1, 'left-top', 1, NULL, NULL, NULL),
       (22, 'default', NULL, NULL, 9, 'Меню пользователя', NULL, NULL, NULL, NULL, NULL, 1, NULL, '---\n- 0\n', '---\n- 1\n', '---\nmenu: personal\nis_detect: 1\nmax_items: 0\n', 0, 'header', 3,
        'avatar', 'wrapper', NULL);

INSERT INTO `{#}widgets_pages` (`id`, `controller`, `name`, `title_const`, `title_subject`, `title`, `url_mask`, `url_mask_not`)
VALUES (143, 'content', 'pages.all', 'LANG_WP_CONTENT_ALL_PAGES', NULL, NULL, 'pages\npages-*\npages/*', NULL),
       (144, 'content', 'pages.list', 'LANG_WP_CONTENT_LIST', NULL, NULL, 'pages\npages-*\npages/*',
        'pages/*/view-*\npages/*.html\npages/add\npages/add/%\npages/addcat\npages/addcat/%\npages/editcat/%\npages/edit/*'),
       (145, 'content', 'pages.item', 'LANG_WP_CONTENT_ITEM', NULL, NULL, 'pages/*.html', NULL),
       (146, 'content', 'pages.edit', 'LANG_WP_CONTENT_ITEM_EDIT', NULL, NULL, 'pages/add\npages/edit/*', NULL),
       (163, 'content', 'news.all', 'LANG_WP_CONTENT_ALL_PAGES', NULL, NULL, 'news\nnews-*\nnews/*', NULL),
       (164, 'content', 'news.list', 'LANG_WP_CONTENT_LIST', NULL, NULL, 'news\nnews-*\nnews/*',
        'news/*/view-*\nnews/*.html\nnews/add\nnews/add/%\nnews/addcat\nnews/addcat/%\nnews/editcat/%\nnews/edit/*'),
       (165, 'content', 'news.item', 'LANG_WP_CONTENT_ITEM', NULL, NULL, 'news/*.html', NULL),
       (166, 'content', 'news.edit', 'LANG_WP_CONTENT_ITEM_EDIT', NULL, NULL, 'news/add\nnews/edit/*', NULL);
