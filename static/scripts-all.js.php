<?php if(extension_loaded('zlib')){ob_start('ob_gzhandler');} header("Content-type: text/javascript");
   header ("Cache-Control: must-revalidate");
   header('Last-Modified: '.gmdate('D, d M Y H:i:s', getlastmod()).' GMT', true, 200);

?>

<?php include("jquery.tooltipster.min.js.php") ?>
<?php include("jquery.idTabs.min.js") ?>
<?php include("jquery.validate.min.js") ?>
<?php include("jquery.tooltipster.min.js") ?>

<?php if(extension_loaded('zlib')){ob_end_flush();}?>