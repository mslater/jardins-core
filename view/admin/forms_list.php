<div>
	<a href="admin.php?view=forms_edit&type=<?php echo $type;?>" class="btn"><?php 
	switch($type)
	{
	case "req":
		echo "Add New Requirement Form";
		break;
	case "care":
		echo "Add New Care Form";
		break;
	case "contact":
		echo "Add New Contact Form";
		break;
	}
	?></a>
</div>
<table class="table">
	<thead>
		<th>Form Name</th>
		<th>Action</th>
	</thead>
	<tbody>
		<?php
		while ($r = mysql_fetch_object($data_forms))
		{
			echo '<tr>
				<td>'.$r->name.'</td>
				<td><a class="btn" href="admin.php?view=forms_edit&id='.$r->id.'&type='.$type.'">Edit</a>
				<a class="btn" href="admin.php?view=forms_edit&delete_id='.$r->id.'&type='.$type.'" onClick="return confirm(\'Are you sure delete this form\');">Delete</a>
				</td>
			</td>';
		}
		?>
	</tbody>
</table>
