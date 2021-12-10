<?php

class actionAdminSettingsThemeIconList extends cmsAction {

    public function run($template_name) {

        $template = new cmsTemplate($template_name);

        $icon_list = $template->getIconList();

        return $this->cms_template->render([
            'template_name' => $template_name,
            'icon_list' => $icon_list,
            'is_exists_list' => $icon_list ? true : false
        ]);
    }

}
