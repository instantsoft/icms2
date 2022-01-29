<?php

class widgetCommentsList extends cmsWidget {

    public $is_cacheable = false;

    public function run() {

        $controller_options = cmsController::loadOptions('comments');

        if (!empty($controller_options['disable_icms_comments'])) {
            return false;
        }

        $show_list    = array_filter($this->getOption('show_list', []));
        $show_avatars = $this->getOption('show_avatars', true);
        $show_text    = $this->getOption('show_text', false);
        $limit        = $this->getOption('limit', 10);

        $model = cmsCore::getModel('comments');

        $model->orderBy('date_pub', 'desc');

        if (!cmsUser::isAllowed('comments', 'view_all')) {
            $model->filterEqual('is_private', 0);
        }

        cmsEventsManager::hook('comments_list_filter', $model);

        if ($show_list) {

            $show_controllers = $show_targets = [];

            foreach ($show_list as $show_target) {
                list($show_controllers[], $show_targets[]) = explode(':', $show_target);
            }

            $model->filterIn('target_controller', $show_controllers);
            $model->filterIn('target_subject', $show_targets);
        }

        $items = $model->filterIsNull('is_deleted')->limit($limit)->getComments();
        if (!$items) {
            return false;
        }

        $items = cmsEventsManager::hook('comments_before_list', $items);

        return [
            'show_rating'  => $this->getOption('show_rating', false),
            'show_avatars' => $show_avatars,
            'show_text'    => $show_text,
            'items'        => $items
        ];
    }

}
