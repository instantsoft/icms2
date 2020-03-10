<?php

class actionAdminWidgets extends cmsAction {

    private $is_dynamic_scheme = false;

    public function run($do = false) {

        // если нужно, передаем управление другому экшену
        if ($do){
            $this->runExternalAction('widgets_'.$do, array_slice($this->params, 1));
            return;
        }

        cmsCore::loadAllControllersLanguages();

        $controllers = $this->model_widgets->getPagesControllers();

        $widgets_list = $this->model_widgets->getAvailableWidgets();

        $tpls = cmsCore::getTemplates();

        $template_name = $this->request->get('template_name', '');

        if(!$template_name || !in_array($template_name, $tpls)){
            $template_name = cmsConfig::get('template');
        }

        cmsCore::loadTemplateLanguage($template_name);

        $templates = [];

        foreach ($tpls as $tpl) {
            if(file_exists($this->cms_config->root_path . cmsTemplate::TEMPLATE_BASE_PATH. $tpl .'/main.tpl.php')){
                $templates[$tpl] = $tpl;
            }
        }

        $scheme_html = $this->getSchemeHTML($template_name);

        return $this->cms_template->render('widgets', array(
            'is_dynamic_scheme' => $this->is_dynamic_scheme,
            'controllers'   => $controllers,
            'template_name' => $template_name,
            'templates'     => $templates,
            'widgets_list'  => $widgets_list,
            'scheme_html'   => $scheme_html
        ));

    }

    public function getSchemeHTML($name=''){

        $template = new cmsTemplate($name);

        $template->setContext($this);

        $scheme_html = $template->getSchemeHTML();
        if (!$scheme_html) {
            $scheme_html = $this->getDynamicSchemeHTML($template);
            if (!$scheme_html) { return false; }

            $this->is_dynamic_scheme = true;
        }

        preg_match_all('/{(.+)}/ui', $scheme_html, $matches);

        if(!empty($matches[1])){
            foreach($matches[1] as $block){

                list($type, $value) = explode(':', $block);

                if ($type=='position') {
                    $replace_html = '<ul class="position" rel="'.$value.'" id="pos-'.$value.'"></ul>';
                }

                if ($type=='block') {
                    if (mb_strpos($value, 'LANG_') === 0){ $value = constant($value); }
                    $replace_html = '<div class="block"><span>'.$value.'</span></div>';
                }

                if ($type=='cell') {
                    if (mb_strpos($value, 'LANG_') === 0){ $value = constant($value); }
                    $replace_html = '<div class="cell"><span>'.$value.'</span></div>';
                }

                $scheme_html = str_replace("{{$block}}", $replace_html, $scheme_html);

            }
        }

        return $scheme_html;

    }

    private function getDynamicSchemeHTML($template) {

        $this->cms_template->addTplJSName('admin-scheme');

        $rows = $this->model_widgets->getLayoutRows($template->getName());

        return $template->getRenderedChild('widgets_scheme', array(
            'rows' => $rows
        ));

    }

}
