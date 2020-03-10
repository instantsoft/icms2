<?php
$this->addBreadcrumb(LANG_COMMENTS_LIST);
$this->setPageTitle(LANG_COMMENTS_LIST);

$this->addToolButton(array(
    'class' => 'delete',
    'title' => LANG_DELETE,
    'href'  => null,
    'onclick' => "return icms.datagrid.submit('".$this->href_to('comments_delete').'?csrf_token='.cmsForm::getCSRFToken()."', '".LANG_DELETE_SELECTED_CONFIRM."')",
));

$this->renderGrid($this->href_to('comments_list'), $grid); ?>

<script type="text/javascript">
    $(function(){
        $(document).on('click', '.datagrid .filter_ip', function (){
            $('#filter_author_url').val($(this).text()).trigger('input');
            return false;
        });
        $('#datagrid').on('click', '.approve_comment', function (){
            var clink = this;
            $(this).closest('.flag_trigger').addClass('loading');
            $.get($(this).data('approve-url'), function(result){
                $(clink).closest('.flag_trigger').removeClass('loading');
                if (result == null || typeof(result) == 'undefined' || result.error){
                    icms.modal.alert(result.message);
                    return false;
                }
                $(clink).hide().closest('.flag_trigger').removeClass('flag_off').addClass('flag_on');
            }, 'json');
            return false;
        });
    });
</script>