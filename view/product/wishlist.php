<div class="well" style="margin-top: 17px;">
<a style="margin-bottom:10px;" href="<?php echo $_SESSION["lastest_wishlist_ref"];?>" class="btn">Back</a>
<form method="post">
<table class="table">
<tbody>
<?php
function show_wishlist_row_pr($p)
{
    echo '<tr>
					<td><input type="hidden" name="type[]" value="'.$p["type"].'" />
			<input type="hidden" name="id[]" value="'.$p["id"].'" />
			<a href="'.$p['link'].'" target="_blank"><img src="/scaleimage.php?w=100&h=100&t=productimage&f='.$p["picture"].'" /></a>
			</td>
					<td>'.stripcslashes($p["name"]).'<br>'.($p["merchant"] != '' ? $p['merchant'] : "").'</td>
					<td>'.GetForm($p["contact_form_id"]).'</td>
					<td>' .($p['map_address'] != "" ? '<a target="_blank" href="https://maps.google.com/?q='. urlencode($p['map_address']).'"><img src="http://maps.googleapis.com/maps/api/staticmap?center='. urlencode($p['map_address']).'&size=150x150&maptype=roadmap&markers=color:blue%7Clabel:A%7C'. urlencode($p['map_address']).'&sensor=false" /><a>' : "").'</td>
					<td><input type="button" value="Delete" class="btn" onClick="DeleteWishlist('.$p["id"].',\''.$p['type'].'\')"/></td>
				</tr>';
}
function show_wishlist_row_gi($p)
{
    echo '<tr>
					<td><input type="hidden" name="type[]" value="'.$p["type"].'" />
			<input type="hidden" name="id[]" value="'.$p["id"].'" />
			<a href="'.$p['link'].'" target="_blank"><img src="/scaleimage.php?w=100&h=100&t=productimage&f='.$p["picture"].'" /></a><br>
			</td>
					<td>'.stripcslashes($p["name"]).'<br>'.($p["merchant"] != '' ? $p['merchant'] : "").'</td>
					<td>'.($p['price'] != '' ? CURRENCY.$p['price'] : "").'</td>
					<td></td>
					<td>' .($p['map_address'] != "" ? '<a target="_blank" href="https://maps.google.com/?q='. urlencode($p['map_address']).'"><img src="http://maps.googleapis.com/maps/api/staticmap?center='. urlencode($p['map_address']).'&size=150x150&maptype=roadmap&markers=color:blue%7Clabel:A%7C'. urlencode($p['map_address']).'&sensor=false" /><a>' : "").'</td>
					<td><input type="button" value="Delete" class="btn" onClick="DeleteWishlist('.$p["id"].',\''.$p['type'].'\')"/></td>
				</tr>';
}
function show_wishlist_row_ds($p)
{
    echo '<tr>
					<td><input type="hidden" name="type[]" value="'.$p["type"].'" />
			<input type="hidden" name="id[]" value="'.$p["id"].'" />
			<a href="'.$p['link'].'" target="_blank"><img src="/scaleimage.php?w=100&h=100&t=productimage&f='.$p["picture"].'" /></a>
			</td>
					<td>'.stripcslashes($p["name"]).'<br>'.($p["merchant"] != '' ? $p['merchant'] : "").'</td>
					<td>'. GetForm($p["contact_form_id"]).'</td>
					<td></td>
					<td>' .($p['map_address'] != "" ? '<a target="_blank" href="https://maps.google.com/?q='. urlencode($p['map_address']).'"><img src="http://maps.googleapis.com/maps/api/staticmap?center='. urlencode($p['map_address']).'&size=150x150&maptype=roadmap&markers=color:blue%7Clabel:A%7C'. urlencode($p['map_address']).'&sensor=false" /><a>' : "").'</td>
					<td><input type="button" value="Delete" class="btn" onClick="DeleteWishlist('.$p["id"].',\''.$p['type'].'\')"/></td>
				</tr>';
}
function show_wishlist_row_gids($p)
{
    echo '<tr>
					<td><input type="hidden" name="type[]" value="'.$p["type"].'" />
			<input type="hidden" name="id[]" value="'.$p["id"].'" />
			<a href="'.$p['link'].'" target="_blank"><img src="/scaleimage.php?w=100&h=100&t=productimage&f='.$p["picture"].'" /></a>
			</td>
					<td>'.stripcslashes($p["name"]).'<br>'.($p["merchant"] != '' ? $p['merchant'] : "").'</td>
					<td>'.($p['price'] != '' ? CURRENCY.$p['price'] : "").'</td>
					<td>'. GetForm($p["contact_form_id"]).'</td>

					<td>' .($p['map_address'] != "" ? '<a target="_blank" href="https://maps.google.com/?q='. urlencode($p['map_address']).'"><img src="http://maps.googleapis.com/maps/api/staticmap?center='. urlencode($p['map_address']).'&size=150x150&maptype=roadmap&markers=color:blue%7Clabel:A%7C'. urlencode($p['map_address']).'&sensor=false" /><a>' : "").'</td>
					<td><input type="button" value="Delete" class="btn" onClick="DeleteWishlist('.$p["id"].',\''.$p['type'].'\')"/></td>
				</tr>';
}
function show_wishlist_row_bs($p)
{
    /*echo '<tr>
            <td><input type="hidden" name="type[]" value="'.$p["type"].'" />
    <input type="hidden" name="id[]" value="'.$p["id"].'" />
    <a href="'.$p['link'].'" target="_blank"><img src="/scaleimage.php?w=100&h=100&t=productimage&f='.$p["picture"].'" /></a></td>
            <td>'.stripcslashes($p["name"]).'<br>'.($p["merchant"] != '' ? $p['merchant'] : "").'</td>
            <td>'.($p['price'] != '' ? CURRENCY.$p['price'] : "").'</td>
            <td></td>
            <td>' .($p['map_address'] != "" ? '<a target="_blank" href="https://maps.google.com/?q='. urlencode($p['map_address']).'"><img src="http://maps.googleapis.com/maps/api/staticmap?center='. urlencode($p['map_address']).'&size=150x150&maptype=roadmap&markers=color:blue%7Clabel:A%7C'. urlencode($p['map_address']).'&sensor=false" /><a>' : "").'</td>
            <td><input type="button" value="Delete" class="btn" onClick="DeleteWishlist('.$p["id"].',\''.$p['type'].'\')"/></td>
        </tr>';*/
    echo '<tr>
					<td><input type="hidden" name="type[]" value="'.$p["type"].'" />
			<input type="hidden" name="id[]" value="'.$p["id"].'" />
			<a href="'.$p['link'].'" target="_blank"><img src="/scaleimage.php?w=100&h=100&t=productimage&f='.$p["picture"].'" /></a></td>
					<td>'.stripcslashes($p["name"]).'<br>'.($p["merchant"] != '' ? $p['merchant'] : "").'</td>

					<td>' .($p['map_address'] != "" ? '<a target="_blank" href="https://maps.google.com/?q='. urlencode($p['map_address']).'"><img src="http://maps.googleapis.com/maps/api/staticmap?center='. urlencode($p['map_address']).'&size=150x150&maptype=roadmap&markers=color:blue%7Clabel:A%7C'. urlencode($p['map_address']).'&sensor=false" /><a>' : "").'</td>
					<td><input type="button" value="Delete" class="btn" onClick="DeleteWishlist('.$p["id"].',\''.$p['type'].'\')"/></td>
				</tr>';
}
function show_wishlist_row_pl($p)
{
    if (startsWith($p["picture"],"http"))
    {
        $picture = $p["picture"];
    }
    else
    {
        $picture = '/scaleimage.php?w=100&h=100&t=productimage&f='.$p["picture"];
    }
    echo '<tr>
					<td><input type="hidden" name="type[]" value="'.$p["type"].'" />
			<input type="hidden" name="id[]" value="'.$p["id"].'" />
			<a href="'.$p['link'].'" target="_blank"><img src="'.$picture.'" class="wlimg" /></a></td>
					<td>'.stripcslashes($p["name"]).'<br><span class="wlsln">'.($p["merchant"] != '' ? $p['merchant'] : "").'</span></td>
					<td>'.($p['price'] != '' ? CURRENCY.$p['price'] : "").'</td>
					<td><input style="width:40px;" type="number" value="'.$p["amount"].'" name="amount[]" /></td>
					<td>'.CURRENCY.($p['price'] * $p["amount"]).'</td>
					<td><input type="button" value="Delete" class="btn" onClick="DeleteWishlist('.$p["id"].',\''.$p['type'].'\')"/></td>
				</tr>';
}
$total = 0;
$hastable = false;
foreach ($wlproduct as $p)
{
    if ($p["category_type"] == 'pr')
    {
        $hastable = true;
        echo '<h2>My Promenades</h2>
                <table class="table wltbl">
				<tr class="wlth">
				<td>Item</td>
				<td>Info</td>
				<td style="width:254px;">Contact</td>

				<td>Map</td>
				<td class="wlacc"></td>
			</tr>';
        break;
    }
}
foreach ($wlproduct as $p)
{
    //$total += $p['price'] * $p["amount"];
    if ($p["category_type"] == 'pr')
    {
        show_wishlist_row_pr($p);
    }
}
if ($hastable)
{
    echo "</table>";
}

