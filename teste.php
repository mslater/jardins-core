<?php
/*session_start();
header("Cache-Control: private, max-age=10800, pre-check=10800");
header("Pragma: private");
header("Expires: " . date(DATE_RFC822,strtotime(" 2 day")));
*/

    DEFINE('IPTC_OBJECT_NAME', '005');
    DEFINE('IPTC_EDIT_STATUS', '007');
    DEFINE('IPTC_PRIORITY', '010');
    DEFINE('IPTC_CATEGORY', '015');
    DEFINE('IPTC_SUPPLEMENTAL_CATEGORY', '020');
    DEFINE('IPTC_FIXTURE_IDENTIFIER', '022');
    DEFINE('IPTC_KEYWORDS', '025');
    DEFINE('IPTC_RELEASE_DATE', '030');
    DEFINE('IPTC_RELEASE_TIME', '035');
    DEFINE('IPTC_SPECIAL_INSTRUCTIONS', '040');
    DEFINE('IPTC_REFERENCE_SERVICE', '045');
    DEFINE('IPTC_REFERENCE_DATE', '047');
    DEFINE('IPTC_REFERENCE_NUMBER', '050');
    DEFINE('IPTC_CREATED_DATE', '055');
    DEFINE('IPTC_CREATED_TIME', '060');
    DEFINE('IPTC_ORIGINATING_PROGRAM', '065');
    DEFINE('IPTC_PROGRAM_VERSION', '070');
    DEFINE('IPTC_OBJECT_CYCLE', '075');
    DEFINE('IPTC_BYLINE', '080');
    DEFINE('IPTC_BYLINE_TITLE', '085');
    DEFINE('IPTC_CITY', '090');
    DEFINE('IPTC_PROVINCE_STATE', '095');
    DEFINE('IPTC_COUNTRY_CODE', '100');
    DEFINE('IPTC_COUNTRY', '101');
    DEFINE('IPTC_ORIGINAL_TRANSMISSION_REFERENCE',     '103');
    DEFINE('IPTC_HEADLINE', '105');
    DEFINE('IPTC_CREDIT', '110');
    DEFINE('IPTC_SOURCE', '115');
    DEFINE('IPTC_COPYRIGHT_STRING', '116');
    DEFINE('IPTC_CAPTION', '120');
    DEFINE('IPTC_LOCAL_CAPTION', '121');

    class iptc {
        var $meta=Array();
        var $hasmeta=false;
        var $file=false;
        
        
        function iptc($filename) {
            $size = getimagesize($filename,$info);
            $this->hasmeta = isset($info["APP13"]);
            if($this->hasmeta)
                $this->meta = iptcparse ($info["APP13"]);
            $this->file = $filename;
        }
        function set($tag, $data) {
            $this->meta ["2#$tag"]= Array( $data );
            $this->hasmeta=true;
        }
        function get($tag) {
            return isset($this->meta["2#$tag"]) ? $this->meta["2#$tag"][0] : false;
        }
        
        function dump() {
            $f = fopen("teste.txt","wb");
             fwrite($f,          print_r($this->meta,TRUE));
            fclose($f);
        }
        function binary() {
            $iptc_new = '';
            foreach (array_keys($this->meta) as $s) {
                $tag = str_replace("2#", "", $s);
                $iptc_new .= $this->iptc_maketag(2, $tag, $this->meta[$s][0]);
            }        
            return $iptc_new;    
        }
        function iptc_maketag($rec,$dat,$val) {
            $len = strlen($val);
            if ($len < 0x8000) {
                   return chr(0x1c).chr($rec).chr($dat).
                   chr($len >> 8).
                   chr($len & 0xff).
                   $val;
            } else {
                   return chr(0x1c).chr($rec).chr($dat).
                   chr(0x80).chr(0x04).
                   chr(($len >> 24) & 0xff).
                   chr(($len >> 16) & 0xff).
                   chr(($len >> 8 ) & 0xff).
                   chr(($len ) & 0xff).
                   $val;
                   
            }
        }    
        function write() {
            if(!function_exists('iptcembed')) return false;
            $mode = 0;
            $content = iptcembed($this->binary(), $this->file, $mode);    
            $filename = $this->file;
                
            $fp = fopen($filename, "w");
            fwrite($fp, $content);
            fclose($fp);
        }    

    };
    

