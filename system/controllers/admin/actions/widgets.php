<?php

class actionAdminWidgets extends cmsAction {

    public function run($do = false) {

        // если нужно, передаем управление другому экшену
        if ($do){
            $this->runExternalAction('widgets_'.$do, array_slice($this->params, 1));
            return;
        }

        cmsCore::loadAllControllersLanguages();

        $widgets_model = cmsCore::getModel('widgets');

        $controllers = $widgets_model->getPagesControllers();

        $widgets_list = $widgets_model->getAvailableWidgets();

        $tpls = cmsCore::getTemplates();

        $template_name = $this->request->get('template_name', '');

        if(!$template_name || !in_array($template_name, $tpls)){
            $template_name = cmsConfig::get('template');
        }

        cmsCore::loadTemplateLanguage($template_name);

        foreach ($tpls as $tpl) {
            if($this->cms_template->getSchemeHTMLFile($tpl)){
                $templates[$tpl] = $tpl;
            }
        }

        $scheme_html = $this->getSchemeHTML($template_name);

        return $this->cms_template->render('widgets', array(
            'controllers'   => $controllers,
            'template_name' => $template_name,
            'templates'     => $templates,
            'widgets_list'  => $widgets_list,
            'scheme_html'   => $scheme_html
        ));

    }

    public function getSchemeHTML($name=''){

        $template = new cmsTemplate($name);

        $scheme_html = $template->getSchemeHTML();
        if (!$scheme_html) { return false; }

        if (!preg_match_all('/{([a-zA-Z0-9:_\-]+)}/u', $scheme_html, $matches)) { return false; }

        $blocks = $matches[1];

        foreach($blocks as $block){

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

        return $scheme_html;

    }

}
