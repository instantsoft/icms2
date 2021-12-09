<?php

class actionAdminSettingsThemeIconList extends cmsAction {

    public function run($template_name) {

        $template = new cmsTemplate($template_name);

        $file_path = $template->path . '/icon_list.php';

        $is_exists_list = file_exists($file_path);

        if($is_exists_list){
            $icon_list = include $file_path;
        } else {
            $icon_list = [];
        }

        return $this->cms_template->render([
            'template_name' => $template_name,
            'icon_list' => $icon_list,
            'is_exists_list' => $is_exists_list
        ]);
    }

}
