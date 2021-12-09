<?php

    if ($do=='add') { $this->setPageTitle(LANG_CP_WIDGETS_ADD_PAGE); }
    if ($do=='edit') { $this->setPageTitle(LANG_CP_WIDGETS_PAGE.': '.$page['title']); }

    $this->addBreadcrumb(LANG_CP_SECTION_WIDGETS, $this->href_to('widgets'));

    if ($do=='add'){
        $this->addBreadcrumb(LANG_CP_WIDGETS_ADD_PAGE);
    }

    if ($do=='edit'){
        $this->addBreadcrumb($page['title']);
    }

    $this->addMenuItem('breadcrumb-menu', [
        'title' => LANG_HELP,
        'url'   => LANG_HELP_URL_WIDGETS_PAGES,
        'options' => [
            'target' => '_blank',
            'icon' => 'question-circle'
        ]
    ]);

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));

    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('widgets')
    ));

    $this->renderForm($form, $page, array(
        'action' => '',
        'method' => 'post'
    ), $errors);

?>
<script>
    $(document).ready(function(){
        $('<div class="inline_button"><input id="fast_add_submit" class="button btn btn-success" value="<?php echo LANG_CP_WIDGETS_FA_ADD; ?>" type="button"></div>').insertAfter('#f_fast_add_into');
        $('#fast_add_ctype').triggerHandler('change');
        $('#fast_add_ctype').change(function(){
            $('#fast_add_item').triggerHandler('input');
        });

        var madd = function(value){
            var into = $('#fast_add_into').val();
            var now = $('#url_mask'+into).val();
            var add = now ? now+"\n" : '';
            add += value;
            $('#url_mask'+into).val(add);
        };
        $('#fast_add_submit').on('click', function(){
            var type = $('#fast_add_type').val();
            if(type === 'items'){
                madd($('#fast_add_item').val());
                $('#fast_add_item').val('');
            }else
            if(type === 'cats'){
                madd($('#fast_add_cat').val());
            }
        });

        var cache = {};
        $('#fast_add_item').autocomplete({
            minLength: 2,
            delay: 500,
            source: function( request, response ){
                var ctype = $('#fast_add_ctype').val();
                var term = ctype+'_'+request.term;
                request['ctype'] = ctype;
                if(term in cache){
                    response(cache[term]);
                    return;
                }
                $.getJSON('<?php echo href_to('admin', 'widgets', 'page_autocomplete'); ?>', request, function(data, status, xhr){
                    cache[term] = data;
                    response(data);
                });
            },
            select: function(event, ui){
                icms.events.run('autocomplete_select', ui);
            }
        });

    });
</script>
