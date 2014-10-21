<?php
require_once "config.php";
$dbFileFolder = getcwd()."/../backups/dbbackups/";
$dirFileFolder = getcwd()."/../backups/dirbackups/";

if (!is_dir($dbFileFolder) || !$dirFileFolder)
{
	die ("Folder is not found");
}

$dbFile = $dbFileFolder.DB_DATABASE.'_'.date('Ymd').'.sql.gz';
$dirFile = $dirFileFolder.DB_DATABASE.'_'.date('Ymd').'.tar.gz';
exec( 'mysqldump --host="'.DB_HOST.'" --user="'.DB_USERNAME.'" --password="'.DB_PASSWORD.'" "'.DB_DATABASE.'" | gzip > "'.$dbFile.'"' );

$files = array("includes","lib","static","upload","view",".htaccess","admin.php","index.php","config.php","scaleimage.php",'searchimg.php',".htaccess");
$folders = "";
foreach ($files as $f)
	$folders .= ' "'.getcwd()."/".$f.'" ';
exec('tar -zcf "'.$dirFile.'" '.$folders);
?>