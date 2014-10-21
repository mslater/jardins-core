<script src="<?php echo BASE_URL. 'static/ckeditor/ckeditor.js';?>"></script>

<h3><?php
if ($type == "req")
	echo "Requirement Form";
if ($type == "care")
	echo "Care Form";
if ($type == "contact")
	echo "Contact Form";
?></h3>
<form method="post">
	<label>Name</label>
	<input type="text" class="input-xxlarge" name="name" style="width:100%" value="<?php echo @stripcslashes($form->name);?>" />
	<label>Related Product Category</label>
	<select name="category">
		<option value="">Related Product Category</option>
		<?php
		while ($r = mysql_fetch_object($data_category))
		{
			if (@$form->keyword_category_id == $r->id)
				echo '<option selected="selected" value="'.$r->id.'">'.$r->category.'</option>';
			else
				echo '<option value="'.$r->id.'">'.$r->category.'</option>';
		}
		?>
	</select>
	<label>Form Content</label>
	<textarea class="ckeditor" name="content"><?php echo @stripcslashes($form->content);?></textarea>
	<br>
	<input type="submit" class="btn" name="smUpdate" value="Submit"/>
</form>
