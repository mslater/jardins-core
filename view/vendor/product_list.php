<script src="<?php echo BASE_URL; ?>static/DataTables-1.9.4/media/js/jquery.dataTables.min.js" ></script>
<script src="<?php echo BASE_URL; ?>static/readmore.js" ></script>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>static/DataTables-1.9.4/media/css/jquery.dataTables.css" type="text/css" />


<div class="col-md-12" align="right">
            <span class="label label-primary">
                <?php
                if ($vendor->no_prod != 0 && $vendor->no_prod != "")
                {
                    echo "You have {$vendor->no_prod} product(s)";
                }
                else
                {
                    echo "You do not have any product.";
                }
                ?>
                </span>
<!--            <span class="label label-primary">Total impressions: 111</span>-->
<!--            <span class="label label-primary">Total clicks: 111</span>-->
        </div>


<h1  class="vendor_header">Product List</h1>

<p>Please find below your list of products available for display in our website. <br>
Product name, Description and Price information will be published in all our related articles and therefore made available to the public. </p>

<table id="datatable">
    <thead>
        <tr>
            <th>Product Name</th>
            <th>Product Description</th>
            <th>Created Date</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Appear in</th>
        </tr>
    </thead>
    <tbody>
        
    </tbody>
</table>

<script>
    $(document).ready(function() {
        $('#datatable').dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "?load_data=1<?php if (isset($_GET["admin_vendor_id"])) echo '&admin_vendor_id=' . $_GET["admin_vendor_id"]; ?>",
            "fnDrawCallback": function(oSettings) {
                $('.rm').readmore({substr_len: 200});
            }
        });
    });

</script>