<?php

class actionAdminCtypesDelete extends cmsAction {

    public function run($id){

        if (!$id) { cmsCore::error404(); }

        if (!cmsForm::validateCSRFToken( $this->request->get('csrf_token', '') )){
            cmsCore::error404();
        }

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($id);

        $ctype = cmsEventsManager::hook('ctype_before_delete', $ctype);

        $content_model->deleteContentType($id);

        cmsEventsManager::hook('ctype_after_delete', $ctype);

        cmsCore::getModel('widgets')->deletePagesByName('content', "{$ctype['name']}.*");

        $binded_widgets = $content_model->get('widgets_bind', function($item, $model){
            $item['options'] = cmsModel::yamlToArray($item['options']);
            return $item;
        });
        if($binded_widgets){
            foreach ($binded_widgets as $widget) {
                if(isset($widget['options']['ctype_id']) && $ctype['id'] == $widget['options']['ctype_id']){
                    $content_model->delete('widgets_bind', $widget['id']);
                }
            }
        }

        cmsCore::getController('activity')->deleteType('content', "add.{$ctype['name']}");

        $this->redirectToAction('ctypes');

    }

}
