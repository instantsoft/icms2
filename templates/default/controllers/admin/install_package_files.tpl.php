<?php

    $this->addTplJSNameFromContext([
        'jquery-cookie',
        'datatree'
        ]);
    $this->addTplCSSNameFromContext('datatree');

    if(empty($hide_title)){

        $title = sprintf(string_lang('LANG_CP_PACKAGE_DELETE_'.$type), $addon_title);

        $this->setPageTitle($title);
        $this->addBreadcrumb($title);

    }

?>

<?php if(empty($hide_title)){ ?>
    <h1><?php echo $title; ?></h1>
<?php } ?>

<form id="file_list_wrap">
<?php if(empty($hide_delete_hint)){ ?>
    <p><?php echo LANG_CP_PACKAGE_DELETE_HINT; ?></p>
<?php } ?>
    <fieldset>
        <legend><?php echo LANG_CP_PACKAGE_CONTENTS; ?></legend>
        <div id="tree">
            <?php echo html_array_to_list($files); ?>
        </div>
    </fieldset>
</form>
<script>
    $(function(){
        $("#tree").dynatree({
            minExpandLevel: 5,
            expand: true
        });
    });
</script>

<?php if(empty($hide_continue_btn)){ ?>
    <p>
        <?php echo html_button(LANG_CONTINUE, 'continue', "location.href='".$this->href_to($type)."'"); ?>
    </p>
<?php } ?>