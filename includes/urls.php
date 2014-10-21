<?php
require_once "includes/functions.php";

function url_product_detail($id,$ex = "")
{
	$d = mysql_query("SELECT name FROM product WHERE id='{$id}'");
	$row = mysql_fetch_object($d);
	$title = $row->name;
	$title = preg_replace('/[\s]+/i',' ',$title);
	$title = preg_replace('/[^0-9a-zA-Z]/i','-',$title);
	$title = preg_replace('/[_]+/i','-',$title);
	$title = trim($title,'-');
    $title = preg_replace('/-+/i','-',$title);
	return "/detail/{$id}/{$title}.html" . $ex;
}

function url_cms($id,$ex = "")
{
	$d = mysql_query("SELECT title FROM cms_page WHERE id='{$id}'");
	$row = mysql_fetch_object($d);
	$title = $row->title;
	$title = preg_replace('/[\s]+/i',' ',$title);
	$title = preg_replace('/[^0-9a-zA-Z]/i','-',$title);
	$title = preg_replace('/[_]+/i','-',$title);
	$title = trim($title,'-');
    $title = preg_replace('/-+/i','-',$title);
	return "/cms/{$id}/{$title}.html" . $ex;
}

function url_redirect($url,$extra = "")
{
	$url = FilterScrapedProductUrl($url);
	return "/redirect/index.html?url=".urlencode($url).$extra;
}

function url_category($id)
{
	$d = mysql_query("SELECT category_name FROM product_category WHERE id='{$id}'");
	$row = mysql_fetch_object($d);
	$title = $row->category_name;
	$title = preg_replace('/[\s]+/i',' ',$title);
	$title = preg_replace('/[^0-9a-zA-Z]/i','-',$title);
	$title = preg_replace('/[_]+/i','-',$title);
	$title = trim($title,'-');
    $title = preg_replace('/-+/i','-',$title);
	return "/category/{$id}/{$title}.html";
}
function url_category_type($id)
{
	switch($id)
	{
	case "gi":
	$title = "Garden-Ideas";
	break;
	case "bs":
	$title = "Basics";
	break;
	case "pr":
	$title = "Promenades";
	break;
	case "pl":
	$title = "Plants";
	break;
	case "ds":
	$title = "Designers";
	break;
	}
	return "/page/{$id}/{$title}.html";
}
?>