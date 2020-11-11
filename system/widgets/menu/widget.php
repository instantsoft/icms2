<?php
class widgetMenu extends cmsWidget {

	public $is_cacheable = false;

    public function run(){

        $menu_name = $this->getOption('menu');
        if(!$menu_name){ return false; }

        $template = cmsTemplate::getInstance();

        if (!$template->hasMenu($menu_name)) {

            $menu_items = modelMenu::getMenuItemsByName($menu_name);
            if(!$menu_items){ return false; }

            $template->setMenuItems($menu_name, $menu_items);
        }

        return [];
    }

}
