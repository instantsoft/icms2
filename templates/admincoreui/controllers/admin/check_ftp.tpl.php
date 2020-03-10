<div id="check_ftp_wrap">
    <div class="alert alert-info" role="alert">
        <?php echo sprintf(LANG_CP_FTP_CONNECTION_INFO, $ftp_path); ?>
    </div>
    <?php if(!$errors){ ?>
        <div class="alert alert-success" role="alert">
            <?php echo LANG_CP_FTP_NO_ERRORS; ?>
        </div>
    <?php } else { ?>
        <div class="alert alert-danger" role="alert">
            <?php foreach ($errors as $key => $error) { ?>
                <div class="errors_available_<?php echo $key; ?>">
                    <?php echo $error; ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
    <?php if($file_list['dirs'] || $file_list['files']) { ?>
        <h4>
            <?php echo LANG_CP_FTP_ROOT_LIST_TITLE; ?>
        </h4>
        <ul class="dynatree-container">
            <?php if($file_list['dirs']) { ?>
                <?php foreach ($file_list['dirs'] as $dir) { ?>
                    <li>
                        <span class="dynatree-node dynatree-ico-cf">
                            <span class="dynatree-icon"></span>
                            <span class="dynatree-title"><?php echo $dir; ?></span>
                        </span>
                    </li>
                <?php } ?>
            <?php } ?>
            <?php if($file_list['files']) { ?>
                <?php foreach ($file_list['files'] as $file) { ?>
                    <li class="">
                        <span class="dynatree-node">
                            <span class="dynatree-icon"></span>
                            <span class="dynatree-title"><?php echo $file; ?></span>
                        </span>
                    </li>
                <?php } ?>
            <?php } ?>
        </ul>
    <?php } ?>
</div>