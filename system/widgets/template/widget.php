<?php
class widgetTemplate extends cmsWidget {

    public $is_cacheable = false;

    public function run(){

        $logos = [];
        $messages = [];
        $template = cmsTemplate::getInstance();
        $config   = cmsConfig::getInstance();

        $type = $this->getOption('type');

        if($type === 'body'){
            if(!$template->isBody() || $template->isBodyDisplayed()){
                return false;
            }
        } elseif($type === 'breadcrumbs') {
            if(!cmsConfig::get('show_breadcrumbs') || !cmsCore::getInstance()->uri || !$template->isBreadcrumbs()){
                return false;
            }
        } elseif($type === 'smessages') {
            $messages = cmsUser::getSessionMessages();
        } elseif($type === 'site_closed') {
            if ($config->is_site_on){
                return false;
            }
        } elseif($type === 'logo') {

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
        } elseif($type === 'lang_select') {
            if(!$config->is_user_change_lang){
                return false;
            }
            $current_lang = cmsCore::getLanguageName();
            $langs = cmsCore::getLanguages();
        }

        return [
            'core'         => cmsCore::getInstance(),
            'config'       => $config,
            'logos'        => $logos,
            'current_lang' => isset($current_lang) ? $current_lang : '',
            'langs'        => isset($langs) ? $langs : [],
            'messages'     => $messages
        ];

    }

}
