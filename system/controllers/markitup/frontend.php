<?php
class markitup extends cmsFrontend {

    protected $useOptions = true;

    public function getEditorWidget($field_id, $content='', $options=array()){

        if ($this->request->isInternal()){
            if ($this->useOptions){
                $this->options = $this->getOptions();
            }
        }

        $this->options['id'] = 'editor'.time().'_'.str_replace(array('[',']'), array('_', ''), $field_id);

        return $this->cms_template->renderInternal($this, 'widget', array(
            'field_id' => $field_id,
            'content'  => $content,
            'options'  => array_merge($this->options, $options)
        ));

    }

    public function actionUpload(){

        if (!cmsUser::isLogged()) {
            return $this->cms_template->renderJSON(array(
                'status' => 'error',
                'msg'    => 'auth error'
            ));
        }

		$result = cmsCore::getController('images')->uploadWithPreset('inline_upload_file', 'wysiwyg_markitup');

        if (!$result['success']){

            return $this->cms_template->renderJSON(array(
                'status' => 'error',
                'msg'    => $result['error']
            ));

        }

        return $this->cms_template->renderJSON(array(
            'status' => 'success',
            'src' => $result['image']['url']
        ));

    }

}
