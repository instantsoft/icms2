<?php
	$this->addJSFromContext( $this->getJavascriptFileName('fileuploader') );
	$this->addJSFromContext( $this->getJavascriptFileName('images-upload') );

    $is_image_exists = !empty($paths);

	$upload_url = $this->href_to('upload', $name);

	if (is_array($sizes)) {
		$upload_url .= '?sizes=' . implode(',', $sizes);
	}

?>
<div id="widget_image_<?php echo $name; ?>" class="widget_image_single">

    <div class="data" style="display:none">
        <?php if ($is_image_exists) { ?>
            <?php foreach($paths as $type=>$path){ ?>
                <?php echo html_input('hidden', "{$name}[{$type}]", $path); ?>
            <?php } ?>
        <?php } ?>
    </div>

    <div class="preview block" <?php if (!$is_image_exists) { ?>style="display:none"<?php } ?>>
        <img src="<?php if ($is_image_exists) { echo cmsConfig::get('upload_host') . '/' . $path; } ?>" />
        <a href="javascript:" onclick="icms.images.remove('<?php echo $name; ?>')"><?php echo LANG_DELETE; ?></a>
    </div>

    <div class="upload block" <?php if ($is_image_exists) { ?>style="display:none"<?php } ?>>
        <div id="file-uploader-<?php echo $name; ?>"></div>
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

        $(document).ready(function(){
            icms.images.upload('<?php echo $name; ?>', '<?php echo $upload_url; ?>');
            <?php if($allow_import_link){ ?>
                $('#widget_image_<?php echo $name; ?> .image_link a').on('click', function (){
                    link = prompt('<?php echo LANG_PARSER_ENTER_IMAGE_LINK; ?>');
                    if(link){
                        icms.images.uploadByLink('<?php echo $name; ?>', '<?php echo $upload_url; ?>', link);
                    }
                    return false;
                });
            <?php } ?>
        });

    </script>

</div>
