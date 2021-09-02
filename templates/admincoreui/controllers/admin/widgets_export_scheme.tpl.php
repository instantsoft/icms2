<?php
if($is_rows){
    $this->renderForm($form, $data, array(
        'action' => $this->href_to('widgets', ['export_scheme', $from_template]),
        'method' => 'ajax'
    ), $errors); ?>
<script>
    function successExport(form_data, result){
        icms.modal.close();
        var file = new File([result.yaml], result.filename, {type: "application/x-yaml;charset=utf-8"});
        saveAs(file);
    }
</script>
<?php } else { ?>

    <p class="alert alert-danger mb-0"><?php echo LANG_CP_WIDGETS_ROW_NONE; ?></p>

<?php } ?>
