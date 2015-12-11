<?php
// This is a template for a PHP scraper on morph.io (https://morph.io)
// including some code snippets below that you should find helpful
$local = 0;
if ($local) {
  require '../scraperwiki-php/scraperwiki.php';
  require '../scraperwiki-php/scraperwiki/simple_html_dom.php';
} else {
  require 'scraperwiki.php';
  require 'scraperwiki/simple_html_dom.php';
}

//
// // Read in a page
$baseurl="http://www.woodcraft.com";
$html = $baseurl . "/category/wdc/woodcraft.aspx?sort=priceD";

getCategories($html);
//
// // Find something on the page using css selectors
// $dom = new simple_html_dom();
// $dom->load($html);

// parse the categories and save to database
// database columns:
//   Category name
//   path
//   URL
//   
function getCategories($u){
  global $baseurl;
  $path = "";
  $d = new simple_html_dom();
  $d->load(scraperwiki::scrape($u));
echo "Loaded URL: " . $u . "\n";
  if ($d->find('div[id=ctl00_cphContent_gsaCatFacetContainer]')) {
    $breadcrumb = $d->find('div[id=breadcrumb]',0);
//foreach($breadcrumb as $b) {
//echo "Breadcrumb = " . $b;}
    if (!is_null($breadcrumb)) {
	foreach ($breadcrumb->children() as $crumb) {
       	  $path .= trim($crumb->innertext) . "/";
    	}
        $path .= trim(strrchr($breadcrumb->innertext,">"),"> ");
    }
    foreach ($d->find('div[id=ctl00_cphContent_gsaCatFacetContainer]',0)->find('div[class=S2refinementsContainer]',0)->children() as $div) {
      $name = trim(strstr($div->children(0)->innertext,"(",true));
      $url = $baseurl . $div->children(0)->href;
      $data = array("Name"=>$name, "Path"=>$path, "URL"=>$url);
      echo $path . "/" . $name . "\n";
      scraperwiki::save_sqlite(array("Name"), $data);
      getCategories($url);
    }
  }
}
// print_r($dom->find("table.list"));
//
// // Write out to the sqlite database using scraperwiki library
// scraperwiki::save_sqlite(array('name'), array('name' => 'susan', 'occupation' => 'software developer'));
//
// // An arbitrary query against the database
// scraperwiki::select("* from data where 'name'='peter'")

// You don't have to do things with the ScraperWiki library.
// You can use whatever libraries you want: https://morph.io/documentation/php
// All that matters is that your final data is written to an SQLite database
// called "data.sqlite" in the current working directory which has at least a table
// called "data".
?>
