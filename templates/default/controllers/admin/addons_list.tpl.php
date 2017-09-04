<?php
    $this->setPageTitle(LANG_CP_OFICIAL_ADDONS);
    $this->addBreadcrumb(LANG_CP_OFICIAL_ADDONS);

    $this->addJS('templates/default/js/jquery-cookie.js');
    $this->addJS('templates/default/js/datatree.js');
    $this->addCSS('templates/default/css/datatree.css');
?>

<?php if($error_text){ ?>

    <h1><?php echo LANG_CP_OFICIAL_ADDONS; ?></h1>
    <p><?php echo $error_text; ?></p>

<?php return; } ?>

<h1><?php echo LANG_CP_OFICIAL_ADDONS; ?></h1>

<?php

    foreach ($datasets as $dataset) {
        $this->addToolButton(array(
            'class' => 'addons_dataset'.($dataset_id == $dataset['id'] ? ' active' : ''),
            'title' => $dataset['title'],
            'data'  => array('id' => $dataset['id']),
            'href'  => '#'
        ));
    }

	$this->addToolButton(array(
		'class' => 'install right',
		'title' => LANG_CP_INSTALL_PACKAGE,
		'href'  => $this->href_to('install')
	));

?>
<table class="layout addons_list_table">
    <tr>
        <td class="sidebar" valign="top">
            <div id="datatree">
                <ul id="treeData" style="display: none">
                    <li id="0.0" class="folder"><?php echo LANG_ALL; ?></li>
                    <?php foreach($cats as $cat){ ?>
                        <li id="<?php echo $cat['id'];?>.0" class="folder"><?php echo $cat['title']; ?></li>
                    <?php } ?>
                </ul>
            </div>
        </td>
        <td class="main" valign="top">
            <div class="cp_toolbar" id="addons_toolbar">
                <?php $this->toolbar(); ?>
            </div>
            <div id="addons_list_wrap">
                <div id="addons_list"></div>
                <div id="show_more" data-to-first="<?php echo LANG_RETURN_TO_FIRST; ?>" data-more="<?php echo LANG_SHOW_MORE; ?>">
                    <?php echo LANG_SHOW_MORE; ?>
                </div>
                <div class="spinner">
                    <div class="bounce1"></div>
                    <div class="bounce2"></div>
                    <div class="bounce3"></div>
                </div>
            </div>
        </td>
    </tr>
</table>
<script>
    var current_cat_id = <?php echo $cat_id; ?>;
    var current_ds_id  = <?php echo $dataset_id; ?>;
    var current_page   = 1;
    var has_next       = 0;
    $(function() {
        $('#addons_list').on('click', '.button-video > a', function(e){
            var id = $(this).data('id');
            var frame = '<iframe width="640" height="480" src="http://www.youtube.com/embed/'+id+'" frameborder="0" allowfullscreen></iframe>'
            icms.modal.openHtml(frame, '<?php echo LANG_CP_PACKAGE_VIDEO_TITLE; ?>');
            return false;
        });
        $('#addons_list').on('click', '.button-install .do_install', function(e){
            $(this).html($('#addons_list_wrap #addons_list ~ .spinner').clone().show());
            $(this).prev().find('input[type=submit]').trigger('click');
            return false;
        });
        $('#addons_toolbar .addons_dataset a').on('click', function(e){
            current_ds_id = $(this).data('id');
            $('#addons_toolbar .addons_dataset').removeClass('active');
            $(this).parent().addClass('active');
            current_page = 1;
            loadAddons();
            return false;
        });
        $('#show_more').on('click', function(e){
            if(has_next == 1){
                $('#show_more').hide();
                loadAddons(true);
            } else {
                $('body,html').animate({
                    scrollTop: $('#wrapper').offset().top
                    }, 500
                );
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
            $('table.layout').css({height: ''});
            $('#show_more').hide().html($('#show_more').data('more'));
            $('#addons_list').html('');
        }
        $('#addons_list_wrap > .spinner').show();
        $.post('<?php echo $this->href_to('addons_list'); ?>', {dataset_id: current_ds_id, cat_id: current_cat_id, page: current_page}, function(result){
            $('#addons_list_wrap > .spinner').hide();
            $('#addons_list').append(result);
            if(has_next == 1){
                $('#show_more').show();
                current_page += 1;
            } else {
                if(current_page > 1){
                    $('#show_more').show().html($('#show_more').data('to-first'));
                } else {
                    $('#show_more').hide();
                }
                current_page = 1;
            }
            $(window).triggerHandler('resize');
            icms.modal.bind('a.ajax-modal');
        });
    };
</script>