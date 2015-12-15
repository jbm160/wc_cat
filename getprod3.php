<?php
// This is a template for a PHP scraper on morph.io (https://morph.io)
// including some code snippets below that you should find helpful
$local = 1;
$baseurl = "http://www.woodcraft.com";
$o = fopen("./detprodlisttestout.csv", "w+");
$i = fopen("./imagestestout.csv", "w+");
$e = fopen("./testlog.csv", "a+");
//fputcsv($i,array("Original Image URL","New Image URL"));

if ($local) {
  require '../scraperwiki-php/scraperwiki.php';
  require '../scraperwiki-php/scraperwiki/simple_html_dom.php';
} else {
  require 'scraperwiki.php';
  require 'scraperwiki/simple_html_dom.php';
}

//
// // Read in a page
echo "Opening detprodlistnew.csv for reading...\n";
if (($f = fopen("./detprodlisttest.csv", "r")) !== FALSE) {
  // copy header line to new file
  $data = fgetcsv($f);
  fputcsv($o, $data);
  while (($data = fgetcsv($f)) !== FALSE) {
    checkData($data);
  }
  fclose($f);
}
fclose($o);
fclose($i);
fclose($e);

// parse the array of values and check for missing images, prices, and thumbs

function checkData($da) {
  global $baseurl, $f, $o, $i, $e, $local;
  $d = new simple_html_dom();
  $u = $baseurl . $da[28];
//  echo "URL: " . $u ."\n";
  $d->load(scraperwiki::scrape($u));
  if (is_null($d->find('div[id=medproimg] a',0))) {
    $errstr = "Error: " . $da[0] . ". Couldn't find product page.\n";
    fwrite($e,$errstr);
    echo $errstr;
    return 0;
  }
  $imgurl = trim($d->find('div[id=medproimg] a',0)->href);
  if ($da[11] == "///" || !is_numeric($da[19])) {
    if ($da[11] == "///") {
      $imgfile = trim(strrchr($imgurl,"/"),"/ ");
      $img = "/" . substr($imgfile,0,1) . "/" . substr($imgfile,1,1) . "/" . $imgfile;
      if ($img != "///") {
        $da[11] = $img;
        $da[22] = $img;
        $da[25] = $img;
        $da[52] = $img;
      } else {
        $errstr = "Error: " . $da[0] . ". Couldn't find img filename.\n";
        fwrite($e,$errstr);
        echo $errstr;
        return 0;
      }
    }
    if (!is_numeric($da[19])) {
      $price = implode(explode(",",trim(strstr(strstr($d->find('div[id=proprice]',0)->innertext,"$"),"<",true),"$ ")));
      if (is_numeric($price)) {
        $da[19] = $price;
      } else {
        $errstr = "Error: " . $da[0] . ". Couldn't find price.\n";
        fwrite($e,$errstr);
        echo $errstr;
        return 0;
      }
    }
    fputcsv($o,$da);
    fputcsv($i,array($imgurl,$da[11]));
    $errstr = "Success: updated " . $da[0] . ".\n";
    fwrite($e,$errstr);
    echo $errstr;
    checkThumbs($d,$da);
  }
  fputcsv($o,$da);
  fputcsv($i,array($imgurl,$da[11]));
  checkThumbs($d,$da);
}

