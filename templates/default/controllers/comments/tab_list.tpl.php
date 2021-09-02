<?php $index = 0; ?>
<div id="comments_widget" class="tabs-menu">

    <span id="comments"></span>

    <ul class="tabbed <?php echo $target_controller.'_'.$target_subject; ?>_comments_tab">
        <?php foreach ($comment_systems as $comment_system) { ?>
            <li><a href="#tab-<?php echo $comment_system['name']; ?>"><?php echo $comment_system['title']; ?></a></li>
        <?php } ?>
    </ul>

    <?php foreach ($comment_systems as $comment_system) { ?>
        <div id="tab-<?php echo $comment_system['name']; ?>" class="tab <?php echo $target_controller.'_'.$target_subject; ?>_comments" <?php if($index){ ?>style="display: none;"<?php } ?>>
            <?php echo $comment_system['html']; ?>
        </div>
        <?php $index++; ?>
    <?php } ?>

</div>
<?php ob_start(); ?>
<script>
    $(function (){
        initTabs('#comments_widget');
    });
</script>
<?php $this->addBottom(ob_get_clean()); ?>