<?php
$this->addBreadcrumb(LANG_COMMENTS_LIST);
$this->setPageTitle(LANG_COMMENTS_LIST);

$this->addJS('templates/default/js/jquery-ui.js');
$this->addCSS('templates/default/css/jquery-ui.css');

$this->renderGrid($this->href_to('comments_list'), $grid); ?>

<script type="text/javascript">
    $(function(){
        $(document).on('click', '.datagrid .filter_ip', function (){
            $('#filter_author_url').val($(this).text()).trigger('input');
            return false;
        });
        $(document).tooltip({
            items: '.tooltip',
            show: { duration: 0 },
            hide: { duration: 0 },
            position: {
                my: "center",
                at: "top-20"
            }
        });
        icms.datagrid.callback = function (){
            icms.modal.bind('a.ajax-modal');
        };
    });
</script>
