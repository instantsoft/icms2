<?php

if(!empty($page_title)) {
    $this->setPageTitle($page_title);
}

if(!empty($breadcrumbs)) {
    foreach ($breadcrumbs as $breadcrumb) {

        if(!is_array($breadcrumb)){
            $breadcrumb = [$breadcrumb, ''];
        }

        $this->addBreadcrumb(string_replace_keys_values($breadcrumb[0], $data), string_replace_keys_values($breadcrumb[1], $data));
    }
}

$this->renderForm($form, $data, [
    'action' => $action,
    'submit' => ['title' => LANG_SAVE],
    'method' => $this->controller->request->isAjax() ? 'ajax' : 'post'
], $errors);

?>
<?php ob_start(); ?>
<script>
function reloadDataGrid(form_data, result){

    icms.datagrid.loadRows();

    icms.forms.submitted = false;

    icms.modal.close();
}
</script>
<?php $this->addBottom(ob_get_clean()); ?>