$hastable = false;
foreach ($wlproduct as $p)
{
    if ($p["category_type"] == 'bs')
    {
        $hastable = true;
        echo '<h2>My Basics</h2>
                <table class="table wltbl">
				<tr class="wlth">
				<td>Item</td>
				<td>Info</td>
				<td></td>
				<td class="wlacc"></td>
			</tr>';
        break;
    }
}
foreach ($wlproduct as $p)
{
    //$total += $p['price'] * $p["amount"];
    if ($p["category_type"] == 'bs')
    {
        show_wishlist_row_bs($p);
    }
}
if ($hastable)
{
    echo "</table>";
}

$hastable = false;
foreach ($wlproduct as $p)
{
    if ($p["category_type"] == 'pl')
    {
        $hastable = true;
        echo '<h2>My Plants</h2>
                <table class="table wltbl">
				<tr class="wlth">
				<td>Item</td>
				<td>Info</td>
				<td>Price</td>
				<td>Quantity</td>
				<td>Cost</td>
				<td class="wlacc"></td>
			</tr>';
        break;
    }
}
foreach ($wlproduct as $p)
{
    if ($p["category_type"] == 'pl')
    {
        $total += $p['price'] * $p["amount"];
        show_wishlist_row_pl($p);
    }
}

/*foreach ($wlproduct as $p)
{
    if ($p["category_type"] == 'gi')
    {
        echo '<tr><td colspan="6"><h2>My Garden Ideas</h2></td></tr>
        <tr class="wlth">
        <td>Item</td>
        <td>Info</td>
        <td>Price</td>
        <td>Quantity</td>
        <td>Cost</td>
        <td></td>
    </tr>';
        break;
    }
}
foreach ($wlproduct as $p)
{
    //$total += $p['price'] * $p["amount"];
    if ($p["category_type"] == 'gi')
    {
        $total += $p['price'] * $p["amount"];
        show_wishlist_row_gi($p);
    }
}

foreach ($wlproduct as $p)
{
    if ($p["category_type"] == 'ds')
    {
        echo '<tr><td colspan="6"><h2>My Designers</h2></td></tr>
        <tr class="wlth">
        <td>Item</td>
        <td>Info</td>
        <td>Contact</td>
        <td></td>
        <td>Map</td>
        <td></td>
    </tr>';
        break;
    }
}
foreach ($wlproduct as $p)
{
    //$total += $p['price'] * $p["amount"];
    if ($p["category_type"] == 'ds')
    {
        show_wishlist_row_ds($p);
    }
}*/
if ($hastable)
{
    echo "</table>";
}

