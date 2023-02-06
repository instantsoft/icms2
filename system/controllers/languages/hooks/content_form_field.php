<?php

class onLanguagesContentFormField extends cmsAction {

    public function run($data){

        list($form, $ctype, $field) = $data;

        if($this->cms_config->is_user_change_lang){

            $form->addField('basic', new fieldCheckbox('multilanguage', [
                'title' => LANG_LANGUAGES_FIELD
            ]));

            if($ctype['id']){
                $table_name = $this->model->getContentTypeTableName($ctype['name']).'_fields';
            } else {
                $table_name = $ctype['name'].'_fields';
            }

            $this->model->db->addTableField($table_name, 'multilanguage', 'TINYINT(1) UNSIGNED NULL DEFAULT NULL');
        }

        return [$form, $ctype, $field];
    }

}
