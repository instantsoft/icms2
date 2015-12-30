<div id="complaints">
        <?php if(!empty($data['orfo'])){ ?>
            <?php ob_start(); ?>
                <div class="content">
                    <div class="title">
                           <div class="text_error">
                               <span style="font-weight: bold;"><?php echo LANG_COMPLAINTS_ORFO_ERROR;?></span><br/>
                                   <?php echo $data['orfo'];?>
                           </div>
                    </div>              
                <?php $prepend_html = ob_get_clean(); ?>
        <?php } ?>
<?php   
    $this->renderForm($form, $data, array(
            'action' => '',
            'method' => 'ajax',
            'toolbar' => false, 
            'submit' => array('class'=> 'button button_ok', 'title' => LANG_SEND),       
            'cancel' => array('show' => true, 'href' => ''),
            'prepend_html' => (isset($prepend_html) ? $prepend_html : '')            
        ), false);
?>      
    </div>
</div>
<div id="complaints_send" style="display: none;">
                      
    <div style="margin:20px; font-weight: bold;">
        
        <?php echo LANG_COMPLAINTS_OK;?>
        
    </div> 
</div>
<style>
    #complaints{width: 500px;}
    #complaints .content{margin: 15px;}
    #complaints .content textarea{height: 100px;}
    #complaints .buttons{text-align: center;}
    #complaints .title{margin-bottom: 10px;}    
    #complaints .text_error{color:#a4a4a4;}
</style>
<script>
    var orfo = $('#orfo').attr('value');
    var url = $('#url').attr('value');
    var author = $('#author').attr('value');	 
    var comment = $('#comment').val();       
    var data = {comment: comment, orfo: orfo, url: url, author: author, submit: true};
    
    $('#complaints .button_ok').on('click', function(){    
        
         $.post('<?php echo $this->href_to('orfo'); ?>', data, function(result){
                    console.log(result);
                    if(result.error){
                        alert(result.error);
                    } else {
                        $('#complaints').hide();
                        $('#complaints_send').show();
                        icms.modal.resize();
                        setTimeout(icms.modal.close, 2000);
                        
                    }
                }, 'json');
    })
<?php echo $this->getLangJS('LANG_COMPLAINTS_OK'); ?>
  
</script>