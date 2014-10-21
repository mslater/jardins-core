<?php
require "config.php";
$postdat = mysql_query("SELECT * FROM product");
while ($r = mysql_fetch_object($postdat))
{
	preg_match_all('/src="([^"]*)"/is',$r->description,$matches);
	if (count($matches[1]) > 0)
	{
		$img = preg_replace('/(-[0-9]*x[0-9]*)\./is','.',$matches[1][0]);
		$bn = pathinfo($img);
		mysql_query("UPDATE product SET main_picture='".$bn["basename"]."' WHERE id='{$r->id}'");
	}
	foreach ($matches[1] as $i)
	{
		$img = preg_replace('/(-[0-9]*x[0-9]*)\./is','.',$i);
		$bn = pathinfo($img);
		$count_image = mysql_query("SELECT * FROM product_images WHERE name='".$bn["basename"]."' AND product_id='{$r->id}'");
		if (mysql_num_rows($count_image) == 0)
			mysql_query("INSERT INTO product_images SET name='".$bn["basename"]."', product_id='{$r->id}'") or die(mysql_error());
	}
}
?>
