<?php

class actionAdminCtypesFiltersEnable extends cmsAction {

    public function run($ctype_id){

        if (!$ctype_id) { cmsCore::error404(); }

        $ctype = $this->model_content->getContentType($ctype_id);
        if (!$ctype) { cmsCore::error404(); }

        $back_url = $this->request->get('back', '');

        $table_exists = $this->model_content->isFiltersTableExists($ctype['name']);

        if(!$table_exists){

            $table_name = $this->model_content->getContentTypeTableName($ctype['name']).'_filters';

            $sql = "CREATE TABLE `{#}{$table_name}` (
                    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `hash` varchar(32) DEFAULT NULL,
                    `slug` varchar(100) NOT NULL,
                    `title` varchar(100) NOT NULL,
                    `description` text,
                    `filters` text,
                    `seo_keys` varchar(256) DEFAULT NULL,
                    `seo_desc` varchar(256) DEFAULT NULL,
                    `seo_title` varchar(256) DEFAULT NULL,
                    `seo_h1` varchar(256) DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    KEY `slug` (`slug`),
                    KEY `hash` (`hash`)
                ) ENGINE={$this->cms_config->db_engine} DEFAULT CHARSET=utf8;";

            $this->model_content->db->query($sql);

            cmsUser::addSessionMessage(LANG_CP_FILTER_TABLE_SUCCESS, 'success');

        }

        if ($back_url){
            $this->redirect($back_url);
        } else {
            $this->redirectBack();
        }

    }

}
