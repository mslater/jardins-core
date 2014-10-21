<?php $xajax->printJavascript(); ?>

<form>
	<input type="hidden" name="view" value="products_list" />
	<input type="hidden" name="category" value="<?php echo $_GET["category"];?>" />
	<label>Search on Titles (<?php echo $total;?> products in): </label>
	<div>
	<select name="filter_feature">
		<option <?php echo $_GET["filter_feature"] == "" ? ' selected="selected" ' : ""; ?> value="">Both Featured/Non-Featured</option>
		<option <?php echo $_GET["filter_feature"] == "0" ? ' selected="selected" ' : ""; ?> value="0">Not Featured</option>
		<option <?php echo $_GET["filter_feature"] == "1" ? ' selected="selected" ' : ""; ?> value="1">Featured</option>
	</select>
	<select name="filter_status">
		<option <?php echo $_GET["filter_status"] == "" ? ' selected="selected" ' : ""; ?> value="">Any Visibility</option>
		<option <?php echo $_GET["filter_status"] == "0" ? ' selected="selected" ' : ""; ?> value="0">Invisible</option>
		<option <?php echo $_GET["filter_status"] == "1" ? ' selected="selected" ' : ""; ?> value="1">Visible</option>
	</select>
	<select name="filter_category">
		<option value="">Any Sub-category</option>
		<?php
		$datacat = mysql_query("SELECT * FROM product_category WHERE category_type='".$_GET["category"]."' ORDER BY category_name");
		while ($c = mysql_fetch_object($datacat))
		{
			echo '<option '. ($_GET["filter_category"] == $c->id ? ' selected="selected" ' : "") .' value="'.$c->id.'">'.$c->category_name.'</option>';
		}
		?>
	</select>
	<input class="input-medium" name="searchKw" value="<?php echo @$_GET["searchKw"];?>" />
	<input class="btn" type="submit" name="smSearch" value="Search"/>
	</div>
</form>

<form method="post">
<div>
	<a href="javascript:$('#bulkeditor').toggle()" class="btn">Bulk Edit</a>
	<div id="bulkeditor" style="display:none;" class="first clear span-24">
		<hr />
		<div class="first span-7">
			<label>Visibility </label>
			<label style="margin-left:30px" class="radio">
					 <input type="radio" value="" name="radio_publish" checked="checked"> No change</label>
			<label style="margin-left:30px" class="radio">
					 <input type="radio" value="0" name="radio_publish"> Invisible</label>
			 <label style="margin-left:30px" class="radio">
					 <input type="radio" value="1" name="radio_publish"> Visible</label>
		</div>
		
		<div class="span-10">
			<label>Category</label>
			
			<select name="category_edit_type">
				<!--option value="replace">Replace old category</option-->
				<option value="append">Append to old category</option>
			</select>
			 <div class="categorylist" style="height: 329px;
	overflow: scroll;
	border: solid;
	width: 341px;">
			  <?php 
			  GenerateCategoryList('gi',"Garden Ideas");
			  GenerateCategoryList('pl',"Plants");
			  GenerateCategoryList('ds',"Designer");
			  GenerateCategoryList('bs',"Basic");
			  GenerateCategoryList('pr',"Promenades");
			  ?>
			  </div>
		  </div>
		<br class="clear"/>
		<input type="submit" class="btn btn-primary clear" value="Submit" name="smBulkUpdate" />
	<br>
	<hr />
	</div>
<div>

<h3><?php
if ($_GET["category"] == 'gi')
	echo 'Garden Ideas';
elseif ($_GET["category"] == 'bs')
	echo 'Basic';
elseif ($_GET["category"] == 'pl')
	echo 'Plants';
elseif ($_GET["category"] == 'pr')
	echo 'Promenades';
elseif ($_GET["category"] == 'ds')
	echo 'Designer';
