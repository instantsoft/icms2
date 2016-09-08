<?php

class actionAdminMenuEdit extends cmsAction {

    public function run($id){

        $menu_model = cmsCore::getModel('menu');

        $form = $this->getForm('menu', array('edit'));

        $is_submitted = $this->request->has('submit');

        $menu = $menu_model->getMenu($id);

        if ($menu['is_fixed']){
            $form->removeField('basic', 'name');
        }

        if ($is_submitted){

            $new_menu = $form->parse($this->request, $is_submitted);
            $errors = $form->validate($this, $new_menu);

            if (!$errors){

                $menu_model->updateMenu($id, $new_menu);

                cmsUser::setCookiePublic('menu_tree_path', "{$id}.0");

                if ($menu['name'] !== $new_menu['name']){ // обновление виджетов меню, в которых используется это меню

                    $widgets_model = cmsCore::getModel('widgets');

                    $w_binds = $widgets_model->join('widgets', 'w', 'w.id = i.widget_id')->filterEqual('w.name', 'menu')->get('widgets_bind', function($item, $model){
                        $item['options'] = cmsModel::yamlToArray($item['options']);
                        return $item;
                    });

                    foreach($w_binds as $w_bind){

                        if(isset($w_bind['options']['menu']) && $w_bind['options']['menu'] === $menu['name']){
                            $new = array('options' => $w_bind['options']);
                            $new['options']['menu'] = $new_menu['name'];
                            $widgets_model->updateWidgetBinding($w_bind['id'], $new);
                        }

                    }

                }

                $this->redirectToAction('menu');

            }

            if ($errors){

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

            $menu = $new_menu;

        }

        return cmsTemplate::getInstance()->render('menu_form', array(
            'do' => 'edit',
            'item' => $menu,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
