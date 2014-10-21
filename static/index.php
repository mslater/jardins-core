<?php
require_once "config.php";

$view = "";
if (isset($_GET["view"]))
	$view = $_GET["view"];
else
	$view = "home";

$temp = pathinfo(__FILE__);
define("CURRENT_VIEW",$view);
define("CURRENT_CONTROLLER",$temp["filename"]);
$funcview = "controller_" . $view;
if (function_exists($funcview))
	call_user_func($funcview);

function controller_home()
{
	$dataslider = mysql_query("SELECT * FROM sliderimage");
	include "view/home/header.php";
	include "view/product/home.php";
	include "view/home/footer.php";
}

function controller_detail()
{
	$id = mysql_real_escape_string($_GET["id"]);
	if ($_SESSION['isadmin'] === true)
		$query = mysql_query("SELECT * FROM product WHERE id='{$id}'") or die (mysql_error());
	else
		$query = mysql_query("SELECT * FROM product WHERE id='{$id}' AND status=1 AND publishing_date < '".time()."'") or die (mysql_error());
	if (mysql_num_rows($query) == 0)
		die ("No product");
	$p = mysql_fetch_object($query);
	
	$images = array();
	$query = mysql_query("SELECT * FROM product_images WHERE product_id='{$id}' ORDER BY ordering");
	while ($r = mysql_fetch_object($query))
		$images [] = array("name"=>$r->name,
			"caption" => $r->caption,
			"alt" => $r->alter_text);
	
	mysql_query("UPDATE product SET view_count = view_count+1 WHERE id='{$id}'") or die (mysql_error());
	
	$pro_type_data = mysql_query("SELECT DISTINCT pc.category_type FROM product_category AS pc
			INNER JOIN product_in_category AS pic ON pic.category_id=pc.id
			WHERE pic.product_id='{$id}'");
	while ($r2 = mysql_fetch_object($pro_type_data))
		$product_type = $r2->category_type;
		
	$pro_type_data = mysql_query("SELECT DISTINCT pc.category_type FROM product_category AS pc
		INNER JOIN product_in_category AS pic ON pic.category_id=pc.id
		WHERE pic.product_id='{$id}'");
	$product_types = array();
	while ($r2 = mysql_fetch_object($pro_type_data))
		$product_types[] = $r2->category_type;
	
	include "view/home/header.php";
	include "view/home/sidebar.php";
	include "view/product/detail.php";
	include "view/home/footer.php";
}

function controller_cms()
{
	$id = mysql_real_escape_string($_GET["id"]);
	$data = mysql_query("SELECT * FROM cms_page WHERE id='{$id}'");
	$rcms = mysql_fetch_object($data);
	
	include "view/home/header.php";
	include "view/home/cms.php";
	include "view/home/footer.php";
}

function controller_contactus()
{
	include "view/home/header.php";
	include "view/home/contactus.php";
	include "view/home/footer.php";
}

function controller_page()
{
    unset($_SESSION["selected_category"]);
	$id = mysql_real_escape_string($_GET["id"]);
	$leftpanelgroup = $id;
	$catdata = mysql_query("SELECT * FROM product_category WHERE id='{$id}'");
	$cat = mysql_fetch_object($catdata);
	$catcdata = mysql_query("SELECT * FROM product_category WHERE category_type='{$id}' AND parent_id=0");
	if (mysql_num_rows($catcdata) == 1)
	{
		$row = mysql_fetch_object($catcdata);
		header("Location: " . url_category($row->id));
		die();
	}
	
	include "view/home/header.php";
	include "view/home/sidebar.php";
	include "view/product/category_menu.php";
	include "view/product/group.php";
	include "view/home/footer.php";
}

function controller_changecountry()
{
	$country = $_POST["country"];
	$_SESSION["country"] = $country;
	setcookie("country",$country,0,"/");
	//var_dump($country);exit;
	//header("Location: /");
	die();
}

function controller_autocompletesearch()
{
	$k = mysql_real_escape_string($_GET["term"]);
	$data = mysql_query("SELECT DISTINCT name FROM product WHERE name LIKE '%{$k}%' AND status=1 AND publishing_date < '".time()."' ORDER BY name");
	$res = array();
	while ($r = mysql_fetch_object($data))
	{
		$res[] = array(
			"id"=>$r->name,
			"label"=>$r->name,
			"value"=>$r->name
		);
	}
	echo json_encode($res);
}

function controller_category()
{
	$id = mysql_real_escape_string($_GET["id"]);
	$where = "";
	$selected_category = $_SESSION["selected_category"];
	
	$pagesize = mysql_real_escape_string($_GET["ps"]);
	$from = mysql_real_escape_string($_GET["f"]);
	if ($from == '')
		$from = '0';
	if ($pagesize == '')
		$pagesize = '8';
			
	if (!isset($_GET["append"]))
	{
		$selected_category = array();
	}
	if (isset($_GET["delone"]))
	{
		$selected_category = $_SESSION["selected_category"];
	}
	
	$cid = $id;		
	/*do
	{
		if (in_array($cid,$selected_category) == false)
			$selected_category[] = $cid;
		$catdata = mysql_query("SELECT * FROM product_category WHERE id={$cid}");
		$catdatarow = mysql_fetch_object($catdata);
		if ($catdatarow->parent_id != 0)
			$cid = $catdatarow->parent_id;
		else
			break;
	}
	while (true);	*/	
	$where3 = "";
	if (isset($_GET["clearall"]))
	{
		$selected_category = array();
		//$selected_category[] = $id;
		$where3 = " pic.category_id IN (".$id.") ";
	}
	elseif (isset($_GET["delone"]))
	{
		$newl = array();
		foreach ($selected_category as $i)
		{
			if ($i != $id)
				$newl[] = $i;
		}
		$selected_category = $newl;
	}
	else
	{		
		if (in_array($cid,$selected_category) == false)
				$selected_category[] = $cid;
	}
	$_SESSION["selected_category"] = $selected_category;

	
	if ($id == "search")
	{
		$k = mysql_real_escape_string($_GET["k"]);
		$k = trim($k);
		if ($k != '')
		{
			$sk = preg_replace('/\s+/',' ',$k);
			$sk = "%".preg_replace('/\s/','%',$sk)."%";
		}
		
		$where2 = " p.name LIKE '{$sk}' OR p.common_name LIKE '{$sk}' ";
		$_SESSION["selected_category"] = array();
	}
	else
	{
        $id = $selected_category[0];
		$catdata = mysql_query("SELECT * FROM product_category WHERE id='{$id}' ORDER BY ordering");
		$cat = mysql_fetch_object($catdata);
		$leftpanelgroup = $cat->category_type;
		$catcdata = mysql_query("SELECT * FROM product_category WHERE parent_id='{$id}' ORDER BY ordering");
		//$where = " pic.category_id='{$cat->id}' ";
		if (count($selected_category) > 0)
			$where3 = " pic.category_id IN (".implode(",",$selected_category).") ";
	}
	
	if ($where3 != '')
	{
		$where3 = " AND ($where3) ";
		$q = "SELECT count(p.name) AS counter, p.* FROM product AS p
			INNER JOIN product_in_category AS pic ON p.id=pic.product_id
			WHERE p.status=1 AND p.publishing_date < '".time()."' {$where3}
			GROUP BY p.name HAVING counter >= ".count($selected_category)."
			ORDER BY p.name";
	}
	else if ($where2 != '')
		$q = "SELECT DISTINCT p.* FROM product AS p
			INNER JOIN product_in_category AS pic ON p.id=pic.product_id
			WHERE p.status=1 AND p.publishing_date < '".time()."' AND ({$where2})
			ORDER BY p.name";
	//echo $q;
    if ($q != '')
    {
        $sproductdata = mysql_query($q . " LIMIT {$from},{$pagesize} ") or die (mysql_error());
        $sproductdatacount = mysql_query($q) or die (mysql_error());
    }
	include "view/home/header.php";
	include "view/home/sidebar.php";
	if ($leftpanelgroup != 'pr' && $leftpanelgroup != 'bs')
       include "view/product/category_menu.php";
	if (mysql_num_rows($catcdata) > 0 && $sproductdatacount == 0)
		include "view/product/group.php";
	else
		include "view/product/category_subgroup.php";
	include "view/home/footer.php";
}

function controller_wishlist()
{
	$wishlist = unserialize($_SESSION["wishlist"]);
	
	if (!empty($_GET["pid"]) || !empty($_GET["sid"]))
	{
		$_SESSION["lastest_wishlist_ref"] = $_SERVER["HTTP_REFERER"];
		if (is_array($wishlist) == false)
			$wishlist = array();
		$id = "";
		if (!empty($_GET["pid"]))
		{
			$type = "product";
			$id = $_GET["pid"];
		}
		elseif (!empty($_GET["sid"]))
		{
			$type = "seller";
			$id = $_GET["sid"];
		}
		$newwishlist = array();
		if (!isset($_GET["is_delete"]))
		{
			foreach ($wishlist as $w)
			{
				if ($w["id"] != $id)
					$newwishlist[] = $w;
			}
			$newwishlist[] = array(
				"type" => $type,
				"id" => $id,
				"amount" => 1
			);
		}
		else
		{
			foreach ($wishlist as $w)
			{
				if ($w["id"] == $id && $w["type"] == $type)
					continue;
				else
					$newwishlist[] = $w;
			}
		}
		$_SESSION["wishlist"] = serialize($newwishlist);
		$wishlist = $newwishlist;
		header("Location: /wishlist/index.html");
		die();
	}
	
	if (isset($_POST["smUpdateWishlist"]))
	{
		$wishlist = array();
		for ($i=0;$i<count($_POST["id"]);$i++)
		{
            $data = mysql_query("SELECT DISTINCT p . * , pc.category_type
				FROM product AS p
				INNER JOIN product_in_category AS pic ON pic.product_id = p.id
				INNER JOIN product_category AS pc ON pc.id = pic.category_id
				WHERE p.id =  '".$i."' AND p.status =1 AND p.publishing_date < '".time()."'
				LIMIT 1");
            $r = mysql_fetch_object($data);
			if ($_POST["amount"][$i] > 0 || !isset($_POST["amount"][$i]))
			{
				$newwishlist[] = array(
					"type" => $_POST["type"][$i],
					"id" => $_POST["id"][$i],
					"amount" => $_POST["amount"][$i]
				);
			}
		}
		$wishlist = $newwishlist;
		$_SESSION["wishlist"] = serialize($newwishlist);
	}

	$wlproduct = array();
	foreach ($wishlist as $w)
	{
		if ($w["type"] == "product")
		{
			$data = mysql_query("SELECT DISTINCT p . * , pc.category_type
				FROM product AS p
				INNER JOIN product_in_category AS pic ON pic.product_id = p.id
				INNER JOIN product_category AS pc ON pc.id = pic.category_id
				WHERE p.id =  '".$w["id"]."' AND p.status =1 AND p.publishing_date < '".time()."'
				LIMIT 1");
			$r = mysql_fetch_object($data);
			if ($r)
			{
				if ($r->category_type == 'gi')
					$price = GetTotalPriceGI($r->id);
				else
					$price = GetMinPriceProduct($r->id);
				preg_match('/[0-9\.,]+/',$price,$match);
				$price = $match[0];
				$wlproduct[] = array(
					"id" => $r->id,
					"type" => $w["type"],
					"picture" => $r->main_picture,
					"name" => $r->name,
					"merchant" => "",
					"price" => $price,
					"contact_form_id" => $r->contact_form_id,
					"map_address" => $r->map_address,
					"category_type" => $r->category_type,
					"amount" => $w["amount"],
					"link" => url_product_detail($w['id'])
				);
			}
		}
		elseif ($w["type"] == "seller")
		{
			$data = mysql_query("SELECT * FROM google_scraped WHERE id='".$w["id"]."'");
			$r = mysql_fetch_object($data);
			if ($r)
			{
				$wlproduct[] = array(
					"id" => $r->id,
					"type" => $w["type"],
					"picture" => $r->picture,
					"name" => $r->name,
					"contact_form_id" => $r->contact_form_id,
					"merchant" => $r->merchant_name,
					"price" => $r->price,
                    "category_type" => "pl",
					"amount" => $w["amount"],
					"link" => $r->site_url
				);
			}
		}
	}
	
	include "view/home/header.php";
	include "view/product/wishlist.php";
	include "view/home/footer.php";
}
?>
