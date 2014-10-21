<?php

require_once "config.php";
require_once "lib/phpfastcache/phpfastcache.php";
phpFastCache::$storage = "auto";

$view = "";
if (isset($_GET["view"]))
    $view = $_GET["view"];
else
    $view = "home";


$temp = pathinfo(__FILE__);
define("CURRENT_VIEW", $view);
define("CURRENT_CONTROLLER", $temp["filename"]);
$funcview = "controller_" . $view;
$funcview = str_replace("-", "_", $funcview);

//
if (function_exists($funcview))
    call_user_func($funcview);
else {
	if (file_exists($view.".html"))
		die (file_get_contents($view.".html"));
	else {
		controller_404();
	}
}

function controller_404() {
    header("HTTP/1.0 404 Not Found");
    include "view/home/header.php";
    include "view/home/404.php";
    include "view/home/footer.php";
}

function controller_GetMerchantList() {
    $content = "";
    $data = mysql_query("SELECT * FROM allow_merchant WHERE country <> '' ORDER BY last_scraped LIMIT 10") or die("ERROR 8999:" . mysql_error());
    while ($row = mysql_fetch_object($data)) {
        $content .= $row->id . ",{$row->merchant},{$row->country};";
    }
    $content = trim($content, ", ;");
    echo $content;
}


function controller_replaceInvalidSymbols($str)
{
   $ret = $str;
  // $ret = str_replace("—","-",$str);
   return $ret;
}

