<?php

class backendWysiwygs extends cmsBackend {

    public function actionIndex(){
        $this->redirectToAction('presets');
    }

    public function getWysiwygOptionsForm($wysiwyg_name, $form_params) {

        $form_file = 'wysiwyg/'.$wysiwyg_name.'/options.php';

        $form_file = $this->cms_config->root_path . $form_file;
        $form_name = 'wysiwyg_'.$wysiwyg_name.'_options';

        $context_controller = null;

        if(cmsCore::isControllerExists($wysiwyg_name)){
            $context_controller = cmsCore::getController($wysiwyg_name, $this->request);
        } else {
            cmsCore::loadControllerLanguage($wysiwyg_name);
        }

        $form = cmsForm::getForm($form_file, $form_name, $form_params, $context_controller);

        if($form === false){
            return cmsCore::error(ERR_FILE_NOT_FOUND . ': '. str_replace(PATH, '', $form_file));
        }

        if(is_string($form)){
            return cmsCore::error($form);
        }

        list($form, $form_params) = cmsEventsManager::hook('form_'.$form_name, array($form, $form_params));

        return $form;

    }

}
