<?php

    $this->addTplJSNameFromContext([
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
<div class="card">
    <div class="card-body">
<?php } ?>

<form id="file_list_wrap">
<?php if(empty($hide_delete_hint)){ ?>
    <div class="alert alert-warning" role="alert">
        <?php echo LANG_CP_PACKAGE_DELETE_HINT; ?>
    </div>
<?php } ?>
    <fieldset>
        <?php if(empty($hide_title)){ ?>
            <legend><?php echo LANG_CP_PACKAGE_CONTENTS; ?></legend>
        <?php } ?>
        <div id="tree" class="no-overflow">
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
    <a class="btn btn-success mt-4" href="<?php echo $this->href_to($type); ?>"><?php echo LANG_CONTINUE; ?></a>
<?php } ?>
<?php if(empty($hide_title)){ ?>
    </div>
</div>
<?php } ?>
