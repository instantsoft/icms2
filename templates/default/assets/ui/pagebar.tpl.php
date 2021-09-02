<?php if($has_next || (!$has_next && $page > 1)){ ?>
    <?php $this->addTplJSName('jquery-scroll'); ?>
    <?php $elemet_id = md5(microtime(true)); ?>
    <a class="show-more" id="<?php echo $elemet_id; ?>" href="<?php echo $base_url.((strpos($base_url, '?') !== false) ? '&' : '?').'page='.($has_next ? ($page+1) : 1); ?>" data-url="<?php echo isset($more_url) ? $more_url : $base_url; ?>" data-url-params="<?php html(isset($url_params) ? json_encode($url_params) : '{}'); ?>" data-first-page-url="<?php echo $base_url; ?>">
        <span data-to-first="<?php echo LANG_RETURN_TO_FIRST; ?>">
            <?php if($has_next){ echo LANG_SHOW_MORE; } else { echo LANG_RETURN_TO_FIRST; } ?>
        </span>
        <div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>
    </a>
    <script>
        $(function(){
            new icms.pagebar('#<?php echo $elemet_id; ?>', <?php echo $page; ?>, <?php if($has_next){ echo 'true'; } else { echo 'false'; } ?>, <?php if(!empty($is_modal)){ echo 'true'; } else { echo 'false'; } ?>);
        });
    </script>
<?php } ?>