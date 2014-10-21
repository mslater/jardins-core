<script src="<?php echo BASE_URL. 'static/ckeditor/ckeditor.js';?>"></script>
<form id="csv_edit_form" method="post">
	<label>Page Title</label><input required type="text" required name="title" class="input-xxlarge" value="<?php echo @stripcslashes($r->title);?>">
	<label>Content</label>
	<textarea name="content" class="ckeditor"><?php echo htmlentities(@stripcslashes($r->content));?></textarea>
	<div>
		<br>
		<input type="submit" class="btn" name="smCMS" value="Update" />
		<a href="<?php echo url_cms($id);?>" class="btn">Preview</a>
	</div>
</form>
<script>
SetValidationForm("#csv_edit_form","top");
</script>