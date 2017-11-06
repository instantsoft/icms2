<div id="check_ftp_wrap">
    <div class="connection_info">
        <?php echo sprintf(LANG_CP_FTP_CONNECTION_INFO, $ftp_path); ?>
    </div>
    <?php if(!$errors){ ?>
        <div class="no_errors">
            <?php echo LANG_CP_FTP_NO_ERRORS; ?>
        </div>
    <?php } else { ?>
        <div class="errors_available">
            <?php foreach ($errors as $key => $error) { ?>
                <div class="errors_available_<?php echo $key; ?>">
                    <?php echo $error; ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
    <?php if($file_list['dirs'] || $file_list['files']) { ?>
        <div class="root_list_title">
            <?php echo LANG_CP_FTP_ROOT_LIST_TITLE; ?>
        </div>
        <div class="root_list">
            <?php if($file_list['dirs']) { ?>
                <?php foreach ($file_list['dirs'] as $dir) { ?>
                    <div class="ftp_folder"><?php echo $dir; ?></div>
                <?php } ?>
            <?php } ?>
            <?php if($file_list['files']) { ?>
                <?php foreach ($file_list['files'] as $file) { ?>
                    <div class="ftp_file"><?php echo $file; ?></div>
                <?php } ?>
            <?php } ?>
        </div>
    <?php } ?>
</div>