<?php
require "config.php";
$doc3 = new DOMDocument('1.0', 'utf-8');
$doc3->load("contentbk.xml");
$items = $doc3->getElementsByTagName("item");
$count = 0;

$data = mysql_query("SELECT * FROM product_category WHERE category_name='Basics' AND category_type='bs'");
if (mysql_num_rows($data) == 0)
{
	mysql_query("INSERT INTO product_category SET category_name='Basics', category_type='bs'");
	$bsid = mysql_insert_id();
}
else
{
	$row = mysql_fetch_object($data);
	$bsid = $row->id;
}
$data = mysql_query("SELECT * FROM product_category WHERE category_name='Promenades' AND category_type='pr'");
if (mysql_num_rows($data) == 0)
{
	mysql_query("INSERT INTO product_category SET category_name='Promenades', category_type='pr'");
	$prid = mysql_insert_id();
}
else
{
	$row = mysql_fetch_object($data);
	$prid = $row->id;
}


foreach ($items as $i) {
	$title = $i->getElementsByTagName("title")->item(0)->nodeValue;
	if ($title == "")
		continue;
	$count++;
	$categories = array();
	$content = $i->getElementsByTagName("encoded")->item(0)->nodeValue;
	$seo_keyword = "";
	$seo_title = "";
	$seo_description = "";
	$created_date = $i->getElementsByTagName("post_date")->item(0)->nodeValue;
	
	foreach ($i->getElementsByTagName("category") as $tt)
	{
		$ttt = trim($tt->nodeValue);
		$ttt = str_replace("&amp;","&",$ttt);
		//$ttt = preg_replace('/^[0-9]*\./is','',$ttt);
		$ttt = trim($ttt);
		$categories[] = $ttt;
	}
	
	foreach ($i->getElementsByTagName("postmeta") as $t)
	{
		if (count($t->getElementsByTagName("meta_key")) > 0 && $t->getElementsByTagName("meta_key")->item(0)->nodeValue == "thesis_title")
		{
			$seo_title = $t->getElementsByTagName("meta_value")->item(0)->nodeValue;
			break;
		}
	}
	
	foreach ($i->getElementsByTagName("postmeta") as $t)
	{
		if (count($t->getElementsByTagName("meta_key")) > 0 && $t->getElementsByTagName("meta_key")->item(0)->nodeValue == "thesis_keywords")
		{
			$seo_keyword = $t->getElementsByTagName("meta_value")->item(0)->nodeValue;
			break;
		}
	}
	
	foreach ($i->getElementsByTagName("postmeta") as $t)
	{
		if (count($t->getElementsByTagName("meta_key")) > 0 && $t->getElementsByTagName("meta_key")->item(0)->nodeValue == "thesis_description")
		{
			$seo_description = $t->getElementsByTagName("meta_value")->item(0)->nodeValue;
			break;
		}
	}
	
	$title = mysql_real_escape_string($title);
	$seo_keyword = mysql_real_escape_string($seo_keyword);
	$seo_title = mysql_real_escape_string($seo_title);
	$seo_description = mysql_real_escape_string($seo_description);
	$content = mysql_real_escape_string($content);
	
	echo $count . ". ". $title . "<br>";
	print_r($categories);
	echo "<br>content length: " . strlen($content) . "<br>";
	echo $seo_title . "<br>";
	echo $seo_keyword . "<br>";
	echo $seo_description . "<br>";
	echo "-=-===-=-=-=-=<br>";
	
	$datacount = mysql_query("SELECT * FROM product WHERE name='{$title}'") or die (mysql_error());
	if (mysql_num_rows($datacount) > 0)
	{
		while ($r = mysql_fetch_object($datacount))
		{
			mysql_query("UPDATE product SET created_date='$created_date' WHERE id='{$r->id}'") or die (mysql_error());
			foreach ($categories as $c)
			{
				$datacat = mysql_query("SELECT id from product_category WHERE category_name='".mysql_real_escape_string($c)."'") or die(mysql_error());
				if (mysql_num_rows($datacat) > 0)
				{
					$rc = mysql_fetch_object($datacat);
					$cid = $rc->id;
					mysql_query("INSERT INTO product_in_category SET product_id='{$r->id}', category_id='{$cid}'") or die(mysql_error());
				}
				elseif ($c == "Basics")
				{
					mysql_query("INSERT INTO product_in_category SET product_id='{$r->id}', category_id='{$bsid}'") or die(mysql_error());
				}
				elseif ($c == "Promenades")
				{
					mysql_query("INSERT INTO product_in_category SET product_id='{$r->id}', category_id='{$prid}'") or die(mysql_error());
				}
			}
		}
		continue;
	}
	echo "creating <br>";

	mysql_query("INSERT INTO product SET name='{$title}',
		description='{$content}',
		status=1,
		seo_keyword='{$seo_keyword}',
		seo_description='{$seo_description}',
		seo_title='{$seo_title}',
		layout_type='ecommerce'") or die(mysql_error());
	$pid = mysql_insert_id();
	foreach ($categories as $c)
	{
		$datacat = mysql_query("SELECT id from product_category WHERE category_name='".mysql_real_escape_string($c)."'") or die(mysql_error());
		if (mysql_num_rows($datacat) > 0)
		{
			$r = mysql_fetch_object($datacat);
			$cid = $r->id;
			mysql_query("INSERT INTO product_in_category SET product_id='{$pid}', category_id='{$cid}'") or die(mysql_error());
		}
		elseif ($c == "Basics")
		{
			mysql_query("INSERT INTO product_in_category SET product_id='{$pid}', category_id='{$bsid}'") or die(mysql_error());
		}
		elseif ($c == "Promenades")
		{
			mysql_query("INSERT INTO product_in_category SET product_id='{$pid}', category_id='{$prid}'") or die(mysql_error());
		}
	}
}
?>
