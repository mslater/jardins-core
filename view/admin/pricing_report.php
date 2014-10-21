<script src="<?php echo BASE_URL; ?>static/DataTables-1.9.4/media/js/jquery.dataTables.min.js" ></script>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>static/DataTables-1.9.4/media/css/jquery.dataTables.css" type="text/css" />
<table id="datatable">
	<thead>
		<tr>
			<td>Category Name</td>
			<td>US #</td>
			<td>UK #</td>
			<td>FR #</td>
			<td>In Post</td>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($returned_data as $r)
		{
			echo '<tr>
			<td>'.$r[0].'</td>
			<td>'.$r[1].'</td>
			<td>'.$r[2].'</td>
			<td>'.$r[3].'</td>
			<td>'.$r[4].'</td>
		</tr>';
		}
		?>
	</tbody>
</table>

<script>
	$(document).ready(function() {
		$('#datatable').dataTable();
	}); 
</script>