<?php
    $this->setPageTitle(LANG_CP_CTYPE_FILTERS);
    $this->addBreadcrumb(LANG_CP_CTYPE_FILTERS);
?>

<p class="alert alert-info mt-4" role="alert">
    <?php printf(LANG_CP_FILTER_NO_TABLE, $this->href_to('ctypes', ['filters_enable', $ctype['id']]) . '?back=' . href_to_current()); ?>
</p>