<h3>Plan Pages</h3>
<table class="table">
	<thead>
		<th>Title</th>
		<th>Image</th>
		<th>Category</th>
		<th>Description</th>
		<th>Status</th>
		<th>Actions</th>
	</thead>
	<tbody>
		<?php 
		while ($r = mysql_fetch_object($data_list))
		{
			echo '<tr>
				<td>'.$r->name.'</td>
				<td><img src="'.BASE_URL.'upload/product_images/'.$r->main_picture.'" style="width:80px" /></td>
				<td>'.$r->category_name.'</td>
				<td>'.excerpt($r->description,50).'</td>
				<td>'. ($r->status == 1 ? "Visible" : "Invisible") .'</td>
				<td><a class="btn" href="admin.php?view=products&edit_id='.$r->id.'" targe="_blank">Edit</a></td>
			</tr>';
		}
		?>
	</tbody>
</table>