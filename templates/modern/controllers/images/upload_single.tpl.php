<?php
    $this->addTplJSNameFromContext([
        'fileuploader',
        'images-upload'
    ]);
    $this->addTplCSSFromContext('controllers/images/styles');

    if($allow_image_cropper){
        $this->addTplJSNameFromContext([
            'vendors/cropperjs/cropper.min'
        ]);
        $this->addTplCSSName('cropperjs');
    }
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
        <div id="file-uploader-<?php echo $dom_id; ?>">
            <div class="qq-uploader">
                <div class="qq-upload-button">
                    <input accept="image/jpeg,image/png,image/gif,image/webp" type="file" name="file" style="position: absolute; right: 0px; top: 0px; font-size: 118px; padding: 0px; cursor: pointer; opacity: 0;" class="qq-input">
                </div>
            </div>
        </div>
        <?php if($allow_import_link){ ?>
            <a class="image_link btn btn-success btn-sm py-0 px-1" href="#" title="<?php echo LANG_OR; ?> <?php echo LANG_PARSER_ADD_FROM_LINK; ?>">
                <?php html_svg_icon('solid', 'link'); ?>
            </a>
        <?php } ?>
    </div>

    <div class="loading block" style="display:none">
        <?php echo LANG_LOADING; ?>
    </div>

    <?php if($allow_image_cropper){ ?>
        <div class="modal" tabindex="-1" role="dialog" id="modal-crop-<?php echo $dom_id; ?>" data-image_cropper_rounded="<?php echo $image_cropper_rounded ? 1 : 0; ?>" data-image_cropper_ratio="<?php echo $image_cropper_ratio; ?>">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo LANG_IMAGES_CROP_IMG; ?></h5>
                    </div>
                    <div class="modal-body">
                        <div class="<?php echo $image_cropper_rounded ? 'cropper-rounded' : ''; ?>">
                            <img class="img-fluid" id="cropper-img-<?php echo $dom_id; ?>" src="">
                        </div>
                        <div class="d-flex align-center mt-3">
                            <div id="crop-actions-<?php echo $dom_id; ?>">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-light" data-method="rotate" data-option="-45">
                                        <?php html_svg_icon('solid', 'undo-alt'); ?>
                                    </button>
                                    <button type="button" class="btn btn-light" data-method="rotate" data-option="45">
                                        <?php html_svg_icon('solid', 'redo-alt'); ?>
                                    </button>
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-light" data-method="scaleX" data-option="-1">
                                        <?php html_svg_icon('solid', 'arrows-alt-h'); ?>
                                    </button>
                                    <button type="button" class="btn btn-light" data-method="scaleY" data-option="-1">
                                        <?php html_svg_icon('solid', 'arrows-alt-v'); ?>
                                    </button>
                                </div>
                            </div>
                            <div class="ml-auto">
                                <button type="button" class="btn btn-secondary mr-3" data-dismiss="modal"><?php echo LANG_CANCEL; ?></button>
                                <button type="button" class="btn btn-primary" id="crop-<?php echo $dom_id; ?>"><?php echo LANG_UPLOAD; ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<?php ob_start(); ?>
<script>
    <?php echo $this->getLangJS('LANG_SELECT_UPLOAD', 'LANG_DROP_TO_UPLOAD', 'LANG_CANCEL', 'LANG_ERROR'); ?>
    icms.images.allowed_mime = <?php echo json_encode($allowed_mime); ?>;
    $(function(){
        icms.images.upload('<?php echo $dom_id; ?>', '<?php echo $upload_url; ?>', <?php echo $allow_image_cropper ? 'true' : 'false'; ?>);
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