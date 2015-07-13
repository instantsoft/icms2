<style>
	* { font-family: sans-serif; font-size: 14px; }
	#image_preview { padding:15px; }
	#image_preview .button { margin-bottom: 5px; }
	#image_preview .image img { width:100%; }
</style>

<div id="image_preview">
	<div class="button">
		<?php echo html_button(LANG_CANCEL, "cancel", "cancel()"); ?>		
	</div>
	<div class="image">
		<img src="<?php echo $url; ?>">
	</div>
</div>

<script>
	parent.fileclick('<?php echo $url; ?>');
	function cancel(){
		parent.fileclick('');
		window.location.href = '<?php echo $this->href_to('upload'); ?>';
	}
</script>
