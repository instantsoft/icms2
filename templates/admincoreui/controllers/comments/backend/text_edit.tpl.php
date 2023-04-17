<div id="comments_edit_form" class="modal_form">
    <form action="<?php echo $action; ?>" method="post">
        <?php echo html_csrf_token(); ?>
        <?php echo html_input('hidden', 'save', 1); ?>
        <div class="field form-group" id="f_content">
            <?php echo html_wysiwyg('content', $comment['content'], $editor_params['editor'], $editor_params['options']); ?>
        </div>
        <div class="buttons">
            <?php echo html_submit(LANG_SAVE); ?>
        </div>
    </form>
</div>
<script>
    $(function (){
        $('#comments_edit_form form').on('submit', function (){
            return icms.forms.submitAjax(this);
        });
    });
    function successSaveComment (form, result){
        icms.datagrid.loadRows();
        icms.modal.close();
    }
</script>