<?php

function install_package(){

	$core = cmsCore::getInstance();

	$content_model = cmsCore::getModel('content');

	$ctypes = $content_model->getContentTypes();

	foreach($ctypes as $ctype){

        $sql = "ALTER TABLE `{#}{$content_model->table_prefix}{$ctype['name']}` ADD INDEX (`slug`)";

		$core->db->query($sql);

	}

    if($core->db->isFieldExists('{users}', 'is_online')){
        $core->db->query("ALTER TABLE `{users}` DROP `is_online`;");
    }

}
