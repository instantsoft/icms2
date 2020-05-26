<?php foreach ($rows as $row) { ?>
    <?php if(!$this->hasWidgetsOn($row['positions'])){ ?>
        <?php continue; ?>
    <?php } ?>
    <?php if (!empty($row['options']['parrent_tag'])) { ?>
        <<?php echo $row['options']['parrent_tag']; ?><?php if ($row['options']['parrent_tag_class']) { ?> class="<?php echo $row['options']['parrent_tag_class']; ?>"<?php } ?>>
    <?php } ?>
    <?php if (!empty($row['options']['container'])) { ?>
        <div class="<?php echo $row['options']['container']; ?> <?php echo $row['options']['container_tag_class']; ?>">
    <?php } ?>
    <?php
        // Собираем класс ряда
        $row_class =  $row['tag'] ? ['row'] : [];
        if (!empty($row['options']['no_gutters'])) {
            $row_class[] = 'no-gutters';
        }
        if (!empty($row['options']['vertical_align'])) {
            $row_class[] = $row['options']['vertical_align'];
        }
        if (!empty($row['options']['horizontal_align'])) {
            $row_class[] = $row['options']['horizontal_align'];
        }
        if ($row['class']) {
            $row_class[] = $row['class'];
        }
    ?>
    <?php if ($row_class) { ?>
        <<?php echo $row['tag']; ?> class="<?php echo implode(' ', $row_class); ?>">
    <?php } ?>
    <?php foreach ($row['cols'] as $col) { ?>
        <?php if($col['type'] === 'custom'){ ?>
            <?php if(!empty($col['rows']['before'])){ ?>
                <?php $this->renderLayoutChild('scheme', ['rows' => $col['rows']['before']]); ?>
            <?php } ?>
            <?php if($this->hasWidgetsOn($col['name'])){ ?>
                <?php $this->widgetsPlain($col['name'], $col['wrapper']); ?>
            <?php } ?>
            <?php if(!empty($col['rows']['after'])){ ?>
                <?php $this->renderLayoutChild('scheme', ['rows' => $col['rows']['after']]); ?>
            <?php } ?>
            <?php continue; ?>
        <?php } ?>
        <?php
            // Собираем класс колонки
            $col_class = [];
            if ($col['options']['col_class']) {
                $col_class[] = $col['options']['col_class'];
            }
            if ($col['options']['default_col_class']) {
                $col_class[] = $col['options']['default_col_class'];
            }
            if ($col['options']['md_col_class']) {
                $col_class[] = $col['options']['md_col_class'];
            }
            if ($col['options']['lg_col_class']) {
                $col_class[] = $col['options']['lg_col_class'];
            }
            if ($col['options']['xl_col_class']) {
                $col_class[] = $col['options']['xl_col_class'];
            }
            if ($col['options']['default_order']) {
                $col_class[] = 'order-'.$col['options']['default_order'];
            }
            if ($col['options']['sm_order']) {
                $col_class[] = 'order-sm-'.$col['options']['sm_order'];
            }
            if ($col['options']['md_order']) {
                $col_class[] = 'order-md-'.$col['options']['md_order'];
            }
            if ($col['options']['lg_order']) {
                $col_class[] = 'order-lg-'.$col['options']['lg_order'];
            }
            if ($col['options']['xl_order']) {
                $col_class[] = 'order-xl-'.$col['options']['xl_order'];
            }
            if ($col['class']) {
                $col_class[] = $col['class'];
            }
        ?>
        <?php if($this->hasWidgetsOn($col['name'])){ ?>
            <div class="<?php echo implode(' ', $col_class); ?>">
                <?php if(!empty($col['rows']['before'])){ ?>
                    <?php $this->renderLayoutChild('scheme', ['rows' => $col['rows']['before']]); ?>
                <?php } ?>
                <?php $this->widgets($col['name']); ?>
                <?php if(!empty($col['rows']['after'])){ ?>
                    <?php $this->renderLayoutChild('scheme', ['rows' => $col['rows']['after']]); ?>
                <?php } ?>
            </div>
        <?php } ?>
    <?php } ?>
    <?php if ($row_class) { ?>
        </<?php echo $row['tag']; ?>>
    <?php } ?>
    <?php if (!empty($row['options']['container'])) { ?>
        </div>
    <?php } ?>
    <?php if (!empty($row['options']['parrent_tag'])) { ?>
        </<?php echo $row['options']['parrent_tag']; ?>>
    <?php } ?>
<?php } ?>