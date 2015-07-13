<?php

class actionAdminWidgets extends cmsAction {

    public function run($do=false){

        // если нужно, передаем управление другому экшену
        if ($do){
            $this->runAction('widgets_'.$do, array_slice($this->params, 1));
            return;
        }

        cmsCore::loadAllControllersLanguages();

        $widgets_model = cmsCore::getModel('widgets');

        $controllers = $widgets_model->getPagesControllers();
        
        $widgets_list = $widgets_model->getAvailableWidgets();

        $template = cmsTemplate::getInstance();

        $scheme_html = $this->getSchemeHTML();

        return $template->render('widgets', array(
            'controllers' => $controllers,
            'widgets_list' => $widgets_list,
            'scheme_html' => $scheme_html
        ));

    }

    public function getSchemeHTML(){

        $template = cmsTemplate::getInstance();

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
                if (mb_strpos($value, 'LANG_')===0){ $value = constant($value); }
                $replace_html = '<div class="block"><span>'.$value.'</span></div>';
            }

            if ($type=='cell') {
                if (mb_strpos($value, 'LANG_')===0){ $value = constant($value); }
                $replace_html = '<div class="cell"><span>'.$value.'</span></div>';
            }

            $scheme_html = str_replace("{{$block}}", $replace_html, $scheme_html);

        }

        return $scheme_html;

    }

}
