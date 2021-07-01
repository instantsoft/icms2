<?php

class onBootstrap4RenderWidgetMenuMenu extends cmsAction {

	public function run($_data){

        list($widget, $tpl_file, $data) = $_data;

        // Выключен показ лого
        if(empty($widget->options['toggler_show_logo'])){
            return $_data;
        }

        $template = cmsTemplate::getInstance();

        $manifest = $template->getManifest();

        if(empty($manifest['properties']['vendor'])){
            return $_data;
        }

        // Нам нужны только шаблоны на bootstrap4
        if($manifest['properties']['vendor'] !== 'bootstrap4'){
            return $_data;
        }

        $logos = [];
        $config = cmsConfig::getInstance();

        $logos['logo'] = $template->getTemplateFilePath('images/logo.svg');
        $logos['small_logo'] = $template->getTemplateFilePath('images/small_logo.svg');

        // Растр
        $r_logo_file = $template->getOption('logo');
        if ($r_logo_file){
            $logos['logo'] = $config->upload_root . $r_logo_file['original'];
        }
        $r_logo_small_file = $template->getOption('logo_small');
        if ($r_logo_small_file){
            $logos['small_logo'] = $config->upload_root . $r_logo_small_file['original'];
        }
        // SVG
        $logo_svg_file = $template->getOption('logo_svg');
        if ($logo_svg_file){
            $logos['logo'] = $config->upload_root . $logo_svg_file['path'];
        }
        $logo_small_svg_file = $template->getOption('logo_small_svg');
        if ($logo_small_svg_file){
            $logos['small_logo'] = $config->upload_root . $logo_small_svg_file['path'];
        }

        $data['core'] = cmsCore::getInstance();
        $data['logos'] = $logos;

        return [$widget, $tpl_file, $data];
    }

}
