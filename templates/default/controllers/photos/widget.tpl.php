<?php $this->addJS( $this->getJavascriptFileName('fileuploader') ); ?>
<?php $this->addJS( $this->getJavascriptFileName('photos') ); ?>

<fieldset>

    <legend><?php echo LANG_PHOTOS; ?></legend>

    <div id="album-photos-widget" data-delete-url="<?php echo $this->href_to('delete'); ?>">

        <div class="previews_list">
            <?php if ($photos){ ?>
                <?php foreach($photos as $photo){ ?>
                    <div class="preview block" rel="<?php echo $photo['id']; ?>">
                        <div class="thumb"><?php echo html_image($photo['image']); ?></div>
                        <div class="info">
                            <div class="title">
                                <?php echo html_input('text', 'photos['.$photo['id'].']', $photo['title']); ?>
                            </div>
                            <div class="actions">
                                <a href="javascript:" onclick="icms.photos.remove(<?php echo $photo['id']; ?>)"><?php echo LANG_DELETE; ?></a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>

        <div class="preview_template block" style="display:none">
            <div class="thumb"><img src="" border="0" /></div>
            <div class="info">
                <div class="title">
                    <?php echo html_input('text', '', '', array('placeholder'=>LANG_PHOTOS_PHOTO_TITLE)); ?>
                </div>
                <div class="actions">
                    <a href="javascript:"><?php echo LANG_DELETE; ?></a>
                </div>
            </div>
        </div>

        <div id="album-photos-uploader"></div>

        <script>
            <?php echo $this->getLangJS('LANG_SELECT_UPLOAD', 'LANG_DROP_TO_UPLOAD', 'LANG_CANCEL', 'LANG_ERROR'); ?>
            icms.photos.createUploader('<?php echo $this->href_to('upload'); ?><?php echo $id ? '/' . $id : ''; ?>');
        </script>


    </div>

</fieldset>