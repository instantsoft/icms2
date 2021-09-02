<?php

    $this->addTplJSName([
        'photos',
        'jquery-owl.carousel',
        'screenfull'
        ]);
    $this->addTplCSSName('jquery-owl.carousel');

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
            'title' => LANG_PHOTOS_SET_COVER,
            'href'  => $this->href_to('set_cover', $photo['id'])
        ));
    }
    if ($is_can_edit) {
        $this->addToolButton(array(
            'class' => 'edit',
            'title' => LANG_PHOTOS_EDIT_PHOTO,
            'href'  => $this->href_to('edit', $photo['id'])
        ));
    }

    if ($is_can_delete) {
        $this->addToolButton(array(
            'class'   => 'delete',
            'title'   => LANG_PHOTOS_DELETE_PHOTO,
            'href'    => 'javascript:icms.photos.delete()',
            'onclick' => "if(!confirm('" . LANG_PHOTOS_DELETE_PHOTO_CONFIRM . "')){ return false; }"
        ));
    }

?>

<div id="album-photo-item" class="content_item" data-item-delete-url="<?php if ($is_can_delete){ echo $this->href_to('delete'); } ?>" data-id="<?php echo $photo['id']; ?>" itemscope itemtype="http://schema.org/ImageObject">
    <div class="left">
        <div class="inside">
            <div class="inside_wrap orientation_<?php echo $photo['orientation']; ?>" id="fullscreen_cont">
                <div id="photo_container" <?php if($full_size_img){?>data-full-size-img="<?php echo $full_size_img; ?>"<?php } ?>>
                    <?php echo $this->renderChild('view_photo_container', array(
                        'photos_url_params' => $photos_url_params,
                        'photo'      => $photo,
                        'preset'     => $preset,
                        'prev_photo' => $prev_photo,
                        'next_photo' => $next_photo
                    )); ?>
                </div>
            </div>
            <?php if($photos){ ?>
            <div id="related_photos_wrap">
                <h3><?php echo $related_title; ?></h3>
                <div class="album-photos-wrap owl-carousel" id="related_photos" data-delete-url="<?php echo href_to('photos', 'delete'); ?>">
                    <?php echo $this->renderChild('photos', array(
                        'photos'        => $photos,
                        'is_owner'      => false,
                        'user'          => $user,
                        'photo_wrap_id' => 'related_photos',
                        'preset_small'  => $preset_small,
                        'disable_flex'  => true
                    )); ?>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
<?php if(!$hide_info_block){ ?>
    <div class="right">
        <div class="photo_author">
            <span class="album_user" title="<?php echo LANG_AUTHOR ?>">
                <a href="<?php echo href_to_profile($photo['user']); ?>">
                    <?php echo html_avatar_image($photo['user']['avatar'], 'micro', $photo['user']['nickname']); ?>
                </a>
            </span>
            <a href="<?php echo href_to_profile($photo['user']); ?>" title="<?php echo LANG_AUTHOR ?>">
                <?php echo $photo['user']['nickname']; ?>
            </a>
            <span class="album_date" title="<?php echo LANG_DATE_PUB; ?>">
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
            <div class="share">
                <?php echo $ctype['options']['share_code']; ?>
            </div>
        <?php } ?>
        </div>

        <?php if (!empty($photo['content'])){ ?>
            <div class="photo_content" itemprop="description">
                <?php echo $photo['content']; ?>
            </div>
        <?php } ?>

        <?php if (!empty($downloads)){ ?>
            <div class="download_menu">
                <span id="download-button" class="download-button"><i class="photo_icon icon_download"></i> <?php echo LANG_DOWNLOAD; ?></span>
                <div id="bubble">
                    <table>
                        <tbody>
                            <?php foreach ($downloads as $download) { ?>
                            <tr class="<?php echo $download['preset']; ?>_download_preset <?php echo (!$download['link'] ? 'disable_download' : ''); ?>">
                                <td>
                                    <label><input <?php echo ($download['select'] ? 'checked=""' : ''); ?> type="radio" name="download" <?php echo (!$download['link'] ? 'disabled=""' : ''); ?> value="<?php echo $download['link']; ?>"> <?php echo $download['name']; ?> </label>
                                </td>
                                <td>
                                    <?php echo $download['size']; ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <a class="download-button process_download" href=""><?php echo LANG_DOWNLOAD; ?></a>
                </div>
            </div>
        <?php } ?>

        <?php if ($photo['exif'] || $photo['camera']){ ?>
            <div class="exif_wrap">
                <?php if ($photo['camera']){ ?>
                    <a href="<?php html(href_to('photos', 'camera-'.urlencode($photo['camera']))); ?>">
                        <?php html($photo['camera']); ?>
                    </a>
                <?php } ?>
                <?php if ($photo['exif']){ ?>
                    <div class="exif_info">
                        <?php foreach ($photo['exif'] as $name => $value) { ?>
                            <span title="<?php echo string_lang('lang_exif_'.$name); ?>"><?php html($value); ?></span>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

        <dl class="photo_details">
            <?php foreach ($photo_details as $detail) { ?>
                <dt><?php echo $detail['name']; ?></dt>
                <dd>
                    <?php if(isset($detail['link'])){ ?>
                        <a href="<?php echo $detail['link']; ?>" title="<?php html($detail['value']); ?>">
                            <?php echo $detail['value']; ?>
                        </a>
                    <?php } else { ?>
                        <?php echo $detail['value']; ?>
                    <?php } ?>
                </dd>
            <?php } ?>
        </dl>

    </div>
<?php } ?>
    <meta itemprop="height" content="<?php echo $photo['sizes'][$preset]['height']; ?> px">
    <meta itemprop="width" content="<?php echo $photo['sizes'][$preset]['width']; ?> px">
</div>

<?php if ($hooks_html) { echo html_each($hooks_html); } ?>

<?php if (!empty($photo['comments_widget'])){ ?>
    <?php echo $photo['comments_widget']; ?>
<?php } ?>

<script>
    icms.photos.init = true;
    icms.photos.mode = 'photo';
    $(function(){
        icms.photos.initCarousel('#related_photos', function (){
            left_height = $('#album-photo-item .inside_wrap').height();
            side_height = $('#album-photo-item .right').height();
            if(side_height <= left_height){
                $('#album-photo-item').append($('#related_photos_wrap'));
            }
        });
    });
</script>