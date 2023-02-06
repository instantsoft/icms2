<?php

class onLanguagesCtypeFieldAfterUpdate extends cmsAction {

    public function run($data){

        list($field, $ctype_name, $model) = $data;

        if(empty($field['multilanguage'])){
            return $data;
        }

        if($model->getContentTypeByName($ctype_name)){

            $table_name = $model->getContentTypeTableName($ctype_name);

        } else {

            $table_name = $ctype_name;
        }

        $this->model->addLanguagesFields([
            $table_name => [
                $field['name']
            ]
        ]);

        return [$field, $ctype_name, $model];
    }

}
