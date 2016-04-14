<?php

	$this->addJSFromContext( $this->getJavascriptFileName('fileuploader') );
	$this->addJSFromContext( $this->getJavascriptFileName('images-upload') );

	$config = cmsConfig::getInstance();

	$upload_url = $this->href_to('upload', $name);

	if (is_array($sizes)) {
		$upload_url .= '?sizes=' . implode(',', $sizes);
	}

?>

<div id="widget_image_<?php echo $name; ?>" class="widget_image_multi">

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
                <div class="preview block" rel="<?php echo $idx; ?>">
					<?php  $is_image_exists = !empty($paths); ?>
					<?php if ($is_image_exists) { ?><img src="<?php echo $config->upload_host . '/' . end($paths); ?>" /><?php } ?>
                    <a href="javascript:" onclick="icms.images.removeOne('<?php echo $name; ?>', <?php echo $idx; ?>)"><?php echo LANG_DELETE; ?></a>
                </div>
            <?php } ?>
        <?php } ?>
    </div>

    <div class="preview_template block" style="display:none">
        <img src="" border="0" />
        <a href="javascript:"><?php echo LANG_DELETE; ?></a>
    </div>

    <div class="upload block">
        <div id="file-uploader-<?php echo $name; ?>"></div>
    </div>

    <?php if($allow_import_link){ ?>
        <div class="image_link upload block">
            <span><?php echo LANG_OR; ?></span> <a class="input_link_block" href="#"><?php echo LANG_PARSER_ADD_FROM_LINK; ?></a>
        </div>
    <?php } ?>

    <div class="loading block" style="display:none">
        <?php echo LANG_LOADING; ?>
    </div>

    <script>
        <?php echo $this->getLangJS('LANG_SELECT_UPLOAD', 'LANG_DROP_TO_UPLOAD', 'LANG_CANCEL', 'LANG_ERROR'); ?>
        icms.images.createUploader('<?php echo $name; ?>', '<?php echo $upload_url; ?>');
        <?php if($allow_import_link){ ?>
            $(function(){
                $('#widget_image_<?php echo $name; ?> .image_link a').on('click', function (){
                    link = prompt('<?php echo LANG_PARSER_ENTER_IMAGE_LINK; ?>');
                    if(link){
                        icms.images.uploadMultyByLink('<?php echo $name; ?>', '<?php echo $upload_url; ?>', link);
                    }
                    return false;
                });
            });
        <?php } ?>
    </script>

</div>
