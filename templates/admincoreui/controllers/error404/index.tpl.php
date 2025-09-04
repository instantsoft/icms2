<?php
    $this->setPageTitle(ERR_PAGE_NOT_FOUND);
?>
<div class="row justify-content-center mt-3 mt-lg-5">
    <div class="col-sm-auto" id="data-wrap">
        <div class="px-3">
            <h1 class="display-3 error mb-3">404</h1>
            <h4><?php echo ERR_PAGE_NOT_FOUND; ?></h4>
            <p><a href="<?php echo href_to('admin'); ?>"><?php echo LANG_BACK_TO_HOME; ?></a></p>
        </div>
    </div>
</div>