// Check for multiple thumbnails
function checkThumbs($doc,$dat) {
  global $f, $o, $i, $e, $local;
  $thumbs = $doc->find('div[id=altvidthmbs] div.thmbs');
  if (count($thumbs) > 1) {
    $errstr = "Success: Found " . (count($thumbs) - 1) . " thumbnails for " . $dat[0] . ".\n";
    fwrite($e,$errstr);
    echo $errstr;
    for ($x = 0; $x <= (count($thumbs) - 2); $x++) {
      $imgfileurl = $thumbs[$x]->first_child()->href;
      $imgfile = trim(strrchr($imgfileurl,"/"),"/ ");
      $img = "/" . substr($imgfile,0,1) . "/" . substr($imgfile,1,1) . "/" . $imgfile;
      fputcsv($i,array($imgfileurl,$img));
      $data = array(
        "","","","","","","","","","",
        "","","","","","","","","","",
        "","","","","","","","","","",
        "","","","","","","","","","",
        "","","","","","","","","","",
        "","88",
        $img,
        $thumbs[$x]->first_child()->title,
        ($x + 2),
        0
        );
      fputcsv($o,$data);
    }
  }
}
// parse the categories and save to database
/** database columns:
sku
_store
_attribute_set
_type
_category
_root_category = "Home"
_product_websites = "base"
created_at = "12/12/15 22:48"
description = d$->find('div[id=prospecsbox]',0)->outertext . d$->find('div[id=ctl00_cphContent_divShippingBilling]',0)->outertext
gift_wrapping = "No"
has_options = 0
image = 
manufacturer = trim(d$->find('span[id=ctl00_cphContent_hidebrandid]',0)->first_child()->innertext)
meta_keyword = ""
msrp_display_actual_price_type = "Use config"
msrp_enabled = "Use config"
name = trim(d$->find('div[id=productname]',0)->first_child()->innertext)
options_container = "Product Info Column"
page_layout = "1 column"
price = trim(d$->find('div[id=proprice]',0)->first_child()->innertext,"$ ")
required_options = 0
short_description = ""
small_image = 
status = 1
tax_class_id = 2
thumbnail
updated_at
url_key = 
url_path
visibility
weight = 1.0000
qty = 0
min_qty = 1
use_config_min_qty = 1
is_qty_decimal = 0
backorders = 0
use_config_backorders = 1
min_sale_qty = 1
use_config_min_sale_qty = 1
max_sale_qty = 0
use_config_max_sale_qty = 1
is_in_stock = 1
use_config_notify_stock_qty = 1
manage_stock = 0
use_config_manage_stock = 1
stock_status_changed_auto = 0
use_config_qty_increments = 1
qty_increments = 0
use_config_enable_qty_inc = 1
enable_qty_increments = 0
is_decimal_divided = 0
_media_attribute_id = 88
_media_image
_media_lable
_media_position
_media_is_disabled

*/
//   sku
//   path
//   URL
//   
function getProduct($u){
  global $baseurl, $o, $r, $i, $local;
  $path = "";
  $d = new simple_html_dom();
  $d->load(scraperwiki::scrape($u));
  if (is_null($d->find('div[id=medproimg]',0))) {
    return 0;
  }
//echo "Loaded URL: " . $u . "\n";
  $imgfileurl = $d->find('div[id=medproimg]',0)->first_child()->href;
  $imgfile = trim(strrchr($imgfileurl,"/"),"/ ");
  $img = "/" . substr($imgfile,0,1) . "/" . substr($imgfile,1,1) . "/" . $imgfile;
  fputcsv($i,array($imgfileurl,$img));
  $catname = "";
  $cats = $d->find('div[id=breadcrumb] ul li a');
  foreach ($cats as $cat) {
    $catname .= trim($cat->innertext) . "/";
  }
  $catname .= trim($d->find('div[id=breadcrumb] ul a b',0)->innertext);
  if (!is_null($d->find('div[id=prospecsbox]',0))) {
    $description = $d->find('div[id=prospecsbox]',0)->outertext;
  } else {
    $description = "";
  }
  if (!is_null($d->find('div[id=ctl00_cphContent_divShippingBilling]',0))) {
    $description .= $d->find('div[id=ctl00_cphContent_divShippingBilling]',0)->outertext;
  }
  if (!is_null($d->find('span[id=ctl00_cphContent_hidebrandid]',0))) {
    $brand = trim($d->find('span[id=ctl00_cphContent_hidebrandid]',0)->first_child()->innertext);
  } else {
    $brand = "";
  }
  $data = array(
    trim($d->find('span[id=pskuonly]',0)->innertext),
    "",
    "Default",
    "simple",
    $catname,
    "Home",
    "base",
    "12/12/15 22:48",
    $description,
    "No",
    0,
    $img,
    $brand,
    "",
    "Use config",
    "Use config",
    trim($d->find('div[id=productname]',0)->first_child()->innertext),
    "Product Info Column",
    "1 column",
    trim($d->find('div[id=proprice]',0)->first_child()->innertext,"$ "),
    0,
    "",
    $img,
    1,
    2,
    $img,
    "12/12/15 22:48",
    "",
    "",
    4,
    1.0000,
    0,
    1,
    1,
    0,
    0,
    1,
    1,
    1,
    0,
    1,
    1,
    1,
    0,
    1,
    0,
    1,
    0,
    1,
    0,
    0,
    88,
    $img,
    $d->find('div[id=medproimg]',0)->first_child()->title,
    1,
    0    
    );
  fputcsv($o,$data);
  $thumbs = $d->find('div[id=altvidthmbs] thmbs');
  if (count($thumbs) > 1) {
    for ($x = 0; $x <= (count($thumbs) - 2); $x++) {
      $imgfileurl = $thumbs[$x]->first_child()->href;
      $imgfile = trim(strrchr($imgfileurl,"/"),"/ ");
      $img = "/" . substr($imgfile,0,1) . "/" . substr($imgfile,1,1) . "/" . $imgfile;
      fputcsv($i,array($imgfileurl,$img));
      $data = array(
        "","","","","","","","","","",
        "","","","","","","","","","",
        "","","","","","","","","","",
        "","","","","","","","","","",
        "","","","","","","","","","",
        "","88",
        $img,
        $thumbs[$x]->first_child()->title,
        ($x + 2),
        0
        );
      fputcsv($o,$data);
    }
  }
  $reviews = $d->find('table[id=ctl00_cphContent_datalistReviews] div.pr-review-wrap');
  if (count($reviews) > 0) {
    foreach ($reviews as $rev) {
      $data = array(
        trim($d->find('span[id=pskuonly]',0)->innertext),
        trim($rev->find('p.pr-review-rating-headline span',0)->innertext),
        trim($rev->find('span.pr-rating',0)->innertext),
        trim($rev->find('span[id$=labelUser]',0)->innertext),
        trim($rev->find('span[id$=labelLocation]',0)->innertext),
        trim($rev->find('div.pr-review-author-date',0)->innertext),
        trim($rev->find('span[id$=labelComments]',0)->innertext)
      );
      fputcsv($r,$data);
    }
  }
  echo trim($d->find('div[id=productname]',0)->first_child()->innertext) . "\n";
  return 1;
}

?>
