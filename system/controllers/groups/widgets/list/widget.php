<?php
class widgetGroupsList extends cmsWidget {

    public function run(){

        $model = cmsCore::getModel('groups');
        $model_content = cmsCore::getModel('content');
        $user = cmsUser::getInstance();

        $dataset_id = $this->getOption('dataset_id');
        $fields_is_in_list = (array)$this->getOption('fields_is_in_list', array());

        $current_group = cmsModel::getCachedResult('current_group');

        $fields = $model_content->setTablePrefix('')->orderBy('ordering')->getContentFields('groups');

        if ($dataset_id){

            $dataset = $model_content->getContentDataset($dataset_id);

            if ($dataset){
                $model->applyDatasetFilters($dataset);
            } else {
                $dataset_id = false;
            }

        }

        if($this->getOption('widget_type') == 'related'){
            if($current_group){

                $this->disableCache();

                $model->filterRelated('title', $current_group['title']);

                $model->filterNotEqual('id', $current_group['id']);

            } else {
                return false;
            }
        }

        list($fields, $model) = cmsEventsManager::hook('groups_list_filter', array($fields, $model));

        $model->limit($this->getOption('limit', 10));

        $groups = $model->getGroups();
        if(!$groups){ return false; }

        list($groups, $fields) = cmsEventsManager::hook('groups_before_list', array($groups, $fields));

        // строим массив полей для списка
        if($groups){
            foreach ($groups as $key => $group) {
                foreach($fields as $name => $field){

                    if ($field['is_system'] || !in_array($field['id'], $fields_is_in_list) || !isset($group[$field['name']])) { continue; }
                    if ($field['groups_read'] && !$user->isInGroups($field['groups_read'])) { continue; }
                    if (!$group[$field['name']] && $group[$field['name']] !== '0') { continue; }

                    if (!isset($field['options']['label_in_list'])) {
                        $label_pos = 'none';
                    } else {
                        $label_pos = $field['options']['label_in_list'];
                    }

                    $field_html = $field['handler']->setItem($group)->parseTeaser($group[$field['name']]);
                    if (!$field_html) { continue; }

                    $groups[$key]['fields'][$field['name']] = array(
                        'label_pos' => $label_pos,
                        'type'      => $field['type'],
                        'name'      => $field['name'],
                        'title'     => $field['title'],
                        'html'      => $field_html
                    );

                }
            }
        }

        cmsCore::loadControllerLanguage('groups');

        return array(
            'show_members_count' => $this->getOption('show_members_count', true),
            'fields'            => $fields,
            'fields_is_in_list' => $fields_is_in_list,
            'groups'            => $groups
        );

    }

}
