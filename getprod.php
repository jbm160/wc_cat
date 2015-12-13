<?php
// This is a template for a PHP scraper on morph.io (https://morph.io)
// including some code snippets below that you should find helpful
$local = 1;
$baseurl = "http://www.woodcraft.com";
$o = fopen("./detprodlist.csv", "w+");
$r = fopen("./reviews.csv", "w+")
if ($local) {
  require '../scraperwiki-php/scraperwiki.php';
  require '../scraperwiki-php/scraperwiki/simple_html_dom.php';
} else {
  require 'scraperwiki.php';
  require 'scraperwiki/simple_html_dom.php';
}

//
// // Read in a page
echo "Opening categories.csv for reading...\n";
if (($f = fopen("./prodlist.csv", "r")) !== FALSE) {
  while (($data = fgetcsv($f)) !== FALSE) {
    $produrl = $baseurl . $data[3];
    getProduct($produrl);
  }
  fclose($f);
}
fclose($o);
fclose($r);


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
description = d$->find('div[class=prospecs]',0)->innertext
gift_wrapping = "No"
has_options = 0
image = 
manufacturer = trim(d$->find('span[id=ctl00_cphContent_hidebrandid]',0)->first_child()->innertext)
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
  global $baseurl, $o, $r, $local;
  $path = "";
  $d = new simple_html_dom();
  $d->load(scraperwiki::scrape($u));
//echo "Loaded URL: " . $u . "\n";
  $data = array(
    trim($->find('span[id=pskuonly]',0)->innertext),
    "",
    "Default",
    "simple",
    $cat,
    "Home",
    "base",
    "12/12/15 22:48",
    
  		fputcsv($o,array($sku, $prodname, $prodthumb, $prodURL));
echo $prodname . "\n";
  	}
  	if ($d->find('div[class=S2itemsPPText]',0)->last_child()->style == "display: inline") {
  		$newURL = $baseurl . $d->find('div[class=S2itemsPPText]',0)->last_child()->href;
  		getProducts($newURL);
  	}
  }
}

?>
