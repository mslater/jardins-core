<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <title>Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta content="text/html; charset=iso 8859-1" http-equiv="Content-Type">
    
    <script src="<?php echo BASE_URL."static/jquery-ui/js/jquery.js";?>"></script>
    <script src="<?php echo BASE_URL."static/jquery-ui/js/jquery-ui.js";?>"></script>
    <script src="<?php echo BASE_URL."static/divshot/js/bootstrap.min.js";?>"></script>
    <script src="<?php echo BASE_URL."static/jquery.validate.min.js";?>"></script>

    <script type="text/javascript" src="/static/jquery.validate.min.js"></script>
    <script type="text/javascript" src="/static/jquery.tooltipster.min.js"></script>
    <script type="text/javascript" src="/static/function.js"></script>
    <link rel="stylesheet" href="<?php echo BASE_URL."static/tooltipster.css";?>" />
    
    
 <!-- Framework CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL;?>static/blueprint/screen.css" type="text/css" media="screen, projection">
    <link rel="stylesheet" href="<?php echo BASE_URL;?>static/blueprint/print.css" type="text/css" media="print">
    <!--[if lt IE 8]><link rel="stylesheet" href="<?php echo BASE_URL;?>static/blueprint/ie.css" type="text/css" media="screen, projection"><![endif]-->
    
    
    <link rel="stylesheet" href="<?php echo BASE_URL."static/divshot/css/bootstrap-responsive.css";?>">
    <link rel="stylesheet" href="<?php echo BASE_URL."static/divshot/css/bootstrap.css";?>">
    <link rel="stylesheet" href="<?php echo BASE_URL."static/jquery-ui/css/jquery-ui.css";?>">
    
    <link rel="stylesheet" href="<?php echo BASE_URL."static/admin_style.css";?>">

  </head>
  
  <body>
  <div class="container">
  <h1>Administration</h1>
	  <ul class="nav nav-tabs">
		 <li <?php echo CURRENT_VIEW == "" ?' class="active" ':"";?>>
			<a href="<?php echo BASE_URL;?>">Home</a> 
		 </li>
		 <li <?php echo CURRENT_VIEW == "google_keywords" ?' class="active" ':"";?>>
			<a href="<?php echo BASE_URL."admin.php?view=google_keywords";?>">Google Scraper</a> 
		 </li>
		 <li <?php echo CURRENT_VIEW == "products" ?' class="active" ':"";?>>
			<a href="<?php echo BASE_URL."admin.php?view=products";?>">Products</a> 
		 </li>
		 <li <?php echo CURRENT_VIEW == "categories" ?' class="active" ':"";?>>
			<a href="<?php echo BASE_URL."admin.php?view=categories";?>">Categories</a> 
		 </li>
		 <li <?php echo CURRENT_VIEW == "forms" ?' class="active" ':"";?>>
			<a href="<?php echo BASE_URL."admin.php?view=forms";?>">Manage Forms</a> 
		 </li>
		 <li <?php echo CURRENT_VIEW == "settings" ?' class="active" ':"";?>>
			<a href="<?php echo BASE_URL."admin.php?view=settings";?>">Site Settings</a> 
		 </li>
		 <li <?php echo CURRENT_VIEW == "reports" ?' class="active" ':"";?>>
			<a href="<?php echo BASE_URL."admin.php?view=reports";?>">Reports</a> 
		 </li>
		 <li <?php echo CURRENT_VIEW == "vendors" ?' class="active" ':"";?>>
			<a href="<?php echo BASE_URL."admin.php?view=vendors";?>">Vendors</a> 
		 </li>
		 <li>
			<a href="javascript:window.open('searchimg.php','searchimg','width=410,height=600')">Search Image</a> 
		 </li>
	  </ul>
	  
	  <div class="label label-primary"><?php if (isset($_SESSION['error'])) {echo $_SESSION['error'];unset($_SESSION['error']);} ?></div>
	  <div class="label label-info"><?php if (isset($_SESSION['info'])) {echo $_SESSION['info'];unset($_SESSION['info']);} ?></div>
	  <div class="label label-warning"><?php if (isset($_SESSION['warn'])) {echo $_SESSION['warn'];unset($_SESSION['warn']);} ?></div>
	  
	  
	  <?php 
	  	$tempqd = mysql_query("SELECT COUNT(*) AS counter FROM user_rating WHERE status='pending'");
		$tempr = mysql_fetch_object($tempqd);
		if ($tempr->counter > 0)
		{
			echo '<div class="info">There are '.$tempr->counter.' review(s) is/are spending for checking. Click <a href="/admin.php?view=reports">here</a> to do that.</div>';
		}
	  ?>