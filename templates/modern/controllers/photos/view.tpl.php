<?php
    $this->addTplJSName([
        'photos',
        'jquery-flex-images',
        'screenfull'
    ]);

    $this->setPageTitle($photo['title']);
    $this->setPageDescription($photo['content'] ? string_get_meta_description($photo['content']) : ($photo['title'].' — '.$album['title']));

    if ($ctype['options']['list_on']) {
        $this->addBreadcrumb($ctype['title'], href_to($ctype['name']));
    }
    if (isset($album['category'])) {
        foreach ($album['category']['path'] as $c) {
            $this->addBreadcrumb($c['title'], href_to($ctype['name'], $c['slug']));
        }
    }
    if ($ctype['options']['item_on']) {
        $this->addBreadcrumb($album['title'], href_to($ctype['name'], $album['slug']) . '.html');
    }
    $this->addBreadcrumb($photo['title']);

    if ($is_can_set_cover) {
        $this->addToolButton(array(
            'class' => 'images',
            'icon'  => 'images',
            'title' => LANG_PHOTOS_SET_COVER,
            'href'  => $this->href_to('set_cover', $photo['id'])
        ));
    }
    if ($is_can_edit) {
        $this->addToolButton(array(
            'class' => 'edit',
            'icon'  => 'pencil-alt',
            'title' => LANG_PHOTOS_EDIT_PHOTO,
            'href'  => $this->href_to('edit', $photo['id'])
        ));
    }

    if ($is_can_delete) {
        $this->addToolButton(array(
            'class'   => 'delete',
            'icon'    => 'minus-circle',
            'title'   => LANG_PHOTOS_DELETE_PHOTO,
            'href'    => 'javascript:icms.photos.delete()',
            'onclick' => "if(!confirm('" . LANG_PHOTOS_DELETE_PHOTO_CONFIRM . "')){ return false; }"
        ));
    }

?>

<div id="album-photo-item" class="content_item row" data-item-delete-url="<?php if ($is_can_delete){ echo $this->href_to('delete'); } ?>" data-id="<?php echo $photo['id']; ?>" itemscope itemtype="http://schema.org/ImageObject">
    <div class="col-sm">
        <div class="inside_wrap orientation_<?php echo $photo['orientation']; ?> text-center bg-light" id="fullscreen_cont">
            <div id="photo_container" class="d-inline-block position-relative overflow-hidden" <?php if($full_size_img){?>data-full-size-img="<?php echo $full_size_img; ?>"<?php } ?>>
                <?php echo $this->renderChild('view_photo_container', array(
                    'photos_url_params' => $photos_url_params,
                    'photo'      => $photo,
                    'preset'     => $preset,
                    'prev_photo' => $prev_photo,
                    'next_photo' => $next_photo
                )); ?>
            </div>
        </div>
    </div>
