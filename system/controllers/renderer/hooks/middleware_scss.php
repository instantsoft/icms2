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
        $vendors = $this->getDirsList('scss/vendors/');
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
        $controllers = $this->getDirsList('scss/controllers/');
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
        $wysiwygs = $this->getDirsList('scss/wysiwyg/');
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

        // Компилируем секции, если есть
        $sections = $this->getDirsList('scss/sections/', true);
        if($sections){
            foreach ($sections as $section_name => $files) {

                $section_css_data = '';

                foreach ($files as $file_name) {

                    $css_data = $this->compile('scss/sections/'.$section_name.'/'.$file_name, $scss);

                    if($css_data){

                        $section_css_data .= $css_data;

                    } elseif($this->hasCompileMessage()) {
                        cmsUser::addSessionMessage($this->getCompileMessage(), 'error');
                    }
                }

                if ($section_css_data) {

                    $compiled_dir_path = $css_dir_path.'sections/';
                    $compiled_file_path = $compiled_dir_path.$section_name.'.css';

                    if(!is_dir($compiled_dir_path)){
                        mkdir($compiled_dir_path, 0755, true);
                    }

                    if(is_writable($compiled_file_path) || !file_exists($compiled_file_path)){
                        file_put_contents($compiled_file_path, $section_css_data);
                    } else {
                        cmsUser::addSessionMessage(sprintf(LANG_CP_FILE_NOT_WRITABLE, $css_dir.'sections/'.$section_name.'.css'), 'error');
                    }
                }
            }
        }

        $end_time = microtime(true) - $start_time;

        cmsUser::addSessionMessage(sprintf(LANG_CP_COMPILE_TIME, nf($end_time, 2)), 'info');

        return $options;
    }

    public function compile($path, $vars = []) {

        $scss_file = $this->cms_template->getTplFilePath($path);

        $working_dir = dirname(realpath($scss_file));

        $scss = new ScssPhp\ScssPhp\Compiler();

        $scss->addImportPath(function($path) use($working_dir) {

            if (ScssPhp\ScssPhp\Compiler::isCssImport($path)) {
                return null;
            }

            $rel_path = $this->getRelPath($path, $working_dir);

            $scss_file = $this->cms_template->getTplFilePath($rel_path);

            if (!$scss_file || is_dir($scss_file)) {

                $partial = dirname($rel_path) . DIRECTORY_SEPARATOR . '_' . basename($rel_path);

                $scss_file = $this->cms_template->getTplFilePath($partial);
            }

            if (!$scss_file || is_dir($scss_file)) {
                return null;
            }

            return $scss_file;
        });

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

            $source = file_get_contents($scss_file);

            $compilation = $scss->compileString($source);

            $result = $compilation->getCss();

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

    private function getRelPath($path, $working_dir) {

        $has_extension = preg_match('/.s[ac]ss$/', $path);

        if (!$has_extension) {
            $path .= '.scss';
        }

        $tpl_path = str_replace($this->cms_config->root_path . str_replace('/', DIRECTORY_SEPARATOR, cmsTemplate::TEMPLATE_BASE_PATH), '', $working_dir.DIRECTORY_SEPARATOR.$path);

        return $this->getAbsolutePath(ltrim(strstr($tpl_path, DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR));
    }

    private function getAbsolutePath($path) {

        $path      = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        $parts     = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = [];

        foreach ($parts as $part) {
            if ('.' === $part){
                continue;
            }
            if ('..' === $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }

        return implode(DIRECTORY_SEPARATOR, $absolutes);
    }

    private function getDirsList($relative_path, $with_files = false) {

        $list = [];

        foreach ($this->cms_template->getInheritNames() as $name) {

            $file = cmsTemplate::TEMPLATE_BASE_PATH . $name . '/' . $relative_path;

            if (is_readable($this->cms_config->root_path . $file)) {

                $list = array_merge($list, cmsCore::getDirsList($file));
            }
        }

        $list = array_unique($list);

        if ($with_files) {

            $list_files = [];

            foreach ($list as $dir_name) {

                $files = [];

                foreach ($this->cms_template->getInheritNames() as $name) {

                    $file = cmsTemplate::TEMPLATE_BASE_PATH . $name . '/' . $relative_path . '/' . $dir_name;

                    if (is_readable($this->cms_config->root_path . $file)) {

                        $files = array_merge($files, cmsCore::getFilesList($file));
                    }
                }

                $files = array_unique($files);

                $list_files[$dir_name] = $files;
            }

            $list = $list_files;
        }

        return $list;
    }

}
