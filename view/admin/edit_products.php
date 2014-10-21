<script src="<?php echo BASE_URL. 'static/ckeditor/ckeditor.js';?>"></script>
<?php
echo @$_GET["msg"] == "s" ? ' <div class="label label-success">Product is updated successfully</div> ' : "";
?>
<form id="product_edit_form" method="post" enctype="multipart/form-data" >
	<?php if (!isset($_GET["edit_id"])):?>
		<input type="submit" value="Add" class="btn btn-primary" name="smAddProduct"/>
	<?php else: ?>
		<input type="submit" value="Update" class="btn btn-primary" name="smEditProduct"/>
		<a class="btn" href="<?php echo url_product_detail($_GET["edit_id"]);?>" >Preview</a>
		<a class="btn" onclick="return confirm('Are you sure?')" href="/admin.php?view=products&delete_id=<?php echo $_GET["edit_id"];?>" >Delete Post</a>
		<br><input type="text" value="<?php echo url_product_detail($_GET["edit_id"]);?>" class="input-xxlarge"/>
	<?php endif; ?>
	<br /><br />
	<div id="tabs">
		<ul>
		 <li><a href="#tabs-1">Product Info</a></li>
		 <li><a href="#tabs-2">Gallery</a></li>
		 <li><a href="#tabs-3">Other Info</a></li>
		 <li><a href="#tabs-4">Seller Products</a></li>
		 <li><a href="#tabs-5">Companions</a></li>
	  </ul>
	  
	  <div id="tabs-1">
		 <label>Product Title</label>
		  <input type="text" required name="title" class="input-medium" value="<?php echo @stripcslashes($product_info->name);?>" style="width:600px">
		 <label>Common Name</label>
		  <input type="text" name="common_name" class="input-medium" value="<?php echo @stripcslashes($product_info->common_name);?>" style="width:600px">
		 <label>Intro</label>
		  <input type="text" name="intro" class="input-medium" value="<?php echo @stripcslashes($product_info->intro);?>" style="width:600px">
		  
		  <div class="categorylist" style="height: 329px;
