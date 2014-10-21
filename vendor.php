<?php

require_once "config.php";

ini_set('max_execution_time', 600);

$view = "";
if (isset($_GET["view"]))
    $view = $_GET["view"];
else
    $view = "index";

if ($_SESSION["isadmin"] === true) {
    if (isset($_GET["admin_vendor_id"]) == false) {
        header("location: /admin.php?view=vendors");
        die();
    } else {
        $data = mysql_query("SELECT am.*,(SELECT COUNT(*) FROM google_scraped2 AS gs WHERE gs.merchant_id=am.id) no_prod FROM allow_merchant AS am WHERE am.id='" . $_GET["admin_vendor_id"] . "'") or die("Error 7788:" . mysql_error());
        $vendor = mysql_fetch_object($data);
    }
} elseif (!isset($_SESSION["isvendor"]) || $_SESSION["isvendor"] !== true) {
    header("Location: /vendor-register.html");
    die();
} else {
    $data = mysql_query("SELECT am.*,(SELECT COUNT(*) FROM google_scraped2 AS gs WHERE gs.merchant_id=am.id) no_prod FROM allow_merchant AS am WHERE am.id='" . $_SESSION["vendor_id"] . "'") or die("Error 7788:" . mysql_error());
    //$data = mysql_query("SELECT * FROM allow_merchant WHERE id='" . $_SESSION["vendor_id"] . "'") or die("Error 7788:" . mysql_error());
    $vendor = mysql_fetch_object($data);
}

if ($vendor->status != 'approved' && $_SESSION["isadmin"] === false) {
    die("Your account is not activated");
}
$temp = pathinfo(__FILE__);
define("CURRENT_VIEW", $view);
define("CURRENT_CONTROLLER", $temp["filename"]);
$funcview = "controller_" . $view;
$funcview = str_replace("-", "_", $funcview);
//echo $funcview;exit;
if (function_exists($funcview))
    call_user_func($funcview);

function controller_index() {
    $page_title = "Merchant Center";
    global $vendor;
    include "view/vendor/header.php";
    include "view/vendor/home.php";
    include "view/vendor/footer.php";
}

function controller_welcome() {
    $page_title = "Welcome - Merchant Center";
    global $vendor;
    if (isset($_POST[smSubmit]) || isset($_POST[smPreview]))
    {
        $form = filter_var($_POST[welcome], FILTER_SANITIZE_SPECIAL_CHARS);
        mysql_query("UPDATE allow_merchant SET welcome='{$form}' WHERE id='{$vendor->id}'") or die("Error 778991:" . mysql_error());
    	
		$data = mysql_query("SELECT am.*,(SELECT COUNT(*) FROM google_scraped2 AS gs WHERE gs.merchant_id=am.id) no_prod FROM allow_merchant AS am WHERE am.id='" . $_SESSION["vendor_id"] . "'") or die("Error 77808:" . mysql_error());
    	$vendor = mysql_fetch_object($data);
	}
    include "view/vendor/header.php";
    include "view/vendor/welcome.php";
    include "view/vendor/footer.php";
}

function controller_aboutus() {
    $page_title = "About Us - Merchant Center";
    global $vendor;
    if (isset($_POST[smSubmit]) || isset($_POST[smPreview]))
    {
        $form = filter_var($_POST[aboutus], FILTER_SANITIZE_SPECIAL_CHARS);
        mysql_query("UPDATE allow_merchant SET aboutus='{$form}' WHERE id='{$vendor->id}'") or die("Error 778992:" . mysql_error());
    	
		$data = mysql_query("SELECT am.*,(SELECT COUNT(*) FROM google_scraped2 AS gs WHERE gs.merchant_id=am.id) no_prod FROM allow_merchant AS am WHERE am.id='" . $_SESSION["vendor_id"] . "'") or die("Error 77808:" . mysql_error());
    	$vendor = mysql_fetch_object($data);
	}
    include "view/vendor/header.php";
    include "view/vendor/aboutus.php";
    include "view/vendor/footer.php";
}

