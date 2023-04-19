<?php

class backendForms extends cmsBackend {

    protected $useOptions = true;

    public $useDefaultOptionsAction = true;

    protected $unknown_action_as_index_param = true;

    public function __construct(cmsRequest $request) {

        parent::__construct($request);

        $this->addEventListener('controller_save_options', function($controller, $options){

            // Выключаем/включем слушателя
            if(empty($options['allow_shortcode'])){
                $is_enabled = 0;
            } else {
                $is_enabled = 1;
            }

            $this->model->filterEqual('event', 'content_before_item');
            $this->model->filterEqual('listener', 'forms');
            $this->model->updateFiltered('events', ['is_enabled' => $is_enabled], true);
        });
    }

    public function getBackendMenu() {
        return [
            [
                'title' => LANG_FORMS_CP_FORMS,
                'url'   => href_to($this->root_url),
                'options' => [
                    'icon' => 'list'
                ]
            ],
            [
                'title' => LANG_OPTIONS,
                'url'   => href_to($this->root_url, 'options'),
                'options' => [
                    'icon' => 'cog'
                ]
            ]
        ];
    }

    public function getFormMenu($do = 'add', $id = null) {

        $menu = [
            [
                'title' => LANG_CP_CTYPE_SETTINGS,
                'url'   => href_to($this->root_url, $do, [$id])
            ],
            [
                'title'    => LANG_CP_CTYPE_FIELDS,
                'url'      => href_to($this->root_url, 'form_fields', [$id]),
                'disabled' => in_array($do, ['add', 'copy'])
            ]
        ];

        list($menu, $do, $id) = cmsEventsManager::hook('admin_forms_menu', [$menu, $do, $id]);

        return $menu;
    }

    public function addFieldOptionsToForm(cmsForm $form) {

        $field_type  = $this->request->get('type', '');
        $field_class = 'field' . string_to_camel('_', $field_type);

        if (!class_exists($field_class)) {
            return cmsCore::error(ERR_CLASS_NOT_FOUND);
        }

        $field_object = new $field_class(null, [
            'subject_name' => 'forms'
        ]);

        $field_options = $field_object->getOptions();

        $form->mergeForm($this->makeForm(function($form) use($field_options){

            $form->addFieldset(LANG_CP_FIELD_TYPE_OPTS, 'field_settings');

            foreach ($field_options as $field_field) {

                $field_field->setName("options:{$field_field->name}");

                $form->addField('field_settings', $field_field);
            }

            return $form;
        }));

        return $form;
    }

    public function validate_unique_field($form_id, $value){

        if (empty($value)) { return true; }
        if (!in_array(gettype($value), ['integer','string','double'])) { return ERR_VALIDATE_INVALID; }

        $this->model->filterEqual('form_id', $form_id);
        $this->model->filterEqual('name', $value);

        $result = $this->model->getCount('forms_fields', 'id', true);
        if ($result) { return ERR_VALIDATE_UNIQUE; }

        return true;
    }

}
