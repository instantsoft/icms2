<?php

class onBootstrap4TemplateModernBeforeSaveOptions extends cmsAction {

    public function run($options){

        // Путь к CSS файлам
        $css_dir = cmsTemplate::TEMPLATE_BASE_PATH . 'modern/css/';
        $css_dir_path = $this->cms_config->root_path . $css_dir;

        // Компилируем основной CSS файл шаблона
        $theme_css = $this->compileScss('scss/theme/theme.scss', $options['scss']);
        if($theme_css){

            $compiled_file_path = $css_dir_path.'theme.css';

            if(is_writable($compiled_file_path)){
                file_put_contents($compiled_file_path, $theme_css);
            } else {
                cmsUser::addSessionMessage(sprintf(LANG_CP_FILE_NOT_WRITABLE, $css_dir.'theme.css'), 'error');
            }
        }

        // Компилируем вендоры
        $vendors = cmsCore::getDirsList(cmsTemplate::TEMPLATE_BASE_PATH . 'modern/scss/vendors');
        if($vendors){
            foreach ($vendors as $vendor_name) {

                $css_data = $this->compileScss('scss/vendors/'.$vendor_name.'/build.scss', $options['scss']);

                if($css_data){

                    $compiled_file_path = $css_dir_path.$vendor_name.'.css';

                    if(is_writable($compiled_file_path)){
                        file_put_contents($compiled_file_path, $css_data);
                    } else {
                        cmsUser::addSessionMessage(sprintf(LANG_CP_FILE_NOT_WRITABLE, $css_dir.$vendor_name.'.css'), 'error');
                    }
                }

            }
        }

        // Компилируем CSS компонентов
        $controllers = cmsCore::getDirsList(cmsTemplate::TEMPLATE_BASE_PATH . 'modern/scss/controllers');
        if($controllers){
            foreach ($controllers as $controller_name) {

                $css_data = $this->compileScss('scss/controllers/'.$controller_name.'/build.scss', $options['scss']);

                if($css_data){

                    $css_controller_dir = cmsTemplate::TEMPLATE_BASE_PATH . 'modern/controllers/'.$controller_name.'/';
                    $css_controller_dir_path = $this->cms_config->root_path . $css_controller_dir;

                    $compiled_file_path = $css_controller_dir_path.'styles.css';

                    if(is_writable($compiled_file_path)){
                        file_put_contents($compiled_file_path, $css_data);
                    } else {
                        cmsUser::addSessionMessage(sprintf(LANG_CP_FILE_NOT_WRITABLE, $css_controller_dir.'styles.css'), 'error');
                    }
                }

            }
        }

        return $options;

    }

}
