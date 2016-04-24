<?php

    $this->addJS( $this->getJavascriptFileName('photos') );
    $this->addJS($this->getJavascriptFileName('jquery-chosen'));
    $this->addCSS('templates/default/css/jquery-chosen.css');

    $this->setPageTitle(LANG_PHOTOS_UPLOAD);

    $user = cmsUser::getInstance();

    if ($ctype['options']['list_on']){
        $this->addBreadcrumb($ctype['title'], href_to($ctype['name']));
    }

    $this->addBreadcrumb(LANG_PHOTOS_UPLOAD);

?>

<h1><?php echo LANG_PHOTOS_UPLOAD; ?></h1>

<form action="" method="post">

    <fieldset>

        <legend><?php printf(LANG_PHOTOS_SELECT_ALBUM, $ctype['labels']['one']); ?></legend>

        <div class="field ft_list f_album_id">
            <?php echo html_select('album_id', $albums_select, $album_id, array('id'=>'album_id')); ?>
        </div>
        <div class="field">
            <?php printf(LANG_PHOTOS_NEW_ALBUM, href_to('albums', 'add'), $ctype['labels']['one']); ?>
        </div>
    </fieldset>

    <?php $this->renderChild('widget', array('photos'=>$photos, 'id'=>false)); ?>

    <div class="buttons">
        <?php echo html_submit(LANG_SAVE); ?>
    </div>

</form>
<script type="text/javascript">
    $(function(){
        $('#album_id').chosen({no_results_text: '<?php echo LANG_LIST_EMPTY; ?>', width: '100%', disable_search_threshold: 8, placeholder_text_single: '<?php echo LANG_SELECT; ?>', search_placeholder: '<?php echo LANG_BEGIN_TYPING; ?>'});
    });
</script>