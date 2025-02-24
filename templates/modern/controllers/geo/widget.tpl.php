<?php
$this->addTplJSNameFromContext('jquery-chosen');
$this->addTplCSSNameFromContext('jquery-chosen');
?>
<div id="geo_window">

    <div class="wrapper mb-n3">
        <form data-items-url="<?php echo $this->href_to('get_items'); ?>">

            <div class="list mb-3">
                <?php echo html_select('countries', $countries, $country_id, ['rel' => 'regions']); ?>
            </div>

            <div class="list mb-3" <?php if (!$city_id){?>style="display:none"<?php } ?>>
                <?php echo html_select('regions', $regions, $region_id, ['rel' => 'cities']); ?>
            </div>

            <div class="list mb-3" <?php if (!$city_id){?>style="display:none"<?php } ?>>
                <?php echo html_select('cities', $cities, $city_id); ?>
            </div>

        </form>

        <div class="buttons mb-3" <?php if (!$city_id){?>style="display:none"<?php } ?>>
            <?php echo html_button(LANG_SELECT, 'select', '', ['id' => 'select-geo-city']); ?>
        </div>
    </div>

</div>
<script>
    $(function(){
        let geo_window = $('#geo_window');
        let geo_window_select = $('.list > select', geo_window);
        geo_window_select.chosen({no_results_text: '<?php echo LANG_LIST_EMPTY; ?>', width: '100%', search_placeholder: '<?php echo LANG_BEGIN_TYPING; ?>'});
        geo_window_select.on('change', function(){
            let type = $(this).attr('rel');
            if (type) {
                icms.geo.changeParent(this, type);
            } else {
                icms.geo.changeCity(this);
            }
        });
        $('#select-geo-city', geo_window).on('click', function(){
            icms.geo.selectCity('<?php echo $field_id; ?>');
        });
        <?php if (!$city_id){?>
            geo_window_select.first().trigger('change');
        <?php } ?>
    });
</script>