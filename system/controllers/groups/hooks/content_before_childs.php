<?php

class onGroupsContentBeforeChilds extends cmsAction {

    public function run($data){

        list($ctype, $childs, $item) = $data;

        foreach($childs['relations'] as $relation){

            // здесь нам нужны только связи с группами
            if($relation['target_controller'] != $this->name){
                continue;
            }

            $filter =   "r.parent_ctype_id = '{$ctype['id']}' AND ".
                        "r.parent_item_id = '{$item['id']}' AND ".
                        'r.child_ctype_id IS NULL AND '.
                        "r.child_item_id = i.id AND r.target_controller = '{$this->name}'";

            $this->model->joinInner('content_relations_bind', 'r', $filter);

            $count = $this->model->getGroupsCount();

            $is_hide_empty = $relation['options']['is_hide_empty'];

            if (($count || !$is_hide_empty) && $relation['layout'] == 'tab'){

                $childs['tabs'][$relation['child_ctype_name']] = array(
                    'title'       => $relation['title'],
                    'url'         => href_to($ctype['name'], $item['slug'].'/view-'.$relation['child_ctype_name']),
                    'counter'     => $count,
                    'relation_id' => $relation['id'],
                    'ordering'    => $relation['ordering']
                );

            }

            if (!$this->cms_core->request->has('child_ctype_name') && ($count || !$is_hide_empty) && $relation['layout'] == 'list'){

                if (!empty($relation['options']['limit'])){
                    $this->setOption('limit', $relation['options']['limit']);
                }

                if (!empty($relation['options']['is_hide_filter'])){
                    $this->setOption('is_filter', false);
                }

                $childs['lists'][] = array(
                    'title'       => empty($relation['options']['is_hide_title']) ? $relation['title'] : false,
                    'ctype_name'  => $relation['child_ctype_name'],
                    'html'        => $this->renderGroupsList(href_to($ctype['name'], $item['slug'] . '.html')),
                    'relation_id' => $relation['id'],
                    'ordering'    => $relation['ordering']
                );

            }

            $this->model->resetFilters();

        }

        return array($ctype, $childs, $item);

    }

}
