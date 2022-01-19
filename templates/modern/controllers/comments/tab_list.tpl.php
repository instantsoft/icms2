<div id="comments_widget" class="tabs-menu icms-comments__tabs">
    <span id="comments"></span>
    <ul class="nav nav-tabs <?php echo $target_controller.'_'.$target_subject; ?>_comments_tab position-relative">
        <?php foreach ($comment_systems as $key => $comment_system) { ?>
            <li class="nav-item">
                <a href="#tab-<?php echo $comment_system['name']; ?>" class="nav-link <?php if(!$key){ ?>active<?php } ?>" data-toggle="tab">
                    <?php echo $comment_system['title']; ?>
                </a>
                <?php if(!empty($comment_system['icon'])){ ?>
                    <a href="<?php echo $comment_system['icon']['href']; ?>" class="icms-comments__tabs-tab btn <?php echo $comment_system['icon']['class']; ?>" title="<?php echo $comment_system['icon']['title']; ?>">
                        <?php html_svg_icon('solid', $comment_system['icon']['icon']); ?>
                    </a>
                <?php } ?>
            </li>
        <?php } ?>
    </ul>
    <div class="tab-content">
        <?php foreach ($comment_systems as $key => $comment_system) { ?>
            <div id="tab-<?php echo $comment_system['name']; ?>" class="tab-pane<?php if(!$key){ ?> show active<?php } ?> <?php echo $target_controller.'_'.$target_subject; ?>_comments">
                <?php echo $comment_system['html']; ?>
            </div>
        <?php } ?>
    </div>
</div>
