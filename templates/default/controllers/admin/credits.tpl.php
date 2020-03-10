<?php
    $this->setPageTitle(LANG_CP_3RDPARTY_CREDITS);
    $this->addBreadcrumb(LANG_CP_3RDPARTY_CREDITS, $this->href_to('credits'));
?>

<div class="card">
    <div class="card-body">
<pre>
<?php echo string_make_links($credits_text); ?>
</pre>
    </div>
</div>

