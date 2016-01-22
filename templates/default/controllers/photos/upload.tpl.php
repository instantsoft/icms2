<?php

    $this->addJS( $this->getJavascriptFileName('photos') );

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
            <select name="album_id">
                <?php foreach($albums as $album) { ?>
					<?php if ($album['is_public']) { $album['title'] = '[' . LANG_PHOTOS_PUBLIC_ALBUM . '] ' . $album['title']; } ?>
                    <option value="<?php echo $album['id']; ?>" <?php if ($album['id'] == $album_id) {?>selected="selected"<?php } ?>>
                        <?php if (empty($album['parent_title'])){ ?>
                            <?php html($album['title']); ?>
                        <?php } else { ?>
                            <?php html($album['parent_title'].' → '.$album['title']); ?>
                        <?php } ?>
                    </option>
                <?php } ?>
            </select>
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
