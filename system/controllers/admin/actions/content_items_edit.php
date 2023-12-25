<?php
/**
 * @property \modelBackendContent $model_backend_content
 */
class actionAdminContentItemsEdit extends cmsAction {

    public function run($ctype_id){

        $ctype = $this->model_backend_content->getContentType($ctype_id);
        if (!$ctype) { return cmsCore::error404(); }

        $items = $this->request->get('selected', []);
        if (!$items) { cmsCore::error404(); }

        $fields = $this->model_backend_content->orderBy('ordering')->getContentFields($ctype['name']);

        $form = $this->controller_content->getItemForm($ctype, $fields, 'add');

        // Форма выбора полей для обновления
        $form_select = new cmsForm();

        // Исключаемые поля
        $excluded_fields = [
            'new_category', 'new_folder'
        ];
        $excluded_fields_types = [
            'parent'
        ];
        // Типы полей, для которых можно добавлять значения
        $add_field_types = [
            'caption', 'html', 'string', 'text'
        ];

        $structure = $form->getStructure();

        $form_select->addFieldset(LANG_BASIC_OPTIONS, 'default_fields');

        $form_select->addField('default_fields',
            new fieldHidden('selected_submit', array(
                    'default' => 1
                )
            )
        );

        foreach ($items as $key => $item_id) {
            $form_select->addField('default_fields',
                new fieldHidden('selected:'.$key, array(
                        'default' => $item_id
                    )
                )
            );
        }
        foreach($structure as $key => $fieldset){

            if(empty($fieldset['childs'])){
                continue;
            }

            $key = $fieldset['title'] ? $form_select->addFieldset($fieldset['title'], $key) : 'default_fields';

            foreach ($fieldset['childs'] as $name => $field) {

                if(in_array($field->field_type, $excluded_fields_types)){ continue; }
                if(in_array($name, $excluded_fields)){ continue; }

                $fs_key = $key;

                if(!$field->element_title){
                    $fs_key = 'default_fields';
                }

                $form_select->addField($fs_key,
                    new fieldCheckbox('fields:'.$name, array(
                            'title' => $field->element_title ? $field->element_title : $fieldset['title']
                        )
                    )
                );

                if(in_array($field->field_type, $add_field_types)){
                    $form_select->addField($fs_key,
                        new fieldCheckbox('fields_is_add:'.$name, array(
                                'title' => LANG_CP_CONTENT_FIELDS_IS_ADD,
                                'visible_depend' => array('fields:'.$name => array('show' => array('1')))
                            )
                        )
                    );
                }

            }

        }

        $form_select->addFieldset(LANG_AUTHOR, 'edit_user_id');
        $form_select->addField('edit_user_id',
            new fieldCheckbox('fields:user_id', array(
                    'title' => LANG_CHANGE
                )
            )
        );

        // Ловим, какие поля выбрали для изменения
        if ($this->request->has('selected_submit')){

            $selected_submit = $form_select->parse($this->request, true);

            $errors = $form_select->validate($this, $selected_submit);

            $is_empty_fields = array_filter($selected_submit['fields']);

            if(!$is_empty_fields){
                $errors = true;
            }

            if (!$errors){

                // строим форму из выбранных полей
                $form_fields = new cmsForm();

                $fid = $form_fields->addFieldset();

                foreach ($selected_submit['fields'] as $fname => $fvalue) {
                    $form_fields->addField($fid,
                        new fieldHidden('fields:'.$fname, array(
                                'default' => $fvalue
                            )
                        )
                    );
                }

                if(!empty($selected_submit['fields_is_add'])){
                    foreach ($selected_submit['fields_is_add'] as $fname => $fvalue) {
                        $form_fields->addField($fid,
                            new fieldHidden('fields_is_add:'.$fname, array(
                                    'default' => $fvalue
                                )
                            )
                        );
                    }
                }

                foreach ($items as $key => $item_id) {
                    $form_fields->addField($fid,
                        new fieldHidden('selected:'.$key, array(
                                'default' => $item_id
                            )
                        )
                    );
                }

                $form_fields->addField($fid,
                    new fieldHidden('selected_save', array(
                            'default' => 1
                        )
                    )
                );

                $form_fields->addField($fid,
                    new fieldHidden('selected_submit', array(
                            'default' => 1
                        )
                    )
                );

                foreach($structure as $key => $fieldset){
                    foreach ($fieldset['childs'] as $name => $field) {

                        if(empty($selected_submit['fields'][$name])){ continue; }

                        if(!$field->element_title){
                            $field->element_title = $fieldset['title'];
                        }

                        if(!empty($selected_submit['fields_is_add'][$field->getName()])){
                            $field->hint = (isset($field->hint) ? $field->hint.'<br>' : '').LANG_CP_CONTENT_ITEMS_EDIT_ADD_HINT;
                        }

                        $field->setName('content_fields:'.$field->getName());

                        $form_fields->addField($fid, $field);

                    }
                }

                // Поле смены автора
                if(!empty($selected_submit['fields']['user_id'])){
                    $form_fields->addField($fid, new fieldString('content_fields:user_id', [
                        'title' => LANG_AUTHOR.' (ID)'
                    ]));
                }

                // непосредственно меняем для выделенных записей нужные значения
                if ($this->request->has('selected_save')){

                    $data = $form_fields->parse($this->request, true);

                    $errors = $form_fields->validate($this, $data);

                    if (!$errors){

                        $fields_is_add = array_filter($data['fields_is_add']);

                        $_items = $this->getContentItems($ctype['name'], $items);

                        foreach ($items as $item_id) {

                            $_items[$item_id]['ctype_name'] = $ctype['name'];
                            $_items[$item_id]['ctype_id']   = $ctype['id'];
                            $_items[$item_id]['ctype_data'] = $ctype;

                            $content_fields = $data['content_fields'];

                            if($fields_is_add){
                                foreach ($fields_is_add as $fname => $flag) {
                                    $content_fields[$fname] = $_items[$item_id][$fname].$content_fields[$fname];
                                }
                            }
                            
                            //Доп категории       
                            $_items[$item_id]['add_cats'] = $this->model_content->getContentItemCategories($ctype['name'], $item_id);
                            if ($_items[$item_id]['add_cats']) {
                                foreach ($_items[$item_id]['add_cats'] as $index => $cat_id) {
                                    if ($cat_id == $_items[$item_id]['category_id']) {
                                        unset($_items[$item_id]['add_cats'][$index]);
                                        break;
                                    }
                                }
                            }
                            
                            $this->model_content->updateContentItem($ctype, $item_id, array_merge($_items[$item_id], $content_fields), $fields);

                        }

                        cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

                        return $this->cms_template->renderJSON(array(
                            'errors' => false,
                            'location' => href_to('admin', 'content', array($ctype['id'])),
                            'callback' => 'contentItemsEditSelectedSaved'
                        ));

                    }

                    if ($errors){
                        return $this->cms_template->renderJSON(array(
                            'errors' => $errors
                        ));
                    }

                }

                return $this->cms_template->renderJSON(array(
                    'errors' => false,
                    'html' => $this->cms_template->render('content_items_edit_save', [
                        'ctype'     => $ctype,
                        'items'     => $items,
                        'form'      => $form_fields,
                        'errors'    => isset($errors) ? $errors : false
                    ], new cmsRequest($this->request->getData(), cmsRequest::CTX_INTERNAL)),
                    'callback' => 'contentItemsEditSelected'
                ));

            }

            if ($errors){
                return $this->cms_template->renderJSON(array(
                    'errors' => $errors
                ));
            }

        }

        return $this->cms_template->render([
            'ctype'     => $ctype,
            'items'     => $items,
            'form'      => $form_select,
            'errors'    => isset($errors) ? $errors : false
        ]);
    }

    public function getContentItems($ctype_name, $items){

        $table_name = $this->model_backend_content->table_prefix . $ctype_name;

        $this->model_backend_content->filterIn('id', $items);

        return $this->model_backend_content->get($table_name);
    }

}
