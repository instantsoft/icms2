<?php if($api_id){ ?>

    <?php $this->addJS('//vk.com/js/api/openapi.js?122','',false); ?>

    <div id="vk_comments_browse"></div>

    <script type="text/javascript">
    window.onload = function () {
        VK.init({
            apiId: '<?php echo $api_id; ?>',
            onlyWidgets: true
        });
        VK.Widgets.CommentsBrowse('vk_comments_browse', <?php echo $vk_params; ?>);
    };
    </script>

<?php } else { ?>

    <p><?php echo LANG_COM_VK_NOAP_ID; ?></p>

<?php }