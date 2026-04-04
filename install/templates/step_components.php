<h1><?php echo LANG_STEP_COMPONENTS; ?></h1>
<p><?php echo LANG_STEP_COMPONENTS_HINT; ?></p>

<form id="step-form">

    <fieldset>
        <legend><?php echo LANG_INSTALL_TYPE; ?></legend>

        <div class="install-type-list">
        <?php foreach ($install_types as $type_id => $type): ?>
        <label class="install-type-card <?php if ($install_type === $type_id) echo 'selected'; ?>">
            <input type="radio" name="install_type" value="<?php echo $type_id; ?>"
                   <?php if ($install_type === $type_id) echo 'checked'; ?>
                   data-demo="<?php echo $type['demo'] ? '1' : '0'; ?>">
            <div class="type-content">
                <strong><?php echo constant($type['title']); ?></strong>
                <span><?php echo constant($type['desc']); ?></span>
            </div>
        </label>
        <?php endforeach; ?>
        </div>

        <div class="field" id="demo-field">
            <label>
                <input type="checkbox" value="1" name="is_install_demo" id="is_install_demo"
                       <?php if ($is_install_demo) echo 'checked'; ?>
                       <?php if ($install_type === 'minimal') echo 'disabled'; ?>>
                <?php echo LANG_INSTALL_DEMO_CONTENT; ?>
            </label>
        </div>

    </fieldset>

    <fieldset id="components-fieldset">
        <legend><?php echo LANG_CUSTOM_COMPONENTS; ?></legend>
        <p class="hint"><?php echo LANG_CUSTOM_COMPONENTS_HINT; ?></p>

        <?php foreach ($categories as $cat_id => $category): ?>
        <div class="category-section">
            <h3><?php echo constant($category['title']); ?></h3>

            <?php foreach ($category['components'] as $comp_id => $comp): ?>
            <?php $is_mandatory = in_array($comp_id, $mandatory); ?>
            <label class="component-item <?php if ($is_mandatory) echo 'mandatory'; ?>">
                <input type="checkbox" 
                       name="components[]" 
                       value="<?php echo $comp_id; ?>"
                       data-deps="<?php echo implode(',', $comp['deps']); ?>"
                       <?php if ($is_mandatory) echo 'disabled checked'; ?>
                       <?php if (in_array($comp_id, $selected)) echo 'checked'; ?>
                       class="custom-checkbox">
                
                <div class="comp-content">
                    <span class="comp-title">
                        <?php echo constant($comp['title']); ?>
                        <?php if ($is_mandatory): ?>
                        <em>(<?php echo LANG_MANDATORY; ?>)</em>
                        <?php endif; ?>
                    </span>
                    <span class="comp-desc"><?php echo constant($comp['desc']); ?></span>
                    
                    <?php if (!empty($comp['deps'])): ?>
                    <span class="comp-deps">
                        <i class="icon-link"></i>
                        <?php echo LANG_REQUIRES; ?>: 
                        <?php
                        $dep_titles = [];
                        foreach ($comp['deps'] as $dep) {
                            foreach ($categories as $dc => $dcat) {
                                if (isset($dcat['components'][$dep])) {
                                    $dep_titles[] = constant($dcat['components'][$dep]['title']);
                                    break;
                                }
                            }
                        }
                        echo implode(', ', $dep_titles);
                        ?>
                    </span>
                    <?php endif; ?>
                </div>
            </label>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>

    </fieldset>

</form>

<div class="buttons">
    <input type="button" value="<?php echo LANG_BACK; ?>" onclick="prevStep()">
    <input type="button" value="<?php echo LANG_NEXT; ?>" onclick="submitStep()">
</div>

<script>
$(function() {
    function updateComponentsVisibility() {
        var type = $('input[name="install_type"]:checked').val();
        
        if (type === 'custom') {
            $('#components-fieldset').show();
            $('.custom-checkbox').prop('disabled', false);
        } else {
            $('#components-fieldset').hide();
            $('.custom-checkbox').prop('disabled', true);
        }
        
        var demo = $('input[name="install_type"]:checked').data('demo');
        if (demo) {
            $('#is_install_demo').prop('disabled', false);
        } else {
            $('#is_install_demo').prop('disabled', true).prop('checked', false);
        }
        
        $('.install-type-card').removeClass('selected');
        $('input[name="install_type"]:checked').closest('.install-type-card').addClass('selected');
    }
    
    $('input[name="install_type"]').change(updateComponentsVisibility);
    updateComponentsVisibility();
    
    $('input[name="components[]"]').change(function() {
        if ($(this).is(':checked')) {
            var deps = $(this).data('deps');
            if (deps && deps.length > 0) {
                deps.split(',').forEach(function(dep) {
                    if (dep) {
                        $('input[value="' + dep + '"]').prop('checked', true);
                    }
                });
            }
        }
    });
    
    $('.component-item.mandatory input').change(function(e) {
        e.preventDefault();
        return false;
    });
});
</script>

<style>
.install-type-list {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 20px;
}
.install-type-card {
    flex: 1;
    min-width: 200px;
    display: flex;
    align-items: flex-start;
    padding: 15px;
    border: 2px solid #ddd;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
}
.install-type-card:hover {
    border-color: #aaa;
}
.install-type-card.selected {
    border-color: #007bff;
    background: #f0f7ff;
}
.install-type-card input {
    margin-right: 12px;
    margin-top: 2px;
}
.install-type-card .type-content {
    display: flex;
    flex-direction: column;
}
.install-type-card .type-content strong {
    font-size: 15px;
    margin-bottom: 4px;
}
.install-type-card .type-content span {
    color: #666;
    font-size: 12px;
}
#demo-field {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #eee;
}
#components-fieldset {
    margin-top: 25px;
}
#components-fieldset .hint {
    color: #666;
    font-size: 13px;
    margin-bottom: 15px;
}
.category-section {
    margin-bottom: 20px;
}
.category-section h3 {
    font-size: 14px;
    color: #333;
    margin-bottom: 10px;
    padding-bottom: 5px;
    border-bottom: 1px solid #eee;
}
.component-item {
    display: flex;
    align-items: flex-start;
    padding: 10px 12px;
    margin-bottom: 6px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    background: #fafafa;
    cursor: pointer;
    transition: all 0.2s;
}
.component-item:hover {
    border-color: #ccc;
    background: #f5f5f5;
}
.component-item.mandatory {
    background: #e8f4ff;
    border-color: #b8daff;
    cursor: default;
}
.component-item.mandatory:hover {
    background: #e8f4ff;
    border-color: #b8daff;
}
.component-item input[type="checkbox"] {
    margin-right: 12px;
    margin-top: 2px;
    width: 16px;
    height: 16px;
}
.comp-content {
    display: flex;
    flex-direction: column;
}
.comp-title {
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 3px;
}
.comp-title em {
    color: #666;
    font-size: 11px;
    font-weight: normal;
    margin-left: 6px;
}
.comp-desc {
    color: #666;
    font-size: 12px;
    margin-bottom: 3px;
}
.comp-deps {
    color: #007bff;
    font-size: 11px;
}
.comp-deps i {
    margin-right: 3px;
}
</style>