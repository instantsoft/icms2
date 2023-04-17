<?php
    $this->setPageTitle(LANG_CP_OFICIAL_ADDONS);
    $this->addBreadcrumb(LANG_CP_OFICIAL_ADDONS);

    $this->addTplJSName([
        'datatree'
    ]);

    $this->addTplCSSName('datatree');

    $this->addMenuItems('admin_toolbar', $this->controller->getAddonsMenu());
?>

<?php if($error_text){ ?>

    <p class="alert alert-info mt-4"><?php echo $error_text; ?></p>

<?php return; } ?>

<?php

	$this->addToolButton([
        'class' => 'menu d-xl-none',
		'data'  => [
            'toggle' =>'quickview',
            'toggle-element' => '#left-quickview'
        ],
		'title' => LANG_CATEGORIES
	]);

    foreach ($datasets as $dataset) {
        $this->addToolButton([
            'class' => 'addons_dataset'.($dataset_id == $dataset['id'] ? ' active' : ''),
            'title' => $dataset['title'],
            'data'  => ['id' => $dataset['id']],
            'href'  => '#'
        ]);
    }

?>
<div class="row align-items-stretch addons_list_table mb-4">
    <div class="col-auto quickview-wrapper" id="left-quickview">
        <a class="quickview-toggle close" data-toggle="quickview" data-toggle-element="#left-quickview" href="#"><span aria-hidden="true">×</span></a>
        <div id="datatree" class="bg-white h-100 pt-3 pb-3 pr-3">
            <ul id="treeData" class="skeleton-tree">
                <li id="0.0" class="folder"><?php echo LANG_ALL; ?></li>
                <?php foreach($cats as $cat){ ?>
                    <li id="<?php echo $cat['id'];?>.0" class="folder"><?php echo $cat['title']; ?></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="col">
        <?php if ($this->isToolbar()){ ?>
            <nav class="cp_toolbar navbar navbar-light bg-light my-2 pl-2 rounded" id="addons_toolbar">
                <?php $this->toolbar(); ?>
            </nav>
        <?php } ?>
        <div id="filter_toolbar" class="row mb-4 align-items-center">
            <div class="col-lg-4">
                <?php echo html_input('text', 'title', '', array('id' => 'search_addon_title', 'placeholder' => LANG_CP_FIND_ADDON_TITLE, 'autocomplete' => 'off')); ?>
            </div>
            <div class="col-auto col-lg-6 mt-2 mt-lg-0" id="is_paid">
                <div class="custom-control custom-radio custom-control-inline">
                    <input checked class="custom-control-input" type="radio" id="a-all" name="is_paid" value="0">
                    <label class="custom-control-label" for="a-all"><?php echo LANG_ALL; ?></label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input class="custom-control-input" type="radio" id="a-pay" name="is_paid" value="1">
                    <label class="custom-control-label" for="a-pay"><?php echo LANG_CP_FIND_ADDON_FREE; ?></label>
                </div>
                <div class="custom-control custom-radio custom-control-inline mr-0">
                    <input class="custom-control-input" type="radio" id="a-nopay" name="is_paid" value="2">
                    <label class="custom-control-label" for="a-nopay"><?php echo LANG_CP_FIND_ADDON_BUY; ?></label>
                </div>
            </div>
            <div class="col-auto col-lg-2 text-muted text-right small mt-2 mt-lg-0" id="addons_count">
                <div class="spinner mr-2">
                    <div class="bounce1"></div>
                    <div class="bounce2"></div>
                    <div class="bounce3"></div>
                </div>
                <strong><?php echo LANG_CP_TOTAL; ?>: <span></span></strong>
            </div>
        </div>
        <div id="addons_list_wrap">
            <div id="addons_list"></div>
            <button type="button" class="btn btn-primary btn-block" style="display: none" id="show_more" data-to-first="<?php echo LANG_RETURN_TO_FIRST; ?>" data-more="<?php echo LANG_SHOW_MORE; ?>">
                <div class="spinner">
                    <div class="bounce1"></div>
                    <div class="bounce2"></div>
                    <div class="bounce3"></div>
                </div>
                <span><?php echo LANG_SHOW_MORE; ?></span>
            </button>
        </div>
    </div>
</div>

<script>
    var current_cat_id = <?php echo $cat_id; ?>;
    var current_ds_id  = <?php echo $dataset_id; ?>;
    var current_page   = 1;
    var has_next       = 0;
    var addons_count   = 0;
    var addon_title    = '';
    var addon_is_paid  = 0;
    $(function() {
        $('#addons_toolbar li:not(.menu):first a').addClass('active');
        $('#addons_list').on('click', 'a.button-video', function(e){
            var id = $(this).data('id');
            var frame = '<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="https://www.youtube.com/embed/'+id+'" frameborder="0" allowfullscreen></iframe></div>';
            icms.modal.openHtml(frame, '<?php echo LANG_CP_PACKAGE_VIDEO_TITLE; ?>', false, 'p-0');
            return false;
        });
        $('#addons_list').on('click', '.do_install', function(e){
            $(this).html($('#addons_count .spinner').clone().show());
            var install_form_btn = $(this).prev().find('input[type=submit]');
            if(install_form_btn.length > 0){
                $(this).prev().find('input[type=submit]').trigger('click');
                return false;
            }
        });
        $('#search_addon_title').on('keyup', function(e){
            if(e.keyCode==13){
                addon_title = $(this).val();
                current_page = 1;
                loadAddons();
            }
        });
        $('#is_paid input').on('click', function(e){
            addon_is_paid = $(this).val();
            current_page = 1;
            loadAddons();
        });
        $('#addons_toolbar .addons_dataset a').on('click', function(e){
            current_ds_id = $(this).data('id');
            $('#addons_toolbar a').removeClass('active').addClass('disabled');
            $(this).css('transition', 'none').addClass('active').closest('li').addClass('is-busy text-primary');
            current_page = 1;
            loadAddons();
            return false;
        });
        $('#show_more').on('click', function(e){
            if(has_next == 1){
                $('.spinner', this).show();
                $('span', this).hide();
                loadAddons(true);
            } else {
                $('body,html').animate({
                    scrollTop: +$('#wrapper').offset().top-56
                }, 500);
            }
            return false;
        });
        $('#datatree').dynatree({
            onPostInit: function(isReloading, isError){
                var path = $.cookie('icms[addons_tree_path]');
                if (!path) { path = '/0.0'; }
                $('#datatree').dynatree('getTree').loadKeyPath(path, function(node, status){
                    if(status == 'loaded') {
                        node.expand();
                    }else if(status == 'ok') {
                        node.activate();
                        node.expand();
                    }
                });
            },
            onActivate: function(node){
                node.expand();
                $.cookie('icms[addons_tree_path]', node.getKeyPath(), {expires: 7, path: '/'});
                var key = node.data.key.split('.');
                current_cat_id = key[0];
                current_page = 1;
                loadAddons();
            }
        });
    });
    function loadAddons(is_append){
        is_append = is_append || false;
        if(!is_append){
            $('#show_more').hide().find('span').html($('#show_more').data('more'));
        }
        $('#addons_count .spinner').show();
        $('#addons_count strong').hide();
        $('#is_paid input').prop('disabled', true);
        $.post('<?php echo $this->href_to('addons_list'); ?>', {
            dataset_id: current_ds_id,
            cat_id: current_cat_id,
            is_paid: addon_is_paid,
            title: addon_title,
            page: current_page
        }, function(result){
            $('#is_paid input').prop('disabled', false);
            $('#show_more .spinner').hide();
            $('#show_more span').show();
            $('#addons_count strong').show();
            $('#addons_count .spinner').hide();
            $('#addons_toolbar .addons_dataset').removeClass('is-busy text-primary').find('a').removeClass('disabled');
            if(!is_append){
                $('#addons_list').html(result);
            } else {
                $('#addons_list').append(result);
            }
            if(has_next == 1){
                $('#show_more').show();
                current_page += 1;
            } else {
                if(current_page > 1){
                    $('#show_more').show().find('span').html($('#show_more').data('to-first'));
                } else {
                    $('#show_more').hide();
                }
                current_page = 1;
            }
            $('#addons_count').find('span').html(addons_count);
            icms.modal.bind('a.ajax-modal');
        });
    };
</script>