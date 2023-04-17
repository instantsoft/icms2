<?php $this->addTplJSName('jquery.scrollbar'); ?>
<ul class="news_targets_tabs">
    <li class="active_news" data-target="icms"><?php echo LANG_CP_DASHBOARD_NEWS_O; ?></li>
    <li data-target="icms_blogs"><?php echo LANG_CP_DASHBOARD_NEWS_A; ?></li>
    <li data-target="icms_docs"><?php echo LANG_CP_DASHBOARD_LINKS_DOCS; ?></li>
</ul>
<div id="icms_news_wrap">
    <div id="news-loading">
        <div class="spinner">
            <div class="bounce1"></div>
            <div class="bounce2"></div>
            <div class="bounce3"></div>
        </div>
    </div>
    <div class="icms_news_wrap scrollbar-macosx"></div>
</div>
<script>
    $(function() {
        $('.news_targets_tabs > li').on('click', function (){
            $('#icms_news_wrap .icms_news_wrap').last().html('');
            $('#news-loading').show();
            var __tab = this;
            $.post('<?php echo $this->href_to('load_icms_news'); ?>/'+$(this).data('target'), {}, function(result){
                $('.news_targets_tabs > li').removeClass('active_news');
                $(__tab).addClass('active_news');
                $('#news-loading').hide();
                $('#icms_news_wrap .icms_news_wrap').last().html(result);
                $('#icms_news_wrap .icms_news_wrap').scrollbar();
            });
        });
        $('.news_targets_tabs > li.active_news').trigger('click');
    });
</script>
