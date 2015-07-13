<?php
class markitup extends cmsFrontend {

    protected $useOptions = true;

//============================================================================//
//============================================================================//

    public function getEditorWidget($field_id, $content='', $options=array()){

        if ($this->request->isInternal()){
            if ($this->useOptions){
               $this->options = $this->getOptions();
            }
        }

        $this->options['id'] = 'editor'.time().'_'.$field_id;

        $options = array_merge($this->options, $options);

        $template = cmsTemplate::getInstance();

        return $template->renderInternal($this, 'widget', array(
            'field_id' => $field_id,
            'content' => $content,
            'options' => $options
        ));

    }

//============================================================================//
//============================================================================//

    public function actionUpload(){
		
		$images_controller = cmsCore::getController('images');

		$result = $images_controller->uploadWithPreset('inline_upload_file', 'wysiwyg_markitup');
		
        if (!$result['success']){

            cmsTemplate::getInstance()->renderJSON(array(
                'status' => 'error',
                'msg' => $result['error']
            ));

            $this->halt();

        }

        cmsTemplate::getInstance()->renderJSON(array(
            'status' => 'success',
            'src' => $result['image']['url']
        ));

        $this->halt();

    }

//============================================================================//
//============================================================================//

}
