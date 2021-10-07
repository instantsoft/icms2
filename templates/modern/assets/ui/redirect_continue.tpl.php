<div>
    <?php
    $messages = cmsUser::getSessionMessages();
    if ($messages){ ?>
        <?php foreach($messages as $message){ ?>
            <div class="alert alert-<?php echo str_replace(['error'], ['danger'], $message['class']); ?>">
                <?php echo $message['text']; ?>
            </div>
         <?php } ?>
    <?php } ?>
    <form id="redirect_form" class="mt-3" action="<?php html($redirect_url); ?>" method="post">
        <?php echo html_submit(LANG_CONTINUE); ?>
    </form>
</div>
<script>
    setTimeout(function (){
        $('#redirect_form .button-submit').trigger('click');
    }, 1000);
</script>