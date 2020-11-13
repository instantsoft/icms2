<?php if($cover_preset){ ?>
    <div class="d-flex justify-content-between flex-wrap mb-n3 mb-md-n4">
        <?php foreach($cats as $item){ ?>
            <div class="mb-3 mb-md-4<?php if ($item['is_hidden']) { ?> d-none<?php } ?><?php if ($item['css_classes']) { ?> <?php echo implode(' ', $item['css_classes']); ?><?php } ?>">
                <div class="card border-0">
                    <?php if($item['img_src']){ ?>
                        <a class="d-block overflow-hidden rounded-lg" href="<?php echo href_to($ctype_name, $item['slug']); ?>">
                            <img class="d-block img-fluid" src="<?php echo $item['img_src']; ?>" alt="<?php html($item['title']); ?>">
                        </a>
                    <?php } ?>
                    <div class="card-body<?php if(!$item['img_src']){ ?> p-0<?php } else { ?> px-0 pb-0 pt-2<?php } ?>">
                        <h3 class="h5 m-0">
                            <?php if(!$item['img_src']){ ?>
                                <span class="text-warning"><?php html_svg_icon('solid', 'folder'); ?></span>
                            <?php } ?>
                            <a href="<?php echo href_to($ctype_name, $item['slug']); ?>">
                                <?php echo $item['title']; ?>
                            </a>
                        </h3>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
<?php } else { ?>
    <ul class="icms-content-subcats list-unstyled my-n2">

        <?php $last_level = 0; ?>

        <?php foreach($cats as $item){ ?>

            <?php for ($i=0; $i<($last_level - $item['ns_level']); $i++) { ?>
                </li></ul>
            <?php } ?>

            <?php if ($item['ns_level'] <= $last_level) { ?>
                </li>
            <?php } ?>

            <li class="my-2<?php if ($item['is_hidden']) { ?> d-none<?php } ?>">

                <a class="h5<?php if ($item['is_active']) { ?> text-dark<?php } ?>" href="<?php echo href_to($ctype_name, $item['slug']); ?>">
                    <span class="text-warning"><?php html_svg_icon('solid', 'folder'); ?></span>
                    <span><?php html($item['title']); ?></span>
                </a>

                <?php if ($item['childs_count']) { ?><ul class="list-unstyled pl-3"><?php } ?>

                <?php $last_level = $item['ns_level']; ?>

        <?php } ?>

    <?php for ($i=0; $i<$last_level; $i++) { ?>
        </li></ul>
    <?php } ?>
<?php } ?>