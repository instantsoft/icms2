<?php
class widgetTemplate extends cmsWidget {

    public $is_cacheable = false;

    public function run(){

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
        }

        return [
            'core'     => cmsCore::getInstance(),
            'config'   => $config,
            'messages' => $messages
        ];

    }

}
