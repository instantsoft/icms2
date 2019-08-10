<?php
    $config = cmsConfig::getInstance();
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo ERR_PAGE_NOT_FOUND; ?></title>
    <?php $this->addMainTplCSSName([
        'theme-errors'
        ]); ?>
    <?php
    $this->printCssTags();
    ?>
</head>
<body>

    <div id="error404">
        <h1>404</h1>
        <h2><?php echo ERR_PAGE_NOT_FOUND; ?></h2>
	    <form action="<?php echo href_to('search'); ?>" method="get">
	        <?php echo html_input('text', 'q', '', array('placeholder'=>ERR_SEARCH_QUERY_INPUT)); ?>
			<button type="submit" name="submit" class="button-submit"><?php echo ERR_SEARCH_TITLE; ?></button>
	    </form>
        <p><a href="<?php echo $config->root; ?>"><?php echo LANG_BACK_TO_HOME; ?></a></p>
    </div>

</body>
