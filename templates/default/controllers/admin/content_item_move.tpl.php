<div class="modal_padding">
    <?php

        $this->renderForm($form, array(
            'ctype_name'=>$ctype['name'],
            'items' => implode(',', $items)
        ), array(
            'action' => $this->href_to('content', array('item_move', $ctype['id'], $parent_id)),
            'method' => 'ajax'
        ), $errors);

    ?>
</div>
<script>
    icms.modal.setCallback('open', function (){
        setTimeout(function(){ $('.nyroModalCont').css('overflow', 'visible'); }, 300);
    });
</script>