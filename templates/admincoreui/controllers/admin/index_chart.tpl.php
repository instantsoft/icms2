<div id="chart" data-url="<?php echo $this->href_to('index_chart_data'); ?>" data-interval="<?php html($defaults['interval']); ?>" data-type="<?php html($defaults['type']); ?>">
    <div class="row mb-3">
        <div class="col-auto form-inline">
            <div class="form-group">
                <label class="mr-3 d-none d-md-block"><?php echo LANG_CP_DASHBOARD_STATS; ?></label>
                <select class="custom-select form-control">
                    <?php foreach($chart_nav as $section){ ?>
                        <optgroup label="<?php echo $section['title']; ?>">
                            <?php foreach($section['sections'] as $id => $link) { ?>
                                <?php if(empty($link['title'])){ continue; } ?>
                                <?php $is_active = $defaults['controller']==$section['id'] && $defaults['section']==$id; ?>
                                <option data-ctrl="<?php echo $section['id']; ?>" data-section="<?php echo $id; ?>" <?php if ($is_active) { ?>selected="selected"<?php } ?>>
                                    <?php echo $link['title']; ?>
                                </option>
                            <?php } ?>
                        </optgroup>
                    <?php } ?>
                </select>
            </div>
            <span id="chart-spinner" class="db_spinner sk-spinner sk-spinner-pulse bg-blue ml-3" data-percent="0%"></span>
        </div>
        <div class="col">
            <button id="toggle-type" class="d-none d-md-block btn <?php if($defaults['type'] == 'line'){ ?>btn-primary<?php } else { ?>btn-outline-secondary<?php } ?> float-right" type="button">
                <?php html_svg_icon('solid', 'chart-area'); ?>
            </button>
            <div id="menu-period" class="btn-group btn-group-toggle float-sm-right mr-3">
                <button class="btn btn-outline-secondary<?php if ($defaults['interval'] === '1:WEEK') { ?> active<?php } ?>" data-interval="1:WEEK">
                    <span><?php echo LANG_WEEK; ?></span>
                </button>
                <button class="btn btn-outline-secondary<?php if ($defaults['interval'] === '1:MONTH') { ?> active<?php } ?>" data-interval="1:MONTH">
                    <span><?php echo LANG_MONTH; ?></span>
                </button>
                <button class="btn btn-outline-secondary<?php if ($defaults['interval'] === '1:YEAR') { ?> active<?php } ?>" data-interval="1:YEAR">
                    <span><?php echo LANG_YEAR; ?></span>
                </button>
            </div>
        </div>
    </div>
    <div class="chart-wrapper" style="position: relative; height:40vh; width:100%">
        <canvas class="chart" id="chart-canvas" role="img"></canvas>
    </div>
</div>
<div class="card-footer" id="chart-footer" style="display: none;">
    <div class="row text-center">
        <div class="col-sm-12 col-md mb-0" id="chart-footer-tpl" style="display: none;">
            <div class="callout m-0">
                <small class="text-muted"></small>
                <br>
                <strong class="h4"></strong>
            </div>
        </div>
    </div>
</div>