<?php if(!$hide_info_block){ ?>
    <div class="col-sm col-lg-4">
        <div class="d-flex align-items-center mb-3 mt-3 mt-lg-0">
            <a href="<?php echo href_to_profile($photo['user']); ?>" class="icms-user-avatar mr-2 small <?php if (!empty($photo['user']['is_online'])){ ?>peer_online<?php } else { ?>peer_no_online<?php } ?>">
                <?php if($photo['user']['avatar']){ ?>
                    <?php echo html_avatar_image($photo['user']['avatar'], 'micro', $photo['user']['nickname']); ?>
                <?php } else { ?>
                    <?php echo html_avatar_image_empty($photo['user']['nickname'], 'avatar__mini'); ?>
                <?php } ?>
            </a>
            <a href="<?php echo href_to_profile($photo['user']); ?>" title="<?php echo LANG_AUTHOR ?>" class="mr-2">
                <?php echo $photo['user']['nickname']; ?>
            </a>
            <span class="text-muted" title="<?php echo LANG_DATE_PUB; ?>">
                <?php html_svg_icon('solid', 'calendar-alt'); ?>
                <?php echo html_date_time($photo['date_pub']); ?>
            </span>
        </div>

        <div class="like_buttons info_bar">
        <?php if (!empty($photo['rating_widget'])){ ?>
            <div class="bar_item bi_rating">
                <?php echo $photo['rating_widget']; ?>
            </div>
        <?php } ?>
        <?php if (!empty($ctype['options']['share_code'])){ ?>
            <div class="bar_item share">
                <?php echo $ctype['options']['share_code']; ?>
            </div>
        <?php } ?>
        </div>

        <?php if (!empty($photo['content'])){ ?>
            <div class="photo_content mt-3" itemprop="description">
                <?php echo $photo['content']; ?>
            </div>
        <?php } ?>

        <?php if (!empty($downloads)){ ?>
            <div class="dropdown my-3">
                <button class="btn btn-success btn-block dropdown-toggle" type="button" data-toggle="dropdown" data-offset="0,10" data-reference="parent">
                    <?php echo LANG_DOWNLOAD; ?>
                </button>
                <div id="bubble" class="dropdown-menu dropdown-menu-arrow w-100 p-3 shadow">
                    <?php foreach ($downloads as $download) { ?>
                        <div class="mb-3 custom-control custom-radio <?php echo $download['preset']; ?>_download_preset <?php echo (!$download['link'] ? 'disable_download' : ''); ?>">
                            <input <?php echo ($download['select'] ? 'checked' : ''); ?> type="radio" id="d-<?php echo $download['preset']; ?>" name="download" class="custom-control-input" value="<?php echo $download['link']; ?>" <?php echo (!$download['link'] ? 'disabled' : ''); ?>>
                            <label class="custom-control-label d-flex justify-content-between" for="d-<?php echo $download['preset']; ?>">
                                <span><?php echo $download['name']; ?></span>
                                <span><?php echo $download['size']; ?></span>
                            </label>
                        </div>
                    <?php } ?>
                    <a class="btn btn-primary btn-block download-button process_download" href="">
                        <?php html_svg_icon('solid', 'download'); ?>
                        <?php echo LANG_DOWNLOAD; ?>
                    </a>
                </div>
            </div>
        <?php } ?>

        <?php if ($photo['exif'] || $photo['camera']){ ?>
            <div class="exif_wrap bg-light px-3 pt-3 pb-2">
                <?php if ($photo['camera']){ ?>
                    <a href="<?php html(href_to('photos', 'camera-'.urlencode($photo['camera']))); ?>">
                        <?php html_svg_icon('solid', 'camera-retro'); ?>
                        <?php html($photo['camera']); ?>
                    </a>
                <?php } ?>
                <?php if ($photo['exif']){ ?>
                    <div class="exif_info icms-dot-between text-muted">
                        <?php foreach ($photo['exif'] as $name => $value) { ?>
                            <span title="<?php echo string_lang('lang_exif_'.$name); ?>"><?php html($value); ?></span>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
        <dl class="photo_details bg-light border-top px-3 pb-3">
            <?php foreach ($photo_details as $detail) { ?>
                <div class="row mt-2">
                    <div class="col font-weight-bold"><?php echo $detail['name']; ?></div>
                    <div class="col">
                        <?php if(isset($detail['link'])){ ?>
                            <a href="<?php echo $detail['link']; ?>" title="<?php html($detail['value']); ?>">
                                <?php echo $detail['value']; ?>
                            </a>
                        <?php } else { ?>
                            <?php echo $detail['value']; ?>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </dl>
    </div>
<?php } ?>
    <meta itemprop="height" content="<?php echo $photo['sizes'][$preset]['height']; ?> px">
    <meta itemprop="width" content="<?php echo $photo['sizes'][$preset]['width']; ?> px">
</div>
<?php ob_start(); ?>
<script>
    icms.photos.init = true;
    icms.photos.mode = 'photo';
    icms.photos.row_height = '<?php echo $row_height; ?>';
</script>
<?php $this->addBottom(ob_get_clean()); ?>
<?php if($photos){ ?>
<div id="related_photos_wrap" class="mt-lg-3 mb-2">
    <h5><?php echo $related_title; ?></h5>
    <div class="album-photos-wrap d-flex flex-wrap m-n1" id="related_photos" data-delete-url="<?php echo href_to('photos', 'delete'); ?>">
        <?php echo $this->renderChild('photos', array(
            'photos'        => $photos,
            'is_owner'      => false,
            'disable_owner' => true,
            'user'          => $user,
            'photo_wrap_id' => 'related_photos',
            'preset_small'  => $preset_small
        )); ?>
    </div>
</div>
<?php } ?>

<?php if ($hooks_html) { echo html_each($hooks_html); } ?>

<?php if (!empty($photo['comments_widget'])){ ?>
    <?php echo $photo['comments_widget']; ?>
<?php } ?>