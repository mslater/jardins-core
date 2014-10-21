<?php

require_once "config.php";
require_once "lib/phpfastcache/phpfastcache.php";
phpFastCache::$storage = "auto";

$view = "";
if (isset($_GET["view"]))
    $view = $_GET["view"];
else
    $view = "index";

if (!isset($_SESSION["isadmin"]) || $_SESSION["isadmin"] !== true)
    $view = "login";

$temp = pathinfo(__FILE__);
define("CURRENT_VIEW", $view);
define("CURRENT_CONTROLLER", $temp["filename"]);
$funcview = "controller_" . $view;
$funcview = str_replace("-", "_", $funcview);
if (function_exists($funcview))
    call_user_func($funcview);

function controller_cleancache() {
    __c()->clean();
    echo "CACHE IS CLEAN";
}

function controller_logout()
{
    unset($_SESSION["isadmin"]);
    header("location: /");
    die();
}

function controller_login() {
    if (isset($_SESSION["isadmin"]) && $_SESSION["isadmin"] === true) {
        header("Location: /admin.php");
        die();
    }
    if (isset($_POST["smLogin"])) {
        $view = $_POST["redirect"];
        if ($_POST["username"] == "admin222" && $_POST["password"] == "Menton") {
            $_SESSION["isadmin"] = true;
            if ($view != '')
                header("Location: /admin.php?view=".$view);
            else
                header("Location: /admin.php");
            die();
        }
    }
    include "view/admin/login.php";
}

function controller_index() {
    include 'view/admin/header.php';
    include 'view/admin/footer.php';
}

