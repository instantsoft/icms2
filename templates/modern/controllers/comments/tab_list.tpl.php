<div id="comments_widget" class="tabs-menu">
    <span id="comments"></span>
    <ul class="nav nav-tabs <?php echo $target_controller.'_'.$target_subject; ?>_comments_tab">
        <?php foreach ($comment_systems as $key => $comment_system) { ?>
            <li class="nav-item">
                <a href="#tab-<?php echo $comment_system['name']; ?>" class="nav-link <?php if(!$key){ ?>active<?php } ?>" data-toggle="tab" role="tab">
                    <?php echo $comment_system['title']; ?>
                </a>
            </li>
        <?php } ?>
    </ul>
    <?php foreach ($comment_systems as $key => $comment_system) { ?>
        <div id="tab-<?php echo $comment_system['name']; ?>" class="tab-pane show <?php if(!$key){ ?>active<?php } ?> <?php echo $target_controller.'_'.$target_subject; ?>_comments">
            <?php echo $comment_system['html']; ?>
        </div>
    <?php } ?>
</div>