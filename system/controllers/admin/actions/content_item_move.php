<?php
/**
 * @property \modelBackendContent $model_backend_content
 */
class actionAdminContentItemMove extends cmsAction {

    public function run($ctype_id, $current_id) {

        $items = $this->request->get('selected', []);

        $is_submitted = $this->request->has('items');

        $ctype = $this->model_backend_content->getContentType($ctype_id);
        if (!$ctype) {
            return cmsCore::error404();
        }

        $fields = $this->model_backend_content->getContentFields($ctype['name']);

        $fields = cmsEventsManager::hook('ctype_content_fields', $fields);

        $form = new cmsForm();

        $fieldset_id = $form->addFieldset(LANG_MOVE_TO_CATEGORY);

        $form->addField($fieldset_id,
                new fieldList('category_id', [
                    'default'   => $current_id,
                    'generator' => function ($data) use ($ctype) {
                        $items = [];
                        $tree = $this->model_backend_content->getCategoriesTree($ctype['name']);
                        foreach ($tree as $c) {
                            $items[$c['id']] = str_repeat('- ', $c['ns_level']) . ' ' . $c['title'];
                        }
                        return $items;
                    }
                ])
        );

        $form->addField($fieldset_id, new fieldHidden('items'));

        $data = $form->parse($this->request, $is_submitted);

        if ($is_submitted) {

            // Проверяем правильность заполнения
            $errors = $form->validate($this, $data);

            if (!$errors) {

                $data['items'] = explode(',', $data['items']);

                $this->model_backend_content->moveContentItemsToCategory($ctype, $current_id, $data['category_id'], $data['items'], $fields);

                cmsEventsManager::hook("content_{$ctype['name']}_move_content_items", [$ctype, $fields, $data]);

                return $this->cms_template->renderJSON([
                    'errors'   => false,
                    'callback' => 'contentItemsMoved'
                ]);
            }

            if ($errors) {
                return $this->cms_template->renderJSON([
                    'errors' => true
                ]);
            }
        }

        return $this->cms_template->render('content_item_move', [
            'ctype'     => $ctype,
            'parent_id' => $current_id,
            'items'     => $items,
            'form'      => $form,
            'errors'    => isset($errors) ? $errors : false
        ]);
    }

}
