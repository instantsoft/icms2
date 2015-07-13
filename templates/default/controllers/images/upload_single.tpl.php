<?php 
	
	$this->addJS( $this->getJavascriptFileName('fileuploader') );
	$this->addJS( $this->getJavascriptFileName('images-upload') );
	
	$config = cmsConfig::getInstance();

	$is_image_exists = isset($paths['small']); 
	
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
        <img src="<?php if ($is_image_exists) { echo $config->upload_host . '/' . $paths['small']; } ?>" border="0" />
        <a href="javascript:" onclick="icms.images.remove('<?php echo $name; ?>')"><?php echo LANG_DELETE; ?></a>
    </div>

    <div class="upload block" <?php if ($is_image_exists) { ?>style="display:none"<?php } ?>>
        <div id="file-uploader-<?php echo $name; ?>"></div>
    </div>

    <div class="loading block" style="display:none">
        <?php echo LANG_LOADING; ?>
    </div>

    <script>

        <?php echo $this->getLangJS('LANG_SELECT_UPLOAD', 'LANG_DROP_TO_UPLOAD', 'LANG_CANCEL', 'LANG_ERROR'); ?>

        $(document).ready(function(){
            icms.images.upload('<?php echo $name; ?>', '<?php echo $upload_url; ?>');
        });

    </script>

</div>
