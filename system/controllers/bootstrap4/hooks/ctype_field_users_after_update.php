<?php

class onBootstrap4CtypeFieldUsersAfterUpdate extends cmsAction {

	public function run($data){

        list($field, $model) = $data;

        if($field['name'] == 'avatar'){

            $list_preset_name = $field['options']['size_teaser'];

            $preset = $this->model_images->getPresetByName($list_preset_name);

            if($preset){

                $avatar_inlist_size = $preset['width'].'px';

                $template = new cmsTemplate('modern');

                $template_modern_options = $template->getOptions();

                // Устанавливаем новый размер
                $template_modern_options['scss']['avatar-inlist-size'] = $avatar_inlist_size;

                // Запоминаем в конфиге шаблона
                $template->saveOptions($template_modern_options);

                // Перекомпилируем CSS
                // Путь к CSS файлам
                $css_dir = cmsTemplate::TEMPLATE_BASE_PATH . 'modern/css/';
                $css_dir_path = $this->cms_config->root_path . $css_dir;

                // Тут все опции SCSS
                $scss = $template_modern_options['scss'];
                // Заменяем опции, которые формируются из select, но могут быть заданы вручную
                if(!empty($template_modern_options['custom_scss'])){
                    foreach ($template_modern_options['custom_scss'] as $key => $value) {
                        if(!$scss[$key]){
                            $scss[$key] = $value;
                        }
                    }
                }

                // Компилируем основной CSS файл шаблона
                $theme_css = $this->compileScss('scss/theme/theme.scss', $scss);
                if($theme_css){

                    $compiled_file_path = $css_dir_path.'theme.css';

                    if(is_writable($compiled_file_path)){
                        file_put_contents($compiled_file_path, $theme_css);
                    } else {
                        cmsUser::addSessionMessage(sprintf(LANG_CP_FILE_NOT_WRITABLE, $css_dir.'theme.css'), 'error');
                    }
                } elseif($this->hasCompileMessage()) {
                    cmsUser::addSessionMessage($this->getCompileMessage(), 'error');
                }

            }
        }

        return [$field, $model];
    }

}
