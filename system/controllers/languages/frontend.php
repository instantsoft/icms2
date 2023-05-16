<?php
/**
 * @property \modelLanguages $model
 */
class languages extends cmsFrontend {

    protected $useOptions = true;

    /**
     * Включает мультиязычные поля формы
     * И при необходимости создаёт колонки в БД
     *
     * @param cmsForm $form
     * @return void
     */
    public function enableMultilanguageFormFields(cmsForm $form) {

        if(!$this->cms_config->is_user_change_lang){
            return;
        }

        // Поля, которые нужно создать в БД
        $create_db_fields = [];

        $structure = $form->getStructure();

        // Включаем в самих формах
        foreach ($structure as $fid => $fieldset) {

            if (!isset($fieldset['childs'])) { continue; }

            // Могут быть включены для всех полей набора сразу
            $is_all_fields_enable = $fieldset['can_multilanguage'] ?? false;

            // Дефолтные параметры для полей
            $default_fields_params = $fieldset['multilanguage_params'] ?? ['is_table_field' => false];

            foreach($fieldset['childs'] as $id => $field){

                if(!$is_all_fields_enable && !$field->can_multilanguage){
                    continue;
                }

                $field->multilanguage = true;

                $is_table_field = $field->multilanguage_params['is_table_field'] ?? $default_fields_params['is_table_field'];

                if($is_table_field){

                    $table_name = $field->multilanguage_params['table'] ?? $default_fields_params['table'];

                    $create_db_fields[$table_name][] = $field->getName();
                }
            }

        }

        if($create_db_fields){
            $this->model->addLanguagesFields($create_db_fields);
        }

        return;
    }

    /**
     * Добавляет теги hreflang в HEAD страницы
     * https://developers.google.com/search/docs/specialty/international/localized-versions?hl=ru
     *
     * @return void
     */
    public function addHreflangTags() {

        if(!$this->cms_config->is_user_change_lang){
            return;
        }

        $langs = cmsCore::getLanguages();

        $uri = $this->cms_core->uri.($this->cms_core->uri_query ? '?'.http_build_query($this->cms_core->uri_query) : '');

        foreach ($langs as $lang) {

            $lang_prefix = '/';

            if ($this->cms_config->language !== $lang) {

                $lang_prefix .= $lang;

                if ($uri) {
                    $lang_prefix .= '/';
                }
            }

            $this->cms_template->addHead('<link rel="alternate" href="'.html($this->cms_config->host . $lang_prefix . $uri, false).'" hreflang="'.$lang.'" />');
        }

        $this->cms_template->addHead('<link rel="alternate" href="'.html($this->cms_config->host . '/' . $uri, false).'" hreflang="x-default" />');

        return;
    }

}
