<div id="geo_window">

    <div class="wrapper">

        <form data-items-url="<?php echo $this->href_to('get_items'); ?>">

            <div class="list">
                <?php echo html_select('countries', $countries, $country_id, array('onchange'=>"icms.geo.changeParent(this, 'regions')", 'rel'=>'regions')); ?>
            </div>

            <div class="list" <?php if (!$city_id){?>style="display:none"<?php } ?>>
                <?php echo html_select('regions', $regions, $region_id, array('onchange'=>"icms.geo.changeParent(this, 'cities')", 'rel'=>'cities')); ?>
            </div>

            <div class="list" <?php if (!$city_id){?>style="display:none"<?php } ?>>
                <?php echo html_select('cities', $cities, $city_id, array('onchange'=>"icms.geo.changeCity(this)")); ?>
            </div>

        </form>

        <div class="buttons" <?php if (!$city_id){?>style="display:none"<?php } ?>>
            <?php echo html_button(LANG_SELECT, 'select', "icms.geo.selectCity('{$field_id}')"); ?>
        </div>

    </div>

</div>
