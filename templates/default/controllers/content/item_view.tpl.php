<?php

    if (!empty($ctype['seo_keys'])){ $this->setPageKeywords($ctype['seo_keys']); }
    if (!empty($ctype['seo_desc'])){ $this->setPageDescription($ctype['seo_desc']); }
    if (!empty($item['seo_keys'])){ $this->setPageKeywords($item['seo_keys']); }
    if (!empty($item['seo_desc'])){ $this->setPageDescription($item['seo_desc']); }

	$seo_title = !empty($item['seo_title']) ? $item['seo_title'] : $item['title'];
	$this->setPageTitle($seo_title);

    $base_url = $ctype['name'];

    if ($item['parent_id'] && !empty($ctype['is_in_groups'])){

        $this->addBreadcrumb(LANG_GROUPS, href_to('groups'));
        $this->addBreadcrumb($item['parent_title'], rel_to_href(str_replace('/content/'.$ctype['name'], '', $item['parent_url'])));
        if ($ctype['options']['list_on']){
            $this->addBreadcrumb((empty($ctype['labels']['profile']) ? $ctype['title'] : $ctype['labels']['profile']), rel_to_href($item['parent_url']));
        }

    } else {

        if ($ctype['options']['list_on']){
            $list_header = empty($ctype['labels']['list']) ? $ctype['title'] : $ctype['labels']['list'];
            $this->addBreadcrumb($list_header, href_to($base_url));
        }

        if (isset($item['category'])){
            foreach($item['category']['path'] as $c){
                $this->addBreadcrumb($c['title'], href_to($base_url, $c['slug']));
            }
        }

    }

    $this->addBreadcrumb($item['title']);

    $user = cmsUser::getInstance();

    if (!$item['is_approved'] && $is_moderator){
        $this->addToolButton(array(
            'class' => 'accept',
            'title' => LANG_MODERATION_APPROVE,
            'href'  => href_to($ctype['name'], 'approve', $item['id'])
        ));
    }

    if ($item['is_approved'] || $is_moderator){

        if ($childs && !empty($childs['to_add'])){
            foreach($childs['to_add'] as $relation){
                $this->addToolButton(array(
                    'class' => 'add',
                    'title' => sprintf(LANG_CONTENT_ADD_ITEM, $relation['child_labels']['create']),
                    'href'  => href_to($relation['child_ctype_name'], 'add') . "?parent_{$ctype['name']}_id={$item['id']}".($item['parent_type']=='group' ? '&group_id='.$item['parent_id'] : '')
                ));
            }
        }
        if ($childs && !empty($childs['to_bind'])){
            foreach($childs['to_bind'] as $relation){
                $this->addToolButton(array(
                    'class' => 'newspaper_add ajax-modal',
                    'title' => sprintf(LANG_CONTENT_BIND_ITEM, $relation['child_labels']['create']),
                    'href'  => href_to($ctype['name'], 'bind_form', array($relation['child_ctype_name'], $item['id']))
                ));
            }
        }
        if ($childs && !empty($childs['to_unbind'])){
            foreach($childs['to_unbind'] as $relation){
                $this->addToolButton(array(
                    'class' => 'newspaper_delete ajax-modal',
                    'title' => sprintf(LANG_CONTENT_UNBIND_ITEM, $relation['child_labels']['create']),
                    'href'  => href_to($ctype['name'], 'bind_form', array($relation['child_ctype_name'], $item['id'], 'unbind'))
                ));
            }
        }

        if (cmsUser::isAllowed($ctype['name'], 'edit', 'all') ||
        (cmsUser::isAllowed($ctype['name'], 'edit', 'own') && $item['user_id'] == $user->id)){
            $this->addToolButton(array(
                'class' => 'edit',
                'title' => sprintf(LANG_CONTENT_EDIT_ITEM, $ctype['labels']['create']),
                'href'  => href_to($ctype['name'], 'edit', $item['id'])
            ));
        }

        $allow_delete = (cmsUser::isAllowed($ctype['name'], 'delete', 'all') ||
            (cmsUser::isAllowed($ctype['name'], 'delete', 'own') && $item['user_id'] == $user->id));
        if ($allow_delete){
            $this->addToolButton(array(
                'class' => 'delete',
                'title' => sprintf(LANG_CONTENT_DELETE_ITEM, $ctype['labels']['create']),
                'href'  => href_to($ctype['name'], 'delete', $item['id']),
                'onclick' => "if(!confirm('".sprintf(LANG_CONTENT_DELETE_ITEM_CONFIRM, $ctype['labels']['create'])."')){ return false; }"
            ));
        }
    }

    if ($item['is_approved'] && !$item['is_deleted']){

        if (cmsUser::isAllowed($ctype['name'], 'move_to_trash', 'all') ||
        (cmsUser::isAllowed($ctype['name'], 'move_to_trash', 'own') && $item['user_id'] == $user->id)){
            $this->addToolButton(array(
                'class' => 'basket_put',
                'title' => ($allow_delete ? LANG_BASKET_DELETE : sprintf(LANG_CONTENT_DELETE_ITEM, $ctype['labels']['create'])),
                'href'  => href_to($ctype['name'], 'trash_put', $item['id']),
                'onclick' => "if(!confirm('".sprintf(LANG_CONTENT_DELETE_ITEM_CONFIRM, $ctype['labels']['create'])."')){ return false; }"
            ));
        }

    }

    if ($item['is_approved'] && $item['is_deleted']){

        if (cmsUser::isAllowed($ctype['name'], 'restore', 'all') ||
        (cmsUser::isAllowed($ctype['name'], 'restore', 'own') && $item['user_id'] == $user->id)){
            $this->addToolButton(array(
                'class' => 'basket_remove',
                'title' => LANG_RESTORE,
                'href'  => href_to($ctype['name'], 'trash_remove', $item['id'])
            ));
        }

    }

    if (!empty($childs['tabs'])){

        $this->addMenuItem('item-menu', array(
            'title' => mb_convert_case($ctype['labels']['one'], MB_CASE_TITLE, 'UTF-8'),
            'url' => href_to($ctype['name'], $item['slug'] . '.html')
        ));

        $this->addMenuItems('item-menu', $childs['tabs']);

    }

    $this->renderContentItem($ctype['name'], array(
        'item' => $item,
        'ctype' => $ctype,
        'fields' => $fields,
        'props' => $props,
        'props_values' => $props_values,
    ));

    if (!empty($childs['lists'])){
        foreach($childs['lists'] as $list){
            if ($list['title']){ ?><h2><?php echo $list['title']; ?></h2><?php }
            echo $list['html'];
        }
    }

?>

<?php if ($item['is_approved'] && $item['approved_by'] && ($user->is_admin || $user->id == $item['user_id'])){ ?>
    <div class="content_moderator_info">
        <?php echo LANG_MODERATION_APPROVED_BY; ?>
        <a href="<?php echo href_to('users', $item['approved_by']['id']); ?>"><?php echo $item['approved_by']['nickname']; ?></a>
        <span class="date"><?php echo html_date_time($item['date_approved']); ?></span>
    </div>
<?php } ?>

<?php if (!empty($item['comments_widget'])){ ?>
    <?php echo $item['comments_widget']; ?>
<?php } ?>