function controller_terms() {
    $page_title = "Terms &amp; Conditions - Merchant Center";
    global $vendor;
    if (isset($_POST[smSubmit]) || isset($_POST[smPreview]))
    {
        $form = filter_var($_POST[terms], FILTER_SANITIZE_SPECIAL_CHARS);
        mysql_query("UPDATE allow_merchant SET terms='{$form}' WHERE id='{$vendor->id}'") or die("Error 778993:" . mysql_error());
    	
		$data = mysql_query("SELECT am.*,(SELECT COUNT(*) FROM google_scraped2 AS gs WHERE gs.merchant_id=am.id) no_prod FROM allow_merchant AS am WHERE am.id='" . $_SESSION["vendor_id"] . "'") or die("Error 77808:" . mysql_error());
    	$vendor = mysql_fetch_object($data);
    }
    include "view/vendor/header.php";
    include "view/vendor/terms.php";
    include "view/vendor/footer.php";
}

function controller_shipping() {
    $page_title = "Shipping Policies - Merchant Center";
    global $vendor;
    if (isset($_POST[smSubmit]) || isset($_POST[smPreview]))
    {
        $form = filter_var($_POST[shipping_policy], FILTER_SANITIZE_SPECIAL_CHARS);
        mysql_query("UPDATE allow_merchant SET shipping_policy='{$form}' WHERE id='{$vendor->id}'") or die("Error 778994:" . mysql_error());
    	
		$data = mysql_query("SELECT am.*,(SELECT COUNT(*) FROM google_scraped2 AS gs WHERE gs.merchant_id=am.id) no_prod FROM allow_merchant AS am WHERE am.id='" . $_SESSION["vendor_id"] . "'") or die("Error 77808:" . mysql_error());
    	$vendor = mysql_fetch_object($data);
	}
    include "view/vendor/header.php";
    include "view/vendor/shipping.php";
    include "view/vendor/footer.php";
}