$hastable = false;
foreach ($wlproduct as $p)
{
    if ($p["category_type"] == 'ds' || $p["category_type"] == 'gi')
    {
        $hastable = true;
        echo '<h2>My Garden Ideas & Designers</h2>
                <table class="table wltbl">
				<tr class="wlth">
				<td>Item</td>
				<td>Info</td>
				<td>Price</td>
				<td style="width:254px;">Contact</td>

				<td>Map</td>
				<td class="wlacc"></td>
			</tr>';
        break;
    }
}
foreach ($wlproduct as $p)
{
    if ($p["category_type"] == 'gi')
        $total += $p['price'] * $p["amount"];
    if ($p["category_type"] == 'ds' || $p["category_type"] == 'gi')
    {
        show_wishlist_row_gids($p);
    }
}
if ($hastable)
{
    echo "</table>";
}

?>
<table>
    <tr>
        <td></td>
        <td></td>
        <td colspan="2" align="right">
            <a href="<?php echo $_SESSION["lastest_wishlist_ref"];?>" class="btn">Back</a>
            <input type="submit" value="Clear" class="btn" name="smClearWishlist"/>
            <input type="submit" value="Update" class="btn" name="smUpdateWishlist"/></td>
        <td style="font-weight: bold;font-size: 17px;color: #2b2b2b;">Total From: <?php echo CURRENCY.$total;?></td>
        <td></td>
    </tr>
    <tbody>
</table>
</form>
</div>
<script>
    function DeleteWishlist(pid,type)
    {
        if (confirm("Are you sure?"))
        {
            if (type == 'seller')
                window.location = '/wishlist/index.html?is_delete=1&sid=' + pid;
            else
                window.location = '/wishlist/index.html?is_delete=1&pid=' + pid;
        }
    }
</script>