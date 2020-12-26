<?php

class onBootstrap4CtypeFieldUsersAfterUpdate extends cmsAction {

	public function run($data){

        $template = new cmsTemplate($this->cms_config->template);

        $manifest = $template->getManifest();
        if(empty($manifest['properties']['style_middleware'])){
            return $data;
        }

        list($field, $model) = $data;

        if($field['name'] == 'avatar'){

            $list_preset_name = $field['options']['size_teaser'];

            $preset = $this->model_images->getPresetByName($list_preset_name);

            if($preset){

                $avatar_inlist_size = $preset['width'].'px';

                $template_options = $template->getOptions();

                // Устанавливаем новый размер
                $template_options['scss']['avatar-inlist-size'] = $avatar_inlist_size;

                // Запоминаем в конфиге шаблона
                $template->saveOptions($template_options);

                // Перекомпилируем CSS
                cmsCore::getController('renderer', new cmsRequest([
                    'middleware' => $manifest['properties']['style_middleware']
                ]), cmsRequest::CTX_INTERNAL)->render($template->getName(), $template_options);

            }
        }

        return [$field, $model];
    }

}
