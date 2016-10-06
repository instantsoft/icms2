<?php $this->addJS('//vk.com/js/api/openapi.js?122','', false); ?>

<script type="text/javascript">
    VK.init({
        apiId: '<?php echo $api_id; ?>',
        onlyWidgets: true
    });
</script>

<div id="<?php echo $page_id; ?>"></div>

<script type="text/javascript">
    VK.Widgets.Comments('<?php echo $page_id; ?>', <?php echo $vk_params; ?>, '<?php echo $page_id; ?>');
</script>