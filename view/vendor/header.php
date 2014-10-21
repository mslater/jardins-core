<?php global $view;?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title><?php echo @$page_title; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta content="text/html; charset=iso-8859-1" http-equiv="Content-Type">

            <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
            <script type="text/javascript" src="/static/bootstrap/js/bootstrap.min.js"></script>
            <script src="/static/function.js"></script>
            <link rel="stylesheet" href="<?php echo BASE_URL . "static/bootstrap/css/bootstrap.min.css"; ?>" />
            <link rel="stylesheet" href="<?php echo BASE_URL."static/style.css";?>" />
            
    <script type="text/javascript" src="/static/jquery.validate.min.js"></script>
    <script type="text/javascript" src="/static/jquery.tooltipster.min.js"></script>
    <link rel="stylesheet" href="<?php echo BASE_URL."static/tooltipster.css";?>" />
            
    <?php
    foreach ($script as $i)
        echo '
        <script type="text/javascript" src="'.$i.'"></script>
        ';
    ?>
            
    <?php   
    foreach ($css as $i)
        echo '
        <link rel="stylesheet" type="text/css" href="'.$i.'" />
        ';
    ?>
            
    </head>
    <div class="container">
        <div class="header">
		<a rel="nofollow" href="/"><img alt="Jardins Sans Secret" src="/static/images/logo.png" class="main_logo"/></a>
        	
  </div>
        
        <div class="col-md-3" style="margin-top:15px;">
            <div class="panel panel-primary">
            <div class="panel-heading"><?php echo $vendor->shop_name; ?></div>
            <div class="panel-body">
            <ul class="nav nav-pills nav-stacked">
            <li>
                <?php
                if ($vendor->logo != '')
                    echo '<img style="display: block;margin: auto;" src="/scaleimage.php?o=1&w=222&amp;h=222&amp;t=vendorlogo&amp;f='.$vendor->logo.'" />';
                ?><br>
<!--                <a href="/">Site home page</a>-->
</li>
            <!--<li class="<?php if ($view == 'index') echo ' active';?>"><a href="/vendor<?php if (isset($_GET["admin_vendor_id"])) echo '?admin_vendor_id='.$_GET["admin_vendor_id"];?>">Vendor home page</a></li>-->
                <li><a href="/vendor/welcome.html<?php if (isset($_GET["admin_vendor_id"])) echo '?admin_vendor_id='.$_GET["admin_vendor_id"];?>">Welcome</a></li>
                <li><a href="/vendor/aboutus.html<?php if (isset($_GET["admin_vendor_id"])) echo '?admin_vendor_id='.$_GET["admin_vendor_id"];?>">About Us</a></li>
<!--                <li><a href="/vendor/review.html<?php if (isset($_GET["admin_vendor_id"])) echo '?admin_vendor_id='.$_GET["admin_vendor_id"];?>">Reviews</a></li>-->
                <li><a href="/vendor/terms.html<?php if (isset($_GET["admin_vendor_id"])) echo '?admin_vendor_id='.$_GET["admin_vendor_id"];?>">Terms &amp; Conditions</a></li>
                <li><a href="/vendor/shipping.html<?php if (isset($_GET["admin_vendor_id"])) echo '?admin_vendor_id='.$_GET["admin_vendor_id"];?>">Shipping Policy</a></li>
<!--                <li><a href="/vendor/return-policy.html<?php if (isset($_GET["admin_vendor_id"])) echo '?admin_vendor_id='.$_GET["admin_vendor_id"];?>">Return Policy</a></li>
                <li><a href="/vendor/contact-us.html<?php if (isset($_GET["admin_vendor_id"])) echo '?admin_vendor_id='.$_GET["admin_vendor_id"];?>">Contact Us</a></li>-->

            <li class="dropdown<?php if ($view == 'product-list' || $view == 'import-csv') echo ' active';?>">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    Product Management <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="/vendor/import-csv.html<?php if (isset($_GET["admin_vendor_id"])) echo '?admin_vendor_id='.$_GET["admin_vendor_id"];?>">Import products from CSV</a></li>
                    <li><a href="/vendor/product-list.html<?php if (isset($_GET["admin_vendor_id"])) echo '?admin_vendor_id='.$_GET["admin_vendor_id"];?>">Product list</a></li>
                </ul>
            </li>
            <li class="<?php if ($view == 'edit-profile') echo ' active';?>"><a href="/vendor/edit-profile.html<?php if (isset($_GET["admin_vendor_id"])) echo '?admin_vendor_id='.$_GET["admin_vendor_id"];?>">Edit profile</a></li>
            <li><a href="/vendor/logout.html">Logout</a></li>
        </ul>
                </div>
        </div>
        </div>
        <div class="col-md-9">