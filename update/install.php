<?php

function install_package(){

	$core = cmsCore::getInstance();
	$content_model = cmsCore::getModel('content');

    $remove_table_indexes = array(
        '{users}_friends' => array(
            'is_mutual', 'friend_id', 'user_id'
        ),
        'tags_bind' => array(
            'tag_id'
        ),
    );

    $add_table_indexes = array(
        '{users}_friends' => array(
            'user_id'   => array('user_id', 'is_mutual'),
            'friend_id' => array('friend_id', 'is_mutual')
        ),
        'tags_bind' => array(
            'tag_id' => array('tag_id')
        ),
    );

    // все таблицы
    // удаляем ненужные индексы
    foreach ($remove_table_indexes as $table=>$ri) {

        foreach ($ri as $index_name) {
            $core->db->dropIndex($table, $index_name);
        }

    }
    // добавляем нужные
    foreach ($add_table_indexes as $table=>$indexes) {

        foreach ($indexes as $index_name => $fields) {
            $core->db->addIndex($table, $fields, $index_name);
        }

    }

    //************************************************************************//
    // типы контента

	$ctypes = $content_model->getContentTypes();

    $varchar_fields = array('seo_keys','seo_desc','seo_title');

    $remove_ctype_indexes = array(
        '_cats' => array('ns_left', 'ns_right', 'ns_differ', 'ns_ignore', 'parent_id'),
        '_props_bind' => array('cat_id', 'ordering'),
        '' => array(
            'date_pub','user_id','parent_id','parent_type','is_comments_on','is_approved','date_approved',
            'comments','rating','is_private','is_parent_hidden','photos_count','date_pub_end','date_last_modified','title'
        ),
    );

    $add_ctype_indexes = array(
        '_cats' => array(
            'ns_left'   => array('ns_level', 'ns_right', 'ns_left'),
            'parent_id' => array('parent_id', 'ns_left'),
        ),
        '_props_bind' => array(
            'ordering' => array('cat_id', 'ordering')
        ),
        '' => array(
            'date_pub'     => array('is_pub', 'is_parent_hidden', 'is_approved', 'date_pub'),
            'parent_id'    => array('parent_id', 'parent_type', 'date_pub'),
            'user_id'      => array('user_id', 'date_pub'),
            'date_pub_end' => array('date_pub_end')
        )
    );

    $add_ctype_fulltext_indexes = array(
        '' => array(
            'title' => array('title')
        )
    );

	foreach($ctypes as $ctype){

        // меняем типы сео полям
        foreach ($varchar_fields as $varchar_field) {

            $core->db->query("ALTER TABLE  `{#}{$content_model->table_prefix}{$ctype['name']}` CHANGE  `{$varchar_field}`  `{$varchar_field}` VARCHAR( 256 ) NULL DEFAULT NULL;");

            $core->db->query("ALTER TABLE  `{#}{$content_model->table_prefix}{$ctype['name']}_cats` CHANGE  `{$varchar_field}`  `{$varchar_field}` VARCHAR( 256 ) NULL DEFAULT NULL;");
        }

        // комментарии по умолчанию включены
        $core->db->query("ALTER TABLE  `{#}{$content_model->table_prefix}{$ctype['name']}` CHANGE  `is_comments_on`  `is_comments_on` TINYINT( 1 ) UNSIGNED NULL DEFAULT  '1'");
        // для текущих записей включаем их
        $core->db->query("UPDATE `{#}{$content_model->table_prefix}{$ctype['name']}` SET `is_comments_on` =  '1'");

        // удаляем ненужные индексы
        foreach ($remove_ctype_indexes as $table_postfix=>$rcci) {

            foreach ($rcci as $index_name) {
                $core->db->dropIndex($content_model->table_prefix.$ctype['name'].$table_postfix, $index_name);
            }

        }

        // добавляем нужные обычные индексы
        foreach ($add_ctype_indexes as $table_postfix=>$indexes) {

            foreach ($indexes as $index_name => $fields) {
                $core->db->addIndex($content_model->table_prefix.$ctype['name'].$table_postfix, $fields, $index_name);
            }

        }
        // добавляем FULLTEXT индексы только для поля title. остальные поля включаются в индекс в настройках
        foreach ($add_ctype_fulltext_indexes as $table_postfix=>$fulltext_indexes) {

            foreach ($fulltext_indexes as $index_name => $fields) {
                $core->db->addIndex($content_model->table_prefix.$ctype['name'].$table_postfix, $fields, $index_name, 'FULLTEXT');
            }

        }

	}

    if(!$core->db->isFieldExists('content_datasets', 'index')){
        $core->db->query("ALTER TABLE `{#}content_datasets` ADD `index` VARCHAR(40) NULL DEFAULT NULL COMMENT 'Название используемого индекса' AFTER `sorting`, ADD INDEX (`index`);");
    }

    if(!$core->db->isFieldExists('controllers', 'is_external')){
        $core->db->query("ALTER TABLE `{#}controllers` ADD `is_external` TINYINT(1) UNSIGNED NULL DEFAULT NULL COMMENT 'Сторонний компонент' AFTER `is_backend`");
    }

    if(!$core->db->isFieldExists('rss_feeds', 'template')){
        $core->db->query("ALTER TABLE  `{#}rss_feeds` ADD  `template` VARCHAR(30) NOT NULL DEFAULT  'feed' COMMENT  'Шаблон ленты';");
    }

    if(!$core->db->isFieldExists('images_presets', 'quality')){
        $core->db->query("ALTER TABLE  `{#}images_presets` ADD  `quality` TINYINT(1) NOT NULL DEFAULT  '90';");
    }

    if(!$core->db->getRowsCount('perms_rules', "controller = 'content' AND name = 'disable_comments'", 1)){
        $core->db->query("INSERT INTO `{#}perms_rules` (`controller`,`name`,`type`,`options`) VALUES ('content','disable_comments','flag', NULL)");
    }
    $core->db->query("UPDATE `{#}perms_rules` SET `options` = 'own,all,full_delete' WHERE controller = 'comments' AND name = 'delete'");

    // для всех датасетов создаем индексы, если нужно
    $datasets = $content_model->select('ct.name', 'ctype_name')->
            joinInner('content_types', 'ct', 'ct.id = i.ctype_id')->
            get('content_datasets', function($item, $model){
                $item['filters'] = cmsModel::yamlToArray($item['filters']);
                $item['sorting'] = cmsModel::yamlToArray($item['sorting']);
                return $item;
            });
    if($datasets){
        foreach ($datasets as $dataset) {
            $index = $content_model->addContentDatasetIndex($dataset, $dataset['ctype_name']);
            $content_model->update('content_datasets', $dataset['id'], array('index'=>$index), true);
        }
    }

    $config = cmsConfig::getInstance();

    $values = $config->getAll();
    $values['db_engine']      = 'InnoDB';
    $values['detect_ip_key']  = 'REMOTE_ADDR';
    $values['allow_ips']      = '';
    $values['default_editor'] = 'redactor';
    $values['show_breadcrumbs'] = 1;
    if(!$config->save($values)){
        cmsUser::addSessionMessage('Не могу записать файл конфигурации сайта. Добавьте в него строку <b>"db_engine" => "InnoDB",</b>. После этого сделайте этот файл доступным для записи, зайдите в общие настройки сайта и просто пересохраните их.', 'info');
    }

    // если вдруг для каких то компонентов нет конфига в таблице cms_controllers
    // пропускаем компонент карты сайта, т.к. там конфиг динамический
    // будем надеяться, что опции в нем хоть раз сохранялись =)
    $controllers = $content_model->filterNotEqual('name', 'sitemap')->get('controllers', function ($item, $model) {
        $item['options'] = cmsModel::yamlToArray($item['options']);
        return $item;
    }, 'name');
    foreach ($controllers as $controller) {

        if(in_array($controller['name'], array('video','channels','places'))){
            continue;
        }

        $controller_root_path = cmsConfig::get('root_path').'system/controllers/'.$controller['name'].'/';

        $form_file = $controller_root_path.'backend/forms/form_options.php';
        $form_name = $controller['name'] . 'options';

        cmsCore::loadControllerLanguage($controller['name']);

        $form = cmsForm::getForm($form_file, $form_name, false);
        if ($form) {
            $options = $form->parse(new cmsRequest(cmsController::loadOptions($controller['name'])));
        } else {
            $options = null;
        }

        $content_model->filterEqual('name', $controller['name'])->updateFiltered('controllers', array(
            'options' => $options
        ));

    }

}
