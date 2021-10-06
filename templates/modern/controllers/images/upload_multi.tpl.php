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
     data-delete_url="<?php echo $delete_url; ?>">

    <div class="data" style="display:none">
        <?php if ($images){ ?>
            <?php foreach($images as $idx => $paths){ ?>
                <?php foreach($paths as $path_name => $path){ ?>
                    <input type="hidden" name="<?php echo $name; ?>[<?php echo $idx; ?>][<?php echo $path_name; ?>]" value="<?php echo $path; ?>" rel="<?php echo $idx; ?>"/>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    </div>

    <div class="previews_list d-flex flex-wrap">
        <?php if ($images){ ?>
            <?php foreach($images as $idx => $paths){ ?>
                <div class="preview multi-block" rel="<?php echo $idx; ?>" data-paths="<?php html(json_encode($paths)); ?>">
					<?php if (!empty($paths)) { ?>
                        <div><img src="<?php echo cmsConfig::get('upload_host') . '/' . reset($paths); ?>" /></div>
                    <?php } ?>
                        <a class="btn btn-danger btn-sm py-0 px-1" href="#" data-id="<?php echo $idx; ?>" onclick="return icms.images.removeOne('<?php echo $dom_id; ?>', this);" title="<?php echo LANG_DELETE; ?>">
                        <?php html_svg_icon('solid', 'minus-circle'); ?>
                    </a>
                </div>
            <?php } ?>
        <?php } ?>
    </div>

    <div class="preview_template preview multi-block" style="display:none">
        <div><img src="" /></div>
        <a class="btn btn-danger btn-sm py-0 px-1" href="javascript:">
            <?php html_svg_icon('solid', 'minus-circle'); ?>
        </a>
    </div>

    <div class="upload row align-items-center">
        <div id="file-uploader-<?php echo $dom_id; ?>" data-uploaded_count="<?php echo ($max_photos && $images && count($images)) ? count($images) : 0; ?>" class="col-sm-auto"></div>
        <?php if($allow_import_link){ ?>
            <span class="col-sm-auto my-1"><?php echo LANG_OR; ?></span>
            <span class="col-sm-auto image_link">
                <a class="input_link_block btn btn-secondary" href="#">
                    <?php html_svg_icon('solid', 'link'); ?>
                    <?php echo LANG_PARSER_ADD_FROM_LINK; ?>
                </a>
            </span>
        <?php } ?>
        <?php if($max_photos){ ?>
            <div class="col-sm-auto upload photo_limit_hint text-muted">
                <?php echo sprintf(LANG_PARSER_IMAGE_MAX_COUNT_HINT, html_spellcount($max_photos, LANG_PARSER_IMAGE_SPELL)); ?>
            </div>
        <?php } ?>
    </div>

</div>
<?php ob_start(); ?>
<script>
    <?php echo $this->getLangJS('LANG_SELECT_UPLOAD', 'LANG_DROP_TO_UPLOAD', 'LANG_CANCEL', 'LANG_ERROR'); ?>
    icms.images.createUploader('<?php echo $dom_id; ?>', '<?php echo $upload_url; ?>', <?php echo $max_photos; ?>, '<?php echo sprintf(LANG_PARSER_IMAGE_MAX_COUNT_HINT, html_spellcount($max_photos, LANG_PARSER_IMAGE_SPELL)); ?>');
    <?php if($allow_import_link){ ?>
        $(function(){
            $('#widget_image_<?php echo $dom_id; ?> .image_link a').on('click', function (){
                var link = prompt('<?php echo LANG_PARSER_ENTER_IMAGE_LINK; ?>');
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
<?php $this->addBottom(ob_get_clean()); ?>
