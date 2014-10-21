<?php
define ("DEBUG",0);
ob_start("ob_gzhandler");
if (DEBUG)
{
	define("DB_HOST","localhost");
	define("DB_USERNAME","root");
	define("DB_PASSWORD","root");
    define("DB_DATABASE","flowershop");
}
else
{
	define("DB_HOST","localhost");
	define("DB_USERNAME","jardinss_dev4");
	define("DB_PASSWORD","]y6PJIf49*o[");
	define("DB_DATABASE","jardinss_dev4");
}

define("ALCHEMY_API","63c89cb6c5c2d29b361ac3396125117e2c7cc229");
define("SSL",true);
define("CACHE_TIME",60*60*2);
define ("PLANT_IDEAS_ID",1035);
define ("MANDRILLAPP_API","dr3Ekz8_dMQqSqccwkdEIw");

ini_set("error_log", "error-log.log");

$protocol = (@$_SERVER['HTTPS'] && @$_SERVER['HTTPS'] != "off") ? "https" : "http";
define("BASE_URL",$protocol . "://" . $_SERVER['HTTP_HOST'].'/');

mysql_connect(DB_HOST, DB_USERNAME, DB_PASSWORD);
mysql_select_db(DB_DATABASE);
mysql_query('SET CHARACTER SET utf8');

require_once "includes/functions.php";
define("ADMIN_EMAIL", GetSetting("admin_email"));
//mysql_query("SET NAMES 'utf8'"); 

if (isset($_REQUEST["session_id"])) {
    session_id($_REQUEST["session_id"]);
    session_start();
} else
    session_start();

require_once "includes/urls.php";

LoadCountry();
ini_set("session.cookie_lifetime", 12 * 3600);

if (isset($_COOKIE['session_id']) && $_COOKIE['session_id'] != '' && $_SESSION["isvendor"] == false)
{
    $data = mysql_query("SELECT * FROM allow_merchant WHERE session_id='".$_COOKIE['session_id']."'");
    $row = mysql_fetch_object($data);
    if (isset($row) && $row)
    {
        $_SESSION["isvendor"] = true;
        $_SESSION["vendor_id"] = $row->id;
    }
}
?>