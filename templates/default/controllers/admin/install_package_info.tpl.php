<?php

    $this->addJS('templates/default/js/jquery-ui.js');
    $this->addJS('templates/default/js/jquery-cookie.js');
    $this->addJS('templates/default/js/datatree.js');
    $this->addCSS('templates/default/css/datatree.css');

    $this->setPageTitle(LANG_CP_INSTALL_PACKAGE);
    $this->addBreadcrumb(LANG_CP_INSTALL_PACKAGE);

    // Зависимости удовлетворены
    $depends_pass = true;

?>

<h1><?php echo LANG_CP_INSTALL_PACKAGE_INFO; ?></h1>

<h2>
    <?php html($manifest['info']['title']); ?>
    <span>
        v<?php html($manifest['version']['major']); ?>.<?php html($manifest['version']['minor']); ?>.<?php html($manifest['version']['build']); ?>
    </span>
</h2>

<div id="cp_package_info">

    <form action="<?php echo $this->href_to('install', 'ftp'); ?>" method="post">

        <div class="info">

                <?php if (isset($manifest['author'])) { ?>
                <fieldset>

                    <legend><?php echo LANG_CP_PACKAGE_AUTHOR; ?></legend>

                    <?php if (isset($manifest['author']['name'])) { ?>
                    <p class="author">
                        <span><?php html($manifest['author']['name']); ?></span>
                        <?php if (isset($manifest['author']['email'])) { ?>
                            <a class="mail" href="mailto:<?php echo $manifest['author']['email']; ?>"></a>
                        <?php } ?>
                        <?php if (isset($manifest['author']['url'])) { ?>
                            <a class="url" href="<?php echo $manifest['author']['url']; ?>" target="_blank"></a>
                        <?php } ?>
                    </p>
                    <?php } ?>

                </fieldset>
                <?php } ?>

                <?php if (isset($manifest['description'])) { ?>
                <fieldset>

                    <legend><?php echo LANG_CP_PACKAGE_DESCRIPTION; ?></legend>

                    <p>
                        <?php echo nl2br(implode("\n", $manifest['description']['text'])); ?>
                    </p>

                </fieldset>
                <?php } ?>

                <?php if (isset($manifest['depends'])) { ?>
                <fieldset>

                    <legend><?php echo LANG_CP_PACKAGE_DEPENDS; ?></legend>

                    <ul class="flat">
                        <?php if (isset($manifest['depends']['core'])) { ?>
                            <li>
                                <?php echo LANG_CP_PACKAGE_DEPENDS_CORE; ?>:
                                <?php echo html_bool_span($manifest['depends']['core'], $manifest['depends_results']['core']); ?>
                            </li>
                            <?php if (!$manifest['depends_results']['core']){ $depends_pass = false; } ?>
                        <?php } ?>
                    </ul>


                </fieldset>
                <?php } ?>

                <?php if ($manifest['contents']) { ?>
                <fieldset>

                    <legend><?php echo LANG_CP_PACKAGE_CONTENTS; ?></legend>

                    <div id="tree">
                        <?php echo html_array_to_list($manifest['contents']); ?>
                    </div>

                    <script type="text/javascript">
                        $(function(){
                            $("#tree").dynatree({
                                expand: true
                            });
                        });
                    </script>

                </fieldset>
                <?php } ?>

        </div>

        <?php if (isset($manifest['info']['image'])) { ?>
            <div class="image">
                <img src="<?php echo $manifest['info']['image']; ?>" />
            </div>
        <?php } ?>

        <div class="buttons">
            <?php if ($depends_pass){ echo html_button(LANG_INSTALL, 'cancel', "location.href='".$this->href_to('install', 'ftp')."'", array('class'=>'button-submit')); } ?>
            <?php echo html_button(LANG_CANCEL, 'cancel', "location.href='".$this->href_to('')."'"); ?>
        </div>

    </form>

</div>
