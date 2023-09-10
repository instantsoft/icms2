<?php if(!$rows){ ?>
<p class="alert alert-warning mt-4" role="alert"><?php echo LANG_CP_WIDGETS_ROW_NONE; ?></p>
<?php } ?>
<?php foreach ($rows as $row) { ?>
<div class="row no-gutters widgets-layout-scheme align-items-center<?php if($row['parent_id']){ ?> disable-sortable<?php } ?>" id="row-<?php echo $row['id']; ?>" data-id="<?php echo $row['id']; ?>">
    <div class="layout-row-title <?php if(!$row['parent_id'] ){ ?>layout-row-parent<?php } ?> col-sm-12 <?php if(!$row['parent_id'] && $rows_titles_pos == 'left'){ ?> col-lg-2<?php } ?> filled p-2 <?php if($rows_titles_pos == 'hide'){ ?>d-none<?php } else { ?>d-flex<?php } ?> justify-content-between">
        {cell:<?php echo $row['title']; ?>}
        <div class="layout-scheme-actions align-items-center">
            <a class="add mr-1 text-success text-decoration-none add-scheme-row ajax-modal" data-toggle="tooltip" data-placement="top" href="<?php echo $this->href_to('widgets', ['col_add', $row['id']]); ?>" title="<?php echo LANG_CP_WIDGETS_ADD_COL; ?>">
                <i class="icon-plus icons"></i>
            </a>
            <a class="edit mr-1 text-decoration-none edit-scheme-row ajax-modal" data-toggle="tooltip" data-placement="top" href="<?php echo $this->href_to('widgets', ['row_edit', $row['id']]); ?>" title="<?php echo LANG_EDIT; ?>">
                <i class="icon-pencil icons"></i>
            </a>
            <a class="delete text-danger text-decoration-none delete-scheme-row" data-toggle="tooltip" data-placement="top" href="<?php echo $this->href_to('widgets', ['row_delete', $row['id']]); ?>" title="<?php echo LANG_DELETE; ?>" onclick="return confirm('<?php echo LANG_CP_WIDGETS_ROW_DEL_CONFIRM; ?>')">
                <i class="icon-close icons"></i>
            </a>
        </div>
    </div>
    <?php if(!$row['parent_id']){ ?>
        <div class="w-100 d-md-none"></div>
    <?php } ?>
    <div class="col-sm-12 layout-row-body <?php if(!$row['parent_id'] ){ ?>layout-row-parent<?php } ?> <?php if(!$row['parent_id'] && $rows_titles_pos == 'left'){ ?>col-lg-10<?php } ?> bg-white">
        <div class="row no-gutters widgets-layout-scheme-col-wrap <?php html($row['options']['vertical_align']); ?> <?php html($row['options']['horizontal_align']); ?>">
        <?php foreach ($row['cols'] as $col) { ?>
            <?php if(!empty($col['options']['cut_before'])){ ?>
                <div class="w-100"></div>
            <?php } ?>
            <?php
            $width_hint = LANG_AUTO; $col_class = 'col-sm'; $col_width_list = [];
            foreach ($col['options'] as $key => $value) {
                if(strpos($key, 'col_') === false){
                    continue;
                }
                if(empty($col['options'][$key])){
                    continue;
                }
                $col_type = preg_replace('#(col\-[a-z]+\-)#ui', '', $value);
                if(is_numeric($col_type)){
                    $width_hint = round((8.333333333*$col_type), 2).'%';
                    $col_width_list[] = $width_hint;
                } elseif($col_type === 'auto'){
                    $width_hint = LANG_CP_WIDGETS_COL_AUTO;
                }
                $col_class = $value;
            }
            ?>
            <div class="<?php echo $col_class; ?> p-1 bg-white widgets-layout-scheme-col" id="col-<?php echo $col['id']; ?>" data-id="<?php echo $col['id']; ?>">
                <?php if(!empty($col['rows']['before'])){ ?>
                    <?php $this->renderChild('widgets_scheme', ['rows' => $col['rows']['before'], 'rows_titles_pos' => $rows_titles_pos]); ?>
                <?php } ?>
                <div class="layout-col-title d-flex justify-content-between" data-toggle="tooltip" data-placement="top" title="<?php html($col['name'].($col_width_list ? ': '.implode(', ', $col_width_list) : '')); ?>">
                    <span><?php echo $col['title']; ?> (<?php echo $width_hint; ?>)</span>
                    <div class="layout-scheme-actions d-flex align-items-center">
                        <?php if(!$row['parent_id']){ ?>
                        <a class="add mr-2 text-white text-decoration-none add-scheme-col ajax-modal" href="<?php echo $this->href_to('widgets', ['row_add_ns', $col['id']]); ?>" title="<?php echo LANG_CP_WIDGETS_ADD_ROW_P; ?>">
                            <i class="icon-plus icons d-block"></i>
                        </a>
                        <?php } ?>
                        <a class="edit mr-2 text-white text-decoration-none edit-scheme-col ajax-modal" href="<?php echo $this->href_to('widgets', ['col_edit', $col['id']]); ?>" title="<?php echo LANG_EDIT; ?>">
                            <i class="icon-pencil icons d-block"></i>
                        </a>
                        <a class="delete text-warning text-decoration-none delete-scheme-col" href="<?php echo $this->href_to('widgets', ['col_delete', $col['id']]); ?>" title="<?php echo LANG_DELETE; ?>" onclick="return confirm('<?php echo LANG_CP_WIDGETS_COL_DEL_CONFIRM; ?>')">
                            <i class="icon-close icons d-block"></i>
                        </a>
                    </div>
                </div>
                {position:<?php echo $col['name']; ?>}
                <?php if(!empty($col['rows']['after'])){ ?>
                    <?php $this->renderChild('widgets_scheme', ['rows' => $col['rows']['after'], 'rows_titles_pos' => $rows_titles_pos]); ?>
                <?php } ?>
            </div>
        <?php } ?>
        </div>
    </div>
</div>
<?php } ?>