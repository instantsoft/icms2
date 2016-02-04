<?php

function install_package(){

	$core = cmsCore::getInstance();

    $remove_table_indexes = array(
        'tags' => array(
            'tag'
        )
    );

    $add_table_indexes = array(
        'tags_bind' => array(
            'target_controller' => array('target_controller', 'target_subject', 'tag_id')
        ),
    );
    $add_unique_table_indexes = array(
        'tags' => array(
            'tag'      => array('tag'),
            'frequency' => array('frequency', 'tag'),
        )
    );

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
    // добавляем нужные уникальные
    foreach ($add_unique_table_indexes as $table=>$indexes) {
        foreach ($indexes as $index_name => $fields) {
            $core->db->addIndex($table, $fields, $index_name, 'UNIQUE');
        }
    }

}
