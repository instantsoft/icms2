<?php
	$this->addTplJSNameFromContext([
        'fileuploader',
        'images-upload',
        'jquery-ui'
        ]);
    $this->addTplCSSNameFromContext('jquery-ui');
    $this->addTplCSSFromContext('controllers/images/styles');
?>

<div id="widget_image_<?php echo $dom_id; ?>"
     class="widget_image_multi"
     data-field_name="<?php echo $name; ?>"
     data-delete_url="<?php html($delete_url); ?>">

    <div class="data" style="display:none">
        <?php if ($images){ ?>
            <?php foreach($images as $idx => $paths){ ?>
                <?php foreach($paths as $path_name => $path){ ?>
                    <input type="hidden" name="<?php echo $name; ?>[<?php echo $idx; ?>][<?php echo $path_name; ?>]" value="<?php echo $path; ?>" rel="<?php echo $idx; ?>"/>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    </div>

    <div class="previews_list">
        <?php if ($images){ ?>
            <?php foreach($images as $idx => $paths){ ?>
                <div class="preview block" rel="<?php echo $idx; ?>" data-paths="<?php html(json_encode($paths)); ?>">
					<?php  $is_image_exists = !empty($paths); ?>
					<?php if ($is_image_exists) { ?><img src="<?php echo cmsConfig::get('upload_host') . '/' . reset($paths); ?>" /><?php } ?>
                    <a href="#" data-id="<?php echo $idx; ?>" onclick="return icms.images.removeOne('<?php echo $dom_id; ?>', this);"><?php echo LANG_DELETE; ?></a>
                </div>
            <?php } ?>
        <?php } ?>
    </div>

    <div class="preview_template block" style="display:none">
        <img src="" />
        <a href="javascript:"><?php echo LANG_DELETE; ?></a>
    </div>

    <div class="upload block">
        <div id="file-uploader-<?php echo $dom_id; ?>" data-uploaded_count="<?php echo ($max_photos && $images && count($images)) ? count($images) : 0; ?>"></div>
    </div>

    <?php if($allow_import_link){ ?>
        <div class="image_link upload block">
            <span><?php echo LANG_OR; ?></span> <a class="input_link_block" href="#"><?php echo LANG_PARSER_ADD_FROM_LINK; ?></a>
        </div>
    <?php } ?>
    <?php if($max_photos){ ?>
        <div class="upload block photo_limit_hint">
            <?php echo sprintf(LANG_PARSER_IMAGE_MAX_COUNT_HINT, html_spellcount($max_photos, LANG_PARSER_IMAGE_SPELL)); ?>
        </div>
    <?php } ?>

    <div class="loading block" style="display:none">
        <?php echo LANG_LOADING; ?>
    </div>

    <script>
        <?php echo $this->getLangJS('LANG_SELECT_UPLOAD', 'LANG_DROP_TO_UPLOAD', 'LANG_CANCEL', 'LANG_ERROR'); ?>
        icms.images.delete_url = "<?php html($delete_url); ?>";
        icms.images.createUploader('<?php echo $dom_id; ?>', '<?php echo $upload_url; ?>', <?php echo $max_photos; ?>, '<?php echo sprintf(LANG_PARSER_IMAGE_MAX_COUNT_HINT, html_spellcount($max_photos, LANG_PARSER_IMAGE_SPELL)); ?>');
        <?php if($allow_import_link){ ?>
            $(function(){
                $('#widget_image_<?php echo $dom_id; ?> .image_link a').on('click', function (){
                    link = prompt('<?php echo LANG_PARSER_ENTER_IMAGE_LINK; ?>');
                    if(link){
                        icms.images.uploadMultyByLink('<?php echo $dom_id; ?>', '<?php echo $upload_url; ?>', link, <?php echo $max_photos; ?>, '<?php echo sprintf(LANG_PARSER_IMAGE_MAX_COUNT_HINT, html_spellcount($max_photos, LANG_PARSER_IMAGE_SPELL)); ?>');
                    }
                    return false;
                });
            });
        <?php } ?>
        $(function(){
            icms.images.initSortable('<?php echo $dom_id; ?>');
        });
    </script>

</div>