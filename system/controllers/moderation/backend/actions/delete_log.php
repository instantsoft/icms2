<?php

class actionModerationDeleteLog extends cmsAction {

    public function run($target_controller = null, $target_subject = null, $target_id = null, $moderator_id = null) {

        $model = cmsCore::getModel('content');

        $delete_title = '';

        $model->filter('1=1');

        $action         = $this->request->get('action', -1);
        $only_to_delete = $this->request->get('only_to_delete', 0);

        if ($action > -1) {

            $model->filterEqual('action', $action);

            if ($only_to_delete) {
                $model->filterNotNull('date_expired');
            }

            $delete_title = string_lang('LANG_MODERATION_ACTION_' . $action);
        }

        if ($target_controller) {

            $model->filterEqual('target_controller', $target_controller);

            if ($target_subject) {

                if (is_numeric($target_subject) && $target_controller === 'content') {

                    $ctype = $model->getContentType($target_subject);

                    if ($ctype) {
                        $target_subject = $ctype['name'];
                    }

                } else {

                    $ctype = $model->getContentTypeByName($target_subject);
                }

                $model->filterEqual('target_subject', $target_subject);

                if ($ctype) {
                    $delete_title = $ctype['title'];
                }

                if ($target_id) {

                    $model->filterEqual('target_id', $target_id);

                    $model->lockFilters();

                    $item = $model->getItem('moderators_logs', function ($item, $model) {
                        $item['data'] = cmsModel::yamlToArray($item['data']);
                        return $item;
                    });

                    if (!empty($item['data']['title'])) {
                        $delete_title = $item['data']['title'];
                    }

                    $model->unlockFilters();
                }
            }
        }

        if ($moderator_id) {

            $model->filterEqual('moderator_id', $moderator_id);

            $user = cmsCore::getModel('users')->getUser($moderator_id);

            if ($user) {
                $delete_title = $user['nickname'];
            }
        }

        $model->deleteFiltered('moderators_logs');

        if ($delete_title) {
            cmsUser::addSessionMessage(sprintf(LANG_MODERATION_DELETE_CUSTOM, $delete_title), 'success');
        } else {
            cmsUser::addSessionMessage(LANG_MODERATION_DELETE_ALL, 'success');
        }

        return $this->redirectToAction('logs');
    }

}
