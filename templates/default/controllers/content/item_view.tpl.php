<?php

    if (!empty($ctype['seo_keys'])){ $this->setPageKeywords($ctype['seo_keys']); }
    if (!empty($ctype['seo_desc'])){ $this->setPageDescription($ctype['seo_desc']); }
    if (!empty($item['seo_keys'])){ $this->setPageKeywords($item['seo_keys']); }
    if (!empty($item['seo_desc'])){ $this->setPageDescription($item['seo_desc']); }

	$this->addHead('<link rel="canonical" href="'.href_to_abs($ctype['name'], $item['slug'] . '.html').'"/>');
	$this->setPageTitle(!empty($item['seo_title']) ? $item['seo_title'] : $item['title']);

    if ($item['parent_id'] && !empty($ctype['is_in_groups'])){

        $this->addBreadcrumb(LANG_GROUPS, href_to('groups'));
        $this->addBreadcrumb($item['parent_title'], rel_to_href(str_replace('/content/'.$ctype['name'], '', $item['parent_url'])));
        if ($ctype['options']['list_on']){
            $this->addBreadcrumb((empty($ctype['labels']['profile']) ? $ctype['title'] : $ctype['labels']['profile']), rel_to_href($item['parent_url']));
        }

    } else {

        if ($ctype['options']['list_on']){
            $list_header = empty($ctype['labels']['list']) ? $ctype['title'] : $ctype['labels']['list'];
            $this->addBreadcrumb($list_header, href_to($ctype['name']));
            if (isset($item['category'])){
                foreach($item['category']['path'] as $c){
                    $this->addBreadcrumb($c['title'], href_to($ctype['name'], $c['slug']));
                }
            }
        }

    }

    $this->addBreadcrumb($item['title']);

    if($tool_buttons){
        $this->addMenuItems('toolbar', $tool_buttons);
    }

    if (!empty($childs['tabs'])){

        $this->addMenuItem('item-menu', array(
            'title' => string_ucfirst($ctype['labels']['one']),
            'url'   => href_to($ctype['name'], $item['slug'] . '.html')
        ));

        $this->addMenuItems('item-menu', $childs['tabs']);

    }

    $this->renderContentItem($ctype['name'], array(
        'item'         => $item,
        'ctype'        => $ctype,
        'fields'       => $fields,
        'props'        => $props,
        'props_values' => $props_values
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