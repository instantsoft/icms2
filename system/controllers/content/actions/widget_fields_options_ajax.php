<?php

class actionContentWidgetFieldsOptionsAjax extends cmsAction {

    public function run(){

        if (!$this->request->isAjax() || !cmsUser::isAdmin()) {
            return cmsCore::error404();
        }

        $ctype_id = $this->request->get('value', 0);
        if(!$ctype_id){ return $this->halt(); }

        $form_id = $this->request->get('form_id', '');

		$ctype = $this->model->getContentType($ctype_id);
		if (!$ctype) { return $this->halt(); }

        cmsCore::loadWidgetLanguage('list', 'content');

		$fields = $this->model->getContentFields($ctype['name']);

        $fields = cmsEventsManager::hook('ctype_content_fields', $fields);

        $form = $this->getForm('widget_content_list', [$ctype, $fields]);

        ob_start();

        $this->cms_template->renderForm($form, [], [
            'only_fields' => true,
            'form_id' => $form_id,
            'form_tpl_file' => 'form_fields'
        ]);

        return die(ob_get_clean());
    }

}
