<?php
/**
 * @property \modelBackendWidgets $model_backend_widgets
 * @property \modelMenu $model_menu
 */
class actionAdminMenuEdit extends cmsAction {

    public function run($id = false) {

        if (!$id) {
            return cmsCore::error404();
        }

        $form = $this->getForm('menu', ['edit']);

        $menu = $this->model_menu->localizedOff()->getMenu($id);
        if (!$menu) {
            return cmsCore::error404();
        }

        $this->model_menu->localizedRestore();

        if ($menu['is_fixed']) {
            $form->removeField('basic', 'name');
        }

        if ($this->request->has('submit')) {

            $new_menu = $form->parse($this->request, true);
            $errors   = $form->validate($this, $new_menu);

            if (!$errors) {

                $this->model_menu->updateMenu($id, $new_menu);

                cmsUser::setCookiePublic('menu_tree_path', "{$id}.0");

                // обновление виджетов меню, в которых используется это меню
                if (!$menu['is_fixed'] && $menu['name'] !== $new_menu['name']) {

                    $w_binds = $this->model_backend_widgets->
                            join('widgets', 'w', 'w.id = i.widget_id')->
                            filterEqual('w.name', 'menu')->
                            get('widgets_bind', function ($item, $model) {
                        $item['options'] = cmsModel::yamlToArray($item['options']);
                        return $item;
                    });

                    foreach ($w_binds as $w_bind) {

                        if (isset($w_bind['options']['menu']) && $w_bind['options']['menu'] === $menu['name']) {

                            $new = ['options' => $w_bind['options']];
                            $new['options']['menu'] = $new_menu['name'];
                            $this->model_backend_widgets->updateWidgetBinding($w_bind['id'], $new);
                        }
                    }
                }

                cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

                $this->redirectToAction('menu');
            }

            if ($errors) {

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

            $menu = $new_menu;
        }

        return $this->cms_template->render('menu_form', [
            'do'     => 'edit',
            'item'   => $menu,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ]);
    }

}
