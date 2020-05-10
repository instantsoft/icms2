<?php
class widgetTemplate extends cmsWidget {

    public $is_cacheable = false;

    public function run(){

        $messages = [];
        $template = cmsTemplate::getInstance();

        $type = $this->getOption('type');

        if($type === 'body'){
            if(!$template->isBody()){
                return false;
            }
        } elseif($type === 'breadcrumbs') {
            if(!cmsConfig::get('show_breadcrumbs') || !cmsCore::getInstance()->uri || !$template->isBreadcrumbs()){
                return false;
            }
        } elseif($type === 'smessages') {
            $messages = cmsUser::getSessionMessages();
        } else {
            return false;
        }

        return [
            'messages' => $messages
        ];

    }

}
