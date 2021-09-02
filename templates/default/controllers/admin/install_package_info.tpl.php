<?php

    $this->addTplJSName([
        'jquery-cookie',
        'datatree'
        ]);
    $this->addTplCSSName('datatree');

    $this->setPageTitle(LANG_CP_INSTALL_PACKAGE);
    $this->addBreadcrumb(LANG_CP_INSTALL_PACKAGE);

	$this->addToolButton(array(
		'class'  => 'help',
        'title'  => LANG_HELP,
        'target' => '_blank',
        'href'   => LANG_HELP_URL_INSTALL
    ));

    // Зависимости удовлетворены
    $depends_pass = true;

    if(!empty($manifest['notice_system_files'])){
        cmsUser::addSessionMessage($manifest['notice_system_files'], 'error');
    }

?>

<h1><?php echo LANG_CP_INSTALL_PACKAGE_INFO; ?></h1>

<div class="cp_toolbar">
    <?php $this->toolbar(); ?>
</div>

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
                            <a rel="noopener noreferrer" class="url" href="<?php echo $manifest['author']['url']; ?>" target="_blank"></a>
                        <?php } ?>
                    </p>
                    <?php } ?>

                </fieldset>
                <?php } ?>

                <?php if (isset($manifest['package'])) { ?>
                <fieldset>

                    <legend><?php echo LANG_CP_PACKAGE_TYPE; ?></legend>

                    <p><?php echo $manifest['package']['type_hint']; ?>
                    <?php if (!empty($manifest['package']['installed_version'])) { ?>
                        <?php echo ' '.$manifest['package']['installed_version'].' => '.$manifest['version']['major'].'.'.$manifest['version']['minor'].'.'.$manifest['version']['build']; ?>
                    <?php } ?>
                    </p>

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
                        <?php if (isset($manifest['depends']['package'])) { ?>
                            <li>
                                <?php echo LANG_CP_PACKAGE_DEPENDS_PACKAGE; ?>:
                                <?php echo html_bool_span($manifest['depends']['package'], $manifest['depends_results']['package']); ?>
                            </li>
                            <?php if (!$manifest['depends_results']['package']){ $depends_pass = false; } ?>
                        <?php } ?>
                        <?php if (isset($manifest['depends']['dependent_type'])) { ?>
                            <li>
                                <?php echo sprintf(LANG_CP_PACKAGE_DEPENDENT_TYPE, string_lang('LANG_CP_PACKAGE_DEPENDENT_'.$manifest['depends']['dependent_type']), $manifest['depends']['dependent_url'], $manifest['depends']['dependent_title']); ?>:
                                <?php echo html_bool_span(($manifest['depends_results']['dependent_type'] ? LANG_CP_INSTALLED : LANG_CP_NOT_INSTALLED), $manifest['depends_results']['dependent_type']); ?>
                            </li>
                            <?php if (!$manifest['depends_results']['dependent_type']){ $depends_pass = false; } ?>
                        <?php } ?>
                        <?php if (!empty($manifest['depends_results']['dependent_type']) && isset($manifest['depends']['dependent_version'])) { ?>
                            <li>
                                <?php echo LANG_CP_PACKAGE_DEPENDS_PACKAGE; ?> <a href="<?php echo $manifest['depends']['dependent_url']; ?>" target="_blank" rel="noopener noreferrer"><?php echo $manifest['depends']['dependent_title']; ?></a>:
                                <?php echo html_bool_span($manifest['depends']['dependent_version'], $manifest['depends_results']['dependent_version']); ?>
                            </li>
                            <?php if (!$manifest['depends_results']['dependent_version']){ $depends_pass = false; } ?>
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

                    <script>
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
            <?php echo html_button(LANG_CANCEL, 'cancel', "location.href='".$this->href_to('addons_list')."'"); ?>
        </div>

    </form>

</div>
