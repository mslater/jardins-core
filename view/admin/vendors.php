<h2>Vendor List</h2>
<script src="<?php echo BASE_URL; ?>static/DataTables-1.9.4/media/js/jquery.dataTables.min.js" ></script>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>static/DataTables-1.9.4/media/css/jquery.dataTables.css" type="text/css" />

<?php
if (isset($info) && $info != '')
    echo "<div class=\"info\">{$info}</div>";
?>

<table id="datatable">
    <thead>
        <tr>
            <th>Vendor</th>
            <th>Shopname</th>
            <th>Country</th>
            <th>Email</th>
            <th>Status</th>
            <th>No. Products</th>
            <th>Created Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        while ($v = mysql_fetch_object($dataVendors))
        {
        ?>
        <tr>
            <td><?=$v->first_name . " " . $v->last_name ?></td>
            <td><a target="_blank" href="http://<?=$v->website ?>"><?=$v->shop_name ?></a></td>
            <td><?=$v->country ?></td>
            <td><?=$v->email ?></td>
            <td><?=$v->status ?></td>
            <td><?=$v->no_prod ?></td>
            <td><?=$v->created_date ?></td>
            <td>
                <?php
                if ($v->status == 'pending')
                    echo '<a href="?view=vendors&action=approved&id='.$v->id.'" class="btn">Approve</a><br>';
                elseif ($v->status == 'approved')
                    echo '<a href="?view=vendors&action=banned&id='.$v->id.'" class="btn">Ban</a><br>';
                elseif ($v->status == 'banned')
                    echo '<a href="?view=vendors&action=approved&id='.$v->id.'" class="btn">Unban</a><br>';
                ?>
                <a class="btn" href="/vendor/edit-profile.html?admin_vendor_id=<?=$v->id?>">Edit</a>
                <a class="btn" onClick="return confirm('Are you sure to delete?')" href="?view=vendors&action=deleted&id=<?=$v->id?>">Delete</a>
            </td>
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>

<script>
    $(document).ready(function() {
        $('#datatable').dataTable();
    });
</script>