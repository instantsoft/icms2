<ul class="nav nav-tabs news_targets_tabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" data-target="icms" href="#icms" role="tab" aria-controls="icms">
            <?php echo LANG_CP_DASHBOARD_NEWS_O; ?>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" data-target="icms_blogs" href="#icms_blogs" role="tab" aria-controls="icms_blogs">
            <?php echo LANG_CP_DASHBOARD_NEWS_A; ?>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" data-target="icms_docs" href="#icms_blogs" role="tab" aria-controls="icms_blogs">
            <?php echo LANG_CP_DASHBOARD_LINKS_DOCS; ?>
        </a>
    </li>
</ul>

<div class="need-scrollbar pr-2" id="icms_news_wrap" role="tabpanel"></div>

<script>
    $(function() {
        $('.news_targets_tabs > li > a').on('click', function (){
            var _this = this;
            icms.admin.dbCardSpinner(this).show();
            $.post('<?php echo $this->href_to('load_icms_news'); ?>/'+$(this).data('target'), {}, function(result){
                icms.admin.dbCardSpinner(_this).fadeOut();
                $('#icms_news_wrap').last().html(result);
            });
        });
        $('.news_targets_tabs > li > a.active').trigger('click');
    });
</script>