overflow: scroll;
border: solid;
margin: 12px;
padding: 5px;
float: right;
margin-top: -32px;
width: 241px;">
		  <?php 
		  GenerateCategoryList('gi',"Garden Ideas");
		  GenerateCategoryList('pl',"Plants");
		  GenerateCategoryList('ds',"Designer");
		  GenerateCategoryList('bs',"Basic");
		  GenerateCategoryList('pr',"Promenades");
		  ?>
		  </div>
		  <label>Publishing</label>
		  <label style="margin-left:30px" class="radio" for="radio_invi">
				 <input <?php echo (!isset($product_info) || $product_info->status == 0 ? ' checked="checked" ' : "")?> type="radio" value="0" id="radio_invi" name="radio_publish"> Invisible</label>
		 <label style="margin-left:30px" class="radio" for="radio_vi" value="0">
				 <input <?php echo (isset($product_info) && $product_info->status == 1 ? ' checked="checked" ' : "")?> type="radio" value="1" id="radio_vi" name="radio_publish"> Visible</label>
				<label style="margin-left:30px">Publishing Date <input type="text" name="publishing_date" value="<?php echo isset($product_info) ? @date('Y-m-d',$product_info->publishing_date) : date("Y-m-d"); ?>" id="publishing_date" /> </label>
		  <label>Featured Post</label>
		  <label style="margin-left:30px" class="radio" for="radio_feature_on">
				 <input <?php echo (!isset($product_info) || $product_info->isfeatured == 0 ? ' checked="checked" ' : "")?> type="radio" value="0" id="radio_feature_on" name="radio_feature"> Not Feature Post</label>
		 <label style="margin-left:30px" class="radio" for="radio_feature_off" value="0">
				 <input <?php echo (isset($product_info) && $product_info->isfeatured == 1 ? ' checked="checked" ' : "")?> type="radio" value="1" id="radio_feature_off" name="radio_feature"> Feature Post</label>
		
		
			 
		  <label>Main Picture</label>
		  <input type="file" name="main_picture">
		  <?php
		  if (isset($product_info) && $product_info->main_picture != '')
		  	  echo '<img src="/scaleimage.php?w=100&h=100&t=productimage&a=1&f='.urlencode($product_info->main_picture).'" /> Delete Image: <input type="checkbox" name="delete_image" value="1" />';
		  ?>
		  <label>Main Picture Alt Text</label><input type="text" name="image_alt" value="<?php echo stripcslashes(@$product_info->image_alt);?>">
		  <label>Layout</label>
		  <input type="checkbox" name="wider_content" <?php echo ($product_info->wider_content == 1 ? " checked='checked' " : "");?> value="1" />  Wide Content<br>
		  <input type="checkbox" name="product_tabs" <?php echo ($product_info->product_tabs == 1 ? " checked='checked' " : "");?> value="1" />  Show Product Tabs
		  <br>
		  <br>
		  <br>
		  <label>Description</label>
		  <textarea name="description" class="ckeditor" id="description"><?php echo @stripcslashes($product_info->description);?></textarea>
		  <label>Companion Plants</label>
	  </div>
	  <div id="tabs-2">
	  	<div class="clearfix">
	  	<table>
	  		<?php
		 while ($r = @mysql_fetch_object($data_photos))
		 	 echo '<tr>
		 		<td>
		 		<img src="/scaleimage.php?w=100&h=100&t=productimage&a=1&f='.stripcslashes($r->name).'" />
		 		<br><label class="checkbox" for="checkbox"><input type="checkbox" value="'.$r->id.'" name="delete_photo[]"> Delete</label>
		 		<br>Order: <input name="ordering[]" value="'.$r->ordering.'" class="input-small" style="width:25px;" />
		 		</td><td>
		 		Alter Text: <input name="old_photo_altertext[]" type="text" class="input-xxlarge" value="'.stripcslashes($r->alter_text).'"> <br>
		 		Caption: <input name="old_photo_caption[]" type="text" class="input-xxlarge" value="'.stripcslashes($r->caption).'"> <br>
		 		Description: <input name="old_photo_desc[]" type="text" class="input-xxlarge" value="'.stripcslashes($r->description).'"> <br>
		 		URL: <input type="text" class="input-xxlarge" value="'.BASE_URL.'upload/product_images/'.$r->name.'"> <br>
		 		<input type="hidden" value="'.$r->id.'" name="old_photo_id[]">
		 		</td>
		 	</tr><tr><td colspan="2"><hr /></td></tr>';
		 ?>
	  	</table>
		 </div>
		 <div>
		 	<div id="UploadPhotoContainer"></div>
		 	<a href="javascript:void()" onClick="AddMorePhoto()" class="btn">Add Photo</a>
		 </div>
	  </div>
	  <div id="tabs-3">
	  	  <label>Calculator Name:</label>
	  	  <input type="text" style="width:100%" name="calculator_name" class="input-medium" value="<?php echo @stripcslashes($product_info->calculator_name);?>" style="width:600px">
	  	  <label>Calculator Size:</label>
	  	  <input type="text" style="width:100%" name="calculator_size" class="input-medium" value="<?php echo @$product_info->calculator_size;?>" style="width:300px">
		  <label>SEO Keywords:</label>
	  	  <textarea name="seo_keyword" class="input-medium" style="width:100%"><?php echo @stripcslashes($product_info->seo_keyword);?></textarea>
		  <label>SEO Description:</label>
	  	  <textarea name="seo_description" class="input-medium" style="width:100%"><?php echo @stripcslashes($product_info->seo_description);?></textarea>
		  <label>Requirements</label>
		  <select name="requirement">
		  	<option value="">Select One</option>
		  <?php
		  while ($r = mysql_fetch_object($data_requirement))
		  {
		  	  echo '<option '. ($product_info->requirement_id == $r->id ? 'selected="selected"' : "") .' value="'.$r->id.'">'.$r->name.'</option>';
		  }
		  ?>
		  </select>
		  <label>Care</label>
		  <select name="care">
		  	<option value="">Select One</option>		
		  <?php
		  while ($r = mysql_fetch_object($data_care))
		  {
		  	  echo '<option '. (@$product_info->care_id == $r->id ? 'selected="selected"' : "") .' value="'.$r->id.'">'.$r->name.'</option>';
		  }
		  ?>
		  </select>
		  <label>Contact</label>
		  <select name="contact">
		  	<option value="">Select One</option>		
		  <?php
		  while ($r = mysql_fetch_object($data_contact))
		  {
		  	  echo '<option '. (@$product_info->contact_form_id == $r->id ? 'selected="selected"' : "") .' value="'.$r->id.'">'.$r->name.'</option>';
		  }
		  ?>
		  </select>
		  <label>Map Address (you can enter address, coordination etc.):</label>
		  <input type="text" name="map" id="map" class="input-xxlarge" value="<?php echo @$product_info->map_address;?>"/>
		  <a href="javascript:void(0)" onClick="CheckMap()" class="btn" >Check</a>
		  
		  <label>Upload Award Icon:</label>
		  <?php
		  if ($product_info->reward_icon != '')
			  echo '<img src="/scaleimage.php?w=70&h=70&t=productimage&f='.$product_info->reward_icon.'" /> <label><input type="checkbox" value="1" name="delete_reward_icon"/>Delete</label><br>';
		  ?>
		  <input type="file" name="reward_icon" />
	  </div>
	  <div id="tabs-4">
		<h4>Seller Categories:</h4>
		<div id="category_list"></div>
		<br>
		<button onClick="return AddCategory('')" class="btn">Add More</button> 
	  </div>
	  <div id="tabs-5">
		<h4>Companion Products:</h4>
		<div id="related_list"></div>
		<br>
		<button onClick="return AddRelated('')" class="btn">Add More</button> 
	  </div>
	</div>
