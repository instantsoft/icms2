<!DOCTYPE html>
<html lang="<?php echo cmsCore::getLanguageName(); ?>">
    <head>
        <title><?php $this->title(); ?></title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="<?php echo cmsForm::getCSRFToken(); ?>" />
        <meta name="generator" content="InstantCMS" />
        <?php $this->addMainTplCSSName([
            'vendors/bootstrap/bootstrap.min',
        ]); ?>
        <?php $this->addMainTplJSName('jquery', true); ?>
        <?php if ($config->debug && cmsUser::isAdmin()) { ?>
            <?php $this->addTplCSSName('debug'); ?>
        <?php } ?>

        <?php $this->head(); ?>

    </head>
    <body id="<?php echo $device_type; ?>_device_type">

        <?php foreach ($rows as $row) { ?>

            <?php if((!$row['has_body'] || ($row['has_body'] && !$this->isBody())) && !$this->hasWidgetsOn($row['positions'])){ ?>
                <?php continue; ?>
            <?php } ?>

            <?php if ($row['options']['parrent_tag']) { ?>
                <<?php echo $row['options']['parrent_tag']; ?><?php if ($row['options']['parrent_tag_class']) { ?> class="<?php echo $row['options']['parrent_tag_class']; ?>"<?php } ?>>
            <?php } ?>
            <?php if ($row['options']['container']) { ?>
                <div class="<?php echo $row['options']['container']; ?> <?php echo $row['options']['container_tag_class']; ?>">
            <?php } ?>

            <?php
                $row_class = ['row'];
                if ($row['options']['no_gutters']) {
                    $row_class[] = 'no-gutters';
                }
                if ($row['options']['vertical_align']) {
                    $row_class[] = $row['options']['vertical_align'];
                }
                if ($row['options']['horizontal_align']) {
                    $row_class[] = $row['options']['horizontal_align'];
                }
            ?>

            <div class="<?php echo implode(' ', $row_class); ?>">
            <?php foreach ($row['cols'] as $col) { ?>
                <?php if($col['is_body']){ ?>
                    <?php if(!$this->isBody()){ ?>
                        <?php continue; ?>
                    <?php } ?>
                <?php } else { ?>
                    <?php if(!$this->hasWidgetsOn($col['name'])){ ?>
                        <?php continue; ?>
                    <?php } ?>
                <?php } ?>

                <?php
                    $col_class = [$col['options']['default_col_class']];
                    if ($col['options']['default_order']) {
                        $col_class[] = 'order-'.$col['options']['default_order'];
                    }
                ?>

                <div class="<?php echo implode(' ', $col_class); ?>">

                    <?php if(!empty($col['rows']['before'])){ ?>

                    <?php } ?>
                    <?php if($col['is_body']){ ?>
                        <?php if ($this->isBody()){ ?>
                            <?php if ($config->show_breadcrumbs && $core->uri && $this->isBreadcrumbs()){ ?>
                                <div id="breadcrumbs">
                                    <?php $this->breadcrumbs(array('strip_last'=>false)); ?>
                                </div>
                            <?php } ?>
                            <div id="controller_wrap">
                                <?php $this->block('before_body'); ?>
                                <?php $this->body(); ?>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <?php $this->widgets($col['name']); ?>
                    <?php } ?>
                    <?php if(!empty($col['rows']['after'])){ ?>

                    <?php } ?>
                </div>
            <?php } ?>
            </div>

            <?php if ($row['options']['container']) { ?>
                </div>
            <?php } ?>
            <?php if ($row['options']['parrent_tag']) { ?>
                </<?php echo $row['options']['parrent_tag']; ?>>
            <?php } ?>
        <?php } ?>

        <?php if ($config->debug && cmsUser::isAdmin()){ ?>
            <?php $this->renderAsset('ui/debug', array('core' => $core)); ?>
        <?php } ?>
        <?php $messages = cmsUser::getSessionMessages(); ?>
        <script>
            $(function(){
            <?php if ($messages){ ?>
                <?php foreach($messages as $message){ ?>
                    toastr.<?php echo $message['class']; ?>('<?php echo $message['text']; ?>');
                 <?php } ?>
            <?php } ?>
            });
        </script>
        <?php $this->bottom(); ?>
    </body>
</html>