<!--noindex-->
<?php if(!$is_domain_banned){ ?>

    <?php $this->addTplJSName('jquery-cookie'); ?>

    <div class="icms-body-toolbox mt-4">
        <h1><?php echo LANG_REDIRECT_H1; ?></h1>
        <div class="position-relative ml-2 d-flex">
            <div class="custom-control custom-switch">
                <input name="accept" type="checkbox" class="custom-control-input" id="accept_redirect" value="1">
                <label class="custom-control-label" for="accept_redirect"><?php echo LANG_REDIRECT_HINT4; ?></label>
            </div>
        </div>
    </div>

    <div class="alert alert-warning mt-4" role="alert">
        <div class="alert-heading h4"><?php printf(LANG_REDIRECT_HINT1, $host, $sitename, html($url, false), html($original_url, false)); ?></div>
        <p class="my-2"><?php printf(LANG_REDIRECT_HINT2, parse_url($original_url, PHP_URL_HOST)); ?></p>
        <p><?php echo LANG_REDIRECT_HINT3; ?> <b><span id="timer"></span> <?php echo LANG_SECOND10; ?></b></p>
        <hr>
        <p class="mb-0">
            <?php printf(LANG_REDIRECT_YOUR_SAFETY, $sitename); ?>
        </p>
    </div>

    <?php ob_start(); ?>
    <script>
        window.opener = null;
        $(function () {
            var timer    = $('#timer');
            var delay    = <?php echo intval($redirect_time); ?>;
            var location = "<?php html($url); ?>";
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
    <?php $this->addBottom(ob_get_clean()); ?>
<?php } else { ?>
    <h1 class="my-4"><?php echo LANG_REDIRECT_SUSPICIOUS_LINK; ?></h1>

    <div class="alert alert-danger mt-4" role="alert">
        <p><?php echo LANG_REDIRECT_SUSPICIOUS_LINK_1; ?></p>
        <?php if(!$is_domain_in_black_list){ ?>
            <p><?php printf(LANG_REDIRECT_SUSPICIOUS_LINK_2, html($url, false)); ?></p>
        <?php } ?>
        <hr>
        <p class="mb-0">
            <?php printf(LANG_REDIRECT_YOUR_SAFETY, $sitename); ?>
        </p>
    </div>
<?php } ?>
<!--/noindex-->