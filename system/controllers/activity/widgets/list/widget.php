<?php
class widgetActivityList extends cmsWidget {

    public function run(){

        $show_avatars     = $this->getOption('show_avatars');
        $show_date_groups = $this->getOption('date_group');
        $limit            = $this->getOption('limit', 10);
        $dataset          = $this->getOption('dataset', 'all');

        $activity = cmsCore::getController('activity');

        $activity->model->orderBy('date_pub', 'desc');

        if ($dataset != 'all') {

            $datasets = $activity->getDatasets();
            $dataset = $datasets[$dataset];

            if (isset($dataset['filter']) && is_callable($dataset['filter'])){
                $dataset['filter']($activity->model);
            }

        }

        $activity->model->filterPrivacy()->enableHiddenParentsFilter()->filterEqual('is_pub', 1);

        cmsEventsManager::hook('activity_list_filter', $activity->model);

        $items = $activity->model->limit($limit)->getEntries();
        if (!$items) { return false; }

        return array(
            'show_avatars'     => $show_avatars,
            'show_date_groups' => $show_date_groups,
            'items'            => cmsEventsManager::hook('activity_before_list', $items)
        );

    }

}
