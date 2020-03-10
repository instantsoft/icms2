<?php $this->setPageTitle(LANG_PROCESS_VERIFY_EMAIL); ?>

<h1><?php echo LANG_PROCESS_VERIFY_EMAIL; ?></h1>

<?php
    $this->renderForm($form, $data, array(
        'submit' => array('title' => LANG_CONTINUE),
        'cancel' => array(
            'title' => LANG_AUTH_CLEAN_REG_EMAIL,
            'show' => ($reg_email ? true : false),
            'href' => $this->href_to('register').'?clean_reg_email=1'
        ),
        'action' => '',
        'method' => 'post',
    ), $errors);
?>

<script>
    <?php echo $this->getLangJS('LANG_SECOND1', 'LANG_SECOND2', 'LANG_SECOND10'); ?>
    var vTimer = {
        timeout_id: null,
        cur_time: 0,
        interval: 1000,
        reg_resubmit_timer: $('#reg_resubmit_timer'),
        reg_resubmit: $('#reg_resubmit'),
        timer: function (){
            vTimer.cur_time -= 1;
            $('strong', vTimer.reg_resubmit_timer).html(vTimer.cur_time+' '+spellcount(vTimer.cur_time, LANG_SECOND1, LANG_SECOND2, LANG_SECOND10));
            if(vTimer.cur_time > 0){
                vTimer.timeout_id = setTimeout(vTimer.timer, vTimer.interval);
            } else {
                $(vTimer.reg_resubmit).show();
                $(vTimer.reg_resubmit_timer).hide();
            }
        },
        init: function (){
            this.cur_time = +$(this.reg_resubmit).data('resubmit_time');
            if(this.cur_time === 0){
                $(this.reg_resubmit).show();
                $(this.reg_resubmit_timer).hide();
            } else {
                $(this.reg_resubmit).hide();
                $(this.reg_resubmit_timer).show();
                $('strong', this.reg_resubmit_timer).html(this.cur_time+' '+spellcount(this.cur_time, LANG_SECOND1, LANG_SECOND2, LANG_SECOND10));
                this.timeout_id = setTimeout(this.timer, this.interval);
            }
        }
    };
    <?php if(!$errors){ ?>
        $(function(){
            vTimer.init();
            if($('#reg_token').val().length == 32){
                $('#reg_token').closest('form').find('.button-submit').trigger('click');
            }
        });
    <?php } ?>
</script>