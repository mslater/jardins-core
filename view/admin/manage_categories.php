<?php
if ($parent_id != 0)
	echo '<a href="/admin.php?view='.CURRENT_VIEW.'&catsec='.$catsec.'" class="btn">Back</a><br>';
?>
<div class="label label-primary"><?php echo @$error;?></div>
  <div class="label label-success"><?php echo @$info;?></div>
  <form method="post">
  <table class="table">
    <thead>
      <tr>
        <th>Category Name in "<?php echo $parent;?>"</th>
        <th>Ordering</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
    	<?php echo $table;?>
    </tbody>
  </table>
  <input type="submit" name="smUpdateOrder" value="Update Order" class="btn"/>
</form>  
<form method="post" enctype="multipart/form-data">
    <h4>Add Category To "<?php echo $parent;?>"</h4>
    <label><?php echo $submenu;?> Category Name</label>
    <input type="text" name="category_name" class="input-medium" value="" placeholder="Category Name">
    <br/>
    <label>Meta Description:</label><textarea name="meta_description" style="width:1000px"></textarea><br/>
    <label>Meta KeyWords:</label><textarea name="meta_keywords" value="" style="width:1000px"></textarea><br/>

    <br>Category Image: <input type="file" name="image"/>
    <div class="form-actions">
      <input type="submit" name="sm_add_category" class="btn btn-primary" value="Add" />
    </div>
  </form>
  
  <form method="post" enctype="multipart/form-data">
  <h4>Modify Category: </h4>
  <input type="text" name="category_name" value="<?php echo stripcslashes($row->category_name);?>"/>
  <br/>
  <label>Meta Description:</label><textarea name="meta_description" style="width:1000px"><?php echo stripcslashes($row->meta_description);?></textarea><br/>
  <label>Meta KeyWords:</label><textarea  name="meta_keywords" style="width:1000px"><?php echo stripcslashes($row->meta_keywords);?></textarea><br/>
  <br><img src="/scaleimage.php?w=150&h=150&t=productimage&f=<?php echo $row->picture;?>" />
  <label><input type="checkbox" <?php if ($row->visible == 1) echo ' checked="checked" '; ?> name="visible" id="visible" value="1" /> Visible</label>
  <label><input type="checkbox" <?php if ($row->only_show_treeview == 1) echo ' checked="checked" '; ?> name="only_show_treeview" id="only_show_treeview" value="1" /> Only show on Tree View</label>
  <label><input type="checkbox" <?php if ($row->only_show_thumbnail == 1) echo ' checked="checked" '; ?> name="only_show_thumbnail" id="only_show_thumbnail" value="1" /> Only show on Thumbnail View</label>
  <label><input type="checkbox" name="delete_image" value="1" /> Delete</label>
  Update Image: <input type="file" name="image"/>
  <div class="form-action">
  	<input type="submit" name="sm_update_category" class="btn btn-primary" value="Update" />
  	<input type="submit" name="sm_delete_category" onclick="return confirm('Are you sure?');" class="btn btn-primary" value="Delete" />
  	<a href="<?php echo url_category($row->id);?>" class="btn btn-primary">View Front Page</a>
  <div>
  </form>