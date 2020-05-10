<?php if($widget->options['type'] === 'body'){ ?>
    <?php $this->block('before_body'); ?>
    <?php $this->body(); ?>
<?php } elseif($widget->options['type'] === 'breadcrumbs') { ?>
    <?php $this->breadcrumbs($widget->options['breadcrumbs']); ?>
<?php } elseif($widget->options['type'] === 'smessages') { ?>
    <?php if($widget->options['session_type'] === 'toastr'){ ?>

        <?php $this->addTplCSSName([
            'vendors/toastr/toastr.min'
        ]); ?>
        <?php $this->addTplJSName([
            'vendors/toastr/toastr.min',
        ]); ?>

        <?php if ($messages){  ob_start(); ?>
        <script type="text/javascript">
            $(function(){
            <?php foreach($messages as $message){ ?>
                toastr.<?php echo $message['class']; ?>('<?php echo $message['text']; ?>');
             <?php } ?>
            });
        </script>
        <?php $this->addBottom(ob_get_clean()); } ?>
    <?php } else { ?>
        <?php if ($messages){ foreach($messages as $message){ ?>
        <div class="alert alert-<?php echo str_replace(['error'], ['danger'], $message['class']); ?> alert-dismissible fade show" role="alert">
                <?php echo $message['text']; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php } } ?>
    <?php } ?>
<?php } ?>