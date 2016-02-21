<?php

class actionAdminContentItemMove extends cmsAction {

    public function run($ctype_id, $parent_id){

        $items = $this->request->get('selected');
        $is_submitted = $this->request->has('items');

        $template = cmsTemplate::getInstance();

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($ctype_id);

		$fields = $content_model->getContentFields($ctype['name']);

        $form = new cmsForm();

        $fieldset_id = $form->addFieldset(LANG_MOVE_TO_CATEGORY);

        $form->addField($fieldset_id,
            new fieldList('category_id', array(
                    'default' => $parent_id,
                    'generator' => function($data){
                        $content_model = cmsCore::getModel('content');
                        $tree = $content_model->getCategoriesTree($data['ctype_name']);
                        foreach($tree as $c){
                            $items[$c['id']] = str_repeat('- ', $c['ns_level']).' '.$c['title'];
                        }
                        return $items;
                    }
                )
            )
        );

        $form->addField($fieldset_id,
            new fieldHidden('items')
        );

        $data = $form->parse($this->request, $is_submitted);

        if ($is_submitted){

            // Проверяем правильность заполнения
            $errors = $form->validate($this,  $data);

            if (!$errors){

                $data['items'] = explode(',', $data['items']);
                $content_model->moveContentItemsToCategory($ctype, $data['category_id'], $data['items'], $fields);

                cmsEventsManager::hook("content_{$ctype['name']}_move_content_items", array($ctype, $fields, $data));

                $template->renderJSON(array(
                    'errors' => false,
                    'callback' => 'contentItemsMoved'
                ));

            }

            if ($errors){
                $template->renderJSON(array(
                    'errors' => true,
                ));
            }

            $this->halt();

        }

        return $template->render('content_item_move', array(
            'ctype' => $ctype,
            'parent_id' => $parent_id,
            'items' => $items,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
