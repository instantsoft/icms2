<?php $this->addJS( $this->getJavascriptFileName('photos') ); ?>

<div id="album-photos-list"<?php if ($is_owner || $album['is_public']) { ?> data-delete-url="<?php echo $this->href_to('delete'); ?>"<?php } ?>>

    <?php if (is_array($photos)){ ?>
        <?php foreach($photos as $photo){ ?>
			<?php $is_photo_owner = $is_owner || $photo['user_id'] == cmsUser::get('id'); ?>
            <div class="photo photo-<?php echo $photo['id']; ?>">
                <div class="image">
                    <a href="<?php echo $this->href_to('view', $photo['id']); ?>" title="<?php html($photo['title']); ?>">
                        <?php echo html_image($photo['image'], 'normal', $photo['title']); ?>
                    </a>
                </div>
                <div class="info<?php if ($is_photo_owner) { ?> info-3<?php } ?>">
                    <?php if ($is_photo_owner) { ?>
                        <div class="delete">
                            <a class="icon-delete" href="#" title="<?php echo LANG_DELETE; ?>" data-id="<?php echo $photo['id']; ?>"></a>
                        </div>
                    <?php } ?>
                    <div class="rating <?php echo html_signed_class($photo['rating']); ?>">
                        <?php echo html_signed_num($photo['rating']); ?>
                    </div>
                    <div class="comments">
                        <span><?php echo $photo['comments']; ?></span>
                    </div>
                </div>
            </div>

        <?php } ?>
    <?php } ?>

</div>

<?php if ($perpage < $total) { ?>
    <?php echo html_pagebar($page, $perpage, $total, $page_url); ?>
<?php } ?>

<script>
    icms.photos.init = true;
    icms.photos.mode = 'album';
</script>