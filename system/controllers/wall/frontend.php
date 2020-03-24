<?php

class wall extends cmsFrontend {

    protected $useOptions = true;

    public function getWidget($title, $target, $permissions = array()){

        $page          = $this->request->get('page', 1);
        $show_id       = $this->request->get('wid', 0);
        $go_reply      = $this->request->get('reply', 0);
        $show_reply_id = 0;

        if ($show_id){

            $entry = $this->model->getEntry($show_id);

            if ($entry){

                if ($entry['parent_id'] > 0) {
                    $show_id       = $entry['parent_id'];
                    $show_reply_id = $entry['id'];
                }

                $page = $this->model->getEntryPageNumber($show_id, $target, $this->options['limit']);

            }

        }

        $this->model->filterEqual('profile_id', $target['profile_id'])->
                filterEqual('parent_id', 0)->
                filterEqual('profile_type', $target['profile_type']);

        $total = $this->model->getEntriesCount();

        $this->model->limitPage($page, $this->options['limit'])->
                orderBy($this->options['order_by'], 'desc');

        $entries = $this->model->getEntries($this->cms_user);

        $entries = cmsEventsManager::hook('wall_before_list', $entries);

        $editor_params = cmsCore::getController('wysiwygs')->getEditorParams([
            'editor'  => $this->options['editor'],
            'presets' => $this->options['editor_presets']
        ]);

        return $this->cms_template->renderInternal($this, 'list', array(
            'title'           => $title,
            'editor_params'   => $editor_params,
            'controller'      => $target['controller'],
            'profile_type'    => $target['profile_type'],
            'profile_id'      => $target['profile_id'],
            'user'            => $this->cms_user,
            'entries'         => $entries,
            'permissions'     => $permissions,
            'page'            => $page,
            'perpage'         => $this->options['limit'],
            'total'           => $total,
            'max_entries'     => $show_id ? 0 : $this->options['show_entries'],
            'show_id'         => $show_id,
            'show_reply_id'   => $show_reply_id,
            'go_reply'        => $go_reply
        ));

    }

}
