<?php

class actionAdminSettingsMime extends cmsAction {

    public function run() {

        $mime = new cmsMimetypes();

        $data = $this->getConfigMimeTypes($mime);

        $form = $this->getForm('settings_mime');

        if ($this->request->has('submit')) {

            $data = $form->parse($this->request, true);

            $errors = $form->validate($this, $data);

            if (!$errors) {

                $data = cmsEventsManager::hook('mime_types_before_update', $data);

                array_multisort($data['mimetypes']);

                $result = $mime->save($data['mimetypes']);

                if (!$result) {

                    $errors = [];

                    cmsUser::addSessionMessage(sprintf(LANG_CP_FILE_NOT_WRITABLE, $mime->getFilePath()), 'error');

                } else {

                    cmsEventsManager::hook('mime_types_after_update', $data);

                    cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

                    return $this->redirectToAction('settings', ['mime']);
                }
            } else {

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render([
            'data'   => $data,
            'form'   => $form,
            'errors' => $errors ?? false
        ]);
    }

    private function getConfigMimeTypes($mime) {

        $data = ['mimetypes' => []];

        $values = $mime->getConfig();

        foreach ($values as $key => $value) {

            $data['mimetypes'][] = [
                'extension' => $key,
                'mimes' => is_array($value) ? implode("\n", $value) : $value
            ];
        }

        return $data;
    }

}
