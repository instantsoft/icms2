<?php
    $this->addTplJSNameFromContext([
        'fileuploader',
        'images-upload'
    ]);
    $this->addTplCSSFromContext('controllers/images/styles');
?>
<div id="widget_image_<?php echo $dom_id; ?>"
     class="widget_image_single"
     data-field_name="<?php echo $name; ?>"
     data-delete_url="<?php html($delete_url); ?>">

    <div class="data" style="display:none">
        <?php if ($is_image_exists) { ?>
            <?php foreach($paths as $type=>$path){ ?>
                <?php echo html_input('hidden', "{$name}[{$type}]", $path); ?>
            <?php } ?>
        <?php } ?>
    </div>

    <div class="preview block" <?php if (!$is_image_exists) { ?>style="display:none"<?php } ?><?php if ($is_image_exists) { ?> data-paths="<?php html(json_encode($paths)); ?>"<?php } ?>>
        <img src="<?php if ($is_image_exists) { echo cmsConfig::get('upload_host') . '/' . reset($paths); } ?>" />
        <a href="javascript:" onclick="icms.images.remove('<?php echo $dom_id; ?>')"><?php echo LANG_DELETE; ?></a>
    </div>

    <div class="upload block" <?php if ($is_image_exists) { ?>style="display:none"<?php } ?>>
        <div id="file-uploader-<?php echo $dom_id; ?>"></div>
    </div>

    <div class="loading block" style="display:none">
        <?php echo LANG_LOADING; ?>
    </div>

    <?php if($allow_import_link){ ?>
        <div class="image_link upload block" <?php if ($is_image_exists) { ?>style="display:none"<?php } ?>>
            <span><?php echo LANG_OR; ?></span> <a class="input_link_block" href="#"><?php echo LANG_PARSER_ADD_FROM_LINK; ?></a>
        </div>
    <?php } ?>

    <script>
        <?php echo $this->getLangJS('LANG_SELECT_UPLOAD', 'LANG_DROP_TO_UPLOAD', 'LANG_CANCEL', 'LANG_ERROR'); ?>
        icms.images.delete_url = "<?php html($delete_url); ?>";
        $(function(){
            icms.images.upload('<?php echo $dom_id; ?>', '<?php echo $upload_url; ?>');
            <?php if($allow_import_link){ ?>
                $('#widget_image_<?php echo $dom_id; ?> .image_link a').on('click', function (){
                    link = prompt('<?php echo LANG_PARSER_ENTER_IMAGE_LINK; ?>');
                    if(link){
                        icms.images.uploadByLink('<?php echo $dom_id; ?>', '<?php echo $upload_url; ?>', link);
                    }
                    return false;
                });
            <?php } ?>
        });
    </script>
</div>
