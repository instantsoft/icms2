<?php

class modelLanguages extends cmsModel {

    public function addLanguagesFields($create_db_fields) {

        $langs = cmsCore::getDirsList('system/languages', true);

        $current_lang_key = array_search($this->default_lang, $langs);

        unset($langs[$current_lang_key]);

        foreach ($create_db_fields as $table_name => $fields) {

            $table_columns = $this->getTableColumnsSql($table_name);

            foreach ($fields as $field_name) {

                foreach ($langs as $lang) {

                    $lang_field_name = $field_name.'_'.$lang;

                    if(isset($table_columns[$lang_field_name])){
                        continue;
                    }

                    $this->db->query("ALTER TABLE `{#}{$table_name}` ADD `{$lang_field_name}` {$table_columns[$field_name]} AFTER `{$field_name}`");
                }
            }
        }

    }

    private function getTableColumnsSql($table_name) {

        $result = $this->db->query("SHOW COLUMNS FROM `{#}{$table_name}`");

        $fields = [];

        while($data = $this->db->fetchAssoc($result)){

            $sql = [$data['Type']];

            if($data['Null'] === 'NO') {
                $sql[] = 'NOT NULL';
            } else {
                $sql[] = 'NULL';
            }

            if($data['Default'] === null && $data['Null'] === 'YES') {
                $sql[] = 'DEFAULT NULL';
            } else {
                $sql[] = "DEFAULT '{$data['Default']}'";
            }

            $fields[$data['Field']] = implode(' ', $sql);
        }

        return $fields;
    }
}
