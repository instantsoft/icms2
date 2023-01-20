<!DOCTYPE html>
<html>
<head>
	<title><?php echo LANG_PAGE_TITLE; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link type="text/css" rel="stylesheet" href="css/sweetalert2.min.css">
    <link type="text/css" rel="stylesheet" href="css/styles.css">
    <script src="js/jquery.js"></script>
    <script src="js/install.js"></script>
    <script src="js/sweetalert2.all.min.js"></script>
</head>
<body>

    <div id="layout">

        <div id="header" class="section">
            <div class="logo">
                <span><?php echo LANG_INSTALLATION_WIZARD; ?></span>
                <div id="langs">
                    <?php foreach($langs as $id){ ?>
                        <a class="language<?php if ($id===$lang) { ?> selected<?php } ?>" href="?lang=<?php echo $id; ?>">
                            <img src="languages/<?php echo $id; ?>/flag.svg" alt="<?php echo strtoupper($id); ?>" title="<?php echo strtoupper($id); ?>">
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>

        <table id="main" class="section">
            <tr>

                <td id="sidebar" valign="top">
                    <ul id="steps">
                        <?php foreach($steps as $num => $step) { ?>
                            <li id="<?php echo $step['id']; ?>" <?php if($num==$current_step) { ?>class="active"<?php } ?>>
                                <?php echo $num+1; ?>. <?php echo $step['title']; ?>
                            </li>
                        <?php } ?>
                    </ul>
                </td>

                <td id="body" valign="top">
                    <div class="page"><?php echo $step_html; ?></div>
                </td>

            </tr>
        </table>

        <div id="footer" class="section">
            <div id="copyright">
                <a href="https://instantcms.ru/" target="_blank">InstantCMS</a> &copy; 2008 – <?php echo date('Y'); ?>
            </div>
            <div id="version">
                <?php echo get_version(); ?>
            </div>
        </div>

    </div>

    <script>
        var current_step = <?php echo $current_step; ?>;
        var LANG_ERROR = '<?php echo LANG_ERROR; ?>';
        var LANG_MANUAL = '<?php echo LANG_MANUAL; ?>';
    </script>

</body>
</html>
