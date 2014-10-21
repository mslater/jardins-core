<script src="<?php echo BASE_URL. 'static/ckeditor/ckeditor.js';?>"></script>
<div id="tabs">
	<ul>
        <li><a href="#tabs-1">Home Page Slider</a></li>
        <li><a href="#tabs-2">Others</a></li>
	</ul>
    <form method="post" enctype="multipart/form-data">
	    <div id="tabs-1">
			<table>
				<?php
			 while ($r = @mysql_fetch_object($data_sliderphotos))
				 echo '<tr>
					<td>
					<img src="/scaleimage.php?w=100&h=100&t=productimage&a=1&f='.stripcslashes($r->image).'" />
					<br><label class="checkbox" for="checkbox"><input type="checkbox" value="'.$r->id.'" name="delete_photo[]"> Delete</label>
					<label class="checkbox" for="checkbox"><input type="checkbox" value="'.$r->id.'" '. ($r->enable == 1 ? " checked='checked' " : "") .' name="enable_photo[]"> Enable</label>
					</td><td>
					Caption: <input name="old_photo_caption[]" type="text" class="input-xxlarge" value="'.stripcslashes($r->caption).'"> <br>
					Related URL: <input name="old_photo_url[]" type="text" class="input-xxlarge" value="'.stripcslashes($r->link).'"> <br>
					<input type="hidden" value="'.$r->id.'" name="old_photo_id[]">
					</td>
				</tr><tr><td colspan="2"><hr /></td></tr>';
			 ?>
			</table>
			<div id="UploadPhotoContainer"></div>
			<a href="javascript:void()" onClick="AddMorePhoto()" class="btn">Add Photo</a>
			<br><br>
			<input type="submit" class="btn btn-primary" name="smUpdateSlider" value="Update Slider Images" />
	    </div>
        <div id="tabs-2">
            <table>
                <tr>
                    <td>Admin Email</td>
                    <td><input type="text" name="admin_email" value="<?php echo GetSetting("admin_email");?>"/></td>
                </tr>
                <tr>
                    <td>Site Description</td>
                    <td><input type="text" name="site_description" value="<?php echo GetSetting("site_description");?>" style="width:100%;"/></td>
                </tr>
                <tr>
                    <td>Site Keyword</td>
                    <td><input type="text" name="site_keyword" value="<?php echo GetSetting("site_keyword");?>" style="width:100%;"/></td>
                </tr>
                <tr>
                    <td colspan="2">Review Rules
                    	<textarea name="review_rules" class="ckeditor"><?php echo htmlentities(@stripcslashes(GetSetting("review_rules")));?></textarea></td>
                </tr>
            </table>
            <input type="submit" value="Save" name="smUpdateSettings" class="btn" />
            <a href="/admin.php?view=cleancache" target="_blank" class="btn">Clean Cache</a>
        </div>
    </form>
</div>

<script>
function AddMorePhoto()
{
	$("#UploadPhotoContainer").append("<input type=\"file\" name=\"photo[]\" /><br>Caption: <input type=\"text\" name=\"photo_caption[]\" style=\"width:600px\" class=\"input-medium\" /><br>Related URL: <input type=\"text\" name=\"photo_url[]\" style=\"width:600px\" class=\"input-medium\" /><hr />");
}


$( "#tabs" ).tabs();
</script>