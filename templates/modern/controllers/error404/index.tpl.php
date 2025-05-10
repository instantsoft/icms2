<?php
    $this->setPageTitle(ERR_PAGE_NOT_FOUND);
?>
<div class="row justify-content-center my-3">
    <div class="col-md-6 align-self-center">
        <img src="<?php echo $this->getTemplateFilePath('images/404.svg', true); ?>" alt="404">
    </div>
    <div class="col-md-6 align-self-center" id="data-wrap">
        <h1 class="display-1">404</h1>
        <h2><?php echo ERR_PAGE_NOT_FOUND; ?></h2>
        <?php if(cmsCore::isControllerExists('search') && cmsController::enabled('search')){ ?>
            <form action="<?php echo href_to('search'); ?>" method="get" class="my-4">
                <div class="input-group">
                    <?php echo html_input('text', 'q', '', array('placeholder'=> ERR_SEARCH_QUERY_INPUT)); ?>
                    <div class="input-group-append">
                        <button type="submit" name="submit" class="btn btn-secondary"><?php echo ERR_SEARCH_TITLE; ?></button>
                    </div>
                </div>
            </form>
        <?php } ?>
        <a class="btn btn-primary mt-3" href="<?php echo href_to_home(); ?>"><?php echo LANG_BACK_TO_HOME; ?></a>
    </div>
</div>