function controller_categories() {
    $parent_id = 0;
    if (isset($_GET["parent_id"]))
        $parent_id = $_GET["parent_id"];
    $catsec = 'gi';
    if (isset($_GET["catsec"]))
        $catsec = $_GET["catsec"];

    if (isset($_POST["smUpdateOrder"])) {
        for ($i = 0; $i < count($_POST["ordering"]); $i++) {
            mysql_query("UPDATE product_category SET ordering='" . $_POST["ordering"][$i] . "' WHERE id='" . $_POST["ids"][$i] . "'") or die("ERROR 119:" . mysql_error());
        }
    }

    if (isset($_POST["sm_add_category"]) || isset($_POST["sm_update_category"])) {
        $category_name = $_POST["category_name"];
        $meta_description = $_POST["meta_description"];
        $meta_keywords = $_POST["meta_keywords"];

        if (isset($_POST["sm_add_category"])) {
            mysql_query("INSERT INTO product_category SET parent_id='{$parent_id}'") or die("ERROR 120:" . mysql_error());
            $newid = mysql_insert_id();
        } else
            $newid = $_GET["parent_id"];

        $singledata = mysql_query("SELECT * FROM product_category WHERE id='$newid'");
        $single = mysql_fetch_object($singledata);
        $image = $single->picture;
        if (isset($_FILES["image"]["name"]) && $_FILES["image"]["name"] != '') {
            $temp = pathinfo($_FILES["image"]["name"]);
            $image = 'category_picture_' . $newid . "." . $temp["extension"];
            $pic_location = 'upload/product_images/' . $image;
            @unlink($pic_location);
            move_uploaded_file($_FILES["image"]["tmp_name"], $pic_location);
        }
        $visible = 0;
        if (isset($_POST["visible"]))
            $visible = $_POST["visible"];
        $only_show_treeview = 0;
        if (isset($_POST["only_show_treeview"]))
            $only_show_treeview = $_POST["only_show_treeview"];
        $only_show_thumbnail = 0;
        if (isset($_POST["only_show_thumbnail"]))
            $only_show_thumbnail = $_POST["only_show_thumbnail"];
        mysql_query("UPDATE product_category SET category_name='{$category_name}',
				category_type='{$catsec}',
				meta_description='{$meta_description}',
				meta_keywords='{$meta_keywords}',
				visible='{$visible}',
				only_show_treeview='{$only_show_treeview}',
				only_show_thumbnail='{$only_show_thumbnail}',
				picture='{$image}' WHERE id='{$newid}'") or die("ERROR 122:" . mysql_error());
        if ($image != '')
            mysql_query("UPDATE product_category SET picture='{$image}' WHERE id='{$newid}'") or die("ERROR 123:" . mysql_error());
        if (isset($_POST["delete_image"]))
            mysql_query("UPDATE product_category SET picture='' WHERE id='{$newid}'") or die("ERROR 124:" . mysql_error());

        $info = "You have update a category";
    } elseif (isset($_POST["sm_delete_category"])) {
        mysql_query("DELETE FROM product_category WHERE id='{$parent_id}'") or die("ERROR 125:" . mysql_error());
        header("Location: /admin.php?view=categories");
        die();
    }

    $query = mysql_query("SELECT * FROM product_category WHERE category_type='{$catsec}' AND parent_id='{$parent_id}' ORDER BY ordering") or die("ERROR 126:" . mysql_error());

    $table = "";
    while ($r = mysql_fetch_object($query)) {
        $table .= '<tr>
				<td><a href="' . BASE_URL . "admin.php?view=categories&catsec=" . $catsec . "&parent_id=" . $r->id . '">' . $r->category_name . '</a></td>
				<td><input type="text" value="' . $r->ordering . '" name="ordering[]"/><input type="hidden" value="' . $r->id . '" name="ids[]"/></td>
				<td><a class="btn" href="/admin.php?view=products&catsec=' . $catsec . '&catid=' . $r->id . '">Add Product</a></td>
			</tr>';
    }

    $parent = "Root";
    if ($parent_id != 0) {
        $query = mysql_query("SELECT * FROM product_category WHERE id='{$parent_id}'") or die("ERROR 172:" . mysql_error());
        $row = mysql_fetch_object($query);
        $parent = $row->category_name;
    }

    $submenu = "Garden Idea";

    include 'view/admin/header.php';
    include 'view/admin/manage_category_header.php';
    include 'view/admin/manage_categories.php';
    include 'view/admin/footer.php';
}

function controller_LoadCSVKeywords() {
    
}

function controller_DeleteDuplicated() {
    $allcount = 0;
    do {
        $data = mysql_query("SELECT * FROM google_scraped2 GROUP BY name,merchant_name,country HAVING COUNT(*) > 1");
        $count = 0;
        while ($row = mysql_fetch_object($data)) {
            mysql_query("DELETE FROM google_scraped2 WHERE id='{$row->id}'") or die(mysql_error());
            $count++;
            $allcount++;
        }
    } while ($count > 0);

    echo "DELETED $allcount rows in google_scraped2 <br>";

    $allcount = 0;
    do {
        $data = mysql_query("SELECT * FROM google_scraped GROUP BY name,merchant_name,country HAVING COUNT(*) > 1");
        $count = 0;
        while ($row = mysql_fetch_object($data)) {
            mysql_query("DELETE FROM google_scraped WHERE id='{$row->id}'") or die(mysql_error());
            $count++;
            $allcount++;
        }
    } while ($count > 0);

    echo "DELETED $allcount rows in google_scraped <br>";
}

function AddKeyword($keyword, $category) {
    $keyword = trim(strtolower(mysql_real_escape_string($keyword)));
    $category = trim(strtolower(mysql_real_escape_string($category)));
    $query = mysql_query("SELECT * FROM keyword_category WHERE category='{$category}'");
    if (mysql_num_rows($query) > 0) {
        $row = mysql_fetch_object($query);
        $cat_id = $row->id;
    } else {
        mysql_query("INSERT INTO keyword_category SET category='{$category}'");
        $cat_id = mysql_insert_id();
    }
    $query = mysql_query("SELECT * FROM keyword WHERE keyword='{$keyword}' AND category_id='{$cat_id}'");
    if (mysql_num_rows($query) > 0) {
        $objResponse = new xajaxResponse();
        $objResponse->script("alert('query is exist');");
        return $objResponse;
    } else {
        mysql_query("INSERT INTO keyword SET keyword='{$keyword}', category_id='{$cat_id}'");
        $kid = mysql_insert_id();
    }
    $objResponse = new xajaxResponse();
    $objResponse->prepend("keyword_list", 'innerHTML', '<tr id="kw_row_' . $kid . '">
			  <td><input type="text" id="kw_' . $kid . '" value="' . $keyword . '"/></td>
			  <td>' . $category . '</td>
			  <td class="scd">Never</td>
			  <td class="scc">0</td>
			  <td>
			  <input type="button" class="btn" onClick="xajax_UpdateKeyword(' . $kid . ',$(\'#kw_' . $kid . '\').val())" value="Update" />
			  <input type="button" class="btn" onClick="xajax_DeleteKeyword(' . $kid . ')" value="Delete" />
			  <a href="/admin.php?view=google_products&category=' . urlencode($category) . '" target="_blank" class="btn">View</a></td>
			</tr>');
    return $objResponse;
}

function UpdateKeyword($kid, $keyword, $show_output = true) {
    $keyword = trim(mysql_real_escape_string($keyword));
    if (!empty($keyword)) {
        mysql_query("UPDATE keyword SET keyword='" . $keyword . "' 
			WHERE `id`='{$kid}'") or die("ERROR 150:" . mysql_error());
    }
    $query = mysql_query("SELECT * FROM keyword WHERE id='{$kid}'") or die("ERROR 151:" . mysql_error());
    $r = mysql_fetch_object($query);
    $cat_id = $r->category_id;

    $allowed_countries = array("US", "UK", "FR", "AU", "DE", "IT", "NL", "ES", "CH");
    $allowed_merchants = array();
    $query = mysql_query("SELECT * FROM allow_merchant") or die("ERROR 152:" . mysql_error());
    while ($r = mysql_fetch_object($query))
        $allowed_merchants[] = $r->merchant;

    $found_product = array();
    $keywords = explode(" ", strtolower($keyword));
    $all_results = 0;
    $found_titles = array();
    $found_urls = array();
    $all_titles = array();
    $found_merchants = array();
    $ignored_count = 0;
    $ignored_count2 = 0;
    $added_products = "";
    $allPStr = "";
    mysql_query("DELETE FROM google_scraped WHERE keyword_id='{$kid}'");

    foreach ($allowed_countries as $country) {
        $json = QueryGoogleShoppingLocal($keyword, $country);
        /* if ($json === -1)//exceed the google api limit
          {
          if ($show_output == true)
          {
          $objResponse = new xajaxResponse();
          $objResponse->script('alert("Error: Daily Limit Exceeded");');
          return $objResponse;
          }
          else
          return false;
          } */
        /* elseif ($json === 0)//exceed the google api limit
          {
          if ($show_output == true)
          {
          $results = array();
          $results["error"] = "Error: No result is found";
          header('Content-type: application/json');
          echo json_encode($results);
          die();
          }
          else
          return false;
          } */

        foreach ($json as $item) {
            $all_results++;
            $all_titles[] = $item["title"] . " (" . $item["merchant_site"] . ")";

            //if(is_domain_in_array($item["merchant_site"], $allowed_merchants)) {
            //if (true) {
            /* $product_title_keyword = $item['title'];//explode(" ",preg_replace('/[^a-zA-Z0-9]/',' ',replace_accents(strtolower(urldecode($item->product->title)))));
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
              continue; */
            if (in_array($item["title"], $found_titles) && in_array($item["merchant_name"], $found_merchants))
                continue;
            $found_titles[] = $item['title'];
            $found_urls[] = $item["merchant_site"];
            $found_merchants[] = $item["merchant_name"];
            $found_product[] = htmlentities($item["title"] . ',' . $item['merchant_name'] . ',' . $country . ',' . 'google');
            //$ID = product_already_in_db($item,$country, $cat_id);

            $added_products .= FilterScrapedProductName($item["title"]) . " (" . $country . ") - <strong>" . htmlentities($item["merchant_name"], ENT_QUOTES, "UTF-8") . "</strong> - <a target=\"_blank\" href=\"" . $item["merchant_site"] . "\">Visit Site</a><br><br>";

            $query = "INSERT IGNORE INTO google_scraped SET 
						name='" . mysql_real_escape_string($item["title"]) . "',
						picture='" . mysql_real_escape_string($item["image"]) . "',
						site_url='" . mysql_real_escape_string($item["merchant_site"]) . "',
						price='" . mysql_real_escape_string($item["price"]) . "',
						keyword_id='{$kid}',
						merchant_id='" . $item["merchant_id"] . "',
						merchant_name='" . mysql_real_escape_string($item["merchant_name"]) . "',
						country='{$country}',
						google_item_id='" . mysql_real_escape_string($item["id"]) . "',
						scraped_type='google'";
            mysql_query($query) or die("ERROR 127:" . mysql_error());

            /* }
              else
              {
              $notinmarchants .= $item["title"] . " (".$country.") - <a target=\"_blank\" href=\"".$item["merchant_site"]."\">" . $item["merchant_site"] . "</a><br><br>";
              $ignored_count++;
              } */
        }
    }

    mysql_query("UPDATE keyword SET last_update='" . time() . "',found_records='" . count($found_product) . "' WHERE id='{$kid}'") or die("ERROR 128:" . mysql_error());

    $results = array();
    $results["all_results"] = $all_results;
    $results["all_titles"] = $all_titles;
    $results["found_count"] = count($found_product);
    $results["found_titles"] = $found_titles;
    $results["error"] = "";
    //$results["delete_count"] = DeleteOutdateProducts($found_product,$keyword,$cat_id);

    $result_res = "All Scraped: " . $results["all_results"] . "<br>";
    //$result_res .= "Deleted Outdate Products: " . $results["delete_count"] . "<br>";
    $result_res .= "</center><h4>Added Products (" . count($found_product) . "):</h4></center> <br>" . $added_products . "<br>";
    //$result_res .= "</center><h4>Not in merchant list Products (".$ignored_count."):</h4></center> <br>" . $notinmarchants . "<br>";
    //$result_res .= "</center><h4>Not contained keyword (".$ignored_count2."):</h4></center> <br><div id=\"allproduct\">" . $allPStr . "</div><br>";
    $result_res = str_replace('\'', '\\\'', $result_res);

    if ($show_output == true) {
        $objResponse = new xajaxResponse();
        $objResponse->script('$("#scraped_result_box_content").html(\'' . $result_res . '\'); 
		$("#kw_row_' . $kid . ' .scd").html(\'' . date("m/d/Y h:i:s") . '\'); 
		$("#kw_row_' . $kid . ' .scc").html(\'' . count($found_product) . '\'); 
		$("#scraped_result_box").dialog({width:800,height:800});');
        return $objResponse;
    } else {
        return true;
    }
}

function product_already_in_db($product, $country, $category_id) {
    global $wpdb;
    $query = mysql_query("SELECT g.id FROM google_scraped AS g
		INNER JOIN keyword AS k ON g.keyword_id=k.id
		WHERE name='" . mysql_real_escape_string($product["title"]) . "' 
		AND merchant_name='" . mysql_real_escape_string($product["merchant_name"]) . "' 
		AND country='{$country}' 
		AND category_id='" . $category_id . "'") or die("ERROR 153:" . mysql_error());
    $r = mysql_fetch_object($query);
    $ID = @$r->id;
    if ($ID) {
        return $ID;
    } else {
        return false;
    }
}

function DeleteOutdateProducts($found, $searched_term, $category_id) {
    global $wpdb;
    $delete_count = 0;
    if (count($found) == 0)
        return 0;
    //there is no result, it may has problem with scraper. so we do not delete in that case.
    $query = mysql_query("SELECT g.* FROM google_scraped AS g
		INNER JOIN keyword AS k ON g.keyword_id=k.id
		WHERE keyword='{$searched_term}' 
		AND category_id='{$category_id}'");
    $inserted_items = array();
    //var_dump($found);
    while ($r = mysql_fetch_object($query))
        $inserted_items[] = array("id" => $r->id, "item" => strtolower($r->name . ',' . $r->merchant_name . ',' . $r->country . ',' . $r->scraped_type));

    foreach ($inserted_items as $i) {
        if (in_array($i["item"], $found) == false) {
            //print_r($i);
            mysql_query("DELETE FROM google_scraped WHERE `id` = '" . $i["id"] . "'") or die("ERROR 129:" . mysql_error());
            $delete_count++;
        }
    }
    return $delete_count;
}

function DeleteKeyword($kid) {
    $query = mysql_query("SELECT * FROM keyword WHERE id='{$kid}'");
    $row = mysql_fetch_object($query);
    $kid = $row->id;
    $cat_id = $row->category_id;
    mysql_query("DELETE FROM keyword WHERE id='{$kid}'");
    mysql_query("DELETE FROM google_scraped WHERE keyword_id='{$kid}'");
    $query = mysql_query("SELECT * FROM keyword WHERE category_id='{$cat_id}'");
    if (mysql_num_rows($query) == 0) {
        mysql_query("DELETE FROM keyword_category WHERE id='{$cat_id}'");
    }
    $objResponse = new xajaxResponse();
    $objResponse->assign("kw_row_" . $kid, 'innerHTML', '');
    return $objResponse;
}

function SearchAll() {
    $query = mysql_query("SELECT * FROM keyword ORDER BY last_update");
    $done = "";
    while ($r = mysql_fetch_object($query)) {
        if (UpdateKeyword($r->id, $r->keyword, false) == false) {
            $objResponse = new xajaxResponse();
            $objResponse->script(" alert('you ran exceed the allowance today.'); $('#smSearchAll').show(); $('#smSearchAllwait').hide();");
            return $objResponse;
        }
        $done .= $r->keyword . ", ";
    }
    $objResponse = new xajaxResponse();
    $objResponse->script(" alert('finished running queries'); alert('Done: " . trim($done, ",") . "'); $('#smSearchAll').show(); $('#smSearchAllwait').hide();");
    return $objResponse;
}

function controller_google_keywords() {
    require ("lib/xajax/xajax_core/xajax.inc.php");
    $xajax = new xajax();
    $xajax->configure('javascript URI', 'lib/xajax/');
    $xajax->register(XAJAX_FUNCTION, "AddKeyword");
    $xajax->register(XAJAX_FUNCTION, "UpdateKeyword");
    $xajax->register(XAJAX_FUNCTION, "DeleteKeyword");
    $xajax->register(XAJAX_FUNCTION, "SearchAll");
    $xajax->processRequest();

    $keywords = mysql_query("SELECT DISTINCT k.id AS keyword_id,k.*,kc.*,k.found_records AS nbproducts FROM keyword AS k
		INNER JOIN keyword_category AS kc ON k.category_id=kc.id
		LEFT JOIN google_scraped AS gs ON gs.keyword_id=k.id
		GROUP BY k.id
		ORDER BY category");

    include 'view/admin/header.php';
    include 'view/admin/google_menu.php';
    include 'view/admin/manage_googlescraper.php';
    include 'view/admin/footer.php';
}

function controller_google_merchants() {
    if (isset($_GET["delete_id"])) {
        $did = strtolower(trim($_GET["delete_id"]));
        $data = mysql_query("SELECT * FROM allow_merchant WHERE id='{$did}'") or die("ERROR 1232009:" . mysql_error());
        $row = mysql_fetch_object($data);
        mysql_query("DELETE FROM google_scraped2 WHERE merchant_id='{$row->id}'") or die("ERROR 1232009:" . mysql_error());
        mysql_query("DELETE FROM allow_merchant WHERE id='{$did}'") or die("ERROR 1232009:" . mysql_error());
    }
    if (isset($_POST["smAddMerchant"])) {
        $merchant = strtolower(trim($_POST["merchant"]));
        $country = strtolower(trim($_POST["country"]));
        $seller_id = strtolower(trim($_POST["seller_id"]));
        if (isset($_GET["edit_id"])) {
            mysql_query("UPDATE allow_merchant SET merchant='{$merchant}', seller_id='{$seller_id}', country='{$country}' WHERE id='" . $_GET["edit_id"] . "'");
            header("Location: /admin.php?view=google_merchants");
            exit;
        } else
            mysql_query("INSERT IGNORE INTO allow_merchant SET merchant='{$merchant}', seller_id='{$seller_id}', country='{$country}'");
    }
    if (isset($_GET["edit_id"])) {
        $data = mysql_query("SELECT * FROM allow_merchant WHERE id='" . $_GET["edit_id"] . "'");
        $m = mysql_fetch_object($data);
    } else
        $merchants = mysql_query("SELECT * FROM allow_merchant WHERE username is NULL ORDER BY last_scraped DESC");
    include 'view/admin/header.php';
    include 'view/admin/google_menu.php';
    include 'view/admin/manage_google_merchants.php';
    include 'view/admin/footer.php';
}

function controller_google_products() {
    $category = "";
    $kid = "";
    $keyword = "";
    $merchant = "";
    if (isset($_GET["category"]))
        $category = $_GET["category"];
    $category = mysql_real_escape_string(strtolower(trim($category)));
    if (isset($_GET["kid"]))
        $kid = $_GET["kid"];
    $kid = mysql_real_escape_string(strtolower(trim($kid)));
    if (isset($_GET["keyword"]))
        $keyword = $_GET["keyword"];
    $keyword = mysql_real_escape_string(strtolower(trim($keyword)));
    if (isset($_GET["merchant"]))
        $merchant = $_GET["merchant"];
    $merchant = htmlspecialchars(html_entity_decode($merchant));
    $merchant = mysql_real_escape_string(strtolower(trim($merchant)));
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

    $where = "";
    if ($merchant != '')
        $where .= " AND merchant_name LIKE '%{$merchant}%'";
    if ($keyword != '')
        $where .= " AND name LIKE '%{$keyword}%'";
    if ($where != '')
        $data_scraped2 = mysql_query("SELECT * FROM google_scraped2
				WHERE 1=1 {$where}");

    $merchants = array();
    $mdata = mysql_query("SELECT DISTINCT merchant_name FROM google_scraped ORDER BY merchant_name");
    while ($r = mysql_fetch_object($mdata))
        $merchants[] = $r->merchant_name;
    $mdata = mysql_query("SELECT DISTINCT merchant_name FROM google_scraped2 ORDER BY merchant_name");
    while ($r = mysql_fetch_object($mdata))
        if (in_array($r->merchant_name, $merchants) == false)
            $merchants[] = $r->merchant_name;

    include 'view/admin/header.php';
    include 'view/admin/google_menu.php';
    include 'view/admin/manage_google_products.php';
    include 'view/admin/footer.php';
}

function controller_reports() {
    include 'view/admin/header.php';
    include 'view/admin/report_header.php';
    include 'view/admin/footer.php';
}

function controller_pricing_report() {
    // if (isset($_REQUEST["data"])) {
    // header('Content-type: application/json');
    // $data = mysql_query("SELECT COUNT(*) AS counter FROM product_category");
    // $temp = mysql_fetch_object($data);
    // $iTotal = $temp -> counter;
    // $columns[0] = "category_name";
    // $columns[1] = "us_count";
    // $columns[2] = "uk_count";
    // $columns[3] = "fr_count";
    // $columns[4] = "in_post";

    $where = "";
    $order = "";
    if (isset($_GET[sSearch]))
        $where = " AND category_name LIKE '%{$_GET[sSearch]}%' ";
    if (isset($_GET[iSortCol_0]))
        $order = " ORDER BY " . $columns[$_GET[iSortCol_0]] . " " . $_GET[sSortDir_0];

    $returned_data = array();
    $data = mysql_query("SELECT * FROM keyword_category WHERE 1=1 $where $order");
    // LIMIT $_GET[iDisplayStart],$_GET[iDisplayLength]");
    while ($row = mysql_fetch_object($data)) {
        $data2 = mysql_query("SELECT COUNT(*) AS counter FROM google_scraped AS gs 
				INNER JOIN keyword AS k ON gs.keyword_id=k.id
				WHERE gs.country='us' AND k.`category_id`=" . $row->id);
        $us_counter_row = mysql_fetch_object($data2);

        $data2 = mysql_query("SELECT COUNT(*) AS counter FROM google_scraped AS gs 
				INNER JOIN keyword AS k ON gs.keyword_id=k.id
				WHERE gs.country='uk' AND k.`category_id`=" . $row->id);
        $uk_counter_row = mysql_fetch_object($data2);

        $data2 = mysql_query("SELECT COUNT(*) AS counter FROM google_scraped AS gs 
				INNER JOIN keyword AS k ON gs.keyword_id=k.id
				WHERE gs.country='fr' AND k.`category_id`=" . $row->id);
        $fr_counter_row = mysql_fetch_object($data2);

        $data2 = mysql_query("SELECT * FROM product_seller_item AS psi
			INNER JOIN keyword_category AS kc ON kc.id=psi.keyword_category_id
			WHERE kc.id={$row->id} LIMIT 1");
        $row2 = mysql_fetch_object($data2);
        $inpost = "No";
        if ($row2)
            $inpost = "Yes";
        $returned_data[] = array($row->category, $us_counter_row->counter, $uk_counter_row->counter, $fr_counter_row->counter, $inpost);
    }

    // $data = mysql_query("SELECT COUNT(*) AS counter WHERE 1=1 $where FROM product_category");
    // $temp = mysql_fetch_object($data);
    // if ($temp)
    // $iFilteredTotal = $temp -> counter;
    // else
    // $iFilteredTotal = 0;
    // $output = array("sEcho" => intval($_GET['sEcho']), "iTotalRecords" => $iTotal, "iTotalDisplayRecords" => $iFilteredTotal, "aaData" => $returned_data);
    //die(json_encode($output));
    // }
    include 'view/admin/header.php';
    include 'view/admin/report_header.php';
    include 'view/admin/pricing_report.php';
    include 'view/admin/footer.php';
}

function controller_companions_report() {

    $data = mysql_query("SELECT * FROM product");
    // LIMIT $_GET[iDisplayStart],$_GET[iDisplayLength]");
    while ($row = mysql_fetch_object($data)) {
        $data2 = mysql_query("SELECT p.* FROM product AS p,related_product AS rp 
			WHERE rp.product_id=" . $row->id . " AND p.id=rp.related_id ORDER BY p.name");
        $bestCompanions = "";
        while ($row2 = mysql_fetch_object($data2))
            $bestCompanions .= '<a href="' . url_product_detail($row2->id) . '" target="_blank">' . $row2->name . '</a><br>';


        $data2 = mysql_query("SELECT p.* FROM product AS p,related_product AS rp 
			WHERE rp.related_id=" . $row->id . " AND p.id=rp.product_id ORDER BY p.name");
        $bestCompanions2 = "";
        while ($row2 = mysql_fetch_object($data2))
            $bestCompanions2 .= '<a href="' . url_product_detail($row2->id) . '" target="_blank">' . $row2->name . '</a><br>';


        $returned_data[] = array('<a href="' . url_product_detail($row->id) . '" target="_blank">' . $row->name . '</a>', $bestCompanions, $bestCompanions2);
    }

    include 'view/admin/header.php';
    include 'view/admin/report_header.php';
    include 'view/admin/companions_report.php';
    include 'view/admin/footer.php';
}

function controller_review_check() {
    if (isset($_GET["id"])) {
        $id = $_GET["id"];
        $status = $_GET["status"];
        mysql_query("UPDATE user_rating SET status='{$status}' WHERE id='{$id}'");

        $data = mysql_query("SELECT * FROM user_rating WHERE id='{$id}'");
        $r = mysql_fetch_object($data);
        $pid = $r->product_id;

        $data = mysql_query("SELECT COUNT(*) AS counter FROM user_rating WHERE product_id='{$pid}' GROUP BY product_id");
        $row = mysql_fetch_object($data);
        $count = $row->counter;

        $data = mysql_query("SELECT AVG(rate) AS avg FROM user_rating WHERE product_id='{$pid}' GROUP BY product_id");
        $row = mysql_fetch_object($data);
        $avg = $row->avg;

        mysql_query("UPDATE product SET rating='{$avg}'
			, rating_count = '{$count}' WHERE id={$pid}");
        header("Location: /admin.php?view=review_check");
        exit;
    }
    $paging_page = isset($_GET["paging_page"]) ? $_GET["paging_page"] : 1;
    $paging_pagesize = 50;
    $paging_q = "SELECT COUNT(*) AS counter FROM user_rating WHERE status='pending'";

    $q = "SELECT * FROM user_rating WHERE status = 'pending'
		LIMIT " . (($paging_page - 1) * $paging_pagesize) . "," . ($paging_pagesize);

    $reviews = mysql_query($q);
    include 'view/admin/header.php';
    include 'view/admin/report_header.php';
    include 'view/admin/report_review_check.php';
    include 'view/admin/footer.php';
}

function controller_report_missing_price() {
    $paging_page = isset($_GET["paging_page"]) ? $_GET["paging_page"] : 1;
    $paging_pagesize = 50;
    $paging_q = "SELECT COUNT(*) AS counter FROM product AS p2 WHERE p2.id NOT IN (SELECT p.id FROM product AS p 
		INNER JOIN product_seller_item AS psi ON psi.product_id=p.id
		INNER JOIN keyword_category AS kc ON kc.id=psi.keyword_category_id
		INNER JOIN keyword AS k ON k.category_id=kc.id
		INNER JOIN google_scraped AS gs ON gs.keyword_id=k.id)";

    $q = "SELECT p2.* FROM product AS p2 WHERE p2.id NOT IN (SELECT p.id FROM product AS p 
		INNER JOIN product_seller_item AS psi ON psi.product_id=p.id
		INNER JOIN keyword_category AS kc ON kc.id=psi.keyword_category_id
		INNER JOIN keyword AS k ON k.category_id=kc.id
		INNER JOIN google_scraped AS gs ON gs.keyword_id=k.id) 
		ORDER BY p2.created_date ASC
		LIMIT " . (($paging_page - 1) * $paging_pagesize) . "," . ($paging_pagesize);
    //echo $q;exit;
    $products = mysql_query($q);
    include 'view/admin/header.php';
    include 'view/admin/report_header.php';
    include 'view/admin/report_missing_price.php';
    include 'view/admin/footer.php';
}

function controller_vendors() {
    $action = filter_input(INPUT_GET, "action", FILTER_SANITIZE_STRING);
    $id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);
    if (isset($action) && ($action == 'approved' || $action == 'banned' || $action == 'deleted'))
    {
        mysql_query("UPDATE allow_merchant SET status='{$action}' WHERE id='{$id}'");
        $emailContent = file_get_contents("static/email_templates/status_changed_{$action}.html");
        if (!empty($emailContent))
        {
            $data = mysql_query("SELECT * FROM allow_merchant WHERE id='{$id}'");
            $row = mysql_fetch_object($data);
            $emailContent = str_replace("##FirstName##", $row->first_name, $emailContent);
            $emailContent = str_replace("##LastName##", $row->last_name, $emailContent);
            $emailContent = str_replace("##status##", $action, $emailContent);
            $emailContent = str_replace("##Login##", $row->username, $emailContent);
            $emails = array();
            $emails[] = array("email" => ADMIN_EMAIL,
                "name" => "Jardins Sans Secret");
            $emails[] = array("email" => $row->email,
                "name" => "{$row->first_name} {$row->last_name}");
			if ($action == 'approved')
            	$ok = send_email_mandrillapp(MANDRILLAPP_API, "Jardins Sans Secret", ADMIN_EMAIL, "Jardins Sans Secret - Welcome to Jardins Sans Secret!",
                	    $emails, $emailContent);
			elseif ($action == 'banned')
				$ok = send_email_mandrillapp(MANDRILLAPP_API, "Jardins Sans Secret", ADMIN_EMAIL, "Jardins Sans Secret - Registration Temporarily Suspended",
                	    $emails, $emailContent);
			elseif ($action == 'deleted')
			{
				$ok = send_email_mandrillapp(MANDRILLAPP_API, "Jardins Sans Secret", ADMIN_EMAIL, "Jardins Sans Secret - Registration Cancellation",
			   	    $emails, $emailContent);
            	
		        $deleted_username = $row->username . "_deleted_" . uniqid();
		        mysql_query("UPDATE allow_merchant SET username='{$deleted_username}', email='{$deleted_username}' WHERE id='{$id}'");
			}
            if ($ok != 'sent')
                die ("Error 381119");
        }
        header("Location: /admin.php?view=vendors&info=" . urlencode("User's status is updated to <strong>{$action}</strong>"));
        die();
    }
    $info = $_GET["info"];
    $dataVendors = mysql_query("SELECT (SELECT COUNT(*) FROM google_scraped AS gs "
            . "WHERE gs.merchant_id=am.id) no_prod,am.* FROM allow_merchant AS am "
            . "WHERE am.status <> 'deleted' AND am.username IS NOT NULL");
    include 'view/admin/header.php';
    include 'view/admin/vendors.php';
    include 'view/admin/footer.php';
}

function controller_report_missing_companion() {
    $paging_page = isset($_GET["paging_page"]) ? $_GET["paging_page"] : 1;
    $paging_pagesize = 50;
    $paging_q = "SELECT COUNT(*) AS counter FROM product AS p2 WHERE p2.id NOT IN (SELECT p.id FROM product AS p 
		INNER JOIN related_product AS pr ON pr.product_id=p.id)";

    $q = "SELECT p2.* FROM product AS p2 WHERE p2.id NOT IN (SELECT p.id FROM product AS p 
		INNER JOIN related_product AS pr ON pr.product_id=p.id) 
		ORDER BY p2.created_date ASC
		LIMIT " . (($paging_page - 1) * $paging_pagesize) . "," . ($paging_pagesize);
    //echo $q;exit;
    $products = mysql_query($q);

    include 'view/admin/header.php';
    include 'view/admin/report_header.php';
    include 'view/admin/report_missing_companion.php';
    include 'view/admin/footer.php';
}

function QueryGoogleApi($q, $country) {
    $q = urlencode($q);
    $api_key = "AIzaSyBj8dN80ZMDigr2aoGWbpIU1ur-730JA2s";
    $searched_url = "https://www.googleapis.com/shopping/search/v1/public/products?key=" . $api_key . "&country=" . $country . "&q=" . $q . "&maxResults=300&alt=json";
    $query_code = md5($searched_url);
    $query = mysql_query("SELECT scraped_data FROM scraped_cache WHERE code='{$query_code}' AND updated_date > " . (time() - 259200)) or die("ERROR 130:" . mysql_error());
    if (mysql_num_rows($query) > 0) {
        $row = mysql_fetch_object($query);
        return $row->scraped_data;
    }
    $json = file_get_contents($searched_url);
    /* mysql_query("DELETE FROM scraped_cache WHERE code='{$query_code}'") or die (mysql_error());//we don't want a duplicated cache
      mysql_query("INSERT INTO scraped_cache SET
      updated_date='".time()."',
      code='".$query_code."',
      scraped_data='".mysql_real_escape_string($json)."'") or die (mysql_error()); */
    return $json;
}

function controller_TestQueryGoogleShopping() {
    $q = $_GET["q"];
    $c = $_GET["c"];
    QueryGoogleShoppingLocal($q, $c, true);
}

function QueryGoogleShoppingLocal($q, $country, $debug = false) {
    $q = preg_replace('/[^0-9a-zA-Z:"]/is', " ", $q);
    $q = str_replace(" ", "%", $q);
    $country = strtolower($country);
    mysql_query("SET NAMES 'utf8'");
    //echo "SELECT * FROM google_scraped2 WHERE name LIKE '%{$q}%' AND country='{$country}'";
    $data = mysql_query("SELECT * FROM google_scraped2 WHERE name LIKE '%{$q}%' AND country='{$country}'");
    while ($row = mysql_fetch_object($data)) {
        $res[] = array("image" => $row->picture, "title" => $row->name, "price" => $row->price, "country" => $row->country, "merchant_name" => $row->merchant_name, "merchant_id" => $row->merchant_id, "merchant_site" => $row->site_url, "id" => $row->google_item_id);
    }
    if ($debug)
        var_dump($res);
    return $res;
}

function QueryGoogleShopping($q, $country, $debug = false) {
    $q = preg_replace('/[^0-9a-zA-Z:"]/is', " ", $q);
    $q = urlencode($q);
    $domain = ".com";
    switch ($country) {
        case "US" :
            $domain = ".com";
            break;
        case "UK" :
            $domain = ".co.uk";
            break;
        case "FR" :
            $domain = ".fr";
            break;
        case "AU" :
            $domain = ".com.au";
            break;
        case "DE" :
            $domain = ".de";
            break;
        case "IT" :
            $domain = ".it";
            break;
        case "NL" :
            $domain = ".nl";
            break;
        case "ES" :
            $domain = ".es";
            break;
        case "CH" :
            $domain = ".ch";
            break;
    }
    $domain = "https://www.google{$domain}";
    $searched_url = "{$domain}/search?num=100&biw=1021&bih=498&tbm=shop&q={$q}";

    //get cache
    $query_code = md5($searched_url);
    $query = mysql_query("SELECT scraped_data FROM scraped_cache WHERE code='{$query_code}' AND updated_date > " . (time() - 259200)) or die("ERROR 131:" . mysql_error());
    if (mysql_num_rows($query) > 0) {
        $row = mysql_fetch_object($query);
        $content = urldecode($row->scraped_data);
    } else {
        $tuCurl = curl_init();
        curl_setopt($tuCurl, CURLOPT_URL, $searched_url);
        curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.17 Safari/537.36');
        $content = curl_exec($tuCurl);
        $responsecode = curl_getinfo($tuCurl, CURLINFO_HTTP_CODE);
        curl_close($tuCurl);
        if ($responsecode == 503 || $responsecode == 302)
            return -1;

        //save cache
        mysql_query("DELETE FROM scraped_cache WHERE code='{$query_code}'") or die("ERROR 154:" . mysql_error());
        //we don't want a duplicated cache
        mysql_query("INSERT INTO scraped_cache SET 
			updated_date='" . time() . "',
			code='" . $query_code . "',
			responsecode='" . $responsecode . "',
			scraped_data='" . urlencode($content) . "'") or die("ERROR 155:" . mysql_error());
    }
    //var_dump($content);exit;

    if ($debug)
        echo '<pre>' . $content . '</pre>';

    $res = array();
    preg_match_all('/<li class="g psli">.*?(?=<\/li>)<\/li>/is', $content, $blocks);
    if (count($blocks[0]) == 0)
        preg_match_all('/<li class="g">.*?(?=<\/li>)<\/li>/is', $content, $blocks);
    if (count($blocks[0]) == 0)
        return 0;
    foreach ($blocks[0] as $block) {
        preg_match('/<img\s+.*?(?=src)src="([^"]*)"/is', $block, $match);
        $image = $match[1];
        preg_match('/alt="([^"]*)"/is', $block, $match);
        $title = html_entity_decode($match[1]);
        $title = preg_replace('/<[^>]*>/is', "", $title);
        $title = RemoveUnreadableChars($title);
        $title = htmlentities($title);
        preg_match('/"psliprice">(.*?(?=<\/div>))/is', $block, $match);
        preg_match('/[0-9\.]+/is', $match[1], $match);
        $price = $match[0];
        preg_match('/<cite>([^<]*)/is', $block, $match);
        $merchant_name = $match[1];
        preg_match('/<a href="(\/aclk\?[^"]*)/is', $block, $match);
        preg_match('/adurl=([^"]*)/is', $match[1], $match);
        if ($match[1] == "")
            preg_match('/href="([^"]*)/is', $block, $match);
        $merchant_site = $match[1];
        preg_match('/;adurl=([^"&]*)/is', $block, $match);
        $description = $match[1];
        preg_match('/srpresultimg_([0-9]*)/is', $block, $match);
        $id = $match[1];

        $res[] = array("image" => $image, "title" => $title, "price" => $price, "country" => $country, "merchant_name" => $merchant_name, "merchant_site" => $merchant_site, "id" => md5($title));
        //echo $block;var_dump($res);exit;
    }
    if ($debug)
        var_dump($res);
    return $res;
}

function controller_products() {
    if (isset($_GET["edit_id"]))
        $product_id = $_GET["edit_id"];
    if (isset($_GET["similar_id"]))
        $product_id = $_GET["similar_id"];

    if (isset($_GET["delete_id"])) {
        $product_id = mysql_real_escape_string($_GET["delete_id"]);
        mysql_query("DELETE FROM product WHERE id='{$product_id}'") or die("ERROR 156:" . mysql_error());
        header("Location: /admin.php?view=products");
        die();
    }

    if (isset($_POST["smAddProduct"]) || isset($_POST["smEditProduct"])) {
        $title = mysql_real_escape_string(htmlentities(utf8_encode($_POST["title"])));
        $description = mysql_real_escape_string($_POST["description"]);
        $radio_product_type = mysql_real_escape_string($_POST["radio_product_type"]);
        $radio_publish = mysql_real_escape_string($_POST["radio_publish"]);
        $category = urlencode(mysql_real_escape_string($_POST["category_" . $radio_product_type]));
        if (isset($_POST["smAddProduct"])) {
            mysql_query("INSERT INTO product SET
				name='{$title}',
				description='{$description}',
				status='{$radio_publish}'") or die("ERROR 157:" . mysql_error());
            $product_id = mysql_insert_id();
        } elseif (isset($_POST["smEditProduct"])) {
            mysql_query("UPDATE product SET
				name='{$title}',
				description='{$description}',
				status='{$radio_publish}',
				status='{$radio_publish}'
				WHERE id='{$product_id}'") or die("ERROR 158:" . mysql_error());
        }
    }

    if (isset($product_id) && (isset($_POST["smAddProduct"]) || isset($_POST["smEditProduct"]))) {
        //set categories
        mysql_query("DELETE FROM product_in_category WHERE product_id='{$product_id}'") or die("ERROR 132:" . mysql_error());
        foreach ($_POST["categories"] as $c) {
            if ($c != '')
                mysql_query("INSERT INTO product_in_category SET product_id='{$product_id}', category_id='" . mysql_real_escape_string($c) . "'") or die("ERROR 103:" . mysql_error());
        }

        $seller_categories = $_POST["seller_categories"];
        mysql_query("DELETE FROM product_seller_item WHERE product_id='{$product_id}'") or die("ERROR 159:" . mysql_error());
        foreach ($seller_categories as $i) {
            if ($i != '')
                mysql_query("INSERT INTO product_seller_item SET product_id='{$product_id}', keyword_category_id='{$i}'") or die("ERROR 160:" . mysql_error());
        }

        mysql_query("DELETE FROM related_product WHERE product_id='{$product_id}'") or die("ERROR 161:" . mysql_error());
        $related_products = $_POST["related_products"];
        foreach ($related_products as $i) {
            if ($i != '')
                mysql_query("INSERT INTO related_product SET product_id='{$product_id}', related_id='{$i}', related_type='seller'") or die("ERROR 162:" . mysql_error());
        }

        $data_old = mysql_query("SELECT * FROM product WHERE id='{$product_id}'") or die("ERROR 134:" . mysql_error());
        $old_info = mysql_fetch_object($data_old);
        $mainPic = $old_info->main_picture;
        if (isset($_FILES["main_picture"]["name"]) && $_FILES["main_picture"]["name"] != '') {
            $temp = pathinfo($_FILES["main_picture"]["name"]);
            @unlink('upload/product_images/' . $mainPic);
            $mainPic = 'main_picture_' . uniqid() . "." . $temp["extension"];
            $pic_location = 'upload/product_images/' . $mainPic;
            move_uploaded_file($_FILES["main_picture"]["tmp_name"], $pic_location);
        }
        if (isset($_POST["delete_image"])) {
            $mainPic = '';
        }

        $keywords = $_POST["seo_keyword"];
        if (trim($keywords) == "") {
            include "lib/AlchemyAPI/AlchemyAPI.php";
            $alchemyObj = new AlchemyAPI();
            $alchemyObj->setAPIKey(ALCHEMY_API);
            $plContent = preg_replace('/<[^>]*>/is', ' ', $description);
            $plContent = preg_replace('/\[[^\]]*\]/is', ' ', $plContent);
            $result = $alchemyObj->TextGetRankedKeywords($plContent, AlchemyAPI::JSON_OUTPUT_MODE);
            $res = array();
            $json = json_decode($result);
            foreach ($json->keywords as $k) {
                $res[] = trim($k->text);
            }
            $keywords = implode(", ", $res);
        }
        mysql_query("UPDATE product SET main_picture='{$mainPic}',
			calculator_name='" . mysql_real_escape_string($_POST["calculator_name"]) . "',
			calculator_size='" . mysql_real_escape_string($_POST["calculator_size"]) . "',
			requirement_id='" . mysql_real_escape_string($_POST["requirement"]) . "',
			map_address='" . mysql_real_escape_string($_POST["map"]) . "',
			contact_form_id='" . mysql_real_escape_string($_POST["contact"]) . "',
			wider_content='" . mysql_real_escape_string($_POST["wider_content"]) . "',
			product_tabs='" . mysql_real_escape_string($_POST["product_tabs"]) . "',
			isfeatured='" . mysql_real_escape_string($_POST["radio_feature"]) . "',
			common_name='" . mysql_real_escape_string(htmlentities(utf8_encode($_POST["common_name"]))) . "',
			publishing_date='" . mysql_real_escape_string(strtotime($_POST["publishing_date"])) . "',
			intro='" . mysql_real_escape_string(htmlentities(utf8_encode($_POST["intro"]))) . "',
			seo_keyword='" . mysql_real_escape_string($keywords) . "',
			last_modified=NOW(),
			image_alt='" . mysql_real_escape_string(htmlentities(utf8_encode($_POST["image_alt"]))) . "',
			seo_description='" . mysql_real_escape_string($_POST["seo_description"]) . "',
			care_id='" . mysql_real_escape_string($_POST["care"]) . "'
			WHERE id='{$product_id}'") or die("ERROR 163:" . mysql_error());
        for ($i = 0; $i < count(@$_FILES["photo"]["name"]); $i++) {
            if ($_FILES["photo"]["name"][$i] == "")
                continue;
            $temp = pathinfo($_FILES["photo"]["name"][$i]);
            $mainPic = uniqid() . "." . $temp["extension"];
            $pic_location = 'upload/product_images/' . $mainPic;
            mysql_query("INSERT INTO product_images SET name='{$mainPic}', 
				description='" . mysql_real_escape_string($_POST["photo_desc"][$i]) . "', 
				caption='" . mysql_real_escape_string($_POST["photo_caption"][$i]) . "', 
				alter_text='" . mysql_real_escape_string($_POST["photo_altertext"][$i]) . "', 
				product_id='{$product_id}'") or die("ERROR 100:" . mysql_error());
            move_uploaded_file($_FILES["photo"]["tmp_name"][$i], $pic_location);
        }

        for ($i = 0; $i < count($_POST["delete_photo"]); $i++) {
            mysql_query("DELETE FROM product_images 
				WHERE id='" . mysql_real_escape_string($_POST["delete_photo"][$i]) . "'") or die("ERROR 101:" . mysql_error());
        }

        for ($i = 0; $i < count($_POST["old_photo_id"]); $i++) {
            mysql_query("UPDATE product_images SET 
				description='" . mysql_real_escape_string($_POST["old_photo_desc"][$i]) . "', 
				ordering='" . mysql_real_escape_string($_POST["ordering"][$i]) . "', 
				caption='" . mysql_real_escape_string($_POST["old_photo_caption"][$i]) . "', 
				alter_text='" . mysql_real_escape_string($_POST["old_photo_altertext"][$i]) . "' 
				WHERE id='" . mysql_real_escape_string($_POST["old_photo_id"][$i]) . "'") or die("ERROR 102:" . mysql_error());
        }

        if (isset($_POST["delete_reward_icon"]))
            mysql_query("UPDATE product SET reward_icon='' WHERE id='{$product_id}'");
        elseif (isset($_FILES["reward_icon"]["name"]) && $_FILES["reward_icon"]["name"] != '') {
            $temp = pathinfo($_FILES["reward_icon"]["name"]);
            $mainPic = 'award_icon_' . $product_id . "." . $temp["extension"];
            $pic_location = 'upload/product_images/' . $mainPic;
            @unlink($pic_location);
            move_uploaded_file($_FILES["reward_icon"]["tmp_name"], $pic_location);
            mysql_query("UPDATE product SET reward_icon='{$mainPic}' WHERE id='{$product_id}'");
        }

        $datarlp = mysql_query("SELECT * FROM product WHERE status=1");

        header("Location: /admin.php?view=products&msg=s&edit_id={$product_id}");
        die();
    }

    $data_categories = mysql_query("SELECT * FROM keyword_category ORDER BY category") or die("ERROR 164:" . mysql_error());
    $option_gi = "";
    $option_pl = "";

    $q = mysql_query("SELECT * FROM product_category WHERE category_type='gi' AND parent_id=0") or die("ERROR 165:" . mysql_error());
    while ($r = mysql_fetch_object($q)) {
        $option_gi .= '<option value="' . $r->id . '">' . $r->category_name . '</option>';
        $q2 = mysql_query("SELECT * FROM product_category WHERE category_type='gi' AND parent_id='{$r->id}'") or die("ERROR 166:" . mysql_error());
        while ($r2 = mysql_fetch_object($q2)) {
            $option_gi .= '<option value="' . $r2->id . '">---' . $r2->category_name . '</option>';
        }
    }

    $q = mysql_query("SELECT * FROM product_category WHERE category_type='pl' AND parent_id=0") or die("ERROR 167:" . mysql_error());
    while ($r = mysql_fetch_object($q)) {
        $option_pl .= '<option value="' . $r->id . '">' . $r->category_name . '</option>';
        $q2 = mysql_query("SELECT * FROM product_category WHERE category_type='pl' AND parent_id='{$r->id}'") or die("ERROR 168:" . mysql_error());
        while ($r2 = mysql_fetch_object($q2)) {
            $option_pl .= '<option value="' . $r2->id . '">---' . $r2->category_name . '</option>';
        }
    }

    if (isset($product_id)) {
        $data_sellers = mysql_query("SELECT * FROM product_seller_item WHERE product_id='{$product_id}'") or die("ERROR 104:" . mysql_error());
        $data_product_info = mysql_query("SELECT * FROM product WHERE id='{$product_id}'") or die("ERROR 104:" . mysql_error());
        $product_info = mysql_fetch_object($data_product_info);
        $data_photos = mysql_query("SELECT * FROM product_images WHERE product_id='{$product_id}'") or die("ERROR 104:" . mysql_error());
        $data_catagory = mysql_query("SELECT * FROM product_in_category WHERE product_id='{$product_id}'") or die("ERROR 104:" . mysql_error());
        $relatedproducts = mysql_query("SELECT * FROM related_product WHERE product_id='{$product_id}'") or die("ERROR 104:" . mysql_error());
    }

    if (isset($_GET["similar_id"]))
        $product_info->status = 0;

    $data_requirement = mysql_query("SELECT * FROM requirement ORDER BY name") or die("ERROR 105:" . mysql_error());
    $data_care = mysql_query("SELECT * FROM form WHERE content_type='care' ORDER BY name") or die("ERROR 106:" . mysql_error());
    $data_contact = mysql_query("SELECT * FROM form WHERE content_type='contact' ORDER BY name") or die("ERROR 107:" . mysql_error());

    include 'view/admin/header.php';
    include 'view/admin/products_menu.php';
    include 'view/admin/edit_products.php';
    include 'view/admin/footer.php';
}

function controller_products_list() {
    $where = "";
//    if (isset($_GET["searchKw"])) {
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
//    }

    if (isset($_POST["smBulkUpdate"]) && count($_POST["selectedid"]) > 0) {
        foreach ($_POST["selectedid"] as $id) {
            if (count($_POST["categories"]) > 0 && $_POST["category_edit_type"] == "replace")
                mysql_query("DELETE FROM product_in_category WHERE product_id='{$id}'") or die("ERROR 108:" . mysql_error());
            foreach ($_POST["categories"] as $c) {
                if ($c != '') {
                    $datacount = mysql_query("SELECT * FROM product_in_category WHERE product_id='{$id}' AND category_id='" . mysql_real_escape_string($c) . "'") or die("ERROR 109:" . mysql_error());
                    ;
                    if (mysql_num_rows($datacount) == 0)
                        mysql_query("INSERT INTO product_in_category SET product_id='{$id}', category_id='" . mysql_real_escape_string($c) . "'") or die("ERROR 110:" . mysql_error());
                }
            }
            if ($_POST["radio_publish"] != '')
                mysql_query("UPDATE product SET status='" . $_POST["radio_publish"] . "' WHERE id='{$id}'") or die("ERROR 111:" . mysql_error());
        }
    }

    $sort_field = isset($_GET["sort_field"]) ? $_GET["sort_field"] : "created_date";
    $sort_type = isset($_GET["sort_type"]) ? $_GET["sort_type"] : "desc";

    if ($_GET["category"] == "") {
        $q = "SELECT * FROM `product` AS p WHERE p.id NOT IN (SELECT product_id FROM product_in_category AS pc)
		{$where} ORDER BY $sort_field $sort_type";
    } else {
        $q = "SELECT distinct p.* FROM product AS p 
				INNER JOIN product_in_category AS pic ON p.id = pic.product_id
				INNER JOIN product_category AS pc ON pc.id = pic.category_id
				WHERE pc.category_type='" . $_GET["category"] . "' {$where} 
				ORDER BY $sort_field $sort_type";
    }
    //echo $q;exit;
    $data_list = mysql_query($q) or die("ERROR 169:" . mysql_error());
    $total = mysql_num_rows($data_list);

    require ("lib/xajax/xajax_core/xajax.inc.php");
    $xajax = new xajax();
    $xajax->configure('javascript URI', 'lib/xajax/');
    $xajax->register(XAJAX_FUNCTION, "DeleteProductFromList");
    $xajax->processRequest();

    include 'view/admin/header.php';
    include 'view/admin/products_menu.php';
    include 'view/admin/products_list.php';
    include 'view/admin/footer.php';
}

function DeleteProductFromList($id) {
    $id = mysql_real_escape_string($id);
    mysql_query("DELETE FROM product WHERE id='{$id}'");
}

function controller_forms() {
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

function controller_forms_edit() {
    $type = "req";
    if (isset($_GET["type"]))
        $type = mysql_real_escape_string($_GET["type"]);

    if (isset($_GET["delete_id"])) {
        $delete_id = mysql_real_escape_string($_GET["delete_id"]);
        if ($type == "req") {
            mysql_query("DELETE FROM requirement WHERE id='{$delete_id}'");
        } else {
            mysql_query("DELETE FROM form WHERE id='{$delete_id}'");
        }
        header("Location: admin.php?view=forms&type={$type}");
        die();
    }

    if (isset($_GET["id"])) {
        $id = mysql_real_escape_string($_GET["id"]);
    }

    if (isset($_POST["smUpdate"])) {
        $name = mysql_real_escape_string($_POST["name"]);
        $category = mysql_real_escape_string($_POST["category"]);
        $content = mysql_real_escape_string($_POST["content"]);
        if (!isset($id)) {
            mysql_query("INSERT INTO form SET name='{$name}', content='{$content}',keyword_category_id='{$category}', content_type='{$type}'") or die("ERROR 112:" . mysql_error());
            $id = mysql_insert_id();
        } else
            mysql_query("UPDATE form SET name='{$name}', content='{$content}', keyword_category_id='{$category}', content_type='{$type}' WHERE id='{$id}'") or die("ERROR 113:" . mysql_error());
        $_SESSION['info'] = "Updating form successfully";
        header("Location: admin.php?view=forms_edit&type={$type}&id={$id}");
        die();
    }
    if (isset($_POST["smUpdateReq"])) {
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
        $soil_type = mysql_real_escape_string($_POST["soil_type"]);
        $soil_ph = mysql_real_escape_string($_POST["soil_ph"]);
        $soil_drainage = mysql_real_escape_string($_POST["soil_drainage"]);
        if (!isset($id)) {
            mysql_query("INSERT INTO requirement SET name='{$name}'") or die("ERROR 114:" . mysql_error());
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
			 `use`='{$use}',
			 `soil_type`='{$soil_type}',
			 `soil_ph`='{$soil_ph}',
			 `soil_drainage`='{$soil_drainage}' WHERE id='{$id}'") or die("ERROR 170:" . mysql_error());
        header("Location: admin.php?view=forms_edit&type={$type}&id={$id}");
        die();
    }

    if (isset($_GET["copyid"]))
        $id = $_GET["copyid"];

    if (isset($id)) {
        if ($type == "req")
            $query = mysql_query("SELECT * FROM requirement WHERE id='{$id}'") or die("ERROR 115:" . mysql_error());
        else
            $query = mysql_query("SELECT * FROM form WHERE id='{$id}'") or die("ERROR 116:" . mysql_error());
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

function controller_cms_edit() {

    $id = mysql_real_escape_string($_GET["id"]);
    if (isset($_POST["smCMS"])) {
        $data = mysql_query("SELECT * FROM cms_page WHERE id='{$id}'");
        if (mysql_num_rows($data) == 0)
        {
            mysql_query("INSERT INTO cms_page SET title='New page'") or die (mysql_error());
            $id = mysql_insert_id();
        }
        $title = mysql_real_escape_string($_POST["title"]);
        $content = mysql_real_escape_string($_POST["content"]);
        $q = "UPDATE cms_page SET title='{$title}', content='{$content}' WHERE id='{$id}'";
        mysql_query($q) or die("ERROR 171:" . mysql_error());
        $info = "Page is updated";
        header("Location: /admin.php?view=cms_edit&id=".$id);
        die();
    }
    $data = mysql_query("SELECT * FROM cms_page WHERE id='{$id}'");
    $r = mysql_fetch_object($data);

    include 'view/admin/header.php';
    include 'view/admin/cms_edit.php';
    include 'view/admin/footer.php';
}

function controller_cms_list() {
    include 'view/admin/header.php';
    include 'view/admin/cms_list.php';
    include 'view/admin/footer.php';
}

function controller_settings() {
    if (isset($_POST["smUpdateSettings"])) {
        SaveSetting("admin_email", $_POST['admin_email']);
        SaveSetting("site_description", $_POST['site_description']);
        SaveSetting("site_keyword", $_POST['site_keyword']);
        SaveSetting("review_rules", $_POST['review_rules']);
    }
    for ($i = 0; $i < count($_POST["delete_photo"]); $i++) {
        mysql_query("DELETE FROM sliderimage 
			WHERE id='" . mysql_real_escape_string($_POST["delete_photo"][$i]) . "'") or die("ERROR 117:" . mysql_error());
    }
    for ($i = 0; $i < count(@$_FILES["photo"]["name"]); $i++) {
        if ($_FILES["photo"]["name"][$i] == "")
            continue;
        $temp = pathinfo($_FILES["photo"]["name"][$i]);
        $mainPic = uniqid() . "." . $temp["extension"];
        $pic_location = 'upload/product_images/' . $mainPic;
        mysql_query("INSERT INTO sliderimage SET image='{$mainPic}', 
			caption='" . mysql_real_escape_string($_POST["photo_caption"][$i]) . "', 
			link='" . mysql_real_escape_string($_POST["photo_url"][$i]) . "'") or die("ERROR 118:" . mysql_error());
        move_uploaded_file($_FILES["photo"]["tmp_name"][$i], $pic_location);
    }
    if (isset($_POST["smUpdateSlider"])) {
        mysql_query("UPDATE sliderimage SET `enable`=0");
        foreach ($_POST["enable_photo"] as $epid) {
            mysql_query("UPDATE sliderimage SET `enable`=1 WHERE id='{$epid}'");
        }
        $info = "Updated sliders";
    }
    $data_sliderphotos = mysql_query("SELECT * FROM sliderimage");
    include 'view/admin/header.php';
    include 'view/admin/settings.php';
    include 'view/admin/footer.php';
}

function controller_getproducturls() {
    $data = mysql_query("SELECT * FROM product WHERE status=1");
    while ($r = mysql_fetch_object($data)) {
        echo "http://" . $_SERVER["HTTP_HOST"] . url_product_detail($r->id) . "<br>";
    }
}

$_SESSION['KCFINDER'] = array();
$_SESSION['KCFINDER']['disabled'] = false;