<?php $this->insertJS('templates/default/js/jquery.js'); ?>
<?php $this->insertJS('templates/default/js/files.js'); ?>

<?php
	$max_size_mb = files_convert_bytes(ini_get('post_max_size'));
	$id = "image";
?>

<style>
	* { font-family: sans-serif; font-size: 14px; }		
	#file_upload { padding:15px; }
	#file_upload .error { color:red; margin-bottom: 10px; }
	#file_upload .hint { color: gray; margin-top:6px; }	
	#file_upload .button { margin-top:15px; }
</style>

<form action="" method="post" enctype="multipart/form-data">

	<?php echo html_csrf_token(); ?>
	
	<div id="file_upload">	
		<?php if (!empty($error)){ ?>
			<div class="error"><?php echo $error; ?></div>
		<?php } ?>
		<?php echo html_file_input($id); ?>
		<?php if ($allowed_extensions){ ?>
			<div class="hint"><?php printf(LANG_PARSER_FILE_EXTS_FIELD_HINT, implode(', ', array_map(function($val) { return trim($val); }, explode(',', mb_strtoupper($allowed_extensions))))); ?></div>
		<?php } ?>
		<?php if ($max_size_mb){ ?>
			<div class="hint"><?php printf(LANG_PARSER_FILE_SIZE_FIELD_HINT, files_format_bytes($max_size_mb)); ?></div>
		<?php } ?>		
		<div class="button">
			<?php echo html_submit(LANG_UPLOAD); ?>
		</div>
	</div>
	
</form>
