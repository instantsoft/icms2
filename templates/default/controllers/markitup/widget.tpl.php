<?php
    $this->addJSFromContext('wysiwyg/markitup/sets/'.$options['set'].'/image_upload.js');
    $this->addJSFromContext('wysiwyg/markitup/insert_smiles.js');
    $this->addJSFromContext('wysiwyg/markitup/jquery.markitup.js');
    $this->addJSFromContext('wysiwyg/markitup/sets/'.$options['set'].'/'.(isset($options['set_name']) ? $options['set_name'] : 'set').'.js');
    $this->addCSSFromContext('wysiwyg/markitup/sets/'.$options['set'].'/style.css');
    $this->addCSSFromContext('wysiwyg/markitup/skins/'.$options['skin'].'/style.css');
?>

<textarea id="<?php echo $options['id']; ?>"
          class="textarea"
          name="<?php echo $field_id;?>"
          data-smiles-url="<?php echo href_to('typograph', 'get_smiles'); ?>"
          data-upload-url="<?php echo href_to('markitup', 'upload'); ?>"><?php echo $content; ?></textarea>

<script type="text/javascript">
    $(document).ready(function(){
        if(!$("#<?php echo $options['id']; ?>").hasClass("markItUpEditor")) {
            $("#<?php echo $options['id']; ?>").markItUp(mySettings);
        }
    });
</script>