<?php if (!$photos){ return; } ?>

<?php $disable_owner = isset($disable_owner) ? true : false; ?>
<?php foreach($photos as $photo){ ?>

    <?php
        $is_photo_owner = ($is_owner || $photo['user_id'] == $user->id) && !$disable_owner;
        $photo_url = $photo['slug'] ? href_to('photos', $photo['slug'].'.html') : '#';
        $photo['title'] = $photo_url=='#' ? LANG_PHOTOS_NO_PUB : $photo['title'];
    ?>

    <div class="m-1 position-relative overflow-hidden icms-photo-album__photo photo-<?php echo $photo['id']; ?> <?php if ($is_photo_owner) { ?> is_my_photo<?php } ?> <?php echo (($photo_url=='#') ? 'unpublished' : ''); ?>" data-w="<?php echo $photo['sizes'][$preset_small]['width']; ?>" data-h="<?php echo $photo['sizes'][$preset_small]['height']; ?>" itemscope itemtype="http://schema.org/ImageObject">
        <h3 class="h5 text-truncate d-flex justify-content-center align-items-center" itemprop="name">
            <?php html($photo['title']); ?>
        </h3>
        <a class="stretched-link d-block" href="<?php echo $photo_url; ?>" title="<?php html($photo['title']); ?>">
            <img class="icms-photo-album__photo-img img-fluid" src="<?php echo html_image_src($photo['image'], $preset_small, true, false); ?>" title="<?php html($photo['title']); ?>" alt="<?php html($photo['title']); ?>" itemprop="thumbnail" />
        </a>
        <div class="icms-photo-album__photo_info d-flex align-items-center justify-content-between">
            <?php if(!empty($photo['user']['nickname'])){ ?>
                <a class="text-truncate" title="<?php echo LANG_AUTHOR; ?>" href="<?php echo href_to_profile($photo['user']); ?>">
                    <?php html($photo['user']['nickname']); ?>
                </a>
            <?php } ?>
            <div>
                <span title="<?php echo LANG_HITS; ?>">
                    <?php html_svg_icon('solid', 'eye'); ?>
                    <?php echo $photo['hits_count']; ?>
                </span>
                <span title="<?php echo LANG_RATING; ?>">
                    <?php html_svg_icon('solid', 'star'); ?>
                    <?php echo html_signed_num($photo['rating']); ?>
                </span>
                <span title="<?php echo LANG_COMMENTS; ?>">
                    <?php html_svg_icon('solid', 'comments'); ?>
                    <?php echo $photo['comments']; ?>
                </span>
            </div>
        </div>
        <?php if ($is_photo_owner) { ?>
            <a class="icms-photo-album__photo_delete btn btn-danger btn-sm rounded-0" href="#" data-id="<?php echo $photo['id']; ?>" title="<?php echo LANG_DELETE; ?>">
                <?php html_svg_icon('solid', 'minus-circle'); ?>
            </a>
        <?php } ?>
        <meta itemprop="height" content="<?php echo $photo['sizes'][$preset_small]['height']; ?> px">
        <meta itemprop="width" content="<?php echo $photo['sizes'][$preset_small]['width']; ?> px">
    </div>

<?php } ?>
<?php if((isset($has_next) || isset($page) || empty($disable_flex)) || !empty($item['photos_url_params'])){ ?>
    <?php ob_start(); ?>
    <script>
        <?php if(isset($has_next) || isset($page) || empty($disable_flex)){ ?>
            <?php if(isset($has_next)){ ?>
                <?php if($has_next){ ?>
                    icms.photos.has_next = true;
                <?php } else { ?>
                    icms.photos.has_next = false;
                <?php } ?>
            <?php } ?>
            <?php if(isset($page)){ ?>
                icms.photos.page = <?php echo $page; ?>;
            <?php } ?>
            <?php if(empty($disable_flex)){ ?>
                icms.photos.flexImagesInit('<?php echo (isset($photo_wrap_id) ? '#'.$photo_wrap_id : ''); ?>');
            <?php } ?>
        <?php } ?>
        <?php if(!empty($item['photos_url_params'])){ ?>
            $(function(){
                $('.photo_page_link').each(function (){
                    $(this).attr('href', $(this).attr('href')+'?<?php echo $item['photos_url_params']; ?>');
                });
            });
        <?php } ?>
    </script>
    <?php $this->addBottom(ob_get_clean()); ?>
<?php } ?>
