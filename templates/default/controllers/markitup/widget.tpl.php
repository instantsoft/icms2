<?php
    cmsTemplate::getInstance()->insertJS('wysiwyg/markitup/sets/'.$options['set'].'/image_upload.js');
    cmsTemplate::getInstance()->insertJS('wysiwyg/markitup/jquery.markitup.js');
    cmsTemplate::getInstance()->insertJS('wysiwyg/markitup/sets/'.$options['set'].'/set.js');
    cmsTemplate::getInstance()->insertCSS('wysiwyg/markitup/sets/'.$options['set'].'/style.css');
    cmsTemplate::getInstance()->insertCSS('wysiwyg/markitup/skins/'.$options['skin'].'/style.css');
?>

<textarea id="<?php echo $options['id']; ?>"
          class="textarea"
          name="<?php echo $field_id;?>"
          data-upload-url="<?php echo href_to('markitup', 'upload'); ?>"><?php echo $content; ?></textarea>

<script type="text/javascript">
    $(document).ready(function(){
        if(!$("#<?php echo $options['id']; ?>").hasClass("markItUpEditor")) {
            $("#<?php echo $options['id']; ?>").markItUp(mySettings);
        }
    });
</script>