</form>
<script>
$("#publishing_date").datepicker({dateFormat:"yy-mm-dd"});
function CheckMap()
{
	newwindow=window.open("https://maps.google.co.uk/?q="+$("#map").val(),'mapcheck','height=500,width=750');
}

function AddMorePhoto()
{
	$("#UploadPhotoContainer").append("<input type=\"file\" name=\"photo[]\" /><br>Alter Text: <input type=\"text\" name=\"photo_altertext[]\" style=\"width:600px\" class=\"input-medium\" /><br>Caption: <input type=\"text\" name=\"photo_caption[]\" style=\"width:600px\" class=\"input-medium\" /><br>Description: <input type=\"text\" name=\"photo_desc[]\" style=\"width:600px\" class=\"input-medium\" /><br><hr />");
}

<?php
while ($r = @mysql_fetch_object($data_sellers))
	echo ' AddCategory("'.$r->keyword_category_id.'"); ';
?>
<?php
while ($r = @mysql_fetch_object($relatedproducts))
	echo ' AddRelated("'.$r->related_id.'"); ';
?>

AddCategory("");
AddRelated("");

$(function() {
		$( "#tabs" ).tabs();
});

<?php 
if (isset($product_info)) 
	echo '$("#category_'.$product_info->product_type.'").val('.$product_info->category.');';
?>

function AddCategory(selected)
{
	$("#category_list").append('<?php
		$res = '<div class="category_list_item"><select name="seller_categories[]">';
		$res .= '<option value="">Select a Category</option>';
		while ($r = mysql_fetch_object($data_categories))
		{
			$res .= '<option value="'.$r->id.'">'.$r->category.'</option>';
		}
		$res .= '</select></div>';
		echo str_replace('\'','\\\'',$res);
		?>');
	if (selected != "")
		$("#category_list .category_list_item:last select").val(selected);
	return false;
}

function AddRelated(selected)
{
	$("#related_list").append('<?php
		$data_related = mysql_query("SELECT * FROM product ORDER BY name");
		$res = '<div class="related_list_item"><select name="related_products[]">';
		$res .= '<option value="">Select a Category</option>';
		while ($r = mysql_fetch_object($data_related))
		{
			$res .= '<option value="'.$r->id.'">'.str_replace("'","",$r->name).'</option>';
		}
		$res .= '</select></div>';
		echo str_replace('\'','\\\'',$res);
		?>');
	if (selected != "")
		$("#related_list .related_list_item:last select").val(selected);
	return false;
}

<?php
if (isset($_GET["catsec"]))
{
	echo '$("#radio_'.$_GET["catsec"].'").attr(\'checked\',true);';
	if (isset($_GET["catid"]))
	{
		echo '$("#category_'.$_GET["catsec"].'").val(\''.$_GET["catid"].'\');';
	}
}
?>

<?php
while ($r = @mysql_fetch_object($data_catagory))
{
	echo '$("#category_'.$r->category_id.'").attr("checked","checked");';
}
?>
    
SetValidationForm("#product_edit_form","top");
</script>