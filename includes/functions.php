<?php

function GetSetting($name) {
    $data = mysql_query("SELECT * FROM settings WHERE settings_name='{$name}'");
    $row = mysql_fetch_object($data);
    if (!isset($row->settings_value) || $row->settings_value == null)
        return "";
    return $row->settings_value;
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

/**
 * 
 * @param type $key
 * @param type $from_name
 * @param type $from_email
 * @param type $subject
 * @param type $emails
 * @param type $html
 * @param type $attachments
 * @return type sent or error
 */
function send_email_mandrillapp($key,$from_name,$from_email,$subject,$emails,$html,$attachments = array())
{
    $message = array();
    $message["html"] = $html;
    $message["subject"] = $subject;
    $message["from_name"] = $from_name;
    $message["from_email"] = $from_email;
    $message["to"] = $emails;
    /*"to": [
            {
                "email": "recipient.email@example.com",
                "name": "Recipient Name",
                "type": "to"
            }
        ],*/
    if (count($attachments) > 0)
        $message["attachments"] = $attachments;
    /* "attachments": [
            {
                "type": "text/plain",
                "name": "myfile.txt",
                "content": "ZXhhbXBsZSBmaWxl" //base64-encoded
            }
        ],*/
    $params = array('key'=> $key, 'message' => $message);
    //var_dump($params);exit;
	
    $json = json_encode($params);
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, 'http://mandrillapp.com/api/1.0/messages/send' );
    curl_setopt($ch,CURLOPT_POST,count($params));
    curl_setopt($ch,CURLOPT_POSTFIELDS,$json);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");		
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($json)));
    $result = curl_exec($ch);
    curl_close($ch);
    $decoded = json_decode($result);
    //var_dump($decoded);exit;

    return $decoded[0]->status;
}

function SetCookieLive($name, $value = '', $expire = 0, $path = '', $domain = '', $secure = false, $httponly = false) {
    $_COOKIE[$name] = $value;
    return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
}

function RemoveCookieLive($name) {
    unset($_COOKIE[$name]);
    return setcookie($name, NULL, -1);
}

function RemoveUnreadableChars($str) {
    return preg_replace('/[^\x00-\x7F]+/', '', $str);
}


function replaceUTF8Symbols($str)
{

$ret = $str;

 
 //                 UTF8 -> ISO
  $ret = str_replace("—"   , "-",  $ret);
  $ret = str_replace("’"   , "'",  $ret);
  $ret = str_replace("‘"   , "'",  $ret);
  $ret = str_replace("œ"  , "oe", $ret);
  $ret = str_replace("…"  , "...", $ret);

 return $ret;

}
function DisplayUTF8EncodedHTMLString($str)
{
  $filteredStr = replaceUTF8Symbols($str);
  $decodedStr = html_entity_decode(utf8_decode($filteredStr));
  
  return $decodedStr;
}

function FilterScrapedProductName($str) {
    $str = html_entity_decode($str);
    preg_match('/.*?(?=\&lt)/is', $str, $match);
    if ($match[0] != "")
        $str = $match[0];
    return $str;
}

function FilterScrapedProductUrl($str) {
    preg_match('/.*?(?=\?p=)/is', $str, $match);
    if ($match[0] != "")
        $str = $match[0];
    return $str;
}

function SaveSetting($name, $value) {
    mysql_query("INSERT IGNORE INTO settings SET settings_name='{$name}'");
    mysql_query("REPLACE settings SET settings_name='{$name}', settings_value='{$value}'");
}

function CheckUserReviewed($pid) {
    $ip = GetIP();
    $data = mysql_query("SELECT COUNT(*) AS counter FROM user_rating WHERE ip='{$ip}' AND product_id='{$pid}'");
    $r = mysql_fetch_object($data);
    if ($r->counter == 0)
        return false;
    else
        return true;
}

function strposa($haystack, $needles = array(), $offset = 0) {
    $chr = array();
    foreach ($needles as $needle) {
        $res = strpos($haystack, $needle, $offset);
        if ($res !== false)
            $chr[$needle] = $res;
    }
    if (empty($chr))
        return false;
    return min($chr);
}

function is_string_in_array($string, $array) {
    $value = false;
    foreach ($array as $element) {
        if (strpos(strtolower($string), strtolower($element)) !== false) {
            return true;
        }
    }
    return false;
}

function excerpt($text, $numb) {
    $text = preg_replace('/<[^>]*>/is', '', $text);
    if (strlen($text) > $numb) {
        $text = substr($text, 0, $numb);
        $text = substr($text, 0, strrpos($text, " "));
        $etc = " ...";
        $text = $text . $etc;
    }
    return $text;
}

