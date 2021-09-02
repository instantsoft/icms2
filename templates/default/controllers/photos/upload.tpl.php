<?php

    $this->addTplJSName('photos');
    $this->addTplJSName('jquery-chosen');
    $this->addTplCSSName('jquery-chosen');

    $this->setPageTitle($title);

    if ($ctype['options']['list_on']){
        $this->addBreadcrumb($ctype['title'], href_to($ctype['name']));
    }
    if ($album){
        $this->addBreadcrumb($album['title'], href_to($ctype['name'], $album['slug'].'.html'));
    }

    $this->addBreadcrumb($title);

?>

<h1><?php echo $title; ?></h1>

<form action="" method="post">

    <?php if(empty($is_edit)){ ?>
        <fieldset>

            <legend><?php printf(LANG_PHOTOS_SELECT_ALBUM, $ctype['labels']['one']); ?></legend>

            <div class="field ft_list f_album_id">
                <?php echo html_select('album_id', $albums_select, $album_id, array('id'=>'album_id')); ?>
            </div>
            <?php if(!empty($allow_add)){ ?>
            <div class="field">
                <?php printf(LANG_PHOTOS_NEW_ALBUM, href_to('albums', 'add'), $ctype['labels']['one']); ?>
            </div>
            <?php } ?>
        </fieldset>
        <script>
            $(function(){
                $('#album_id').chosen({no_results_text: '<?php echo LANG_LIST_EMPTY; ?>', width: '100%', disable_search_threshold: 8, placeholder_text_single: '<?php echo LANG_SELECT; ?>', allow_single_deselect: true, search_placeholder: '<?php echo LANG_BEGIN_TYPING; ?>'});
            });
        </script>
    <?php } ?>

    <?php $this->renderChild('widget', array(
        'photos'     => $photos,
        'editor_params' => $editor_params,
        'album'      => $album,
        'is_edit'    => $is_edit,
        'ctype'      => $ctype,
        'preset_big' => $preset_big,
        'types'      => $types
    )); ?>

    <div class="buttons">
        <?php echo html_submit(LANG_SAVE); ?>
    </div>

</form>