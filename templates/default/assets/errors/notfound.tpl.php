<?php
    $config = cmsConfig::getInstance();
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo ERR_PAGE_NOT_FOUND; ?></title>
    <link type="text/css" rel="stylesheet" href="<?php echo $config->root; ?>templates/<?php echo $this->name; ?>/css/theme-errors.css">
</head>
<body>

    <div id="error404">
        <h1>404</h1>
        <h2><?php echo ERR_PAGE_NOT_FOUND; ?></h2>
        <p><a href="<?php echo $config->host; ?>"><?php echo LANG_BACK_TO_HOME; ?></a></p>
    </div>

</body>