require "lib/phpthumb/ThumbLib.inc.php";

$thumb = PhpThumbFactory::create("teste.jpg");
	$thumb->adaptiveResize(100, 100);
	$thumb->save("teste123123.jpg");


function transferIptcExif2File($srcfile, $destfile) {
    // Function transfers EXIF (APP1) and IPTC (APP13) from $srcfile and adds it to $destfile
    // JPEG file has format 0xFFD8 + [APP0] + [APP1] + ... [APP15] + <image data> where [APPi] are optional
    // Segment APPi (where i=0x0 to 0xF) has format 0xFFEi + 0xMM + 0xLL + <data> (where 0xMM is 
    //   most significant 8 bits of (strlen(<data>) + 2) and 0xLL is the least significant 8 bits 
    //   of (strlen(<data>) + 2)  

    if (file_exists($srcfile) && file_exists($destfile)) {
        $srcsize = @getimagesize($srcfile, $imageinfo);
        // Prepare EXIF data bytes from source file
        $exifdata = (is_array($imageinfo) && key_exists("APP1", $imageinfo)) ? $imageinfo['APP1'] : null;
        if ($exifdata) {
            $exiflength = strlen($exifdata) + 2;
            if ($exiflength > 0xFFFF) return false;
            // Construct EXIF segment
            $exifdata = chr(0xFF) . chr(0xE1) . chr(($exiflength >> 8) & 0xFF) . chr($exiflength & 0xFF) . $exifdata;
        }
        // Prepare IPTC data bytes from source file
        $iptcdata = (is_array($imageinfo) && key_exists("APP13", $imageinfo)) ? $imageinfo['APP13'] : null;
        if ($iptcdata) {
            $iptclength = strlen($iptcdata) + 2;
            if ($iptclength > 0xFFFF) return false;
            // Construct IPTC segment
            $iptcdata = chr(0xFF) . chr(0xED) . chr(($iptclength >> 8) & 0xFF) . chr($iptclength & 0xFF) . $iptcdata;
        }
        $destfilecontent = @file_get_contents($destfile);
        if (!$destfilecontent) return false;
        if (strlen($destfilecontent) > 0) {
            $destfilecontent = substr($destfilecontent, 2);
            $portiontoadd = chr(0xFF) . chr(0xD8);          // Variable accumulates new & original IPTC application segments
            $exifadded = !$exifdata;
            $iptcadded = !$iptcdata;

            while ((substr($destfilecontent, 0, 2) & 0xFFF0) === 0xFFE0) {
                $segmentlen = (substr($destfilecontent, 2, 2) & 0xFFFF);
                $iptcsegmentnumber = (substr($destfilecontent, 1, 1) & 0x0F);   // Last 4 bits of second byte is IPTC segment #
                if ($segmentlen <= 2) return false;
                $thisexistingsegment = substr($destfilecontent, 0, $segmentlen + 2);
                if ((1 <= $iptcsegmentnumber) && (!$exifadded)) {
                    $portiontoadd .= $exifdata;
                    $exifadded = true;
                    if (1 === $iptcsegmentnumber) $thisexistingsegment = '';
                }
                if ((13 <= $iptcsegmentnumber) && (!$iptcadded)) {
                    $portiontoadd .= $iptcdata;
                    $iptcadded = true;
                    if (13 === $iptcsegmentnumber) $thisexistingsegment = '';
                }
                $portiontoadd .= $thisexistingsegment;
                $destfilecontent = substr($destfilecontent, $segmentlen + 2);
            }
            if (!$exifadded) $portiontoadd .= $exifdata;  //  Add EXIF data if not added already
            if (!$iptcadded) $portiontoadd .= $iptcdata;  //  Add IPTC data if not added already
            $outputfile = fopen($destfile, 'w');
            if ($outputfile) return fwrite($outputfile, $portiontoadd . $destfilecontent); else return false;
        } else {
            return false;
        }
    } else {
        return false;
    }

}
echo   transferIptcExif2File("teste.jpg","teste123123.jpg");


?>