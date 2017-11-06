<?php
class actionSitemapOptions extends cmsAction {

    public function run(){

        $form = $this->getForm('options');

        if (!$form) { cmsCore::error404(); }

        $is_submitted = $this->request->has('submit');

        $options = cmsController::loadOptions($this->name);

        $source_controllers = cmsEventsManager::hookAll('sitemap_sources');

        if (is_array($source_controllers)){
            foreach($source_controllers as $controller){
                foreach($controller['sources'] as $id => $title){

                    $form->addField('sources', new fieldCheckbox("sources:{$controller['name']}|{$id}", array(
                        'title' => $title
                    )));

                }
            }
        }

        if ($is_submitted){

            $options = $form->parse($this->request, $is_submitted);
            $errors = $form->validate($this, $options);

            if (!$errors){

                cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

                cmsController::saveOptions($this->name, $options);

                $this->redirectToAction('options');

            }

            if ($errors){

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        return cmsTemplate::getInstance()->render('backend/options', array(
            'options' => $options,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
