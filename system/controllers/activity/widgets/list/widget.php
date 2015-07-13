<?php
class widgetActivityList extends cmsWidget {

    public function run(){

        $show_avatars = $this->getOption('show_avatars');
        $show_date_groups = $this->getOption('date_group');
        $limit = $this->getOption('limit', 10);
        $dataset = $this->getOption('dataset', 'all');

        $model = cmsCore::getModel('activity');

        $model->orderBy('date_pub', 'desc');

        if ($dataset != 'all') {

            $datasets = cmsCore::getController('activity')->getDatasets();
            $dataset = $datasets[$dataset];

            if (isset($dataset['filter']) && is_callable($dataset['filter'])){
                $model = $dataset['filter']( $model );
            }

        }

        $items = $model->
                    filterPrivacy()->
                    filterHiddenParents()->
                    limit($limit)->
                    getEntries();

        if (!$items) { return false; }

        return array(
            'show_avatars' => $show_avatars,
            'show_date_groups' => $show_date_groups,
            'items' => $items,
        );

    }

}
