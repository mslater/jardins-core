<h2>Approving Reviews</h2>

<?php 
echo GetPaging($paging_page,$paging_pagesize,$paging_q,4,"/admin.php?view=report_missing_companion");
?>

<table class="table">
	<thead>
		<tr>
			<th>Review</th>
			<th>Created Date</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php while ($p = mysql_fetch_object($reviews)):?>
		<tr>
			<td width="60%">
				<strong>Title:</strong><br>
				<p><?php echo $p->title;?></p>
				
				<strong>Pros:</strong><br>
				<p><?php echo str_replace("\n","<br>",$p->pros);?></p>
				
				<strong>Cons:</strong><br>
				<p><?php echo $p->cons;?></p>
				
				<strong>Comment:</strong><br>
				<p><?php echo $p->comment;?></p>
			</td>
			<td><?php echo $p->created_date;?></td>
			<td><a onClick="return confirm('Approve this review?')" href="/admin.php?view=review_check&id=<?php echo $p->id;?>&status=approve" class="btn">Approve</a><br>
				<a onClick="return confirm('Block this review?')" href="/admin.php?view=review_check&id=<?php echo $p->id;?>&status=block" class="btn">Block</a>
			</td>
		</tr>
		<?php endwhile; ?>
	</tbody>
</table>

<?php 
echo GetPaging($paging_page,$paging_pagesize,$paging_q,4,"/admin.php?view=report_missing_companion");
?>