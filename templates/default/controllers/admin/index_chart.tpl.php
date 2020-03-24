<div id="chart" data-url="<?php echo $this->href_to('index_chart_data'); ?>" data-period="<?php html($defaults['period']); ?>">
    <div class="toolbar">
        <select>
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
        <div class="pills-menu">
            <ul class="menu">
                <li <?php if ($defaults['period'] == 7) { ?>class="active"<?php } ?>>
                    <a class="item" href="#" data-period="7"><?php echo LANG_WEEK; ?></a>
                </li>
                <li <?php if ($defaults['period'] == 30) { ?>class="active"<?php } ?>>
                    <a class="item" href="#" data-period="30"><?php echo LANG_MONTH; ?></a>
                </li>
                <li <?php if ($defaults['period'] == 365) { ?>class="active"<?php } ?>>
                    <a class="item" href="#" data-period="365"><?php echo LANG_YEAR; ?></a>
                </li>
            </ul>
        </div>
    </div>
    <canvas id="chart-canvas"></canvas>
</div>