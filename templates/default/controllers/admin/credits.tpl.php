<?php

    $this->setPageTitle(LANG_CP_3RDPARTY_CREDITS);

    $this->addBreadcrumb(LANG_CP_3RDPARTY_CREDITS, $this->href_to('credits'));

?>

<pre>
<?php echo string_make_links($credits_text); ?>
</pre>