?> Pages</h3>
<script>
function SelectAllProducts(sel)
{
	if ($(sel).is(':checked'))
		$('.prochb').prop('checked',true);
	else
		$('.prochb').prop('checked',false);
}
</script>
<table class="table">
	<thead>
		<th><a href="?view=products_list&category=<?php echo $_GET["category"];?>&searchKw=<?php echo urlencode($_GET["searchKw"]);?>&sort_field=name&sort_type=<?php if ($sort_field != "name") echo "asc"; elseif ($sort_type == "desc") echo "asc"; else echo "desc";?>">Title</a>
			<br><input type="checkbox" onchange="SelectAllProducts(this)"/> Select All
		</th>
		<th>Image</th>
		<th style="width:350px;">Category</th>
                <th><a href="?view=products_list&filter_feature=<?=$_GET[filter_feature]?>&filter_status=<?=$_GET[filter_status]?>&filter_category=<?=$_GET[filter_category]?>&category=<?php echo $_GET["category"];?>&searchKw=<?php echo urlencode($_GET["searchKw"]);?>&sort_field=status&sort_type=<?php if ($sort_field != "status") echo "asc"; elseif ($sort_type == "desc") echo "asc"; else echo "desc";?>">Status</a></th>
		<th><a href="?view=products_list&filter_feature=<?=$_GET[filter_feature]?>&filter_status=<?=$_GET[filter_status]?>&filter_category=<?=$_GET[filter_category]?>&category=<?php echo $_GET["category"];?>&searchKw=<?php echo urlencode($_GET["searchKw"]);?>&sort_field=view_count&sort_type=<?php if ($sort_field != "view_count") echo "asc"; elseif ($sort_type == "desc") echo "asc"; else echo "desc";?>">View</a></th>
		<th><a href="?view=products_list&filter_feature=<?=$_GET[filter_feature]?>&filter_status=<?=$_GET[filter_status]?>&filter_category=<?=$_GET[filter_category]?>&category=<?php echo $_GET["category"];?>&searchKw=<?php echo urlencode($_GET["searchKw"]);?>&sort_field=created_date&sort_type=<?php if ($sort_field != "created_date") echo "asc"; elseif ($sort_type == "desc") echo "asc"; else echo "desc";?>">Created Date</a></th>
		<th>Actions</th>
	</thead>
	<tbody>
		<?php 
		$c = 1;
		while ($r = mysql_fetch_object($data_list))
		{
			$cat = "";
			$dat_cat = mysql_query("SELECT * FROM product_category AS pc
				INNER JOIN product_in_category AS pic ON pc.id=pic.category_id
				WHERE pic.product_id='{$r->id}'");
			while ($rc = mysql_fetch_object($dat_cat))
				$cat .= $rc->category_name . ", ";
			
			$pro_type_data = mysql_query("SELECT DISTINCT pc.category_type FROM product_category AS pc
				INNER JOIN product_in_category AS pic ON pic.category_id=pc.id
				WHERE pic.product_id='{$r->id}'");
		    $product_types = array();
		    while ($r2 = mysql_fetch_object($pro_type_data))
		        $product_types[] = $r2->category_type;
			if (in_array('pl',$product_types))
			{
				$counterdata = mysql_query("SELECT gs.country, psi.product_id, COUNT( gs.country ) AS counter
						FROM product_seller_item AS psi
						INNER JOIN keyword_category AS kc ON kc.id = psi.keyword_category_id
						INNER JOIN keyword AS k ON k.category_id = kc.id
						INNER JOIN google_scraped AS gs ON gs.keyword_id = k.id
						WHERE psi.product_id='{$r->id}'
						GROUP BY gs.country, psi.product_id");
				$countrycount = array();
				$countrycount["US"] = 0;
				$countrycount["UK"] = 0;
				$countrycount["FR"] = 0;
				$countrycount["AU"] = 0;
				$countrycount["DE"] = 0;
				$countrycount["IT"] = 0;
				$countrycount["LN"] = 0;
				$countrycount["CH"] = 0;
				$countrycount["ES"] = 0;
				while ($rc = mysql_fetch_object($counterdata))
				{
					$countrycount[$rc->country] = $rc->counter;
				}
				
				$couterdata = mysql_query("SELECT COUNT(*) AS counter FROM product AS p 
					INNER JOIN related_product AS pr ON pr.product_id=p.id
					WHERE p.id='{$r->id}'");
				$compCounter = 0;
				$rc = mysql_fetch_object($couterdata);
				$compCounter = $rc->counter;
			}
			echo '<tr id="pdr_'.$r->id.'">
				<td><input class="prochb" type="checkbox" name="selectedid[]" value="'.$r->id.'" />'.($c++) . ". ".$r->name.'<br><br>';
				
			if (isset($countrycount))
			{
				foreach ($countrycount as $k => $v)
					echo $k."(".$v.") ";
				echo "<br># of Companions: $compCounter";
			}
			echo '</td>
				<td><img src="/scaleimage.php?w=100&h=100&t=productimage&f='.urlencode($r->main_picture).'" style="width:80px" /></td>
				<td><div class="cat_list">'.$cat.'</div></td>
				<td>'. ($r->status == 1 ? "Visible" : "Invisible") .'</td>
				<td>'. $r->view_count .'</td>
				<td>'.date('m/d/Y',strtotime($r->created_date)).'</td>
				<td><a class="btn" href="admin.php?view=products&edit_id='.$r->id.'" targe="_blank">Edit</a>
				<a class="btn" target="_blank" onclick="return confirm(\'Are you sure?\')" href="javascript:DeleteProductFromList('.$r->id.')" targe="_blank">Delete</a>
				<a class="btn" target="_blank" href="'.url_product_detail($r->id).'" targe="_blank">View</a>
				<a class="btn" href="admin.php?view=products&similar_id='.$r->id.'" targe="_blank">Add Similar</a>
				</td>
			</tr>';
		}

		?>
	</tbody>
</table>
</form>

<script>
function DeleteProductFromList(id)
{
	$("#pdr_"+id).remove();
	xajax_DeleteProductFromList(id);
}
</script>