function replace_accents($string) {
    return str_replace(array('à', 'á', 'â', 'ã', 'ä', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý'), array('a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y'), $string);
}

function GenerateCategoryList($cattype, $name) {
    echo '<h4>' . $name . '</h4>';
    $data_cat = mysql_query("SELECT * FROM product_category WHERE category_type='{$cattype}' AND parent_id=0") or die(mysql_error());
    while ($r = mysql_fetch_object($data_cat)) {
        $data_cat2 = mysql_query("SELECT * FROM product_category WHERE category_type='{$cattype}' AND parent_id={$r->id}") or die(mysql_error());
        echo '<label class="checkbox" for="category_' . $r->id . '"><input type="checkbox" value="' . $r->id . '" id="category_' . $r->id . '" name="categories[]"> ' . $r->category_name . '<br>';
        while ($r2 = mysql_fetch_object($data_cat2)) {
            $data_cat3 = mysql_query("SELECT * FROM product_category WHERE category_type='{$cattype}' AND parent_id={$r2->id}") or die(mysql_error());
            echo '<label class="checkbox" for="category_' . $r2->id . '"><input type="checkbox" value="' . $r2->id . '" id="category_' . $r2->id . '" name="categories[]"> ' . $r2->category_name . '<br>';
            while ($r3 = mysql_fetch_object($data_cat3)) {
                $data_cat4 = mysql_query("SELECT * FROM product_category WHERE category_type='{$cattype}' AND parent_id={$r3->id}") or die(mysql_error());
                echo '<label class="checkbox" for="category_' . $r3->id . '"><input type="checkbox" value="' . $r3->id . '" id="category_' . $r3->id . '" name="categories[]"> ' . $r3->category_name . '<br>';
                while ($r4 = mysql_fetch_object($data_cat4)) {
                    echo '<label class="checkbox" for="category_' . $r4->id . '"><input type="checkbox" value="' . $r4->id . '" id="category_' . $r4->id . '" name="categories[]"> ' . $r4->category_name . '<br>';
                    echo '</label>';
                }
                echo '</label>';
            }
            echo '</label>';
        }
        echo '</label>';
    }
}

function GetProductInCategories($categories) {
    $where = "";
    foreach ($categories as $c) {
        if (!empty($c))
            $where[] = $c;
    }
    $q = "SELECT count(p.name) AS counter, p.* FROM product AS p
		INNER JOIN product_in_category AS pic ON p.id=pic.product_id 
			WHERE p.status=1 AND p.publishing_date < '" . time() . "' AND ( pic.category_id IN (" . implode(",", $where) . ") ) GROUP BY p.name HAVING counter >= " . count($where);
    $data = mysql_query($q);
    //echo "<!--$q-->";
    //$row = mysql_fetch_object($data);
    return mysql_num_rows($data);
}

/*function GetLeftMenu($cattype, $name, $parentid = -1, $ignores = array(), $countin = array()) {
    if ($countin[0] == "search")
        $countin = array();
    if ($parentid == -1) {
        echo '<li><a rel="nofollow" href="' . url_category_type($cattype) . '">' . $name . '</a><ul>';
        $data_cat = mysql_query("SELECT * FROM product_category WHERE category_type='{$cattype}' AND visible=1 AND only_show_thumbnail=0 AND parent_id=0 ORDER BY ordering") or die(mysql_error());
    } else {
        $data_cat = mysql_query("SELECT * FROM product_category WHERE category_type='{$cattype}' AND visible=1 AND only_show_thumbnail=0 AND parent_id={$parentid} ORDER BY ordering") or die(mysql_error());
    }
    while ($r = mysql_fetch_object($data_cat)) {
        $dc = $countin;
        $dc[] = $r->id;
        $data_cat2 = mysql_query("SELECT * FROM product_category WHERE category_type='{$cattype}' AND visible=1 AND only_show_thumbnail=0 AND parent_id={$r->id} ORDER BY ordering") or die(mysql_error());
        $countInCat = GetProductInCategories($dc);
        if (mysql_num_rows($data_cat2) > 0)
            if (!in_array($r->id, $ignores))
                echo '<li id="tvc_' . $r->id . '"><span>' . $r->category_name . '</span><ul>';
            else
                continue;
        elseif (!in_array($r->id, $ignores) && $countInCat > 0)
            echo '<li><a rel="nofollow" href="' . url_category($r->id) . '?append=1">' . $r->category_name . ' (' . $countInCat . ')</a>';
        while ($r2 = mysql_fetch_object($data_cat2)) {
            $dc = $countin;
            $dc[] = $r2->id;
            $data_cat3 = mysql_query("SELECT * FROM product_category WHERE category_type='{$cattype}' AND visible=1 AND only_show_thumbnail=0 AND parent_id={$r2->id} ORDER BY ordering") or die(mysql_error());
            $countInCat = GetProductInCategories($dc);
            if (mysql_num_rows($data_cat3) > 0)
                if (!in_array($r2->id, $ignores))
                    echo '<li><span>' . $r2->category_name . '</span><ul>';
                else
                    continue;
            elseif (!in_array($r2->id, $ignores) && $countInCat > 0)
                echo '<li><a rel="nofollow" href="' . url_category($r2->id) . '?append=1">' . $r2->category_name . ' (' . $countInCat . ')</a>';
            while ($r3 = mysql_fetch_object($data_cat3)) {
                $dc = $countin;
                $dc[] = $r3->id;
                $data_cat4 = mysql_query("SELECT * FROM product_category WHERE category_type='{$cattype}' AND visible=1 AND only_show_thumbnail=0 AND parent_id={$r3->id} ORDER BY ordering") or die(mysql_error());
                $countInCat = GetProductInCategories($dc);
                if (mysql_num_rows($data_cat4) > 0)
                    if (!in_array($r3->id, $ignores))
                        echo '<li><span>' . $r3->category_name . '</span><ul>';
                    else
                        continue;
                elseif (!in_array($r3->id, $ignores) && $countInCat > 0)
                    echo '<li><a rel="nofollow" href="' . url_category($r3->id) . '?append=1">' . $r3->category_name . ' (' . $countInCat . ')</a>';
                while ($r4 = mysql_fetch_object($data_cat4)) {
                    $dc = $countin;
                    $dc[] = $r4->id;
                    $countInCat = GetProductInCategories($dc);
                    if (!in_array($r4->id, $ignores) && $countInCat > 0) {
                        echo '<li><a rel="nofollow" href="' . url_category($r4->id) . '?append=1">' . $r4->category_name . ' (' . $countInCat . ')</a>';

                        echo '</li>';
                    }
                }
                if (mysql_num_rows($data_cat4) > 0)
                    echo '</ul></li>';
                else
                    echo '</li>';
            }
            if (mysql_num_rows($data_cat3) > 0)
                echo '</ul></li>';
            else
                echo '</li>';
        }
        if (mysql_num_rows($data_cat2) > 0)
            echo '</ul></li>';
        else
            echo '</li>';
        echo '</li>';
    }
    echo '</ul></li>';
}*/

function GetLeftMenu($cattype, $name, $parentid = -1, $ignores = array(), $countin = array()) {
    if ($countin[0] == "search")
        $countin = array();
    if ($parentid == -1) {
        echo '<li><a rel="dofollow" href="' . url_category_type($cattype) . '">' . $name . '</a><ul>';
        $data_cat = mysql_query("SELECT * FROM product_category WHERE category_type='{$cattype}' AND visible=1 AND only_show_thumbnail=0 AND parent_id=0 ORDER BY ordering") or die(mysql_error());
    } else {
        $data_cat = mysql_query("SELECT * FROM product_category WHERE category_type='{$cattype}' AND visible=1 AND only_show_thumbnail=0 AND parent_id={$parentid} ORDER BY ordering") or die(mysql_error());
    }
    while ($r = mysql_fetch_object($data_cat)) {
        $dc = $countin;
        $dc[] = $r->id;
        $data_cat2 = mysql_query("SELECT * FROM product_category WHERE category_type='{$cattype}' AND visible=1 AND only_show_thumbnail=0 AND parent_id={$r->id} ORDER BY ordering") or die(mysql_error());
        //$countInCat = GetProductInCategories($dc);
        if (mysql_num_rows($data_cat2) > 0)
            if (!in_array($r->id, $ignores))
                echo '<li id="tvc_' . $r->id . '"><span>' . $r->category_name . '</span><ul>';
            else
                continue;
        elseif (!in_array($r->id, $ignores))
            echo '<li><a rel="dofollow" href="' . url_category($r->id) . '?append=1">' . $r->category_name . '</a>';
        while ($r2 = mysql_fetch_object($data_cat2)) {
            $dc = $countin;
            $dc[] = $r2->id;
            $data_cat3 = mysql_query("SELECT * FROM product_category WHERE category_type='{$cattype}' AND visible=1 AND only_show_thumbnail=0 AND parent_id={$r2->id} ORDER BY ordering") or die(mysql_error());
            //$countInCat = GetProductInCategories($dc);
            if (mysql_num_rows($data_cat3) > 0)
                if (!in_array($r2->id, $ignores))
                    echo '<li><span>' . $r2->category_name . '</span><ul>';
                else
                    continue;
            elseif (!in_array($r2->id, $ignores))
                echo '<li><a rel="dofollow" href="' . url_category($r2->id) . '?append=1">' . $r2->category_name . '</a>';
            while ($r3 = mysql_fetch_object($data_cat3)) {
                $dc = $countin;
                $dc[] = $r3->id;
                $data_cat4 = mysql_query("SELECT * FROM product_category WHERE category_type='{$cattype}' AND visible=1 AND only_show_thumbnail=0 AND parent_id={$r3->id} ORDER BY ordering") or die(mysql_error());
                //$countInCat = GetProductInCategories($dc);
                if (mysql_num_rows($data_cat4) > 0)
                    if (!in_array($r3->id, $ignores))
                        echo '<li><span>' . $r3->category_name . '</span><ul>';
                    else
                        continue;
                elseif (!in_array($r3->id, $ignores))
                    echo '<li><a rel="dofollow" href="' . url_category($r3->id) . '?append=1">' . $r3->category_name . '</a>';
                while ($r4 = mysql_fetch_object($data_cat4)) {
                    $dc = $countin;
                    $dc[] = $r4->id;
                    //$countInCat = GetProductInCategories($dc);
                    if (!in_array($r4->id, $ignores)) {
                        echo '<li><a rel="dofollow" href="' . url_category($r4->id) . '?append=1">' . $r4->category_name . '</a>';

                        echo '</li>';
                    }
                }
                if (mysql_num_rows($data_cat4) > 0)
                    echo '</ul></li>';
                else
                    echo '</li>';
            }
            if (mysql_num_rows($data_cat3) > 0)
                echo '</ul></li>';
            else
                echo '</li>';
        }
        if (mysql_num_rows($data_cat2) > 0)
            echo '</ul></li>';
        else
            echo '</li>';
        echo '</li>';
    }
    echo '</ul></li>';
}

function GetMenu($cattype, $name, $class) {
    echo '<li class="' . $class . '"><a href="' . url_category_type($cattype) . '">' . $name . '</a><ul class="submenu">';
    $data_cat = mysql_query("SELECT * FROM product_category WHERE category_type='{$cattype}' AND show_in_main_menu=1 AND parent_id=0 AND visible=1") or die(mysql_error());
    while ($r = mysql_fetch_object($data_cat)) {
        echo '<li><a href="' . url_category($r->id) . '">' . $r->category_name . '</a></li>';
    }
    echo '</ul></li>';
}

function GenerateBreadcrumb($name, $id, $cid = 0, $type = "") {
    $content = '';
    $query = mysql_query("SELECT pc.* FROM product_category AS pc
		INNER JOIN product_in_category AS pic ON pc.id = pic.category_id
		WHERE product_id='{$id}' AND visible=1");
    if ($r = mysql_fetch_object($query)) {
        $content .= '<li>
			<a itemprop="category" rel="dofollow" href="' . url_category($r->id) . '">' . stripcslashes($r->category_name) . '</a>
			
			</li>';
    }

    if ($cid != 0) {
        $query = mysql_query("SELECT pc.* FROM product_category AS pc
			INNER JOIN product_in_category AS pic ON pc.id = pic.category_id
			WHERE category_id='{$cid}' AND visible=1");
        $r = mysql_fetch_object($query);
    }

    $query = mysql_query("SELECT pc.* FROM product_category AS pc
			WHERE pc.id='{$r->parent_id}' AND visible=1");
    if ($r = mysql_fetch_object($query))
        $content = '<li>
			<a itemprop="category" rel="dofollow" href="' . url_category($r->id) . '">' . stripcslashes($r->category_name) . '</a>
			
			</li>' . $content;
    if ($r)
        $type = $r->category_type;
    if ($r) {
        $query = mysql_query("SELECT pc.* FROM product_category AS pc
			WHERE pc.id='{$r->parent_id}' AND visible=1");
        if ($r = mysql_fetch_object($query)) {
            $content = '<li>
				<a itemprop="category" rel="dofollow" href="' . url_category($r->id) . '">' . stripcslashes($r->category_name) . '</a>
				
				</li>' . $content;
            $type = $r->category_type;
        }
    }

    if ($type == "pl")
        $ctype = "Plants";
    elseif ($type == "pr")
        $ctype = "Promenades";
    elseif ($type == "bs")
        $ctype = "Basics";
    elseif ($type == "ds")
        $ctype = "Designs";

    if ($ctype != '') {
        $content = '<li>
			<a itemprop="category" rel="dofollow" href="' . url_category_type($r->id) . '">' . stripcslashes($ctype) . '</a>
			
			</li>' . $content;
    }
    $content = '<li>
		<a rel="dofollow" href="/">Home</a>
		
		</li>' . $content;
    $content .= '<li class="active">' . stripcslashes($name) . '</li>';
    echo $content;
}

function GetCategories($id) {
    $pro_type_data = mysql_query("SELECT DISTINCT pc.category_type FROM product_category AS pc
		INNER JOIN product_in_category AS pic ON pic.category_id=pc.id
		WHERE pic.product_id='{$id}'");
    $product_types = array();
    while ($r2 = mysql_fetch_object($pro_type_data))
        $product_types[] = $r2->category_type;

    return $product_types;
}

function GetTotalPriceGI($id, $tag = false, $pritag = "From") {
    $sproductdata = mysql_query("SELECT p.*,rp.related_id FROM product AS p
		INNER JOIN related_product AS rp ON p.id = rp.related_id
		WHERE rp.product_id='{$id}' AND p.status=1 AND p.publishing_date < '" . time() . "' ") or die(mysql_error());
    if (mysql_num_rows($sproductdata) == 0) {
        return "Price: N/A";
    }
    $total = 0;
    while ($r = mysql_fetch_object($sproductdata)) {
        $total += GetMinPriceProduct2($r->id);
    }
    if ($total == 0)
        return "Price: N/A";
    if ($tag)
        return "{$pritag}: <span itemprop=\"price\">" . CURRENCY . number_format($total, 2) . "</span>";
    else
        return "{$pritag}: " . CURRENCY . number_format($total, 2);
}

function slugify($text) {
    // replace non letter or digits by -
    $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

    // trim
    $text = trim($text, '-');

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // lowercase
    $text = strtolower($text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}

function GetMinPriceProduct($id, $tag = false, $pritag = "From") {
    $query = mysql_query("SELECT MIN(gs.price) AS price FROM google_scraped AS gs
		INNER JOIN keyword AS k ON gs.keyword_id=k.id
		INNER JOIN keyword_category AS kc ON kc.id=k.category_id
		INNER JOIN product_seller_item AS psi ON psi.keyword_category_id=kc.id
		WHERE psi.product_id='{$id}' AND gs.country='" . USER_REGION . "'
		GROUP BY price");
    $row = mysql_fetch_object($query);
    if ($row->price == "NULL" || $row->price == '')
        return "Price: N/A";
    if ($tag)
        return "{$pritag}: <span  itemprop=\"price\">" . CURRENCY . number_format($row->price, 2) . "</span>";
    else
        return "{$pritag}: " . CURRENCY . number_format($row->price, 2);
}

function GetMinPriceCatKw($id, $tag = false, $pritag = "From") {
    $query = mysql_query("SELECT MIN(gs.price) AS price FROM google_scraped AS gs
	INNER JOIN keyword AS k ON gs.keyword_id=k.id
	WHERE k.category_id='{$id}' AND gs.country='" . USER_REGION . "'
	GROUP BY price");
    if ($row->price == "NULL" || $row->price == '')
        return "Price: N/A";
    $row = mysql_fetch_object($query);
    if ($tag)
        return "{$pritag}: <span  itemprop=\"price\">" . CURRENCY . number_format($row->price, 2) . "</span>";
    else
        return "{$pritag}: " . CURRENCY . number_format($row->price, 2);
}

function GetMinPriceProduct2($id) {
    $query = mysql_query("SELECT MIN(gs.price) AS price FROM google_scraped AS gs
		INNER JOIN keyword AS k ON gs.keyword_id=k.id
		INNER JOIN keyword_category AS kc ON kc.id=k.category_id
		INNER JOIN product_seller_item AS psi ON psi.keyword_category_id=kc.id
		WHERE psi.product_id='{$id}' AND gs.country='" . USER_REGION . "'
		GROUP BY price");
    $row = mysql_fetch_object($query);
    if ($row->price == "NULL" || $row->price == '')
        return 0;
    return $row->price;
}

function GetForm($id) {
    $query = mysql_query("SELECT * FROM form WHERE id='{$id}'");
    if (mysql_num_rows($query) == 0)
        return "";
    $row = mysql_fetch_object($query);
    $content = stripcslashes($row->content);
    $catid = $row->keyword_category_id;
    //$minprice = GetMinPriceCatKw($id);
    //$content = str_replace('[price]',$minprice,$content);
    return $content;
}

function GetIP() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if (getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if (getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if (getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if (getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if (getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';

    return $ipaddress;
}

/**
 * Get the feature product from category. 
 * 
 * If there is no feature products are defined, the lastest published products are pulled.
 * @param string $type category to get from
 */
function GetFeaturedProducts($type) {
    $data = mysql_query("SELECT DISTINCT p.* FROM product AS p
		INNER JOIN product_in_category AS pic ON p.id=pic.product_id
		INNER JOIN product_category AS pc ON pic.category_id=pc.id
		WHERE p.isfeatured=1 AND pc.category_type='{$type}' AND p.status=1 AND p.publishing_date < '" . time() . "' LIMIT 4");
    $counter = 0;
    $nextw = "";
    echo "<ul>";
    $products = __c()->get("GetFeaturedProducts_" . $type);
    if ($products == null) {
        $products = array();
        while ($r = mysql_fetch_object($data))
            $products[] = $r;
        __c()->set("GetFeaturedProducts_" . $type, $products, CACHE_TIME);
    }
    $added = array();
    foreach ($products as $r) {
        $added[] = $r->id;
        echo '<li>
			<a class="thumb" href="' . url_product_detail($r->id) . '"><img alt="' . htmlentities($r->image_alt) . '" data-original="/scaleimage.php?w=185&h=120&t=productimage&f=' . $r->main_picture . '" width="185" height="120" class="lazy"></a>
			<h2><a href="' . url_product_detail($r->id) . '">' . stripcslashes($r->name) . '</a></h2>                                
		</li>';
        $nextw .= " AND p.id <> '{$r->id}' ";
        $counter++;
    }

    $data = mysql_query("SELECT DISTINCT p.* FROM product AS p
            INNER JOIN product_in_category AS pic ON p.id=pic.product_id
            INNER JOIN product_category AS pc ON pic.category_id=pc.id
            WHERE p.status=1 AND p.publishing_date < '" . time() . "' AND pc.category_type='{$type}' {$nextw}
            ORDER BY publishing_date DESC
            LIMIT 4");
    $products = __c()->get("GetFeaturedProducts_" . $type . "_2");
    if ($products == null) {
        $products = array();
        while ($r = mysql_fetch_object($data))
            $products[] = $r;
        __c()->set("GetFeaturedProducts_" . $type . "_2", $products, CACHE_TIME);
    }

    foreach ($products as $r) {
        if (count($added) >= 4)
            break;
        if (in_array($r->id, $added))
            continue;
        $added[] = $r->id;
        echo '<li>
                            <a class="thumb" href="' . url_product_detail($r->id) . '"><img alt="' . htmlentities($r->image_alt) . '" data-original="/scaleimage.php?w=185&h=120&t=productimage&f=' . $r->main_picture . '" width="185" height="120" class="lazy"></a>
                            <h2><a href="' . url_product_detail($r->id) . '">' . stripcslashes($r->name) . '</a></h2>
                    </li>';
        $nextw .= " AND p.id <> '{$r->id}' ";
        $counter++;
    }
    echo "</ul>";
}

/**
 * check if the string starts with a specific string
 * 
 * @param string $haystack input string
 * @param string $needle to check
 * @return boolean true if it starts with $needle, otherwise it returns false 
 */
function startsWith($haystack, $needle) {
    return !strncmp($haystack, $needle, strlen($needle));
}

function endsWith($haystack, $needle) {
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

function SunIcon($s) {
    if ($s == 'Full Sun')
        return '<img alt="full sun" src="/static/images/fullsun.jpg" >';
    elseif ($s == 'Partial Sun')
        return '<img alt="partial sun" src="/static/images/partialsun.jpg" >';
    elseif ($s == 'Full Shade')
        return '<img alt="full shade" src="/static/images/full_shade.jpg" >';
}

function GetShortReq($id) {
    $query = mysql_query("SELECT * FROM requirement WHERE id='{$id}'") or die(mysql_error());
    if (mysql_num_rows($query) == 0)
        return "";
    $row = mysql_fetch_object($query);

    $sun = "";
    $plant_type = "";
    if ($row->sun1 != '')
        $sun .= $row->sun1;
    if ($row->sun2 != '')
        $sun .= ', ' . $row->sun2;
    if ($row->sun3 != '')
        $sun .= ', ' . $row->sun3;
    if ($row->sun4 != '')
        $sun .= ', ' . $row->sun4;

    if ($row->plant_type1 != '')
        $plant_type_ar[] = $row->plant_type1;
    if ($row->plant_type2 != '')
        $plant_type_ar[] = $row->plant_type2;
    if ($row->plant_type3 != '')
        $plant_type_ar[] = $row->plant_type3;
    $plant_type = implode(", ", $plant_type_ar);

    if ($row->hardiness != '')
        $content = '<strong>Hardiness Zones</strong>: ' . $row->hardiness . "<br>";
    if ($sun != '')
        $content .= '<strong>Sun</strong>: ' . $sun . "<br>";
    if ($plant_type != '')
        $content .= '<strong>Plant Type</strong>: ' . $plant_type . "<br>";
    if ($plant_type != '')
        $content .= '<strong>Height</strong>: ' . $row->height . "<br>";
    if ($plant_type != '')
        $content .= '<strong>Spacing</strong>: ' . $row->spacing . "<br>";
    if ($row->periodofinterest != '')
        $content .= '<strong>Season</strong>: ' . $row->periodofinterest . "<br>";
    if ($row->periodofinterest != '')
        $content .= '<strong>Soil</strong>: ' . $row->soil_type . "<br>";
    if ($row->periodofinterest != '')
        $content .= '<strong>Soil PH</strong>: ' . $row->soil_ph . "<br>";
    if ($row->periodofinterest != '')
        $content .= '<strong>Soil Drainage</strong>: ' . $row->soil_drainage;

    return $content;
}

function GetStars($rate) {
    $stars = "";
    $count = 0;
    while ($rate > 0) {
        if ($rate == 0.5)
            $stars .= '<img src="/static/raty/img/star-half.png">&nbsp;';
        else if ($rate >= 1)
            $stars .= '<img src="/static/raty/img/star-on.png">&nbsp;';
        else
            break;
        $rate -= 1;
        $count++;
    }
    while ($count < 5) {
        $stars .= '<img src="/static/raty/img/star-off.png">&nbsp;';
        $count++;
    }
    return $stars;
}

function GetRequirementForm($id) {
    $query = mysql_query("SELECT * FROM requirement WHERE id='{$id}'");
    if (mysql_num_rows($query) == 0)
        return "";
    $row = mysql_fetch_object($query);

    $sun = "";
    $plant_type = "";
    if ($row->sun1 != '')
        $sun .= '<div class="rvs" ' . ($row->sun2 == "" ? ' style="margin-left:35px;" ' : "") . '>' . SunIcon($row->sun1) . ' <span>' . $row->sun1 . '</span></div>';
    if ($row->sun2 != '')
        $sun .= '<div class="rvs">' . SunIcon($row->sun2) . ' <span>' . $row->sun2 . '</span></div>';
    if ($row->sun3 != '')
        $sun .= '<div class="rvs" ' . ($row->sun4 == "" ? ' style="margin-left:35px;" ' : "") . '>' . SunIcon($row->sun3) . ' <span>' . $row->sun3 . '</span></div>';
    if ($row->sun4 != '')
        $sun .= '<div class="rvs">' . SunIcon($row->sun4) . ' <span>' . $row->sun4 . '</span></div>';

    if ($row->plant_type1 != '')
        $plant_type_ar[] = $row->plant_type1;
    if ($row->plant_type2 != '')
        $plant_type_ar[] = $row->plant_type2;
    if ($row->plant_type3 != '')
        $plant_type_ar[] = $row->plant_type3;
    $plant_type = implode(", ", $plant_type_ar);
    $content = '<table class="rtbl">
	
			' . ($row->hardiness != '' ? '<tr>
				<td class="rn">Hardiness Zones</td><td class="hdn">' . $row->hardiness . '</td>
			</tr>' : '') . '
			
			' . ($sun != '' ? '<tr>
				<td class="rn">Sun</td><td>' . $sun . '</td>
			</tr>' : '') . '
			
			' . ($plant_type != '' ? '<tr>
				<td class="rn">Plant Type</td><td>' . $plant_type . '</td>
			</tr>' : '') . '
			
			' . ($row->periodofinterest != '' ? '<tr>
				<td class="rn">Period Of Interest</td><td>' . $row->periodofinterest . '</td>
			</tr>' : '') . '
			
			' . ($row->difficulty != '' ? '<tr>
				<td class="rn">Difficulty</td><td>' . $row->difficulty . '</td>
			</tr>' : '') . '
			
			' . ($row->height != '' ? '<tr>
				<td class="rn">Height</td><td>' . $row->height . '</td>
			</tr>' : '') . '
			
			' . ($row->spacing != '' ? '<tr>
				<td class="rn">Spacing</td><td>' . $row->spacing . '</td>
			</tr>' : '') . '
			
			' . ($row->depth != '' ? '<tr>
				<td class="rn">Depth</td><td>' . $row->depth . '</td>
			</tr>' : '') . '
			
			' . ($row->feature != '' ? '<tr>
				<td class="rn">Features</td><td>' . $row->feature . '</td>
			</tr>' : '') . '
			
			' . ($row->use != '' ? '<tr>
				<td class="rn">Garden Uses</td><td>' . $row->use . '</td>
			</tr>' : '') . '
			
			' . ($row->water != '' ? '<tr>
				<td class="rn">Water</td><td>' . $row->water . '</td>
			</tr>' : '') . '
			
			' . ($row->maintain != '' ? '<tr>
				<td class="rn">Maintenance</td><td>' . $row->maintain . '</td>
			</tr>' : '') . '
			
			' . ($row->soil_type != '' ? '<tr>
				<td class="rn">Soil Type</td><td>' . $row->soil_type . '</td>
			</tr>' : '') . '
			
			' . ($row->soil_ph != '' ? '<tr>
				<td class="rn">Soil PH</td><td>' . $row->soil_ph . '</td>
			</tr>' : '') . '
			
			' . ($row->soil_drainage != '' ? '<tr>
				<td class="rn">Soil Drainage</td><td>' . $row->soil_drainage . '</td>
			</tr>' : '') . '
			
		</table>';

    return $content;
}

function GetCalculator($cname, $csize) {

    $content = '<div style="display:none;" id="newcalculator" title="Calculator">
		<table id="calculator_table">
		<tbody>
		<tr>
		<td style="text-align: center; padding-bottom: 12px;" colspan="2">
		<p style="color: #244da3;">Planting Calculator</p>
		(Approximate Quantities)
		' . (count(explode(";", $cname)) > 1 ? "<br><br>Suggested Combination Percentage - <span style='color:red;'>Feel free to change them as you wish!</span>" : "") . '
		</td>
		</tr>
		<tr>
		<td style="text-align: center;" colspan="2"></td>
		</tr>
		<tr>
		<td style="text-align: center;" colspan="2">
		<div id="calculator_combination">
		<table>
		<tbody>';
    foreach (explode(";", $cname) as $cnn) {
        $temp = explode("#", $cnn);
        $content .= '<tr>
		<td><label class="flowername">' . str_replace('\\', '\\\\', str_replace("'", '\'', $temp[0])) . '</label></td>
		<td><input class="calculatorvalue" type="text" value="' . ($temp[1] == "" ? "100" : $temp[1]) . '" />%</td>
		</tr>';
    }
    $content .= '</tbody>
		</table>
		</div></td>
		</tr>
		<tr>
		<td style="width: 240px; padding-left: 10px;">
		<div id="calculator_measure">Select your mesuring unit:</div></td>
		<td><select id="measure" name="measure"> <option value="foot">Foot</option> <option value="meter">Meter</option> </select></td>
		</tr>
		<tr>
		<td style="width: 240px; padding-left: 10px;">
		<div id="calculator_start">Select your planting shape\'s area:</div></td>
		<td><select id="shape" name="shape" onchange="showfield(this.options[this.selectedIndex].value)"> <option value=""></option> <option value="square">Square</option> <option value="triangle">Triangle</option> <option value="circle"> Circle</option> <option value="oval">Oval</option> </select></td>
		</tr>
		<tr id="calculator_square1" style="display: none;">
		<td style="width: 240px; padding-left: 10px;">Enter the avg length of the area:</td>
		<td><input id="calc_length" class="calculatorvalue" type="text" name="calc_length" /></td>
		</tr>
		<tr id="calculator_square2" style="display: none;">
		<td style="width: 240px; padding-left: 10px;">Enter the avg width of the area:</td>
		<td><input id="calc_width" class="calculatorvalue" type="text" name="calc_width" /></td>
		</tr>
		<tr id="calculator_circle" style="display: none;">
		<td style="width: 240px; padding-left: 10px;">Enter the avg radius of the area:</td>
		<td><input id="calc_radius" class="calculatorvalue" type="text" name="calc_radius" /></td>
		</tr>
		<tr>
		<td style="text-align: center;" colspan="2">
		<div id="calculator_result" style="display: none;"><input id="calculator_calculate" class="btn btn-primary" onclick="bulbCount(\'' . str_replace('\\', '\\\\', str_replace("'", '\'', $csize)) . '\')" type="button" value="Calculate" /></div></td>
		</tr>
		<tr>
		<td style="text-align: center;" colspan="2">
		<div id="calculator_results" style="display: block;"><input type="hidden" /></div></td>
		</tr>
		</tbody>
		</table>
		</div>';

    return $content;
}

function LoadCountry() {
    $country = "";
    $currency = "";

    if (isset($_SESSION["country"]))
        $country = $_SESSION["country"];
    if (isset($_COOKIE["country"])) {
        $country = $_COOKIE["country"];
        $_SESSION["country"] = $country;
    }
    //var_dump($country);exit;
    if ($country == "") {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_COOKIE["country"]))
            $country = $_COOKIE["country"];
        else
            $country = file_get_contents("http://api.hostip.info/country.php?ip=" . $ip);
        if ($country == "GB")
            $country = "UK";
        $_SESSION["country"] = $country;
        setcookie("country", $country, 0, "/");
    }

    if ($country == "" || $country == "XX")
        $country = "US";

    if ($country == "US")
        $currency = "$";
    elseif ($country == "UK" || $country == "GB")
        $currency = "&pound;";
    else
        $currency = "&euro;";

    define('USER_REGION', $country);
    define('CURRENCY', $currency);
}

function is_domain_in_array($string, $array) {
    preg_match('@^(?:https?://)?([^/]+)@i', $string, $matches);
    $host = $matches[1];
    $host = str_replace('www.', '', $host);
    preg_match('/(.*)?(?=\.)\.[a-z]+$/i', $host, $matches);
    $string1 = $matches[1];
    $string2 = $matches[0];
    preg_match('/[^\.]*/i', $host, $matches);
    $string3 = $matches[0];

    $value = false;
    foreach ($array as $element) {
        if (strtolower(trim($element)) == strtolower(trim($string1)) || strtolower(trim($element)) == strtolower(trim($string2)) || strtolower(trim($element)) == strtolower(trim($string3))) {
            return true;
        }
    }
    return false;
}

function GetPaging($page, $limit, $query, $adjacents, $targetpage) {
    /*
      First get total number of rows in data table.
      If you have a WHERE clause in your query, make sure you mirror it here.
     */
    $total_pages = mysql_fetch_array(mysql_query($query));
    $total_pages = $total_pages["counter"];
    //var_dump($adjacents);
    /* Setup vars for query. */
    if ($page)
        $start = ($page - 1) * $limit;    //first item to display on this page
    else
        $start = 0;        //if no page var is given, set start to 0

        /* Setup page vars for display. */
    if ($page == 0)
        $page = 1;     //if no page var is given, default to 1.
    $prev = $page - 1;       //previous page is page - 1
    $next = $page + 1;       //next page is page + 1
    $lastpage = ceil($total_pages / $limit);  //lastpage is = total pages / items per page, rounded up.
    $lpm1 = $lastpage - 1;      //last page minus 1

    /*
      Now we apply our rules and draw the pagination object.
      We're actually saving the code to a variable in case we want to draw it more than once.
     */
    $pagination = "";
    if (strpos($targetpage, "?") === FALSE)
        $urllink = "?";
    else
        $urllink = "&";
    if ($lastpage > 1) {
        $pagination .= "<div class=\"pagination\">";
        //previous button
        if ($page > 1)
            $pagination.= "<a href=\"$targetpage" . $urllink . "paging_page=$prev\">previous</a>";
        else
            $pagination.= "<span class=\"disabled\">previous</span>";

        //pages	
        if ($lastpage < 7 + ($adjacents * 2)) { //not enough pages to bother breaking it up
            for ($counter = 1; $counter <= $lastpage; $counter++) {
                if ($counter == $page)
                    $pagination.= "<span class=\"current\">$counter</span>";
                else
                    $pagination.= "<a href=\"$targetpage" . $urllink . "paging_page=$counter\">$counter</a>";
            }
        }
        elseif ($lastpage > 5 + ($adjacents * 2)) { //enough pages to hide some
            //close to beginning; only hide later pages
            if ($page < 1 + ($adjacents * 2)) {
                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                    if ($counter == $page)
                        $pagination.= "<span class=\"current\">$counter</span>";
                    else
                        $pagination.= "<a href=\"$targetpage" . $urllink . "paging_page=$counter\">$counter</a>";
                }
                $pagination.= "...";
                $pagination.= "<a href=\"$targetpage" . $urllink . "paging_page=$lpm1\">$lpm1</a>";
                $pagination.= "<a href=\"$targetpage" . $urllink . "paging_page=$lastpage\">$lastpage</a>";
            }
            //in middle; hide some front and some back
            elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                $pagination.= "<a href=\"$targetpage" . $urllink . "paging_page=1\">1</a>";
                $pagination.= "<a href=\"$targetpage" . $urllink . "paging_page=2\">2</a>";
                $pagination.= "...";
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                    if ($counter == $page)
                        $pagination.= "<span class=\"current\">$counter</span>";
                    else
                        $pagination.= "<a href=\"$targetpage" . $urllink . "paging_page=$counter\">$counter</a>";
                }
                $pagination.= "...";
                $pagination.= "<a href=\"$targetpage" . $urllink . "paging_page=$lpm1\">$lpm1</a>";
                $pagination.= "<a href=\"$targetpage" . $urllink . "paging_page=$lastpage\">$lastpage</a>";
            }
            //close to end; only hide early pages
            else {
                $pagination.= "<a href=\"$targetpage" . $urllink . "paging_page=1\">1</a>";
                $pagination.= "<a href=\"$targetpage" . $urllink . "paging_page=2\">2</a>";
                $pagination.= "...";
                for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                    if ($counter == $page)
                        $pagination.= "<span class=\"current\">$counter</span>";
                    else
                        $pagination.= "<a href=\"$targetpage" . $urllink . "paging_page=$counter\">$counter</a>";
                }
            }
        }

        //next button
        if ($page < $counter - 1)
            $pagination.= "<a href=\"$targetpage" . $urllink . "paging_page=$next\">next</a>";
        else
            $pagination.= "<span class=\"disabled\">next</span>";
        $pagination.= "</div>\n";
    }

    return $pagination;
}

?>