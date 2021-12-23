<?php

class onRendererMiddlewareScss extends cmsAction {

    public $disallow_event_db_register = true;

    private $last_compile_error = false;

    public function run($template_name, $options) {

        $start_time = microtime(true);

        // Путь к CSS файлам
        $css_dir = cmsTemplate::TEMPLATE_BASE_PATH . $template_name.'/css/';
        $css_dir_path = $this->cms_config->root_path . $css_dir;

        // Формируем строку шрифтов
        if(isset($options['font_type'])){ // Совместимость
            if(!$options['font_type']){
                if($options['font_string']){
                    $options['scss']['font-family-sans-serif'] = $options['font_string'];
                }
            }
            if($options['font_type'] == 'gfont'){
                $options['scss']['font-family-sans-serif'] = '"'.str_replace('+', ' ', $options['gfont']).'", sans-serif';
            }
        }

        // Тут все опции SCSS
        $scss = $options['scss'];
        // Заменяем опции, которые формируются из select, но могут быть заданы вручную
        if(!empty($options['custom_scss'])){
            foreach ($options['custom_scss'] as $key => $value) {
                if(!$scss[$key]){
                    $scss[$key] = $value;
                }
            }
        }
        // Кастомные переменные
        if(!empty($options['vars'])){
            foreach ($options['vars'] as $var) {
                $scss['$'.$var['name']] = $var['value'];
            }
        }

        // Компилируем основной CSS файл шаблона
        $theme_css = $this->compile('scss/theme/theme.scss', $scss);
        if($theme_css){

            $compiled_file_path = $css_dir_path.'theme.css';

            if(!is_dir($css_dir_path)){
                mkdir($css_dir_path, 0755, true);
            }

            if(is_writable($compiled_file_path) || !file_exists($compiled_file_path)){
                file_put_contents($compiled_file_path, $theme_css);
            } else {
                cmsUser::addSessionMessage(sprintf(LANG_CP_FILE_NOT_WRITABLE, $css_dir.'theme.css'), 'error');
            }
        } elseif($this->hasCompileMessage()) {
            cmsUser::addSessionMessage($this->getCompileMessage(), 'error');
        }

        // Компилируем вендоры
        $vendors_dir = $this->cms_template->getTplFilePath('scss/vendors/');

        $vendors = cmsCore::getDirsList(str_replace($this->cms_config->root_path, '', $vendors_dir));
        if($vendors){
            foreach ($vendors as $vendor_name) {

                $css_data = $this->compile('scss/vendors/'.$vendor_name.'/build.scss', $scss);
                if($css_data){
                    $compiled_file_path = $css_dir_path.$vendor_name.'.css';
                    if(is_writable($compiled_file_path) || !file_exists($compiled_file_path)){
                        if(!file_exists($compiled_file_path) && !is_writable(dirname($compiled_file_path))){
                            cmsUser::addSessionMessage(sprintf(LANG_CP_INSTALL_NOT_WRITABLE, $css_dir), 'error');
                        } else {
                            file_put_contents($compiled_file_path, $css_data);
                        }
                    } else {
                        cmsUser::addSessionMessage(sprintf(LANG_CP_FILE_NOT_WRITABLE, $css_dir.$vendor_name.'.css'), 'error');
                    }
                } elseif($this->hasCompileMessage()) {
                    cmsUser::addSessionMessage($this->getCompileMessage(), 'error');
                }

            }
        }

        // Компилируем CSS компонентов
        $controllers_dir = $this->cms_template->getTplFilePath('scss/controllers/');

        $controllers = cmsCore::getDirsList(str_replace($this->cms_config->root_path, '', $controllers_dir));
        if($controllers){
            foreach ($controllers as $controller_name) {

                $css_data = $this->compile('scss/controllers/'.$controller_name.'/build.scss', $scss);

                if($css_data){

                    $css_controller_dir = cmsTemplate::TEMPLATE_BASE_PATH . $template_name.'/controllers/'.$controller_name.'/';
                    $css_controller_dir_path = $this->cms_config->root_path . $css_controller_dir;

                    $compiled_file_path = $css_controller_dir_path.'styles.css';

                    if(!is_dir($css_controller_dir_path)){
                        mkdir($css_controller_dir_path, 0755, true);
                    }

                    if(is_writable($compiled_file_path) || !file_exists($compiled_file_path)){
                        file_put_contents($compiled_file_path, $css_data);
                    } else {
                        cmsUser::addSessionMessage(sprintf(LANG_CP_FILE_NOT_WRITABLE, $css_controller_dir.'styles.css'), 'error');
                    }
                } elseif($this->hasCompileMessage()) {
                    cmsUser::addSessionMessage($this->getCompileMessage(), 'error');
                }

            }
        }

        // Компилируем стили wysiwyg
        $wysiwygs_dir = $this->cms_template->getTplFilePath('scss/wysiwyg/');

        $wysiwygs = cmsCore::getDirsList(str_replace($this->cms_config->root_path, '', $wysiwygs_dir));
        if($wysiwygs){
            foreach ($wysiwygs as $wysiwyg_name) {

                $css_data = $this->compile('scss/wysiwyg/'.$wysiwyg_name.'/build.scss', $scss);

                if($css_data){

                    $compiled_dir_path = $css_dir_path.'wysiwyg/'.$wysiwyg_name.'/';
                    $compiled_file_path = $compiled_dir_path.'styles.css';

                    if(!is_dir($compiled_dir_path)){
                        mkdir($compiled_dir_path, 0755, true);
                    }

                    if(is_writable($compiled_file_path) || !file_exists($compiled_file_path)){
                        file_put_contents($compiled_file_path, $css_data);
                    } else {
                        cmsUser::addSessionMessage(sprintf(LANG_CP_FILE_NOT_WRITABLE, $css_dir.'wysiwyg/'.$wysiwyg_name.'/styles.css'), 'error');
                    }
                } elseif($this->hasCompileMessage()) {
                    cmsUser::addSessionMessage($this->getCompileMessage(), 'error');
                }
            }
        }

        $end_time = microtime(true) - $start_time;

        cmsUser::addSessionMessage(sprintf(LANG_CP_COMPILE_TIME, nf($end_time, 2)), 'info');

        return $options;
    }

    public function compile($path, $vars = []) {

        if(!cmsCore::includeFile('system/libs/scssphp/scss.inc.php')){
            return false;
        }

        $scss_file = $this->cms_template->getTplFilePath($path);

        $data = file_get_contents($scss_file);

        $working_dir = dirname(realpath($scss_file));

        $scss_file_name = basename($scss_file);

        chdir($working_dir);

        $scss = new ScssPhp\ScssPhp\Compiler();

        $scss->setOutputStyle(ScssPhp\ScssPhp\OutputStyle::COMPRESSED);

        if($vars){

            $_vars = [];

            foreach ($vars as $key => $value) {
                if(!$value){
                    $_vars[$key] = 'false'; continue;
                }
                if($value === 1){
                    $_vars[$key] = 'true'; continue;
                }
                $_vars[$key] = $value;
            }

            $scss->addVariables($_vars);
        }

        $result = false;

        try {
            $result = $scss->compile($data, $scss_file_name);
        } catch (Exception $exc) {
            $this->last_compile_error = $exc->getMessage();
        }

        return $result;
    }

    public function hasCompileMessage() {
        return $this->last_compile_error ? true : false;
    }

    public function getCompileMessage() {

        $msg = $this->last_compile_error;
        $this->last_compile_error = false;

        return $msg;
    }

}
