<?php

function install_package(){

	$core = cmsCore::getInstance();
    $content_model = cmsCore::getModel('content');
    $photo_model = cmsCore::getModel('photos');

    if(!$core->db->getRowsCount('controllers', "name = 'redirect'")){
        $core->db->query("INSERT INTO `{#}controllers` (`title`, `name`, `is_enabled`, `options`, `author`, `url`, `version`, `is_backend`, `is_external`) VALUES ('Редиректы', 'redirect', 1, '---\nno_redirect_list:\nblack_list:\nis_check_link: null\nwhite_list:\nredirect_time: 10\n', 'InstantCMS Team', 'http://www.instantcms.ru', '2.0', 1, NULL);");
    }
    if(!$core->db->getRowsCount('controllers', "name = 'commentsvk'")){
        $core->db->query("INSERT INTO `{#}controllers` (`title`, `name`, `is_enabled`, `options`, `author`, `url`, `version`, `is_backend`, `is_external`) VALUES ('Комментарии Вконтакте', 'commentsvk', 1, '---\napi_id: \nredesign: null\nautoPublish: 1\nnorealtime: null\nmini: 0\nattach:\n  - graffiti\n  - photo\n  - video\n  - audio\nlimit: 50\n', 'InstantCMS Team', 'http://www.instantcms.ru', '1.0', 1, NULL)");
    }
    if(!$core->db->getRowsCount('controllers', "name = 'geo'")){
        $core->db->query("INSERT INTO `{#}controllers` (`title`, `name`, `is_enabled`, `options`, `author`, `url`, `version`, `is_backend`, `is_external`) VALUES ('География', 'geo', 1, NULL, 'InstantCMS Team', 'http://www.instantcms.ru', '2.0', 1, NULL)");
    }

    if(!$core->db->getRowsCount('activity_types', "name = 'vote.comment'")){
        $core->db->query("INSERT INTO `{#}activity_types` (`is_enabled`, `controller`, `name`, `title`, `description`) VALUES (1, 'comments', 'vote.comment', 'Оценка комментария', 'оценил комментарий на странице %s');");
    }

    if(!$core->db->getRowsCount('scheduler_tasks', "controller = 'messages'")){
        $core->db->query("INSERT INTO `{#}scheduler_tasks` (`title`, `controller`, `hook`, `period`, `is_active`) VALUES ('Очистка удалённых личных сообщений', 'messages', 'clean', '1440', '1');");
    }

    if(!$core->db->getRowsCount('scheduler_tasks', "controller = 'auth' AND hook = 'delete_expired_unverified'")){
        $core->db->query("INSERT INTO `{#}scheduler_tasks` (`title`, `controller`, `hook`, `period`, `is_active`) VALUES ('Удаление пользователей, не прошедших верификацию', 'auth', 'delete_expired_unverified', '60', '1');");
    }

    if(!isFieldExists('rating_log', 'ip')){
        $core->db->query("ALTER TABLE `{#}rating_log` ADD `ip` INT(10) UNSIGNED NULL DEFAULT NULL, ADD INDEX (`ip`)");
    }

    if(!isFieldExists('content_datasets', 'description')){
        $core->db->query("ALTER TABLE `{#}content_datasets` ADD `description` TEXT NULL DEFAULT NULL AFTER `title`");
    }

    if(!isFieldExists('content_datasets', 'seo_keys')){
        $core->db->query("ALTER TABLE `{#}content_datasets` ADD `seo_keys` VARCHAR(256) NULL DEFAULT NULL");
    }

    if(!isFieldExists('content_datasets', 'seo_desc')){
        $core->db->query("ALTER TABLE `{#}content_datasets` ADD `seo_desc` VARCHAR(256) NULL DEFAULT NULL");
    }

    if(!isFieldExists('comments', 'is_approved')){
        $core->db->query("ALTER TABLE `{#}comments` ADD `is_approved` TINYINT(1) UNSIGNED NULL DEFAULT '1'");
    }

    if(isFieldExists('{users}', 'auth_token')){
        $core->db->query("ALTER TABLE `{users}` DROP `auth_token`");
    }

    if(!isFieldExists('{users}_messages', 'is_deleted')){
        $core->db->query("ALTER TABLE `{users}_messages` ADD `is_deleted` TINYINT(1) UNSIGNED NULL DEFAULT NULL");
    }

    if(!isFieldExists('{users}_messages', 'date_delete')){
        $core->db->query("ALTER TABLE `{users}_messages` ADD `date_delete` TIMESTAMP NULL DEFAULT NULL COMMENT 'Дата удаления' AFTER `date_pub`, ADD INDEX (`date_delete`);");
    }

    $core->db->query("ALTER TABLE `{#}comments` CHANGE `author_url` `author_url` VARCHAR( 15 ) NULL DEFAULT NULL COMMENT 'ip адрес'");
    $core->db->query("ALTER TABLE `{users}_messages` CHANGE `date_pub` `date_pub` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата создания'");

    // фотогалерея —> //

    if(!isFieldExists('photos', 'hits_count')){
        $core->db->query("ALTER TABLE `{#}photos` ADD `hits_count` INT(11) UNSIGNED NOT NULL DEFAULT '0'");
    }

    if(!isFieldExists('photos', 'downloads_count')){
        $core->db->query("ALTER TABLE `{#}photos` ADD `downloads_count` INT(11) UNSIGNED NOT NULL DEFAULT '0'");
    }

    if(!isFieldExists('photos', 'sizes')){
        $core->db->query("ALTER TABLE `{#}photos` ADD `sizes` VARCHAR(250) NULL DEFAULT NULL AFTER `image`");
    }

    if(!isFieldExists('photos', 'width')){
        $core->db->query("ALTER TABLE `{#}photos` ADD `width` SMALLINT UNSIGNED NOT NULL DEFAULT '0' AFTER `image`");
    }

    if(!isFieldExists('photos', 'height')){
        $core->db->query("ALTER TABLE `{#}photos` ADD `height` SMALLINT UNSIGNED NOT NULL DEFAULT '0' AFTER `image`");
    }

    if(!isFieldExists('photos', 'orientation')){
        $core->db->query("ALTER TABLE `{#}photos` ADD `orientation` ENUM( 'square',  'landscape',  'portrait',  '') NULL DEFAULT NULL");
    }

    if(!isFieldExists('photos', 'type')){
        $core->db->query("ALTER TABLE `{#}photos` ADD `type` TINYINT UNSIGNED NULL DEFAULT NULL");
    }

    if(!isFieldExists('photos', 'content')){
        $core->db->query("ALTER TABLE  `{#}photos` ADD  `content` TEXT NULL DEFAULT NULL AFTER  `title`");
        $core->db->query("ALTER TABLE  `{#}photos` ADD  `content_source` TEXT NULL DEFAULT NULL AFTER  `title`");
    }

    if(!isFieldExists('photos', 'camera')){
        $core->db->query("ALTER TABLE `{#}photos` ADD `camera` VARCHAR(50) NULL DEFAULT NULL");
    }

    if(!isFieldExists('photos', 'slug')){
        $core->db->query("ALTER TABLE `{#}photos` ADD `slug` VARCHAR(100) NULL DEFAULT NULL");
    }

    if(!isFieldExists('photos', 'is_private')){
        $core->db->query("ALTER TABLE  `{#}photos` ADD `is_private` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'");
    }

    if(!isFieldExists('photos', 'exif')){
        $core->db->query("ALTER TABLE `{#}photos` ADD `exif` VARCHAR(250) NULL DEFAULT NULL AFTER `image`");
    }

    if(!isFieldExists('photos', 'date_photo')){
        $core->db->query("ALTER TABLE `{#}photos` ADD `date_photo` TIMESTAMP NULL DEFAULT NULL AFTER `date_pub`");
    }

    if(!isFieldExists('photos', 'ordering')){
        $core->db->query("ALTER TABLE `{#}photos` ADD `ordering` INT(11) UNSIGNED NOT NULL DEFAULT '0'");
    }

    $core->db->query("ALTER TABLE `{#}photos` CHANGE `date_pub` `date_pub` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
    $core->db->query("ALTER TABLE `{#}photos` CHANGE `title` `title` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL");
    $core->db->query("ALTER TABLE `{#}photos` ENGINE = MYISAM");

    if(!$core->db->getRowsCount('widgets', "controller = 'photos' AND name = 'list'")){
        $core->db->query("INSERT INTO `{#}widgets` (`controller`, `name`, `title`, `author`, `url`, `version`) VALUES ('photos', 'list', 'Список фотографий', 'InstantCMS Team', 'http://www.instantcms.ru', '2.0');");
    }

    if(!$core->db->getRowsCount('widgets_pages', "controller = 'photos' AND name = 'item'")){
        $core->db->query("INSERT INTO `{#}widgets_pages` (`controller`, `name`, `title_const`, `url_mask`) VALUES ('photos', 'item', 'LANG_PHOTOS_WP_ITEM', 'photos/*.html')");
    }

    if(!$core->db->getRowsCount('widgets_pages', "controller = 'photos' AND name = 'upload'")){
        $core->db->query("INSERT INTO `{#}widgets_pages` (`controller`, `name`, `title_const`, `url_mask`) VALUES ('photos', 'upload', 'LANG_PHOTOS_WP_UPLOAD', 'photos/upload/%\r\nphotos/upload')");
    }

    save_controller_options(array('photos'));

    migratePhotos();

    // —> фотогалерея //

    $remove_table_indexes = array(
        '{users}_contacts' => array(
            'user_id', 'contact_id'
        ),
        '{users}_ignors' => array(
            'user_id', 'ignored_id'
        ),
        '{users}_messages' => array(
            'from_id', 'to_id', 'date_pub', 'is_new'
        ),
        'photos' => array(
            'comments', 'rating', 'date_pub', 'user_id', 'album_id'
        ),
        'content_folders' => array(
            'ctype_id', 'user_id'
        ),
    );

    $add_table_indexes = array(
        'comments' => array(
            'author_url' => array('author_url'),
            'date_pub' => array('date_pub')
        ),
        '{users}_contacts' => array(
            'user_id' => array('user_id', 'contact_id'),
            'contact_id' => array('contact_id', 'user_id')
        ),
        '{users}_ignors' => array(
            'user_id' => array('user_id'),
            'ignored_user_id' => array('ignored_user_id', 'user_id')
        ),
        '{users}_messages' => array(
            'from_id' => array('from_id', 'to_id', 'is_deleted'),
            'to_id' => array('to_id', 'is_new', 'is_deleted'),
        ),
        'content_folders' => array(
            'user_id' => array('user_id', 'ctype_id')
        ),
        'photos' => array(
            'album_id' => array('album_id', 'date_pub', 'id'),
            'user_id' => array('user_id', 'date_pub'),
            'slug' => array('slug'),
            'camera' => array('camera'),
            'ordering' => array('ordering')
        ),
    );

    $add_table_ft_indexes = array(
        'photos' => array(
            'title' => array('title', 'content')
        ),
    );

    // удаляем ненужные индексы
    if($remove_table_indexes){
        foreach ($remove_table_indexes as $table=>$indexes) {
            foreach ($indexes as $index_name) {
                $core->db->dropIndex($table, $index_name);
            }
        }
    }
    // добавляем нужные
    if($add_table_indexes){
        foreach ($add_table_indexes as $table=>$indexes) {
            foreach ($indexes as $index_name => $fields) {
                $core->db->addIndex($table, $fields, $index_name);
            }
        }
    }
    if($add_table_ft_indexes){
        foreach ($add_table_ft_indexes as $table=>$indexes) {
            foreach ($indexes as $index_name => $fields) {
                $core->db->addIndex($table, $fields, $index_name, 'FULLTEXT');
            }
        }
    }

    // в ленте делаем  относительные урлы
    $root = cmsConfig::get('root');
    $root_len = strlen($root)+1;
    $core->db->query("UPDATE `{#}activity` SET `subject_url` = SUBSTRING(`subject_url`, {$root_len}) WHERE `subject_url` IS NOT NULL AND `subject_url` LIKE '{$root}%'");
    $core->db->query("UPDATE `{#}activity` SET `reply_url` = SUBSTRING(`reply_url`, {$root_len}) WHERE `reply_url` IS NOT NULL AND `reply_url` LIKE '{$root}%'");
    $core->db->query("UPDATE `{#}activity` SET `images` = REPLACE(`images`, 'url: {$root}', 'url: ') WHERE `images` IS NOT NULL");

    // правим некорректные записи стены
    $core->db->query("UPDATE `{#}wall_entries` SET `controller` = 'users' WHERE `controller` IS NULL AND `profile_type` = 'user'");
    $core->db->query("UPDATE `{#}wall_entries` SET `controller` = 'groups' WHERE `controller` IS NULL AND `profile_type` = 'group'");

    // права доступа flag
    add_perms(array(
        'comments' => array(
            'add_approved', 'is_moderator'
        )
    ), 'flag');

}

function add_perms($data, $type, $options = null) {

    $model = new cmsModel();

    foreach ($data as $controller => $names) {

        foreach ($names as $name) {

            if(!$model->db->getRowsCount('perms_rules', "controller = '{$controller}' AND name = '{$name}'", 1)){
                $model->insert('perms_rules', array(
                    'controller' => $controller,
                    'name'       => $name,
                    'type'       => $type,
                    'options'    => $options
                ));
            }

        }

    }

}

// настройки контроллеров для пересохранения
function save_controller_options($controllers) {

    foreach ($controllers as $controller) {
        $controller_root_path = cmsConfig::get('root_path').'system/controllers/'.$controller.'/';
        $form_file = $controller_root_path.'backend/forms/form_options.php';
        $form_name = $controller.'options';
        cmsCore::loadControllerLanguage($controller);
        $form = cmsForm::getForm($form_file, $form_name, false);
        if ($form) {
            $options = $form->parse(new cmsRequest(cmsController::loadOptions($controller)));
            cmsCore::getModel('content')->filterEqual('name', $controller)->updateFiltered('controllers', array(
                'options' => $options
            ));
        }
    }

}
/**
 * Правильная функция isFieldExists
 * сделана для случая, если сначала выполнится инсталлер, а потом заменятся файлы
 */
function isFieldExists($table_name, $field) {
    $table_fields = getTableFields($table_name);
    return in_array($field, $table_fields, true);
}
function getTableFields($table) {
    $db = cmsDatabase::getInstance();
    $fields = array();
    $result = $db->query("SHOW COLUMNS FROM `{#}{$table}`");
    while($data = $db->fetchAssoc($result)){
        $fields[] = $data['Field'];
    }
    return $fields;
}

function migratePhotos() {

    $model = cmsCore::getModel('photos');
    $config = cmsConfig::getInstance();

    $photos = $model->orderByList(array(
            array(
                'by' => 'album_id',
                'to' => 'asc'
            ),
            array(
                'by' => 'date_pub',
                'to' => 'asc'
            )
    ))->limit(false)->get('photos', function($item, $model){
        $item['image'] = cmsModel::yamlToArray($item['image']);
        return $item;
    });

    if(!$photos){ return false; }

    $album_ids = $last_photo_id = $order = array();

    foreach ($photos as $photo) {

        $album_ids[] = $photo['album_id'];

        $_order = isset($order[$photo['album_id']]) ? $order[$photo['album_id']] : 1;

        $_widths = $_heights = $sizes = $width_presets = array();

        foreach ($photo['image'] as $preset => $path) {

            if(!is_readable($config->upload_path.$path)){ continue; }

            $s = getimagesize($config->upload_path.$path);
            if ($s === false) { continue; }

            $_widths[]  = $s[0];
            $_heights[] = $s[1];

            $sizes[$preset] = array(
                'width'  => $s[0],
                'height' => $s[1]
            );

            $width_presets[$s[0]] = $preset;

        }

        $order[$photo['album_id']] = $_order + 1;
        $last_photo_id[$photo['album_id']] = $photo['id'];

        // exif
        $max_size_preset = $width_presets[max($_widths)];
        $image_data = img_get_params($config->upload_path.$photo['image'][$max_size_preset]);

        $date_photo = (isset($image_data['exif']['date']) ? $image_data['exif']['date'] : false);
        $camera     = (isset($image_data['exif']['camera']) ? $image_data['exif']['camera'] : null);
        unset($image_data['exif']['date'], $image_data['exif']['camera'], $image_data['exif']['orientation']);

        $photo['slug']     = $model->getPhotoSlug($photo);
        $photo['sizes']    = $sizes;
        $photo['height']   = max($_heights);
        $photo['width']    = max($_widths);
        $photo['ordering'] = $_order;
        $photo['orientation'] = $image_data['orientation'];
        $photo['date_photo'] = $date_photo;
        $photo['camera'] = $camera;
        $photo['exif'] = (!empty($image_data['exif']) ? $image_data['exif'] : null);

        $model->filterEqual('id', $photo['id'])->updateFiltered('photos', $photo);

    }

    $album_ids = array_unique($album_ids);

    foreach ($album_ids as $album_id) {

        cmsCache::getInstance()->clean("photos.{$album_id}");

        $model->updateAlbumCoverImage($album_id, array($last_photo_id[$album_id]));

        $model->updateAlbumPhotosCount($album_id);

    }

}