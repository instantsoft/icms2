<?php
/**
 * @property \modelWall $model
 */
class wall extends cmsFrontend {

    protected $useOptions = true;

    public function getWidget($title, $target, $permissions = []) {

        $page          = $this->request->get('page', 1);
        $show_id       = $this->request->get('wid', 0);
        $go_reply      = $this->request->get('reply', 0);
        $show_reply_id = 0;

        if ($show_id) {

            $this->model->filterEqual('profile_id', $target['profile_id'])->
                    filterEqual('profile_type', $target['profile_type']);

            $entry = $this->model->getEntry($show_id);

            if ($entry) {

                if ($entry['parent_id'] > 0) {
                    $show_id       = $entry['parent_id'];
                    $show_reply_id = $entry['id'];
                }

                $page = $this->model->orderBy($this->options['order_by'], 'desc')->
                        getEntryPageNumber($show_id, $target, $this->options['limit']);
            }
        }

        $this->model->filterEqual('profile_id', $target['profile_id'])->
                filterEqual('parent_id', 0)->
                filterEqual('profile_type', $target['profile_type']);

        $total = $this->model->getEntriesCount();

        $this->model->limitPage($page, $this->options['limit'])->
                orderBy($this->options['order_by'], 'desc');

        $entries = $this->model->getEntries($this->cms_user, $this->getWallEntryActions($permissions));

        $entries = cmsEventsManager::hook('wall_before_list', $entries);

        $editor_params = cmsCore::getController('wysiwygs')->getEditorParams([
            'editor'  => $this->options['editor'],
            'presets' => $this->options['editor_presets']
        ]);

        $editor_params['options']['id'] = 'content';

        return $this->cms_template->renderInternal($this, 'list', [
            'title'         => $title,
            'editor_params' => $editor_params,
            'controller'    => $target['controller'],
            'profile_type'  => $target['profile_type'],
            'profile_id'    => $target['profile_id'],
            'user'          => $this->cms_user,
            'entries'       => $entries,
            'permissions'   => $permissions,
            'page'          => $page,
            'perpage'       => $this->options['limit'],
            'total'         => $total,
            'max_entries'   => $show_id ? 0 : $this->options['show_entries'],
            'show_id'       => $show_id,
            'show_reply_id' => $show_reply_id,
            'go_reply'      => $go_reply
        ]);
    }

    public function getWallEntryActions($permissions = []) {

        $actions = [
            [
                'title'   => LANG_REPLY,
                'icon'    => 'reply',
                'href'    => '#wall-reply',
                'class'   => 'btn-outline-secondary reply mr-2',
                'onclick' => 'return icms.wall.add({id})',
                'handler' => function($entry) use($permissions) {
                    return !empty($permissions['reply']) && $entry['parent_id'] == 0;
                }
            ],
            [
                'hint'    => LANG_EDIT,
                'icon'    => 'edit',
                'href'    => '#wall-edit',
                'class'   => 'btn-outline-secondary edit',
                'onclick' => 'return icms.wall.edit({id})',
                'handler' => function($entry) use($permissions) {
                    return ($entry['user']['id'] == $this->cms_user->id) || $this->cms_user->is_admin;
                }
            ],
            [
                'hint'    => LANG_DELETE,
                'icon'    => 'trash',
                'href'    => '#wall-delete',
                'class'   => 'btn-outline-danger delete',
                'onclick' => 'return icms.wall.remove({id})',
                'handler' => function($entry) use($permissions) {

                    if(isset($permissions['delete'])){
                        return ($entry['user']['id'] == $this->cms_user->id) || !empty($permissions['delete']);
                    }

                    if(isset($permissions['delete_handler'])){
                        return $permissions['delete_handler']($entry);
                    }

                    return false;
                }
            ]
        ];

        list($permissions, $actions) = cmsEventsManager::hook('wall_entry_actions', [$permissions, $actions]);

        return $actions;
    }

}