function processing_edit_profile() {
    global $vendor;
    $res = array();
    $res["error"] = "";
    $res["info"] = "";

    if (isset($_POST["smVendorEdit"])) {
        $firstname = filter_input(INPUT_POST, "firstname", FILTER_SANITIZE_STRING);
        $lastname = filter_input(INPUT_POST, "lastname", FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
        $password1 = filter_input(INPUT_POST, "password", FILTER_SANITIZE_ENCODED);
        $password2 = filter_input(INPUT_POST, "password2", FILTER_SANITIZE_STRING);
        $shopname = filter_input(INPUT_POST, "shopname", FILTER_SANITIZE_STRING);
        $website = filter_input(INPUT_POST, "website", FILTER_SANITIZE_STRING);
        $phonenumber = filter_input(INPUT_POST, "phonenumber", FILTER_SANITIZE_STRING);
        $country = filter_input(INPUT_POST, "country", FILTER_SANITIZE_STRING);
        $state = filter_input(INPUT_POST, "state", FILTER_SANITIZE_STRING);
        $street = filter_input(INPUT_POST, "street", FILTER_SANITIZE_STRING);
        $city = filter_input(INPUT_POST, "city", FILTER_SANITIZE_STRING);
        $zip = filter_input(INPUT_POST, "zip", FILTER_SANITIZE_STRING);
        $aboutus = filter_input(INPUT_POST, "aboutus", FILTER_SANITIZE_STRING);
        $terms = filter_input(INPUT_POST, "terms", FILTER_SANITIZE_STRING);
        $shipping_policy = filter_input(INPUT_POST, "shipping_policy", FILTER_SANITIZE_STRING);
        $return_policy = filter_input(INPUT_POST, "return_policy", FILTER_SANITIZE_STRING);
        $privacy_policy = filter_input(INPUT_POST, "privacy_policy", FILTER_SANITIZE_STRING);
        $contact = filter_input(INPUT_POST, "contact", FILTER_SANITIZE_STRING);
        

        if ($_SESSION["isadmin"] === true) {
            if (empty($firstname) ||
                    empty($lastname) ||
                    empty($email) ||
                    empty($shopname) ||
                    empty($street) ||
                    empty($city) ||
                    empty($zip) ||
                    //empty($state) ||
                    empty($website) ||
                    empty($country)) {
                $res["error"] = "Please enter all the required fields";
                return $res;
            }
        } else {
            if (empty($firstname) ||
                    empty($street) ||
                    empty($city) ||
                    empty($zip) ||
                   // empty($state) ||
                    empty($lastname)) {
                $res["error"] = "Please enter all the required fields!";
                return $res;
            }
        }

        if ($password1 != '' && $password1 != $password2) {
            $res["error"] = "Passwords are not matched";
            return $res;
        }

        $mid = $vendor->id;
        $singledata = mysql_query("SELECT * FROM allow_merchant WHERE id='$mid'");
        $single = mysql_fetch_object($singledata);
        $image = $single->logo;
        if (isset($_FILES["logo"]["name"]) && $_FILES["logo"]["name"] != '') {
            $temp = pathinfo($_FILES["logo"]["name"]);
            $image = uniqid("logo_") . "." . $temp["extension"];
            $pic_location = 'upload/vendor_logo/' . $image;
            @unlink($pic_location);
            move_uploaded_file($_FILES["logo"]["tmp_name"], $pic_location);
        }
        //var_dump($_FILES);var_dump($image);exit;
        if ($_SESSION["isadmin"] === true) {
            mysql_query("UPDATE allow_merchant SET "
                            . "first_name='{$firstname}'"
                            . " ,last_name='{$lastname}'"
                            . " ,country='{$country}'"
                            . " ,merchant='{$website}'"
                            . " ,logo='{$image}'"
                            . " ,email='{$email}'"
                            . " ,street='{$street}'"
                            . " ,city='{$city}'"
                            . " ,zip='{$zip}'"
                            . " ,state='{$state}'"
                            . " ,username='$email'"
                            . " ,phone_number='{$phonenumber}'"
                            . " ,shop_name='{$shopname}'"
                            . " ,website='{$website}'"
                            . " WHERE id='{$mid}'") or die("Error 78203:" . mysql_error());
        } else {
            mysql_query("UPDATE allow_merchant SET "
                            . "first_name='{$firstname}'"
                            . " ,last_name='{$lastname}'"
                            . " ,merchant='{$website}'"
                            . " ,street='{$street}'"
                            . " ,city='{$city}'"
                            . " ,zip='{$zip}'"
                            . " ,state='{$state}'"
                            . " ,aboutus='{$aboutus}'"
                            . " ,shipping_policy='{$shipping_policy}'"
                            . " ,return_policy='{$return_policy}'"
                            . " ,privacy_policy='{$privacy_policy}'"
                            . " ,contact='{$contact}'"
                            . " ,terms='{$terms}'"
                            . " ,logo='{$image}'"
                            . " ,phone_number='{$phonenumber}'"
                            . " WHERE id='{$mid}'") or die("Error 78203:" . mysql_error());
        }
        if ($password1 != '') {
            mysql_query("UPDATE allow_merchant SET "
                            . "`password`=MD5('{$password1}'), `password2`=MD5('{$password1}')"
                            . " WHERE id='{$mid}'") or die("Error 78203:" . mysql_error());
        }
        $res["info"] = "Profile is updated successfully. Please login.";
        return $res;
    }
}

function controller_edit_profile() {
    $page_title = "Edit profile - Merchant Center";
    global $vendor;
    $res = processing_edit_profile();
    $error = $res["error"];
    $info = $res["info"];
    $warn = $res["warn"];

    include "view/vendor/header.php";
    include "view/vendor/profile.php";
    include "view/vendor/footer.php";
}

function processing_load_data() {
    global $vendor;
    if (isset($_GET["load_data"])) {
        $aColumns = array('name', 'description', 'created_date', "price", "quantity",'google_item_id', 'site_url', 'picture');
        $sIndexColumn = "id";
        $sTable = "google_scraped2";

        /*
         * Paging
         */
        $sLimit = "";
        if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
            $sLimit = " LIMIT " . intval($_GET['iDisplayStart']) . ", " .
                    intval($_GET['iDisplayLength']);
        }


        /*
         * Ordering
         */
        $sOrder = "";
        if (isset($_GET['iSortCol_0'])) {
            $sOrder = "ORDER BY  ";
            for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
                if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
                    $sOrder .= "`" . $aColumns[intval($_GET['iSortCol_' . $i])] . "` " .
                            ($_GET['sSortDir_' . $i] === 'asc' ? 'asc' : 'desc') . ", ";
                }
            }

            $sOrder = substr_replace($sOrder, "", -2);
            if ($sOrder == "ORDER BY") {
                $sOrder = "";
            }
        }


        /*
         * Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
         */
        $sWhere = "";
        if (isset($_GET['sSearch']) && $_GET['sSearch'] != "") {
            $sWhere = "WHERE (";
            for ($i = 0; $i < count($aColumns); $i++) {
                $sWhere .= "`" . $aColumns[$i] . "` LIKE '%" . mysql_real_escape_string($_GET['sSearch']) . "%' OR ";
            }
            $sWhere = substr_replace($sWhere, "", -3);
            $sWhere .= ')';
        }
        if ($sWhere != '')
            $sWhere .= " AND merchant_id=" . $vendor->id . " ";
        else
            $sWhere .= " WHERE merchant_id=" . $vendor->id . " ";

        /* Individual column filtering */
        for ($i = 0; $i < count($aColumns); $i++) {
            if (isset($_GET['bSearchable_' . $i]) && $_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != '') {
                if ($sWhere == "") {
                    $sWhere = "WHERE ";
                } else {
                    $sWhere .= " AND ";
                }
                $sWhere .= "`" . $aColumns[$i] . "` LIKE '%" . mysql_real_escape_string($_GET['sSearch_' . $i]) . "%' ";
            }
        }


        /*
         * SQL queries
         * Get data to display
         */
        $sQuery = "
		SELECT SQL_CALC_FOUND_ROWS `" . str_replace(" , ", " ", implode("`, `", $aColumns)) . "`
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit
		";
        //echo $sQuery;
        $rResult = mysql_query($sQuery) or die("Error 889291:" . mysql_error());

        /* Data set length after filtering */
        $sQuery = "
		SELECT FOUND_ROWS()
	";
        $rResultFilterTotal = mysql_query($sQuery) or die("Error 889292:" . mysql_error());
        $aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
        $iFilteredTotal = $aResultFilterTotal[0];

        /* Total data set length */
        $sQuery = "
		SELECT COUNT(`" . $sIndexColumn . "`)
		FROM   $sTable
	";
        $rResultTotal = mysql_query($sQuery) or die("Error 889293:" . mysql_error());
        $aResultTotal = mysql_fetch_array($rResultTotal);
        $iTotal = $aResultTotal[0];


        /*
         * Output
         */
        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );

        while ($aRow = mysql_fetch_array($rResult)) {
            $row = array();
            //$row[] = $aRow["google_item_id"];
            //if ($aRow["picture"] != '' || $aRow["downloaded_image"] != '') {
            if ($aRow["picture"] != '') {// || $aRow["downloaded_image"] != '') {    
                //if ($aRow["downloaded_image"] != '')
                //  $row[] = "<img src=\"/scaleimage.php?w=150&amp;h=150&amp;t=retailimages&amp;f=" . $aRow["downloaded_image"] . "\" /><br><a href=\"" . ($aRow["site_url"]) . "\" target=\"_blank\">" . html_entity_decode($aRow["name"]) . "</a>";
                //else //if (strpos($aRow["picture"], "http") === 0 || strpos($aRow["picture"], "data") === 0)
                $row[] = "<img style=\"width:150px;height:150px;\" src=\"" . $aRow["picture"] . "\" /><br><a href=\"" . ($aRow["site_url"]) . "\" target=\"_blank\">" . html_entity_decode($aRow["name"]) . "</a>";
            } else {
                $row[] = "<a href=\"" . ($aRow["site_url"]) . "\" target=\"_blank\">" . html_entity_decode($aRow["name"]) . "</a>";
            }
            $row[] = "<span class=\"rm\">" . html_entity_decode($aRow["description"]) . "</span>";
            $row[] = html_entity_decode($aRow["created_date"]);
            $row[] = CURRENCY.$aRow["price"];
            $row[] = $aRow["quantity"];

            $data_inpost = mysql_query("SELECT p.* FROM product AS p
        	  INNER JOIN product_seller_item AS ps ON p.id = ps.product_id
        	  INNER JOIN keyword_category AS kc ON kc.id = ps.keyword_category_id
        	  INNER JOIN keyword AS k ON k.category_id = kc.id
                  INNER JOIN google_scraped AS gs ON gs.keyword_id = k.id
        	  WHERE gs.google_item_id='" . $aRow["google_item_id"] . "'") or die(mysql_error());
            if (mysql_num_rows($data_inpost) == 0) {
                $row[] = 'none';
            } else {
                while ($r2 = mysql_fetch_object($data_inpost)) {
                    $row[] = '<a target="_blank" href="' . url_product_detail($r2->id) . '">' . $r2->name . '</a> <br>';
                }
            }

            $output['aaData'][] = $row;
        }
        echo json_encode($output);
        die();
    }
}