function controller_DownloadMerchantProducts() {

    $test = $_GET["test"];
    if (isset($_GET["mid"]))
        $data = mysql_query("SELECT * FROM allow_merchant WHERE country <> '' AND id='" . $_GET["mid"] . "' ORDER BY last_scraped LIMIT 1") or die("ERROR 8979:" . mysql_error());
    else
        $data = mysql_query("SELECT * FROM allow_merchant WHERE country <> '' ORDER BY last_scraped LIMIT 1") or die("ERROR 8999:" . mysql_error());
    $row = mysql_fetch_object($data);


    $countries = array(".com", ".co.uk", ".fr", ".com.au", ".de", ".it", ".nl", ".es", ".ch");
    $countryCodes = array("us", "uk", "fr", "au", "de", "it", "nl", "es", "ch");
    if ($row === false)
        die("Nothing to scrape");
    if (isset($_GET["test"])) {
        $row->country = $_GET["country"];
    }
    $countryindex = array_search($row->country, $countryCodes);
    $code = $countries[$countryindex];
    $from = 0;
    $content = "";
    SaveSetting("last_scraped_time", time());

    $allow_domains = array();
    $resultDomains = mysql_query("SELECT * FROM allow_merchant");
    while ($drow = mysql_fetch_object($resultDomains)) {
        $temp = explode(" ", $drow->merchant);
        $searching_domain = $temp[0];
        $searching_domain = strtolower(trim($searching_domain, " /"));
        $searching_domain = str_replace("www.", "", $searching_domain);
        $searching_domain = str_replace("https://", "", $searching_domain);
        $searching_domain = str_replace("http://", "", $searching_domain);
        $allow_domains[] = $searching_domain;
    }


    while (true) {

        $domain = "https://www.google{$code}";

        if ($row->seller_id != '')
            $searched_url = "{$domain}/search?num=100&tbm=shop&tbs=seller:" . $row->seller_id . "&q=" . urlencode($row->merchant) . "&start={$from}";
        else
            $searched_url = "{$domain}/search?num=100&tbm=shop&q=" . urlencode($row->merchant) . "&start={$from}";

        echo $searched_url;

        $from += 100;


        $tuCurl = curl_init();
        curl_setopt($tuCurl, CURLOPT_URL, $searched_url);
        curl_setopt($tuCurl, CURLOPT_COOKIEFILE, "cookie.txt"); //saved cookies
        curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($tuCurl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.17 Safari/537.36');
        $backupfile = md5($searched_url) . ".html";

            $newcontent = curl_exec($tuCurl);
            $responsecode = curl_getinfo($tuCurl, CURLINFO_HTTP_CODE);
            curl_close($tuCurl);
            echo " " . count($newcontent) . " {$responsecode}<br>";

            if ($responsecode == 503 || $responsecode == 302) {
       //         SaveSetting("last_scraped_result", "blocked");

                $tuCurl = curl_init();

                curl_setopt($tuCurl, CURLOPT_FOLLOWLOCATION, TRUE);
                curl_setopt($tuCurl, CURLOPT_URL, $searched_url);
                curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($tuCurl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.17 Safari/537.36');
                $page = curl_exec($tuCurl);
                curl_close($tuCurl);


                preg_match('/<img src="(.*?)"/is', $page, $img);

                $tuCurl = curl_init();



                curl_setopt($tuCurl, CURLOPT_FOLLOWLOCATION, TRUE);
                curl_setopt($tuCurl, CURLOPT_COOKIEJAR, "cookie.txt");
                curl_setopt($tuCurl, CURLOPT_URL, "http://www.google.com".$img[1]);
                curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($tuCurl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.17 Safari/537.36');
                $data =  curl_exec($tuCurl);
                curl_close($tuCurl);

               $fp = fopen("captchaImg",'w');
               fwrite($fp, $data); 
               fclose($fp);             
                 
 
                 $page = str_replace("CaptchaRedirect", "CaptchaRedirect.php", $page);  
                 $page = str_replace("/sorry/image", "captchaImg", $page);  
                 echo $page;
                 die(-1);
            
            //file_put_contents("htmlbackups/".$backupfile, $newcontent);
        }
        $content .= " " . $newcontent;

//if($from >= 400){break;}




        if (strpos($newcontent, ">Next<") === false && strpos($newcontent, ">Suivant<") === false && strpos($newcontent, ">Weiter<") === false && strpos($newcontent, ">Avanti<") === false && strpos($newcontent, ">Volgende<") === false && strpos($newcontent, ">Siguiente <") === false || $from >= 1000) {
            echo "no more result";
            break;
        }
    }

    if (isset($_GET["debug"]) || isset($_GET["test"]))
        var_dump($content);
    
   //$content=file_get_contents("temp_scrap.html");
   file_put_contents("temp_scrap.html",$content);

    $pattern = "/_image_src='(.*?)';var _image_ids=\['(.*?)'/";
    preg_match_all($pattern, $content, $matches);


    $res = array();
    preg_match_all('/<li class="g psli">.*?(?=<\/li>)<\/li>/is', $content, $blocks);
    //var_dump($content);exit;
    if (count($blocks[0]) == 0)
        preg_match_all('/<li class="g">.*?(?=<\/li>)<\/li>/is', $content, $blocks);
    if (count($blocks[0]) == 0)
        preg_match_all('/<li class="g psli"[^>]*>.*?(?=<\/li>)<\/li>/is', $content, $blocks);
    if (count($blocks[0]) == 0)
        preg_match_all('/<li class="g"[^>]*>.*?(?=<\/li>)<\/li>/is', $content, $blocks);
    if (count($blocks[0]) == 0) {
        mysql_query("UPDATE allow_merchant SET last_scraped='" . time() . "' WHERE id='{$row->id}'") or die("ERROR 3003:" . mysql_error());
        SaveSetting("last_scraped_result", "0 found items");
        mysql_query("DELETE FROM google_scraped2 WHERE merchant_id='{$row->id}'") or die("ERROR 3004:" . mysql_error());
        die("<br>not found block");
    }

    



  //  var_dump(count($blocks[0]));exit;
    foreach ($blocks[0] as $block) {
        preg_match('/<img(.*?)id="(.*?)"/is', $block, $match);
        $image = $match[2];
        $key = array_search($image, $matches[2]);
        if($key === "")
         {
           $image= "";

         }
         else
         {
           $image= $matches[1][$key];
           $image = str_replace('\75','', $image);
           $image = str_replace('\075','', $image);
         }

        preg_match('/alt="([^"]*)"/is', $block, $match);
        $title = $match[1];
        $title = str_replace("&lt;b&gt;", "", $title);
        $title = str_replace("&lt;/b&gt;", "", $title);
        $title = preg_replace('/<[^>]*>/is', "", $title);

        preg_match('/"psliprice">(.*?(?=<\/div>))/is', $block, $match);
        preg_match('/[0-9,\.]+/is', $match[1], $match);
        $price = $match[0];
        if ($price == "") {
            preg_match('/"psmkprice">(.*?(?=<\/div>))/is', $block, $match);
            preg_match('/[0-9,\.]+/is', $match[1], $match);
            $price = $match[0];
        }


        if ($price == "") { 
            preg_match('/"_Am">(.*?(?=<\/b>))/is', $block, $match);
            preg_match('/[0-9,\.]+/is', $match[1], $match);
            $price = $match[0];
        }

     if ($price == "") { 
            preg_match('/"_vm">(.*?(?=<\/b>))/is', $block, $match);
            preg_match('/[0-9,\.]+/is', $match[1], $match);
            $price = $match[0];
        }

     if ($price == "") { 
            preg_match('/"price">(.*?(?=<\/b>))/is', $block, $match);
            preg_match('/[0-9,\.]+/is', $match[1], $match);
            $price = $match[0];
        }

        $price = str_replace(",", ".", $price);

    
        preg_match('/<cite>([^<]*)/is', $block, $match);
        $merchant_name = $match[1];
        if ($merchant_name == "") {
            preg_match('/>\s*from\s*([^<]*)</is', $block, $match);
            $merchant_name = $match[1];
        }
        if ($merchant_name == "") {
            preg_match('/>\s*chez\s*([^<]*)</is', $block, $match);
            $merchant_name = $match[1];
        }
       
        if($merchant_name == "")
        {

            preg_match('/"_ur" href="(.*?)">(.*?(?=<\/a>))/is', $block, $match);
            $merchant_name= $match[2];
             //echo $description;

        } 



echo "xx " . $merchant_name;
//exit;

        preg_match('/<a href="(\/aclk\?[^"]*)/is', $block, $match);
        preg_match('/adurl=([^"]*)/is', $match[1], $match);
        if ($match[1] == "")
            preg_match('/href="([^"]*)/is', $block, $match);
        $merchant_site = trim($match[1]);
        
        $pos = strrpos($merchant_site, "http");

        if ($pos !== false) {
          $merchant_site =  substr($merchant_site, $pos); 
        }

        preg_match('/"psst">(.*?)<\/div>/is', $block, $match);
        $description = $match[1];
       
        if($description == "")
        {
            preg_match('/"_ZL">(.*?(?=<\/div>))/is', $block, $match);
            $description = $match[1];
             //echo $description;

        } 


 if($description == "")
        {
            preg_match('/"_NQ">(.*?(?=<\/div>))/is', $block, $match);
            $description = $match[1];
             //echo $description;

        } 

   if($description == "")
        {
            preg_match('/"_HO">(.*?(?=<\/div>))/is', $block, $match);
            $description = $match[1];
             //echo $description;

        } 


if($description == "")
        {
            preg_match('/"_HS">(.*?(?=<\/div>))/is', $block, $match);
            $description = $match[1];
             //echo $description;

        } 

        preg_match('/srpresultimg_([0-9]*)/is', $block, $match);
        $id = $match[1];

        if (is_domain_in_array($merchant_site, $allow_domains) == false ||
                strpos($merchant_site, "/shopping/") === 0)
        {

                          preg_match('/data\-href="([^"]*)/is', $block, $match);
                         $merchant_site = trim($match[2]);

                 if (is_domain_in_array($merchant_site, $allow_domains) == false ||
                strpos($merchant_site, "/shopping/") === 0)
                  { 
 
                     continue;
                  }
        } 


        $res[] = array( 
            "image" => $image,
            "title" => mysql_real_escape_string($title),
            "price" => $price,
            "description" => mysql_real_escape_string($description),
            "merchant_name" => mysql_real_escape_string($merchant_name),
            "merchant_site" => mysql_real_escape_string($merchant_site),
            "id" => md5($title . $row->country . $merchant_name)
        );



   }

 foreach ($res as $r) {
//  echo $r["title"]."<br/>"; 
}


    SaveSetting("last_scraped_result", count($res) . " found items");
    mysql_query("UPDATE allow_merchant SET last_scraped='" . time() . "' WHERE id='{$row->id}'") or die("ERROR 3003:" . mysql_error());
    mysql_query("DELETE FROM google_scraped2 WHERE merchant_id='{$row->id}'") or die("ERROR 3004:" . mysql_error());
    foreach ($res as $r) {
  // echo $r["merchant_name"]."<br/>";
        mysql_query("REPLACE INTO google_scraped2 SET picture='" . $r["image"] . "', 
				name='" . $r["title"] . "', price='" . $r["price"] . "', country='{$row->country}', 
				merchant_name='" . $r["merchant_name"] . "', 
				description='" . $r["description"] . "', 
				merchant_id='{$row->id}',
				site_url='" . $r["merchant_site"] . "', google_item_id='" . $r["id"] . "'") or die("ERROR 3005:" . mysql_error());
    }

$data = mysql_query("SELECT COUNT(*) FROM google_scraped2 WHERE merchant_id='{$row->id}'") or die("ERROR 3004:" . mysql_error());
$numItems = mysql_result($data,0);


    echo "<br>inserted " . $numItems . " items";
}

function controller_home() {
    $dataslider = mysql_query("SELECT * FROM sliderimage WHERE enable=1");
    $page_title = "Jardins Sans Secret, Gardens have no more secrets";
    include "view/home/header.php";
    include "view/product/home.php";
    include "view/home/footer.php";
}

function controller_sitemap() {
    require "lib/Sitemap.php";
    $sitemap = new Sitemap('https://www.jardins-sans-secret.com');
    $sitemap->setPath('');
    $sitemap->setFilename('sitemap');
    $data = mysql_query("SELECT * FROM product WHERE status=1 AND publishing_date < '" . time() . "'");
    while ($row = mysql_fetch_object($data)) {
        $sitemap->addItem(url_product_detail($row->id), '1.0', "monthly", $row->last_modified);
    }
    /* $data = mysql_query("SELECT * FROM product_category");
      while ($row = mysql_fetch_object($data))
      {
      $sitemap->addItem(url_category($row->id), '1.0');
      }
      $data = mysql_query("SELECT * FROM cms_page WHERE status=1");
      while ($row = mysql_fetch_object($data))
      {
      $sitemap->addItem(url_cms($row->id), '1.0');
      } */
    $sitemap->createSitemapIndex('https://www.jardins-sans-secret.com/', 'Today');
}

function controller_alllinks() {
    $data = mysql_query("SELECT * FROM product WHERE status=1 AND publishing_date < '" . time() . "'");
    while ($row = mysql_fetch_object($data)) {
        echo "https://jardins-sans-secret.com" . url_product_detail($row->id) . "<br>";
    }
    $data = mysql_query("SELECT * FROM product_category WHERE visible=1");
    while ($row = mysql_fetch_object($data)) {
        echo "https://jardins-sans-secret.com" . url_category($row->id) . "<br>";
    }
    $data = mysql_query("SELECT * FROM cms_page WHERE status=1");
    while ($row = mysql_fetch_object($data)) {
        echo "https://jardins-sans-secret.com" . url_cms($row->id) . "<br>";
    }
}

function controller_redirectcategoryoldsite() {
    $p = explode("/", $_SERVER["REQUEST_URI"]);
    $length = count($p);
    $length--;
    $cat_name = $p[$length];
    if ($cat_name == "") {
        $length--;
        $cat_name = $p[$length];
    }
    $cat_name = str_replace("-", " ", $cat_name);
    $cat_name = str_replace(" ", "%", $cat_name);
    $cat_name = "%" . $cat_name . "%";
    $data = mysql_query("SELECT * FROM product_category WHERE category_name LIKE '{$cat_name}' AND visible=1");
    $row = mysql_fetch_object($data);
    //var_dump($row);
    if ($row == null || $row == false) {
       controller_404();
    } else 
        header("Location: " . url_category($row->id)); 
}

function controller_redirectoldsite() {
    $p2 = trim($_GET["p2"]);
    $p2 = str_replace("-", " ", $p2);
    $p2 = str_replace(" ", "%", $p2);
    $data = mysql_query("SELECT * FROM product WHERE name LIKE '%{$p2}%'");
    $row = mysql_fetch_object($data);
    //var_dump($row);
    if ($row == null || $row == false) {
           controller_404();
    } else
        header("Location: " . url_product_detail($row->id));
    die();
}

function controller_detail() {


    $id = mysql_real_escape_string($_GET["id"]);
    //redirect old site
    $url = $_SERVER["REQUEST_URI"];
    preg_match('/\/([^\/]+)\.html/', $url, $match);
    $title = $match[1];
    if (strpos($title, "--") !== false || strpos($title, "_") !== false) {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: " . url_product_detail($id));
        die();
    }

    if($url != url_product_detail($id))
     {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: " . url_product_detail($id));
        die();
     }

    ////////////

    if ($_SESSION['isadmin'] === true)
        $query = mysql_query("SELECT * FROM product WHERE id='{$id}'") or die(mysql_error());
    else
        $query = mysql_query("SELECT * FROM product WHERE id='{$id}' AND status=1 AND publishing_date < '" . time() . "'") or die(mysql_error());
    if (mysql_num_rows($query) == 0)
        die("No product");
    $p = mysql_fetch_object($query);

    if (SSL) {
        $p->description = str_replace("http://www.jardins-sans-secret.com", "https://www.jardins-sans-secret.com", $p->description);
        $p->description = str_replace("http://jardins-sans-secret.com", "https://www.jardins-sans-secret.com", $p->description);
    }

    $images = array();
    $query = mysql_query("SELECT * FROM product_images WHERE product_id='{$id}' ORDER BY ordering");
    while ($r = mysql_fetch_object($query))
        $images [] = array("name" => $r->name,
            "caption" => $r->caption,
            "alt" => $r->alter_text);

    mysql_query("UPDATE product SET view_count = view_count+1 WHERE id='{$id}'") or die(mysql_error());

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

    $seo_description = $p->seo_description;
    $seo_keywords = $p->seo_keyword;

    $seo_description = preg_replace('/[^\x00-\x7F]+/', '', $seo_description);
    $seo_keywords = preg_replace('/[^\x00-\x7F]+/', '', $seo_keywords);
    
    
    $page_title = $p->name . " - Jardins Sans Secret";
    $p->description = preg_replace('/<img /is', '<img itemprop="image" ', $p->description);
    
   if(SSL)
    {
      $canonicalURL = "https://www.jardins-sans-secret.com/?view=detail&id={$id}";
    }
   else
   {
      $canonicalURL = "http://www.jardins-sans-secret.com/?view=detail&id={$id}";
   }
  
    $q2 = mysql_query("SELECT count(*) as total FROM `product_in_category` WHERE category_id in(1179, 922,934) and product_id=$p->id");
    $r2 = mysql_fetch_object($q2);
    
    if($r2->total == 1) 
     {
         $page_title = $p->name;
     }
     else
     {$page_title = $p->name . " - Jardins Sans Secret";
      }
    


    $reviewLists = mysql_query("SELECT * FROM user_rating WHERE product_id='{$p->id}' AND status=1");
    //require_once "lib/disqusapi/disqusapi.php";
    //require('disqusapi/disqusapi.php');
    //$disqus = new DisqusAPI("Zs2ahoBG5FAkucTma1p8MSrBX95um77LQsbZrd3pbITIOOJuKlb4y9vV3ICkK1h2");

    $script = array();
    $css = array();

    $script[] = '/static/carouFredSel/jquery.carouFredSel-6.2.0-packed.js.php';
    $script[] = '/static/lightbox/jquery.lightbox-0.5.pack.js.php';
    $script[] = '/static/jquery.imgreview.js.php';
    $script[] = '/static/raty/jquery.raty.min.js.php';
    $css[] = '/static/lightbox/jquery.lightbox-0.5.css.php';
 
   
    include "view/home/header.php";
    //include "view/home/sidebar.php";
    include "view/product/detail.php";
    include "view/home/footer.php";
}

function controller_cms() {

    if ($_POST["smContactForm"]) {
        require_once 'static/securimage/securimage.php';
        $securimage = new Securimage();
        if ($securimage->check($captcha)) {
            //if (1==1)
            $content = "";
            foreach ($_POST as $k => $v) {
                if ($k == 'smContactTitle')
                    $subject = $v;
                if ($k == 'smSuccess')
                    $succ = $v;

                if ($k != 'smContactForm' && $k != 'smContactTitle' && $k != 'smSuccess') {
                    $title = $k;
                    $title = str_replace("_", " ", $title);
                    $title = ucfirst($title);
                    $content .= "<strong>{$title}</strong>: {$v}<br>";
                }
            }
            $to = ADMIN_EMAIL; //"ngochoanghcm@gmail.com";

            $headers = "From: " . $_POST["email"];
            $semi_rand = md5(time());
            $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
            $headers .= "\nMIME-Version: 1.0\n" .
                    "Content-Type: multipart/mixed;\n" .
                    " boundary=\"{$mime_boundary}\"";

            $message = "This is a multi-part message in MIME format.\n\n";
            $message.="--{$mime_boundary}\n";
            $message.="Content-Type: text/html; charset=\"iso-8859-1\"\n";
            $message.="Content-Transfer-Encoding: 7bit\n\n";

            $message.=$content;


            for ($i = 0; $i < count($_FILES['attachment']['name']); $i++) {
                $fileatt = $_FILES['attachment']['tmp_name'][$i];
                $fileatt_type = $_FILES['attachment']['type'][$i];
                $fileatt_name = $_FILES['attachment']['name'][$i];

                if (is_uploaded_file($fileatt) && $fileatt_name != "") {
                    $file = fopen($fileatt, 'rb');
                    $data = fread($file, filesize($fileatt));
                    fclose($file);
                    $data = chunk_split(base64_encode($data));
                    $message .= "\n\n--{$mime_boundary}\n" .
                            "Content-Type: {$fileatt_type};\n" .
                            " name=\"{$fileatt_name}\"\n" .
                            //"Content-Disposition: attachment;\n" .
                            //" filename=\"{$fileatt_name}\"\n" .
                            "Content-Transfer-Encoding: base64\n\n" .
                            $data;
                }
            }
            $message .= "\n\n--{$mime_boundary}\n";
            //echo "<pre>";echo $message;echo "</pre>";exit;
            if (!mail($to, $subject, $message, $headers))
                $error = "Email cannot be sent. Please try again later.";
            else
                $info = $succ;
        }
        else {
            $error = "Security code is not correct";
        }
    }

    $id = mysql_real_escape_string($_GET["id"]);
    $data = mysql_query("SELECT * FROM cms_page WHERE id='{$id}'");
    $rcms = mysql_fetch_object($data);

    $page_title = $rcms->title . " - Jardins Sans Secret, Gardens have no more secrets";
     
    
    // KEYWORDS AND DESCRIPTIONS - CMS
    if($id == 1)
     {
       // About CMS Page
       $seo_description="This site is for beginners, amateurs, as well as professionals who may be looking for inspiration or wish to share their gardening or horticultural projects and experiences with others.";
       $seo_keywords="garden ideas, landscaping ideas, plants, perennials, annuals, roses, flowers, bulbs, grasses, border ideas, plant combination ideas";
     }
     else if($id == 2) 
     {
       // Terms & Conditions CMS Page
       $seo_description="The site and related content and services are provided subject to these Terms and Conditions. Please read the following information carefully. Your continued use of the site will indicate your agreement to be bound by the Terms and Conditions set forth below. If you do not agree to these Terms and Conditions, promptly exit the site.";
       $seo_keywords="garden ideas, landscaping ideas, plants, perennials, annuals, roses, flowers, bulbs, grasses, border ideas, plant combination ideas";

     }
     else if($id == 3) 
     {
       // Privacy Policy CMS Page
       $seo_description="Jardins Sans Secret respects our customers and understands that you are concerned about privacy. We have posted this Privacy Policy to let you know what kind of information we collect, how it is handled and with whom it may be shared.";
       $seo_keywords="garden ideas, landscaping ideas, plants, perennials, annuals, roses, flowers, bulbs, grasses, border ideas, plant combination ideas";

     }
     else if($id == 4) 
     {
       // Contact us CMS Page
       $seo_description="We would love to hear from you! Your feedback is very important to us in order that we can continue to improve our Website and service to you, the user! ";
       $seo_keywords="garden ideas, landscaping ideas, plants, perennials, annuals, roses, flowers, bulbs, grasses, border ideas, plant combination ideas";

     }  
     else if($id == 5) 
     {
       // Join us CMS Page
       $seo_description="Do you wish Jardins Sans Secret to feature your products and enable shoppers, who visit Jardins Sans Secret, to shop directly from your website ? If so, then to join us is pretty simple and will require just a few minutes of your time. ";
       $seo_keywords="garden ideas, landscaping ideas, plants, perennials, annuals, roses, flowers, bulbs, grasses, border ideas, plant combination ideas";

     }

    $rcms->content = str_replace("[SECURITY_CODE]", '<img style="border: 1px solid #000; margin-right: 15px;" src="/static/securimage/securimage_show.php?sid=' . md5(uniqid()) . '" alt="CAPTCHA Image"/>                <input type="text" required name="securitycode" style="width:200px;" class="form-control" id="securitycode" placeholder="Enter security code"><br>', $rcms->content);

    include "view/home/header.php";
    include "view/home/cms.php";
    include "view/home/footer.php";
}

function controller_contactus() {
    $page_title = "Contact Us - Jardins Sans Secret, Gardens have no more secrets";
    include "view/home/header.php";
    include "view/home/contactus.php";
    include "view/home/footer.php";
}

function controller_page() {
    $id = mysql_real_escape_string($_GET["id"]);
    //redirect old site
    $url = $_SERVER["REQUEST_URI"];
    preg_match('/\/([^\/]+)\.html/', $url, $match);
    $title = $match[1];
    if (strpos($title, "--") !== false || strpos($title, "_") !== false) {
        header("HTTP/1.1 301 Moved Permanently");
        if ($id == "gi" || $id == "bs" || $id == "pr" || $id == "pl" || $id == "ds")
            header("Location: " . url_category_type($id));
        else
            header("Location: " . url_category($id));
        die();
    }
    ////////////

    unset($_SESSION["selected_category"]);
    $leftpanelgroup = $id;
    $catdata = mysql_query("SELECT * FROM product_category WHERE id='{$id}' AND visible=1");
    $cat = mysql_fetch_object($catdata);
    $catcdata = mysql_query("SELECT * FROM product_category WHERE category_type='{$id}' AND visible=1 AND parent_id=0");
    if (mysql_num_rows($catcdata) == 1) {
        $row = mysql_fetch_object($catcdata);
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: " . url_category($row->id));
        die();
    }

    switch ($id) {
        case "gi":
            $title = "Category: Garden Ideas";
            break;
        case "bs":
            $title = "Category: Basics";
            break;
        case "pr":
            $title = "Category: Promenades";
            break;
        case "pl":
            $title = "Category: Plants";
            break;
        case "ds":
            $title = "Category: Designers";
            break;
    }
    $page_title = $title . " - Jardins Sans Secret, Gardens have no more secrets";

    include "view/home/header.php";
    //include "view/home/sidebar.php";
    include "view/product/category_menu.php";
    include "view/product/group.php";
    include "view/home/footer.php";
}

function controller_changecountry() {
    $country = $_POST["country"];
    $_SESSION["country"] = $country;
    setcookie("country", $country, 0, "/");
    //var_dump($country);exit;
    //header("Location: /");
    die();
}

function controller_autocompletesearch() {
    mysql_query("SET NAMES 'utf8'");
    $k = mysql_real_escape_string($_GET["term"]);
    $data = mysql_query("SELECT DISTINCT name FROM product WHERE name LIKE '%{$k}%' AND status=1 AND publishing_date < '" . time() . "' ORDER BY name");
    $res = array();
    while ($r = mysql_fetch_object($data)) {
        $res[] = array(
            "id" => $r->name,
            "label" => $r->name,
            "value" => $r->name
        );
    }
    echo json_encode($res);
}

function controller_category() {
    $id = mysql_real_escape_string($_GET["id"]);

    //echo url_category_type($id);exit;
    //redirect old site
    $url = $_SERVER["REQUEST_URI"];
    preg_match('/\/([^\/]+)\.html/', $url, $match);
    $title = $match[1];
    //var_dump($url);
    //var_dump($match);exit;
    if (strpos($title, "--") !== false || strpos($title, "_") !== false) {
        header("HTTP/1.1 301 Moved Permanently");
        if ($id == "gi" || $id == "bs" || $id == "pr" || $id == "pl" || $id == "ds")
            header("Location: " . url_category_type($id));
        else
            header("Location: " . url_category($id));
        die();
    }
    ////////////

    $where = "";
    $selected_category = $_SESSION["selected_category"];

    $pagesize = mysql_real_escape_string($_GET["ps"]);
    $from = mysql_real_escape_string($_GET["f"]);
    if ($from == '')
        $from = '0';
    if ($pagesize == '')
        $pagesize = '8';

    if (!isset($_GET["append"])) {
        $selected_category = array();
    }
    if (isset($_GET["delone"])) {
        $selected_category = $_SESSION["selected_category"];
    }

    $cid = $id;
    /* do
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
      while (true); */
    $where3 = "";
    if (isset($_GET["clearall"])) {
        $selected_category = array();
        //$selected_category[] = $id;
        $where3 = " pic.category_id IN (" . $id . ") ";
    } elseif (isset($_GET["delone"])) {
        $newl = array();
        foreach ($selected_category as $i) {
            if ($i != $id)
                $newl[] = $i;
        }
        $selected_category = $newl;
    }
    else {
        if (in_array($cid, $selected_category) == false)
            $selected_category[] = $cid;
    }

    $selected_property_root = array();
    foreach ($selected_category as $c) {
        $cc = $c;
        while (true) {
            $dataprt = mysql_query("SELECT * FROM product_category WHERE id={$cc} AND visible=1");
            $row = mysql_fetch_object($dataprt);
            if ($row == null || $row == false)
                break;
            //if ($row->parent_id == 0)
            //{
            if (!in_array($row->id, $selected_property_root))
                if ($row->id != PLANT_IDEAS_ID)
                    $selected_property_root[] = $row->id;
            if ($row->parent_id == 0) {
                $selected_category_root = $row->id;
                break;
            }
            //}
            $cc = $row->parent_id;
        }
    }
    $_SESSION["selected_category"] = $selected_category;


    if ($id == "search") {

        $k = mysql_real_escape_string($_GET["k"]);
        $k = trim($k);
        $page_title = "{$k} - Jardins Sans Secret, Gardens have no more secrets";
        if ($k != '') {
            $sk = preg_replace('/\s+/', ' ', $k);
            $sk = "%" . preg_replace('/\s/', '%', $sk) . "%";
            $sk1 = $sk;
            $sk = preg_replace('/[^a-z]/is', '%', $k);
        }

        $where2 = " p.name LIKE '{$sk}' OR p.common_name LIKE '{$sk}' OR p.description LIKE '{$sk1}' ";
        $_SESSION["selected_category"] = array();

        /*
          mysql_query("INSERT IGNORE INTO searched_keyword SET keyword='{$k}'");
          mysql_query("UPDATE searched_keyword SET count = count+1 WHERE keyword='{$k}'");

          include "view/home/header.php";
          include "view/product/category_menu.php";
          include "view/product/googlesearch.php";
          include "view/home/footer.php";
          return; */
    } else {
        $id = $selected_category[0];
        $catdata = mysql_query("SELECT * FROM product_category WHERE id='{$id}' AND visible=1 ORDER BY ordering");
        $cat = mysql_fetch_object($catdata);
        $leftpanelgroup = $cat->category_type;
        $catcdata = mysql_query("SELECT * FROM product_category WHERE parent_id='{$id}' AND visible=1 ORDER BY ordering");
        //$where = " pic.category_id='{$cat->id}' ";
        if (count($selected_category) > 0)
            $where3 = " pic.category_id IN (" . implode(",", $selected_category) . ") ";
    }

    if ($where3 != '') {
        $where3 = " AND ($where3) ";
        $q = "SELECT count(p.name) AS counter, p.* FROM product AS p
			INNER JOIN product_in_category AS pic ON p.id=pic.product_id
			WHERE p.status=1 AND p.publishing_date < '" . time() . "' {$where3}
			GROUP BY p.name HAVING counter >= " . count($selected_category) . "
			ORDER BY p.name";
    } else if ($where2 != '')
        $q = "SELECT DISTINCT p.* FROM product AS p
			INNER JOIN product_in_category AS pic ON p.id=pic.product_id
			WHERE p.status=1 AND p.publishing_date < '" . time() . "' AND ({$where2})
			ORDER BY p.name";
    //echo $q;
    if ($q != '') {
        $query_code = md5($where3 . $where2 . " LIMIT {$from},{$pagesize} ");
        $cachedproducts = __c()->get("category_cached_" . $query_code);
        //echo "category_cached_".$query_code;
        //var_dump($cachedproducts);
        if ($cachedproducts == null) {
            $sproductdata = mysql_query($q . " LIMIT {$from},{$pagesize} ") or die(mysql_error());
            while ($rsp = mysql_fetch_object($sproductdata)) {
                $cachedproducts[] = $rsp;
            }
            __c()->set("category_cached_" . $query_code, $cachedproducts, CACHE_TIME);
        }

        $query_code = md5($where3 . $where2);
        $cachedproductCount = __c()->get("category_count_cached_" . $query_code);
        if ($cachedproductCount == null) {
            $sproductdatacount = mysql_query($q) or die(mysql_error());
            $cachedproductCount = mysql_num_rows($sproductdatacount);
            __c()->set("category_count_cached_" . $query_code, $cachedproductCount, CACHE_TIME);
        }
    }
    if (isset($_GET["clearall"])) {
        $leftpanelgroup = $_GET["lastgroup"];
    }

    if (isset($cat->category_name))
        $page_title = @$cat->category_name . " - Jardins Sans Secret";


    if (isset($_GET["append"]) || isset($_GET["f"]) || isset($_GET["ps"])) {
        if($id == "search")
         {
           $canonical_URL = "https://www.jardins-sans-secret.com/category/search/search.html?k=".$_GET["k"];
         }
         else{
           $pageURL = 'http';
            if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
            $pageURL .= "://";
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
           $canonical_URL = preg_replace('/\?.*/', '', $pageURL);
          }
    }

    $seo_description = $cat->meta_description;
    $seo_keywords = $cat->meta_keywords;

    include "view/home/header.php";
    //include "view/home/sidebar.php";
    if ($leftpanelgroup != 'pr' && $leftpanelgroup != 'bs')
        include "view/product/category_menu.php";
    if (count($selected_category) > 1)
        include "view/product/category_subgroup.php";
    elseif (mysql_num_rows($catcdata) == 0)
        include "view/product/category_subgroup.php";
    else
        include "view/product/group.php";

    include "view/home/footer.php";
}

function controller_downloadimages() {
    $data = mysql_query("SELECT * FROM google_scraped WHERE downloaded_image = '' LIMIT 30");
    while ($row = mysql_fetch_object($data)) {
        $url = $row->picture;
        $temp = pathinfo($url);
        if (strpos($temp['extension'], "?") != -1) {
            $t = explode("?", $temp['extension']);
            $temp['extension'] = $t[0];
        }
        if ($temp['extension'] == "")
            $temp['extension'] = "jpg";
        $image_name = date("Y") . '/' . date("m") . '/' . date("d") . '/' . slugify($row->name) . "-" . $row->id . "." . $temp['extension'];
        $image_name = preg_replace('/[^\x00-\x7F]+/', '', $image_name);
        $folder = "static/retailimages/" . date("Y") . '/' . date("m") . '/' . date("d") . '/';
        if (!file_exists('path/to/directory')) {
            mkdir($folder, 0755, true);
        }

        $dlpath = "static/retailimages/" . $image_name;
        if (!file_exists($dlpath) && $temp['extension'] != '') {
            //echo $url . " -> " . $image_name . "<br>";

            $content = file_get_contents($url);
            $fp = fopen($dlpath, "w");
            fwrite($fp, $content);
            fclose($fp);
            if ($fp === false || count($content) == 0)
                mysql_query("UPDATE google_scraped SET downloaded_image='0' WHERE id='{$row->id}'");
            else
                mysql_query("UPDATE google_scraped SET downloaded_image='{$image_name}' WHERE id='{$row->id}'");
        }
        else {
            mysql_query("UPDATE google_scraped SET downloaded_image='{$image_name}' WHERE id='{$row->id}'");
        }
    }
}

function controller_wishlist() {
    if (isset($_POST["smClearWishlist"])) {
        $_SESSION["wishlist"] = "";
    }
    $wishlist = unserialize($_SESSION["wishlist"]);

    if (!empty($_GET["pid"]) || !empty($_GET["sid"])) {
        $_SESSION["lastest_wishlist_ref"] = $_SERVER["HTTP_REFERER"];
        if (is_array($wishlist) == false)
            $wishlist = array();
        $id = "";
        if (!empty($_GET["pid"])) {
            $type = "product";
            $id = $_GET["pid"];
        } elseif (!empty($_GET["sid"])) {
            $type = "seller";
            $id = $_GET["sid"];
        }
        $newwishlist = array();
        if (!isset($_GET["is_delete"])) {
            foreach ($wishlist as $w) {
                if ($w["id"] != $id)
                    $newwishlist[] = $w;
            }
            $newwishlist[] = array(
                "type" => $type,
                "id" => $id,
                "amount" => 1
            );
        }
        else {
            foreach ($wishlist as $w) {
                if ($w["id"] == $id && $w["type"] == $type)
                    continue;
                else
                    $newwishlist[] = $w;
            }
        }
        $_SESSION["wishlist"] = serialize($newwishlist);
        $wishlist = $newwishlist;
        header("Location: /wishlist/index.html");
        header("HTTP/1.1 301 Moved Permanently");
        die();
    }

    if (isset($_POST["smUpdateWishlist"])) {
        $wishlist = array();
        for ($i = 0; $i < count($_POST["id"]); $i++) {
            $data = mysql_query("SELECT DISTINCT p . * , pc.category_type
				FROM product AS p
				INNER JOIN product_in_category AS pic ON pic.product_id = p.id
				INNER JOIN product_category AS pc ON pc.id = pic.category_id
				WHERE p.id =  '" . $i . "' AND p.status =1 AND p.publishing_date < '" . time() . "'
				LIMIT 1");
            $r = mysql_fetch_object($data);
            if ($_POST["amount"][$i] > 0 || !isset($_POST["amount"][$i])) {
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
    foreach ($wishlist as $w) {
        if ($w["type"] == "product") {
            $data = mysql_query("SELECT DISTINCT p . * , pc.category_type
				FROM product AS p
				INNER JOIN product_in_category AS pic ON pic.product_id = p.id
				INNER JOIN product_category AS pc ON pc.id = pic.category_id
				WHERE p.id =  '" . $w["id"] . "' AND p.status =1 AND p.publishing_date < '" . time() . "'
				LIMIT 1");
            $r = mysql_fetch_object($data);
            if ($r) {
                if ($r->category_type == 'gi')
                    $price = GetTotalPriceGI($r->id);
                else
                    $price = GetMinPriceProduct($r->id);
                preg_match('/[0-9\.,]+/', $price, $match);
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
        elseif ($w["type"] == "seller") {
            $data = mysql_query("SELECT * FROM google_scraped WHERE id='" . $w["id"] . "'");
            $r = mysql_fetch_object($data);
            if ($r) {
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

    $page_title = "Wishlist - Jardins Sans Secret, Gardens have no more secrets";
    include "view/home/header.php";
    include "view/product/wishlist.php";
    include "view/home/footer.php";
}

function controller_redirect() {
    $url = urldecode($_GET["url"]);
    $scraped_product_id = urldecode($_GET["spi"]);
    if (isset($scraped_product_id)) {
        $data = mysql_query("SELECT * FROM google_scraped WHERE id='{$scraped_product_id}'") or die("ERROR 77788");
        $row = mysql_fetch_object($data);
        if (isset($row)) {
            $data = mysql_query("SELECT * FROM google_scraped2 WHERE name='{$row->name}' AND country='{$row->country}'") or die("ERROR 77789");
            $row = mysql_fetch_object($data);
            if (isset($row)) {
                mysql_query("UPDATE allow_merchant SET view=view+1 WHERE id='{$row->merchant_id}'") or die("ERROR 77790");
            }
        }
    }
    include "view/home/header.php";
    include "view/home/redirect.php";
    include "view/home/footer.php";
}

function controller_rating() {
    $pid = $_POST["pid"];
    $pid = mysql_escape_string($pid);
    $score = $_POST["score"];
    $score = mysql_escape_string($score);
    $title = mysql_escape_string($_POST["title"]);
    $pros = mysql_escape_string($_POST["pros"]);
    $cons = mysql_escape_string($_POST["cons"]);
    $thoughts = mysql_escape_string($_POST["thoughts"]);
    $name = mysql_escape_string($_POST["name"]);
    $email = mysql_escape_string($_POST["email"]);
    $conclusion = mysql_escape_string($_POST["conclusion"]);
    $location = mysql_escape_string($_POST["location"]);
    $captcha = $_POST["captcha"];

    require_once 'static/securimage/securimage.php';
    $securimage = new Securimage();
    if ($securimage->check($captcha) == false) {
        $res = array();
        $res["error_code"] = 1;
        $res["error"] = "Incorrect security code entered";
        die(json_encode($res));
    }

    $pros = str_replace("\n", "<br>", $pros);
    $cons = str_replace("\n", "<br>", $cons);
    $thoughts = str_replace("\n", "<br>", $thoughts);

    $ip = GetIP();
    if ($ip == "")
        $ip = "127.0.0.1";
    if (!empty($pid) && !empty($score)) {
        if (CheckUserReviewed($pid) == false) {
            mysql_query("REPLACE INTO user_rating SET ip='{$ip}', product_id='{$pid}'
				, title = '{$title}'
				, pros = '{$pros}'
				, cons = '{$cons}'
				, comment = '{$thoughts}'
				, conclusion = '{$conclusion}'
				, email = '{$email}'
				, name = '{$name}'
				, location = '{$location}'
				, review_type = 'product'
				, status='pending', rate='{$score}'");

            /* $data = mysql_query("SELECT COUNT(*) AS counter FROM user_rating WHERE product_id='{$pid}' GROUP BY product_id");
              $row = mysql_fetch_object($data);
              $count = $row->counter;

              $data = mysql_query("SELECT AVG(rate) AS avg FROM user_rating WHERE product_id='{$pid}' GROUP BY product_id");
              $row = mysql_fetch_object($data);
              $avg = $row->avg;

              mysql_query("UPDATE product SET rating='{$avg}'
              , rating_count = '{$count}' WHERE id={$pid}"); */

            $res = array();
            //$res["reviewCount"] = $count;
            //$res["ratingValue"] = round($avg,1);
            $res["error_code"] = 0;
            $res["message"] = "Thanks for you review.";
            die(json_encode($res));
        } else {
            $res = array();
            $res["message"] = "You have left review for this product.";
            die(json_encode($res));
        }
    }
}

function controller_reviewing() {
    $pid = $_POST["pid"];
    $pid = mysql_escape_string($pid);
    if (!empty($pid))
        mysql_query("UPDATE product SET reviewer = reviewer + 1 WHERE id='{$pid}'");
}

function vendor_register_processing() {
    require_once 'static/securimage/securimage.php';
    $securimage = new Securimage();
    $res = array();
    $res["error"] = "";
    $res["info"] = "";

    $firstname = filter_input(INPUT_POST, "firstname", FILTER_SANITIZE_STRING);
    $lastname = filter_input(INPUT_POST, "lastname", FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $password1 = filter_input(INPUT_POST, "password", FILTER_SANITIZE_ENCODED);
    $password2 = filter_input(INPUT_POST, "password2", FILTER_SANITIZE_STRING);
    $shopname = filter_input(INPUT_POST, "shopname", FILTER_SANITIZE_STRING);
    $website = filter_input(INPUT_POST, "website", FILTER_SANITIZE_STRING);
    $phonenumber = filter_input(INPUT_POST, "phonenumber", FILTER_SANITIZE_STRING);
    $country = filter_input(INPUT_POST, "country", FILTER_SANITIZE_STRING);
    $securitycode = filter_input(INPUT_POST, "securitycode", FILTER_SANITIZE_STRING);
    $agreed = filter_input(INPUT_POST, "agreed", FILTER_SANITIZE_STRING);
    $rememberme = filter_input(INPUT_POST, "rememberme", FILTER_SANITIZE_STRING);
    $street = filter_input(INPUT_POST, "street", FILTER_SANITIZE_STRING);
    $city = filter_input(INPUT_POST, "city", FILTER_SANITIZE_STRING);
    $zip = filter_input(INPUT_POST, "zip", FILTER_SANITIZE_STRING);
    $state = filter_input(INPUT_POST, "state", FILTER_SANITIZE_STRING);

    if (isset($_POST["smVendorRegister"])) {
        if ($securimage->check($securitycode) == false) {
            $res["error"] = "Incorrect security code entered!";
            return $res;
        }

        if (empty($firstname) ||
                empty($lastname) ||
                empty($email) ||
                empty($password1) ||
                empty($shopname) ||
                empty($website) ||
                empty($country) ||
                empty($city) ||
                empty($street) ||
                empty($securitycode) ||
                empty($agreed)) {
            $res["error"] = "Please enter all the required fields";
            return $res;
        }

        if ($password1 != $password2) {
            $res["error"] = "Passwords are not matched";
            return $res;
        }

        $data = mysql_query("SELECT COUNT(*) AS counter FROM allow_merchant WHERE email='{$email}'") or die("Error 78001:" . mysql_error());
        $row = mysql_fetch_object($data);
        if ($row->counter > 0) {
            $res["error"] = "This email address already exists.";
            return $res;
        }
        mysql_query("INSERT INTO allow_merchant SET email='{$email}', username='$email'") or die("Error 78002:" . mysql_error());
        $mid = mysql_insert_id();

        try {
            //$mid = $vendor->id;
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
            mysql_query("UPDATE allow_merchant SET "
                    . "first_name='{$firstname}'"
                    . " ,last_name='{$lastname}'"
                    . " ,country='{$country}'"
                    . " ,street='{$street}'"
                    . " ,city='{$city}'"
                    . " ,zip='{$zip}'"
                    . " ,state='{$state}'"
                    . " ,merchant='{$website}'"
                    . " ,logo='{$image}'"
                    . " ,phone_number='{$phonenumber}'"
                    . " ,`password`=MD5('{$password1}')"
                    . " ,shop_name='{$shopname}'"
                    . " ,website='{$website}'"
                    . " ,status='pending'"
                    . " WHERE id='{$mid}'"); // or die("Error 78003:" . mysql_error());
            $res["info"] = "Your registration information has been successfully sent and will be promptly reviewed by Jardins sans secret!";
            $emailContent = file_get_contents("static/email_templates/registration.html");
            $emailContent = str_replace("##FirstName##", $firstname, $emailContent);
            $emailContent = str_replace("##LastName##", $lastname, $emailContent);
            $emailContent = str_replace("##Login##", $email, $emailContent);
            $emailContent = str_replace("##Company##", $shopname, $emailContent);
            $emailContent = str_replace("##Email##", $email, $emailContent);
            $emailContent = str_replace("##Phone##", $phonenumber, $emailContent);
            @mail(ADMIN_EMAIL, "{$shopname} has registered as Vendor to Jardins sans secret", $emailContent, "Content-type: text/html; charset=iso-8859-1");
            @mail($email, "Welcome to Jardins Sans Secret!", $emailContent, "Content-type: text/html; charset=iso-8859-1");
        } catch (Exception $ex) {
            mysql_query("DELETE allow_merchant FROM WHERE id='{$mid}'") or die("Error 91011: " . mysql_error());
            $res["error"] = "Error 91010: " . $ex->getMessage();
            return $res;
            //die();
        }
        return $res;
    } elseif (isset($_POST["smVendorLogin"])) {
        $data = mysql_query("SELECT * FROM allow_merchant WHERE email='{$email}' AND (password=MD5('$password1') OR password2=MD5('$password1'))") or die("Error 78004:" . mysql_error());
        $row = mysql_fetch_object($data);
        if (!isset($row) || !$row) {
            $res["error"] = "Username and password are not matched, Please try again.";
            return $res;
        } elseif ($row->status != 'approved') {
            switch ($row->status) {
                case "pending":
                    $res["warn"] = "Your merchant has been successfully sent and will be promptly reviewed by Jardins Sans secret! ";
                    break;
                case "banned":
                    $res["warn"] = "Your Account is temporarily suspended.";
                    break;
                case "deleted":
                    $res["warn"] = "Your merchant is unable to login.";
                    break;
            }
            return $res;
        } else {
            $_SESSION["isvendor"] = true;
            $_SESSION["vendor_id"] = $row->id;
            $_SESSION["vendor_session"] = md5(uniqid("vs_"));
            mysql_query("UPDATE allow_merchant SET session_id='" . $_SESSION["vendor_session"] . "' WHERE id='{$row->id}'") or die("Error 78005:" . mysql_error());
            if ($rememberme == '1') {
                SetCookieLive("session_id", $_SESSION["vendor_session"], time() + 3600 * 24 * 30, '', '', $secure = true, false);
			} else {
                RemoveCookieLive("session_id");
            }
            header("Location: /vendor");
            die();
        }
    } elseif (isset($_POST["smVendorRecovery"])) {
        $data = mysql_query("SELECT * FROM allow_merchant WHERE email='{$email}'") or die("Error 78204:" . mysql_error());
        $row = mysql_fetch_object($data);
        if (!isset($row) || !$row) {
            $res["error"] = "The account is not exist.";
            return $res;
        } elseif ($row->status != 'approved') {
            switch ($row->status) {
                case "pending":
                    $res["warn"] = "Your merchant has been successfully sent and will be promptly reviewed by Jardins Sans secret! ";
                    break;
                case "banned":
                    $res["warn"] = "Your merchant is Banned.";
                    break;
                case "deleted":
                    $res["warn"] = "Your merchant is unable to login.";
                    break;
            }
            return $res;
        } else {
            $new_password = generateRandomString(10);
            mysql_query("UPDATE allow_merchant SET password2=MD5('$new_password') WHERE id='{$row->id}'") or die("Error 78205:" . mysql_error());
            $html = file_get_contents("static/email_templates/recovery_password.html");
            
            $html = str_replace("##FirstName##", $row->first_name, $html);
            $html = str_replace("##LastName##", $row->last_name, $html);
            $html = str_replace("##Login##", $row->username, $html);
            $html = str_replace("##NewPassword##", $new_password, $html);
            $emails = array();
            $emails[] = array("email" => $row->email, "name" => $row->first_name . " " . $row->last_name);
            $ok = send_email_mandrillapp(MANDRILLAPP_API, "jardins Sans Secret", ADMIN_EMAIL, "Recover password request", $emails, $html);
            if ($ok != 'sent')
                $res["error"] = "System has problem. Please contact with Wed Master to solve the problem.";
            elseif ($ok == 'sent')
                $res["info"] = "New password has been sent to your email address.";
            return $res;
        }
    }
}

function controller_vendor_register() {
    $page_title = "Merchant Center";
    if (isset($_SESSION["isvendor"]) && $_SESSION["isvendor"] === true) {
        header("Location: /vendor");
        die();
    }
    $res = vendor_register_processing();
    $error = $res["error"];
    $info = $res["info"];
    $warn = $res["warn"];
    
    include "view/home/header.php";
    include "view/home/vendor_register.php";
    include "view/home/footer.php";
}   

?>