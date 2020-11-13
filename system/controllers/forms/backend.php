<?php

class backendForms extends cmsBackend {

    protected $useOptions = true;

    public $useDefaultOptionsAction = true;

    protected $unknown_action_as_index_param = true;

    public function loadCallback() {
        $this->callbacks = array(
            'actionoptions'=>array(
                function($controller, $options){
                    // Выключаем/включем слушателя
                    if(empty($options['allow_shortcode'])){
                        $is_enabled = 0;
                    } else {
                        $is_enabled = 1;
                    }
                    $this->model->filterEqual('event', 'content_before_item');
                    $this->model->filterEqual('listener', 'forms');
                    $this->model->updateFiltered('events', ['is_enabled' => $is_enabled], true);
                }
            )
        );
    }

    public function getBackendMenu(){
        return array(
            array(
                'title' => LANG_FORMS_CP_FORMS,
                'url' => href_to($this->root_url)
            ),
            array(
                'title' => LANG_OPTIONS,
                'url' => href_to($this->root_url, 'options')
            )
        );
    }

    public function getFormMenu($do = 'add', $id = null){

        $menu = array(

            array(
                'title' => LANG_CP_CTYPE_SETTINGS,
                'url' => href_to($this->root_url, $do, [$id])
            ),
            array(
                'title' => LANG_CP_CTYPE_FIELDS,
                'url' => href_to($this->root_url, 'form_fields', [$id]),
                'disabled' => in_array($do, ['add', 'copy'])
            )
        );

        list($menu, $do, $id) = cmsEventsManager::hook('admin_forms_menu', array($menu, $do, $id));

        return $menu;

    }

    public function validate_unique_field($form_id, $value){

        if (empty($value)) { return true; }
        if (!in_array(gettype($value), array('integer','string','double'))) { return ERR_VALIDATE_INVALID; }

        $this->model->filterEqual('form_id', $form_id);
        $this->model->filterEqual('name', $value);

        $result = $this->model->getCount('forms_fields', 'id', true);
        if ($result) { return ERR_VALIDATE_UNIQUE; }

        return true;
    }

}
