<h1><?php echo LANG_STEP_CLEANUP; ?></h1>

<p><?php echo LANG_CLEANUP_INFO; ?></p>

<form id="step-form">

    <?php if (empty($cleanup_items)): ?>
    <p class="success"><?php echo LANG_CLEANUP_NOTHING; ?></p>
    <?php else: ?>
    
    <p class="size-info">
        <?php echo LANG_CLEANUP_TOTAL; ?>: <strong><?php echo format_size($total_size); ?></strong>
    </p>

    <fieldset>
        <legend><?php echo LANG_CLEANUP_SELECT; ?></legend>

        <div class="cleanup-list">
        <?php foreach ($cleanup_items as $item): ?>
        <label class="cleanup-item">
            <input type="checkbox" 
                   name="remove_components[]" 
                   value="<?php echo urlencode($item['path']); ?>"
                   checked>
            <div class="cleanup-content">
                <span class="cleanup-type">[<?php echo constant('LANG_CLEANUP_TYPE_' . strtoupper($item['type'])); ?>]</span>
                <span class="cleanup-name"><?php echo htmlspecialchars($item['name']); ?></span>
                <span class="cleanup-title"><?php echo $item['title']; ?></span>
                <span class="cleanup-size"><?php echo format_size($item['size']); ?></span>
            </div>
        </label>
        <?php endforeach; ?>
        </div>

        <div class="select-actions">
            <a href="#" onclick="$('.cleanup-item input').prop('checked', true); return false;"><?php echo LANG_SELECT_ALL; ?></a>
            <a href="#" onclick="$('.cleanup-item input').prop('checked', false); return false;"><?php echo LANG_DESELECT_ALL; ?></a>
        </div>

    </fieldset>

    <?php endif; ?>

</form>

<div class="buttons">
    <input type="button" value="<?php echo LANG_BACK; ?>" onclick="prevStep()">
    <input type="button" value="<?php echo LANG_NEXT; ?>" onclick="submitStep()"<?php if (empty($cleanup_items)) echo ' style="display:none"'; ?>>
</div>

<style>
.size-info {
    padding: 10px 15px;
    background: #e8f4ff;
    border-radius: 6px;
    margin-bottom: 20px;
}
.size-info strong {
    color: #007bff;
}
.cleanup-list {
    max-height: 400px;
    overflow-y: auto;
}
.cleanup-item {
    display: flex;
    align-items: flex-start;
    padding: 12px 15px;
    margin-bottom: 8px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    background: #fafafa;
    cursor: pointer;
}
.cleanup-item:hover {
    background: #f0f0f0;
}
.cleanup-item input[type="checkbox"] {
    margin-right: 15px;
    margin-top: 3px;
    width: 18px;
    height: 18px;
}
.cleanup-content {
    display: flex;
    flex-direction: column;
    flex: 1;
}
.cleanup-type {
    font-size: 11px;
    color: #888;
    text-transform: uppercase;
}
.cleanup-name {
    font-size: 14px;
    font-weight: 500;
}
.cleanup-title {
    font-size: 13px;
    color: #666;
}
.cleanup-size {
    font-size: 12px;
    color: #999;
    margin-top: 4px;
}
.select-actions {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #eee;
}
.select-actions a {
    color: #007bff;
    margin-right: 15px;
    font-size: 13px;
}
.select-actions a:hover {
    text-decoration: underline;
}
p.success {
    padding: 15px;
    background: #d4edda;
    border-radius: 6px;
    color: #155724;
}
</style>
