<?php

    $this->addJS( $this->getJavascriptFileName('photos') );

    $this->setPageTitle($photo['title']);

    $user = cmsUser::getInstance();

    if ($ctype['options']['list_on']){
        $this->addBreadcrumb($ctype['title'], href_to($ctype['name']));
    }

    if (isset($album['category'])){
        foreach($album['category']['path'] as $c){
            $this->addBreadcrumb($c['title'], href_to($ctype['name'], $c['slug']));
        }
    }

    if ($ctype['options']['item_on']){
        $this->addBreadcrumb($album['title'], href_to($ctype['name'], $album['slug']) . '.html');
    }

	$is_can_edit =  (cmsUser::isAllowed($ctype['name'], 'edit', 'all') ||
					(cmsUser::isAllowed($ctype['name'], 'edit', 'own') && $album['user_id'] == $user->id) ||
					($photo['user_id'] == $user->id));
	$is_can_delete =	(cmsUser::isAllowed($ctype['name'], 'delete', 'all') ||
						(cmsUser::isAllowed($ctype['name'], 'delete', 'own') && $album['user_id'] == $user->id) ||
						($photo['user_id'] == $user->id));

	if ($is_can_edit){
        $this->addToolButton(array(
            'class' => 'edit',
            'title' => LANG_PHOTOS_RENAME_PHOTO,
            'href'  => 'javascript:icms.photos.rename()'
        ));
   }

    if ($is_can_delete){
        $this->addToolButton(array(
            'class' => 'delete',
            'title' => LANG_PHOTOS_DELETE_PHOTO,
            'href'  => 'javascript:icms.photos.delete()',
            'onclick' => "if(!confirm('".LANG_PHOTOS_DELETE_PHOTO_CONFIRM."')){ return false; }"
        ));
   }

    $this->addBreadcrumb($photo['title']);

    $photos_ids = array_keys($photos);
    $curr_photo_pos = array_search($photo['id'], $photos_ids);

    if ($curr_photo_pos == count($photos)-1) {
        $next_photo_id = $photos_ids[0];
    } else {
        $next_photo_id = $photos_ids[$curr_photo_pos+1];
    }

    $next_photo_url = $this->href_to('view', $next_photo_id);

?>

<h1><?php html($photo['title']); ?></h1>

<div id="album-photo-item" class="content_item" data-title="<?php html($photo['title']); ?>" data-id="<?php html($photo['id']); ?>">

    <div class="image">
        <a href="<?php echo $next_photo_url; ?>">
            <?php echo html_image($photo['image'], 'big', $photo['title']); ?>
        </a>
    </div>

	<div class="image-nav">
		<?php if ($is_origs && isset($photo['image']['original'])) { ?>
			<a href="<?php echo html_image_src($photo['image'], 'original', true); ?>" target="_blank" class="ajax-modal ajaxlink"><?php echo LANG_PHOTOS_SHOW_ORIG; ?></a>
		<?php } ?>
	</div>

    <div id="album-nav">
        <div class="arrow arr-prev"><a href="javascript:"></a></div>
        <div id="photos-slider">
            <ul>
                <?php $index = 0; ?>
                <?php foreach($photos as $thumb) { ?>
                    <li <?php if ($thumb['id'] == $photo['id']) { ?>class="active"<?php } ?>>
                        <a href="<?php echo $this->href_to('view', $thumb['id']); ?>" title="<?php html($thumb['title']); ?>">
                            <?php echo html_image($thumb['image'], 'small', $thumb['title']); ?>
                        </a>
                    </li>
                    <?php if ($thumb['id'] == $photo['id']) { $active_index = $index; } else { $index++; } ?>
                <?php } ?>
            </ul>
        </div>
        <div class="arrow arr-next"><a href="javascript:"></a></div>
    </div>

    <div class="info_bar">
        <?php if (!empty($photo['rating_widget'])){ ?>
            <div class="bar_item bi_rating">
                <?php echo $photo['rating_widget']; ?>
            </div>
        <?php } ?>
        <div class="bar_item bi_date_pub" title="<?php echo LANG_DATE_PUB; ?>">
            <?php echo html_date_time($photo['date_pub']); ?>
        </div>
        <div class="bar_item bi_user" title="<?php echo LANG_AUTHOR ?>">
            <a href="<?php echo href_to('users', $photo['user']['id']) ?>"><?php echo $photo['user']['nickname']; ?></a>
        </div>
        <div class="bar_item bi_share">
            <div class="share" style="margin:-4px">
                <script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
                <div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="none" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir,lj,gplus"></div>
            </div>
        </div>
    </div>

</div>

<?php
    $html = cmsEventsManager::hook("photos_item_html", $photo, false);
    if ($html) { echo $html; }
?>

<?php if (!empty($photo['comments_widget'])){ ?>
    <?php echo $photo['comments_widget']; ?>
<?php } ?>

<script>
	<?php if ($is_can_edit){ ?>
		<?php echo $this->getLangJS('LANG_PHOTOS_RENAME_PHOTO'); ?>
		var rename_url = '<?php echo $this->href_to('rename'); ?>';
	<?php } ?>
	<?php if ($is_can_delete){ ?>
		var delete_url = '<?php echo $this->href_to('delete'); ?>';
	<?php } ?>
    var li_w = 78;
    var li_in_frame = <?php echo sizeof($photos) > 7 ? 7 : sizeof($photos); ?>;
    var li_count = <?php echo sizeof($photos); ?>;
    var slider_w = li_w * li_in_frame;
    var left_li_offset = 3;
    var slide_left = <?php echo $active_index; ?> * li_w;
    var arrows_w = 32 + 32;
    var min_left = left_li_offset*li_w;
    var max_left = (li_w*li_count - slider_w);
    icms.photos.init = true;
    icms.photos.mode = 'photo';
</script>