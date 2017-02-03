<?php

class actionModerationDeleteLog extends cmsAction {

    public function run($target_controller = null, $target_subject = null, $target_id = null, $moderator_id = null){

        $model = cmsCore::getModel('content');

        $sub_url      = array();
        $delete_title = '';

        $model->filterEqual('1', 1);

        $action = $this->request->get('action', -1);
        $only_to_delete = $this->request->get('only_to_delete', 0);
        if($action > -1){

            $model->filterEqual('action', $action);

            if($only_to_delete){
                $model->filterNotNull('date_expired');
            }

            $delete_title = string_lang('LANG_MODERATION_ACTION_'.$action);

        }

        if(!empty($target_controller)){

            $model->filterEqual('target_controller', $target_controller);

            $sub_url[] = $target_controller;

            if(!empty($target_subject)){

                if(is_numeric($target_subject) && $target_controller == 'content'){
                    $ctype = $model->getContentType($target_subject);
                    if($ctype){
                        $target_subject = $ctype['name'];
                    }
                } else {
                    $ctype = $model->getContentTypeByName($target_subject);
                }

                $model->filterEqual('target_subject', $target_subject);

                $sub_url[] = $target_subject;

                if($ctype){
                    $delete_title = $ctype['title'];
                }

                if(!empty($target_id)){

                    $model->filterEqual('target_id', $target_id);

                    $sub_url[] = $target_id;

                    $model->lockFilters();

                        $item = $model->getItem('moderators_logs', function ($item, $model){
                            $item['data'] = cmsModel::yamlToArray($item['data']);
                            return $item;
                        });

                        if($item){
                            $delete_title = $item['data']['title'];
                        }

                    $model->unlockFilters();

                }

            }

        }

        if(!empty($moderator_id)){

            $model->filterEqual('moderator_id', $moderator_id);

            if(count($sub_url) == 3){
                $sub_url[] = $moderator_id;
            } elseif(count($sub_url) == 2){
                $sub_url[] = 0;
                $sub_url[] = $moderator_id;
            } elseif(count($sub_url) == 1){
                $sub_url[] = 0;
                $sub_url[] = 0;
                $sub_url[] = $moderator_id;
            } else {
                $sub_url[] = 0;
                $sub_url[] = 0;
                $sub_url[] = 0;
                $sub_url[] = $moderator_id;
            }

            $user = cmsCore::getModel('users')->getuser($moderator_id);

            if($user){
                $delete_title = $user['nickname'];
            }

        }

        $model->deleteFiltered('moderators_logs');

        if($delete_title){
            cmsUser::addSessionMessage(sprintf(LANG_MODERATION_DELETE_CUSTOM, $delete_title), 'success');
        } else {
            cmsUser::addSessionMessage(LANG_MODERATION_DELETE_ALL, 'success');
        }

        $this->redirectToAction('logs');

    }

}
