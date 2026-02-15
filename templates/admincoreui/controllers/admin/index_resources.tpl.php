<div class="row">
    <div class="col-md-6 mb-3 mb-md-0">
        <a class="btn btn-block btn-light" href="https://instantcms.ru/donate.html" target="_blank"><?php echo LANG_CP_DASHBOARD_LINKS_DONATE; ?></a>
    </div>
    <div class="col-md-6">
        <a class="btn btn-block btn-warning" href="https://instantcms.ru/sponsorship.html" target="_blank"><?php echo LANG_CP_DASHBOARD_LINKS_SPONSORS; ?></a>
    </div>
</div>
<!--<h4 class="mb-3"><?php echo LANG_CP_DASHBOARD_PREMIUM; ?></h4>
<div class="mb-3">
    <i class="float-left mr-3 mt-2 h3">
        <?php html_svg_icon('solid', 'play-circle'); ?>
    </i>
    <h5 class="mb-1">
        <a class="text-white" href="https://instantvideo.ru/software/instantvideo2.html" target="_blank">
            InstantVideo
        </a>
    </h5>
    <p class="m-0 text-light"><?php echo LANG_CP_DASHBOARD_INVIDEO_HINT; ?></p>
</div>
<div>
    <i class="float-left mr-3 mt-2 h3">
        <?php html_svg_icon('solid', 'map-marked-alt'); ?>
    </i>
    <h5 class="mb-1">
        <a class="text-white" href="https://instantcms.ru/blogs/instantsoft/instantmaps-dlja-instantcms-2.html" target="_blank">
            InstantMaps
        </a>
    </h5>
    <p class="m-0 text-light"><?php echo LANG_CP_DASHBOARD_INMAPS_HINT; ?></p>
</div>-->
<div id="icms_sponsorship" class="d-none">
    <hr>
    <div class="icms_sponsorship__wrap"></div>
</div>
<?php ob_start(); ?>
<script>
    $(function() {
        let icms_sponsorship = $('#icms_sponsorship');

        let index = 0;
        let delay = 15000;
        let timer = null;
        let ads   = [];

        $.post('<?php echo $this->href_to('load_icms_sponsorship'); ?>', {}, function(result) {
            if (result.length === 0) {
                return;
            }
            ads = result;
            icms_sponsorship.removeClass('d-none');
            showAd(index);
            if (result.length > 1) {
                startRotation();
                bindMouseenter();
            }
        });

        function renderAd(ad) {

            let imgHtml = '';
            if (ad.img) {
                imgHtml = `<a class="text-white" href="${ad.url}" target="_blank"><img src="${ad.img}" alt="" class="mb-2 img-fluid"></a>`;
            }

            let titleHtml = '';
            if (ad.title) {
                titleHtml = `<h5 class="mb-1"><a class="text-white" href="${ad.url}" target="_blank">${ad.title}</a></h5>`;
            }

            return `
                ${imgHtml}
                ${titleHtml}
                <p class="m-0 text-light">${ad.desc}</p>
            `;
        }

        function showAd(i) {
            $('.icms_sponsorship__wrap', icms_sponsorship).fadeOut('fast', function () {
                $(this).html(renderAd(ads[i])).fadeIn('fast');
            });
        }

        function startRotation() {
            timer = setInterval(function () {
                index = (index + 1) % ads.length;
                showAd(index);
            }, delay);
        }

        function stopRotation() {
            clearInterval(timer);
            timer = null;
        }

        function bindMouseenter() {
            icms_sponsorship.on('mouseenter', function () {
                stopRotation();
            }).on('mouseleave', function () {
                if (!timer) {
                    startRotation();
                }
            });
        }
    });
</script>
<?php $this->addBottom(ob_get_clean()); ?>