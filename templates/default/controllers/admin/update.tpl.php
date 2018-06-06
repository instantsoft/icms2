<?php

    $this->setPageTitle(LANG_CP_SECTION_UPDATE);
    $this->addBreadcrumb(LANG_CP_SECTION_UPDATE, $this->href_to('update'));

?>

<h1><?php echo LANG_CP_SECTION_UPDATE; ?></h1>

<?php if ($update === cmsUpdater::UPDATE_CHECK_ERROR){ ?>
    <p><?php echo LANG_CP_UPDATE_CHECK_FAIL; ?></p>
<?php } ?>

<?php if ($update === cmsUpdater::UPDATE_NOT_AVAILABLE){ ?>
    <p><?php echo sprintf(LANG_CP_UPDATE_NOT_AVAILABLE, $current_version['version'], html_date($current_version['date'])); ?></p>
<?php } ?>

<?php if (!empty($update['version'])){ ?>

    <h2><?php printf(LANG_CP_UPDATE_AVAILABLE, $update['version']); ?></h2>
    <h3><?php echo LANG_CP_UPDATE_DATE; ?>: <?php echo html_date($update['date']); ?></h3>

    <?php if (!function_exists('curl_init')){ ?>

        <p>
            <?php echo LANG_CP_UPDATE_MANUAL_1; ?><br>
            <?php echo LANG_CP_UPDATE_MANUAL_2; ?>
        </p>
        <h3><a href="<?php echo $update['url'];?>"><?php echo LANG_CP_UPDATE_DOWNLOAD; ?></a></h3>

    <?php } else { ?>

        <h3>
            <a href="<?php echo $this->href_to('update', 'install');?>" onclick="return installUpdate(this)"><?php echo LANG_CP_UPDATE_INSTALL; ?></a>
            <span class="wait" style="display:none"><?php echo LANG_LOADING; ?></span>
        </h3>

        <script>
            function installUpdate(link){

                link = $(link);

                link.parent('h3').addClass('loading').find('.wait').show();
                link.hide();

                window.location.href = link.attr('href');

                return false;

            }
        </script>

    <?php } ?>

<?php } ?>