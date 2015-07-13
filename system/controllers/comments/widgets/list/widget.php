<?php
class widgetCommentsList extends cmsWidget {

    public function run(){

        $show_avatars = $this->getOption('show_avatars', true);
        $show_text = $this->getOption('show_text', false);
        $limit = $this->getOption('limit', 10);

        $model = cmsCore::getModel('comments');

        $model->orderBy('date_pub', 'desc');

        if (!cmsUser::isAllowed('comments', 'view_all')) {
            $model->filterEqual('is_private', 0);
        }

        $items = $model->
                    filterIsNull('is_deleted')->
                    limit($limit)->
                    getComments();

        if (!$items) { return false; }

        return array(
            'show_avatars' => $show_avatars,
            'show_text' => $show_text,
            'items' => $items
        );

    }

}
