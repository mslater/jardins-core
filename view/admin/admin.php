<?php
require_once "config.php";

$view = "";
if (isset($_GET["view"]))
	$view = $_GET["view"];
else
	$view = "index";
	
if (!isset($_SESSION["isadmin"]) || $_SESSION["isadmin"] !== true)
	$view = "login";

$temp = pathinfo(__FILE__);
define("CURRENT_VIEW",$view);
define("CURRENT_CONTROLLER",$temp["filename"]);
$funcview = "controller_" . $view;
if (function_exists($funcview))
	call_user_func($funcview);

function controller_login()
{
	if (isset($_SESSION["isadmin"]) && $_SESSION["isadmin"] === true)
	{
		header("Location: /admin.php");
		die();
	}
	if (isset($_POST["smLogin"]))
	{
		if ($_POST["username"] == "admin222" && $_POST["password"] == "isamenton#1")
		{
			$_SESSION["isadmin"] = true;
			header("Location: /admin.php");
			die();
		}
	}
	include "view/admin/login.php";
}

function controller_index()
{
	include 'view/admin/header.php';
	include 'view/admin/footer.php';
}

function controller_categories()
{
	$parent_id = 0;
	if (isset($_GET["parent_id"]))
		$parent_id = $_GET["parent_id"];
	$catsec = 'gi';
	if (isset($_GET["catsec"]))
		$catsec = $_GET["catsec"];
	
	if (isset($_POST["smUpdateOrder"]))
	{
		for ($i=0;$i<count($_POST["ordering"]);$i++)
		{
			mysql_query("UPDATE product_category SET ordering='".$_POST["ordering"][$i]."' WHERE id='".$_POST["ids"][$i]."'") or die(mysql_error());
		}
	}
	
	if (isset($_POST["sm_add_category"]) || isset($_POST["sm_update_category"]))
	{
		$category_name = $_POST["category_name"];
		
		if (isset($_POST["sm_add_category"]))
		{
			mysql_query("INSERT INTO product_category SET parent_id='{$parent_id}'") or die(mysql_error());
			$newid = mysql_insert_id();
		}
		else
			$newid = $_GET["parent_id"];
		
		$singledata = mysql_query("SELECT * FROM product_category WHERE id='$newid'");
		$single = mysql_fetch_object($singledata);
		$image = $single->picture;
		if (isset($_FILES["image"]["name"]) && $_FILES["image"]["name"] != '')
		{
			$temp = pathinfo($_FILES["image"]["name"]);
			$image = 'category_picture_' . $newid . "." . $temp["extension"];
			$pic_location = 'upload/product_images/'.$image;
			@unlink($pic_location);
			move_uploaded_file($_FILES["image"]["tmp_name"],$pic_location);
		}
		mysql_query("UPDATE product_category SET category_name='{$category_name}',
				category_type='{$catsec}',
				picture='{$image}' WHERE id='{$newid}'") or die(mysql_error());
		if ($image != '')
			mysql_query("UPDATE product_category SET picture='{$image}' WHERE id='{$newid}'") or die(mysql_error());
		if (isset($_POST["delete_image"]))
			mysql_query("UPDATE product_category SET picture='' WHERE id='{$newid}'") or die(mysql_error());
		
		$info = "You have update a category";
	}
	elseif (isset($_POST["sm_delete_category"]))
	{
		mysql_query("DELETE FROM product_category WHERE id='{$parent_id}'") or die(mysql_error());
		header("Location: /admin.php?view=categories");
		die();
	}
	
	$query = mysql_query("SELECT * FROM product_category WHERE category_type='{$catsec}' AND parent_id='{$parent_id}' ORDER BY ordering") or die(mysql_error());
	
	$table = "";
	while ($r = mysql_fetch_object($query))
	{
			$table .= '<tr>
				<td><a href="'.BASE_URL."admin.php?view=categories&catsec=".$catsec."&parent_id=".$r->id.'">'.$r->category_name.'</a></td>
				<td><input type="text" value="'.$r->ordering.'" name="ordering[]"/><input type="hidden" value="'.$r->id.'" name="ids[]"/></td>
				<td><a class="btn" href="/admin.php?view=products&catsec='.$catsec.'&catid='.$r->id.'">Add Product</a></td>
			</tr>';
	}
	
	$parent = "Root";
	if ($parent_id != 0)
	{
		$query = mysql_query("SELECT * FROM product_category WHERE id='{$parent_id}'") or die (mysql_error());
		$row = mysql_fetch_object($query);
		$parent = $row->category_name;
	}
	
	$submenu = "Garden Idea";
	
	include 'view/admin/header.php';
	include 'view/admin/manage_category_header.php';
	include 'view/admin/manage_categories.php';
	include 'view/admin/footer.php';
}

function AddKeyword($keyword,$category)
{
	$keyword = trim(strtolower($keyword));
	$category = trim(strtolower($category));
	$query = mysql_query("SELECT * FROM keyword_category WHERE category='{$category}'");
	if (mysql_num_rows($query) > 0)
	{
		$row = mysql_fetch_object($query);
		$cat_id = $row->id;
	}
	else
	{
		mysql_query("INSERT INTO keyword_category SET category='{$category}'");
		$cat_id = mysql_insert_id();
	}
	$query = mysql_query("SELECT * FROM keyword WHERE keyword='{$keyword}' AND category_id='{$cat_id}'");
	if (mysql_num_rows($query) > 0)
	{
		$objResponse = new xajaxResponse();
		$objResponse->script("alert('query is exist');");
		return $objResponse;
	}
	else
	{
		mysql_query("INSERT INTO keyword SET keyword='{$keyword}', category_id='{$cat_id}'");
		$kid = mysql_insert_id();
	}
	$objResponse = new xajaxResponse();
	$objResponse->prepend("keyword_list",'innerHTML','<tr id="kw_row_'.$kid.'">
			  <td><input type="text" id="kw_'.$kid.'" value="'.$keyword.'"/></td>
			  <td>'.$category.'</td>
			  <td class="scd">Never</td>
			  <td class="scc">0</td>
			  <td>
			  <input type="button" class="btn" onClick="xajax_UpdateKeyword('.$kid.',$(\'#kw_'.$kid.'\').val())" value="Update" />
			  <input type="button" class="btn" onClick="xajax_DeleteKeyword('.$kid.')" value="Delete" />
			  <a href="/admin.php?view=google_products&category='.urlencode($category).'" target="_blank" class="btn">View</a></td>
			</tr>');
	return $objResponse;
}


function is_domain_in_array($string, $array) {
	preg_match('@^(?:http://)?([^/]+)@i',$string, $matches);
	$host = $matches[1];
	$host = str_replace('www.','',$host);
	preg_match('/[^\.]*/', $host, $matches);
	$string = $matches[0];

	$value = false;
	foreach($array as $element) {
		if (strtolower(trim($element)) == strtolower(trim($string)) ) {
			return true;
		}
	}
	return false;
}

function UpdateKeyword($kid,$keyword,$show_output = true)
{	
	$keyword = trim($keyword);
	if(!empty($keyword)) {
		mysql_query("UPDATE keyword SET keyword='".$keyword."' 
			WHERE `id`='{$kid}'") or die (mysql_error());
	}
	$query = mysql_query("SELECT * FROM keyword WHERE id='{$kid}'") or die (mysql_error());
	$r = mysql_fetch_object($query);
	$cat_id = $r->category_id;
	
	$allowed_countries = array("US", "UK", "FR", "AU", "DE", "IT", "NL", "CH", "ES");
	$allowed_merchants = array();
	$query = mysql_query("SELECT * FROM allow_merchant") or die (mysql_error());
	while ($r = mysql_fetch_object($query))
		$allowed_merchants[] = $r->merchant;
	
	$found_product = array();
	$keywords = explode(" ",strtolower($keyword));
	$all_results = 0;
	$found_titles = array();
	$all_titles = array();
	$ignored_count = 0;
	$ignored_count2 = 0;
	$added_products = "";
	$allPStr = "";
	foreach($allowed_countries as $country) {
		$json  = QueryGoogleApi($keyword,$country);
		if ($json == false)//exceed the google api limit
		{
			if ($show_output == true)
			{
				$results = array();
				$results["error"] = "Error: Daily Limit Exceeded";
				header('Content-type: application/json');
				echo json_encode($results);	
				die();
			}
			else
				return false;
		}
		$json_output = json_decode($json);
		if (isset($json_output->items) == false)
			continue;
		foreach($json_output->items as $item) {
			$all_results++;
			$all_titles[] = $item->product->title . " (".$item->product->link.")";
			
			if(is_domain_in_array($item->product->link, $allowed_merchants)) {
				$product_title_keyword = explode(" ",preg_replace('/[^a-zA-Z0-9]/',' ',replace_accents(strtolower(urldecode($item->product->title)))));
				$keyword_ok = true;				
				foreach ($keywords as $k)
				{
					if (in_array(replace_accents($k),$product_title_keyword) == false)
					{
						$keyword_ok = false;
						$ignored_count2++;
						$allPStr .= $item->product->title . " (".$country.") - <a target=\"_blank\" href=\"".$item->product->link."\">" . $item->product->link . "</a><br><br>";
						break;
					}
				}
				if ($keyword_ok == false)
					continue;
				$found_titles[] = $item->product->title;
				$found_product[] = strtolower($item->product->title . ',' . $item->product->author->name . ',' . $country . ',' . 'google');
				$ID = product_already_in_db($item->product,$country, $cat_id);
				
				$added_products .= $item->product->title . " (".$country.") - <a target=\"_blank\" href=\"".$item->product->link."\">" . $item->product->link . "</a><br><br>";
				if($ID != false) {
					mysql_query( 
							"UPDATE google_scraped SET price = '".$item->product->inventories[0]->price."'
							, description='".mysql_real_escape_string($item->product->description)."',
							shipping_cost='".$item->product->inventories[0]->shipping."',
							availability='".$item->product->inventories[0]->availability."' 
								WHERE `id` = '".$ID."'
							") or die (mysql_error());
				}
				else {
					$query = "INSERT INTO google_scraped SET 
						name='".mysql_real_escape_string($item->product->title)."',
						picture='".mysql_real_escape_string($item->product->images[0]->link)."',
						site_url='".mysql_real_escape_string($item->product->link)."',
						price='".mysql_real_escape_string($item->product->inventories[0]->price)."',
						keyword_id='{$kid}',
						merchant_name='".mysql_real_escape_string($item->product->author->name)."',
						country='{$country}',
						google_item_id='".mysql_real_escape_string($item->product->googleId)."',
						scraped_type='google',
						description='".mysql_real_escape_string($item->product->description)."',
						shipping_cost='".mysql_real_escape_string($item->product->inventories[0]->shipping)."',
						availability='".mysql_real_escape_string($item->product->inventories[0]->availability)."'";
					mysql_query($query) or die(mysql_error());
				}
						
			}
			else
			{
				$notinmarchants .= $item->product->title . " (".$country.") - <a target=\"_blank\" href=\"".$item->product->link."\">" . $item->product->link . "</a><br><br>";
				$ignored_count++;
			}
		}
	}
	
	mysql_query("UPDATE keyword SET last_update='".time()."'WHERE id='{$kid}'") or die(mysql_error());
	
	
	$results = array();
	$results["all_results"] = $all_results;
	$results["all_titles"] = $all_titles;
	$results["found_count"] = count($found_product);
	$results["found_titles"] = $found_titles;
	$results["error"] = "";
	$results["delete_count"] = DeleteOutdateProducts($found_product,$keyword,$cat_id);


	$result_res = "All Scraped: " . $results["all_results"] . "<br>";
	$result_res .= "Deleted Outdate Products: " . $results["delete_count"] . "<br>";
	$result_res .= "</center><h4>Added Products (".count($found_product)."):</h4></center> <br>" . $added_products . "<br>";
	$result_res .= "</center><h4>Not in merchant list Products (".$ignored_count."):</h4></center> <br>" . $notinmarchants . "<br>";
	$result_res .= "</center><h4>Not contained keyword (".$ignored_count2."):</h4></center> <br><div id=\"allproduct\">" . $allPStr . "</div><br>";
	$result_res = str_replace('\'','\\\'',$result_res);
	
	if ($show_output == true)
	{
		$objResponse = new xajaxResponse();
		$objResponse->script('$("#scraped_result_box_content").html(\''.$result_res.'\'); 
		$("#kw_row_'.$kid.' .scd").html(\''.date("m/d/Y h:i:s").'\'); 
		$("#kw_row_'.$kid.' .scc").html(\''.count($found_product).'\'); 
		$("#scraped_result_box").dialog({width:800,height:800});');
		return $objResponse;
	}
	else
	{
		return true;
	}
}

function product_already_in_db($product,$country,$category_id) {
	global $wpdb;
	$query = mysql_query("SELECT g.id FROM google_scraped AS g
		INNER JOIN keyword AS k ON g.keyword_id=k.id
		WHERE name='".mysql_real_escape_string($product->title)."' 
		AND merchant_name='".mysql_real_escape_string($product->author->name)."' 
		AND country='{$country}' 
		AND category_id='".$category_id."'") or die (mysql_error());
	$r = mysql_fetch_object($query);
	$ID = @$r->id;
	if($ID) {
		return $ID;
	}
	else {
		return false;
	}
}

function DeleteOutdateProducts($found,$searched_term,$category_id)
{
	global $wpdb;
	$delete_count = 0;
	if (count($found) == 0)
		return 0;//there is no result, it may has problem with scraper. so we do not delete in that case.
	$query = mysql_query("SELECT g.* FROM google_scraped AS g
		INNER JOIN keyword AS k ON g.keyword_id=k.id
		WHERE keyword='{$searched_term}' 
		AND category_id='{$category_id}'");
	$inserted_items = array();
	while ($r = mysql_fetch_object($query))
		$inserted_items[] = array(
			"id" => $r->id,
			"item" => strtolower($r->name . ',' . $r->merchant_name . ',' . $r->country . ',' . $r->scraped_type)
			);
	
	foreach ($inserted_items as $i)
	{
		if (in_array($i["item"], $found) == false)	
		{
			//print_r($i);
			mysql_query("DELETE FROM google_scraped WHERE `id` = '".$i["id"]."'") or die(mysql_error());
			$delete_count++;
		}
	}
	return $delete_count;
}

function DeleteKeyword($kid)
{
	$query = mysql_query("SELECT * FROM keyword WHERE id='{$kid}'");
	$row = mysql_fetch_object($query);
	$kid = $row->id;
	$cat_id = $row->category_id;
	mysql_query("DELETE FROM keyword WHERE id='{$kid}'");
	mysql_query("DELETE FROM google_scraped WHERE keyword_id='{$kid}'");
	$query = mysql_query("SELECT * FROM keyword WHERE category_id='{$cat_id}'");
	if (mysql_num_rows($query) == 0)
	{
		mysql_query("DELETE FROM keyword_category WHERE id='{$cat_id}'");
	}
	$objResponse = new xajaxResponse();
	$objResponse->assign("kw_row_".$kid,'innerHTML','');
	return $objResponse;
}

function SearchAll()
{
	$query = mysql_query("SELECT * FROM keyword ORDER BY last_update LIMIT 30");
	$done = "";
	while ($r = mysql_fetch_object($query))
	{
		if (UpdateKeyword($r->id,$r->keyword,false) == false)
		{
			$objResponse = new xajaxResponse();
			$objResponse->script(" alert('you ran exceed the allowance today.'); $('#smSearchAll').show(); $('#smSearchAllwait').hide();");
			return $objResponse;
		}
		$done .= $r->keyword . ", ";
	}
	$objResponse = new xajaxResponse();
	$objResponse->script(" alert('finished running queries'); alert('Done: ".trim($done,",")."'); $('#smSearchAll').show(); $('#smSearchAllwait').hide();");
	return $objResponse;
}

function controller_google_keywords()
{
	require( "lib/xajax/xajax_core/xajax.inc.php" );
	$xajax=new xajax();
	$xajax->configure('javascript URI','lib/xajax/');
	$xajax->register(XAJAX_FUNCTION,"AddKeyword");
	$xajax->register(XAJAX_FUNCTION,"UpdateKeyword");
	$xajax->register(XAJAX_FUNCTION,"DeleteKeyword");
	$xajax->register(XAJAX_FUNCTION,"SearchAll");
	$xajax->processRequest();
	
	$keywords = mysql_query("SELECT DISTINCT k.id AS keyword_id,k.*,kc.*,IF (COUNT(k.id) - 1 <> 0, COUNT(k.id),COUNT(k.id) - 1) AS nbproducts FROM keyword AS k
		INNER JOIN keyword_category AS kc ON k.category_id=kc.id
		LEFT JOIN google_scraped AS gs ON gs.keyword_id=k.id
		GROUP BY k.id
		ORDER BY category");

	include 'view/admin/header.php';
	include 'view/admin/google_menu.php';
	include 'view/admin/manage_googlescraper.php';
	include 'view/admin/footer.php';
}

function controller_google_merchants()
{
	if (isset($_GET["delete_id"]))
	{
		$did = strtolower(trim($_GET["delete_id"]));
		mysql_query("DELETE FROM allow_merchant WHERE id='{$did}'");
	}
	if (isset($_POST["smAddMerchant"]))
	{
		$merchant = strtolower(trim($_POST["merchant"]));
		mysql_query("INSERT IGNORE INTO allow_merchant SET merchant='{$merchant}'");
	}
	$merchants = mysql_query("SELECT * FROM allow_merchant");
	include 'view/admin/header.php';
	include 'view/admin/google_menu.php';
	include 'view/admin/manage_google_merchants.php';
	include 'view/admin/footer.php';
}

function controller_google_products()
{
	$category = "";
	$kid = "";
	$keyword = "";
	$merchant = "";
	if (isset($_GET["category"]))
		$category = $_GET["category"];
	$category = strtolower(trim($category));
	if (isset($_GET["kid"]))
		$kid = $_GET["kid"];
	$kid = strtolower(trim($kid));
	if (isset($_GET["keyword"]))
		$keyword = $_GET["keyword"];
	$keyword = strtolower(trim($keyword));
	if (isset($_GET["merchant"]))
		$merchant = $_GET["merchant"];
	$merchant = strtolower(trim($merchant));
	
	$where = "";
	
	if (isset($_GET["smSearchAll"]))
		$where .= " ";
	if ($category != '')
		$where .= " AND kc.category='{$category}'";
	if ($kid != '')
		$where .= " AND g.keyword_id='{$kid}'";
	if ($keyword != '')
		$where .= " AND k.keyword LIKE '%{$keyword}%'";
	if ($merchant != '')
		$where .= " AND g.merchant_name LIKE '%{$merchant}%'";
			
	if ($where != '')
		$data_scraped = mysql_query("SELECT DISTINCT g.*,k.keyword,kc.category FROM google_scraped AS g
			INNER JOIN keyword AS k ON g.keyword_id = k.id
			INNER JOIN keyword_category as kc ON k.category_id=kc.id
			WHERE 1=1 {$where}");
	//echo $where;
	include 'view/admin/header.php';
	include 'view/admin/google_menu.php';
	include 'view/admin/manage_google_products.php';
	include 'view/admin/footer.php';
}


function QueryGoogleApi($q,$country)
{
	$q = urlencode($q);
	$api_key = "AIzaSyBj8dN80ZMDigr2aoGWbpIU1ur-730JA2s";
	$searched_url = "https://www.googleapis.com/shopping/search/v1/public/products?key=".$api_key."&country=".$country."&q=".$q."&maxResults=300&alt=json";
	$query_code = md5($searched_url);
	$query = mysql_query("SELECT scraped_data FROM scraped_cache WHERE code='{$query_code}' AND updated_date > " . (time() - 259200)) or die(mysql_error());
	if (mysql_num_rows($query) > 0)
	{
		$row = mysql_fetch_object($query);
		return $row->scraped_data;
	}
	$json  = file_get_contents($searched_url);
	mysql_query("DELETE FROM scraped_cache WHERE code='{$query_code}'") or die (mysql_error());//we don't want a duplicated cache
	mysql_query("INSERT INTO scraped_cache SET 
		updated_date='".time()."',
		code='".$query_code."',
		scraped_data='".mysql_real_escape_string($json)."'") or die (mysql_error());
	return $json;
}

function controller_products()
{
	if (isset($_GET["edit_id"]))
		$product_id = $_GET["edit_id"];
	
	if (isset($_GET["delete_id"]))
	{
		$product_id = mysql_real_escape_string($_GET["delete_id"]);
		mysql_query("DELETE FROM product WHERE id='{$product_id}'") or die (mysql_error());
		header("Location: /admin.php?view=products");
		die();
	}
	 
	if (isset($_POST["smAddProduct"]) || isset($_POST["smEditProduct"]))
	{

		$title = mysql_real_escape_string($_POST["title"]);
		$description = mysql_real_escape_string($_POST["description"]);
		$radio_product_type = mysql_real_escape_string($_POST["radio_product_type"]);
		$radio_publish = mysql_real_escape_string($_POST["radio_publish"]);
		$category = urlencode(mysql_real_escape_string($_POST["category_".$radio_product_type]));
		if (isset($_POST["smAddProduct"]))
		{
			mysql_query("INSERT INTO product SET
				name='{$title}',
				description='{$description}',
				status='{$radio_publish}'") or die (mysql_error());
			$product_id = mysql_insert_id();
		}
		elseif (isset($_POST["smEditProduct"]))
		{
			mysql_query("UPDATE product SET
				name='{$title}',
				description='{$description}',
				status='{$radio_publish}',
				status='{$radio_publish}'
				WHERE id='{$product_id}'") or die (mysql_error());
		}
	}
	
	if (isset($product_id) && (isset($_POST["smAddProduct"]) || isset($_POST["smEditProduct"])))
	{
		//set categories
		mysql_query("DELETE FROM product_in_category WHERE product_id='{$product_id}'") or die("delete products category:" . mysql_error());
		foreach ($_POST["categories"] as $c)
		{
			if ($c != '')
				mysql_query("INSERT INTO product_in_category SET product_id='{$product_id}', category_id='".mysql_real_escape_string($c)."'") or die("insert category products:" . mysql_error());
		}
		
		$seller_categories = $_POST["seller_categories"];
		mysql_query("DELETE FROM product_seller_item WHERE product_id='{$product_id}'") or die (mysql_error());
		foreach ($seller_categories as $i)
		{
			if ($i != '')
				mysql_query("INSERT INTO product_seller_item SET product_id='{$product_id}', keyword_category_id='{$i}'") or die (mysql_error());
		}
		
		mysql_query("DELETE FROM related_product WHERE product_id='{$product_id}'") or die (mysql_error());
		$related_products = $_POST["related_products"];
		foreach ($related_products as $i)
		{
			if ($i != '')
				mysql_query("INSERT INTO related_product SET product_id='{$product_id}', related_id='{$i}', related_type='seller'") or die (mysql_error());
		}
		
		$data_old = mysql_query("SELECT * FROM product WHERE id='{$product_id}'") or die(mysql_error());
		$old_info = mysql_fetch_object($data_old);
		$mainPic = $old_info->main_picture;
		if (isset($_FILES["main_picture"]["name"]) && $_FILES["main_picture"]["name"] != '')
		{
			$temp = pathinfo($_FILES["main_picture"]["name"]);
			@unlink('upload/product_images/'.$mainPic);
			$mainPic = 'main_picture_' . uniqid() . "." . $temp["extension"];
			$pic_location = 'upload/product_images/'.$mainPic;
			move_uploaded_file($_FILES["main_picture"]["tmp_name"],$pic_location);
		}
		if (isset($_POST["delete_image"]))
		{
			$mainPic = '';
		}
		
		$keywords = $_POST["seo_keyword"];
		if (trim($keywords) == "")
		{
			include "lib/AlchemyAPI/AlchemyAPI.php";
			$alchemyObj = new AlchemyAPI();
			$alchemyObj->setAPIKey(ALCHEMY_API);
			$plContent = preg_replace('/<[^>]*>/is',' ',$description);
			$plContent = preg_replace('/\[[^\]]*\]/is',' ',$plContent);
			$result = $alchemyObj->TextGetRankedKeywords($plContent,AlchemyAPI::JSON_OUTPUT_MODE);
			$res = array();
			$json = json_decode($result);
			foreach ($json->keywords as $k)
			{
				$res[] = trim($k->text);
			}
			$keywords = implode(", ",$res);
		}
		mysql_query("UPDATE product SET main_picture='{$mainPic}',
			calculator_name='".mysql_real_escape_string($_POST["calculator_name"])."',
			calculator_size='".mysql_real_escape_string($_POST["calculator_size"])."',
			requirement_id='".mysql_real_escape_string($_POST["requirement"])."',
			map_address='".mysql_real_escape_string($_POST["map"])."',
			contact_form_id='".mysql_real_escape_string($_POST["contact"])."',
			wider_content='".mysql_real_escape_string($_POST["wider_content"])."',
			product_tabs='".mysql_real_escape_string($_POST["product_tabs"])."',
			isfeatured='".mysql_real_escape_string($_POST["radio_feature"])."',
			common_name='".mysql_real_escape_string($_POST["common_name"])."',
			intro='".mysql_real_escape_string($_POST["intro"])."',
			seo_keyword='".mysql_real_escape_string($keywords)."',
			image_alt='".mysql_real_escape_string($_POST["image_alt"])."',
			seo_description='".mysql_real_escape_string($_POST["seo_description"])."',
			care_id='".mysql_real_escape_string($_POST["care"])."'
			WHERE id='{$product_id}'") or die (mysql_error());
		for($i=0;$i<count(@$_FILES["photo"]["name"]);$i++)
		{
			if ($_FILES["photo"]["name"][$i] == "")
				continue;
			$temp = pathinfo($_FILES["photo"]["name"][$i]);
			$mainPic = uniqid() . "." . $temp["extension"];
			$pic_location = 'upload/product_images/'.$mainPic;
			mysql_query("INSERT INTO product_images SET name='{$mainPic}', 
				description='".mysql_real_escape_string($_POST["photo_desc"][$i])."', 
				caption='".mysql_real_escape_string($_POST["photo_caption"][$i])."', 
				alter_text='".mysql_real_escape_string($_POST["photo_altertext"][$i])."', 
				product_id='{$product_id}'") or die(mysql_error());
			move_uploaded_file($_FILES["photo"]["tmp_name"][$i],$pic_location);
		}
		
		for($i=0;$i<count($_POST["delete_photo"]);$i++)
		{
			mysql_query("DELETE FROM product_images 
				WHERE id='".mysql_real_escape_string($_POST["delete_photo"][$i])."'") or die(mysql_error());
		}
		
		for($i=0;$i<count($_POST["old_photo_id"]);$i++)
		{
			mysql_query("UPDATE product_images SET 
				description='".mysql_real_escape_string($_POST["old_photo_desc"][$i])."', 
				ordering='".mysql_real_escape_string($_POST["ordering"][$i])."', 
				caption='".mysql_real_escape_string($_POST["old_photo_caption"][$i])."', 
				alter_text='".mysql_real_escape_string($_POST["old_photo_altertext"][$i])."' 
				WHERE id='".mysql_real_escape_string($_POST["old_photo_id"][$i])."'") or die(mysql_error());
		}
		
		if (isset($_POST["delete_reward_icon"]))
			mysql_query("UPDATE product SET reward_icon='' WHERE id='{$product_id}'");
		elseif (isset($_FILES["reward_icon"]["name"]) && $_FILES["reward_icon"]["name"] != '')
		{
			$temp = pathinfo($_FILES["reward_icon"]["name"]);
			$mainPic = 'award_icon_' . $product_id . "." . $temp["extension"];
			$pic_location = 'upload/product_images/'.$mainPic;
			@unlink($pic_location);
			move_uploaded_file($_FILES["reward_icon"]["tmp_name"],$pic_location);
			mysql_query("UPDATE product SET reward_icon='{$mainPic}' WHERE id='{$product_id}'");
		}
		
		$datarlp = mysql_query("SELECT * FROM product WHERE status=1");
		
		header("Location: /admin.php?view=products&msg=s&edit_id={$product_id}");
		die();
	}
	
	$data_categories = mysql_query("SELECT * FROM keyword_category ORDER BY category") or die (mysql_error());
	$option_gi = "";
	$option_pl = "";
	
	$q = mysql_query("SELECT * FROM product_category WHERE category_type='gi' AND parent_id=0") or die (mysql_error());
	while ($r = mysql_fetch_object($q))
	{
		$option_gi .= '<option value="'.$r->id.'">'.$r->category_name.'</option>';
		$q2 = mysql_query("SELECT * FROM product_category WHERE category_type='gi' AND parent_id='{$r->id}'") or die (mysql_error());
		while ($r2 = mysql_fetch_object($q2))
		{
			$option_gi .= '<option value="'.$r2->id.'">---'.$r2->category_name.'</option>';
		}
	}
	
	$q = mysql_query("SELECT * FROM product_category WHERE category_type='pl' AND parent_id=0") or die (mysql_error());
	while ($r = mysql_fetch_object($q))
	{
		$option_pl .= '<option value="'.$r->id.'">'.$r->category_name.'</option>';
		$q2 = mysql_query("SELECT * FROM product_category WHERE category_type='pl' AND parent_id='{$r->id}'") or die (mysql_error());
		while ($r2 = mysql_fetch_object($q2))
		{
			$option_pl .= '<option value="'.$r2->id.'">---'.$r2->category_name.'</option>';
		}
	}
	
	if (isset($product_id))
	{
		$data_sellers = mysql_query("SELECT * FROM product_seller_item WHERE product_id='{$product_id}'");
		$data_product_info = mysql_query("SELECT * FROM product WHERE id='{$product_id}'");
		$product_info = mysql_fetch_object($data_product_info);
		$data_photos = mysql_query("SELECT * FROM product_images WHERE product_id='{$product_id}'");
		$data_catagory = mysql_query("SELECT * FROM product_in_category WHERE product_id='{$product_id}'");
		$relatedproducts = mysql_query("SELECT * FROM related_product WHERE product_id='{$product_id}'") or die(mysql_error());
	}
	
	$data_requirement = mysql_query("SELECT * FROM requirement ORDER BY name") or die(mysql_error());
	$data_care = mysql_query("SELECT * FROM form WHERE content_type='care' ORDER BY name") or die(mysql_error());
	$data_contact = mysql_query("SELECT * FROM form WHERE content_type='contact' ORDER BY name") or die(mysql_error());
	
	include 'view/admin/header.php';
	include 'view/admin/products_menu.php';
	include 'view/admin/edit_products.php';
	include 'view/admin/footer.php';
}

function controller_products_list()
{
	$where = "";
	if (isset($_GET["searchKw"]))
	{
		$searchKw = mysql_real_escape_string(trim($_GET["searchKw"]));
		$fcategory = $_GET["filter_category"];
		$fstatus = $_GET["filter_status"];
		$ffeature = $_GET["filter_feature"];
		$where = " AND (p.name LIKE '%{$searchKw}%' OR  p.common_name LIKE '%{$searchKw}%' OR  p.description LIKE '%{$searchKw}%') ";
		if ($fcategory != '')
			$where .= " AND pc.id='{$fcategory}' ";
		if ($fstatus != '')
			$where .= " AND p.status='{$fstatus}' ";
		if ($ffeature != '')
			$where .= " AND p.isfeatured='{$ffeature}' ";
	}
	
	if (isset($_POST["smBulkUpdate"]) && count($_POST["selectedid"]) > 0)
	{
		foreach ($_POST["selectedid"] as $id)
		{
			if (count($_POST["categories"]) > 0 && $_POST["category_edit_type"] == "replace")
				mysql_query("DELETE FROM product_in_category WHERE product_id='{$id}'") or die("delete products category:" . mysql_error());
			foreach ($_POST["categories"] as $c)
			{
				if ($c != '')
				{
					$datacount = mysql_query("SELECT * FROM product_in_category WHERE product_id='{$id}' AND category_id='".mysql_real_escape_string($c)."'") or die(mysql_error());;
					if (mysql_num_rows($datacount) == 0)
						mysql_query("INSERT INTO product_in_category SET product_id='{$id}', category_id='".mysql_real_escape_string($c)."'") or die("insert category products:" . mysql_error());
				}
			}
			if ($_POST["radio_publish"] != '')
				mysql_query("UPDATE product SET status='".$_POST["radio_publish"]."' WHERE id='{$id}'") or die(mysql_error());
		}
	}
	
	$sort_field = isset($_GET["sort_field"]) ? $_GET["sort_field"] : "created_date";
	$sort_type = isset($_GET["sort_type"]) ? $_GET["sort_type"] : "desc";
	
	if ($_GET["category"] == "")
	{
		$q = "SELECT * FROM `product` AS p WHERE p.id NOT IN (SELECT product_id FROM product_in_category AS pc)";
	}
	else
	{
		$q = "SELECT distinct p.* FROM product AS p 
				INNER JOIN product_in_category AS pic ON p.id = pic.product_id
				INNER JOIN product_category AS pc ON pc.id = pic.category_id
				WHERE pc.category_type='".$_GET["category"]."' {$where} 
				ORDER BY $sort_field $sort_type";
	}
	//echo $q;exit;
	$data_list = mysql_query($q) or die (mysql_error());
	$total = mysql_num_rows($data_list);
	
	require( "lib/xajax/xajax_core/xajax.inc.php" );
	$xajax=new xajax();
	$xajax->configure('javascript URI','lib/xajax/');
	$xajax->register(XAJAX_FUNCTION,"DeleteProductFromList");
	$xajax->processRequest();
	
	include 'view/admin/header.php';
	include 'view/admin/products_menu.php';
	include 'view/admin/products_list.php';
	include 'view/admin/footer.php';
}

function DeleteProductFromList($id)
{
	$id = mysql_real_escape_string($id);
	mysql_query("DELETE FROM product WHERE id='{$id}'");
}

function controller_forms()
{
	$type = "req";
	if (isset($_GET["type"]))
		$type = $_GET["type"];
	if ($type == "req")
		$data_forms = mysql_query("SELECT * FROM requirement ORDER BY name");
	else
		$data_forms = mysql_query("SELECT * FROM form WHERE content_type='{$type}' ORDER BY name");
	include 'view/admin/header.php';
	include 'view/admin/forms_menu.php';
	include 'view/admin/forms_list.php';
	include 'view/admin/footer.php';
}

function controller_forms_edit()
{
	$type = "req";
	if (isset($_GET["type"]))
		$type = mysql_real_escape_string($_GET["type"]);
	
	if (isset($_GET["delete_id"]))
	{
		$delete_id = mysql_real_escape_string($_GET["delete_id"]);
		if ($type == "req")
		{
			mysql_query("DELETE FROM requirement WHERE id='{$delete_id}'");
		}
		else
		{
			mysql_query("DELETE FROM form WHERE id='{$delete_id}'");
		}
		header("Location: admin.php?view=forms&type={$type}");
		die();
	}
	
	if (isset($_GET["id"]))
	{
		$id = mysql_real_escape_string($_GET["id"]);
	}
	
	if (isset($_POST["smUpdate"]))
	{
		$name = mysql_real_escape_string($_POST["name"]);
		$category = mysql_real_escape_string($_POST["category"]);
		$content = mysql_real_escape_string($_POST["content"]);
		if (!isset($id))
		{
			mysql_query("INSERT INTO form SET name='{$name}', content='{$content}',keyword_category_id='{$category}', content_type='{$type}'") or die(mysql_error());
			$id = mysql_insert_id();
		}
		else
			mysql_query("UPDATE form SET name='{$name}', content='{$content}', keyword_category_id='{$category}', content_type='{$type}' WHERE id='{$id}'") or die(mysql_error());
		$_SESSION['info'] = "Updating form successfully";	
		header("Location: admin.php?view=forms_edit&type={$type}&id={$id}");
		die();
	}
	if (isset($_POST["smUpdateReq"]))
	{
		$name = mysql_real_escape_string($_POST["name"]);
		$related_category = mysql_real_escape_string($_POST["related_category"]);
		$hardinessMin = mysql_real_escape_string($_POST["hardinessMin"]);
		$hardinessMax = mysql_real_escape_string($_POST["hardinessMax"]);
		$sun1 = mysql_real_escape_string($_POST["sun1"]);
		$sun2 = mysql_real_escape_string($_POST["sun2"]);
		$sun3 = mysql_real_escape_string($_POST["sun3"]);
		$sun4 = mysql_real_escape_string($_POST["sun4"]);
		$periodofinterest = mysql_real_escape_string($_POST["periodofinterest"]);
		$difficulty = mysql_real_escape_string($_POST["difficulty"]);
		$water = mysql_real_escape_string($_POST["water"]);
		$maintain = mysql_real_escape_string($_POST["maintain"]);
		$plant_type1 = mysql_real_escape_string($_POST["plant_type1"]);
		$plant_type2 = mysql_real_escape_string($_POST["plant_type2"]);
		$plant_type3 = mysql_real_escape_string($_POST["plant_type3"]);
		$height = mysql_real_escape_string($_POST["height"]);
		$spacing = mysql_real_escape_string($_POST["spacing"]);
		$depth = mysql_real_escape_string($_POST["depth"]);
		$feature = mysql_real_escape_string($_POST["feature"]);
		$use = mysql_real_escape_string($_POST["use"]);
		if (!isset($id))
		{
			mysql_query("INSERT INTO requirement SET name='{$name}'") or die(mysql_error());
			$id = mysql_insert_id();
		}
              
                $hardiness = $hardinessMin." - ".$hardinessMax;


		mysql_query("UPDATE requirement SET  name='{$name}',
			 related_category='{$related_category}',
			 hardiness='{$hardiness}',
			 sun1='{$sun1}',
			 sun2='{$sun2}',
			 sun3='{$sun3}',
			 sun4='{$sun4}',
			 periodofinterest='{$periodofinterest}',
			 difficulty='{$difficulty}',
			 water='{$water}',
			 maintain='{$maintain}',
			 plant_type1='{$plant_type1}',
			 plant_type2='{$plant_type2}',
			 plant_type3='{$plant_type3}',
			 height='{$height}',
			 spacing='{$spacing}',
			 depth='{$depth}',
			 feature='{$feature}',
			 `use`='{$use}' WHERE id='{$id}'") or die (mysql_error());
		header("Location: admin.php?view=forms_edit&type={$type}&id={$id}");
		die();
	}
	
	if (isset($_GET["copyid"]))
		$id = $_GET["copyid"];
	
	if (isset($id))
	{
		if ($type == "req")
			$query = mysql_query("SELECT * FROM requirement WHERE id='{$id}'") or die(mysql_error());
		else
			$query = mysql_query("SELECT * FROM form WHERE id='{$id}'") or die(mysql_error());
		$form = mysql_fetch_object($query);
	}
	
	$data_category = mysql_query("SELECT * FROM keyword_category ORDER BY category");
	
	include 'view/admin/header.php';
	include 'view/admin/forms_menu.php';
	if ($type == "req")
		include 'view/admin/requirement_form_edit.php';
	else
		include 'view/admin/form_edit.php';
	include 'view/admin/footer.php';
}

function controller_cms_edit()
{
	
	$id = mysql_real_escape_string($_GET["id"]);
	if (isset($_POST["smCMS"]))
	{
		$title = mysql_real_escape_string($_POST["title"]);
		$content = mysql_real_escape_string($_POST["content"]);
		$q = "UPDATE cms_page SET title='{$title}', content='{$content}' WHERE id='{$id}'";
		mysql_query($q) or die (mysql_error());
		$info = "Page is updated";
	}
	$data = mysql_query("SELECT * FROM cms_page WHERE id='{$id}'");
	$r = mysql_fetch_object($data);
	
	include 'view/admin/header.php';
	include 'view/admin/cms_edit.php';
	include 'view/admin/footer.php';
}

function controller_cms_list()
{
	include 'view/admin/header.php';
	include 'view/admin/cms_list.php';
	include 'view/admin/footer.php';
}

function controller_settings()
{

	for($i=0;$i<count($_POST["delete_photo"]);$i++)
	{
		mysql_query("DELETE FROM sliderimage 
			WHERE id='".mysql_real_escape_string($_POST["delete_photo"][$i])."'") or die(mysql_error());
	}
	for($i=0;$i<count(@$_FILES["photo"]["name"]);$i++)
	{
		if ($_FILES["photo"]["name"][$i] == "")
			continue;
		$temp = pathinfo($_FILES["photo"]["name"][$i]);
		$mainPic = uniqid() . "." . $temp["extension"];
		$pic_location = 'upload/product_images/'.$mainPic;
		mysql_query("INSERT INTO sliderimage SET image='{$mainPic}', 
			caption='".mysql_real_escape_string($_POST["photo_caption"][$i])."', 
			link='".mysql_real_escape_string($_POST["photo_url"][$i])."'") or die(mysql_error());
		move_uploaded_file($_FILES["photo"]["tmp_name"][$i],$pic_location);
	}
	$data_sliderphotos = mysql_query("SELECT * FROM sliderimage");
	include 'view/admin/header.php';
	include 'view/admin/settings.php';
	include 'view/admin/footer.php';
}

$_SESSION['KCFINDER'] = array();
$_SESSION['KCFINDER']['disabled'] = false;