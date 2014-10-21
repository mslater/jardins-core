<h2>Missing price products</h2>

<?php 
echo GetPaging($paging_page,$paging_pagesize,$paging_q,4,"/admin.php?view=report_missing_price");
?>

<table class="table">
	<thead>
		<tr>
			<th>Name</th>
			<th>Status</th>
			<th>Created Date</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php while ($p = mysql_fetch_object($products)):?>
		<tr>
			<td><?php echo $p->name;?></td>
			<td><?php echo ($p->status == 1) ? "Public" : "Hidden"; ?></td>
			<td><?php echo $p->created_date;?></td>
			<td><a href="/admin.php?view=products&edit_id=<?php echo $p->id;?>" target="_blank" class="btn">Edit</a></td>
		</tr>
		<?php endwhile; ?>
	</tbody>
</table>

<?php 
echo GetPaging($paging_page,$paging_pagesize,$paging_q,4,"/admin.php?view=report_missing_price");
?>