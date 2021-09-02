<!--noindex-->
<?php if(!$is_domain_banned){ ?>

    <?php $this->addTplJSName('jquery-cookie'); ?>

    <?php if($user->is_logged){ ?>
        <div class="accept_redirect">
            <label><input name="accept" id="accept_redirect" type="checkbox" value="1"> <?php echo LANG_REDIRECT_HINT4; ?></label>
        </div>
    <?php } ?>
    <h1><?php echo LANG_REDIRECT_H1; ?></h1>

    <p class="redirect"><?php printf(LANG_REDIRECT_HINT1, $host, $sitename, $url, $original_url); ?></p>
    <p class="redirect"><?php printf(LANG_REDIRECT_HINT2, parse_url($original_url, PHP_URL_HOST)); ?></p>
    <p class="redirect">
        <?php echo LANG_REDIRECT_HINT3; ?> <b><span id="timer"></span> <?php echo LANG_SECOND10; ?></b>
    </p>
    <script>
        window.opener = null;
        $(function () {
            var timer    = $('#timer');
            var delay    = +<?php echo $redirect_time; ?>;
            var location = '<?php echo $url; ?>';
            $(timer).html(delay);
            var interval = setInterval(function () {
                if(delay) { delay--; }
                $(timer).html(delay);
                if(delay <= 0){
                    clearInterval(interval);
                    window.location.href=location;
                }
            }, 1000);
            $('#accept_redirect').on('click', function(){
                if($(this).is(':checked')){
                    $.cookie('icms[allow_redirect]', 'allow', {expires: 30, path: '/'});
                } else {
                    $.cookie('icms[allow_redirect]', null, {expires: 30, path: '/'});
                }
            });
        });
    </script>
<?php } else { ?>
    <h1><?php echo LANG_REDIRECT_SUSPICIOUS_LINK; ?></h1>
    <p class="redirect_warning"><?php echo LANG_REDIRECT_SUSPICIOUS_LINK_1; ?></p>
    <?php if(!$is_domain_in_black_list){ ?>
        <p class="redirect_warning_visit"><?php printf(LANG_REDIRECT_SUSPICIOUS_LINK_2, $url); ?></p>
    <?php } ?>
<?php } ?>
    <p><i><?php printf(LANG_REDIRECT_YOUR_SAFETY, $sitename); ?></i></p>
<!--/noindex-->
