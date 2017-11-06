<?php $index = 0; ?>
<div id="comments_widget" class="tabs-menu">

    <span id="comments"></span>

    <ul class="tabbed">
        <?php foreach ($comment_systems as $comment_system) { ?>
            <li><a href="#tab-<?php echo $comment_system['name']; ?>"><?php echo $comment_system['title']; ?></a></li>
        <?php } ?>
    </ul>

    <?php foreach ($comment_systems as $comment_system) { ?>
        <div id="tab-<?php echo $comment_system['name']; ?>" class="tab" <?php if($index){ ?>style="display: none;"<?php } ?>>
            <?php echo $comment_system['html']; ?>
        </div>
        <?php $index++; ?>
    <?php } ?>

</div>
<script type="text/javascript">
    $(function (){
        initTabs('#comments_widget');
    });
</script>
