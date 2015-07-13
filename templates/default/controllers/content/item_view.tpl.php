<?php

    if (!empty($ctype['seo_keys'])){ $this->setPageKeywords($ctype['seo_keys']); }
    if (!empty($ctype['seo_desc'])){ $this->setPageDescription($ctype['seo_desc']); }
    if (!empty($item['seo_keys'])){ $this->setPageKeywords($item['seo_keys']); }
    if (!empty($item['seo_desc'])){ $this->setPageDescription($item['seo_desc']); }

	$seo_title = !empty($item['seo_title']) ? $item['seo_title'] : $item['title'];
	$this->setPageTitle($seo_title);	
	
    $base_url = $ctype['name'];

    if ($ctype['options']['list_on']){
        $list_header = empty($ctype['labels']['list']) ? $ctype['title'] : $ctype['labels']['list'];
        $this->addBreadcrumb($list_header, href_to($base_url));
    }

    if (isset($item['category'])){
        foreach($item['category']['path'] as $c){
            $this->addBreadcrumb($c['title'], href_to($base_url, $c['slug']));
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
        if (cmsUser::isAllowed($ctype['name'], 'edit', 'all') ||
        (cmsUser::isAllowed($ctype['name'], 'edit', 'own') && $item['user_id'] == $user->id)){
            $this->addToolButton(array(
                'class' => 'edit',
                'title' => sprintf(LANG_CONTENT_EDIT_ITEM, $ctype['labels']['create']),
                'href'  => href_to($ctype['name'], 'edit', $item['id'])
            ));
        }

        if (cmsUser::isAllowed($ctype['name'], 'delete', 'all') ||
        (cmsUser::isAllowed($ctype['name'], 'delete', 'own') && $item['user_id'] == $user->id)){
            $this->addToolButton(array(
                'class' => 'delete',
                'title' => sprintf(LANG_CONTENT_DELETE_ITEM, $ctype['labels']['create']),
                'href'  => href_to($ctype['name'], 'delete', $item['id']),
                'onclick' => "if(!confirm('".sprintf(LANG_CONTENT_DELETE_ITEM_CONFIRM, $ctype['labels']['create'])."')){ return false; }"
            ));
        }
    }

?>

<?php

    $this->renderContentItem($ctype['name'], array(
        'item' => $item,
        'ctype' => $ctype,
        'fields' => $fields,
        'props' => $props,
        'props_values' => $props_values,
    ));

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
