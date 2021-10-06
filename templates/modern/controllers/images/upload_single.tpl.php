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
     data-delete_url="<?php echo $delete_url; ?>">

    <div class="data" style="display:none">
        <?php if ($is_image_exists) { ?>
            <?php foreach($paths as $type=>$path){ ?>
                <?php echo html_input('hidden', "{$name}[{$type}]", $path); ?>
            <?php } ?>
        <?php } ?>
    </div>

    <div class="preview block" <?php if (!$is_image_exists) { ?>style="display:none"<?php } ?><?php if ($is_image_exists) { ?> data-paths="<?php html(json_encode($paths)); ?>"<?php } ?>>
        <div><img src="<?php if ($preview_url) { echo $preview_url; } ?>" /></div>
        <a class="btn btn-danger btn-sm py-0 px-1" href="javascript:" onclick="icms.images.remove('<?php echo $dom_id; ?>')" title="<?php echo LANG_DELETE; ?>">
            <?php html_svg_icon('solid', 'minus-circle'); ?>
        </a>
    </div>

    <div class="upload block" <?php if ($is_image_exists) { ?>style="display:none"<?php } ?>>
        <div id="file-uploader-<?php echo $dom_id; ?>"></div>
        <?php if($allow_import_link){ ?>
            <a class="image_link btn btn-success btn-sm py-0 px-1" href="#" title="<?php echo LANG_OR; ?> <?php echo LANG_PARSER_ADD_FROM_LINK; ?>">
                <?php html_svg_icon('solid', 'link'); ?>
            </a>
        <?php } ?>
    </div>

    <div class="loading block" style="display:none">
        <?php echo LANG_LOADING; ?>
    </div>

</div>
<?php ob_start(); ?>
<script>
    <?php echo $this->getLangJS('LANG_SELECT_UPLOAD', 'LANG_DROP_TO_UPLOAD', 'LANG_CANCEL', 'LANG_ERROR'); ?>
    $(function(){
        icms.images.upload('<?php echo $dom_id; ?>', '<?php echo $upload_url; ?>');
        <?php if($allow_import_link){ ?>
            $('#widget_image_<?php echo $dom_id; ?> a.image_link').on('click', function (){
                var link = prompt('<?php echo LANG_PARSER_ENTER_IMAGE_LINK; ?>');
                if(link){
                    icms.images.uploadByLink('<?php echo $dom_id; ?>', '<?php echo $upload_url; ?>', link);
                }
                return false;
            });
        <?php } ?>
    });
</script>
<?php $this->addBottom(ob_get_clean()); ?>
