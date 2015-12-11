<?php
// This is a template for a PHP scraper on morph.io (https://morph.io)
// including some code snippets below that you should find helpful
$local = 1;
$baseurl = "http://www.woodcraft.com";
$o = fopen("./prodlist.csv", "w+");
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
if (($f = fopen("./categories.csv", "r")) !== FALSE) {
  while (($data = fgetcsv($f)) !== FALSE) {
    getProducts($data[2]);
  }
  fclose($f);
}
fclose($o);


// parse the categories and save to database
// database columns:
//   Category name
//   path
//   URL
//   
function getProducts($u){
  global $baseurl, $o, $local;
  $path = "";
  $d = new simple_html_dom();
  $d->load(scraperwiki::scrape($u));
//echo "Loaded URL: " . $u . "\n";
  $S2Prod = $d->find('span[class=S2Product]');
  if (count($S2Prod) > 0) {
  	foreach ($S2Prod as $p) {
  		$sku = trim($p->find('div[class=S2ProductSku]',0)->innertext,"# ");
  		$prodname = trim($p->find('div[class=S2ProductName]',0)->first_child()->innertext);
  		$prodthumb = $p->find('img[class=S2ProductImg]',0)->src;
  		$prodURL = $p->find('div[class=S2ProductName]',0)->first_child()->href;
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
