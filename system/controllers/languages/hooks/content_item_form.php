<?php

class onLanguagesContentItemForm extends cmsAction {

    public function run($data) {

        // Должна быть включена опция "Панель управления / Тип контента / Поля"
        if(empty($this->options['sources']['admin']['ctypes_field'])){
            return $data;
        }

        list($form, $item, $ctype) = $data;

        $this->enableMultilanguageFormFields($form);

        return [$form, $item, $ctype];
    }

}