function controller_product_list() {
    $page_title = "Product list - Merchant Center";
    processing_load_data();
    global $vendor;
    include "view/vendor/header.php";
    include "view/vendor/product_list.php";
    include "view/vendor/footer.php";
}

function processing_import() {
    //return status of process
    $res = array();
    if (isset($_POST["smCSVFile"])) {
        require_once "includes/csv.php";
        //access several info of curreng logged in vendor
        global $vendor;
        //log update insert and delete
        $res["insert"] = 0;
        $res["update"] = 0;
        $res["delete"] = 0;
        //get content of uploaded file
        //var_dump(parse_csv($_FILES["csvFile"]["tmp_name"], ","));exit;
        $content = file_get_contents($_FILES["csvFile"]["tmp_name"]);
        //var_dump($content);exit;
        //will be used to compare uploaded and old data to delete outdate ones
        $updatingProducts = array();
        $oldProducts = array();
        $import_id = -1;
        $isFirst = true;
        //var_dump(explode("\r", $content));exit;
        mysql_query("INSERT INTO imported_csv SET import_status='not_parsed', merchant_id='{$vendor->id}'") or die("Error 100085");
        $import_id = mysql_insert_id();
        //var_dump($import_id);exit;
        $lines = parse_csv($content);//explode("\r", $content);
//        if (count($lines) == 0 || count($lines) == 1)
//            $lines = explode("\n", $content);
        //var_dump($lines);exit;
        foreach ($lines as $line) {
            $data = $line;
            if ($isFirst) {
                $isFirst = false;
//                $line = trim($line);
                //explode(",", $line);
                //var_dump($data);
                $column_index = 0;
                foreach ($data as $keycolumn)
                {
                    if (strtolower($keycolumn) == 'id')
                        $index_item_id = $column_index;
                    if (strtolower($keycolumn) == 'title')
                        $index_name = $column_index;
                    if (strtolower($keycolumn) == 'price')
                        $index_price = $column_index;
                    if (strtolower($keycolumn) == 'image_link')
                        $index_picture = $column_index;
                    if (strtolower($keycolumn) == 'link')
                        $index_site_url = $column_index;
                    if (strtolower($keycolumn) == 'quantity')
                        $index_quantity = $column_index;
                    if (strtolower($keycolumn) == 'description')
                        $index_description = $column_index;
                    $column_index++;
                }
                
                continue;
            }
//            $data = explode(",", $line);
            for ($i=0;$i<count($data);$i++)
                $data[$i] = str_replace ("\"", "", $data[$i]);
            //var_dump($data);exit;
            $data[$index_item_id] = trim($data[$index_item_id]);
            if (empty($data[$index_item_id]))
                continue;
            $google_item_id = md5($vendor->id . '_' . $data[$index_item_id]);
            $updatingProducts[] = $google_item_id;
            $columns["name"] = mysql_real_escape_string($data[$index_name]);
            $columns["merchant_name"] = mysql_real_escape_string($vendor->shop_name);
            $columns["merchant_id"] = mysql_real_escape_string($vendor->id);
            $columns["price"] = mysql_real_escape_string($data[$index_price]);
            $columns["picture"] = mysql_real_escape_string($data[$index_picture]);
            $columns["site_url"] = mysql_real_escape_string($data[$index_site_url]);
            $columns["country"] = mysql_real_escape_string(strtolower($vendor->country));
            $columns["quantity"] = mysql_real_escape_string($data[$index_quantity]);
            $columns["description"] = mysql_real_escape_string($data[$index_description]);
//            print_r($columns);
            $set = "";
            foreach ($columns as $k => $v) {
                if (empty($set))
                    $set .= " `{$k}`= '{$v}' ";
                else
                    $set .= ", `{$k}`= '{$v}' ";
            }
            $data = mysql_query("SELECT * FROM google_scraped2 WHERE google_item_id = '$google_item_id'") or die("Error 100082");
            $row = mysql_fetch_object($data);
            if ($row && !empty($row)) {
                $res["update"] = $res["update"] + 1;
                $q = "UPDATE google_scraped2 SET {$set} WHERE google_item_id= '$google_item_id'";
            } else {
                $res["insert"] = $res["insert"] + 1;
                $q = "INSERT INTO google_scraped2 SET {$set}, google_item_id= '$google_item_id'";
            }
            //echo $q;exit;
            mysql_query($q) or die("Error 100078 " . mysql_error());
        }
        $data = mysql_query("SELECT * FROM google_scraped2 WHERE merchant_id='{$vendor->id}'") or die("Error 100079");
        
        while ($row = mysql_fetch_object($data)) {
            $oldProducts[] = $row->google_item_id;
        }
        foreach ($oldProducts as $id) {
            if (in_array($id, $updatingProducts) == false) {
                $res["delete"] = $res["delete"] + 1;
                mysql_query("DELETE FROM google_scraped2 WHERE google_item_id='{$id}' AND merchant_id='{$vendor->id}'") or die("Error 100080");
            }
        }
        //echo "UPDATE imported_csv SET import_status='parsed', import_count='".($res["update"]+$res["insert"])."', no_insert='".$res["insert"]."', no_delete='".$res["delete"]."', no_update='".$res["update"]."' WHERE id='{$import_id}'";
        mysql_query("UPDATE imported_csv SET import_status='parsed', imported_count='" . ($res["update"] + $res["insert"]) . "', no_insert='" . $res["insert"] . "', no_delete='" . $res["delete"] . "', no_update='" . $res["update"] . "' WHERE id='{$import_id}'") or die("Error 100081");
        $res["success"] = "Your data has been successfully imported. Updates:". $res["update"] . ", Inserts:" . $res["insert"] . ", Deletes: " . $res["delete"] . ". Click <a href=\"/vendor/product-list.html".((isset($_GET["admin_vendor_id"])) ? '?admin_vendor_id='.$_GET["admin_vendor_id"] : "")."\">here</a> to view imported products";
    }
    return $res;
}

function controller_import_csv() {
    $page_title = "Import CSV - Merchant Center";
    $res = processing_import();
    global $vendor;
    $data = mysql_query("SELECT * FROM imported_csv WHERE merchant_id='{$vendor->id}' ORDER BY created_date DESC LIMIT 50");
    include "view/vendor/header.php";
    include "view/vendor/import_csv.php";
    include "view/vendor/footer.php";
}

function controller_logout() {
    RemoveCookieLive("session_id");
    mysql_query("UPDATE allow_merchant SET session_id='".  uniqid() ."' WHERE id='{$_SESSION[vendor_id]}'");
    unset($_SESSION["isvendor"]);
    unset($_SESSION["vendor_id"]);
    header("Location: /");
}
