<?php

    $this->addJS( $this->getJavascriptFileName('photos') );
    $this->addJS($this->getJavascriptFileName('jquery-chosen'));
    $this->addCSS('templates/default/css/jquery-chosen.css');

    $this->setPageTitle($title);

    $user = cmsUser::getInstance();

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
            <div class="field">
                <?php printf(LANG_PHOTOS_NEW_ALBUM, href_to('albums', 'add'), $ctype['labels']['one']); ?>
            </div>
        </fieldset>
        <script type="text/javascript">
            $(function(){
                $('#album_id').chosen({no_results_text: '<?php echo LANG_LIST_EMPTY; ?>', width: '100%', disable_search_threshold: 8, placeholder_text_single: '<?php echo LANG_SELECT; ?>', allow_single_deselect: true, search_placeholder: '<?php echo LANG_BEGIN_TYPING; ?>'});
            });
        </script>
    <?php } ?>

    <?php $this->renderChild('widget', array(
        'photos'     => $photos,
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